<?php
if (preg_match("/vpn_control.php/i", $_SERVER['SCRIPT_NAME'])) {
    Header("Location: /");
    die();
}

function programmit_vpn_normalize_key($value) {
    $value = strtolower(trim((string)$value));
    if ($value === '') {
        return '';
    }
    $value = preg_replace('/[^a-z0-9_-]+/', '-', $value);
    $value = trim((string)$value, '-_');
    if ($value === '') {
        return '';
    }
    return substr($value, 0, 64);
}

function programmit_vpn_normalize_host($host) {
    $host = trim((string)$host);
    if ($host === '') {
        return '';
    }
    $host = preg_replace('#^https?://#i', '', $host);
    $slashPos = strpos($host, '/');
    if ($slashPos !== false) {
        $host = substr($host, 0, $slashPos);
    }
    $host = preg_replace('/:\d+$/', '', $host);
    return strtolower(trim((string)$host));
}

function programmit_vpn_json_decode($raw) {
    $raw = trim((string)$raw);
    if ($raw === '') {
        return array();
    }
    $arr = json_decode($raw, true);
    if (!is_array($arr)) {
        return array();
    }
    return $arr;
}

function programmit_vpn_json_encode($arr) {
    if (!is_array($arr)) {
        $arr = array();
    }
    $json = json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!is_string($json) || $json === '') {
        return '{}';
    }
    return $json;
}

function programmit_vpn_value_string($value, $maxLen = 255) {
    $value = trim((string)$value);
    if ($value === '') {
        return '';
    }
    $value = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $value);
    if ($maxLen > 0 && function_exists('mb_substr')) {
        $value = mb_substr($value, 0, $maxLen, 'UTF-8');
    } elseif ($maxLen > 0) {
        $value = substr($value, 0, $maxLen);
    }
    return trim((string)$value);
}

function programmit_vpn_is_ip($value) {
    $value = trim((string)$value);
    if ($value === '') {
        return false;
    }
    return filter_var($value, FILTER_VALIDATE_IP) !== false;
}

function programmit_vpn_unique_host_list($values) {
    $out = array();
    foreach ((array)$values as $value) {
        $host = programmit_vpn_normalize_host($value);
        if ($host === '') {
            continue;
        }
        $out[$host] = $host;
    }
    return array_values($out);
}

function programmit_vpn_unique_ip_list($values) {
    $out = array();
    foreach ((array)$values as $value) {
        $value = trim((string)$value);
        if (!programmit_vpn_is_ip($value)) {
            continue;
        }
        $out[$value] = $value;
    }
    return array_values($out);
}

function programmit_vpn_lists_intersect($left, $right) {
    if (!is_array($left) || !is_array($right) || empty($left) || empty($right)) {
        return array();
    }
    $left = array_values(array_unique(array_map('strval', $left)));
    $right = array_values(array_unique(array_map('strval', $right)));
    return array_values(array_intersect($left, $right));
}

function programmit_vpn_age_seconds($dateValue) {
    $dateValue = trim((string)$dateValue);
    if ($dateValue === '') {
        return -1;
    }
    $ts = strtotime($dateValue);
    if ($ts === false || $ts <= 0) {
        return -1;
    }
    return time() - $ts;
}

function programmit_vpn_server_identity_summary($row) {
    if (!is_array($row)) {
        return array('class' => 'off', 'label' => 'sin runtime', 'note' => 'Sin datos del agente');
    }

    $configHosts = programmit_vpn_unique_host_list(array(
        isset($row['server_host']) ? $row['server_host'] : '',
        isset($row['public_base_url']) ? $row['public_base_url'] : ''
    ));
    $configIps = programmit_vpn_unique_ip_list(array(
        isset($row['server_ip']) ? $row['server_ip'] : ''
    ));
    $runtimeHosts = programmit_vpn_unique_host_list(array(
        isset($row['runtime_fqdn']) ? $row['runtime_fqdn'] : '',
        isset($row['runtime_hostname']) ? $row['runtime_hostname'] : ''
    ));
    $runtimeIps = programmit_vpn_unique_ip_list(array(
        isset($row['runtime_request_ip']) ? $row['runtime_request_ip'] : '',
        isset($row['runtime_public_ip']) ? $row['runtime_public_ip'] : ''
    ));

    $hasRuntime = !empty($runtimeHosts) || !empty($runtimeIps) || trim((string)(isset($row['runtime_agent_version']) ? $row['runtime_agent_version'] : '')) !== '';
    if (!$hasRuntime) {
        return array('class' => 'off', 'label' => 'sin runtime', 'note' => 'El agente aun no reporta identidad');
    }

    $hostMatches = programmit_vpn_lists_intersect($configHosts, $runtimeHosts);
    $ipMatches = programmit_vpn_lists_intersect($configIps, $runtimeIps);

    if (!empty($hostMatches) || !empty($ipMatches)) {
        $noteParts = array();
        if (!empty($hostMatches)) {
            $noteParts[] = 'host ok';
        }
        if (!empty($ipMatches)) {
            $noteParts[] = 'ip ok';
        }
        if (empty($noteParts)) {
            $noteParts[] = 'runtime confirmado';
        }
        return array(
            'class' => 'ok',
            'label' => 'coincide',
            'note' => ucfirst(implode(' / ', $noteParts))
        );
    }

    if (empty($configHosts) && empty($configIps)) {
        return array(
            'class' => 'warn',
            'label' => 'sin config',
            'note' => 'El nodo reporta runtime pero el host/IP no estan definidos en el panel'
        );
    }

    $expected = array();
    if (!empty($configHosts)) {
        $expected[] = 'host ' . implode(', ', $configHosts);
    }
    if (!empty($configIps)) {
        $expected[] = 'ip ' . implode(', ', $configIps);
    }
    $seen = array();
    if (!empty($runtimeHosts)) {
        $seen[] = 'host ' . implode(', ', $runtimeHosts);
    }
    if (!empty($runtimeIps)) {
        $seen[] = 'ip ' . implode(', ', $runtimeIps);
    }

    return array(
        'class' => 'warn',
        'label' => 'revisar',
        'note' => 'Esperado: ' . implode(' | ', $expected) . '. Runtime: ' . implode(' | ', $seen)
    );
}

function programmit_vpn_server_sync_summary($row) {
    if (!is_array($row)) {
        return array('class' => 'off', 'label' => 'sin sync', 'note' => 'Sin actividad');
    }

    $candidates = array(
        isset($row['last_ack_at']) ? $row['last_ack_at'] : '',
        isset($row['last_sync_at']) ? $row['last_sync_at'] : '',
        isset($row['last_seen_at']) ? $row['last_seen_at'] : '',
        isset($row['runtime_collected_at_utc']) ? $row['runtime_collected_at_utc'] : ''
    );
    $bestAge = -1;
    foreach ($candidates as $candidate) {
        $age = programmit_vpn_age_seconds($candidate);
        if ($age < 0) {
            continue;
        }
        if ($bestAge < 0 || $age < $bestAge) {
            $bestAge = $age;
        }
    }

    if ($bestAge < 0) {
        return array('class' => 'off', 'label' => 'sin sync', 'note' => 'No hay pull/ack recientes');
    }
    if ($bestAge <= 180) {
        return array('class' => 'ok', 'label' => 'al dia', 'note' => 'Sync reciente');
    }
    if ($bestAge <= 900) {
        return array('class' => 'warn', 'label' => 'atrasado', 'note' => 'Revisar pull/ack del nodo');
    }
    return array('class' => 'off', 'label' => 'sin contacto', 'note' => 'El nodo no reporta hace rato');
}

function programmit_vpn_agent_runtime_normalize($runtime, $requestIp = '') {
    if (!is_array($runtime)) {
        $runtime = array();
    }

    $normalized = array(
        'agent_version' => programmit_vpn_value_string(isset($runtime['agent_version']) ? $runtime['agent_version'] : '', 64),
        'hostname' => programmit_vpn_value_string(isset($runtime['hostname']) ? $runtime['hostname'] : '', 191),
        'fqdn' => programmit_vpn_normalize_host(isset($runtime['fqdn']) ? $runtime['fqdn'] : ''),
        'local_ip' => programmit_vpn_value_string(isset($runtime['local_ip']) ? $runtime['local_ip'] : '', 64),
        'public_ip' => programmit_vpn_value_string(isset($runtime['public_ip']) ? $runtime['public_ip'] : '', 64),
        'python_version' => programmit_vpn_value_string(isset($runtime['python_version']) ? $runtime['python_version'] : '', 64),
        'platform' => programmit_vpn_value_string(isset($runtime['platform']) ? $runtime['platform'] : '', 191),
        'collected_at_utc' => programmit_vpn_value_string(isset($runtime['collected_at_utc']) ? $runtime['collected_at_utc'] : '', 64),
        'sync_local_users' => programmit_vpn_bool(isset($runtime['sync_local_users']) ? $runtime['sync_local_users'] : false) ? 1 : 0,
        'apply_linux_accounts' => programmit_vpn_bool(isset($runtime['apply_linux_accounts']) ? $runtime['apply_linux_accounts'] : false) ? 1 : 0
    );

    if (programmit_vpn_is_ip($requestIp)) {
        $normalized['request_ip_panel_seen'] = $requestIp;
    } else {
        $normalized['request_ip_panel_seen'] = '';
    }

    $sshHostkeys = array();
    if (isset($runtime['ssh_hostkeys']) && is_array($runtime['ssh_hostkeys'])) {
        foreach ($runtime['ssh_hostkeys'] as $algo => $fpRow) {
            $algo = strtolower(trim((string)$algo));
            if ($algo === '') {
                continue;
            }
            if (!is_array($fpRow)) {
                continue;
            }
            $md5 = programmit_vpn_value_string(isset($fpRow['md5']) ? $fpRow['md5'] : '', 128);
            $sha256 = programmit_vpn_value_string(isset($fpRow['sha256']) ? $fpRow['sha256'] : '', 128);
            if ($md5 === '' && $sha256 === '') {
                continue;
            }
            $sshHostkeys[$algo] = array(
                'md5' => $md5,
                'sha256' => $sha256
            );
        }
    }
    $normalized['ssh_hostkeys'] = $sshHostkeys;
    $normalized['updated_at'] = date('Y-m-d H:i:s');

    return $normalized;
}

function programmit_vpn_server_update_runtime($db, $serverId, $runtime, $requestIp = '') {
    $serverId = (int)$serverId;
    if ($serverId <= 0 || !is_array($runtime) || empty($runtime)) {
        return false;
    }

    $qry = $db->sql_query("SELECT server_host, server_ip, meta_json
        FROM vpn_servers
        WHERE id='" . $db->SanitizeForSQL($serverId) . "'
        LIMIT 1");
    $row = $db->sql_fetchrow($qry);
    if (!$row) {
        return false;
    }

    $meta = programmit_vpn_json_decode(isset($row['meta_json']) ? $row['meta_json'] : '');
    $meta['agent_runtime'] = programmit_vpn_agent_runtime_normalize($runtime, $requestIp);

    $setHostSql = '';
    $runtimeHost = '';
    if (isset($meta['agent_runtime']['fqdn']) && $meta['agent_runtime']['fqdn'] !== '') {
        $runtimeHost = $meta['agent_runtime']['fqdn'];
    } elseif (isset($meta['agent_runtime']['hostname'])) {
        $runtimeHost = programmit_vpn_normalize_host($meta['agent_runtime']['hostname']);
    }
    if (trim((string)(isset($row['server_host']) ? $row['server_host'] : '')) === '' && $runtimeHost !== '') {
        $setHostSql = ", server_host='" . $db->SanitizeForSQL($runtimeHost) . "'";
    }

    $setIpSql = '';
    $runtimeIp = '';
    if (isset($meta['agent_runtime']['request_ip_panel_seen']) && programmit_vpn_is_ip($meta['agent_runtime']['request_ip_panel_seen'])) {
        $runtimeIp = (string)$meta['agent_runtime']['request_ip_panel_seen'];
    } elseif (isset($meta['agent_runtime']['public_ip']) && programmit_vpn_is_ip($meta['agent_runtime']['public_ip'])) {
        $runtimeIp = (string)$meta['agent_runtime']['public_ip'];
    }
    if (trim((string)(isset($row['server_ip']) ? $row['server_ip'] : '')) === '' && $runtimeIp !== '') {
        $setIpSql = ", server_ip='" . $db->SanitizeForSQL($runtimeIp) . "'";
    }

    return (bool)$db->sql_query("UPDATE vpn_servers
        SET meta_json='" . $db->SanitizeForSQL(programmit_vpn_json_encode($meta)) . "'
            " . $setHostSql . "
            " . $setIpSql . ",
            last_seen_at=NOW(),
            updated_at=NOW()
        WHERE id='" . $db->SanitizeForSQL($serverId) . "'
        LIMIT 1");
}

function programmit_vpn_bool($value, $default = false) {
    if (is_bool($value)) {
        return $value;
    }
    $value = strtolower(trim((string)$value));
    if ($value === '') {
        return (bool)$default;
    }
    return in_array($value, array('1', 'true', 'yes', 'on', 'enabled'), true);
}

function programmit_vpn_schema_version() {
    return '20260311b';
}

function programmit_vpn_schema_stamp_path($db = null) {
    $tmpDir = rtrim((string)sys_get_temp_dir(), '/\\');
    if ($tmpDir === '') {
        $tmpDir = '/tmp';
    }
    $stampDir = $tmpDir . DIRECTORY_SEPARATOR . 'programmit_bootstrap';
    if (!is_dir($stampDir)) {
        @mkdir($stampDir, 0775, true);
    }
    $parts = array(
        programmit_vpn_schema_version(),
        'vpn_control',
        defined('DOC_ROOT_PATH') ? (string)DOC_ROOT_PATH : dirname(__DIR__),
        (is_object($db) && isset($db->db_driver)) ? (string)$db->db_driver : '',
        (is_object($db) && isset($db->db_host)) ? (string)$db->db_host : '',
        (is_object($db) && isset($db->database)) ? (string)$db->database : '',
        (is_object($db) && isset($db->db_schema)) ? (string)$db->db_schema : ''
    );
    $stampKey = substr(sha1(implode('|', $parts)), 0, 20);
    return $stampDir . DIRECTORY_SEPARATOR . 'vpn_control_schema_' . $stampKey . '.stamp';
}

function programmit_vpn_table_columns($db, $tableName, $refresh = false) {
    static $cache = array();

    $tableName = strtolower(trim((string)$tableName));
    if ($tableName === '') {
        return array();
    }

    $driver = method_exists($db, 'get_db_driver') ? (string)$db->get_db_driver() : '';
    $cacheKey = $driver . '|' . $tableName;
    if (!$refresh && isset($cache[$cacheKey]) && is_array($cache[$cacheKey])) {
        return $cache[$cacheKey];
    }

    $columns = array();
    $qry = $db->sql_query("SHOW COLUMNS FROM " . $tableName);
    while ($row = $db->sql_fetchrow($qry)) {
        if (!$row) {
            continue;
        }
        $field = '';
        if (isset($row['Field'])) {
            $field = (string)$row['Field'];
        } elseif (isset($row['field'])) {
            $field = (string)$row['field'];
        }
        $field = strtolower(trim($field));
        if ($field !== '') {
            $columns[$field] = $row;
        }
    }

    if (empty($columns) && method_exists($db, 'is_pgsql') && $db->is_pgsql()) {
        $qry = $db->sql_query("SELECT column_name, data_type, is_nullable, column_default
            FROM information_schema.columns
            WHERE table_schema=current_schema()
              AND table_name='" . $db->SanitizeForSQL($tableName) . "'
            ORDER BY ordinal_position");
        while ($row = $db->sql_fetchrow($qry)) {
            if (!$row || !isset($row['column_name'])) {
                continue;
            }
            $field = strtolower(trim((string)$row['column_name']));
            if ($field !== '') {
                $columns[$field] = $row;
            }
        }
    }

    $cache[$cacheKey] = $columns;
    return $columns;
}

function programmit_vpn_table_has_column($db, $tableName, $columnName) {
    $columnName = strtolower(trim((string)$columnName));
    if ($columnName === '') {
        return false;
    }
    $columns = programmit_vpn_table_columns($db, $tableName);
    return isset($columns[$columnName]);
}

function programmit_vpn_table_refresh_columns($db, $tableName) {
    return programmit_vpn_table_columns($db, $tableName, true);
}

function programmit_vpn_add_column_if_missing($db, $tableName, $columnName, $columnSql) {
    if (programmit_vpn_table_has_column($db, $tableName, $columnName)) {
        return false;
    }
    $db->sql_query("ALTER TABLE " . $tableName . " ADD COLUMN " . $columnSql);
    programmit_vpn_table_refresh_columns($db, $tableName);
    return true;
}

function programmit_vpn_pg_ensure_id_default($db, $tableName, $columnName = 'id') {
    if (!(method_exists($db, 'is_pgsql') && $db->is_pgsql())) {
        return false;
    }

    $tableName = strtolower(trim((string)$tableName));
    $columnName = strtolower(trim((string)$columnName));
    if ($tableName === '' || $columnName === '') {
        return false;
    }
    if (!programmit_vpn_table_has_column($db, $tableName, $columnName)) {
        return false;
    }

    $qry = $db->sql_query("SELECT column_default
        FROM information_schema.columns
        WHERE table_schema=current_schema()
          AND table_name='" . $db->SanitizeForSQL($tableName) . "'
          AND column_name='" . $db->SanitizeForSQL($columnName) . "'
        LIMIT 1");
    $row = $db->sql_fetchrow($qry);
    $currentDefault = ($row && isset($row['column_default'])) ? trim((string)$row['column_default']) : '';
    if ($currentDefault !== '') {
        return false;
    }

    $sequenceName = $tableName . '_' . $columnName . '_seq';
    $db->sql_query("CREATE SEQUENCE IF NOT EXISTS " . $sequenceName);
    $db->sql_query("ALTER SEQUENCE " . $sequenceName . " OWNED BY " . $tableName . "." . $columnName);
    $db->sql_query("ALTER TABLE " . $tableName . " ALTER COLUMN " . $columnName . " SET DEFAULT nextval('" . $sequenceName . "'::regclass)");
    $db->sql_query("SELECT setval('" . $sequenceName . "', COALESCE((SELECT MAX(" . $columnName . ") FROM " . $tableName . "), 0) + 1, false)");
    programmit_vpn_table_refresh_columns($db, $tableName);
    return true;
}

function programmit_vpn_table_column_default($db, $tableName, $columnName) {
    $columns = programmit_vpn_table_columns($db, $tableName);
    $columnName = strtolower(trim((string)$columnName));
    if ($columnName === '' || !isset($columns[$columnName]) || !is_array($columns[$columnName])) {
        return '';
    }
    $row = $columns[$columnName];
    if (isset($row['Default'])) {
        return trim((string)$row['Default']);
    }
    if (isset($row['default'])) {
        return trim((string)$row['default']);
    }
    if (isset($row['column_default'])) {
        return trim((string)$row['column_default']);
    }
    return '';
}

function programmit_vpn_table_needs_manual_id($db, $tableName, $columnName = 'id') {
    if (!programmit_vpn_table_has_column($db, $tableName, $columnName)) {
        return false;
    }
    $defaultValue = programmit_vpn_table_column_default($db, $tableName, $columnName);
    return ($defaultValue === '');
}

function programmit_vpn_next_id($db, $tableName, $columnName = 'id') {
    $columnName = preg_replace('/[^a-z0-9_]+/i', '', (string)$columnName);
    $tableName = preg_replace('/[^a-z0-9_]+/i', '', (string)$tableName);
    if ($columnName === '' || $tableName === '') {
        return 1;
    }
    $qry = $db->sql_query("SELECT COALESCE(MAX(" . $columnName . "), 0) + 1 AS next_id
        FROM " . $tableName);
    $row = $db->sql_fetchrow($qry);
    if ($row && isset($row['next_id'])) {
        return (int)$row['next_id'];
    }
    return 1;
}

function programmit_vpn_insert_id_parts($db, $tableName, $columnName = 'id') {
    if (!programmit_vpn_table_needs_manual_id($db, $tableName, $columnName)) {
        return array('columns' => '', 'values' => '');
    }
    $nextId = programmit_vpn_next_id($db, $tableName, $columnName);
    return array(
        'columns' => ', ' . $columnName,
        'values' => ", '" . $db->SanitizeForSQL($nextId) . "'"
    );
}

function programmit_vpn_upgrade_schema($db) {
    $db->sql_query(programmit_vpn_settings_table_sql($db));
    $db->sql_query(programmit_vpn_servers_table_sql($db));
    $db->sql_query(programmit_vpn_methods_table_sql($db));
    $db->sql_query(programmit_vpn_method_map_table_sql($db));
    $db->sql_query(programmit_vpn_user_assignments_table_sql($db));
    $db->sql_query(programmit_vpn_user_snapshots_table_sql($db));
    $db->sql_query(programmit_vpn_sync_events_table_sql($db));
    $db->sql_query(programmit_vpn_sync_logs_table_sql($db));

    if (method_exists($db, 'is_pgsql') && $db->is_pgsql()) {
        foreach (array(
            'vpn_settings',
            'vpn_method_server_map',
            'vpn_user_method_assignments',
            'vpn_sync_events',
            'vpn_sync_logs'
        ) as $tableName) {
            programmit_vpn_pg_ensure_id_default($db, $tableName, 'id');
        }
    }

    programmit_vpn_add_column_if_missing($db, 'vpn_servers', 'server_host', "server_host VARCHAR(191) NOT NULL DEFAULT ''");
    programmit_vpn_add_column_if_missing($db, 'vpn_servers', 'server_ip', "server_ip VARCHAR(64) NOT NULL DEFAULT ''");
    programmit_vpn_add_column_if_missing($db, 'vpn_servers', 'server_port', "server_port INTEGER NOT NULL DEFAULT 443");
    programmit_vpn_add_column_if_missing($db, 'vpn_servers', 'server_provider', "server_provider VARCHAR(64) NOT NULL DEFAULT 'custom'");
    programmit_vpn_add_column_if_missing($db, 'vpn_servers', 'legacy_category', "legacy_category VARCHAR(32) NOT NULL DEFAULT ''");
    programmit_vpn_add_column_if_missing($db, 'vpn_servers', 'location_label', "location_label VARCHAR(120) NOT NULL DEFAULT ''");
    programmit_vpn_add_column_if_missing($db, 'vpn_servers', 'public_base_url', "public_base_url VARCHAR(255) NOT NULL DEFAULT ''");
    programmit_vpn_add_column_if_missing($db, 'vpn_servers', 'sync_token_hash', "sync_token_hash VARCHAR(128) NOT NULL DEFAULT ''");
    programmit_vpn_add_column_if_missing($db, 'vpn_servers', 'sync_enabled', "sync_enabled SMALLINT NOT NULL DEFAULT 1");
    programmit_vpn_add_column_if_missing($db, 'vpn_servers', 'is_public', "is_public SMALLINT NOT NULL DEFAULT 1");
    programmit_vpn_add_column_if_missing($db, 'vpn_servers', 'last_sync_at', "last_sync_at TIMESTAMP NULL");
    programmit_vpn_add_column_if_missing($db, 'vpn_servers', 'last_ack_at', "last_ack_at TIMESTAMP NULL");
    programmit_vpn_add_column_if_missing($db, 'vpn_servers', 'last_sync_cursor', "last_sync_cursor INTEGER NOT NULL DEFAULT 0");
    programmit_vpn_add_column_if_missing($db, 'vpn_servers', 'public_payload_json', "public_payload_json TEXT NULL");
    programmit_vpn_add_column_if_missing($db, 'vpn_servers', 'meta_json', "meta_json TEXT NULL");

    programmit_vpn_add_column_if_missing($db, 'vpn_methods', 'method_type', "method_type VARCHAR(32) NOT NULL DEFAULT 'custom'");
    programmit_vpn_add_column_if_missing($db, 'vpn_methods', 'legacy_group', "legacy_group VARCHAR(32) NOT NULL DEFAULT ''");
    programmit_vpn_add_column_if_missing($db, 'vpn_methods', 'auth_mode', "auth_mode VARCHAR(32) NOT NULL DEFAULT 'local'");
    programmit_vpn_add_column_if_missing($db, 'vpn_methods', 'is_public', "is_public SMALLINT NOT NULL DEFAULT 1");
    programmit_vpn_add_column_if_missing($db, 'vpn_methods', 'config_json', "config_json TEXT NULL");

    if (programmit_vpn_table_has_column($db, 'vpn_servers', 'provider') && programmit_vpn_table_has_column($db, 'vpn_servers', 'server_provider')) {
        $db->sql_query("UPDATE vpn_servers
            SET server_provider=provider
            WHERE COALESCE(server_provider, '')=''
              AND COALESCE(provider, '')<>''");
    }
    if (programmit_vpn_table_has_column($db, 'vpn_servers', 'public_host') && programmit_vpn_table_has_column($db, 'vpn_servers', 'server_host')) {
        $db->sql_query("UPDATE vpn_servers
            SET server_host=public_host
            WHERE COALESCE(server_host, '')=''
              AND COALESCE(public_host, '')<>''");
    }
    if (programmit_vpn_table_has_column($db, 'vpn_servers', 'public_ip') && programmit_vpn_table_has_column($db, 'vpn_servers', 'server_ip')) {
        $db->sql_query("UPDATE vpn_servers
            SET server_ip=public_ip
            WHERE COALESCE(server_ip, '')=''
              AND COALESCE(public_ip, '')<>''");
    }
    if (programmit_vpn_table_has_column($db, 'vpn_servers', 'region') && programmit_vpn_table_has_column($db, 'vpn_servers', 'location_label')) {
        $db->sql_query("UPDATE vpn_servers
            SET location_label=region
            WHERE COALESCE(location_label, '')=''
              AND COALESCE(region, '')<>''");
    }
    if (programmit_vpn_table_has_column($db, 'vpn_servers', 'api_base_url') && programmit_vpn_table_has_column($db, 'vpn_servers', 'public_base_url')) {
        $db->sql_query("UPDATE vpn_servers
            SET public_base_url=api_base_url
            WHERE COALESCE(public_base_url, '')=''
              AND COALESCE(api_base_url, '')<>''");
    }
    if (programmit_vpn_table_has_column($db, 'vpn_servers', 'is_sync_enabled') && programmit_vpn_table_has_column($db, 'vpn_servers', 'sync_enabled')) {
        $db->sql_query("UPDATE vpn_servers
            SET sync_enabled=is_sync_enabled
            WHERE sync_enabled<>is_sync_enabled");
    }

    if (programmit_vpn_table_has_column($db, 'vpn_methods', 'category') && programmit_vpn_table_has_column($db, 'vpn_methods', 'method_type')) {
        $db->sql_query("UPDATE vpn_methods
            SET method_type=category
            WHERE COALESCE(method_type, '') IN ('', 'custom')
              AND COALESCE(category, '')<>''");
        $db->sql_query("UPDATE vpn_methods
            SET legacy_group=category
            WHERE COALESCE(legacy_group, '')=''
              AND COALESCE(category, '')<>''");
    }
    if (programmit_vpn_table_has_column($db, 'vpn_methods', 'auth_strategy') && programmit_vpn_table_has_column($db, 'vpn_methods', 'auth_mode')) {
        $db->sql_query("UPDATE vpn_methods
            SET auth_mode=auth_strategy
            WHERE COALESCE(auth_mode, '') IN ('', 'local')
              AND COALESCE(auth_strategy, '')<>''");
    }
    if (programmit_vpn_table_has_column($db, 'vpn_methods', 'ui_config_json') && programmit_vpn_table_has_column($db, 'vpn_methods', 'config_json')) {
        $db->sql_query("UPDATE vpn_methods
            SET config_json=ui_config_json
            WHERE COALESCE(config_json, '')=''
              AND COALESCE(ui_config_json, '')<>''");
    }
}

function programmit_vpn_pg_create_table($tableName, $columns, $constraints = array()) {
    $lines = array_merge($columns, $constraints);
    return "CREATE TABLE IF NOT EXISTS " . $tableName . " (\n        " . implode(",\n        ", $lines) . "\n    )";
}

function programmit_vpn_mysql_create_table($tableName, $columns, $constraints = array()) {
    $lines = array_merge($columns, $constraints);
    return "CREATE TABLE IF NOT EXISTS " . $tableName . " (\n        " . implode(",\n        ", $lines) . "\n    )";
}

function programmit_vpn_table_sql($db, $tableName, $pgColumns, $pgConstraints, $mysqlColumns, $mysqlConstraints) {
    if (method_exists($db, 'is_pgsql') && $db->is_pgsql()) {
        return programmit_vpn_pg_create_table($tableName, $pgColumns, $pgConstraints);
    }
    return programmit_vpn_mysql_create_table($tableName, $mysqlColumns, $mysqlConstraints);
}

function programmit_vpn_settings_table_sql($db) {
    return programmit_vpn_table_sql(
        $db,
        'vpn_settings',
        array(
            'id INTEGER GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY',
            'setting_key VARCHAR(64) NOT NULL',
            'setting_value TEXT NULL',
            'created_at TIMESTAMP NOT NULL',
            'updated_at TIMESTAMP NULL'
        ),
        array(
            'UNIQUE (setting_key)'
        ),
        array(
            'id INT(11) NOT NULL AUTO_INCREMENT',
            'setting_key VARCHAR(64) NOT NULL',
            'setting_value TEXT NULL',
            'created_at DATETIME NOT NULL',
            'updated_at DATETIME DEFAULT NULL',
            'PRIMARY KEY (id)',
            'UNIQUE KEY uniq_vpn_setting_key (setting_key)'
        ),
        array()
    );
}

function programmit_vpn_servers_table_sql($db) {
    return programmit_vpn_table_sql(
        $db,
        'vpn_servers',
        array(
            'id INTEGER GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY',
            'server_key VARCHAR(64) NOT NULL',
            'server_name VARCHAR(140) NOT NULL',
            'server_host VARCHAR(191) NOT NULL DEFAULT \'\'',
            'server_ip VARCHAR(64) NOT NULL DEFAULT \'\'',
            'server_port INTEGER NOT NULL DEFAULT 443',
            'server_provider VARCHAR(64) NOT NULL DEFAULT \'custom\'',
            'legacy_category VARCHAR(32) NOT NULL DEFAULT \'\'',
            'country_code VARCHAR(8) NOT NULL DEFAULT \'\'',
            'location_label VARCHAR(120) NOT NULL DEFAULT \'\'',
            'public_base_url VARCHAR(255) NOT NULL DEFAULT \'\'',
            'sync_token_hash VARCHAR(128) NOT NULL DEFAULT \'\'',
            'sync_enabled SMALLINT NOT NULL DEFAULT 1',
            'is_public SMALLINT NOT NULL DEFAULT 1',
            'status VARCHAR(24) NOT NULL DEFAULT \'active\'',
            'last_seen_at TIMESTAMP NULL',
            'last_sync_at TIMESTAMP NULL',
            'last_ack_at TIMESTAMP NULL',
            'last_sync_cursor INTEGER NOT NULL DEFAULT 0',
            'public_payload_json TEXT NULL',
            'meta_json TEXT NULL',
            'created_at TIMESTAMP NOT NULL',
            'updated_at TIMESTAMP NULL'
        ),
        array(
            'UNIQUE (server_key)'
        ),
        array(
            'id INT(11) NOT NULL AUTO_INCREMENT',
            'server_key VARCHAR(64) NOT NULL',
            'server_name VARCHAR(140) NOT NULL',
            'server_host VARCHAR(191) NOT NULL DEFAULT \'\'',
            'server_ip VARCHAR(64) NOT NULL DEFAULT \'\'',
            'server_port INT(11) NOT NULL DEFAULT 443',
            'server_provider VARCHAR(64) NOT NULL DEFAULT \'custom\'',
            'legacy_category VARCHAR(32) NOT NULL DEFAULT \'\'',
            'country_code VARCHAR(8) NOT NULL DEFAULT \'\'',
            'location_label VARCHAR(120) NOT NULL DEFAULT \'\'',
            'public_base_url VARCHAR(255) NOT NULL DEFAULT \'\'',
            'sync_token_hash VARCHAR(128) NOT NULL DEFAULT \'\'',
            'sync_enabled TINYINT(1) NOT NULL DEFAULT 1',
            'is_public TINYINT(1) NOT NULL DEFAULT 1',
            'status VARCHAR(24) NOT NULL DEFAULT \'active\'',
            'last_seen_at DATETIME DEFAULT NULL',
            'last_sync_at DATETIME DEFAULT NULL',
            'last_ack_at DATETIME DEFAULT NULL',
            'last_sync_cursor INT(11) NOT NULL DEFAULT 0',
            'public_payload_json TEXT NULL',
            'meta_json TEXT NULL',
            'created_at DATETIME NOT NULL',
            'updated_at DATETIME DEFAULT NULL',
            'PRIMARY KEY (id)',
            'UNIQUE KEY uniq_vpn_server_key (server_key)'
        ),
        array()
    );
}

function programmit_vpn_methods_table_sql($db) {
    return programmit_vpn_table_sql(
        $db,
        'vpn_methods',
        array(
            'id INTEGER GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY',
            'method_key VARCHAR(64) NOT NULL',
            'method_name VARCHAR(140) NOT NULL',
            'method_type VARCHAR(32) NOT NULL DEFAULT \'custom\'',
            'legacy_group VARCHAR(32) NOT NULL DEFAULT \'\'',
            'auth_mode VARCHAR(32) NOT NULL DEFAULT \'local\'',
            'is_active SMALLINT NOT NULL DEFAULT 1',
            'is_public SMALLINT NOT NULL DEFAULT 1',
            'sort_order INTEGER NOT NULL DEFAULT 100',
            'config_json TEXT NULL',
            'created_at TIMESTAMP NOT NULL',
            'updated_at TIMESTAMP NULL'
        ),
        array(
            'UNIQUE (method_key)'
        ),
        array(
            'id INT(11) NOT NULL AUTO_INCREMENT',
            'method_key VARCHAR(64) NOT NULL',
            'method_name VARCHAR(140) NOT NULL',
            'method_type VARCHAR(32) NOT NULL DEFAULT \'custom\'',
            'legacy_group VARCHAR(32) NOT NULL DEFAULT \'\'',
            'auth_mode VARCHAR(32) NOT NULL DEFAULT \'local\'',
            'is_active TINYINT(1) NOT NULL DEFAULT 1',
            'is_public TINYINT(1) NOT NULL DEFAULT 1',
            'sort_order INT(11) NOT NULL DEFAULT 100',
            'config_json TEXT NULL',
            'created_at DATETIME NOT NULL',
            'updated_at DATETIME DEFAULT NULL',
            'PRIMARY KEY (id)',
            'UNIQUE KEY uniq_vpn_method_key (method_key)'
        ),
        array()
    );
}

function programmit_vpn_method_map_table_sql($db) {
    return programmit_vpn_table_sql(
        $db,
        'vpn_method_server_map',
        array(
            'id INTEGER GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY',
            'method_id INTEGER NOT NULL',
            'server_id INTEGER NOT NULL',
            'endpoint_protocol VARCHAR(16) NOT NULL DEFAULT \'https\'',
            'endpoint_host VARCHAR(191) NOT NULL DEFAULT \'\'',
            'endpoint_port INTEGER NOT NULL DEFAULT 443',
            'deploy_path VARCHAR(191) NOT NULL DEFAULT \'\'',
            'tls_sni VARCHAR(191) NOT NULL DEFAULT \'\'',
            'weight INTEGER NOT NULL DEFAULT 100',
            'is_active SMALLINT NOT NULL DEFAULT 1',
            'is_default SMALLINT NOT NULL DEFAULT 0',
            'config_json TEXT NULL',
            'created_at TIMESTAMP NOT NULL',
            'updated_at TIMESTAMP NULL'
        ),
        array(
            'UNIQUE (method_id, server_id)'
        ),
        array(
            'id INT(11) NOT NULL AUTO_INCREMENT',
            'method_id INT(11) NOT NULL',
            'server_id INT(11) NOT NULL',
            'endpoint_protocol VARCHAR(16) NOT NULL DEFAULT \'https\'',
            'endpoint_host VARCHAR(191) NOT NULL DEFAULT \'\'',
            'endpoint_port INT(11) NOT NULL DEFAULT 443',
            'deploy_path VARCHAR(191) NOT NULL DEFAULT \'\'',
            'tls_sni VARCHAR(191) NOT NULL DEFAULT \'\'',
            'weight INT(11) NOT NULL DEFAULT 100',
            'is_active TINYINT(1) NOT NULL DEFAULT 1',
            'is_default TINYINT(1) NOT NULL DEFAULT 0',
            'config_json TEXT NULL',
            'created_at DATETIME NOT NULL',
            'updated_at DATETIME DEFAULT NULL',
            'PRIMARY KEY (id)',
            'UNIQUE KEY uniq_vpn_method_server (method_id, server_id)'
        ),
        array()
    );
}

function programmit_vpn_user_assignments_table_sql($db) {
    return programmit_vpn_table_sql(
        $db,
        'vpn_user_method_assignments',
        array(
            'id INTEGER GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY',
            'user_id INTEGER NOT NULL',
            'method_key VARCHAR(64) NOT NULL',
            'desired_state VARCHAR(24) NOT NULL DEFAULT \'active\'',
            'notes VARCHAR(191) NOT NULL DEFAULT \'\'',
            'created_at TIMESTAMP NOT NULL',
            'updated_at TIMESTAMP NULL'
        ),
        array(
            'UNIQUE (user_id, method_key)'
        ),
        array(
            'id INT(11) NOT NULL AUTO_INCREMENT',
            'user_id INT(11) NOT NULL',
            'method_key VARCHAR(64) NOT NULL',
            'desired_state VARCHAR(24) NOT NULL DEFAULT \'active\'',
            'notes VARCHAR(191) NOT NULL DEFAULT \'\'',
            'created_at DATETIME NOT NULL',
            'updated_at DATETIME DEFAULT NULL',
            'PRIMARY KEY (id)',
            'UNIQUE KEY uniq_vpn_user_method (user_id, method_key)'
        ),
        array()
    );
}

function programmit_vpn_user_snapshots_table_sql($db) {
    return programmit_vpn_table_sql(
        $db,
        'vpn_user_snapshots',
        array(
            'user_id INTEGER PRIMARY KEY',
            'snapshot_hash VARCHAR(64) NOT NULL DEFAULT \'\'',
            'methods_json TEXT NULL',
            'payload_json TEXT NULL',
            'updated_at TIMESTAMP NOT NULL'
        ),
        array(),
        array(
            'user_id INT(11) NOT NULL',
            'snapshot_hash VARCHAR(64) NOT NULL DEFAULT \'\'',
            'methods_json TEXT NULL',
            'payload_json TEXT NULL',
            'updated_at DATETIME NOT NULL',
            'PRIMARY KEY (user_id)'
        ),
        array()
    );
}

function programmit_vpn_sync_events_table_sql($db) {
    return programmit_vpn_table_sql(
        $db,
        'vpn_sync_events',
        array(
            'id INTEGER GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY',
            'event_type VARCHAR(24) NOT NULL DEFAULT \'upsert\'',
            'user_id INTEGER NOT NULL DEFAULT 0',
            'user_name VARCHAR(128) NOT NULL DEFAULT \'\'',
            'methods_json TEXT NULL',
            'previous_methods_json TEXT NULL',
            'payload_json TEXT NULL',
            'payload_hash VARCHAR(64) NOT NULL DEFAULT \'\'',
            'created_at TIMESTAMP NOT NULL'
        ),
        array(),
        array(
            'id INT(11) NOT NULL AUTO_INCREMENT',
            'event_type VARCHAR(24) NOT NULL DEFAULT \'upsert\'',
            'user_id INT(11) NOT NULL DEFAULT 0',
            'user_name VARCHAR(128) NOT NULL DEFAULT \'\'',
            'methods_json TEXT NULL',
            'previous_methods_json TEXT NULL',
            'payload_json TEXT NULL',
            'payload_hash VARCHAR(64) NOT NULL DEFAULT \'\'',
            'created_at DATETIME NOT NULL',
            'PRIMARY KEY (id)'
        ),
        array()
    );
}

function programmit_vpn_sync_logs_table_sql($db) {
    return programmit_vpn_table_sql(
        $db,
        'vpn_sync_logs',
        array(
            'id INTEGER GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY',
            'server_id INTEGER NOT NULL DEFAULT 0',
            'action_name VARCHAR(24) NOT NULL DEFAULT \'pull\'',
            'status VARCHAR(24) NOT NULL DEFAULT \'ok\'',
            'cursor_from INTEGER NOT NULL DEFAULT 0',
            'cursor_to INTEGER NOT NULL DEFAULT 0',
            'events_count INTEGER NOT NULL DEFAULT 0',
            'request_ip VARCHAR(64) NOT NULL DEFAULT \'\'',
            'details_json TEXT NULL',
            'created_at TIMESTAMP NOT NULL'
        ),
        array(),
        array(
            'id INT(11) NOT NULL AUTO_INCREMENT',
            'server_id INT(11) NOT NULL DEFAULT 0',
            'action_name VARCHAR(24) NOT NULL DEFAULT \'pull\'',
            'status VARCHAR(24) NOT NULL DEFAULT \'ok\'',
            'cursor_from INT(11) NOT NULL DEFAULT 0',
            'cursor_to INT(11) NOT NULL DEFAULT 0',
            'events_count INT(11) NOT NULL DEFAULT 0',
            'request_ip VARCHAR(64) NOT NULL DEFAULT \'\'',
            'details_json TEXT NULL',
            'created_at DATETIME NOT NULL',
            'PRIMARY KEY (id)'
        ),
        array()
    );
}

function programmit_vpn_settings_cache_bucket() {
    if (!isset($GLOBALS['_programmit_vpn_settings_cache']) || !is_array($GLOBALS['_programmit_vpn_settings_cache'])) {
        $GLOBALS['_programmit_vpn_settings_cache'] = array(
            '__loaded' => 0,
            '__data' => array()
        );
    }
    return $GLOBALS['_programmit_vpn_settings_cache'];
}

function programmit_vpn_get_setting($db, $key, $default = '') {
    $key = strtolower(trim((string)$key));
    if ($key === '') {
        return (string)$default;
    }
    $bucket = &programmit_vpn_settings_cache_bucket();
    if ((int)$bucket['__loaded'] !== 1) {
        $db->sql_query(programmit_vpn_settings_table_sql($db));
        $qry = $db->sql_query("SELECT setting_key, setting_value FROM vpn_settings");
        while ($row = $db->sql_fetchrow($qry)) {
            if ($row && isset($row['setting_key'])) {
                $bucket['__data'][(string)$row['setting_key']] = isset($row['setting_value']) ? (string)$row['setting_value'] : '';
            }
        }
        $bucket['__loaded'] = 1;
    }
    if (isset($bucket['__data'][$key])) {
        return (string)$bucket['__data'][$key];
    }
    return (string)$default;
}

function programmit_vpn_set_setting($db, $key, $value) {
    $key = strtolower(trim((string)$key));
    $value = (string)$value;
    if ($key === '') {
        return false;
    }
    $db->sql_query(programmit_vpn_settings_table_sql($db));
    $idParts = programmit_vpn_insert_id_parts($db, 'vpn_settings', 'id');
    if (method_exists($db, 'is_pgsql') && $db->is_pgsql()) {
        $sql = "INSERT INTO vpn_settings
            (setting_key, setting_value, created_at, updated_at" . $idParts['columns'] . ")
            VALUES
            ('".$db->SanitizeForSQL($key)."',
             '".$db->SanitizeForSQL($value)."',
             NOW(),
             NOW()" . $idParts['values'] . ")
            ON CONFLICT (setting_key) DO UPDATE
            SET setting_value=EXCLUDED.setting_value,
                updated_at=NOW()";
    } else {
        $sql = "INSERT INTO vpn_settings
            (setting_key, setting_value, created_at, updated_at" . $idParts['columns'] . ")
            VALUES
            ('".$db->SanitizeForSQL($key)."',
             '".$db->SanitizeForSQL($value)."',
             NOW(),
             NOW()" . $idParts['values'] . ")
            ON DUPLICATE KEY UPDATE
                setting_value=VALUES(setting_value),
                updated_at=NOW()";
    }
    $ok = (bool)$db->sql_query($sql);
    if ($ok) {
        $bucket = &programmit_vpn_settings_cache_bucket();
        $bucket['__data'][$key] = $value;
        $bucket['__loaded'] = 1;
    }
    return $ok;
}

function programmit_vpn_generate_token($length = 40) {
    $length = (int)$length;
    if ($length < 24) {
        $length = 24;
    }
    if ($length > 128) {
        $length = 128;
    }
    try {
        return substr(bin2hex(random_bytes((int)ceil($length / 2))), 0, $length);
    } catch (Exception $e) {
        return substr(sha1(uniqid((string)mt_rand(), true) . microtime(true)), 0, $length);
    }
}

function programmit_vpn_hash_token($token) {
    $token = trim((string)$token);
    if ($token === '') {
        return '';
    }
    return hash('sha256', $token);
}

function programmit_vpn_can_manage($userId, $userLevel) {
    $userId = (int)$userId;
    $userLevel = strtolower(trim((string)$userLevel));
    return ($userId === 1 || in_array($userLevel, array('superadmin', 'administrator', 'subadmin'), true));
}

function programmit_vpn_seed_methods($db) {
    $defaults = array(
        array('method_key' => 'premium', 'method_name' => 'Premium', 'method_type' => 'premium', 'legacy_group' => 'premium', 'auth_mode' => 'local', 'is_public' => 1, 'sort_order' => 10, 'config_json' => array('source' => 'legacy')),
        array('method_key' => 'vip', 'method_name' => 'VIP', 'method_type' => 'vip', 'legacy_group' => 'vip', 'auth_mode' => 'local', 'is_public' => 1, 'sort_order' => 20, 'config_json' => array('source' => 'legacy')),
        array('method_key' => 'private', 'method_name' => 'Private', 'method_type' => 'private', 'legacy_group' => 'private', 'auth_mode' => 'local', 'is_public' => 1, 'sort_order' => 30, 'config_json' => array('source' => 'legacy')),
        array('method_key' => 'free', 'method_name' => 'Free', 'method_type' => 'free', 'legacy_group' => 'free', 'auth_mode' => 'local', 'is_public' => 1, 'sort_order' => 40, 'config_json' => array('source' => 'legacy'))
    );
    $existing = array();
    $qry = $db->sql_query("SELECT method_key FROM vpn_methods");
    while ($row = $db->sql_fetchrow($qry)) {
        if ($row && isset($row['method_key'])) {
            $existing[(string)$row['method_key']] = true;
        }
    }
    foreach ($defaults as $item) {
        if (isset($existing[$item['method_key']])) {
            continue;
        }
        $idParts = programmit_vpn_insert_id_parts($db, 'vpn_methods', 'id');
        $db->sql_query("INSERT INTO vpn_methods
            (method_key, method_name, method_type, legacy_group, auth_mode, is_active, is_public, sort_order, config_json, created_at, updated_at".$idParts['columns'].")
            VALUES
            ('".$db->SanitizeForSQL($item['method_key'])."',
             '".$db->SanitizeForSQL($item['method_name'])."',
             '".$db->SanitizeForSQL($item['method_type'])."',
             '".$db->SanitizeForSQL($item['legacy_group'])."',
             '".$db->SanitizeForSQL($item['auth_mode'])."',
             '1',
             '".$db->SanitizeForSQL($item['is_public'])."',
             '".$db->SanitizeForSQL($item['sort_order'])."',
             '".$db->SanitizeForSQL(programmit_vpn_json_encode($item['config_json']))."',
             NOW(),
             NOW()".$idParts['values'].")");
    }
}

function programmit_vpn_legacy_status_to_state($statusValue) {
    if ((int)$statusValue === 1 || (string)$statusValue === '1') {
        return 'active';
    }
    return 'maintenance';
}

function programmit_vpn_guess_server_key($serverName, $serverIp, $serverCategory) {
    $base = programmit_vpn_normalize_key($serverName);
    if ($base === '') {
        $base = programmit_vpn_normalize_key($serverCategory . '-' . str_replace('.', '-', (string)$serverIp));
    }
    if ($base === '') {
        $base = 'server-' . substr(sha1((string)$serverName . '|' . (string)$serverIp . '|' . (string)$serverCategory), 0, 12);
    }
    return $base;
}

function programmit_vpn_seed_servers_from_legacy($db) {
    if (!function_exists('table_exists_cached') || !table_exists_cached('server_list')) {
        return;
    }
    $qry = $db->sql_query("SELECT server_name, server_ip, server_port, server_category, status
        FROM server_list
        ORDER BY server_name ASC");
    while ($row = $db->sql_fetchrow($qry)) {
        if (!$row) {
            continue;
        }
        $serverName = trim((string)$row['server_name']);
        $serverIp = trim((string)$row['server_ip']);
        $serverPort = isset($row['server_port']) ? (int)$row['server_port'] : 443;
        $serverCategory = strtolower(trim((string)$row['server_category']));
        $serverKey = programmit_vpn_guess_server_key($serverName, $serverIp, $serverCategory);
        $existsQry = $db->sql_query("SELECT id FROM vpn_servers
            WHERE server_key='".$db->SanitizeForSQL($serverKey)."'
            LIMIT 1");
        if ($existsQry && $db->sql_numrows($existsQry) > 0) {
            continue;
        }
        if ($serverPort <= 0) {
            $serverPort = 443;
        }
        $idParts = programmit_vpn_insert_id_parts($db, 'vpn_servers', 'id');
        $db->sql_query("INSERT INTO vpn_servers
            (server_key, server_name, server_host, server_ip, server_port, server_provider, legacy_category,
             sync_enabled, is_public, status, public_payload_json, meta_json, created_at, updated_at".$idParts['columns'].")
            VALUES
            ('".$db->SanitizeForSQL($serverKey)."',
             '".$db->SanitizeForSQL($serverName !== '' ? $serverName : $serverKey)."',
             '".$db->SanitizeForSQL($serverIp)."',
             '".$db->SanitizeForSQL($serverIp)."',
             '".$db->SanitizeForSQL($serverPort)."',
             'legacy',
             '".$db->SanitizeForSQL($serverCategory)."',
             '0',
             '1',
             '".$db->SanitizeForSQL(programmit_vpn_legacy_status_to_state(isset($row['status']) ? $row['status'] : 0))."',
             '".$db->SanitizeForSQL(programmit_vpn_json_encode(array('legacy' => true)))."',
             '".$db->SanitizeForSQL(programmit_vpn_json_encode(array('seed_source' => 'server_list')))."',
             NOW(),
             NOW()".$idParts['values'].")");
    }
}

function programmit_vpn_method_id_map($db) {
    $map = array();
    $qry = $db->sql_query("SELECT id, method_key FROM vpn_methods");
    while ($row = $db->sql_fetchrow($qry)) {
        if ($row && isset($row['method_key'])) {
            $map[(string)$row['method_key']] = (int)$row['id'];
        }
    }
    return $map;
}

function programmit_vpn_seed_mappings_from_legacy($db) {
    $methodIds = programmit_vpn_method_id_map($db);
    if (empty($methodIds)) {
        return;
    }
    $qry = $db->sql_query("SELECT id, legacy_category, server_host, server_port
        FROM vpn_servers
        ORDER BY id ASC");
    while ($row = $db->sql_fetchrow($qry)) {
        if (!$row) {
            continue;
        }
        $legacyCategory = strtolower(trim((string)$row['legacy_category']));
        if ($legacyCategory === '' || !isset($methodIds[$legacyCategory])) {
            continue;
        }
        $methodId = (int)$methodIds[$legacyCategory];
        $serverId = (int)$row['id'];
        $existsQry = $db->sql_query("SELECT id
            FROM vpn_method_server_map
            WHERE method_id='".$db->SanitizeForSQL($methodId)."'
              AND server_id='".$db->SanitizeForSQL($serverId)."'
            LIMIT 1");
        if ($existsQry && $db->sql_numrows($existsQry) > 0) {
            continue;
        }
        $idParts = programmit_vpn_insert_id_parts($db, 'vpn_method_server_map', 'id');
        $db->sql_query("INSERT INTO vpn_method_server_map
            (method_id, server_id, endpoint_protocol, endpoint_host, endpoint_port, deploy_path, tls_sni, weight, is_active, is_default, config_json, created_at, updated_at".$idParts['columns'].")
            VALUES
            ('".$db->SanitizeForSQL($methodId)."',
             '".$db->SanitizeForSQL($serverId)."',
             'https',
             '".$db->SanitizeForSQL((string)$row['server_host'])."',
             '".$db->SanitizeForSQL((int)$row['server_port'] > 0 ? (int)$row['server_port'] : 443)."',
             '',
             '',
             '100',
             '1',
             '1',
             '".$db->SanitizeForSQL(programmit_vpn_json_encode(array('seed_source' => 'server_list')))."',
             NOW(),
             NOW()".$idParts['values'].")");
    }
}

function programmit_vpn_ensure_tables($db) {
    static $booted = false;
    if ($booted) {
        return true;
    }
    $booted = true;

    $ttlSeconds = 86400;
    $stampFile = programmit_vpn_schema_stamp_path($db);
    clearstatcache(true, $stampFile);
    if (is_file($stampFile) && (time() - (int)@filemtime($stampFile)) < $ttlSeconds) {
        return true;
    }

    programmit_vpn_upgrade_schema($db);

    programmit_vpn_seed_methods($db);
    programmit_vpn_seed_servers_from_legacy($db);
    programmit_vpn_seed_mappings_from_legacy($db);

    if (trim((string)programmit_vpn_get_setting($db, 'vpn_public_app_endpoint_key', '')) === '') {
        programmit_vpn_set_setting($db, 'vpn_public_app_endpoint_key', 'vpn-app-config');
    }
    if (trim((string)programmit_vpn_get_setting($db, 'vpn_reconcile_interval_seconds', '')) === '') {
        programmit_vpn_set_setting($db, 'vpn_reconcile_interval_seconds', '15');
    }

    $settingsProbe = $db->sql_query("SELECT setting_key FROM vpn_settings LIMIT 1");
    $serversProbe = $db->sql_query("SELECT id FROM vpn_servers LIMIT 1");
    $methodsProbe = $db->sql_query("SELECT id FROM vpn_methods LIMIT 1");
    if ($settingsProbe !== false && $serversProbe !== false && $methodsProbe !== false) {
        @touch($stampFile);
    }
    return true;
}

function programmit_vpn_list_servers($db) {
    programmit_vpn_ensure_tables($db);
    $rows = array();
    $qry = $db->sql_query("SELECT *
        FROM vpn_servers
        ORDER BY server_name ASC, id ASC");
    while ($row = $db->sql_fetchrow($qry)) {
        if (!$row) {
            continue;
        }
        if (!isset($row['server_provider']) && isset($row['provider'])) {
            $row['server_provider'] = $row['provider'];
        }
        if ((!isset($row['server_host']) || trim((string)$row['server_host']) === '') && isset($row['public_host'])) {
            $row['server_host'] = $row['public_host'];
        }
        if ((!isset($row['server_ip']) || trim((string)$row['server_ip']) === '') && isset($row['public_ip'])) {
            $row['server_ip'] = $row['public_ip'];
        }
        if (!isset($row['sync_enabled']) && isset($row['is_sync_enabled'])) {
            $row['sync_enabled'] = $row['is_sync_enabled'];
        }
        if (!isset($row['location_label']) && isset($row['region'])) {
            $row['location_label'] = $row['region'];
        }
        if (!isset($row['public_base_url']) && isset($row['api_base_url'])) {
            $row['public_base_url'] = $row['api_base_url'];
        }
        if (!isset($row['server_port'])) {
            $row['server_port'] = 443;
        }
        if (!isset($row['legacy_category'])) {
            $row['legacy_category'] = '';
        }
        if (!isset($row['sync_token_hash'])) {
            $row['sync_token_hash'] = '';
        }
        if (!isset($row['is_public'])) {
            $row['is_public'] = 1;
        }
        if (!isset($row['last_sync_cursor'])) {
            $row['last_sync_cursor'] = 0;
        }
        if (!isset($row['public_payload_json'])) {
            $row['public_payload_json'] = '{}';
        }
        if (!isset($row['meta_json'])) {
            $row['meta_json'] = '{}';
        }
        $row['public_payload'] = programmit_vpn_json_decode(isset($row['public_payload_json']) ? $row['public_payload_json'] : '');
        $row['meta'] = programmit_vpn_json_decode(isset($row['meta_json']) ? $row['meta_json'] : '');
        $row['runtime'] = (isset($row['meta']['agent_runtime']) && is_array($row['meta']['agent_runtime']))
            ? $row['meta']['agent_runtime']
            : array();
        $row['runtime_hostname'] = isset($row['runtime']['hostname']) ? (string)$row['runtime']['hostname'] : '';
        $row['runtime_fqdn'] = isset($row['runtime']['fqdn']) ? (string)$row['runtime']['fqdn'] : '';
        $row['runtime_request_ip'] = isset($row['runtime']['request_ip_panel_seen']) ? (string)$row['runtime']['request_ip_panel_seen'] : '';
        $row['runtime_public_ip'] = isset($row['runtime']['public_ip']) ? (string)$row['runtime']['public_ip'] : '';
        $row['runtime_agent_version'] = isset($row['runtime']['agent_version']) ? (string)$row['runtime']['agent_version'] : '';
        $row['runtime_platform'] = isset($row['runtime']['platform']) ? (string)$row['runtime']['platform'] : '';
        $row['runtime_local_ip'] = isset($row['runtime']['local_ip']) ? (string)$row['runtime']['local_ip'] : '';
        $row['runtime_collected_at_utc'] = isset($row['runtime']['collected_at_utc']) ? (string)$row['runtime']['collected_at_utc'] : '';
        $sshMd5 = array();
        if (isset($row['runtime']['ssh_hostkeys']) && is_array($row['runtime']['ssh_hostkeys'])) {
            foreach ((array)$row['runtime']['ssh_hostkeys'] as $algo => $fpRow) {
                if (!is_array($fpRow)) {
                    continue;
                }
                $md5 = isset($fpRow['md5']) ? trim((string)$fpRow['md5']) : '';
                if ($md5 !== '') {
                    $sshMd5[] = strtoupper((string)$algo) . ': ' . $md5;
                }
            }
        }
        $row['runtime_ssh_md5_summary'] = implode(' | ', $sshMd5);
        $row['identity_state'] = programmit_vpn_server_identity_summary($row);
        $row['sync_state'] = programmit_vpn_server_sync_summary($row);
        $rows[] = $row;
    }
    return $rows;
}

function programmit_vpn_list_methods($db) {
    programmit_vpn_ensure_tables($db);
    $rows = array();
    $qry = $db->sql_query("SELECT *
        FROM vpn_methods
        ORDER BY sort_order ASC, method_name ASC");
    while ($row = $db->sql_fetchrow($qry)) {
        if (!$row) {
            continue;
        }
        if (!isset($row['method_type']) && isset($row['category'])) {
            $row['method_type'] = $row['category'];
        }
        if (!isset($row['legacy_group']) && isset($row['category'])) {
            $row['legacy_group'] = $row['category'];
        }
        if (!isset($row['auth_mode']) && isset($row['auth_strategy'])) {
            $row['auth_mode'] = $row['auth_strategy'];
        }
        if (!isset($row['is_public'])) {
            $row['is_public'] = 1;
        }
        if (!isset($row['config_json']) && isset($row['ui_config_json'])) {
            $row['config_json'] = $row['ui_config_json'];
        }
        $row['config'] = programmit_vpn_json_decode(isset($row['config_json']) ? $row['config_json'] : '');
        $rows[] = $row;
    }
    return $rows;
}

function programmit_vpn_list_deployments($db) {
    programmit_vpn_ensure_tables($db);
    $rows = array();
    $qry = $db->sql_query("SELECT ms.id, ms.method_id, ms.server_id, ms.endpoint_protocol, ms.endpoint_host, ms.endpoint_port,
            ms.deploy_path, ms.tls_sni, ms.weight, ms.is_active, ms.is_default, ms.config_json,
            ms.created_at, ms.updated_at,
            m.method_key, m.method_name, m.method_type, m.legacy_group,
            s.server_key, s.server_name, s.server_host, s.server_ip, s.status AS server_status
        FROM vpn_method_server_map ms
        INNER JOIN vpn_methods m ON m.id=ms.method_id
        INNER JOIN vpn_servers s ON s.id=ms.server_id
        ORDER BY m.sort_order ASC, m.method_name ASC, s.server_name ASC");
    while ($row = $db->sql_fetchrow($qry)) {
        if (!$row) {
            continue;
        }
        $row['config'] = programmit_vpn_json_decode(isset($row['config_json']) ? $row['config_json'] : '');
        $rows[] = $row;
    }
    return $rows;
}

function programmit_vpn_server_find_by_key($db, $serverKey) {
    $serverKey = programmit_vpn_normalize_key($serverKey);
    if ($serverKey === '') {
        return null;
    }
    $qry = $db->sql_query("SELECT *
        FROM vpn_servers
        WHERE server_key='".$db->SanitizeForSQL($serverKey)."'
        LIMIT 1");
    $row = $db->sql_fetchrow($qry);
    return $row ? $row : null;
}

function programmit_vpn_server_authenticate($db, $serverKey, $token) {
    $server = programmit_vpn_server_find_by_key($db, $serverKey);
    if (!$server) {
        return null;
    }
    $syncEnabled = isset($server['sync_enabled']) ? (int)$server['sync_enabled'] : (isset($server['is_sync_enabled']) ? (int)$server['is_sync_enabled'] : 0);
    if ($syncEnabled !== 1 || trim((string)(isset($server['sync_token_hash']) ? $server['sync_token_hash'] : '')) === '') {
        return null;
    }
    $expected = trim((string)$server['sync_token_hash']);
    $given = programmit_vpn_hash_token($token);
    if ($expected === '' || $given === '') {
        return null;
    }
    if (!hash_equals($expected, $given)) {
        return null;
    }
    return $server;
}

function programmit_vpn_assignment_map($db) {
    $map = array();
    $qry = $db->sql_query("SELECT user_id, method_key
        FROM vpn_user_method_assignments
        WHERE desired_state='active'");
    while ($row = $db->sql_fetchrow($qry)) {
        if (!$row) {
            continue;
        }
        $userId = isset($row['user_id']) ? (int)$row['user_id'] : 0;
        $methodKey = programmit_vpn_normalize_key(isset($row['method_key']) ? $row['method_key'] : '');
        if ($userId <= 0 || $methodKey === '') {
            continue;
        }
        if (!isset($map[$userId])) {
            $map[$userId] = array();
        }
        $map[$userId][$methodKey] = $methodKey;
    }
    return $map;
}

function programmit_vpn_resolve_user_methods($db, $userRow, $assignmentMap = array()) {
    $userId = isset($userRow['user_id']) ? (int)$userRow['user_id'] : 0;
    $methods = array();
    if ($userId > 0 && isset($assignmentMap[$userId]) && is_array($assignmentMap[$userId])) {
        foreach ($assignmentMap[$userId] as $methodKey) {
            $methodKey = programmit_vpn_normalize_key($methodKey);
            if ($methodKey !== '') {
                $methods[$methodKey] = $methodKey;
            }
        }
    }

    $duration = isset($userRow['duration']) ? (int)$userRow['duration'] : 0;
    $vipDuration = isset($userRow['vip_duration']) ? (int)$userRow['vip_duration'] : 0;
    $privateDuration = isset($userRow['private_duration']) ? (int)$userRow['private_duration'] : 0;
    $isVip = isset($userRow['is_vip']) ? (int)$userRow['is_vip'] : 0;
    $isPrivate = isset($userRow['is_private']) ? (int)$userRow['is_private'] : 0;

    if ($duration > 0) {
        $methods['premium'] = 'premium';
    }
    if ($vipDuration > 0 || $isVip === 1) {
        $methods['vip'] = 'vip';
    }
    if ($privateDuration > 0 || $isPrivate === 1) {
        $methods['private'] = 'private';
    }
    if (empty($methods)) {
        $methods['free'] = 'free';
    }

    $out = array_values($methods);
    sort($out);
    return $out;
}

function programmit_vpn_user_state_label($userRow) {
    $status = strtolower(trim((string)(isset($userRow['status']) ? $userRow['status'] : 'live')));
    $isActive = isset($userRow['is_active']) ? (int)$userRow['is_active'] : 0;
    $isFreeze = isset($userRow['is_freeze']) ? (int)$userRow['is_freeze'] : 0;
    $isBan = isset($userRow['is_ban']) ? (int)$userRow['is_ban'] : 0;
    if ($isBan === 1 || $status === 'banned') {
        return 'banned';
    }
    if ($isFreeze === 1 || $status === 'freeze') {
        return 'freeze';
    }
    if ($status === 'suspended' || $isActive !== 1) {
        return 'suspended';
    }
    return 'active';
}

function programmit_vpn_build_user_payload($db, $userRow, $assignmentMap = array()) {
    $methods = programmit_vpn_resolve_user_methods($db, $userRow, $assignmentMap);
    $payload = array(
        'user_id' => isset($userRow['user_id']) ? (int)$userRow['user_id'] : 0,
        'user_name' => isset($userRow['user_name']) ? (string)$userRow['user_name'] : '',
        'user_pass' => isset($userRow['user_pass']) ? (string)$userRow['user_pass'] : '',
        'pass_plain' => isset($userRow['pass_plain']) ? (string)$userRow['pass_plain'] : '',
        'auth_vpn' => isset($userRow['auth_vpn']) ? (string)$userRow['auth_vpn'] : '',
        'user_email' => isset($userRow['user_email']) ? (string)$userRow['user_email'] : '',
        'full_name' => isset($userRow['full_name']) ? (string)$userRow['full_name'] : '',
        'code' => isset($userRow['code']) ? (string)$userRow['code'] : '',
        'ss_id' => isset($userRow['ss_id']) ? (string)$userRow['ss_id'] : '',
        'state' => programmit_vpn_user_state_label($userRow),
        'status' => isset($userRow['status']) ? (string)$userRow['status'] : '',
        'is_active' => isset($userRow['is_active']) ? (int)$userRow['is_active'] : 0,
        'is_freeze' => isset($userRow['is_freeze']) ? (int)$userRow['is_freeze'] : 0,
        'is_ban' => isset($userRow['is_ban']) ? (int)$userRow['is_ban'] : 0,
        'duration' => isset($userRow['duration']) ? (int)$userRow['duration'] : 0,
        'vip_duration' => isset($userRow['vip_duration']) ? (int)$userRow['vip_duration'] : 0,
        'private_duration' => isset($userRow['private_duration']) ? (int)$userRow['private_duration'] : 0,
        'user_level' => isset($userRow['user_level']) ? (string)$userRow['user_level'] : '',
        'methods' => $methods
    );
    if (function_exists('programmit_client_default_password_sync_meta')) {
        $payload = array_merge($payload, programmit_client_default_password_sync_meta($db, $userRow));
    }
    return $payload;
}

function programmit_vpn_payload_hash_payload($payload) {
    if (!is_array($payload)) {
        return array();
    }
    $hashPayload = $payload;
    $hashPayload['duration_active'] = isset($payload['duration']) && (int)$payload['duration'] > 0 ? 1 : 0;
    $hashPayload['vip_duration_active'] = isset($payload['vip_duration']) && (int)$payload['vip_duration'] > 0 ? 1 : 0;
    $hashPayload['private_duration_active'] = isset($payload['private_duration']) && (int)$payload['private_duration'] > 0 ? 1 : 0;
    unset($hashPayload['duration'], $hashPayload['vip_duration'], $hashPayload['private_duration']);
    return $hashPayload;
}

function programmit_vpn_payload_hash($payload) {
    return sha1(programmit_vpn_json_encode(programmit_vpn_payload_hash_payload($payload)));
}

function programmit_vpn_create_event($db, $eventType, $userId, $userName, $methods, $previousMethods, $payload, $payloadHash) {
    if (!is_array($methods)) {
        $methods = array();
    }
    if (!is_array($previousMethods)) {
        $previousMethods = array();
    }
    if (!is_array($payload)) {
        $payload = array();
    }
    $idParts = programmit_vpn_insert_id_parts($db, 'vpn_sync_events', 'id');
    $db->sql_query("INSERT INTO vpn_sync_events
        (event_type, user_id, user_name, methods_json, previous_methods_json, payload_json, payload_hash, created_at".$idParts['columns'].")
        VALUES
        ('".$db->SanitizeForSQL((string)$eventType)."',
         '".$db->SanitizeForSQL((int)$userId)."',
         '".$db->SanitizeForSQL((string)$userName)."',
         '".$db->SanitizeForSQL(programmit_vpn_json_encode(array_values($methods)))."',
         '".$db->SanitizeForSQL(programmit_vpn_json_encode(array_values($previousMethods)))."',
         '".$db->SanitizeForSQL(programmit_vpn_json_encode($payload))."',
         '".$db->SanitizeForSQL((string)$payloadHash)."',
         NOW()".$idParts['values'].")");
}

function programmit_vpn_create_targeted_event($db, $eventType, $targetServerIds, $payload, $methods = array(), $previousMethods = array()) {
    if (!is_array($payload)) {
        $payload = array();
    }
    $serverIds = array();
    foreach ((array)$targetServerIds as $serverId) {
        $serverId = (int)$serverId;
        if ($serverId > 0) {
            $serverIds[$serverId] = $serverId;
        }
    }
    if (empty($serverIds)) {
        return false;
    }
    $payload['target_server_ids'] = array_values($serverIds);
    $hashBase = (string)$eventType . '|' . programmit_vpn_json_encode($payload) . '|' . programmit_vpn_json_encode(array_values((array)$methods)) . '|' . programmit_vpn_json_encode(array_values((array)$previousMethods));
    programmit_vpn_create_event($db, $eventType, 0, '', $methods, $previousMethods, $payload, sha1($hashBase));
    return true;
}

function programmit_vpn_snapshot_rows($db) {
    $rows = array();
    $qry = $db->sql_query("SELECT user_id, snapshot_hash, methods_json, payload_json
        FROM vpn_user_snapshots");
    while ($row = $db->sql_fetchrow($qry)) {
        if ($row) {
            $rows[(int)$row['user_id']] = $row;
        }
    }
    return $rows;
}

function programmit_vpn_reconcile_users($db, $force = false) {
    programmit_vpn_ensure_tables($db);
    $interval = (int)programmit_vpn_get_setting($db, 'vpn_reconcile_interval_seconds', '15');
    if ($interval < 5) {
        $interval = 5;
    }
    $lastRun = (int)programmit_vpn_get_setting($db, 'vpn_reconcile_last_run_ts', '0');
    if (!$force && $lastRun > 0 && (time() - $lastRun) < $interval) {
        return array('created' => 0, 'updated' => 0, 'deleted' => 0, 'skipped' => 1);
    }

    $snapshotRows = programmit_vpn_snapshot_rows($db);
    $assignmentMap = programmit_vpn_assignment_map($db);
    $seen = array();
    $created = 0;
    $updated = 0;
    $deleted = 0;

    $qry = $db->sql_query("SELECT user_id, user_name, user_pass, pass_plain, auth_vpn, user_email, full_name,
            code, ss_id, status, is_active, is_freeze, is_ban, duration, vip_duration, private_duration,
            user_level, is_vip, is_private
        FROM users
        ORDER BY user_id ASC");
    while ($row = $db->sql_fetchrow($qry)) {
        if (!$row) {
            continue;
        }
        $userId = isset($row['user_id']) ? (int)$row['user_id'] : 0;
        if ($userId <= 0) {
            continue;
        }
        $seen[$userId] = true;
        $payload = programmit_vpn_build_user_payload($db, $row, $assignmentMap);
        $payloadJson = programmit_vpn_json_encode($payload);
        $payloadHash = programmit_vpn_payload_hash($payload);
        $methods = isset($payload['methods']) && is_array($payload['methods']) ? $payload['methods'] : array();

        $previousHash = '';
        $previousMethods = array();
        if (isset($snapshotRows[$userId])) {
            $previousHash = isset($snapshotRows[$userId]['snapshot_hash']) ? (string)$snapshotRows[$userId]['snapshot_hash'] : '';
            $previousMethods = programmit_vpn_json_decode(isset($snapshotRows[$userId]['methods_json']) ? $snapshotRows[$userId]['methods_json'] : '');
        }

        if (!isset($snapshotRows[$userId])) {
            programmit_vpn_create_event($db, 'upsert', $userId, (string)$payload['user_name'], $methods, array(), $payload, $payloadHash);
            $created++;
        } elseif ($previousHash !== $payloadHash) {
            programmit_vpn_create_event($db, 'upsert', $userId, (string)$payload['user_name'], $methods, $previousMethods, $payload, $payloadHash);
            $updated++;
        }

        if (method_exists($db, 'is_pgsql') && $db->is_pgsql()) {
            $db->sql_query("INSERT INTO vpn_user_snapshots
                (user_id, snapshot_hash, methods_json, payload_json, updated_at)
                VALUES
                ('".$db->SanitizeForSQL($userId)."',
                 '".$db->SanitizeForSQL($payloadHash)."',
                 '".$db->SanitizeForSQL(programmit_vpn_json_encode($methods))."',
                 '".$db->SanitizeForSQL($payloadJson)."',
                 NOW())
                ON CONFLICT (user_id) DO UPDATE
                SET snapshot_hash=EXCLUDED.snapshot_hash,
                    methods_json=EXCLUDED.methods_json,
                    payload_json=EXCLUDED.payload_json,
                    updated_at=NOW()");
        } else {
            $db->sql_query("INSERT INTO vpn_user_snapshots
                (user_id, snapshot_hash, methods_json, payload_json, updated_at)
                VALUES
                ('".$db->SanitizeForSQL($userId)."',
                 '".$db->SanitizeForSQL($payloadHash)."',
                 '".$db->SanitizeForSQL(programmit_vpn_json_encode($methods))."',
                 '".$db->SanitizeForSQL($payloadJson)."',
                 NOW())
                ON DUPLICATE KEY UPDATE
                    snapshot_hash=VALUES(snapshot_hash),
                    methods_json=VALUES(methods_json),
                    payload_json=VALUES(payload_json),
                    updated_at=NOW()");
        }
    }

    foreach ($snapshotRows as $userId => $snapshotRow) {
        if (isset($seen[(int)$userId])) {
            continue;
        }
        $previousMethods = programmit_vpn_json_decode(isset($snapshotRow['methods_json']) ? $snapshotRow['methods_json'] : '');
        $previousPayload = programmit_vpn_json_decode(isset($snapshotRow['payload_json']) ? $snapshotRow['payload_json'] : '');
        $userName = isset($previousPayload['user_name']) ? (string)$previousPayload['user_name'] : '';
        programmit_vpn_create_event($db, 'delete', (int)$userId, $userName, array(), $previousMethods, array(
            'user_id' => (int)$userId,
            'user_name' => $userName,
            'deleted' => 1
        ), sha1('delete|' . (int)$userId . '|' . $userName));
        $db->sql_query("DELETE FROM vpn_user_snapshots
            WHERE user_id='".$db->SanitizeForSQL((int)$userId)."'
            LIMIT 1");
        $deleted++;
    }

    programmit_vpn_set_setting($db, 'vpn_reconcile_last_run_ts', (string)time());
    programmit_vpn_set_setting($db, 'vpn_reconcile_last_summary', programmit_vpn_json_encode(array(
        'created' => $created,
        'updated' => $updated,
        'deleted' => $deleted,
        'ran_at' => date('Y-m-d H:i:s')
    )));

    return array(
        'created' => $created,
        'updated' => $updated,
        'deleted' => $deleted,
        'skipped' => 0
    );
}

function programmit_vpn_server_method_keys($db, $serverId) {
    $serverId = (int)$serverId;
    $keys = array();
    if ($serverId <= 0) {
        return $keys;
    }
    $qry = $db->sql_query("SELECT m.method_key
        FROM vpn_method_server_map ms
        INNER JOIN vpn_methods m ON m.id=ms.method_id
        WHERE ms.server_id='".$db->SanitizeForSQL($serverId)."'
          AND ms.is_active='1'
          AND m.is_active='1'");
    while ($row = $db->sql_fetchrow($qry)) {
        if ($row && isset($row['method_key'])) {
            $keys[(string)$row['method_key']] = (string)$row['method_key'];
        }
    }
    return array_values($keys);
}

function programmit_vpn_methods_intersect($a, $b) {
    if (!is_array($a) || !is_array($b) || empty($a) || empty($b)) {
        return false;
    }
    $index = array();
    foreach ($b as $item) {
        $index[(string)$item] = true;
    }
    foreach ($a as $item) {
        if (isset($index[(string)$item])) {
            return true;
        }
    }
    return false;
}

function programmit_vpn_payload_targets_server($payload, $serverId, $serverKey = '') {
    $serverId = (int)$serverId;
    $serverKey = programmit_vpn_normalize_key($serverKey);
    if (!is_array($payload)) {
        return false;
    }

    if (isset($payload['target_server_ids']) && is_array($payload['target_server_ids'])) {
        foreach ($payload['target_server_ids'] as $targetId) {
            if ((int)$targetId === $serverId && $serverId > 0) {
                return true;
            }
        }
    }

    if ($serverKey !== '' && isset($payload['target_server_keys']) && is_array($payload['target_server_keys'])) {
        foreach ($payload['target_server_keys'] as $targetKey) {
            if (programmit_vpn_normalize_key($targetKey) === $serverKey) {
                return true;
            }
        }
    }

    return false;
}

function programmit_vpn_server_events_since($db, $serverId, $cursor, $limit = 100) {
    $serverId = (int)$serverId;
    $cursor = (int)$cursor;
    $limit = (int)$limit;
    if ($limit <= 0) {
        $limit = 100;
    }
    if ($limit > 500) {
        $limit = 500;
    }
    $methodKeys = programmit_vpn_server_method_keys($db, $serverId);
    $serverRow = null;
    if ($serverId > 0) {
        $serverQry = $db->sql_query("SELECT server_key
            FROM vpn_servers
            WHERE id='".$db->SanitizeForSQL($serverId)."'
            LIMIT 1");
        $serverRow = $db->sql_fetchrow($serverQry);
    }
    $serverKey = ($serverRow && isset($serverRow['server_key'])) ? (string)$serverRow['server_key'] : '';
    $events = array();
    $scanCursor = $cursor;
    $queryCursor = $cursor;
    $keepGoing = true;
    while ($keepGoing && count($events) < $limit) {
        $batchQry = $db->sql_query("SELECT id, event_type, user_id, user_name, methods_json, previous_methods_json, payload_json, created_at
            FROM vpn_sync_events
            WHERE id>'".$db->SanitizeForSQL($queryCursor)."'
            ORDER BY id ASC
            LIMIT 250");
        $batchCount = 0;
        while ($row = $db->sql_fetchrow($batchQry)) {
            $batchCount++;
            $queryCursor = (int)$row['id'];
            $methods = programmit_vpn_json_decode(isset($row['methods_json']) ? $row['methods_json'] : '');
            $previousMethods = programmit_vpn_json_decode(isset($row['previous_methods_json']) ? $row['previous_methods_json'] : '');
            $payload = programmit_vpn_json_decode(isset($row['payload_json']) ? $row['payload_json'] : '');
            $forcedTarget = programmit_vpn_payload_targets_server($payload, $serverId, $serverKey);
            $relevant = false;
            if ($forcedTarget) {
                $relevant = true;
            } elseif (!empty($methodKeys) && (programmit_vpn_methods_intersect($methods, $methodKeys) || programmit_vpn_methods_intersect($previousMethods, $methodKeys))) {
                $relevant = true;
            }
            if (!empty($methodKeys) || $forcedTarget) {
                $scanCursor = (int)$row['id'];
            }
            if (!$relevant) {
                continue;
            }
            $events[] = array(
                'id' => (int)$row['id'],
                'event_type' => (string)$row['event_type'],
                'user_id' => (int)$row['user_id'],
                'user_name' => (string)$row['user_name'],
                'methods' => $methods,
                'previous_methods' => $previousMethods,
                'payload' => $payload,
                'created_at' => (string)$row['created_at']
            );
            if (count($events) >= $limit) {
                break;
            }
        }
        if ($batchCount < 250) {
            $keepGoing = false;
        }
    }
    return array(
        'method_keys' => $methodKeys,
        'events' => $events,
        'next_cursor' => $scanCursor
    );
}

function programmit_vpn_mark_server_sync($db, $serverId, $cursor, $ack = false) {
    $serverId = (int)$serverId;
    $cursor = (int)$cursor;
    if ($serverId <= 0) {
        return false;
    }
    $extra = $ack ? ", last_ack_at=NOW()" : ", last_sync_at=NOW()";
    return (bool)$db->sql_query("UPDATE vpn_servers
        SET last_sync_cursor='".$db->SanitizeForSQL($cursor)."',
            last_seen_at=NOW()
            ".$extra.",
            updated_at=NOW()
        WHERE id='".$db->SanitizeForSQL($serverId)."'
        LIMIT 1");
}

function programmit_vpn_touch_server($db, $serverId, $lastCursor = null) {
    $serverId = (int)$serverId;
    if ($serverId <= 0) {
        return false;
    }

    $setCursorSql = '';
    if ($lastCursor !== null) {
        $setCursorSql = ", last_sync_cursor='" . $db->SanitizeForSQL((int)$lastCursor) . "'";
    }

    return (bool)$db->sql_query("UPDATE vpn_servers
        SET last_seen_at=NOW(),
            last_sync_at=NOW()
            " . $setCursorSql . ",
            updated_at=NOW()
        WHERE id='" . $db->SanitizeForSQL($serverId) . "'
        LIMIT 1");
}

function programmit_vpn_sync_log($db, $serverId, $actionName, $status, $cursorFrom, $cursorTo, $eventsCount, $requestIp, $details) {
    if (!is_array($details)) {
        $details = array();
    }
    $idParts = programmit_vpn_insert_id_parts($db, 'vpn_sync_logs', 'id');
    $db->sql_query("INSERT INTO vpn_sync_logs
        (server_id, action_name, status, cursor_from, cursor_to, events_count, request_ip, details_json, created_at".$idParts['columns'].")
        VALUES
        ('".$db->SanitizeForSQL((int)$serverId)."',
         '".$db->SanitizeForSQL((string)$actionName)."',
         '".$db->SanitizeForSQL((string)$status)."',
         '".$db->SanitizeForSQL((int)$cursorFrom)."',
         '".$db->SanitizeForSQL((int)$cursorTo)."',
         '".$db->SanitizeForSQL((int)$eventsCount)."',
         '".$db->SanitizeForSQL((string)$requestIp)."',
         '".$db->SanitizeForSQL(programmit_vpn_json_encode($details))."',
         NOW()".$idParts['values'].")");
}

function programmit_vpn_list_sync_logs($db, $limit = 80) {
    $limit = (int)$limit;
    if ($limit <= 0) {
        $limit = 80;
    }
    if ($limit > 300) {
        $limit = 300;
    }
    $rows = array();
    $qry = $db->sql_query("SELECT l.*, s.server_name, s.server_key
        FROM vpn_sync_logs l
        LEFT JOIN vpn_servers s ON s.id=l.server_id
        ORDER BY l.id DESC
        LIMIT ".$limit);
    while ($row = $db->sql_fetchrow($qry)) {
        if ($row) {
            $row['details'] = programmit_vpn_json_decode(isset($row['details_json']) ? $row['details_json'] : '');
            $rows[] = $row;
        }
    }
    return $rows;
}

function programmit_vpn_public_catalog($db) {
    programmit_vpn_ensure_tables($db);
    $methods = array();
    $qry = $db->sql_query("SELECT m.method_key, m.method_name, m.method_type, m.legacy_group, m.sort_order,
            m.config_json,
            s.server_key, s.server_name, s.server_host, s.server_ip, s.server_port, s.country_code, s.location_label,
            s.public_base_url, s.public_payload_json,
            ms.endpoint_protocol, ms.endpoint_host, ms.endpoint_port, ms.deploy_path, ms.tls_sni, ms.weight, ms.config_json AS map_config_json
        FROM vpn_method_server_map ms
        INNER JOIN vpn_methods m ON m.id=ms.method_id
        INNER JOIN vpn_servers s ON s.id=ms.server_id
        WHERE ms.is_active='1'
          AND m.is_active='1'
          AND m.is_public='1'
          AND s.is_public='1'
          AND s.status='active'
        ORDER BY m.sort_order ASC, m.method_name ASC, ms.weight DESC, s.server_name ASC");
    while ($row = $db->sql_fetchrow($qry)) {
        if (!$row) {
            continue;
        }
        $methodKey = (string)$row['method_key'];
        if (!isset($methods[$methodKey])) {
            $methods[$methodKey] = array(
                'method_key' => $methodKey,
                'method_name' => (string)$row['method_name'],
                'method_type' => (string)$row['method_type'],
                'legacy_group' => (string)$row['legacy_group'],
                'config' => programmit_vpn_json_decode(isset($row['config_json']) ? $row['config_json'] : ''),
                'servers' => array()
            );
        }
        $publicPayload = programmit_vpn_json_decode(isset($row['public_payload_json']) ? $row['public_payload_json'] : '');
        $mapConfig = programmit_vpn_json_decode(isset($row['map_config_json']) ? $row['map_config_json'] : '');
        $endpointHost = trim((string)$row['endpoint_host']);
        if ($endpointHost === '') {
            $endpointHost = trim((string)$row['server_host']);
        }
        $methods[$methodKey]['servers'][] = array(
            'server_key' => (string)$row['server_key'],
            'server_name' => (string)$row['server_name'],
            'host' => $endpointHost,
            'ip' => (string)$row['server_ip'],
            'port' => (int)$row['endpoint_port'] > 0 ? (int)$row['endpoint_port'] : (int)$row['server_port'],
            'protocol' => (string)$row['endpoint_protocol'],
            'path' => (string)$row['deploy_path'],
            'tls_sni' => (string)$row['tls_sni'],
            'weight' => (int)$row['weight'],
            'country_code' => (string)$row['country_code'],
            'location_label' => (string)$row['location_label'],
            'public_base_url' => (string)$row['public_base_url'],
            'public_payload' => $publicPayload,
            'mapping' => $mapConfig
        );
    }

    return array(
        'generated_at' => date('Y-m-d H:i:s'),
        'host' => isset($_SERVER['HTTP_HOST']) ? (string)$_SERVER['HTTP_HOST'] : '',
        'methods' => array_values($methods)
    );
}
