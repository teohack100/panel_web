<?php
if (preg_match("/saas.php/i", $_SERVER['SCRIPT_NAME'])) {
    Header("Location: /");
    die();
}

function programmit_saas_normalize_host($host) {
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
    $host = strtolower(trim((string)$host));
    return $host;
}

function programmit_saas_current_host() {
    $raw = isset($_SERVER['HTTP_HOST']) ? (string)$_SERVER['HTTP_HOST'] : '';
    return programmit_saas_normalize_host($raw);
}

function programmit_saas_is_local_host($host) {
    $host = programmit_saas_normalize_host($host);
    return ($host === 'localhost' || $host === '127.0.0.1' || $host === '::1');
}

function programmit_saas_valid_key($key) {
    $key = strtolower(trim((string)$key));
    if ($key === '') {
        return false;
    }
    return (bool)preg_match('/^[a-z0-9][a-z0-9_-]{2,63}$/', $key);
}

function programmit_saas_valid_hostname($host) {
    $host = programmit_saas_normalize_host($host);
    if ($host === '' || strlen($host) > 191) {
        return false;
    }
    if (programmit_saas_is_local_host($host)) {
        return true;
    }
    if (!preg_match('/^[a-z0-9.-]+$/', $host)) {
        return false;
    }
    if (strpos($host, '..') !== false || $host[0] === '.' || substr($host, -1) === '.') {
        return false;
    }
    return (bool)preg_match('/^[a-z0-9-]+(\.[a-z0-9-]+)+$/', $host);
}

function programmit_saas_status_normalize($status) {
    $status = strtolower(trim((string)$status));
    $allowed = array('trial', 'active', 'suspended', 'cancelled');
    if (!in_array($status, $allowed, true)) {
        return 'trial';
    }
    return $status;
}

function programmit_saas_schema_stamp_path() {
    $tmpDir = rtrim((string)sys_get_temp_dir(), '/\\');
    if ($tmpDir === '') {
        $tmpDir = '/tmp';
    }
    $stampDir = $tmpDir . DIRECTORY_SEPARATOR . 'programmit_bootstrap';
    if (!is_dir($stampDir)) {
        @mkdir($stampDir, 0775, true);
    }
    return $stampDir . DIRECTORY_SEPARATOR . 'saas_schema.stamp';
}

function programmit_saas_tenant_ctx_cache_path($host) {
    $host = programmit_saas_normalize_host($host);
    if ($host === '') {
        return '';
    }
    $tmpDir = rtrim((string)sys_get_temp_dir(), '/\\');
    if ($tmpDir === '') {
        $tmpDir = '/tmp';
    }
    $cacheDir = $tmpDir . DIRECTORY_SEPARATOR . 'programmit_bootstrap';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0775, true);
    }
    return $cacheDir . DIRECTORY_SEPARATOR . 'saas_ctx_' . md5($host) . '.json';
}

function programmit_saas_tenant_ctx_cache_read($host, $ttlSeconds = 300) {
    $cacheFile = programmit_saas_tenant_ctx_cache_path($host);
    if ($cacheFile === '') {
        return null;
    }
    clearstatcache(true, $cacheFile);
    if (!is_file($cacheFile)) {
        return null;
    }
    $age = time() - (int)@filemtime($cacheFile);
    if ($age < 0 || $age > (int)$ttlSeconds) {
        return null;
    }
    $raw = @file_get_contents($cacheFile);
    if (!is_string($raw) || $raw === '') {
        return null;
    }
    $ctx = json_decode($raw, true);
    if (!is_array($ctx)) {
        return null;
    }
    return $ctx;
}

function programmit_saas_tenant_ctx_cache_write($host, $ctx) {
    if (!is_array($ctx)) {
        return false;
    }
    $cacheFile = programmit_saas_tenant_ctx_cache_path($host);
    if ($cacheFile === '') {
        return false;
    }
    $json = json_encode($ctx, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!is_string($json) || $json === '') {
        return false;
    }
    return (bool)@file_put_contents($cacheFile, $json, LOCK_EX);
}

function programmit_saas_ensure_settings_table_once($db) {
    static $ready = false;
    if ($ready) {
        return true;
    }
    $db->sql_query(programmit_saas_settings_table_sql());
    $ready = true;
    return true;
}

function &programmit_saas_settings_cache_bucket() {
    if (!isset($GLOBALS['_programmit_saas_settings_cache']) || !is_array($GLOBALS['_programmit_saas_settings_cache'])) {
        $GLOBALS['_programmit_saas_settings_cache'] = array(
            '__loaded' => 0,
            '__data' => array()
        );
    }
    return $GLOBALS['_programmit_saas_settings_cache'];
}

function programmit_saas_is_platform_admin($userId, $userLevel) {
    if ((int)$userId === 1) {
        return true;
    }
    return (strtolower(trim((string)$userLevel)) === 'superadmin');
}

function programmit_saas_plans_table_sql() {
    return "CREATE TABLE IF NOT EXISTS saas_plans (
        id INT(11) NOT NULL AUTO_INCREMENT,
        plan_code VARCHAR(64) NOT NULL,
        plan_name VARCHAR(120) NOT NULL,
        description VARCHAR(255) NOT NULL DEFAULT '',
        monthly_price_usd DECIMAL(12,2) NOT NULL DEFAULT 0.00,
        setup_fee_usd DECIMAL(12,2) NOT NULL DEFAULT 0.00,
        credit_price_usd DECIMAL(12,4) NOT NULL DEFAULT 1.0000,
        included_credits INT(11) NOT NULL DEFAULT 0,
        panel_limit INT(11) NOT NULL DEFAULT 1,
        user_limit INT(11) NOT NULL DEFAULT 1,
        method_limit INT(11) NOT NULL DEFAULT 3,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        is_public TINYINT(1) NOT NULL DEFAULT 1,
        created_at DATETIME NOT NULL,
        updated_at DATETIME DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY uniq_plan_code (plan_code),
        KEY idx_plan_active (is_active, is_public)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

function programmit_saas_tenants_table_sql() {
    return "CREATE TABLE IF NOT EXISTS saas_tenants (
        id INT(11) NOT NULL AUTO_INCREMENT,
        tenant_key VARCHAR(64) NOT NULL,
        owner_user_id INT(11) NOT NULL DEFAULT 1,
        plan_id INT(11) NOT NULL DEFAULT 0,
        display_name VARCHAR(140) NOT NULL,
        brand_name VARCHAR(140) NOT NULL DEFAULT '',
        support_email VARCHAR(191) NOT NULL DEFAULT '',
        status VARCHAR(24) NOT NULL DEFAULT 'trial',
        credits_balance INT(11) NOT NULL DEFAULT 0,
        monthly_price_usd DECIMAL(12,2) NOT NULL DEFAULT 0.00,
        credit_price_usd DECIMAL(12,4) NOT NULL DEFAULT 0.0000,
        default_currency VARCHAR(8) NOT NULL DEFAULT 'USD',
        timezone VARCHAR(64) NOT NULL DEFAULT 'UTC',
        notes MEDIUMTEXT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME DEFAULT NULL,
        last_seen_at DATETIME DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY uniq_tenant_key (tenant_key),
        KEY idx_tenant_owner (owner_user_id),
        KEY idx_tenant_plan (plan_id),
        KEY idx_tenant_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

function programmit_saas_domains_table_sql() {
    return "CREATE TABLE IF NOT EXISTS saas_tenant_domains (
        id INT(11) NOT NULL AUTO_INCREMENT,
        tenant_id INT(11) NOT NULL,
        hostname VARCHAR(191) NOT NULL,
        is_primary TINYINT(1) NOT NULL DEFAULT 0,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        verified_at DATETIME DEFAULT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY uniq_hostname (hostname),
        KEY idx_domain_tenant (tenant_id, is_active, is_primary)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

function programmit_saas_branding_table_sql() {
    return "CREATE TABLE IF NOT EXISTS saas_tenant_branding (
        tenant_id INT(11) NOT NULL,
        logo_url VARCHAR(255) NOT NULL DEFAULT '',
        favicon_url VARCHAR(255) NOT NULL DEFAULT '',
        primary_color VARCHAR(16) NOT NULL DEFAULT '#2fbde5',
        accent_color VARCHAR(16) NOT NULL DEFAULT '#95f100',
        background_color VARCHAR(16) NOT NULL DEFAULT '#132744',
        custom_css MEDIUMTEXT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME DEFAULT NULL,
        PRIMARY KEY (tenant_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

function programmit_saas_settings_table_sql() {
    return "CREATE TABLE IF NOT EXISTS saas_settings (
        id INT(11) NOT NULL AUTO_INCREMENT,
        setting_key VARCHAR(64) NOT NULL,
        setting_value MEDIUMTEXT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY uniq_saas_setting_key (setting_key)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

function programmit_saas_sync_logs_table_sql() {
    return "CREATE TABLE IF NOT EXISTS saas_sync_logs (
        id INT(11) NOT NULL AUTO_INCREMENT,
        sync_source VARCHAR(32) NOT NULL DEFAULT 'auto',
        started_at DATETIME NOT NULL,
        ended_at DATETIME DEFAULT NULL,
        status VARCHAR(24) NOT NULL DEFAULT 'running',
        summary_json MEDIUMTEXT NULL,
        error_text MEDIUMTEXT NULL,
        PRIMARY KEY (id),
        KEY idx_sync_started (started_at),
        KEY idx_sync_status (status, ended_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

function programmit_saas_column_exists($db, $tableName, $columnName) {
    $tableName = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$tableName);
    $columnName = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$columnName);
    if ($tableName === '' || $columnName === '') {
        return false;
    }
    $qry = $db->sql_query("SHOW COLUMNS FROM `".$db->SanitizeForSQL($tableName)."` LIKE '".$db->SanitizeForSQL($columnName)."'");
    return ($qry && $db->sql_numrows($qry) > 0);
}

function programmit_saas_get_setting($db, $key, $default = '') {
    $key = strtolower(trim((string)$key));
    if ($key === '') {
        return (string)$default;
    }

    $bucket = &programmit_saas_settings_cache_bucket();
    if ((int)$bucket['__loaded'] !== 1) {
        programmit_saas_ensure_settings_table_once($db);
        $qry = $db->sql_query("SELECT setting_key, setting_value FROM saas_settings");
        $rows = array();
        while ($row = $db->sql_fetchrow($qry)) {
            if (!$row || !isset($row['setting_key'])) {
                continue;
            }
            $rowKey = strtolower(trim((string)$row['setting_key']));
            if ($rowKey === '') {
                continue;
            }
            $rows[$rowKey] = isset($row['setting_value']) ? (string)$row['setting_value'] : '';
        }
        $bucket['__data'] = $rows;
        $bucket['__loaded'] = 1;
    }

    if (array_key_exists($key, $bucket['__data'])) {
        return (string)$bucket['__data'][$key];
    }
    return (string)$default;
}

function programmit_saas_set_setting($db, $key, $value) {
    $key = strtolower(trim((string)$key));
    if ($key === '') {
        return false;
    }
    $value = (string)$value;
    programmit_saas_ensure_settings_table_once($db);
    if (method_exists($db, 'is_pgsql') && $db->is_pgsql()) {
        $sql = "INSERT INTO saas_settings
            (setting_key, setting_value, created_at, updated_at)
            VALUES
            ('".$db->SanitizeForSQL($key)."', '".$db->SanitizeForSQL($value)."', NOW(), NOW())
            ON CONFLICT (setting_key) DO UPDATE
            SET setting_value=EXCLUDED.setting_value,
                updated_at=NOW()";
    } else {
        $sql = "INSERT INTO saas_settings
            (setting_key, setting_value, created_at, updated_at)
            VALUES
            ('".$db->SanitizeForSQL($key)."', '".$db->SanitizeForSQL($value)."', NOW(), NOW())
            ON DUPLICATE KEY UPDATE
                setting_value=VALUES(setting_value),
                updated_at=NOW()";
    }
    $ok = (bool)$db->sql_query($sql);
    if ($ok) {
        $bucket = &programmit_saas_settings_cache_bucket();
        if (!isset($bucket['__data']) || !is_array($bucket['__data'])) {
            $bucket['__data'] = array();
        }
        $bucket['__data'][$key] = $value;
        $bucket['__loaded'] = 1;
    }
    return $ok;
}

function programmit_saas_get_control_host($db) {
    $raw = trim((string)programmit_saas_get_setting($db, 'saas_control_host', ''));
    $host = programmit_saas_normalize_host($raw);
    if ($host === '') {
        if (function_exists('programmit_finance_get_master_host')) {
            $host = programmit_saas_normalize_host(programmit_finance_get_master_host($db));
        }
    }
    if ($host === '') {
        $host = 'panel.programmit.com';
    }
    return $host;
}

function programmit_saas_can_manage_from_current_host($db) {
    $controlHost = programmit_saas_get_control_host($db);
    $currentHost = programmit_saas_current_host();
    if ($controlHost === '' || $currentHost === '') {
        return false;
    }
    if (strcasecmp($controlHost, $currentHost) === 0) {
        return true;
    }
    if (programmit_saas_is_local_host($currentHost)) {
        $allowLocal = (int)programmit_saas_get_setting($db, 'saas_allow_local_control', '1');
        return ($allowLocal === 1);
    }
    return false;
}

function programmit_saas_ensure_columns($db) {
    if (!programmit_saas_column_exists($db, 'users', 'tenant_id')) {
        $db->sql_query("ALTER TABLE users ADD tenant_id INT(11) NOT NULL DEFAULT 0");
        $db->sql_query("ALTER TABLE users ADD KEY idx_users_tenant (tenant_id)");
    }
    if (!programmit_saas_column_exists($db, 'users', 'is_tenant_owner')) {
        $db->sql_query("ALTER TABLE users ADD is_tenant_owner TINYINT(1) NOT NULL DEFAULT 0");
        $db->sql_query("ALTER TABLE users ADD KEY idx_users_tenant_owner (is_tenant_owner)");
    }

    if (!programmit_saas_column_exists($db, 'finance_recharges', 'tenant_id')) {
        $db->sql_query("ALTER TABLE finance_recharges ADD tenant_id INT(11) NOT NULL DEFAULT 0 AFTER user_id");
        $db->sql_query("ALTER TABLE finance_recharges ADD KEY idx_recharge_tenant (tenant_id, created_at)");
    }
    if (!programmit_saas_column_exists($db, 'finance_recharges', 'owner_user_id')) {
        $db->sql_query("ALTER TABLE finance_recharges ADD owner_user_id INT(11) NOT NULL DEFAULT 0 AFTER tenant_id");
        $db->sql_query("ALTER TABLE finance_recharges ADD KEY idx_recharge_owner (owner_user_id, created_at)");
    }

    if (!programmit_saas_column_exists($db, 'finance_wallet_logs', 'tenant_id')) {
        $db->sql_query("ALTER TABLE finance_wallet_logs ADD tenant_id INT(11) NOT NULL DEFAULT 0 AFTER user_id");
        $db->sql_query("ALTER TABLE finance_wallet_logs ADD KEY idx_wallet_tenant (tenant_id, created_at)");
    }
}

function programmit_saas_get_user_scope($db, $userId) {
    $userId = (int)$userId;
    if ($userId <= 0) {
        return array('tenant_id' => 0, 'owner_user_id' => 0);
    }

    $qry = $db->sql_query("SELECT user_id, upline, user_level, tenant_id
        FROM users
        WHERE user_id='".$db->SanitizeForSQL($userId)."'
        LIMIT 1");
    $row = $db->sql_fetchrow($qry);
    if (!$row) {
        return array('tenant_id' => 0, 'owner_user_id' => 0);
    }

    $tenantId = isset($row['tenant_id']) ? (int)$row['tenant_id'] : 0;
    $ownerUserId = 0;
    $userLevel = strtolower(trim((string)$row['user_level']));
    if ($tenantId > 0) {
        $tQry = $db->sql_query("SELECT owner_user_id
            FROM saas_tenants
            WHERE id='".$db->SanitizeForSQL($tenantId)."'
            LIMIT 1");
        $tRow = $db->sql_fetchrow($tQry);
        $ownerUserId = $tRow ? (int)$tRow['owner_user_id'] : 0;
    }

    if ($tenantId <= 0) {
        $candidateOwner = 0;
        if (in_array($userLevel, array('superadmin', 'administrator', 'subadmin', 'reseller', 'subreseller'), true)) {
            $candidateOwner = (int)$row['user_id'];
        } else {
            $candidateOwner = (int)$row['upline'];
        }

        if ($candidateOwner > 0) {
            $tenantQry = $db->sql_query("SELECT id, owner_user_id
                FROM saas_tenants
                WHERE owner_user_id='".$db->SanitizeForSQL($candidateOwner)."'
                ORDER BY id ASC
                LIMIT 1");
            $tenantRow = $db->sql_fetchrow($tenantQry);
            if ($tenantRow) {
                $tenantId = (int)$tenantRow['id'];
                $ownerUserId = (int)$tenantRow['owner_user_id'];
            }
        }
    }

    return array(
        'tenant_id' => $tenantId > 0 ? $tenantId : 0,
        'owner_user_id' => $ownerUserId > 0 ? $ownerUserId : 0
    );
}

function programmit_saas_sync_runtime($db, $source = 'auto') {
    $source = strtolower(trim((string)$source));
    if ($source === '') {
        $source = 'auto';
    }
    $db->sql_query(programmit_saas_sync_logs_table_sql());
    $db->sql_query("INSERT INTO saas_sync_logs (sync_source, started_at, status) VALUES ('".$db->SanitizeForSQL($source)."', NOW(), 'running')");
    $logId = (int)$db->sql_nextid();

    $summary = array(
        'users_tagged' => 0,
        'owners_tagged' => 0,
        'recharges_tagged' => 0,
        'wallet_logs_tagged' => 0
    );

    $tenantsQry = $db->sql_query("SELECT id, owner_user_id FROM saas_tenants WHERE status IN ('trial','active')");
    while ($tenant = $db->sql_fetchrow($tenantsQry)) {
        if (!$tenant) {
            continue;
        }
        $tenantId = (int)$tenant['id'];
        $ownerUserId = (int)$tenant['owner_user_id'];
        if ($tenantId <= 0 || $ownerUserId <= 0) {
            continue;
        }

        $db->sql_query("UPDATE users
            SET tenant_id='".$db->SanitizeForSQL($tenantId)."',
                is_tenant_owner=IF(user_id='".$db->SanitizeForSQL($ownerUserId)."',1,is_tenant_owner)
            WHERE user_id='".$db->SanitizeForSQL($ownerUserId)."'
              AND tenant_id<>'".$db->SanitizeForSQL($tenantId)."'");
        $summary['owners_tagged']++;

        $db->sql_query("UPDATE users
            SET tenant_id='".$db->SanitizeForSQL($tenantId)."'
            WHERE upline='".$db->SanitizeForSQL($ownerUserId)."'
              AND tenant_id<>'".$db->SanitizeForSQL($tenantId)."'");
        $summary['users_tagged']++;

        if (method_exists($db, 'is_pgsql') && $db->is_pgsql()) {
            $db->sql_query("UPDATE finance_recharges r
                SET tenant_id=u.tenant_id,
                    owner_user_id=CASE WHEN u.upline>0 THEN u.upline ELSE '".$db->SanitizeForSQL($ownerUserId)."' END
                FROM users u
                WHERE u.user_id=r.user_id
                  AND u.tenant_id='".$db->SanitizeForSQL($tenantId)."'
                  AND (r.tenant_id<>'".$db->SanitizeForSQL($tenantId)."' OR r.owner_user_id=0)");
        } else {
            $db->sql_query("UPDATE finance_recharges r
                INNER JOIN users u ON u.user_id=r.user_id
                SET r.tenant_id=u.tenant_id,
                    r.owner_user_id=IF(u.upline>0,u.upline,'".$db->SanitizeForSQL($ownerUserId)."')
                WHERE u.tenant_id='".$db->SanitizeForSQL($tenantId)."'
                  AND (r.tenant_id<>'".$db->SanitizeForSQL($tenantId)."' OR r.owner_user_id=0)");
        }
        $summary['recharges_tagged']++;

        if (method_exists($db, 'is_pgsql') && $db->is_pgsql()) {
            $db->sql_query("UPDATE finance_wallet_logs w
                SET tenant_id=r.tenant_id
                FROM finance_recharges r
                WHERE r.id=w.recharge_id
                  AND r.tenant_id='".$db->SanitizeForSQL($tenantId)."'
                  AND w.tenant_id<>'".$db->SanitizeForSQL($tenantId)."'");
        } else {
            $db->sql_query("UPDATE finance_wallet_logs w
                INNER JOIN finance_recharges r ON r.id=w.recharge_id
                SET w.tenant_id=r.tenant_id
                WHERE r.tenant_id='".$db->SanitizeForSQL($tenantId)."'
                  AND w.tenant_id<>'".$db->SanitizeForSQL($tenantId)."'");
        }
        $summary['wallet_logs_tagged']++;
    }

    $db->sql_query("UPDATE saas_sync_logs
        SET ended_at=NOW(),
            status='ok',
            summary_json='".$db->SanitizeForSQL(json_encode($summary))."'
        WHERE id='".$db->SanitizeForSQL($logId)."'
        LIMIT 1");

    programmit_saas_set_setting($db, 'saas_last_sync_at', gmdate('Y-m-d H:i:s'));
    return $summary;
}

function programmit_saas_maybe_auto_sync($db) {
    $enabled = (int)programmit_saas_get_setting($db, 'saas_auto_sync_enabled', '1');
    if ($enabled !== 1) {
        return false;
    }
    $lastRaw = trim((string)programmit_saas_get_setting($db, 'saas_last_sync_at', ''));
    $now = time();
    $lastTs = $lastRaw !== '' ? strtotime($lastRaw . ' UTC') : false;
    if ($lastTs !== false && ($now - $lastTs) < 45) {
        return false;
    }
    programmit_saas_sync_runtime($db, 'auto');
    return true;
}

function programmit_saas_seed_plans($db) {
    $defaults = array(
        array(
            'plan_code' => 'starter',
            'plan_name' => 'Starter',
            'description' => 'Plan base para comenzar a vender paneles.',
            'monthly_price_usd' => 19.00,
            'setup_fee_usd' => 0.00,
            'credit_price_usd' => 1.0000,
            'included_credits' => 25,
            'panel_limit' => 1,
            'user_limit' => 150,
            'method_limit' => 3,
            'is_active' => 1,
            'is_public' => 1
        ),
        array(
            'plan_code' => 'pro',
            'plan_name' => 'Pro',
            'description' => 'Para revendedores con mayor volumen.',
            'monthly_price_usd' => 59.00,
            'setup_fee_usd' => 0.00,
            'credit_price_usd' => 0.9500,
            'included_credits' => 100,
            'panel_limit' => 5,
            'user_limit' => 1000,
            'method_limit' => 10,
            'is_active' => 1,
            'is_public' => 1
        ),
        array(
            'plan_code' => 'enterprise',
            'plan_name' => 'Enterprise',
            'description' => 'Plan alto rendimiento para marca blanca masiva.',
            'monthly_price_usd' => 149.00,
            'setup_fee_usd' => 0.00,
            'credit_price_usd' => 0.9000,
            'included_credits' => 300,
            'panel_limit' => 25,
            'user_limit' => 5000,
            'method_limit' => 50,
            'is_active' => 1,
            'is_public' => 1
        )
    );

    $exists = array();
    $qry = $db->sql_query("SELECT plan_code FROM saas_plans");
    while ($row = $db->sql_fetchrow($qry)) {
        if (!$row) {
            continue;
        }
        $exists[strtolower((string)$row['plan_code'])] = true;
    }

    foreach ($defaults as $plan) {
        $key = strtolower((string)$plan['plan_code']);
        if (isset($exists[$key])) {
            continue;
        }

        $db->sql_query("INSERT INTO saas_plans
            (plan_code, plan_name, description, monthly_price_usd, setup_fee_usd, credit_price_usd,
             included_credits, panel_limit, user_limit, method_limit, is_active, is_public, created_at, updated_at)
            VALUES
            ('".$db->SanitizeForSQL($plan['plan_code'])."',
             '".$db->SanitizeForSQL($plan['plan_name'])."',
             '".$db->SanitizeForSQL($plan['description'])."',
             '".$db->SanitizeForSQL(number_format((float)$plan['monthly_price_usd'], 2, '.', ''))."',
             '".$db->SanitizeForSQL(number_format((float)$plan['setup_fee_usd'], 2, '.', ''))."',
             '".$db->SanitizeForSQL(number_format((float)$plan['credit_price_usd'], 4, '.', ''))."',
             '".$db->SanitizeForSQL((int)$plan['included_credits'])."',
             '".$db->SanitizeForSQL((int)$plan['panel_limit'])."',
             '".$db->SanitizeForSQL((int)$plan['user_limit'])."',
             '".$db->SanitizeForSQL((int)$plan['method_limit'])."',
             '".$db->SanitizeForSQL((int)$plan['is_active'])."',
             '".$db->SanitizeForSQL((int)$plan['is_public'])."',
             NOW(),
             NOW())");
    }
}

function programmit_saas_get_plan_id_by_code($db, $planCode) {
    $planCode = strtolower(trim((string)$planCode));
    if ($planCode === '') {
        return 0;
    }
    $qry = $db->sql_query("SELECT id FROM saas_plans
        WHERE plan_code='".$db->SanitizeForSQL($planCode)."'
        LIMIT 1");
    $row = $db->sql_fetchrow($qry);
    if (!$row) {
        return 0;
    }
    return (int)$row['id'];
}

function programmit_saas_ensure_default_tenant($db) {
    $panelHost = trim((string)programmit_saas_get_setting($db, 'saas_default_panel_host', 'panel.programmit.com'));
    $panelHost = programmit_saas_normalize_host($panelHost);
    if ($panelHost === '') {
        $panelHost = programmit_saas_current_host();
    }
    if ($panelHost === '' || !programmit_saas_valid_hostname($panelHost)) {
        return;
    }

    $tenantId = 0;
    $qry = $db->sql_query("SELECT id
        FROM saas_tenants
        WHERE tenant_key='programmit'
        LIMIT 1");
    $row = $db->sql_fetchrow($qry);
    if ($row) {
        $tenantId = (int)$row['id'];
    }

    if ($tenantId <= 0) {
        $planId = programmit_saas_get_plan_id_by_code($db, 'enterprise');
        if ($planId <= 0) {
            $planId = programmit_saas_get_plan_id_by_code($db, 'pro');
        }
        $db->sql_query("INSERT INTO saas_tenants
            (tenant_key, owner_user_id, plan_id, display_name, brand_name, support_email, status, credits_balance,
             monthly_price_usd, credit_price_usd, default_currency, timezone, notes, created_at, updated_at)
            VALUES
            ('programmit', '1', '".$db->SanitizeForSQL($planId)."', 'PROGRAMMIT', 'PROGRAMMIT',
             '', 'active', '0', '0.00', '0.0000', 'USD', 'UTC',
             'Tenant principal autogenerado por el sistema', NOW(), NOW())");

        $qry2 = $db->sql_query("SELECT id
            FROM saas_tenants
            WHERE tenant_key='programmit'
            LIMIT 1");
        $row2 = $db->sql_fetchrow($qry2);
        $tenantId = $row2 ? (int)$row2['id'] : 0;
    }

    if ($tenantId <= 0) {
        return;
    }

    $hostQry = $db->sql_query("SELECT id FROM saas_tenant_domains
        WHERE hostname='".$db->SanitizeForSQL($panelHost)."'
        LIMIT 1");
    $hostRow = $db->sql_fetchrow($hostQry);
    if (!$hostRow) {
        $db->sql_query("UPDATE saas_tenant_domains
            SET is_primary='0', updated_at=NOW()
            WHERE tenant_id='".$db->SanitizeForSQL($tenantId)."'");
        $db->sql_query("INSERT INTO saas_tenant_domains
            (tenant_id, hostname, is_primary, is_active, verified_at, created_at, updated_at)
            VALUES
            ('".$db->SanitizeForSQL($tenantId)."',
             '".$db->SanitizeForSQL($panelHost)."',
             '1',
             '1',
             NOW(),
             NOW(),
             NOW())");
    }
}

function programmit_saas_ensure_tables($db) {
    static $ready = false;
    if ($ready) {
        return true;
    }

    $ttlSeconds = 86400;
    $stampFile = programmit_saas_schema_stamp_path();
    clearstatcache(true, $stampFile);
    if (is_file($stampFile) && (time() - (int)@filemtime($stampFile)) < $ttlSeconds) {
        $ready = true;
        return true;
    }

    $db->sql_query(programmit_saas_plans_table_sql());
    $db->sql_query(programmit_saas_tenants_table_sql());
    $db->sql_query(programmit_saas_domains_table_sql());
    $db->sql_query(programmit_saas_branding_table_sql());
    $db->sql_query(programmit_saas_settings_table_sql());
    $db->sql_query(programmit_saas_sync_logs_table_sql());

    if (trim((string)programmit_saas_get_setting($db, 'saas_control_host', '')) === '') {
        programmit_saas_set_setting($db, 'saas_control_host', 'panel.programmit.com');
    }
    if (trim((string)programmit_saas_get_setting($db, 'saas_default_panel_host', '')) === '') {
        programmit_saas_set_setting($db, 'saas_default_panel_host', 'panel.programmit.com');
    }
    if (trim((string)programmit_saas_get_setting($db, 'saas_auto_sync_enabled', '')) === '') {
        programmit_saas_set_setting($db, 'saas_auto_sync_enabled', '1');
    }
    if (trim((string)programmit_saas_get_setting($db, 'saas_allow_local_control', '')) === '') {
        programmit_saas_set_setting($db, 'saas_allow_local_control', '1');
    }

    programmit_saas_ensure_columns($db);
    programmit_saas_seed_plans($db);
    programmit_saas_ensure_default_tenant($db);

    if (function_exists('programmit_finance_set_setting')) {
        $controlHost = programmit_saas_get_control_host($db);
        if ($controlHost !== '') {
            programmit_finance_set_setting($db, 'finance_master_host', $controlHost);
        }
    }

    programmit_saas_maybe_auto_sync($db);

    @touch($stampFile);
    $ready = true;
    return true;
}

function programmit_saas_get_tenant_context($db) {
    static $cached = false;
    static $ctx = array();
    if ($cached) {
        return $ctx;
    }
    $cached = true;

    programmit_saas_ensure_tables($db);
    $host = programmit_saas_current_host();

    $ctx = array(
        'is_bound' => 0,
        'host' => $host,
        'tenant_id' => 0,
        'tenant_key' => '',
        'display_name' => '',
        'brand_name' => '',
        'support_email' => '',
        'tenant_status' => '',
        'owner_user_id' => 0,
        'plan_id' => 0,
        'plan_code' => '',
        'plan_name' => '',
        'plan_monthly_usd' => 0.0,
        'plan_credit_price_usd' => 0.0,
        'tenant_credit_price_usd' => 0.0,
        'effective_credit_price_usd' => 1.0,
        'logo_url' => '',
        'favicon_url' => '',
        'primary_color' => '#2fbde5',
        'accent_color' => '#95f100',
        'background_color' => '#132744',
        'custom_css' => ''
    );

    if ($host === '') {
        return $ctx;
    }

    $cacheTtlSeconds = 300;
    $cachedCtx = programmit_saas_tenant_ctx_cache_read($host, $cacheTtlSeconds);
    if (is_array($cachedCtx) && !empty($cachedCtx)) {
        $ctx = array_merge($ctx, $cachedCtx);
        $ctx['host'] = $host;
        $ctx['is_bound'] = !empty($ctx['is_bound']) ? 1 : 0;
        return $ctx;
    }

    $qry = $db->sql_query("SELECT
        t.id AS tenant_id,
        t.tenant_key,
        t.owner_user_id,
        t.plan_id,
        t.display_name,
        t.brand_name,
        t.support_email,
        t.status AS tenant_status,
        t.credit_price_usd AS tenant_credit_price_usd,
        p.plan_code,
        p.plan_name,
        p.monthly_price_usd AS plan_monthly_usd,
        p.credit_price_usd AS plan_credit_price_usd,
        b.logo_url,
        b.favicon_url,
        b.primary_color,
        b.accent_color,
        b.background_color,
        b.custom_css
        FROM saas_tenant_domains d
        INNER JOIN saas_tenants t ON t.id=d.tenant_id
        LEFT JOIN saas_plans p ON p.id=t.plan_id
        LEFT JOIN saas_tenant_branding b ON b.tenant_id=t.id
        WHERE d.hostname='".$db->SanitizeForSQL($host)."'
          AND d.is_active='1'
        LIMIT 1");
    $row = $db->sql_fetchrow($qry);
    if (!$row) {
        programmit_saas_tenant_ctx_cache_write($host, $ctx);
        return $ctx;
    }

    $tenantCredit = (float)$row['tenant_credit_price_usd'];
    $planCredit = (float)$row['plan_credit_price_usd'];
    $effectiveCredit = 1.0;
    if ($tenantCredit > 0) {
        $effectiveCredit = $tenantCredit;
    } elseif ($planCredit > 0) {
        $effectiveCredit = $planCredit;
    }

    $ctx = array(
        'is_bound' => 1,
        'host' => $host,
        'tenant_id' => (int)$row['tenant_id'],
        'tenant_key' => (string)$row['tenant_key'],
        'display_name' => (string)$row['display_name'],
        'brand_name' => (string)$row['brand_name'],
        'support_email' => (string)$row['support_email'],
        'tenant_status' => (string)$row['tenant_status'],
        'owner_user_id' => (int)$row['owner_user_id'],
        'plan_id' => (int)$row['plan_id'],
        'plan_code' => (string)$row['plan_code'],
        'plan_name' => (string)$row['plan_name'],
        'plan_monthly_usd' => (float)$row['plan_monthly_usd'],
        'plan_credit_price_usd' => $planCredit,
        'tenant_credit_price_usd' => $tenantCredit,
        'effective_credit_price_usd' => $effectiveCredit,
        'logo_url' => (string)$row['logo_url'],
        'favicon_url' => (string)$row['favicon_url'],
        'primary_color' => (string)$row['primary_color'],
        'accent_color' => (string)$row['accent_color'],
        'background_color' => (string)$row['background_color'],
        'custom_css' => (string)$row['custom_css']
    );
    programmit_saas_tenant_ctx_cache_write($host, $ctx);
    return $ctx;
}

function programmit_saas_apply_context($db, $smarty, $ctx) {
    if (!is_array($ctx)) {
        $ctx = array();
    }

    $brand = '';
    if (isset($ctx['brand_name']) && trim((string)$ctx['brand_name']) !== '') {
        $brand = trim((string)$ctx['brand_name']);
    } elseif (isset($ctx['display_name']) && trim((string)$ctx['display_name']) !== '') {
        $brand = trim((string)$ctx['display_name']);
    }

    if ($brand !== '') {
        $db->SetWebsiteName($brand);
        $db->SetWebsiteTitle($brand . ' PANEL');
    }

    $smarty->assign('saas_ctx', $ctx);
    $smarty->assign('saas_bound', isset($ctx['is_bound']) ? (int)$ctx['is_bound'] : 0);
    $smarty->assign('saas_tenant_id', isset($ctx['tenant_id']) ? (int)$ctx['tenant_id'] : 0);
    $smarty->assign('saas_tenant_key', isset($ctx['tenant_key']) ? (string)$ctx['tenant_key'] : '');
    $smarty->assign('saas_tenant_brand', $brand);
    $smarty->assign('saas_plan_code', isset($ctx['plan_code']) ? (string)$ctx['plan_code'] : '');
    $smarty->assign('saas_plan_name', isset($ctx['plan_name']) ? (string)$ctx['plan_name'] : '');
    $smarty->assign('saas_credit_price_usd', isset($ctx['effective_credit_price_usd']) ? (float)$ctx['effective_credit_price_usd'] : 1.0);
    $smarty->assign('saas_support_email', isset($ctx['support_email']) ? (string)$ctx['support_email'] : '');
    $smarty->assign('saas_custom_css', isset($ctx['custom_css']) ? (string)$ctx['custom_css'] : '');
}

function programmit_saas_list_plans($db) {
    programmit_saas_ensure_tables($db);
    $rows = array();
    $qry = $db->sql_query("SELECT
        id, plan_code, plan_name, description,
        monthly_price_usd, setup_fee_usd, credit_price_usd,
        included_credits, panel_limit, user_limit, method_limit,
        is_active, is_public, created_at, updated_at
        FROM saas_plans
        ORDER BY id ASC");
    while ($row = $db->sql_fetchrow($qry)) {
        if (!$row) {
            continue;
        }
        $row['id'] = (int)$row['id'];
        $row['monthly_price_usd'] = (float)$row['monthly_price_usd'];
        $row['setup_fee_usd'] = (float)$row['setup_fee_usd'];
        $row['credit_price_usd'] = (float)$row['credit_price_usd'];
        $row['included_credits'] = (int)$row['included_credits'];
        $row['panel_limit'] = (int)$row['panel_limit'];
        $row['user_limit'] = (int)$row['user_limit'];
        $row['method_limit'] = (int)$row['method_limit'];
        $row['is_active'] = (int)$row['is_active'];
        $row['is_public'] = (int)$row['is_public'];
        $rows[] = $row;
    }
    return $rows;
}

function programmit_saas_list_tenants($db) {
    programmit_saas_ensure_tables($db);
    $rows = array();
    $qry = $db->sql_query("SELECT
        t.id, t.tenant_key, t.owner_user_id, t.plan_id, t.display_name, t.brand_name,
        t.support_email, t.status, t.credits_balance, t.monthly_price_usd, t.credit_price_usd,
        t.default_currency, t.timezone, t.created_at, t.updated_at,
        p.plan_code, p.plan_name
        FROM saas_tenants t
        LEFT JOIN saas_plans p ON p.id=t.plan_id
        ORDER BY t.id ASC");
    while ($row = $db->sql_fetchrow($qry)) {
        if (!$row) {
            continue;
        }
        $row['id'] = (int)$row['id'];
        $row['owner_user_id'] = (int)$row['owner_user_id'];
        $row['plan_id'] = (int)$row['plan_id'];
        $row['credits_balance'] = (int)$row['credits_balance'];
        $row['monthly_price_usd'] = (float)$row['monthly_price_usd'];
        $row['credit_price_usd'] = (float)$row['credit_price_usd'];
        $rows[] = $row;
    }
    return $rows;
}

function programmit_saas_list_domains_by_tenant($db, $tenantId) {
    $tenantId = (int)$tenantId;
    if ($tenantId <= 0) {
        return array();
    }
    $rows = array();
    $qry = $db->sql_query("SELECT id, tenant_id, hostname, is_primary, is_active, verified_at
        FROM saas_tenant_domains
        WHERE tenant_id='".$db->SanitizeForSQL($tenantId)."'
        ORDER BY is_primary DESC, id ASC");
    while ($row = $db->sql_fetchrow($qry)) {
        if (!$row) {
            continue;
        }
        $row['id'] = (int)$row['id'];
        $row['tenant_id'] = (int)$row['tenant_id'];
        $row['is_primary'] = (int)$row['is_primary'];
        $row['is_active'] = (int)$row['is_active'];
        $rows[] = $row;
    }
    return $rows;
}

function programmit_saas_list_sync_logs($db, $limit = 50) {
    $limit = (int)$limit;
    if ($limit <= 0) {
        $limit = 50;
    }
    if ($limit > 500) {
        $limit = 500;
    }
    $rows = array();
    $qry = $db->sql_query("SELECT id, sync_source, started_at, ended_at, status, summary_json, error_text
        FROM saas_sync_logs
        ORDER BY id DESC
        LIMIT ".$db->SanitizeForSQL($limit));
    while ($row = $db->sql_fetchrow($qry)) {
        if (!$row) {
            continue;
        }
        $row['id'] = (int)$row['id'];
        $rows[] = $row;
    }
    return $rows;
}
