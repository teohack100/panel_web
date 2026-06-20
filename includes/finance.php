<?php
if (preg_match("/finance.php/i", $_SERVER['SCRIPT_NAME'])) {
    Header("Location: /");
    die();
}

require_once __DIR__ . '/finance_bnb.php';

function programmit_finance_methods_table_sql() {
    return "CREATE TABLE IF NOT EXISTS finance_payment_methods (
        id INT(11) NOT NULL AUTO_INCREMENT,
        method_key VARCHAR(64) NOT NULL,
        provider_key VARCHAR(64) NOT NULL DEFAULT '',
        method_name VARCHAR(120) NOT NULL,
        display_order INT(11) NOT NULL DEFAULT 100,
        min_amount DECIMAL(12,2) NOT NULL DEFAULT 1.00,
        max_amount DECIMAL(12,2) NOT NULL DEFAULT 1000.00,
        fee_fixed DECIMAL(12,2) NOT NULL DEFAULT 0.00,
        fee_percent DECIMAL(7,2) NOT NULL DEFAULT 0.00,
        rate_bob DECIMAL(12,4) NOT NULL DEFAULT 0.0000,
        allow_new_users TINYINT(1) NOT NULL DEFAULT 1,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        settings_json MEDIUMTEXT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY uniq_method_key (method_key),
        KEY idx_method_active (is_active, display_order)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

function programmit_finance_recharges_table_sql() {
    return "CREATE TABLE IF NOT EXISTS finance_recharges (
        id INT(11) NOT NULL AUTO_INCREMENT,
        recharge_ref VARCHAR(64) NOT NULL,
        user_id INT(11) NOT NULL,
        tenant_id INT(11) NOT NULL DEFAULT 0,
        owner_user_id INT(11) NOT NULL DEFAULT 0,
        method_id INT(11) NOT NULL,
        method_key VARCHAR(64) NOT NULL,
        method_name VARCHAR(120) NOT NULL,
        amount_usd DECIMAL(12,2) NOT NULL DEFAULT 0.00,
        fee_usd DECIMAL(12,2) NOT NULL DEFAULT 0.00,
        total_usd DECIMAL(12,2) NOT NULL DEFAULT 0.00,
        rate_bob DECIMAL(12,4) NOT NULL DEFAULT 0.0000,
        total_bob DECIMAL(12,2) NOT NULL DEFAULT 0.00,
        credits_to_add INT(11) NOT NULL DEFAULT 0,
        provider_txn_id VARCHAR(120) NOT NULL DEFAULT '',
        qr_payload MEDIUMTEXT NULL,
        qr_image_url MEDIUMTEXT NULL,
        provider_request MEDIUMTEXT NULL,
        provider_response MEDIUMTEXT NULL,
        status VARCHAR(24) NOT NULL DEFAULT 'pending',
        created_ip VARCHAR(64) NOT NULL DEFAULT '',
        created_at DATETIME NOT NULL,
        expires_at DATETIME DEFAULT NULL,
        paid_at DATETIME DEFAULT NULL,
        updated_at DATETIME DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY uniq_recharge_ref (recharge_ref),
        KEY idx_recharge_user (user_id, created_at),
        KEY idx_recharge_tenant (tenant_id, created_at),
        KEY idx_recharge_owner (owner_user_id, created_at),
        KEY idx_recharge_status (status, created_at),
        KEY idx_recharge_provider (provider_txn_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

function programmit_finance_wallet_logs_table_sql() {
    return "CREATE TABLE IF NOT EXISTS finance_wallet_logs (
        id INT(11) NOT NULL AUTO_INCREMENT,
        user_id INT(11) NOT NULL,
        tenant_id INT(11) NOT NULL DEFAULT 0,
        recharge_id INT(11) NOT NULL DEFAULT 0,
        log_type VARCHAR(24) NOT NULL DEFAULT 'recharge',
        amount_before INT(11) NOT NULL DEFAULT 0,
        amount_change INT(11) NOT NULL DEFAULT 0,
        amount_after INT(11) NOT NULL DEFAULT 0,
        description VARCHAR(255) NOT NULL DEFAULT '',
        created_by INT(11) NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id),
        KEY idx_wallet_user (user_id, created_at),
        KEY idx_wallet_tenant (tenant_id, created_at),
        KEY idx_wallet_recharge (recharge_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

function programmit_finance_settings_table_sql() {
    return "CREATE TABLE IF NOT EXISTS finance_settings (
        id INT(11) NOT NULL AUTO_INCREMENT,
        setting_key VARCHAR(64) NOT NULL,
        setting_value MEDIUMTEXT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY uniq_setting_key (setting_key)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

function programmit_finance_safe_float($value, $default = 0.0) {
    if (is_numeric($value)) {
        return (float)$value;
    }
    return (float)$default;
}

function programmit_finance_safe_int($value, $default = 0) {
    if (is_numeric($value)) {
        return (int)$value;
    }
    return (int)$default;
}

function programmit_finance_current_host() {
    $host = isset($_SERVER['HTTP_HOST']) ? (string)$_SERVER['HTTP_HOST'] : '';
    $host = preg_replace('/:\d+$/', '', trim($host));
    return strtolower((string)$host);
}

function programmit_finance_normalize_host($host) {
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

function programmit_finance_is_local_host($host) {
    $host = programmit_finance_normalize_host($host);
    return ($host === 'localhost' || $host === '127.0.0.1' || $host === '::1');
}

function programmit_finance_get_master_host($db) {
    $raw = programmit_finance_get_setting($db, 'finance_master_host', 'panel.programmit.com');
    $master = programmit_finance_normalize_host($raw);
    if ($master === '') {
        $master = 'panel.programmit.com';
    }
    return $master;
}

function programmit_finance_can_edit_from_current_host($db) {
    $current = programmit_finance_current_host();
    $master = programmit_finance_get_master_host($db);
    return ($master !== '' && strcasecmp($current, $master) === 0);
}

function programmit_finance_json_decode($raw) {
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

function programmit_finance_json_encode($arr) {
    if (!is_array($arr)) {
        $arr = array();
    }
    $json = json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!is_string($json)) {
        return '{}';
    }
    return $json;
}

function programmit_finance_schema_stamp_path() {
    $tmpDir = rtrim((string)sys_get_temp_dir(), '/\\');
    if ($tmpDir === '') {
        $tmpDir = '/tmp';
    }
    $stampDir = $tmpDir . DIRECTORY_SEPARATOR . 'programmit_bootstrap';
    if (!is_dir($stampDir)) {
        @mkdir($stampDir, 0775, true);
    }
    return $stampDir . DIRECTORY_SEPARATOR . 'finance_schema.stamp';
}

function programmit_finance_ensure_settings_table_once($db) {
    static $ready = false;
    if ($ready) {
        return true;
    }
    $db->sql_query(programmit_finance_settings_table_sql());
    $ready = true;
    return true;
}

function &programmit_finance_settings_cache_bucket() {
    if (!isset($GLOBALS['_programmit_finance_settings_cache']) || !is_array($GLOBALS['_programmit_finance_settings_cache'])) {
        $GLOBALS['_programmit_finance_settings_cache'] = array(
            '__loaded' => 0,
            '__data' => array()
        );
    }
    return $GLOBALS['_programmit_finance_settings_cache'];
}

function programmit_finance_get_setting($db, $key, $default = '') {
    $key = strtolower(trim((string)$key));
    if ($key === '') {
        return (string)$default;
    }

    $bucket = &programmit_finance_settings_cache_bucket();
    if ((int)$bucket['__loaded'] !== 1) {
        programmit_finance_ensure_settings_table_once($db);
        $qry = $db->sql_query("SELECT setting_key, setting_value FROM finance_settings");
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

function programmit_finance_set_setting($db, $key, $value) {
    $key = strtolower(trim((string)$key));
    if ($key === '') {
        return false;
    }
    $value = (string)$value;
    programmit_finance_ensure_settings_table_once($db);
    if (method_exists($db, 'is_pgsql') && $db->is_pgsql()) {
        $sql = "INSERT INTO finance_settings
            (setting_key, setting_value, created_at, updated_at)
            VALUES
            ('".$db->SanitizeForSQL($key)."', '".$db->SanitizeForSQL($value)."', NOW(), NOW())
            ON CONFLICT (setting_key) DO UPDATE
            SET setting_value=EXCLUDED.setting_value,
                updated_at=NOW()";
    } else {
        $sql = "INSERT INTO finance_settings
            (setting_key, setting_value, created_at, updated_at)
            VALUES
            ('".$db->SanitizeForSQL($key)."', '".$db->SanitizeForSQL($value)."', NOW(), NOW())
            ON DUPLICATE KEY UPDATE
                setting_value=VALUES(setting_value),
                updated_at=NOW()";
    }
    $ok = (bool)$db->sql_query($sql);
    if ($ok) {
        $bucket = &programmit_finance_settings_cache_bucket();
        if (!isset($bucket['__data']) || !is_array($bucket['__data'])) {
            $bucket['__data'] = array();
        }
        $bucket['__data'][$key] = $value;
        $bucket['__loaded'] = 1;
    }
    return $ok;
}

function programmit_finance_get_default_rate_bob($db) {
    $raw = programmit_finance_get_setting($db, 'usd_bob_default_rate', '6.95');
    $rate = programmit_finance_safe_float($raw, 6.95);
    if ($rate <= 0) {
        $rate = 6.95;
    }
    return $rate;
}

function programmit_finance_get_credit_price_usd($db) {
    $raw = programmit_finance_get_setting($db, 'credit_price_usd', '1.00');
    $price = programmit_finance_safe_float($raw, 1.00);
    if ($price <= 0) {
        $price = 1.00;
    }
    return $price;
}

function programmit_finance_effective_credit_price($db, $method = array()) {
    $price = programmit_finance_get_credit_price_usd($db);
    if (is_array($method) && isset($method['settings']) && is_array($method['settings'])) {
        $localRaw = trim((string)programmit_finance_setting_value($method['settings'], 'credit_price_usd', ''));
        if ($localRaw !== '') {
            $local = programmit_finance_safe_float($localRaw, 0);
            if ($local > 0) {
                $price = $local;
            }
        }
    }
    return $price;
}

function programmit_finance_compute_credits_to_add($amountUsd, $creditPriceUsd) {
    $amountUsd = programmit_finance_safe_float($amountUsd, 0);
    $creditPriceUsd = programmit_finance_safe_float($creditPriceUsd, 1);
    if ($creditPriceUsd <= 0) {
        $creditPriceUsd = 1;
    }
    if ($amountUsd <= 0) {
        return 0;
    }
    return (int)floor(($amountUsd + 0.000001) / $creditPriceUsd);
}

function programmit_finance_setting_value($settings, $key, $default = '') {
    if (!is_array($settings)) {
        return (string)$default;
    }
    if (!array_key_exists($key, $settings)) {
        return (string)$default;
    }
    return trim((string)$settings[$key]);
}

function programmit_finance_array_get_path($arr, $path) {
    if (!is_array($arr)) {
        return '';
    }
    $path = trim((string)$path);
    if ($path === '') {
        return '';
    }
    $chunks = explode('.', $path);
    $cur = $arr;
    foreach ($chunks as $chunk) {
        $chunk = trim((string)$chunk);
        if ($chunk === '') {
            return '';
        }
        if (!is_array($cur) || !array_key_exists($chunk, $cur)) {
            return '';
        }
        $cur = $cur[$chunk];
    }
    if (is_scalar($cur) || $cur === null) {
        return trim((string)$cur);
    }
    return '';
}

function programmit_finance_array_get_first_path($arr, $paths = array()) {
    if (!is_array($paths)) {
        return '';
    }
    foreach ($paths as $path) {
        $val = programmit_finance_array_get_path($arr, $path);
        if ($val !== '') {
            return $val;
        }
    }
    return '';
}

function programmit_finance_normalize_qr_image($value) {
    $value = trim((string)$value);
    if ($value === '') {
        return '';
    }

    $lower = strtolower($value);
    if (strpos($lower, 'http://') === 0 || strpos($lower, 'https://') === 0 || strpos($lower, 'data:image/') === 0) {
        return $value;
    }

    $raw = preg_replace('/\s+/', '', $value);
    if ($raw === null || $raw === '') {
        return '';
    }
    if (strlen($raw) < 80) {
        return '';
    }
    if (!preg_match('/^[A-Za-z0-9+\/=]+$/', $raw)) {
        return '';
    }

    $mime = 'image/png';
    if (strpos($raw, '/9j/') === 0) {
        $mime = 'image/jpeg';
    } elseif (strpos($raw, 'R0lGOD') === 0) {
        $mime = 'image/gif';
    }

    return 'data:' . $mime . ';base64,' . $raw;
}

function programmit_finance_resolve_qr_provider($method = array(), $settings = array()) {
    $provider = strtolower(programmit_finance_setting_value($settings, 'qr_provider', ''));
    if ($provider === '') {
        $provider = strtolower(programmit_finance_setting_value($settings, 'qb_provider', ''));
    }
    if ($provider !== '') {
        return $provider;
    }

    $providerKey = is_array($method) ? strtolower(trim((string)($method['provider_key'] ?? ''))) : '';
    if ($providerKey === 'bnb_qr') {
        return 'bnb';
    }
    if ($providerKey === 'veripagos_qr') {
        return 'veripagos';
    }
    return '';
}

function programmit_finance_is_qr_bolivia_method($method = array(), $settings = array()) {
    $methodKey = is_array($method) ? strtolower(trim((string)($method['method_key'] ?? ''))) : '';
    $providerKey = is_array($method) ? strtolower(trim((string)($method['provider_key'] ?? ''))) : '';
    if ($methodKey === 'qr_bolivia_auto' || $methodKey === 'veripagos') {
        return true;
    }
    if ($providerKey === 'bnb_qr' || $providerKey === 'veripagos_qr') {
        return true;
    }
    return programmit_finance_resolve_qr_provider($method, $settings) !== '';
}

function programmit_finance_create_bnb_provider_qr($method, $recharge, $settings = array()) {
    $result = array(
        'ok' => false,
        'provider_txn_id' => '',
        'qr_image_url' => '',
        'qr_payload' => '',
        'expires_at' => '',
        'request_raw' => '',
        'response_raw' => '',
        'error' => ''
    );

    $expiryMinutes = (int)programmit_finance_setting_value($settings, 'qb_expiry_minutes', '15');
    if ($expiryMinutes < 1 || $expiryMinutes > 1440) {
        $expiryMinutes = 15;
    }

    $client = new ProgrammitFinanceBnbQrSimple(array(
        'account_id' => programmit_finance_setting_value($settings, 'bnb_account_id', ''),
        'authorization_id' => programmit_finance_setting_value($settings, 'bnb_authorization_id', ''),
        'destination_account_id' => (int)programmit_finance_setting_value($settings, 'bnb_destination_account_id', '1'),
        'currency' => programmit_finance_setting_value($settings, 'bnb_currency', 'BOB'),
        'token_url' => programmit_finance_setting_value($settings, 'bnb_token_url', 'http://test.bnb.com.bo/ClientAuthentication.API/api/v1/auth/token'),
        'qr_url' => programmit_finance_setting_value($settings, 'bnb_qr_url', 'http://test.bnb.com.bo/QRSimple.API/api/v1/main/getQRWithImageAsync'),
        'status_url' => programmit_finance_setting_value($settings, 'bnb_status_url', 'http://test.bnb.com.bo/QRSimple.API/api/v1/main/getQRStatusAsync'),
        'cancel_url' => programmit_finance_setting_value($settings, 'bnb_cancel_url', 'http://test.bnb.com.bo/QRSimple.API/api/v1/main/CancelQRByIdAsync'),
    ));

    $reference = trim((string)($recharge['recharge_ref'] ?? ''));
    $amountBob = round(programmit_finance_safe_float($recharge['total_bob'], 0), 2);
    $gloss = 'Recarga ' . $reference;
    if (strlen($gloss) > 80) {
        $gloss = substr($gloss, 0, 80);
    }

    $additionalData = array(
        'reference' => $reference,
        'recharge_ref' => $reference,
        'user_id' => (int)($recharge['user_id'] ?? 0),
        'method_key' => (string)($recharge['method_key'] ?? ''),
    );

    $result['request_raw'] = programmit_finance_json_encode(array(
        'currency' => programmit_finance_setting_value($settings, 'bnb_currency', 'BOB'),
        'gloss' => $gloss,
        'amount' => $amountBob,
        'singleUse' => true,
        'expirationDate' => date('Y-m-d', time() + ($expiryMinutes * 60)),
        'additionalData' => $additionalData,
        'destinationAccountId' => (int)programmit_finance_setting_value($settings, 'bnb_destination_account_id', '1')
    ));

    $bnbResult = $client->generarQR($amountBob, $additionalData, $expiryMinutes, $gloss, true);
    $result['response_raw'] = programmit_finance_json_encode(isset($bnbResult['raw']) && is_array($bnbResult['raw']) ? $bnbResult['raw'] : array());

    if (empty($bnbResult['success'])) {
        $result['error'] = isset($bnbResult['error']) ? (string)$bnbResult['error'] : 'No se pudo generar QR en BNB.';
        return $result;
    }

    $data = isset($bnbResult['data']) && is_array($bnbResult['data']) ? $bnbResult['data'] : array();
    $result['ok'] = true;
    $result['provider_txn_id'] = trim((string)($data['movimiento_id'] ?? ''));
    $result['qr_payload'] = trim((string)($data['qr'] ?? ''));
    $result['qr_image_url'] = trim((string)($data['qr_image'] ?? ''));
    $result['expires_at'] = trim((string)($data['expiration_date'] ?? ''));
    if ($result['expires_at'] === '') {
        $result['expires_at'] = date('Y-m-d 23:59:59', strtotime('+' . $expiryMinutes . ' minutes'));
    }

    return $result;
}

function programmit_finance_format_veripagos_vigencia($minutes) {
    $minutes = (int)$minutes;
    if ($minutes < 1) {
        $minutes = 15;
    }
    $days = (int)floor($minutes / 1440);
    $remaining = $minutes % 1440;
    $hours = (int)floor($remaining / 60);
    $mins = (int)($remaining % 60);
    return $days . '/' . str_pad((string)$hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad((string)$mins, 2, '0', STR_PAD_LEFT);
}

function programmit_finance_create_veripagos_provider_qr($method, $recharge, $settings = array()) {
    $result = array(
        'ok' => false,
        'provider_txn_id' => '',
        'qr_image_url' => '',
        'qr_payload' => '',
        'expires_at' => '',
        'request_raw' => '',
        'response_raw' => '',
        'error' => ''
    );

    $createUrl = programmit_finance_setting_value($settings, 'vp_base_url', '');
    if ($createUrl === '') {
        $createUrl = programmit_finance_setting_value($settings, 'create_url', 'https://veripagos.com/api/bcp/generar-qr');
    }

    $username = programmit_finance_setting_value($settings, 'vp_username', '');
    if ($username === '') {
        $username = programmit_finance_setting_value($settings, 'api_user', '');
    }

    $password = programmit_finance_setting_value($settings, 'vp_password', '');
    if ($password === '') {
        $password = programmit_finance_setting_value($settings, 'api_password', '');
    }

    $secretKey = programmit_finance_setting_value($settings, 'vp_secret_key', '');
    if ($secretKey === '') {
        $secretKey = programmit_finance_setting_value($settings, 'secret', '');
    }
    if ($secretKey === '') {
        $secretKey = programmit_finance_setting_value($settings, 'api_key', '');
    }

    if ($createUrl === '') {
        $result['error'] = 'VeriPagos sin URL configurada.';
        return $result;
    }
    if ($username === '' || $password === '') {
        $result['error'] = 'VeriPagos requiere usuario y contrasena.';
        return $result;
    }
    if ($secretKey === '') {
        $result['error'] = 'VeriPagos requiere Secret Key.';
        return $result;
    }

    $expiryMinutes = (int)programmit_finance_setting_value($settings, 'qb_expiry_minutes', '15');
    if ($expiryMinutes < 1 || $expiryMinutes > 1440) {
        $expiryMinutes = 15;
    }

    $reference = trim((string)($recharge['recharge_ref'] ?? ''));
    $payload = array(
        'secret_key' => $secretKey,
        'monto' => round(programmit_finance_safe_float($recharge['total_bob'], 0), 2),
        'uso_unico' => true,
        'vigencia' => programmit_finance_format_veripagos_vigencia($expiryMinutes),
        'detalle' => 'Recarga ' . $reference,
        'data' => array(
            'reference' => $reference,
            'recharge_ref' => $reference,
            'user_id' => (int)($recharge['user_id'] ?? 0),
            'recharge_id' => (int)($recharge['id'] ?? 0)
        )
    );
    $result['request_raw'] = programmit_finance_json_encode($payload);

    $headers = array(
        'Authorization: Basic ' . base64_encode($username . ':' . $password)
    );
    $extraHeaders = programmit_finance_setting_value($settings, 'extra_headers', '');
    if ($extraHeaders !== '') {
        $lines = preg_split('/\r\n|\r|\n/', $extraHeaders);
        if (is_array($lines)) {
            foreach ($lines as $line) {
                $line = trim((string)$line);
                if ($line !== '' && strpos($line, ':') !== false) {
                    $headers[] = $line;
                }
            }
        }
    }

    $http = programmit_finance_http_json_post($createUrl, $payload, $headers);
    $result['response_raw'] = isset($http['body']) ? (string)$http['body'] : '';
    if (!$http['ok']) {
        $result['error'] = 'No se pudo crear el QR automatico en VeriPagos.';
        if (!empty($http['error'])) {
            $result['error'] .= ' ' . trim((string)$http['error']);
        }
        return $result;
    }

    $json = isset($http['json']) && is_array($http['json']) ? $http['json'] : array();
    $codigo = isset($json['Codigo']) ? (int)$json['Codigo'] : 0;
    $mensaje = trim((string)($json['Mensaje'] ?? ''));
    if ($codigo !== 0) {
        $result['error'] = ($mensaje !== '') ? ('VeriPagos: ' . $mensaje) : 'VeriPagos no devolvio QR.';
        return $result;
    }

    $result['provider_txn_id'] = programmit_finance_array_get_first_path($json, array(
        'Data.movimiento_id',
        'data.movimiento_id',
        'movimiento_id',
        'id'
    ));
    $result['qr_payload'] = programmit_finance_array_get_first_path($json, array(
        'Data.qr',
        'data.qr',
        'Data.qr_text',
        'data.qr_text',
        'qr',
        'qr_text'
    ));
    $result['qr_image_url'] = programmit_finance_normalize_qr_image($result['qr_payload']);
    $result['expires_at'] = gmdate('Y-m-d\TH:i:s\Z', time() + ($expiryMinutes * 60));

    if ($result['provider_txn_id'] === '' || ($result['qr_payload'] === '' && $result['qr_image_url'] === '')) {
        $result['error'] = ($mensaje !== '') ? ('VeriPagos: ' . $mensaje) : 'El proveedor no devolvio datos de QR.';
        return $result;
    }

    $result['ok'] = true;
    return $result;
}

function programmit_finance_build_provider_headers($settings) {
    $headers = array();
    if (!is_array($settings)) {
        return $headers;
    }

    $authType = strtolower(programmit_finance_setting_value($settings, 'auth_type', 'bearer'));
    $apiKey = programmit_finance_setting_value($settings, 'api_key', '');
    $apiUser = programmit_finance_setting_value($settings, 'api_user', '');
    $apiPassword = programmit_finance_setting_value($settings, 'api_password', '');

    if ($authType === 'bearer' && $apiKey !== '') {
        if (stripos($apiKey, 'bearer ') === 0) {
            $headers[] = 'Authorization: '.$apiKey;
        } else {
            $headers[] = 'Authorization: Bearer '.$apiKey;
        }
    } elseif ($authType === 'basic' && $apiUser !== '') {
        $headers[] = 'Authorization: Basic '.base64_encode($apiUser.':'.$apiPassword);
    } elseif ($authType === 'apikey' && $apiKey !== '') {
        $headers[] = 'X-API-KEY: '.$apiKey;
    }

    $extraHeaders = programmit_finance_setting_value($settings, 'extra_headers', '');
    if ($extraHeaders !== '') {
        $lines = preg_split('/\r\n|\r|\n/', $extraHeaders);
        if (is_array($lines)) {
            foreach ($lines as $line) {
                $line = trim((string)$line);
                if ($line !== '' && strpos($line, ':') !== false) {
                    $headers[] = $line;
                }
            }
        }
    }

    return $headers;
}

function programmit_finance_seed_methods($db) {
    $defaultRate = programmit_finance_get_default_rate_bob($db);
    $defaults = array(
        array(
            'method_key' => 'qr_bolivia_auto',
            'provider_key' => 'veripagos_qr',
            'method_name' => 'QR Bolivia [Automatico]',
            'display_order' => 10,
            'min_amount' => 1.00,
            'max_amount' => 1000.00,
            'fee_fixed' => 0.00,
            'fee_percent' => 0.00,
            'rate_bob' => $defaultRate,
            'allow_new_users' => 1,
            'is_active' => 1,
            'settings_json' => array(
                'create_url' => '',
                'auth_type' => 'bearer',
                'api_key' => '',
                'api_user' => '',
                'api_password' => '',
                'secret' => '',
                'description' => 'Pago con QR automatico',
                'qr_image_path' => '',
                'qr_payload_path' => '',
                'txn_path' => '',
                'expires_path' => '',
                'extra_headers' => '',
                'credit_price_usd' => '',
                'instructions' => 'Escanea el QR y espera la confirmacion automatica.'
            )
        ),
        array(
            'method_key' => 'manual_transfer',
            'provider_key' => 'manual',
            'method_name' => 'Transferencia Manual',
            'display_order' => 90,
            'min_amount' => 1.00,
            'max_amount' => 1000.00,
            'fee_fixed' => 0.00,
            'fee_percent' => 0.00,
            'rate_bob' => $defaultRate,
            'allow_new_users' => 1,
            'is_active' => 1,
            'settings_json' => array(
                'description' => 'Transferencia validada manualmente',
                'credit_price_usd' => '',
                'instructions' => 'Realiza el pago y envia comprobante a soporte para aprobacion.'
            )
        )
    );

    $qry = $db->sql_query("SELECT method_key FROM finance_payment_methods");
    $exists = array();
    while ($row = $db->sql_fetchrow($qry)) {
        if ($row && isset($row['method_key'])) {
            $exists[(string)$row['method_key']] = true;
        }
    }

    foreach ($defaults as $m) {
        if (isset($exists[$m['method_key']])) {
            continue;
        }

        $db->sql_query("INSERT INTO finance_payment_methods
            (method_key, provider_key, method_name, display_order, min_amount, max_amount, fee_fixed, fee_percent, rate_bob, allow_new_users, is_active, settings_json, created_at, updated_at)
            VALUES
            ('".$db->SanitizeForSQL($m['method_key'])."',
             '".$db->SanitizeForSQL($m['provider_key'])."',
             '".$db->SanitizeForSQL($m['method_name'])."',
             '".$db->SanitizeForSQL($m['display_order'])."',
             '".$db->SanitizeForSQL(number_format((float)$m['min_amount'], 2, '.', ''))."',
             '".$db->SanitizeForSQL(number_format((float)$m['max_amount'], 2, '.', ''))."',
             '".$db->SanitizeForSQL(number_format((float)$m['fee_fixed'], 2, '.', ''))."',
             '".$db->SanitizeForSQL(number_format((float)$m['fee_percent'], 2, '.', ''))."',
             '".$db->SanitizeForSQL(number_format((float)$m['rate_bob'], 4, '.', ''))."',
             '".$db->SanitizeForSQL((int)$m['allow_new_users'])."',
             '".$db->SanitizeForSQL((int)$m['is_active'])."',
             '".$db->SanitizeForSQL(programmit_finance_json_encode($m['settings_json']))."',
             NOW(),
             NOW())");
    }
}

function programmit_finance_ensure_tables($db) {
    static $ready = false;
    if ($ready) {
        return true;
    }

    $ttlSeconds = 86400;
    $stampFile = programmit_finance_schema_stamp_path();
    clearstatcache(true, $stampFile);
    if (is_file($stampFile) && (time() - (int)@filemtime($stampFile)) < $ttlSeconds) {
        $ready = true;
        return true;
    }

    $db->sql_query(programmit_finance_methods_table_sql());
    $db->sql_query(programmit_finance_recharges_table_sql());
    $db->sql_query(programmit_finance_wallet_logs_table_sql());
    $db->sql_query(programmit_finance_settings_table_sql());

    // Migration: old schema used VARCHAR(500) for qr_image_url and truncates base64/data URLs.
    $colQry = $db->sql_query("SHOW COLUMNS FROM finance_recharges LIKE 'qr_image_url'");
    $colRow = $db->sql_fetchrow($colQry);
    if ($colRow && isset($colRow['Type'])) {
        $qrType = strtolower(trim((string)$colRow['Type']));
        if (strpos($qrType, 'varchar(') === 0) {
            $db->sql_query("ALTER TABLE finance_recharges MODIFY qr_image_url MEDIUMTEXT NULL");
        }
    }

    $currentRate = trim(programmit_finance_get_setting($db, 'usd_bob_default_rate', ''));
    if ($currentRate === '') {
        programmit_finance_set_setting($db, 'usd_bob_default_rate', '6.95');
    }
    $currentCreditPrice = trim(programmit_finance_get_setting($db, 'credit_price_usd', ''));
    if ($currentCreditPrice === '') {
        programmit_finance_set_setting($db, 'credit_price_usd', '1.00');
    }
    $currentMasterHost = trim(programmit_finance_get_setting($db, 'finance_master_host', ''));
    if ($currentMasterHost === '') {
        programmit_finance_set_setting($db, 'finance_master_host', 'panel.programmit.com');
    }
    programmit_finance_seed_methods($db);

    @touch($stampFile);
    $ready = true;
    return true;
}

function programmit_finance_list_methods($db, $activeOnly = true) {
    programmit_finance_ensure_tables($db);
    $where = $activeOnly ? "WHERE is_active=1" : "";
    $qry = $db->sql_query("SELECT id, method_key, provider_key, method_name, display_order,
        min_amount, max_amount, fee_fixed, fee_percent, rate_bob,
        allow_new_users, is_active, settings_json, created_at, updated_at
        FROM finance_payment_methods
        ".$where."
        ORDER BY display_order ASC, id ASC");

    $rows = array();
    while ($row = $db->sql_fetchrow($qry)) {
        if (!$row) {
            continue;
        }
        $row['id'] = (int)$row['id'];
        $row['min_amount'] = programmit_finance_safe_float($row['min_amount']);
        $row['max_amount'] = programmit_finance_safe_float($row['max_amount']);
        $row['fee_fixed'] = programmit_finance_safe_float($row['fee_fixed']);
        $row['fee_percent'] = programmit_finance_safe_float($row['fee_percent']);
        $row['rate_bob'] = programmit_finance_safe_float($row['rate_bob']);
        $row['allow_new_users'] = (int)$row['allow_new_users'];
        $row['is_active'] = (int)$row['is_active'];
        $row['settings'] = programmit_finance_json_decode($row['settings_json']);
        $rows[] = $row;
    }
    return $rows;
}

function programmit_finance_get_method($db, $methodId) {
    $methodId = (int)$methodId;
    if ($methodId <= 0) {
        return null;
    }

    programmit_finance_ensure_tables($db);
    $qry = $db->sql_query("SELECT id, method_key, provider_key, method_name, display_order,
        min_amount, max_amount, fee_fixed, fee_percent, rate_bob,
        allow_new_users, is_active, settings_json, created_at, updated_at
        FROM finance_payment_methods
        WHERE id='".$db->SanitizeForSQL($methodId)."'
        LIMIT 1");
    $row = $db->sql_fetchrow($qry);
    if (!$row) {
        return null;
    }
    $row['id'] = (int)$row['id'];
    $row['min_amount'] = programmit_finance_safe_float($row['min_amount']);
    $row['max_amount'] = programmit_finance_safe_float($row['max_amount']);
    $row['fee_fixed'] = programmit_finance_safe_float($row['fee_fixed']);
    $row['fee_percent'] = programmit_finance_safe_float($row['fee_percent']);
    $row['rate_bob'] = programmit_finance_safe_float($row['rate_bob']);
    $row['allow_new_users'] = (int)$row['allow_new_users'];
    $row['is_active'] = (int)$row['is_active'];
    $row['settings'] = programmit_finance_json_decode($row['settings_json']);
    return $row;
}

function programmit_finance_calculate_totals($amountUsd, $method) {
    $amountUsd = programmit_finance_safe_float($amountUsd);
    if ($amountUsd < 0) {
        $amountUsd = 0.0;
    }
    $feeFixed = isset($method['fee_fixed']) ? programmit_finance_safe_float($method['fee_fixed']) : 0.0;
    $feePercent = isset($method['fee_percent']) ? programmit_finance_safe_float($method['fee_percent']) : 0.0;
    $rateBob = isset($method['rate_bob']) ? programmit_finance_safe_float($method['rate_bob']) : 0.0;

    $feeUsd = $feeFixed + (($feePercent / 100) * $amountUsd);
    $totalUsd = $amountUsd + $feeUsd;
    $totalBob = ($rateBob > 0) ? ($totalUsd * $rateBob) : 0.0;

    return array(
        'amount_usd' => round($amountUsd, 2),
        'fee_usd' => round($feeUsd, 2),
        'total_usd' => round($totalUsd, 2),
        'rate_bob' => round($rateBob, 4),
        'total_bob' => round($totalBob, 2)
    );
}

function programmit_finance_generate_reference() {
    return 'RCG'.gmdate('YmdHis').strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 8));
}

function programmit_finance_http_json_post($url, $payload, $headers = array()) {
    $url = trim((string)$url);
    if ($url === '') {
        return array('ok' => false, 'status' => 0, 'body' => '', 'json' => array(), 'error' => 'empty_url');
    }
    $rawPayload = json_encode($payload);
    if (!is_string($rawPayload)) {
        $rawPayload = '{}';
    }

    $httpHeaders = array(
        'Content-Type: application/json',
        'Accept: application/json',
        'User-Agent: ProgrammitFinance/1.0'
    );
    if (is_array($headers)) {
        foreach ($headers as $h) {
            $h = trim((string)$h);
            if ($h !== '') {
                $httpHeaders[] = $h;
            }
        }
    }

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 12);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $rawPayload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);

        $body = curl_exec($ch);
        $err = curl_error($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $json = programmit_finance_json_decode((string)$body);
        return array(
            'ok' => ($err === '' && $status >= 200 && $status < 300),
            'status' => $status,
            'body' => is_string($body) ? $body : '',
            'json' => $json,
            'error' => $err
        );
    }

    return array('ok' => false, 'status' => 0, 'body' => '', 'json' => array(), 'error' => 'curl_not_available');
}

function programmit_finance_create_provider_qr($method, $recharge) {
    $providerKey = strtolower(trim((string)$method['provider_key']));
    $settings = isset($method['settings']) && is_array($method['settings']) ? $method['settings'] : array();

    $result = array(
        'ok' => true,
        'provider_txn_id' => '',
        'qr_image_url' => '',
        'qr_payload' => '',
        'expires_at' => '',
        'request_raw' => '',
        'response_raw' => '',
        'error' => ''
    );

    if ($providerKey === 'manual') {
        if (isset($settings['instructions']) && trim((string)$settings['instructions']) !== '') {
            $result['qr_payload'] = trim((string)$settings['instructions']);
        }
        return $result;
    }

    if (programmit_finance_is_qr_bolivia_method($method, $settings)) {
        $qrProvider = programmit_finance_resolve_qr_provider($method, $settings);
        if ($qrProvider === 'bnb') {
            return programmit_finance_create_bnb_provider_qr($method, $recharge, $settings);
        }
        if ($qrProvider === 'veripagos') {
            return programmit_finance_create_veripagos_provider_qr($method, $recharge, $settings);
        }
    }

    $createUrl = programmit_finance_setting_value($settings, 'create_url', '');
    if ($createUrl === '') {
        if (isset($settings['instructions']) && trim((string)$settings['instructions']) !== '') {
            $result['qr_payload'] = trim((string)$settings['instructions']);
            return $result;
        }
        $result['ok'] = false;
        $result['error'] = 'Metodo sin create_url configurado.';
        return $result;
    }

    $headers = programmit_finance_build_provider_headers($settings);

    $payload = array(
        'reference' => (string)$recharge['recharge_ref'],
        'amount_usd' => programmit_finance_safe_float($recharge['amount_usd']),
        'amount_bob' => programmit_finance_safe_float($recharge['total_bob']),
        'currency' => 'BOB',
        'user_id' => (int)$recharge['user_id']
    );
    $result['request_raw'] = programmit_finance_json_encode($payload);

    $http = programmit_finance_http_json_post($createUrl, $payload, $headers);
    $result['response_raw'] = isset($http['body']) ? (string)$http['body'] : '';
    if (!$http['ok']) {
        $result['ok'] = false;
        $result['error'] = 'No se pudo crear el QR automatico.';
        if (!empty($http['error'])) {
            $result['error'] .= ' '.$http['error'];
        }
        return $result;
    }

    $json = isset($http['json']) && is_array($http['json']) ? $http['json'] : array();
    $txnPath = programmit_finance_setting_value($settings, 'txn_path', '');
    $qrImagePath = programmit_finance_setting_value($settings, 'qr_image_path', '');
    $qrPayloadPath = programmit_finance_setting_value($settings, 'qr_payload_path', '');
    $expiresPath = programmit_finance_setting_value($settings, 'expires_path', '');

    $result['provider_txn_id'] = ($txnPath !== '') ? programmit_finance_array_get_path($json, $txnPath) : '';
    $result['qr_image_url'] = ($qrImagePath !== '') ? programmit_finance_array_get_path($json, $qrImagePath) : '';
    $result['qr_payload'] = ($qrPayloadPath !== '') ? programmit_finance_array_get_path($json, $qrPayloadPath) : '';
    $result['expires_at'] = ($expiresPath !== '') ? programmit_finance_array_get_path($json, $expiresPath) : '';

    if ($result['provider_txn_id'] === '') {
        $result['provider_txn_id'] = isset($json['payment_id']) ? (string)$json['payment_id'] : (isset($json['id']) ? (string)$json['id'] : '');
    }
    if ($result['qr_image_url'] === '') {
        $result['qr_image_url'] = isset($json['qr_image']) ? (string)$json['qr_image'] : (isset($json['qr_url']) ? (string)$json['qr_url'] : '');
    }
    if ($result['qr_payload'] === '') {
        $result['qr_payload'] = isset($json['qr_text']) ? (string)$json['qr_text'] : (isset($json['qr_data']) ? (string)$json['qr_data'] : '');
    }
    if ($result['expires_at'] === '') {
        $result['expires_at'] = isset($json['expires_at']) ? (string)$json['expires_at'] : '';
    }

    // Auto-detection fallback (helps providers like Veripagos: Data.qr, Data.movimiento_id)
    if ($result['provider_txn_id'] === '') {
        $result['provider_txn_id'] = programmit_finance_array_get_first_path($json, array(
            'Data.movimiento_id',
            'data.movimiento_id',
            'Data.payment_id',
            'data.payment_id',
            'Data.id',
            'data.id',
            'movimiento_id',
            'payment_id',
            'id'
        ));
    }
    if ($result['qr_image_url'] === '') {
        $result['qr_image_url'] = programmit_finance_array_get_first_path($json, array(
            'Data.qr_image',
            'data.qr_image',
            'Data.qr_url',
            'data.qr_url',
            'Data.qrImage',
            'data.qrImage',
            'qr_image',
            'qr_url',
            'qrImage'
        ));
    }
    if ($result['qr_payload'] === '') {
        $result['qr_payload'] = programmit_finance_array_get_first_path($json, array(
            'Data.qr',
            'data.qr',
            'Data.qr_text',
            'data.qr_text',
            'Data.qr_data',
            'data.qr_data',
            'Data.qrString',
            'data.qrString',
            'qr',
            'qr_text',
            'qr_data',
            'qrString'
        ));
    }
    if ($result['expires_at'] === '') {
        $result['expires_at'] = programmit_finance_array_get_first_path($json, array(
            'Data.expires_at',
            'data.expires_at',
            'Data.expire_at',
            'data.expire_at',
            'Data.expiration',
            'data.expiration',
            'expires_at',
            'expire_at',
            'expiration'
        ));
    }

    $normalizedQrImage = programmit_finance_normalize_qr_image($result['qr_image_url']);
    if ($normalizedQrImage !== '') {
        $result['qr_image_url'] = $normalizedQrImage;
    } elseif ($result['qr_image_url'] === '' && $result['qr_payload'] !== '') {
        $fromPayload = programmit_finance_normalize_qr_image($result['qr_payload']);
        if ($fromPayload !== '') {
            $result['qr_image_url'] = $fromPayload;
        }
    }

    if ($result['qr_image_url'] === '' && $result['qr_payload'] === '') {
        $result['ok'] = false;
        $result['error'] = 'El proveedor no devolvio datos de QR.';
        return $result;
    }

    return $result;
}

function programmit_finance_create_recharge($db, $userId, $methodId, $amountUsd) {
    $userId = (int)$userId;
    $methodId = (int)$methodId;
    $amountUsd = programmit_finance_safe_float($amountUsd);
    if ($userId <= 0 || $methodId <= 0) {
        return array('ok' => false, 'error' => 'Solicitud invalida.');
    }

    programmit_finance_ensure_tables($db);
    $method = programmit_finance_get_method($db, $methodId);
    if (!$method || (int)$method['is_active'] !== 1) {
        return array('ok' => false, 'error' => 'Metodo de pago no disponible.');
    }
    if ((float)$method['rate_bob'] <= 0) {
        $method['rate_bob'] = programmit_finance_get_default_rate_bob($db);
    }

    if ($amountUsd < (float)$method['min_amount'] || $amountUsd > (float)$method['max_amount']) {
        return array(
            'ok' => false,
            'error' => 'Monto fuera de rango. Min '.number_format((float)$method['min_amount'], 2).' / Max '.number_format((float)$method['max_amount'], 2)
        );
    }

    $calc = programmit_finance_calculate_totals($amountUsd, $method);
    $creditPriceUsd = programmit_finance_effective_credit_price($db, $method);
    $creditsToAdd = programmit_finance_compute_credits_to_add($calc['amount_usd'], $creditPriceUsd);
    if ($creditsToAdd <= 0) {
        return array(
            'ok' => false,
            'error' => 'Monto insuficiente. Con tu configuracion actual, 1 credito cuesta $'.number_format($creditPriceUsd, 2).'.'
        );
    }

    $ref = programmit_finance_generate_reference();
    $ip = method_exists($db, 'get_client_ip') ? $db->get_client_ip() : '';

    $tenantId = 0;
    $ownerUserId = 0;
    if (function_exists('programmit_saas_get_user_scope')) {
        $scope = programmit_saas_get_user_scope($db, $userId);
        $tenantId = isset($scope['tenant_id']) ? (int)$scope['tenant_id'] : 0;
        $ownerUserId = isset($scope['owner_user_id']) ? (int)$scope['owner_user_id'] : 0;
    }

    $insertOk = $db->sql_query("INSERT INTO finance_recharges
        (recharge_ref, user_id, tenant_id, owner_user_id, method_id, method_key, method_name, amount_usd, fee_usd, total_usd, rate_bob, total_bob, credits_to_add, status, created_ip, created_at, expires_at, updated_at)
        VALUES
        ('".$db->SanitizeForSQL($ref)."',
         '".$db->SanitizeForSQL($userId)."',
         '".$db->SanitizeForSQL($tenantId)."',
         '".$db->SanitizeForSQL($ownerUserId)."',
         '".$db->SanitizeForSQL($method['id'])."',
         '".$db->SanitizeForSQL($method['method_key'])."',
         '".$db->SanitizeForSQL($method['method_name'])."',
         '".$db->SanitizeForSQL(number_format($calc['amount_usd'], 2, '.', ''))."',
         '".$db->SanitizeForSQL(number_format($calc['fee_usd'], 2, '.', ''))."',
         '".$db->SanitizeForSQL(number_format($calc['total_usd'], 2, '.', ''))."',
         '".$db->SanitizeForSQL(number_format($calc['rate_bob'], 4, '.', ''))."',
         '".$db->SanitizeForSQL(number_format($calc['total_bob'], 2, '.', ''))."',
         '".$db->SanitizeForSQL($creditsToAdd)."',
         'pending',
         '".$db->SanitizeForSQL($ip)."',
         NOW(),
         DATE_ADD(NOW(), INTERVAL 25 MINUTE),
         NOW())");
    if (!$insertOk) {
        return array('ok' => false, 'error' => 'No se pudo crear la recarga.');
    }

    $rechargeId = (int)$db->sql_nextid();
    if ($rechargeId <= 0) {
        return array('ok' => false, 'error' => 'No se pudo obtener ID de recarga.');
    }

    $recharge = programmit_finance_get_recharge($db, $rechargeId, $userId);
    $providerRes = programmit_finance_create_provider_qr($method, $recharge);

    $expiresSql = '';
    if (!empty($providerRes['expires_at'])) {
        $expiresRaw = (string)$providerRes['expires_at'];
        $expiresTs = strtotime($expiresRaw);
        if ($expiresTs !== false) {
            $expiresValue = date('Y-m-d H:i:s', $expiresTs);
            if (preg_match('/(?:Z|UTC|[+\-]\d{2}:\d{2}|[+\-]\d{4})$/i', $expiresRaw)) {
                $expiresValue = gmdate('Y-m-d H:i:s', $expiresTs);
            }
            $expiresSql = ", expires_at='".$db->SanitizeForSQL($expiresValue)."'";
        }
    }

    $db->sql_query("UPDATE finance_recharges
        SET provider_txn_id='".$db->SanitizeForSQL((string)$providerRes['provider_txn_id'])."',
            qr_payload='".$db->SanitizeForSQL((string)$providerRes['qr_payload'])."',
            qr_image_url='".$db->SanitizeForSQL((string)$providerRes['qr_image_url'])."',
            provider_request='".$db->SanitizeForSQL((string)$providerRes['request_raw'])."',
            provider_response='".$db->SanitizeForSQL((string)$providerRes['response_raw'])."',
            updated_at=NOW()
            ".$expiresSql."
        WHERE id='".$db->SanitizeForSQL($rechargeId)."'");

    if (!$providerRes['ok']) {
        return array(
            'ok' => false,
            'error' => (string)$providerRes['error'],
            'id' => $rechargeId,
            'ref' => $ref
        );
    }

    return array(
        'ok' => true,
        'id' => $rechargeId,
        'ref' => $ref
    );
}

function programmit_finance_get_recharge($db, $rechargeId, $userId = 0) {
    $rechargeId = (int)$rechargeId;
    $userId = (int)$userId;
    if ($rechargeId <= 0) {
        return null;
    }

    programmit_finance_ensure_tables($db);
    $whereUser = ($userId > 0) ? " AND user_id='".$db->SanitizeForSQL($userId)."'" : "";
    $qry = $db->sql_query("SELECT *
        FROM finance_recharges
        WHERE id='".$db->SanitizeForSQL($rechargeId)."'
        ".$whereUser."
        LIMIT 1");
    $row = $db->sql_fetchrow($qry);
    if (!$row) {
        return null;
    }
    return $row;
}

function programmit_finance_list_user_recharges($db, $userId, $limit = 100) {
    $userId = (int)$userId;
    $limit = (int)$limit;
    if ($userId <= 0) {
        return array();
    }
    if ($limit <= 0) {
        $limit = 100;
    }
    if ($limit > 500) {
        $limit = 500;
    }

    programmit_finance_ensure_tables($db);
    $qry = $db->sql_query("SELECT *
        FROM finance_recharges
        WHERE user_id='".$db->SanitizeForSQL($userId)."'
        ORDER BY id DESC
        LIMIT ".$db->SanitizeForSQL($limit));

    $rows = array();
    while ($row = $db->sql_fetchrow($qry)) {
        if ($row) {
            $rows[] = $row;
        }
    }
    return $rows;
}

function programmit_finance_mark_recharge_paid($db, $rechargeId, $providerTxn = '', $description = 'Recarga de saldo acreditada', $createdBy = 0) {
    $rechargeId = (int)$rechargeId;
    $createdBy = (int)$createdBy;
    if ($rechargeId <= 0) {
        return array('ok' => false, 'error' => 'Recarga invalida.');
    }

    programmit_finance_ensure_tables($db);
    $recharge = programmit_finance_get_recharge($db, $rechargeId, 0);
    if (!$recharge) {
        return array('ok' => false, 'error' => 'Recarga no encontrada.');
    }

    if ((string)$recharge['status'] === 'paid') {
        return array('ok' => true, 'already_paid' => true);
    }

    $userId = (int)$recharge['user_id'];
    $creditsToAdd = (int)$recharge['credits_to_add'];
    if ($userId <= 0 || $creditsToAdd <= 0) {
        return array('ok' => false, 'error' => 'Datos de recarga invalidos.');
    }

    $userQry = $db->sql_query("SELECT credits FROM users WHERE user_id='".$db->SanitizeForSQL($userId)."' LIMIT 1");
    $userRow = $db->sql_fetchrow($userQry);
    if (!$userRow) {
        return array('ok' => false, 'error' => 'Usuario no encontrado.');
    }

    $before = (int)$userRow['credits'];
    $after = $before + $creditsToAdd;

    $db->sql_query("UPDATE users
        SET credits='".$db->SanitizeForSQL($after)."'
        WHERE user_id='".$db->SanitizeForSQL($userId)."'
        LIMIT 1");

    $tenantId = isset($recharge['tenant_id']) ? (int)$recharge['tenant_id'] : 0;

    $db->sql_query("INSERT INTO finance_wallet_logs
        (user_id, tenant_id, recharge_id, log_type, amount_before, amount_change, amount_after, description, created_by, created_at)
        VALUES
        ('".$db->SanitizeForSQL($userId)."',
         '".$db->SanitizeForSQL($tenantId)."',
         '".$db->SanitizeForSQL($rechargeId)."',
         'recharge',
         '".$db->SanitizeForSQL($before)."',
         '".$db->SanitizeForSQL($creditsToAdd)."',
         '".$db->SanitizeForSQL($after)."',
         '".$db->SanitizeForSQL($description)."',
         '".$db->SanitizeForSQL($createdBy)."',
         NOW())");

    $db->sql_query("UPDATE finance_recharges
        SET status='paid',
            provider_txn_id='".$db->SanitizeForSQL((string)$providerTxn)."',
            paid_at=NOW(),
            updated_at=NOW()
        WHERE id='".$db->SanitizeForSQL($rechargeId)."'
        LIMIT 1");

    return array('ok' => true, 'credits_before' => $before, 'credits_after' => $after);
}
