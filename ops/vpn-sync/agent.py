#!/usr/bin/env python3
import hashlib
import json
import os
import platform
import re
import socket
import subprocess
import sys
import urllib.request
from datetime import datetime, timezone

try:
    import pwd
except ImportError:  # pragma: no cover - Windows local validation only
    pwd = None

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
CONFIG_PATH = os.path.join(BASE_DIR, 'config.json')
SAFE_LINUX_USER = re.compile(r'^[A-Za-z0-9._-]{1,32}$')
DEFAULT_SKIP_USER_IDS = {1}
AGENT_VERSION = '20260315-runtime4'


def utc_now():
    return datetime.now(timezone.utc).strftime('%Y-%m-%dT%H:%M:%SZ')


def load_json(path, default):
    try:
        with open(path, 'r', encoding='utf-8') as fh:
            data = json.load(fh)
            if isinstance(default, dict) and isinstance(data, dict):
                return data
            return data
    except FileNotFoundError:
        return default
    except Exception:
        return default


def save_json(path, data):
    tmp_path = path + '.tmp'
    with open(tmp_path, 'w', encoding='utf-8') as fh:
        json.dump(data, fh, ensure_ascii=False, indent=2, sort_keys=True)
        fh.write('\n')
    os.replace(tmp_path, path)


def parse_ssh_fingerprint(output):
    text = str(output or '').strip()
    if not text:
        return ''
    parts = text.split()
    if len(parts) < 2:
        return ''
    return parts[1].strip()


def post_json(base, path, payload, timeout):
    raw = json.dumps(payload).encode('utf-8')
    req = urllib.request.Request(
        base.rstrip('/') + path,
        data=raw,
        headers={'Content-Type': 'application/json', 'Accept': 'application/json'},
        method='POST',
    )
    with urllib.request.urlopen(req, timeout=timeout) as resp:
        body = resp.read().decode('utf-8')
        return resp.status, json.loads(body)


def build_users_by_method(users):
    out = {}
    for user_id, payload in users.items():
        methods = payload.get('methods') if isinstance(payload, dict) else []
        if not isinstance(methods, list):
            methods = []
        for method_key in methods:
            method_key = str(method_key).strip().lower()
            if not method_key:
                continue
            bucket = out.setdefault(method_key, {})
            bucket[user_id] = payload
    return out


def normalize_method_keys(methods):
    out = []
    for method_key in methods if isinstance(methods, list) else []:
        method_text = str(method_key).strip().lower()
        if method_text:
            out.append(method_text)
    return sorted(set(out))


def normalize_username_key(payload):
    if not isinstance(payload, dict):
        return ''
    return str(payload.get('user_name') or '').strip().lower()


def payload_sort_key(user_id, payload):
    if not isinstance(payload, dict):
        return (0, 0, 0, 0)
    return (
        to_int(payload.get('_synced_event_id'), 0),
        int(payload_flags_active(payload)),
        int(payload_duration_active(payload)),
        to_int(payload.get('user_id', user_id), to_int(user_id, 0)),
    )


def deduplicate_users_by_username(users):
    if not isinstance(users, dict):
        return {}, {}

    kept = {}
    aliases = {}
    by_username = {}
    for user_id, payload in users.items():
        user_key = str(user_id).strip()
        if not user_key or not isinstance(payload, dict):
            kept[user_key] = payload
            continue

        username_key = normalize_username_key(payload)
        if not username_key:
            kept[user_key] = payload
            continue

        existing_user_id = by_username.get(username_key)
        if existing_user_id is None:
            by_username[username_key] = user_key
            kept[user_key] = payload
            continue

        existing_payload = kept.get(existing_user_id)
        if payload_sort_key(user_key, payload) >= payload_sort_key(existing_user_id, existing_payload):
            aliases[existing_user_id] = user_key
            kept.pop(existing_user_id, None)
            by_username[username_key] = user_key
            kept[user_key] = payload
        else:
            aliases[user_key] = by_username[username_key]

    return kept, aliases


def payload_targets_server(payload):
    if not isinstance(payload, dict):
        return False
    targets = payload.get('target_server_ids')
    return isinstance(targets, list) and len(targets) > 0


def payload_matches_server_methods(payload, method_keys):
    if payload_targets_server(payload):
        return True
    wanted = set(normalize_method_keys(method_keys))
    if not wanted:
        return True
    current = set(normalize_method_keys(payload.get('methods') if isinstance(payload, dict) else []))
    return len(wanted.intersection(current)) > 0


def filter_users_for_server(users, method_keys):
    if not isinstance(users, dict):
        return {}, 0
    kept = {}
    removed = 0
    for user_id, payload in users.items():
        if payload_matches_server_methods(payload, method_keys):
            kept[str(user_id).strip()] = payload
        else:
            removed += 1
    return kept, removed


def to_bool(value, default=False):
    if isinstance(value, bool):
        return value
    if value is None:
        return default
    if isinstance(value, int):
        return value != 0
    text = str(value).strip().lower()
    if text in ('1', 'true', 'yes', 'on'):
        return True
    if text in ('0', 'false', 'no', 'off'):
        return False
    return default


def to_int(value, default=0):
    try:
        return int(str(value).strip())
    except Exception:
        return default


def get_skip_user_ids(config):
    raw = config.get('skip_user_ids')
    if raw is None:
        return set(DEFAULT_SKIP_USER_IDS)
    if isinstance(raw, list):
        return {to_int(item, -1) for item in raw if to_int(item, -1) >= 0}
    return set(DEFAULT_SKIP_USER_IDS)


def safe_linux_username(value, prefix=''):
    username = (str(prefix or '') + str(value or '').strip()).strip()
    if not SAFE_LINUX_USER.match(username):
        return ''
    if username in ('root', 'nobody'):
        return ''
    return username


def linux_user_exists(username):
    if pwd is None:
        return False
    try:
        pwd.getpwnam(username)
        return True
    except KeyError:
        return False


def run_command(args, input_text=None):
    return subprocess.run(
        args,
        input=input_text,
        stdout=subprocess.PIPE,
        stderr=subprocess.PIPE,
        text=True,
        check=False,
    )


def guess_local_ip():
    sock = None
    try:
        sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        sock.connect(('8.8.8.8', 80))
        return sock.getsockname()[0]
    except Exception:
        return ''
    finally:
        if sock is not None:
            try:
                sock.close()
            except Exception:
                pass


def collect_ssh_hostkeys():
    hostkeys = {}
    paths = {
        'rsa': '/etc/ssh/ssh_host_rsa_key.pub',
        'ecdsa': '/etc/ssh/ssh_host_ecdsa_key.pub',
        'ed25519': '/etc/ssh/ssh_host_ed25519_key.pub',
    }
    for algo, path in paths.items():
        if not os.path.exists(path):
            continue
        sha256_result = run_command(['ssh-keygen', '-lf', path])
        md5_result = run_command(['ssh-keygen', '-lf', path, '-E', 'md5'])
        sha256_fp = parse_ssh_fingerprint(sha256_result.stdout if sha256_result.returncode == 0 else '')
        md5_fp = parse_ssh_fingerprint(md5_result.stdout if md5_result.returncode == 0 else '')
        if not sha256_fp and not md5_fp:
            continue
        hostkeys[algo] = {
            'sha256': sha256_fp,
            'md5': md5_fp,
        }
    return hostkeys


def collect_runtime_info(config):
    return {
        'agent_version': AGENT_VERSION,
        'hostname': socket.gethostname(),
        'fqdn': socket.getfqdn(),
        'local_ip': guess_local_ip(),
        'public_ip': str(config.get('public_ip') or '').strip(),
        'python_version': platform.python_version(),
        'platform': platform.platform(),
        'collected_at_utc': utc_now(),
        'sync_local_users': to_bool(config.get('sync_local_users', config.get('apply_linux_accounts', 1)), True),
        'apply_linux_accounts': to_bool(config.get('apply_linux_accounts', config.get('sync_local_users', 1)), True),
        'ssh_hostkeys': collect_ssh_hostkeys(),
    }


def account_is_locked(username):
    result = run_command(['passwd', '-S', username])
    if result.returncode != 0:
        return None
    parts = result.stdout.strip().split()
    if len(parts) < 2:
        return None
    return parts[1].upper().startswith('L')


def account_expiry_value(username):
    result = run_command(['getent', 'shadow', username])
    if result.returncode != 0:
        return None
    parts = result.stdout.strip().split(':')
    if len(parts) < 8:
        return ''
    return parts[7].strip()


def create_linux_user(username):
    commands = [
        ['useradd', '--badname', '-m', '-s', '/bin/bash', username],
        ['adduser', '--disabled-password', '--gecos', '', '--force-badname', username],
        ['useradd', '-m', '-s', '/bin/bash', username],
    ]
    last_error = ''
    for args in commands:
        result = run_command(args)
        if result.returncode == 0:
            return True, ''
        last_error = (result.stderr or result.stdout or '').strip()
    return False, last_error


def rename_linux_user(old_username, new_username):
    rename_result = run_command(['usermod', '--badname', '-l', new_username, old_username])
    if rename_result.returncode != 0:
        rename_result = run_command(['usermod', '-l', new_username, old_username])
    if rename_result.returncode != 0:
        return False, (rename_result.stderr or rename_result.stdout or '').strip()
    run_command(['groupmod', '-n', new_username, old_username])
    if os.path.isdir(os.path.join('/home', old_username)):
        run_command(['usermod', '-d', os.path.join('/home', new_username), '-m', new_username])
    return True, ''


def delete_linux_user(username):
    result = run_command(['userdel', '-r', username])
    if result.returncode == 0:
        return True, ''
    fallback = run_command(['userdel', username])
    if fallback.returncode == 0:
        return True, ''
    return False, (fallback.stderr or fallback.stdout or result.stderr or result.stdout or '').strip()


def set_linux_password(username, password):
    result = run_command(['chpasswd'], input_text=f'{username}:{password}\n')
    if result.returncode != 0:
        return False, (result.stderr or result.stdout or '').strip()
    return True, ''


def set_linux_expiry(username, expire_value):
    result = run_command(['chage', '-E', str(expire_value), username])
    if result.returncode != 0:
        return False, (result.stderr or result.stdout or '').strip()
    return True, ''


def lock_linux_user(username):
    result = run_command(['usermod', '-L', username])
    if result.returncode != 0:
        return False, (result.stderr or result.stdout or '').strip()
    expiry_result = set_linux_expiry(username, 0)
    if expiry_result[0] is False:
        return False, expiry_result[1]
    run_command(['pkill', '-KILL', '-u', username])
    return True, ''


def unlock_linux_user(username):
    result = run_command(['usermod', '-U', username])
    if result.returncode != 0:
        return False, (result.stderr or result.stdout or '').strip()
    expiry_result = set_linux_expiry(username, -1)
    if expiry_result[0] is False:
        return False, expiry_result[1]
    return True, ''


def password_fingerprint(password):
    return hashlib.sha256(password.encode('utf-8')).hexdigest()


def payload_duration_active(payload):
    if not isinstance(payload, dict):
        return False
    return any(
        to_int(payload.get(field), 0) > 0
        for field in ('duration', 'vip_duration', 'private_duration')
    )


def payload_flags_active(payload):
    if not isinstance(payload, dict):
        return False
    is_active = to_int(payload.get('is_active'), 0)
    is_ban = to_int(payload.get('is_ban'), 0)
    is_freeze = to_int(payload.get('is_freeze'), 0)
    status = str(payload.get('status') or '').strip().lower()
    state = str(payload.get('state') or '').strip().lower()
    effective_status = status or state
    return is_active == 1 and is_ban == 0 and is_freeze == 0 and effective_status in ('live', 'active')


def desired_linux_state(payload):
    if payload_duration_active(payload) and payload_flags_active(payload):
        return 'unlocked'
    return 'locked'


def resolve_user_password(payload, config):
    if isinstance(payload, dict):
        pass_plain = str(payload.get('pass_plain') or '').strip()
        if pass_plain:
            return pass_plain
    return str(config.get('default_linux_password') or '').strip()


def should_skip_payload(user_id, payload, config):
    if to_int(user_id, 0) in get_skip_user_ids(config):
        return True
    username = str(payload.get('user_name') or '').strip().lower() if isinstance(payload, dict) else ''
    if username in ('root', 'nobody'):
        return True
    return False


def write_legacy_counter(users, config):
    if not to_bool(config.get('write_legacy_counter', 1), True):
        return None

    counts = {
        'con': 0,
        'exp': 0,
        'lok': 0,
        'total': 0,
    }
    for user_id, payload in users.items():
        if not isinstance(payload, dict):
            continue
        if should_skip_payload(user_id, payload, config):
            continue
        username = safe_linux_username(payload.get('user_name'), str(config.get('local_user_prefix') or ''))
        if not username:
            continue

        desired_state = desired_linux_state(payload)
        if desired_state == 'unlocked':
            counts['con'] += 1
        else:
            counts['exp'] += 1

        if to_int(payload.get('is_freeze'), 0) == 1 or str(payload.get('status') or '').strip().lower() == 'freeze':
            counts['lok'] += 1
        counts['total'] += 1

    counter_path = str(config.get('legacy_counter_path') or '/tmp/contador')
    tmp_path = counter_path + '.new'
    with open(tmp_path, 'w', encoding='utf-8') as fh:
        fh.write(f'lok={counts["lok"]}\n')
        fh.write(f'exp={counts["exp"]}\n')
        fh.write(f'total={counts["total"]}\n')
        fh.write(f'con={counts["con"]}\n')
    os.replace(tmp_path, counter_path)
    os.chmod(counter_path, 0o644)
    return counts


def ensure_linux_user_present(username, managed_entry, payload, report, config):
    exists = linux_user_exists(username)
    created_by_sync = bool(managed_entry.get('created_by_sync'))
    if exists:
        return True, created_by_sync

    if not to_bool(config.get('sync_local_users', config.get('apply_linux_accounts', 1)), True):
        report['skipped_missing'] += 1
        return False, created_by_sync

    ok, error = create_linux_user(username)
    if not ok:
        report['errors'].append({'user_name': username, 'action': 'create', 'error': error})
        report['skipped_missing'] += 1
        return False, created_by_sync

    report['created'] += 1
    return True, True


def reconcile_linux_accounts(users, state_dir, config):
    enabled = to_bool(config.get('apply_linux_accounts', config.get('sync_local_users', 1)), True)
    report = {
        'enabled': enabled,
        'deduplicated_cache_users': 0,
        'forgotten_duplicate_state_users': 0,
        'pruned_off_method_users': 0,
        'created': 0,
        'renamed': 0,
        'deleted': 0,
        'password_updated': 0,
        'expiry_fixed': 0,
        'locked': 0,
        'unlocked': 0,
        'already_locked': 0,
        'already_unlocked': 0,
        'locked_missing': 0,
        'skipped_missing': 0,
        'skipped_invalid': 0,
        'skipped_reserved': 0,
        'errors': [],
        'updated_at_utc': utc_now(),
    }

    managed_path = os.path.join(state_dir, 'linux_account_state.json')
    report_path = os.path.join(state_dir, 'linux_apply_report.json')
    managed = load_json(managed_path, {'users': {}})
    managed_users = managed.get('users') if isinstance(managed, dict) else {}
    if not isinstance(managed_users, dict):
        managed_users = {}

    if not enabled:
        save_json(report_path, report)
        return report

    users, duplicate_aliases = deduplicate_users_by_username(users)
    report['deduplicated_cache_users'] = len(duplicate_aliases)

    seen_ids = set()
    seen_usernames = set()
    local_prefix = str(config.get('local_user_prefix') or '')
    manage_passwords = to_bool(config.get('manage_linux_passwords', 1), True)
    remove_missing = to_bool(config.get('remove_missing_linux_users', 0), False)
    lock_missing = to_bool(config.get('lock_missing_linux_users', 1), True)

    for user_id, payload in users.items():
        user_id_key = str(user_id).strip()
        if not user_id_key or not isinstance(payload, dict):
            continue
        if should_skip_payload(user_id_key, payload, config):
            report['skipped_reserved'] += 1
            continue

        username = safe_linux_username(payload.get('user_name'), local_prefix)
        if not username:
            report['skipped_invalid'] += 1
            continue

        managed_entry = managed_users.get(user_id_key, {})
        if not isinstance(managed_entry, dict):
            managed_entry = {}

        old_username = str(managed_entry.get('user_name') or '').strip()
        if old_username and old_username != username and linux_user_exists(old_username) and not linux_user_exists(username):
            ok, error = rename_linux_user(old_username, username)
            if ok:
                report['renamed'] += 1
            else:
                report['errors'].append({
                    'user_id': user_id_key,
                    'user_name': username,
                    'action': 'rename',
                    'error': error,
                })

        exists, created_by_sync = ensure_linux_user_present(username, managed_entry, payload, report, config)
        if not exists:
            managed_users[user_id_key] = {
                'user_name': username,
                'created_by_sync': created_by_sync,
                'last_seen_utc': utc_now(),
                'last_state': 'missing',
            }
            seen_ids.add(user_id_key)
            continue

        password = resolve_user_password(payload, config)
        password_sha = str(managed_entry.get('password_sha256') or '')
        if manage_passwords and password:
            desired_sha = password_fingerprint(password)
            if desired_sha != password_sha:
                ok, error = set_linux_password(username, password)
                if ok:
                    report['password_updated'] += 1
                    password_sha = desired_sha
                else:
                    report['errors'].append({
                        'user_id': user_id_key,
                        'user_name': username,
                        'action': 'password',
                        'error': error,
                    })

        desired_state = desired_linux_state(payload)
        locked = account_is_locked(username)
        expiry_value = account_expiry_value(username)
        if desired_state == 'locked':
            if locked is True and expiry_value == '0':
                report['already_locked'] += 1
            elif locked is True:
                ok, error = set_linux_expiry(username, 0)
                if ok:
                    report['already_locked'] += 1
                    report['expiry_fixed'] += 1
                else:
                    report['errors'].append({
                        'user_id': user_id_key,
                        'user_name': username,
                        'action': 'expiry_locked',
                        'error': error,
                    })
            else:
                ok, error = lock_linux_user(username)
                if ok:
                    report['locked'] += 1
                else:
                    report['errors'].append({
                        'user_id': user_id_key,
                        'user_name': username,
                        'action': 'lock',
                        'error': error,
                    })
        else:
            if locked is False and expiry_value in ('', '-1', None):
                report['already_unlocked'] += 1
            elif locked is False:
                expiry_ok, expiry_error = set_linux_expiry(username, -1)
                if expiry_ok:
                    report['already_unlocked'] += 1
                    report['expiry_fixed'] += 1
                else:
                    report['errors'].append({
                        'user_id': user_id_key,
                        'user_name': username,
                        'action': 'expiry_active',
                        'error': expiry_error,
                    })
            else:
                ok, error = unlock_linux_user(username)
                if ok:
                    report['unlocked'] += 1
                else:
                    report['errors'].append({
                        'user_id': user_id_key,
                        'user_name': username,
                        'action': 'unlock',
                        'error': error,
                    })

        managed_users[user_id_key] = {
            'user_id': to_int(user_id_key, 0),
            'user_name': username,
            'created_by_sync': created_by_sync,
            'password_sha256': password_sha,
            'last_seen_utc': utc_now(),
            'last_state': desired_state,
        }
        seen_ids.add(user_id_key)
        seen_usernames.add(username)

    stale_ids = [key for key in managed_users.keys() if key not in seen_ids]
    for user_id_key in stale_ids:
        entry = managed_users.get(user_id_key, {})
        if not isinstance(entry, dict):
            managed_users.pop(user_id_key, None)
            continue
        username = str(entry.get('user_name') or '').strip()
        if not username or not linux_user_exists(username):
            managed_users.pop(user_id_key, None)
            continue
        if username in seen_usernames:
            managed_users.pop(user_id_key, None)
            report['forgotten_duplicate_state_users'] += 1
            continue

        if remove_missing:
            ok, error = delete_linux_user(username)
            if ok:
                report['deleted'] += 1
                managed_users.pop(user_id_key, None)
                continue
            report['errors'].append({
                'user_id': user_id_key,
                'user_name': username,
                'action': 'delete_missing',
                'error': error,
            })

        if lock_missing:
            locked = account_is_locked(username)
            if locked is True:
                report['already_locked'] += 1
            else:
                ok, error = lock_linux_user(username)
                if ok:
                    report['locked_missing'] += 1
                else:
                    report['errors'].append({
                        'user_id': user_id_key,
                        'user_name': username,
                        'action': 'lock_missing',
                        'error': error,
                    })
            entry['last_state'] = 'locked_missing'
            entry['last_seen_utc'] = utc_now()
            managed_users[user_id_key] = entry

    save_json(managed_path, {'users': managed_users, 'updated_at_utc': utc_now()})
    save_json(report_path, report)
    return report


def main():
    config = load_json(CONFIG_PATH, {})
    if not config:
        raise RuntimeError('missing_config')

    state_dir = config.get('state_dir') or os.path.join(BASE_DIR, 'state')
    os.makedirs(state_dir, mode=0o700, exist_ok=True)
    cursor_path = os.path.join(state_dir, 'cursor.json')
    cache_path = os.path.join(state_dir, 'cache.json')
    by_method_path = os.path.join(state_dir, 'users_by_method.json')
    pull_path = os.path.join(state_dir, 'last_pull.json')

    state = load_json(cursor_path, {'cursor': 0})
    cache = load_json(cache_path, {'users': {}, 'meta': {}})
    if not isinstance(cache, dict):
        cache = {'users': {}, 'meta': {}}
    users = cache.get('users')
    if not isinstance(users, dict):
        users = {}

    cursor = int(state.get('cursor') or 0)
    timeout = int(config.get('timeout_seconds') or 25)
    limit = int(config.get('limit') or 200)
    runtime = collect_runtime_info(config)
    payload = {
        'server_key': config['server_key'],
        'sync_token': config['sync_token'],
        'cursor': cursor,
        'limit': limit,
        'runtime': runtime,
    }

    pull_status, pull_data = post_json(config['panel_base'], '/vpn_sync_pull.php', payload, timeout)
    if pull_status != 200 or not pull_data.get('ok'):
        raise RuntimeError('pull_failed')

    events = pull_data.get('events') or []
    if not isinstance(events, list):
        events = []
    method_keys = pull_data.get('method_keys') or []
    if not isinstance(method_keys, list):
        method_keys = []

    for event in events:
        if not isinstance(event, dict):
            continue
        event_type = str(event.get('event_type') or '').strip().lower()
        payload_data = event.get('payload') if isinstance(event.get('payload'), dict) else {}
        user_id = payload_data.get('user_id', event.get('user_id', 0))
        user_key = str(int(user_id)) if str(user_id).strip() not in ('', 'None') else ''
        if not user_key:
            continue
        if event_type == 'delete' or int(payload_data.get('deleted', 0) or 0) == 1:
            users.pop(user_key, None)
            continue
        payload_data['_synced_event_id'] = int(event.get('id') or 0)
        payload_data['_synced_event_type'] = event_type
        payload_data['_synced_event_at'] = str(event.get('created_at') or '')
        payload_data['_synced_at_utc'] = utc_now()
        payload_data['_synced_previous_methods'] = event.get('previous_methods') if isinstance(event.get('previous_methods'), list) else []
        if not payload_matches_server_methods(payload_data, method_keys):
            users.pop(user_key, None)
            continue
        users[user_key] = payload_data

    users, duplicate_aliases = deduplicate_users_by_username(users)
    users, pruned_off_method_users = filter_users_for_server(users, method_keys)

    next_cursor = int(pull_data.get('next_cursor') or cursor)
    ack_data = None
    if next_cursor > cursor:
        ack_status, ack_data = post_json(config['panel_base'], '/vpn_sync_ack.php', {
            'server_key': config['server_key'],
            'sync_token': config['sync_token'],
            'cursor_from': cursor,
            'ack_cursor': next_cursor,
            'runtime': runtime,
        }, timeout)
        if ack_status != 200 or not ack_data.get('ok'):
            raise RuntimeError('ack_failed')
        cursor = next_cursor

    cache = {
        'users': users,
        'meta': {
            'server_key': config['server_key'],
            'method_keys': method_keys,
            'events_count': len(events),
            'deduplicated_users': len(duplicate_aliases),
            'pruned_off_method_users': pruned_off_method_users,
            'cursor': cursor,
            'generated_at': pull_data.get('generated_at') or '',
            'last_sync_utc': utc_now(),
        }
    }
    save_json(cache_path, cache)
    save_json(by_method_path, build_users_by_method(users))
    save_json(cursor_path, {'cursor': cursor, 'updated_at_utc': utc_now()})

    apply_report = reconcile_linux_accounts(users, state_dir, config)
    apply_report['pruned_off_method_users'] = pruned_off_method_users
    legacy_counter = write_legacy_counter(users, config)
    save_json(pull_path, {
        'pull_status': pull_status,
        'events_count': len(events),
        'cursor_from': int(pull_data.get('cursor_from') or 0),
        'next_cursor': next_cursor,
        'method_keys': method_keys,
        'deduplicated_users': len(duplicate_aliases),
        'pruned_off_method_users': pruned_off_method_users,
        'ack_ok': bool(ack_data.get('ok')) if isinstance(ack_data, dict) else None,
        'last_sync_utc': utc_now(),
        'runtime': runtime,
        'apply_report': apply_report,
        'legacy_counter': legacy_counter,
    })
    print(json.dumps({
        'ok': True,
        'cursor': cursor,
        'events_count': len(events),
        'deduplicated_users': len(duplicate_aliases),
        'pruned_off_method_users': pruned_off_method_users,
        'users_cached': len(users),
        'apply_report': apply_report,
        'legacy_counter': legacy_counter,
    }, ensure_ascii=False))


if __name__ == '__main__':
    try:
        main()
    except Exception as exc:
        sys.stderr.write(str(exc) + '\n')
        sys.exit(1)
