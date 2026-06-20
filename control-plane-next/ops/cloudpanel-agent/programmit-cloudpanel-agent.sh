#!/usr/bin/env bash
set -euo pipefail

ENV_FILE="${ENV_FILE:-/etc/programmit-control-agent.env}"
if [ ! -f "$ENV_FILE" ]; then
  echo "[programmit-agent] missing env file: $ENV_FILE" >&2
  exit 1
fi

# shellcheck disable=SC1090
source "$ENV_FILE"

CONTROL_BASE_URL="${CONTROL_BASE_URL:-}"
NODE_KEY="${NODE_KEY:-}"
NODE_TOKEN="${NODE_TOKEN:-}"
POLL_INTERVAL="${POLL_INTERVAL:-5}"
INSECURE_TLS="${INSECURE_TLS:-0}"
ALLOW_RUN_COMMAND="${ALLOW_RUN_COMMAND:-0}"

if [ -z "$CONTROL_BASE_URL" ] || [ -z "$NODE_KEY" ] || [ -z "$NODE_TOKEN" ]; then
  echo "[programmit-agent] missing CONTROL_BASE_URL/NODE_KEY/NODE_TOKEN" >&2
  exit 1
fi

for bin in curl jq clpctl; do
  if ! command -v "$bin" >/dev/null 2>&1; then
    echo "[programmit-agent] required binary not found: $bin" >&2
    exit 1
  fi
done

BASE="${CONTROL_BASE_URL%/}"
PULL_URL="$BASE/api/control/cloudpanel/agent/pull"
ACK_URL="$BASE/api/control/cloudpanel/agent/ack"

if ! [[ "$POLL_INTERVAL" =~ ^[0-9]+$ ]] || [ "$POLL_INTERVAL" -lt 1 ]; then
  POLL_INTERVAL=5
fi

curl_common=(
  -fsS
  -H "x-node-key: $NODE_KEY"
  -H "authorization: Bearer $NODE_TOKEN"
  -H "content-type: application/json"
)

if [ "$INSECURE_TLS" = "1" ]; then
  curl_common=(-k "${curl_common[@]}")
fi

log() {
  printf '[%s] %s\n' "$(date -u '+%Y-%m-%d %H:%M:%S UTC')" "$*"
}

json_escape() {
  jq -Rn --arg v "$1" '$v'
}

safe_user_from_payload() {
  local payload="$1"
  local from_payload from_slug from_domain raw

  from_payload="$(jq -r '.siteUser // empty' <<<"$payload")"
  if [ -n "$from_payload" ]; then
    raw="$from_payload"
  else
    from_slug="$(jq -r '.tenantSlug // empty' <<<"$payload")"
    from_domain="$(jq -r '.domain // .primaryDomain // empty' <<<"$payload")"
    raw="${from_slug:-${from_domain%%.*}}"
  fi

  raw="$(echo "$raw" | tr '[:upper:]' '[:lower:]' | tr -cd 'a-z0-9_' | cut -c1-24)"
  if [ -z "$raw" ]; then
    raw="site$(date +%s)"
  fi

  if [[ ! "$raw" =~ ^[a-z] ]]; then
    raw="s${raw}"
  fi

  echo "$raw"
}

ack_task() {
  local task_id="$1"
  local status="$2"
  local retry="$3"
  local error_message="$4"
  local result_json="$5"

  local payload
  payload="$(jq -n \
    --arg taskId "$task_id" \
    --arg status "$status" \
    --argjson retry "$retry" \
    --arg errorMessage "$error_message" \
    --argjson result "$result_json" \
    '{taskId:$taskId,status:$status,retry:$retry,errorMessage:($errorMessage|select(length>0)),result:$result}')"

  curl "${curl_common[@]}" -X POST "$ACK_URL" -d "$payload" >/dev/null || true
}

create_site() {
  local payload="$1"
  local domain php_version vhost_template site_user site_pass add_out

  domain="$(jq -r '.domain // .primaryDomain // empty' <<<"$payload")"
  php_version="$(jq -r '.phpVersion // "8.2"' <<<"$payload")"
  vhost_template="$(jq -r '.vhostTemplate // "Generic"' <<<"$payload")"

  if [ -z "$domain" ]; then
    echo '{"ok":false,"error":"missing domain in payload"}'
    return 0
  fi

  if clpctl site:list 2>/dev/null | grep -q "${domain}"; then
    echo "$(jq -n --arg domain "$domain" '{ok:true,alreadyExists:true,domain:$domain}')"
    return 0
  fi

  site_user="$(safe_user_from_payload "$payload")"
  site_pass="$(jq -r '.siteUserPassword // empty' <<<"$payload")"
  if [ -z "$site_pass" ]; then
    site_pass="$(openssl rand -base64 18 | tr -d '=+/' | cut -c1-16)"
  fi

  set +e
  add_out="$(clpctl site:add:php \
    --domainName="$domain" \
    --phpVersion="$php_version" \
    --vhostTemplate="$vhost_template" \
    --siteUser="$site_user" \
    --siteUserPassword="$site_pass" 2>&1)"
  local rc=$?
  set -e

  if [ "$rc" -ne 0 ]; then
    echo "$(jq -n --arg domain "$domain" --arg err "$add_out" '{ok:false,domain:$domain,error:$err}')"
    return 0
  fi

  if [ "$(jq -r '.installLetsEncrypt // "false"' <<<"$payload")" = "true" ]; then
    clpctl lets-encrypt:install:certificate --domainName="$domain" >/dev/null 2>&1 || true
  fi

  echo "$(jq -n --arg domain "$domain" --arg siteUser "$site_user" --arg phpVersion "$php_version" --arg output "$add_out" '{ok:true,domain:$domain,siteUser:$siteUser,phpVersion:$phpVersion,output:$output}')"
}

delete_site() {
  local payload="$1"
  local domain out

  domain="$(jq -r '.domain // .primaryDomain // empty' <<<"$payload")"
  if [ -z "$domain" ]; then
    echo '{"ok":false,"error":"missing domain in payload"}'
    return 0
  fi

  set +e
  out="$(clpctl site:delete --domainName="$domain" --force 2>&1)"
  local rc=$?
  set -e

  if [ "$rc" -ne 0 ]; then
    echo "$(jq -n --arg domain "$domain" --arg err "$out" '{ok:false,domain:$domain,error:$err}')"
    return 0
  fi

  echo "$(jq -n --arg domain "$domain" --arg output "$out" '{ok:true,domain:$domain,output:$output}')"
}

run_remote_command() {
  local payload="$1"
  local cmd out

  if [ "$ALLOW_RUN_COMMAND" != "1" ]; then
    echo '{"ok":false,"error":"RUN_COMMAND disabled by ALLOW_RUN_COMMAND=0"}'
    return 0
  fi

  cmd="$(jq -r '.command // empty' <<<"$payload")"
  if [ -z "$cmd" ]; then
    echo '{"ok":false,"error":"missing command in payload"}'
    return 0
  fi

  set +e
  out="$(bash -lc "$cmd" 2>&1)"
  local rc=$?
  set -e

  if [ "$rc" -ne 0 ]; then
    echo "$(jq -n --arg err "$out" '{ok:false,error:$err}')"
    return 0
  fi

  echo "$(jq -n --arg output "$out" '{ok:true,output:$output}')"
}

execute_task() {
  local task_type="$1"
  local payload="$2"

  case "$task_type" in
    CREATE_SITE)
      create_site "$payload"
      ;;
    DELETE_SITE)
      delete_site "$payload"
      ;;
    RUN_COMMAND)
      run_remote_command "$payload"
      ;;
    *)
      echo "$(jq -n --arg t "$task_type" '{ok:false,error:("unsupported taskType: " + $t)}')"
      ;;
  esac
}

log "programmit cloudpanel agent started for node=$NODE_KEY"

while true; do
  set +e
  resp="$(curl "${curl_common[@]}" -X POST "$PULL_URL" -d '{}' 2>&1)"
  rc=$?
  set -e

  if [ "$rc" -ne 0 ]; then
    log "pull error: $resp"
    sleep "$POLL_INTERVAL"
    continue
  fi

  task_id="$(jq -r '.task.id // empty' <<<"$resp")"
  if [ -z "$task_id" ]; then
    sleep "$POLL_INTERVAL"
    continue
  fi

  task_type="$(jq -r '.task.taskType // empty' <<<"$resp")"
  payload="$(jq -c '.task.payload // {}' <<<"$resp")"

  log "processing task=$task_id type=$task_type"

  result="$(execute_task "$task_type" "$payload")"
  ok="$(jq -r '.ok // false' <<<"$result")"

  if [ "$ok" = "true" ]; then
    ack_task "$task_id" "SUCCESS" "false" "" "$result"
    log "task=$task_id success"
  else
    err_msg="$(jq -r '.error // "task failed"' <<<"$result")"
    retry="true"
    if [[ "$err_msg" == unsupported* ]] || [[ "$err_msg" == RUN_COMMAND* ]] || [[ "$err_msg" == missing* ]]; then
      retry="false"
    fi
    ack_task "$task_id" "FAILED" "$retry" "$err_msg" "$result"
    log "task=$task_id failed error=$err_msg"
  fi

done
