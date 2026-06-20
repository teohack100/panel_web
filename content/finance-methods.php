<?php
chkSession();

if (!function_exists('programmit_finance_upload_method_icon')) {
    function programmit_finance_upload_method_icon($fieldName, $targetDir, $prefix, &$errorOut) {
        $errorOut = '';
        if (!isset($_FILES[$fieldName]) || !is_array($_FILES[$fieldName])) {
            return '';
        }
        $file = $_FILES[$fieldName];
        $errorCode = isset($file['error']) ? (int)$file['error'] : UPLOAD_ERR_NO_FILE;
        if ($errorCode === UPLOAD_ERR_NO_FILE) {
            return '';
        }
        if ($errorCode !== UPLOAD_ERR_OK) {
            $errorOut = 'No se pudo subir el icono.';
            return '';
        }
        $tmpName = isset($file['tmp_name']) ? (string)$file['tmp_name'] : '';
        $name = isset($file['name']) ? (string)$file['name'] : '';
        $size = isset($file['size']) ? (int)$file['size'] : 0;
        if ($tmpName === '' || !is_uploaded_file($tmpName)) {
            $errorOut = 'Archivo temporal invalido para el icono.';
            return '';
        }
        if ($size <= 0 || $size > (4 * 1024 * 1024)) {
            $errorOut = 'El icono supera el tamano permitido.';
            return '';
        }
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $allowed = array('png', 'jpg', 'jpeg', 'webp', 'gif');
        if ($ext === '' || !in_array($ext, $allowed, true)) {
            $errorOut = 'Formato de icono no permitido.';
            return '';
        }
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0775, true);
        }
        if (!is_dir($targetDir)) {
            $errorOut = 'No se pudo crear el directorio de iconos.';
            return '';
        }
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '', pathinfo($name, PATHINFO_FILENAME));
        if ($safeName === '') {
            $safeName = 'metodo';
        }
        $finalName = $prefix . '_' . date('Ymd_His') . '_' . substr(sha1($safeName . microtime(true)), 0, 10) . '.' . $ext;
        $targetPath = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $finalName;
        if (!@move_uploaded_file($tmpName, $targetPath)) {
            $errorOut = 'No se pudo guardar el icono.';
            return '';
        }
        @chmod($targetPath, 0644);
        return 'logo/metodos/' . $finalName;
    }
}

$can_manage_finance_methods = (
    (int)$user_id_2 === 1 ||
    $user_level_2 === 'superadmin' ||
    $user_level_2 === 'administrator' ||
    $user_level_2 === 'subadmin'
);

if (!$can_manage_finance_methods) {
    header("Location: ".$db->base_url()."index.php?p=dashboard");
    exit;
}

programmit_finance_ensure_tables($db);
$masterHost = programmit_finance_get_master_host($db);
if (!programmit_finance_can_edit_from_current_host($db)) {
    header("Location: https://" . $masterHost . "/index.php?p=finance-methods");
    exit;
}

$method_error = '';
$method_success = '';
$finance_current_host = programmit_finance_current_host();
$finance_master_host = programmit_finance_get_master_host($db);
$finance_methods_locked = programmit_finance_can_edit_from_current_host($db) ? 0 : 1;

$active_tab = isset($_GET['tab']) ? strtolower(trim((string)$_GET['tab'])) : 'methods';
if (!in_array($active_tab, array('general', 'providers', 'methods'), true)) {
    $active_tab = 'methods';
}
$embed_raw = isset($_GET['embed']) ? strtolower(trim((string)$_GET['embed'])) : '';
$finance_embed_admin = in_array($embed_raw, array('1', 'admin', 'yes'), true);
$finance_embed_qs = $finance_embed_admin ? '&embed=admin' : '';
if ($finance_embed_admin) {
    // Embedded inside admin.php: force methods tab to avoid full standalone view.
    $active_tab = 'methods';
}

if (isset($_SESSION['finance_method_success_flash'])) {
    $method_success = (string)$_SESSION['finance_method_success_flash'];
    unset($_SESSION['finance_method_success_flash']);
}
if (isset($_SESSION['finance_method_error_flash'])) {
    $method_error = (string)$_SESSION['finance_method_error_flash'];
    unset($_SESSION['finance_method_error_flash']);
}

$provider_options = array(
    array('key' => 'veripagos_qr', 'name' => 'Veripagos QR'),
    array('key' => 'bnb_qr', 'name' => 'BNB QR'),
    array('key' => 'custom_qr', 'name' => 'Custom QR API'),
    array('key' => 'paypal', 'name' => 'PayPal'),
    array('key' => 'stripe', 'name' => 'Stripe'),
    array('key' => 'mercadopago', 'name' => 'MercadoPago'),
    array('key' => 'binance_pay', 'name' => 'Binance Pay'),
    array('key' => 'binance_gateway', 'name' => 'Binance Pay Gateway'),
    array('key' => 'binance_usdt', 'name' => 'Binance Pay (USDT)'),
    array('key' => 'yape', 'name' => 'Yape'),
    array('key' => 'cryptomus', 'name' => 'Cryptomus'),
    array('key' => 'hotmart', 'name' => 'Hotmart Checkout'),
    array('key' => 'manual', 'name' => 'Manual Payment')
);

$method_presets = array(
    array('method_key' => 'qr_bolivia_auto', 'provider_key' => 'veripagos_qr', 'method_name' => 'QR Bolivia Auto (Multi-banco)'),
    array('method_key' => 'veripagos', 'provider_key' => 'veripagos_qr', 'method_name' => 'Veripagos QR'),
    array('method_key' => 'stripe', 'provider_key' => 'stripe', 'method_name' => 'Stripe Checkout'),
    array('method_key' => 'paypal', 'provider_key' => 'paypal', 'method_name' => 'PayPal Express Checkout'),
    array('method_key' => 'mercadopago', 'provider_key' => 'mercadopago', 'method_name' => 'MercadoPago'),
    array('method_key' => 'binance_pay', 'provider_key' => 'binance_pay', 'method_name' => 'Binance Pay'),
    array('method_key' => 'binance_gateway', 'provider_key' => 'binance_gateway', 'method_name' => 'Binance Pay Gateway'),
    array('method_key' => 'binance_usdt', 'provider_key' => 'binance_usdt', 'method_name' => 'Binance Pay (USDT)'),
    array('method_key' => 'yape', 'provider_key' => 'yape', 'method_name' => 'Yape'),
    array('method_key' => 'cryptomus', 'provider_key' => 'cryptomus', 'method_name' => 'Cryptomus'),
    array('method_key' => 'hotmart', 'provider_key' => 'hotmart', 'method_name' => 'Hotmart Checkout'),
    array('method_key' => 'manual_transfer', 'provider_key' => 'manual', 'method_name' => 'Manual Payment')
);
$method_preset_lookup = array();
foreach ($method_presets as $preset) {
    $presetKey = strtolower(trim((string)$preset['method_key']));
    if ($presetKey !== '') {
        $method_preset_lookup[$presetKey] = $preset;
    }
}
$bnb_default_urls = array(
    'token' => 'http://test.bnb.com.bo/ClientAuthentication.API/api/v1/auth/token',
    'qr' => 'http://test.bnb.com.bo/QRSimple.API/api/v1/main/getQRWithImageAsync',
    'status' => 'http://test.bnb.com.bo/QRSimple.API/api/v1/main/getQRStatusAsync',
    'cancel' => 'http://test.bnb.com.bo/QRSimple.API/api/v1/main/CancelQRByIdAsync',
);

$default_rate_bob = programmit_finance_get_default_rate_bob($db);
$credit_price_usd = programmit_finance_get_credit_price_usd($db);
$finance_master_host = programmit_finance_get_master_host($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['preview_method']) && (string)$_POST['preview_method'] === '1') {
    header('Content-Type: application/json; charset=utf-8');

    $preview_response = array(
        'ok' => false,
        'message' => 'No se pudo probar el metodo.',
        'txn_id' => '',
        'expires_at' => '',
        'has_qr' => 0,
        'qr_mode' => '',
        'qr_image_url' => '',
        'qr_payload' => ''
    );

    if ($finance_methods_locked === 1) {
        $preview_response['message'] = 'Edicion bloqueada en este subdominio.';
        echo json_encode($preview_response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    $method_id = isset($_POST['method_id']) ? (int)$_POST['method_id'] : 0;
    $method_preset = isset($_POST['method_preset']) ? strtolower(trim((string)$_POST['method_preset'])) : '';
    $method_key = strtolower(trim((string)($_POST['method_key'] ?? '')));
    $provider_key = strtolower(trim((string)($_POST['provider_key'] ?? '')));
    $method_name = trim((string)($_POST['method_name'] ?? ''));
    $description = trim((string)($_POST['description'] ?? ''));
    $min_amount = isset($_POST['min_amount']) ? (float)$_POST['min_amount'] : 1;
    $max_amount = isset($_POST['max_amount']) ? (float)$_POST['max_amount'] : 1000;
    $fee_fixed = isset($_POST['fee_fixed']) ? (float)$_POST['fee_fixed'] : 0;
    $fee_percent = isset($_POST['fee_percent']) ? (float)$_POST['fee_percent'] : 0;
    $rate_bob = isset($_POST['rate_bob']) ? (float)$_POST['rate_bob'] : 0;

    $create_url = trim((string)($_POST['create_url'] ?? ''));
    $auth_type = strtolower(trim((string)($_POST['auth_type'] ?? '')));
    $api_key = trim((string)($_POST['api_key'] ?? ''));
    $api_user = trim((string)($_POST['api_user'] ?? ''));
    $api_password = trim((string)($_POST['api_password'] ?? ''));
    $secret = trim((string)($_POST['secret'] ?? ''));
    $txn_path = trim((string)($_POST['txn_path'] ?? ''));
    $qr_image_path = trim((string)($_POST['qr_image_path'] ?? ''));
    $qr_payload_path = trim((string)($_POST['qr_payload_path'] ?? ''));
    $expires_path = trim((string)($_POST['expires_path'] ?? ''));
    $extra_headers = trim((string)($_POST['extra_headers'] ?? ''));
    $instructions = trim((string)($_POST['instructions'] ?? ''));
    $instruction_legacy = trim((string)($_POST['instruction'] ?? ''));
    $credit_price_method_raw = trim((string)($_POST['credit_price_usd'] ?? ''));
    $credit_price_method = ($credit_price_method_raw !== '') ? (float)$credit_price_method_raw : 0;
    $qr_provider = strtolower(trim((string)($_POST['qr_provider'] ?? '')));
    if ($qr_provider === '') {
        $qr_provider = 'veripagos';
    }
    $qb_mode = strtolower(trim((string)($_POST['qb_mode'] ?? '')));
    if ($qb_mode !== 'prod' && $qb_mode !== 'test') {
        $qb_mode = 'test';
    }
    $qb_expiry_minutes = isset($_POST['qb_expiry_minutes']) ? (int)$_POST['qb_expiry_minutes'] : 15;
    if ($qb_expiry_minutes < 1 || $qb_expiry_minutes > 1440) {
        $qb_expiry_minutes = 15;
    }
    $vp_username = trim((string)($_POST['vp_username'] ?? ''));
    $vp_password = trim((string)($_POST['vp_password'] ?? ''));
    $vp_secret_key = trim((string)($_POST['vp_secret_key'] ?? ''));
    $vp_base_url = trim((string)($_POST['vp_base_url'] ?? ''));
    $bnb_account_id = trim((string)($_POST['bnb_account_id'] ?? ''));
    $bnb_authorization_id = trim((string)($_POST['bnb_authorization_id'] ?? ''));
    $bnb_destination_account_id = isset($_POST['bnb_destination_account_id']) ? (int)$_POST['bnb_destination_account_id'] : 1;
    if ($bnb_destination_account_id !== 1 && $bnb_destination_account_id !== 2) {
        $bnb_destination_account_id = 1;
    }
    $bnb_currency = strtoupper(trim((string)($_POST['bnb_currency'] ?? 'BOB')));
    if ($bnb_currency === '') {
        $bnb_currency = 'BOB';
    }
    $bnb_currency = substr($bnb_currency, 0, 3);
    $bnb_token_url = trim((string)($_POST['bnb_token_url'] ?? ''));
    $bnb_qr_url = trim((string)($_POST['bnb_qr_url'] ?? ''));
    $bnb_status_url = trim((string)($_POST['bnb_status_url'] ?? ''));
    $bnb_cancel_url = trim((string)($_POST['bnb_cancel_url'] ?? ''));
    $processing_fee = isset($_POST['processing_fee']) ? 1 : 0;

    if ($instructions === '' && $instruction_legacy !== '') {
        $instructions = $instruction_legacy;
    }

    if ($method_preset !== '' && isset($method_preset_lookup[$method_preset])) {
        $presetData = $method_preset_lookup[$method_preset];
        if ($method_key === '') {
            $method_key = strtolower(trim((string)$presetData['method_key']));
        }
        if ($provider_key === '') {
            $provider_key = strtolower(trim((string)$presetData['provider_key']));
        }
        if ($method_name === '') {
            $method_name = trim((string)$presetData['method_name']);
        }
    }

    if ($auth_type === '') {
        $auth_type = 'bearer';
    }

    $existing_method = null;
    $existing_settings = array();
    if ($method_id > 0) {
        $existing_method = programmit_finance_get_method($db, $method_id);
        if ($existing_method && isset($existing_method['settings']) && is_array($existing_method['settings'])) {
            $existing_settings = $existing_method['settings'];
        }
    }

    if ($secret === '' && isset($existing_settings['secret'])) {
        $secret = (string)$existing_settings['secret'];
    }
    if ($api_password === '' && isset($existing_settings['api_password'])) {
        $api_password = (string)$existing_settings['api_password'];
    }
    if ($vp_password === '' && isset($existing_settings['vp_password'])) {
        $vp_password = (string)$existing_settings['vp_password'];
    }
    if ($vp_secret_key === '') {
        if (isset($existing_settings['vp_secret_key'])) {
            $vp_secret_key = (string)$existing_settings['vp_secret_key'];
        } elseif (isset($existing_settings['secret'])) {
            $vp_secret_key = (string)$existing_settings['secret'];
        }
    }
    if ($bnb_authorization_id === '' && isset($existing_settings['bnb_authorization_id'])) {
        $bnb_authorization_id = (string)$existing_settings['bnb_authorization_id'];
    }
    if ($vp_username === '' && isset($existing_settings['vp_username'])) {
        $vp_username = (string)$existing_settings['vp_username'];
    }
    if ($vp_base_url === '' && isset($existing_settings['vp_base_url'])) {
        $vp_base_url = (string)$existing_settings['vp_base_url'];
    }

    if ($create_url === '') {
        if (isset($existing_settings['create_url'])) {
            $create_url = (string)$existing_settings['create_url'];
        } elseif ($vp_base_url !== '') {
            $create_url = $vp_base_url;
        }
    }

    if ($rate_bob <= 0) {
        $rate_bob = (float)$default_rate_bob;
    }

    $is_qr_bolivia_method = (
        $method_key === 'qr_bolivia_auto' ||
        $method_key === 'veripagos' ||
        $method_preset === 'qr_bolivia_auto' ||
        $method_preset === 'veripagos' ||
        $provider_key === 'bnb_qr' ||
        $provider_key === 'veripagos_qr'
    );
    if ($is_qr_bolivia_method) {
        if ($qr_provider === 'bnb') {
            $provider_key = 'bnb_qr';
        } elseif ($qr_provider === 'veripagos') {
            $provider_key = 'veripagos_qr';
        }
    }

    $is_veripagos_profile = (
        $method_key === 'veripagos' ||
        $method_preset === 'veripagos' ||
        $provider_key === 'veripagos_qr' ||
        ($is_qr_bolivia_method && $qr_provider === 'veripagos')
    );
    if ($is_veripagos_profile) {
        if ($create_url === '' && $vp_base_url !== '') {
            $create_url = $vp_base_url;
        }
        if ($api_user === '' && $vp_username !== '') {
            $api_user = $vp_username;
        }
        if ($api_password === '' && $vp_password !== '') {
            $api_password = $vp_password;
        }
        if ($api_key === '' && $vp_secret_key !== '') {
            $api_key = $vp_secret_key;
        }
        if ($secret === '' && $vp_secret_key !== '') {
            $secret = $vp_secret_key;
        }
        if ($vp_username === '' && $api_user !== '') {
            $vp_username = $api_user;
        }
        if ($vp_password === '' && $api_password !== '') {
            $vp_password = $api_password;
        }
        if ($vp_secret_key === '') {
            if ($secret !== '') {
                $vp_secret_key = $secret;
            } elseif ($api_key !== '') {
                $vp_secret_key = $api_key;
            }
        }
        if ($vp_base_url === '' && $create_url !== '') {
            $vp_base_url = $create_url;
        }
        if (!in_array($auth_type, array('none', 'bearer', 'basic', 'apikey'), true)) {
            $auth_type = 'basic';
        }
    }

    if ($method_key === '' || $provider_key === '') {
        $preview_response['message'] = 'Clave y proveedor del metodo son obligatorios para probar.';
        echo json_encode($preview_response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    if (!in_array($auth_type, array('none', 'bearer', 'basic', 'apikey'), true)) {
        $preview_response['message'] = 'Tipo de autenticacion API invalido.';
        echo json_encode($preview_response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    if ($is_qr_bolivia_method && $qr_provider === 'bnb' && $bnb_account_id === '') {
        $preview_response['message'] = 'BNB Account ID es obligatorio para la prueba.';
        echo json_encode($preview_response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    if ($is_qr_bolivia_method && $qr_provider === 'bnb' && $bnb_authorization_id === '') {
        $preview_response['message'] = 'BNB Authorization ID es obligatorio para la prueba.';
        echo json_encode($preview_response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    if ($is_qr_bolivia_method && $qr_provider === 'bnb' && (
        $bnb_token_url === '' || $bnb_qr_url === '' || $bnb_status_url === '' || $bnb_cancel_url === ''
    )) {
        $preview_response['message'] = 'Completa las URLs BNB para probar este metodo.';
        echo json_encode($preview_response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    if ($is_veripagos_profile && ($vp_username === '' || $vp_password === '' || $vp_secret_key === '')) {
        $preview_response['message'] = 'Completa usuario, contrasena y secret key de VeriPagos.';
        echo json_encode($preview_response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    if ($is_veripagos_profile && $vp_base_url === '' && $create_url === '') {
        $preview_response['message'] = 'Falta URL Base VeriPagos para la prueba.';
        echo json_encode($preview_response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    if (!$is_veripagos_profile && !($is_qr_bolivia_method && $qr_provider === 'bnb') && $provider_key !== 'manual' && trim($create_url) === '') {
        $preview_response['message'] = 'Falta URL de creacion para probar este metodo.';
        echo json_encode($preview_response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    $settings_preview = array(
        'description' => $description,
        'create_url' => $create_url,
        'auth_type' => $auth_type,
        'api_key' => $api_key,
        'api_user' => $api_user,
        'api_password' => $api_password,
        'secret' => $secret,
        'instruction' => $instructions,
        'instructions' => $instructions,
        'txn_path' => $txn_path,
        'qr_image_path' => $qr_image_path,
        'qr_payload_path' => $qr_payload_path,
        'expires_path' => $expires_path,
        'extra_headers' => $extra_headers,
        'credit_price_usd' => ($credit_price_method_raw !== '') ? number_format($credit_price_method, 4, '.', '') : '',
        'qr_provider' => $qr_provider,
        'qb_provider' => $qr_provider,
        'qb_mode' => $qb_mode,
        'qb_expiry_minutes' => $qb_expiry_minutes,
        'vp_username' => $vp_username,
        'vp_password' => $vp_password,
        'vp_secret_key' => $vp_secret_key,
        'vp_base_url' => $vp_base_url,
        'bnb_account_id' => $bnb_account_id,
        'bnb_authorization_id' => $bnb_authorization_id,
        'bnb_destination_account_id' => $bnb_destination_account_id,
        'bnb_currency' => $bnb_currency,
        'bnb_token_url' => $bnb_token_url,
        'bnb_qr_url' => $bnb_qr_url,
        'bnb_status_url' => $bnb_status_url,
        'bnb_cancel_url' => $bnb_cancel_url,
        'processing_fee' => $processing_fee,
        'fee_fixed' => number_format($fee_fixed, 2, '.', ''),
        'fee_percent' => number_format($fee_percent, 2, '.', '')
    );

    $method_preview = array(
        'method_key' => $method_key,
        'provider_key' => $provider_key,
        'method_name' => ($method_name !== '') ? $method_name : $method_key,
        'rate_bob' => $rate_bob,
        'fee_fixed' => $fee_fixed,
        'fee_percent' => $fee_percent,
        'settings' => $settings_preview
    );

    $test_amount = $min_amount > 0 ? $min_amount : 1.0;
    if ($max_amount > 0 && $test_amount > $max_amount) {
        $test_amount = $max_amount;
    }
    if ($test_amount <= 0) {
        $test_amount = 1.0;
    }

    $totals = programmit_finance_calculate_totals($test_amount, $method_preview);
    $recharge_preview = array(
        'id' => 0,
        'recharge_ref' => 'PREVIEW' . gmdate('YmdHis') . substr((string)mt_rand(), 0, 3),
        'amount_usd' => isset($totals['amount_usd']) ? (float)$totals['amount_usd'] : $test_amount,
        'total_bob' => isset($totals['total_bob']) ? (float)$totals['total_bob'] : 0.0,
        'user_id' => (int)$user_id_2,
        'method_key' => $method_key
    );
    if ((float)$recharge_preview['total_bob'] <= 0) {
        $recharge_preview['total_bob'] = round(((float)$recharge_preview['amount_usd']) * (float)$rate_bob, 2);
    }

    $preview_result = programmit_finance_create_provider_qr($method_preview, $recharge_preview);
    if (!$preview_result || !is_array($preview_result) || empty($preview_result['ok'])) {
        $preview_response['message'] = !empty($preview_result['error'])
            ? (string)$preview_result['error']
            : 'El proveedor rechazo la prueba o no devolvio datos validos.';
        echo json_encode($preview_response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    $qr_image = isset($preview_result['qr_image_url']) ? trim((string)$preview_result['qr_image_url']) : '';
    $qr_payload = isset($preview_result['qr_payload']) ? trim((string)$preview_result['qr_payload']) : '';

    $preview_response['ok'] = true;
    $preview_response['message'] = 'Conexion correcta. Credenciales validas para este metodo.';
    $preview_response['txn_id'] = isset($preview_result['provider_txn_id']) ? (string)$preview_result['provider_txn_id'] : '';
    $preview_response['expires_at'] = isset($preview_result['expires_at']) ? (string)$preview_result['expires_at'] : '';
    $preview_response['has_qr'] = ($qr_image !== '' || $qr_payload !== '') ? 1 : 0;
    $preview_response['qr_mode'] = ($qr_image !== '') ? 'image' : (($qr_payload !== '') ? 'payload' : '');
    $preview_response['qr_image_url'] = $qr_image;
    $preview_response['qr_payload'] = $qr_payload;

    echo json_encode($preview_response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_general']) && $finance_methods_locked !== 1) {
    $active_tab = 'general';
    $default_rate_input = isset($_POST['default_rate_bob']) ? (float)$_POST['default_rate_bob'] : 0.0;
    $credit_price_input = isset($_POST['credit_price_usd']) ? (float)$_POST['credit_price_usd'] : 0.0;
    $master_host_input = isset($_POST['finance_master_host']) ? programmit_finance_normalize_host((string)$_POST['finance_master_host']) : '';
    $apply_rate_all = isset($_POST['apply_rate_all']) ? 1 : 0;

    if ($default_rate_input <= 0) {
        $method_error = 'Tipo de cambio global invalido.';
    } elseif ($credit_price_input <= 0) {
        $method_error = 'Precio por credito invalido.';
    } elseif ($master_host_input === '') {
        $method_error = 'Dominio admin principal invalido.';
    } else {
        programmit_finance_set_setting($db, 'usd_bob_default_rate', (string)number_format($default_rate_input, 4, '.', ''));
        programmit_finance_set_setting($db, 'credit_price_usd', (string)number_format($credit_price_input, 4, '.', ''));
        programmit_finance_set_setting($db, 'finance_master_host', $master_host_input);
        if ($apply_rate_all === 1) {
            $db->sql_query("UPDATE finance_payment_methods
                SET rate_bob='".$db->SanitizeForSQL(number_format($default_rate_input, 4, '.', ''))."',
                    updated_at=NOW()");
        }
        $default_rate_bob = $default_rate_input;
        $credit_price_usd = $credit_price_input;
        $finance_master_host = $master_host_input;
        $method_success = ($apply_rate_all === 1)
            ? 'Tipo de cambio global actualizado y aplicado a todos los metodos.'
            : 'Tipo de cambio global actualizado.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_method']) && $finance_methods_locked !== 1) {
    $active_tab = 'methods';
    $method_id = isset($_POST['method_id']) ? (int)$_POST['method_id'] : 0;
    $method_preset = isset($_POST['method_preset']) ? strtolower(trim((string)$_POST['method_preset'])) : '';
    $method_key = strtolower(trim((string)$_POST['method_key']));
    $provider_key = strtolower(trim((string)$_POST['provider_key']));
    $method_name = trim((string)$_POST['method_name']);
    $description = trim((string)$_POST['description']);
    $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 100;
    $min_amount = isset($_POST['min_amount']) ? (float)$_POST['min_amount'] : 1;
    $max_amount = isset($_POST['max_amount']) ? (float)$_POST['max_amount'] : 1000;
    $fee_fixed = isset($_POST['fee_fixed']) ? (float)$_POST['fee_fixed'] : 0;
    $fee_percent = isset($_POST['fee_percent']) ? (float)$_POST['fee_percent'] : 0;
    $rate_bob = isset($_POST['rate_bob']) ? (float)$_POST['rate_bob'] : 0;
    $allow_new_users = isset($_POST['allow_new_users']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $create_url = isset($_POST['create_url']) ? trim((string)$_POST['create_url']) : '';
    $auth_type = isset($_POST['auth_type']) ? strtolower(trim((string)$_POST['auth_type'])) : '';
    $api_key = isset($_POST['api_key']) ? trim((string)$_POST['api_key']) : '';
    $api_user = isset($_POST['api_user']) ? trim((string)$_POST['api_user']) : '';
    $api_password = isset($_POST['api_password']) ? trim((string)$_POST['api_password']) : '';
    $secret = isset($_POST['secret']) ? trim((string)$_POST['secret']) : '';
    $txn_path = isset($_POST['txn_path']) ? trim((string)$_POST['txn_path']) : '';
    $qr_image_path = isset($_POST['qr_image_path']) ? trim((string)$_POST['qr_image_path']) : '';
    $qr_payload_path = isset($_POST['qr_payload_path']) ? trim((string)$_POST['qr_payload_path']) : '';
    $expires_path = isset($_POST['expires_path']) ? trim((string)$_POST['expires_path']) : '';
    $extra_headers = isset($_POST['extra_headers']) ? trim((string)$_POST['extra_headers']) : '';
    $instructions = isset($_POST['instructions']) ? trim((string)$_POST['instructions']) : '';
    $instruction_legacy = isset($_POST['instruction']) ? trim((string)$_POST['instruction']) : '';
    $icon_url = isset($_POST['icon_url']) ? trim((string)$_POST['icon_url']) : '';
    $remove_icon = isset($_POST['remove_icon']) ? 1 : 0;
    $credit_price_method_raw = isset($_POST['credit_price_usd']) ? trim((string)$_POST['credit_price_usd']) : '';
    $credit_price_method = 0;
    if ($credit_price_method_raw !== '') {
        $credit_price_method = (float)$credit_price_method_raw;
    }
    $qr_provider = isset($_POST['qr_provider']) ? strtolower(trim((string)$_POST['qr_provider'])) : '';
    if ($qr_provider === '') {
        $qr_provider = 'veripagos';
    }
    $qb_mode = isset($_POST['qb_mode']) ? strtolower(trim((string)$_POST['qb_mode'])) : '';
    if ($qb_mode !== 'prod' && $qb_mode !== 'test') {
        $qb_mode = 'test';
    }
    $qb_expiry_minutes = isset($_POST['qb_expiry_minutes']) ? (int)$_POST['qb_expiry_minutes'] : 15;
    if ($qb_expiry_minutes < 1 || $qb_expiry_minutes > 1440) {
        $qb_expiry_minutes = 15;
    }
    $vp_username = isset($_POST['vp_username']) ? trim((string)$_POST['vp_username']) : '';
    $vp_password = isset($_POST['vp_password']) ? trim((string)$_POST['vp_password']) : '';
    $vp_secret_key = isset($_POST['vp_secret_key']) ? trim((string)$_POST['vp_secret_key']) : '';
    $vp_base_url = isset($_POST['vp_base_url']) ? trim((string)$_POST['vp_base_url']) : '';
    $bnb_account_id = isset($_POST['bnb_account_id']) ? trim((string)$_POST['bnb_account_id']) : '';
    $bnb_authorization_id = isset($_POST['bnb_authorization_id']) ? trim((string)$_POST['bnb_authorization_id']) : '';
    $bnb_destination_account_id = isset($_POST['bnb_destination_account_id']) ? (int)$_POST['bnb_destination_account_id'] : 1;
    if ($bnb_destination_account_id <= 0) {
        $bnb_destination_account_id = 1;
    }
    if ($bnb_destination_account_id !== 1 && $bnb_destination_account_id !== 2) {
        $bnb_destination_account_id = 1;
    }
    $bnb_currency = isset($_POST['bnb_currency']) ? strtoupper(trim((string)$_POST['bnb_currency'])) : '';
    if ($bnb_currency === '') {
        $bnb_currency = 'BOB';
    }
    $bnb_currency = substr($bnb_currency, 0, 3);
    $bnb_token_url = isset($_POST['bnb_token_url']) ? trim((string)$_POST['bnb_token_url']) : '';
    $bnb_qr_url = isset($_POST['bnb_qr_url']) ? trim((string)$_POST['bnb_qr_url']) : '';
    $bnb_status_url = isset($_POST['bnb_status_url']) ? trim((string)$_POST['bnb_status_url']) : '';
    $bnb_cancel_url = isset($_POST['bnb_cancel_url']) ? trim((string)$_POST['bnb_cancel_url']) : '';
    $processing_fee = isset($_POST['processing_fee']) ? 1 : 0;

    if ($instructions === '' && $instruction_legacy !== '') {
        $instructions = $instruction_legacy;
    }

    if ($method_preset !== '' && isset($method_preset_lookup[$method_preset])) {
        $presetData = $method_preset_lookup[$method_preset];
        if ($method_key === '') {
            $method_key = strtolower(trim((string)$presetData['method_key']));
        }
        if ($provider_key === '') {
            $provider_key = strtolower(trim((string)$presetData['provider_key']));
        }
        if ($method_name === '') {
            $method_name = trim((string)$presetData['method_name']);
        }
    }

    if ($auth_type === '') {
        $auth_type = 'bearer';
    }

    $existing_method = null;
    $existing_settings = array();
    if ($method_id > 0) {
        $existing_method = programmit_finance_get_method($db, $method_id);
        if ($existing_method && isset($existing_method['settings']) && is_array($existing_method['settings'])) {
            $existing_settings = $existing_method['settings'];
        }
    }

    if ($secret === '' && isset($existing_settings['secret'])) {
        $secret = (string)$existing_settings['secret'];
    }
    if ($api_password === '' && isset($existing_settings['api_password'])) {
        $api_password = (string)$existing_settings['api_password'];
    }
    if ($vp_password === '' && isset($existing_settings['vp_password'])) {
        $vp_password = (string)$existing_settings['vp_password'];
    }
    if ($vp_secret_key === '') {
        if (isset($existing_settings['vp_secret_key'])) {
            $vp_secret_key = (string)$existing_settings['vp_secret_key'];
        } elseif (isset($existing_settings['secret'])) {
            $vp_secret_key = (string)$existing_settings['secret'];
        }
    }
    if ($bnb_authorization_id === '' && isset($existing_settings['bnb_authorization_id'])) {
        $bnb_authorization_id = (string)$existing_settings['bnb_authorization_id'];
    }
    if ($vp_username === '' && isset($existing_settings['vp_username'])) {
        $vp_username = (string)$existing_settings['vp_username'];
    }
    if ($vp_base_url === '' && isset($existing_settings['vp_base_url'])) {
        $vp_base_url = (string)$existing_settings['vp_base_url'];
    }
    if ($icon_url === '' && $remove_icon !== 1 && isset($existing_settings['icon_url'])) {
        $icon_url = (string)$existing_settings['icon_url'];
    }
    if ($remove_icon === 1) {
        $icon_url = '';
    }
    if ($method_error === '' && isset($_FILES['icon_file']) && is_array($_FILES['icon_file'])) {
        $uploadError = '';
        $uploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logo' . DIRECTORY_SEPARATOR . 'metodos';
        $uploadedIcon = programmit_finance_upload_method_icon('icon_file', $uploadDir, 'metodo_pago', $uploadError);
        if ($uploadError !== '') {
            $method_error = $uploadError;
        } elseif ($uploadedIcon !== '') {
            $icon_url = $uploadedIcon;
        }
    }

    if ($create_url === '') {
        if (isset($existing_settings['create_url'])) {
            $create_url = (string)$existing_settings['create_url'];
        } elseif ($vp_base_url !== '') {
            $create_url = $vp_base_url;
        }
    }

    if ($rate_bob <= 0) {
        $rate_bob = (float)$default_rate_bob;
    }

    $is_qr_bolivia_method = (
        $method_key === 'qr_bolivia_auto' ||
        $method_key === 'veripagos' ||
        $method_preset === 'qr_bolivia_auto' ||
        $method_preset === 'veripagos' ||
        $provider_key === 'bnb_qr' ||
        $provider_key === 'veripagos_qr'
    );
    if ($is_qr_bolivia_method) {
        if ($qr_provider === 'bnb') {
            $provider_key = 'bnb_qr';
        } elseif ($qr_provider === 'veripagos') {
            $provider_key = 'veripagos_qr';
        }
    }

    if ($method_name === '' || $method_key === '') {
        $method_error = 'Nombre y clave del metodo son obligatorios.';
    } elseif (!preg_match('/^[a-z0-9_\-]{3,64}$/', $method_key)) {
        $method_error = 'La clave del metodo solo acepta a-z, 0-9, guion y guion bajo.';
    } elseif ($provider_key === '') {
        $method_error = 'Proveedor invalido.';
    } elseif ($min_amount <= 0 || $max_amount < $min_amount) {
        $method_error = 'Rango de montos invalido.';
    } elseif ($rate_bob <= 0) {
        $method_error = 'Tipo de cambio invalido.';
    } elseif ($credit_price_method_raw !== '' && $credit_price_method <= 0) {
        $method_error = 'Precio por credito del metodo invalido.';
    } elseif (!in_array($auth_type, array('none', 'bearer', 'basic', 'apikey'), true)) {
        $method_error = 'Tipo de autenticacion API invalido.';
    } elseif ($is_qr_bolivia_method && !in_array($qr_provider, array('bnb', 'veripagos'), true)) {
        $method_error = 'Proveedor QR Bolivia invalido.';
    } elseif ($is_qr_bolivia_method && $qr_provider === 'bnb' && $bnb_account_id === '') {
        $method_error = 'BNB Account ID es obligatorio para QR Bolivia con BNB.';
    } elseif ($is_qr_bolivia_method && $qr_provider === 'bnb' && $bnb_authorization_id === '') {
        $method_error = 'BNB Authorization ID es obligatorio para QR Bolivia con BNB.';
    } elseif ($is_qr_bolivia_method && $qr_provider === 'bnb' && !in_array($bnb_currency, array('BOB', 'USD'), true)) {
        $method_error = 'Moneda BNB invalida. Solo se permite BOB o USD.';
    } elseif ($is_qr_bolivia_method && $qr_provider === 'bnb' && (
        $bnb_token_url === '' ||
        $bnb_qr_url === '' ||
        $bnb_status_url === '' ||
        $bnb_cancel_url === ''
    )) {
        $method_error = 'Completa todas las URLs de integracion BNB.';
    } else {
        $dupWhere = ($method_id > 0)
            ? " AND id<>'".$db->SanitizeForSQL($method_id)."'"
            : "";
        $dup_qry = $db->sql_query("SELECT id FROM finance_payment_methods
            WHERE method_key='".$db->SanitizeForSQL($method_key)."'
            ".$dupWhere."
            LIMIT 1");
        if ($dup_qry && $db->sql_numrows($dup_qry) > 0) {
            $method_error = 'La clave del metodo ya existe.';
        } else {
            $settings_json = programmit_finance_json_encode(array(
                'description' => $description,
                'create_url' => $create_url,
                'auth_type' => $auth_type,
                'api_key' => $api_key,
                'api_user' => $api_user,
                'api_password' => $api_password,
                'secret' => $secret,
                'instruction' => $instructions,
                'txn_path' => $txn_path,
                'qr_image_path' => $qr_image_path,
                'qr_payload_path' => $qr_payload_path,
                'expires_path' => $expires_path,
                'extra_headers' => $extra_headers,
                'credit_price_usd' => ($credit_price_method_raw !== '') ? number_format($credit_price_method, 4, '.', '') : '',
                'instructions' => $instructions,
                'icon_url' => $icon_url,
                'qr_provider' => $qr_provider,
                'qb_provider' => $qr_provider,
                'qb_mode' => $qb_mode,
                'qb_expiry_minutes' => $qb_expiry_minutes,
                'vp_username' => $vp_username,
                'vp_password' => $vp_password,
                'vp_secret_key' => $vp_secret_key,
                'vp_base_url' => $vp_base_url,
                'bnb_account_id' => $bnb_account_id,
                'bnb_authorization_id' => $bnb_authorization_id,
                'bnb_destination_account_id' => $bnb_destination_account_id,
                'bnb_currency' => $bnb_currency,
                'bnb_token_url' => $bnb_token_url,
                'bnb_qr_url' => $bnb_qr_url,
                'bnb_status_url' => $bnb_status_url,
                'bnb_cancel_url' => $bnb_cancel_url,
                'processing_fee' => $processing_fee,
                'fee_fixed' => number_format($fee_fixed, 2, '.', ''),
                'fee_percent' => number_format($fee_percent, 2, '.', '')
            ));

            if ($method_id > 0) {
                $ok = $db->sql_query("UPDATE finance_payment_methods
                    SET method_key='".$db->SanitizeForSQL($method_key)."',
                        provider_key='".$db->SanitizeForSQL($provider_key)."',
                        method_name='".$db->SanitizeForSQL($method_name)."',
                        display_order='".$db->SanitizeForSQL($display_order)."',
                        min_amount='".$db->SanitizeForSQL(number_format($min_amount, 2, '.', ''))."',
                        max_amount='".$db->SanitizeForSQL(number_format($max_amount, 2, '.', ''))."',
                        fee_fixed='".$db->SanitizeForSQL(number_format($fee_fixed, 2, '.', ''))."',
                        fee_percent='".$db->SanitizeForSQL(number_format($fee_percent, 2, '.', ''))."',
                        rate_bob='".$db->SanitizeForSQL(number_format($rate_bob, 4, '.', ''))."',
                        allow_new_users='".$db->SanitizeForSQL($allow_new_users)."',
                        is_active='".$db->SanitizeForSQL($is_active)."',
                        settings_json='".$db->SanitizeForSQL($settings_json)."',
                        updated_at=NOW()
                    WHERE id='".$db->SanitizeForSQL($method_id)."'
                    LIMIT 1");
                if ($ok) {
                    $method_success = 'Metodo actualizado correctamente.';
                } else {
                    $method_error = 'No se pudo actualizar el metodo.';
                }
            } else {
                $ok = $db->sql_query("INSERT INTO finance_payment_methods
                    (method_key, provider_key, method_name, display_order, min_amount, max_amount, fee_fixed, fee_percent, rate_bob, allow_new_users, is_active, settings_json, created_at, updated_at)
                    VALUES
                    ('".$db->SanitizeForSQL($method_key)."',
                     '".$db->SanitizeForSQL($provider_key)."',
                     '".$db->SanitizeForSQL($method_name)."',
                     '".$db->SanitizeForSQL($display_order)."',
                     '".$db->SanitizeForSQL(number_format($min_amount, 2, '.', ''))."',
                     '".$db->SanitizeForSQL(number_format($max_amount, 2, '.', ''))."',
                     '".$db->SanitizeForSQL(number_format($fee_fixed, 2, '.', ''))."',
                     '".$db->SanitizeForSQL(number_format($fee_percent, 2, '.', ''))."',
                     '".$db->SanitizeForSQL(number_format($rate_bob, 4, '.', ''))."',
                     '".$db->SanitizeForSQL($allow_new_users)."',
                     '".$db->SanitizeForSQL($is_active)."',
                     '".$db->SanitizeForSQL($settings_json)."',
                     NOW(),
                     NOW())");
                if ($ok) {
                    $method_success = 'Metodo creado correctamente.';
                } else {
                    $method_error = 'No se pudo crear el metodo.';
                }
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_method']) && $finance_methods_locked !== 1) {
    $active_tab = 'methods';
    $delete_id = isset($_POST['delete_id']) ? (int)$_POST['delete_id'] : 0;
    if ($delete_id > 0) {
        $use_qry = $db->sql_query("SELECT COUNT(*) AS total
            FROM finance_recharges
            WHERE method_id='".$db->SanitizeForSQL($delete_id)."'");
        $use_row = $db->sql_fetchrow($use_qry);
        $used_total = $use_row ? (int)$use_row['total'] : 0;

        if ($used_total > 0) {
            $db->sql_query("UPDATE finance_payment_methods
                SET is_active='0', updated_at=NOW()
                WHERE id='".$db->SanitizeForSQL($delete_id)."'
                LIMIT 1");
            $method_success = 'Metodo desactivado. No se elimina porque ya tiene recargas historicas.';
        } else {
            $db->sql_query("DELETE FROM finance_payment_methods
                WHERE id='".$db->SanitizeForSQL($delete_id)."'
                LIMIT 1");
            $method_success = 'Metodo eliminado.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $finance_methods_locked === 1) {
    $method_error = 'Edicion bloqueada en este subdominio. Usa el panel central: https://' . $finance_master_host . '/index.php?p=finance-methods';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $method_success !== '' && $method_error === '') {
    $_SESSION['finance_method_success_flash'] = $method_success;
    $redirectTab = in_array($active_tab, array('general', 'providers', 'methods'), true) ? $active_tab : 'methods';
    header("Location: " . $db->base_url() . "index.php?p=finance-methods&tab=" . $redirectTab . $finance_embed_qs);
    exit;
}

$edit_method_id = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$edit_method = null;
if ($edit_method_id > 0) {
    $edit_method = programmit_finance_get_method($db, $edit_method_id);
}

if (!$edit_method) {
    $edit_method = array(
        'id' => 0,
        'method_key' => '',
        'provider_key' => 'custom_qr',
        'method_name' => '',
        'display_order' => 100,
        'min_amount' => 1,
        'max_amount' => 1000,
        'fee_fixed' => 0,
        'fee_percent' => 0,
        'rate_bob' => $default_rate_bob,
        'allow_new_users' => 1,
        'is_active' => 1,
        'settings' => array(
            'description' => '',
            'create_url' => '',
            'auth_type' => 'bearer',
            'api_key' => '',
            'api_user' => '',
            'api_password' => '',
            'secret' => '',
            'instruction' => '',
            'txn_path' => '',
            'qr_image_path' => '',
            'qr_payload_path' => '',
            'expires_path' => '',
            'extra_headers' => '',
            'credit_price_usd' => '',
            'instructions' => '',
            'icon_url' => '',
            'qr_provider' => 'veripagos',
            'qb_provider' => 'veripagos',
            'qb_mode' => 'test',
            'qb_expiry_minutes' => 15,
            'vp_username' => '',
            'vp_password' => '',
            'vp_secret_key' => '',
            'vp_base_url' => '',
            'bnb_account_id' => '',
            'bnb_authorization_id' => '',
            'bnb_destination_account_id' => 1,
            'bnb_currency' => 'BOB',
            'bnb_token_url' => $bnb_default_urls['token'],
            'bnb_qr_url' => $bnb_default_urls['qr'],
            'bnb_status_url' => $bnb_default_urls['status'],
            'bnb_cancel_url' => $bnb_default_urls['cancel'],
            'processing_fee' => 0,
            'fee_fixed' => '0',
            'fee_percent' => '0'
        )
    );
} else {
    if (!isset($edit_method['settings']) || !is_array($edit_method['settings'])) {
        $edit_method['settings'] = array();
    }
    if (!isset($edit_method['settings']['description'])) {
        $edit_method['settings']['description'] = '';
    }
    if (!isset($edit_method['settings']['auth_type'])) {
        $edit_method['settings']['auth_type'] = 'bearer';
    }
    $settings_defaults = array(
        'instruction' => '',
        'instructions' => '',
        'icon_url' => '',
        'qr_provider' => 'veripagos',
        'qb_provider' => 'veripagos',
        'qb_mode' => 'test',
        'qb_expiry_minutes' => 15,
        'vp_username' => '',
        'vp_password' => '',
        'vp_secret_key' => '',
        'vp_base_url' => '',
        'bnb_account_id' => '',
        'bnb_authorization_id' => '',
        'bnb_destination_account_id' => 1,
        'bnb_currency' => 'BOB',
        'bnb_token_url' => $bnb_default_urls['token'],
        'bnb_qr_url' => $bnb_default_urls['qr'],
        'bnb_status_url' => $bnb_default_urls['status'],
        'bnb_cancel_url' => $bnb_default_urls['cancel'],
        'processing_fee' => 0,
        'fee_fixed' => '0',
        'fee_percent' => '0'
    );
    foreach ($settings_defaults as $settingsKey => $settingsValue) {
        if (!isset($edit_method['settings'][$settingsKey])) {
            $edit_method['settings'][$settingsKey] = $settingsValue;
        }
    }
}

$method_rows = programmit_finance_list_methods($db, false);
foreach ($method_rows as $k => $r) {
    $effectivePrice = programmit_finance_effective_credit_price($db, $r);
    $method_rows[$k]['credit_price_effective'] = $effectivePrice;
}

$provider_rows = array();
foreach ($provider_options as $opt) {
    $count_qry = $db->sql_query("SELECT COUNT(*) AS total
        FROM finance_payment_methods
        WHERE provider_key='".$db->SanitizeForSQL($opt['key'])."'");
    $count_row = $db->sql_fetchrow($count_qry);
    $provider_rows[] = array(
        'key' => $opt['key'],
        'name' => $opt['name'],
        'total_methods' => $count_row ? (int)$count_row['total'] : 0
    );
}

$edit_method_json = 'null';
if ($edit_method && is_array($edit_method)) {
    $edit_method_json_try = json_encode($edit_method, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (is_string($edit_method_json_try) && $edit_method_json_try !== '') {
        $edit_method_json = $edit_method_json_try;
    }
}

$smarty->assign('finance_active_tab', $active_tab);
$smarty->assign('finance_method_error', $method_error);
$smarty->assign('finance_method_success', $method_success);
$smarty->assign('finance_default_rate_bob', number_format((float)$default_rate_bob, 4, '.', ''));
$smarty->assign('finance_credit_price_usd', number_format((float)$credit_price_usd, 4, '.', ''));
$smarty->assign('finance_master_host', $finance_master_host);
$smarty->assign('finance_current_host', $finance_current_host);
$smarty->assign('finance_methods_locked', $finance_methods_locked);
$smarty->assign('finance_provider_options', $provider_options);
$smarty->assign('finance_method_presets', $method_presets);
$smarty->assign('finance_provider_rows', $provider_rows);
$smarty->assign('finance_edit_method', $edit_method);
$smarty->assign('finance_edit_method_json', $edit_method_json);
$smarty->assign('finance_method_rows', $method_rows);
$smarty->assign('finance_embed_admin', $finance_embed_admin ? 1 : 0);
$smarty->assign('finance_embed_qs', $finance_embed_qs);
$smarty->assign('page', 'finance-methods');
$smarty->display('finance-methods.tpl');
?>
