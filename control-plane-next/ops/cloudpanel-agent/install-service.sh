#!/usr/bin/env bash
set -euo pipefail

if [ "$(id -u)" -ne 0 ]; then
  echo "run as root"
  exit 1
fi

if [ $# -lt 3 ]; then
  cat <<USAGE
Usage:
  $0 <CONTROL_BASE_URL> <NODE_KEY> <NODE_TOKEN> [INSECURE_TLS:0|1]

Example:
  $0 https://control.programmit.com vps-bo-01 super-long-token 0
USAGE
  exit 1
fi

CONTROL_BASE_URL="$1"
NODE_KEY="$2"
NODE_TOKEN="$3"
INSECURE_TLS="${4:-0}"

install -d /opt/programmit
cp -f ./programmit-cloudpanel-agent.sh /opt/programmit/programmit-cloudpanel-agent.sh
chmod 750 /opt/programmit/programmit-cloudpanel-agent.sh

cat > /etc/programmit-control-agent.env <<EOF
CONTROL_BASE_URL="$CONTROL_BASE_URL"
NODE_KEY="$NODE_KEY"
NODE_TOKEN="$NODE_TOKEN"
POLL_INTERVAL="5"
INSECURE_TLS="$INSECURE_TLS"
ALLOW_RUN_COMMAND="0"
EOF
chmod 600 /etc/programmit-control-agent.env

cat > /etc/systemd/system/programmit-control-agent.service <<'EOF'
[Unit]
Description=Programmit CloudPanel Agent
After=network-online.target
Wants=network-online.target

[Service]
Type=simple
Environment=ENV_FILE=/etc/programmit-control-agent.env
ExecStart=/opt/programmit/programmit-cloudpanel-agent.sh
Restart=always
RestartSec=3
User=root
Group=root

[Install]
WantedBy=multi-user.target
EOF

systemctl daemon-reload
systemctl enable --now programmit-control-agent
systemctl status programmit-control-agent --no-pager || true

echo
echo "Installed. Logs: journalctl -u programmit-control-agent -f"
