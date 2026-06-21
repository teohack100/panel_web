
<?php
define('DOC_ROOT_PATH', __DIR__ . '/');
require __DIR__ . '/includes/functions.php';
if (function_exists('session_status')) {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        @session_start();
    }
} elseif (session_id() === '') {
    @session_start();
}

function programmit_admin_auth_cookie_valid_runtime($db, $userId, $userName, $userPass) {
    if (!isset($_COOKIE['panel_admin_auth']) || trim((string)$_COOKIE['panel_admin_auth']) === '') {
        return false;
    }
    $raw = $db->decrypt_key((string)$_COOKIE['panel_admin_auth']);
    if (!is_string($raw) || $raw === '') {
        return false;
    }
    $parts = explode('|', $raw);
    if (!isset($parts[0], $parts[1], $parts[2])) {
        return false;
    }
    return (
        (int)$parts[0] === (int)$userId &&
        hash_equals((string)$parts[1], (string)$userName) &&
        hash_equals((string)$parts[2], (string)$userPass)
    );
}

function admin_h($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function admin_fmt_int($value) { return number_format((int)$value, 0, '.', ','); }
function admin_fmt_money($value) { return number_format((float)$value, 2, '.', ','); }

function admin_fmt_datetime($value) {
    $raw = trim((string)$value);
    if ($raw === '') { return '-'; }
    $ts = strtotime($raw);
    if ($ts === false) { return $raw; }
    return date('Y-m-d H:i', $ts);
}

function admin_table_exists($tableName) {
    global $db;
    if (function_exists('table_exists_cached')) {
        return table_exists_cached((string)$tableName);
    }
    $key = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$tableName);
    if ($key === '') { return false; }
    $qry = $db->sql_query("SHOW TABLES LIKE '" . $db->SanitizeForSQL($key) . "'");
    return ($qry && $db->sql_numrows($qry) > 0);
}

function admin_fetch_row_or_default($sql, $defaultRow = array()) {
    global $db;
    $qry = $db->sql_query((string)$sql);
    if (!$qry) { return $defaultRow; }
    $row = $db->sql_fetchrow($qry);
    if (!$row || !is_array($row)) { return $defaultRow; }
    return array_merge($defaultRow, $row);
}

function admin_cache_path($userId) {
    $userId = (int)$userId;
    if ($userId <= 0) { return ''; }
    $tmpDir = rtrim((string)sys_get_temp_dir(), '/\\');
    if ($tmpDir === '') { $tmpDir = '/tmp'; }
    $cacheDir = $tmpDir . DIRECTORY_SEPARATOR . 'programmit_bootstrap';
    if (!is_dir($cacheDir)) { @mkdir($cacheDir, 0775, true); }
    return $cacheDir . DIRECTORY_SEPARATOR . 'admin_dashboard_user_' . $userId . '.html';
}

function admin_recharge_badge_class($status) {
    $status = strtolower(trim((string)$status));
    if ($status === 'paid') { return 'ok'; }
    if ($status === 'pending') { return 'warn'; }
    if ($status === 'failed' || $status === 'expired' || $status === 'cancelled') { return 'err'; }
    return 'muted';
}

function admin_setting_get($db, $key, $default = '') {
    if (function_exists('programmit_saas_get_setting')) {
        return (string)programmit_saas_get_setting($db, (string)$key, (string)$default);
    }
    return (string)$default;
}

function admin_setting_set($db, $key, $value) {
    if (function_exists('programmit_saas_set_setting')) {
        return (bool)programmit_saas_set_setting($db, (string)$key, (string)$value);
    }
    return false;
}

function admin_flash_set($scope, $type, $message) {
    if (!isset($_SESSION) || !is_array($_SESSION)) {
        return;
    }
    $_SESSION['programmit_admin_flash'] = array(
        'scope' => trim((string)$scope),
        'type' => trim((string)$type),
        'message' => trim((string)$message),
    );
}

function admin_flash_pull() {
    if (!isset($_SESSION['programmit_admin_flash']) || !is_array($_SESSION['programmit_admin_flash'])) {
        return array('scope' => '', 'type' => '', 'message' => '');
    }
    $flash = $_SESSION['programmit_admin_flash'];
    unset($_SESSION['programmit_admin_flash']);
    return array(
        'scope' => isset($flash['scope']) ? trim((string)$flash['scope']) : '',
        'type' => isset($flash['type']) ? trim((string)$flash['type']) : '',
        'message' => isset($flash['message']) ? trim((string)$flash['message']) : '',
    );
}

function admin_redirect_with_flash($baseUrl, $scope, $type, $message) {
    admin_flash_set($scope, $type, $message);
    header('Location: ' . rtrim((string)$baseUrl, '/') . '/admin.php#' . rawurlencode((string)$scope));
    exit;
}

function admin_asset_url($baseUrl, $rawValue, $fallbackRelative = '') {
    $value = trim((string)$rawValue);
    if ($value === '') {
        $value = trim((string)$fallbackRelative);
    }
    if ($value === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $value) === 1 || strpos($value, '//') === 0) {
        return $value;
    }
    return rtrim((string)$baseUrl, '/') . '/' . ltrim($value, '/');
}

function admin_upload_file($fieldName, $targetDir, $prefix, $allowedExt, $maxBytes, &$errorOut) {
    $errorOut = '';
    if (!isset($_FILES[$fieldName]) || !is_array($_FILES[$fieldName])) {
        return '';
    }
    $file = $_FILES[$fieldName];
    if (!isset($file['error']) || (int)$file['error'] === UPLOAD_ERR_NO_FILE) {
        return '';
    }
    if ((int)$file['error'] !== UPLOAD_ERR_OK) {
        $errorOut = 'Error al subir archivo en ' . $fieldName . '.';
        return '';
    }
    $size = isset($file['size']) ? (int)$file['size'] : 0;
    if ($size <= 0 || $size > (int)$maxBytes) {
        $errorOut = 'Archivo demasiado grande en ' . $fieldName . '.';
        return '';
    }
    $name = isset($file['name']) ? (string)$file['name'] : '';
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if ($ext === '' || !in_array($ext, $allowedExt, true)) {
        $errorOut = 'Extension no permitida en ' . $fieldName . '.';
        return '';
    }
    if (!is_dir($targetDir)) {
        @mkdir($targetDir, 0775, true);
    }
    if (!is_dir($targetDir)) {
        $errorOut = 'No se pudo crear directorio de branding.';
        return '';
    }
    $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '', pathinfo($name, PATHINFO_FILENAME));
    if ($safeName === '') {
        $safeName = 'asset';
    }
    $finalName = $prefix . '_' . date('Ymd_His') . '_' . substr(sha1($safeName . microtime(true)), 0, 10) . '.' . $ext;
    $targetPath = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $finalName;
    $tmpName = isset($file['tmp_name']) ? (string)$file['tmp_name'] : '';
    if ($tmpName === '' || !is_uploaded_file($tmpName)) {
        $errorOut = 'Archivo temporal invalido en ' . $fieldName . '.';
        return '';
    }
    if (!@move_uploaded_file($tmpName, $targetPath)) {
        $errorOut = 'No se pudo guardar archivo en ' . $fieldName . '.';
        return '';
    }
    @chmod($targetPath, 0644);
    return 'logo/branding/' . $finalName;
}

function admin_clear_render_caches() {
    $tmpDir = rtrim((string)sys_get_temp_dir(), '/\\');
    if ($tmpDir === '') { $tmpDir = '/tmp'; }
    $cacheDir = $tmpDir . DIRECTORY_SEPARATOR . 'programmit_bootstrap';
    if (!is_dir($cacheDir)) { return; }
    $patterns = array(
        $cacheDir . DIRECTORY_SEPARATOR . 'admin_dashboard_user_*.html',
        $cacheDir . DIRECTORY_SEPARATOR . 'public_page_*.html',
        $cacheDir . DIRECTORY_SEPARATOR . 'saas_ctx_*.json'
    );
    foreach ($patterns as $pattern) {
        $files = glob($pattern);
        if (!is_array($files)) { continue; }
        foreach ($files as $file) {
            if (is_file($file)) { @unlink($file); }
        }
    }
}

if (!is_logged_in($user)) {
    header("Location: " . $db->base_url() . "admin-login.php");
    exit;
}

if (!programmit_admin_auth_cookie_valid_runtime($db, (int)$user_id_2, (string)$user_name_2, (string)$auth_2)) {
    header("Location: " . $db->base_url() . "admin-login.php?reauth=1");
    exit;
}

$isAdminUser = (
    (int)$user_id_2 === 1 ||
    $user_level_2 === 'superadmin' ||
    $user_level_2 === 'administrator' ||
    $user_level_2 === 'subadmin'
);

if (!$isAdminUser) {
    clear_auth_cookies();
    header("Location: " . $db->base_url() . "admin-login.php?error=role");
    exit;
}

if (function_exists('programmit_control_is_host') && programmit_control_is_host($db)) {
    $controlIp = function_exists('programmit_control_security_resolve_client_ip')
        ? programmit_control_security_resolve_client_ip($db)
        : $db->get_client_ip();
    if (function_exists('programmit_control_security_ip_allowed') && !programmit_control_security_ip_allowed($db, $controlIp)) {
        clear_auth_cookies();
        header("Location: " . $db->base_url() . "admin-login.php?control=ip_blocked");
        exit;
    }
    $controlUser = array(
        'user_id' => (int)$user_id_2,
        'user_level' => (string)$user_level_2,
        'user_email' => (string)$user_email_2
    );
    if (function_exists('programmit_control_security_user_allowed') && !programmit_control_security_user_allowed($db, $controlUser)) {
        clear_auth_cookies();
        header("Location: " . $db->base_url() . "admin-login.php?control=access_denied");
        exit;
    }
}

$cacheEnabled = (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'GET' && !isset($_GET['refresh']));
$cacheFile = admin_cache_path((int)$user_id_2);
if ($cacheEnabled && $cacheFile !== '') {
    clearstatcache(true, $cacheFile);
    $cacheTtlSeconds = 15;
    if (is_file($cacheFile) && (time() - (int)@filemtime($cacheFile)) <= $cacheTtlSeconds) {
        if (!headers_sent()) { header('Content-Type: text/html; charset=UTF-8'); }
        readfile($cacheFile);
        exit;
    }
    ob_start();
}

if (function_exists('programmit_finance_ensure_tables')) { programmit_finance_ensure_tables($db); }
if (function_exists('programmit_saas_ensure_tables')) { programmit_saas_ensure_tables($db); }

$baseUrl = $db->base_url();
$dashboardUrl = $baseUrl . "index.php?p=dashboard";
$logoutUrl = $baseUrl . "index.php?p=logout";
$refreshUrl = $baseUrl . "admin.php?refresh=1";
$serverStatusEmbedUrl = $baseUrl . "index.php?p=server-status&embed=admin";
$serverUpdateEmbedUrl = $baseUrl . "index.php?p=server-update&embed=admin";
$noticeUpdateEmbedUrl = $baseUrl . "index.php?p=notice-update&embed=admin";
$creditLogsEmbedUrl = $baseUrl . "index.php?p=credit-logs&embed=admin";
$financeMethodsUrl = $baseUrl . "index.php?p=finance-methods&tab=methods";
$financeMethodsEmbedUrl = $baseUrl . "index.php?p=finance-methods&tab=methods&embed=admin";
$vpnControlEmbedUrl = $baseUrl . "index.php?p=vpn-control&embed=admin";
$currentHost = function_exists('programmit_saas_current_host') ? (string)programmit_saas_current_host() : (string)(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
$today = date('Y-m-d');
$generatedAt = date('Y-m-d H:i:s');

$adminNoticeError = '';
$adminNoticeSuccess = '';
$adminNoticeScope = '';

$panelBrandName = trim(admin_setting_get($db, 'panel_brand_name', 'panel.programmit.com'));
if ($panelBrandName === '') { $panelBrandName = 'panel.programmit.com'; }
$panelSiteTitle = trim(admin_setting_get($db, 'panel_site_title', 'PROGRAMMIT PANEL'));
if ($panelSiteTitle === '') { $panelSiteTitle = 'PROGRAMMIT PANEL'; }
$panelHeaderTitle = trim(admin_setting_get($db, 'panel_admin_header_title', 'Panel Administrativo'));
if ($panelHeaderTitle === '') { $panelHeaderTitle = 'Panel Administrativo'; }
$panelHeaderSubtitle = trim(admin_setting_get($db, 'panel_admin_header_subtitle', 'PROGRAMMIT'));
if ($panelHeaderSubtitle === '') { $panelHeaderSubtitle = 'PROGRAMMIT'; }
$panelHeaderContext = trim(admin_setting_get($db, 'panel_admin_context_label', 'Control Interno'));
if ($panelHeaderContext === '') { $panelHeaderContext = 'Control Interno'; }

$panelLogoStored = trim(admin_setting_get($db, 'panel_logo_url', ''));
$panelLoginLogoStored = trim(admin_setting_get($db, 'panel_login_logo_url', ''));
$panelFaviconStored = trim(admin_setting_get($db, 'panel_favicon_url', ''));
$clientDefaultPasswordConfigured = (function_exists('programmit_client_default_password_is_configured') && programmit_client_default_password_is_configured($db));
$clientDefaultPasswordScopeLabel = (((int)$user_id_2 === 1 || $user_level_2 === 'superadmin') ? 'todos los clientes normales' : 'los clientes normales de tu panel');
$clientDefaultPasswordSummary = array('targeted' => 0, 'needs_update' => 0);
if ($clientDefaultPasswordConfigured && function_exists('programmit_client_default_password_existing_summary')) {
    $clientDefaultPasswordSummary = programmit_client_default_password_existing_summary($db, (int)$user_id_2, (string)$user_level_2);
}

$panelLogoUrl = admin_asset_url($baseUrl, $panelLogoStored, 'logo/icon_panel.png');
$panelLoginLogoUrl = admin_asset_url($baseUrl, $panelLoginLogoStored !== '' ? $panelLoginLogoStored : $panelLogoStored, 'logo/icon_panel.png');
$panelFaviconUrl = admin_asset_url($baseUrl, $panelFaviconStored, 'logo/favicon2.png');
$controlSecurityControlHost = function_exists('programmit_saas_get_control_host') ? (string)programmit_saas_get_control_host($db) : 'panel.programmit.com';
$controlSecurityIsHost = (function_exists('programmit_control_is_host') && programmit_control_is_host($db));
$controlSecurityCurrentIp = function_exists('programmit_control_security_resolve_client_ip')
    ? (string)programmit_control_security_resolve_client_ip($db)
    : (string)$db->get_client_ip();
$controlSecuritySettings = array(
    'strict_mode' => function_exists('programmit_control_security_bool') ? programmit_control_security_bool(programmit_control_security_setting($db, 'control_admin_strict_mode', '1'), true) : true,
    'require_superadmin' => function_exists('programmit_control_security_bool') ? programmit_control_security_bool(programmit_control_security_setting($db, 'control_admin_require_superadmin', '1'), true) : true,
    'ip_whitelist_enabled' => function_exists('programmit_control_security_bool') ? programmit_control_security_bool(programmit_control_security_setting($db, 'control_admin_ip_whitelist_enabled', '0'), false) : false,
    'ip_whitelist' => trim((string)programmit_control_security_setting($db, 'control_admin_ip_whitelist', '')),
    'allowed_user_ids' => trim((string)programmit_control_security_setting($db, 'control_admin_allowed_user_ids', '1')),
    'allowed_emails' => trim((string)programmit_control_security_setting($db, 'control_admin_allowed_emails', '')),
    'allow_register' => function_exists('programmit_control_security_bool') ? programmit_control_security_bool(programmit_control_security_setting($db, 'control_admin_allow_register', '0'), false) : false,
    'allow_magic_login' => function_exists('programmit_control_security_bool') ? programmit_control_security_bool(programmit_control_security_setting($db, 'control_admin_allow_magic_login', '0'), false) : false,
);

$adminFlash = admin_flash_pull();
if ($adminFlash['message'] !== '') {
    $adminNoticeScope = $adminFlash['scope'];
    if ($adminFlash['type'] === 'success') {
        $adminNoticeSuccess = $adminFlash['message'];
    } else {
        $adminNoticeError = $adminFlash['message'];
    }
}

if (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'POST') {
    if (isset($_POST['save_panel_identity'])) {
        $newBrandName = trim((string)($_POST['panel_brand_name'] ?? ''));
        $newSiteTitle = trim((string)($_POST['panel_site_title'] ?? ''));
        $newHeaderTitle = trim((string)($_POST['panel_admin_header_title'] ?? ''));
        $newHeaderSubtitle = trim((string)($_POST['panel_admin_header_subtitle'] ?? ''));
        $newHeaderContext = trim((string)($_POST['panel_admin_context_label'] ?? ''));

        if ($newBrandName === '' || strlen($newBrandName) > 120) {
            $adminNoticeError = 'Nombre de panel invalido.';
        } elseif ($newSiteTitle === '' || strlen($newSiteTitle) > 180) {
            $adminNoticeError = 'Titulo del panel invalido.';
        } elseif ($newHeaderTitle === '' || strlen($newHeaderTitle) > 80) {
            $adminNoticeError = 'Titulo de encabezado invalido.';
        } elseif ($newHeaderSubtitle === '' || strlen($newHeaderSubtitle) > 80) {
            $adminNoticeError = 'Subtitulo de encabezado invalido.';
        } elseif ($newHeaderContext === '' || strlen($newHeaderContext) > 80) {
            $adminNoticeError = 'Contexto de encabezado invalido.';
        } else {
            admin_setting_set($db, 'panel_brand_name', $newBrandName);
            admin_setting_set($db, 'panel_site_title', $newSiteTitle);
            admin_setting_set($db, 'panel_admin_header_title', $newHeaderTitle);
            admin_setting_set($db, 'panel_admin_header_subtitle', $newHeaderSubtitle);
            admin_setting_set($db, 'panel_admin_context_label', $newHeaderContext);

            $panelBrandName = $newBrandName;
            $panelSiteTitle = $newSiteTitle;
            $panelHeaderTitle = $newHeaderTitle;
            $panelHeaderSubtitle = $newHeaderSubtitle;
            $panelHeaderContext = $newHeaderContext;
            $db->SetWebsiteName($panelBrandName);
            $db->SetWebsiteTitle($panelSiteTitle);
            admin_clear_render_caches();
            admin_redirect_with_flash($baseUrl, 'cfg-general', 'success', 'Configuracion general guardada.');
        }

        if ($adminNoticeError !== '') {
            admin_redirect_with_flash($baseUrl, 'cfg-general', 'error', $adminNoticeError);
        }
    }

    if ($adminNoticeError === '' && isset($_POST['save_panel_appearance'])) {
        $logoInput = trim((string)($_POST['panel_logo_url'] ?? ''));
        $loginLogoInput = trim((string)($_POST['panel_login_logo_url'] ?? ''));
        $faviconInput = trim((string)($_POST['panel_favicon_url'] ?? ''));

        $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'logo' . DIRECTORY_SEPARATOR . 'branding';
        $uploadError = '';
        $logoUpload = admin_upload_file('panel_logo_file', $uploadDir, 'panel_logo', array('png', 'jpg', 'jpeg', 'webp'), 4 * 1024 * 1024, $uploadError);
        if ($uploadError !== '') {
            $adminNoticeError = $uploadError;
        }
        $loginLogoUpload = '';
        if ($adminNoticeError === '') {
            $loginLogoUpload = admin_upload_file('panel_login_logo_file', $uploadDir, 'panel_login_logo', array('png', 'jpg', 'jpeg', 'webp'), 4 * 1024 * 1024, $uploadError);
            if ($uploadError !== '') {
                $adminNoticeError = $uploadError;
            }
        }
        $faviconUpload = '';
        if ($adminNoticeError === '') {
            $faviconUpload = admin_upload_file('panel_favicon_file', $uploadDir, 'panel_favicon', array('png', 'ico', 'jpg', 'jpeg', 'webp'), 2 * 1024 * 1024, $uploadError);
            if ($uploadError !== '') {
                $adminNoticeError = $uploadError;
            }
        }

        if ($adminNoticeError === '') {
            if ($logoUpload !== '') { $logoInput = $logoUpload; }
            if ($loginLogoUpload !== '') { $loginLogoInput = $loginLogoUpload; }
            if ($faviconUpload !== '') { $faviconInput = $faviconUpload; }
            if ($loginLogoInput === '') { $loginLogoInput = $logoInput; }

            admin_setting_set($db, 'panel_logo_url', $logoInput);
            admin_setting_set($db, 'panel_login_logo_url', $loginLogoInput);
            admin_setting_set($db, 'panel_favicon_url', $faviconInput);

            $panelLogoStored = $logoInput;
            $panelLoginLogoStored = $loginLogoInput;
            $panelFaviconStored = $faviconInput;

            $panelLogoUrl = admin_asset_url($baseUrl, $panelLogoStored, 'logo/icon_panel.png');
            $panelLoginLogoUrl = admin_asset_url($baseUrl, $panelLoginLogoStored !== '' ? $panelLoginLogoStored : $panelLogoStored, 'logo/icon_panel.png');
            $panelFaviconUrl = admin_asset_url($baseUrl, $panelFaviconStored, 'logo/favicon2.png');
            admin_clear_render_caches();
            admin_redirect_with_flash($baseUrl, 'cfg-appearance', 'success', 'Apariencia guardada y publicada.');
        }

        if ($adminNoticeError !== '') {
            admin_redirect_with_flash($baseUrl, 'cfg-appearance', 'error', $adminNoticeError);
        }
    }

    if ($adminNoticeError === '' && isset($_POST['save_control_security'])) {
        $strictMode = isset($_POST['control_admin_strict_mode']) ? '1' : '0';
        $requireSuperadmin = isset($_POST['control_admin_require_superadmin']) ? '1' : '0';
        $whitelistEnabled = isset($_POST['control_admin_ip_whitelist_enabled']) ? '1' : '0';
        $rawWhitelist = trim((string)($_POST['control_admin_ip_whitelist'] ?? ''));
        $rawAllowedUserIds = trim((string)($_POST['control_admin_allowed_user_ids'] ?? ''));
        $rawAllowedEmails = trim((string)($_POST['control_admin_allowed_emails'] ?? ''));
        $allowRegister = isset($_POST['control_admin_allow_register']) ? '1' : '0';
        $allowMagicLogin = isset($_POST['control_admin_allow_magic_login']) ? '1' : '0';

        $invalidIps = array();
        $invalidUserIds = array();
        $invalidEmails = array();
        $allowedIps = function_exists('programmit_control_security_normalize_ip_list')
            ? programmit_control_security_normalize_ip_list($rawWhitelist, $invalidIps)
            : array();
        $allowedUserIds = function_exists('programmit_control_security_normalize_user_id_list')
            ? programmit_control_security_normalize_user_id_list($rawAllowedUserIds, $invalidUserIds)
            : array();
        $allowedEmails = function_exists('programmit_control_security_normalize_email_list')
            ? programmit_control_security_normalize_email_list($rawAllowedEmails, $invalidEmails)
            : array();

        if (!empty($invalidIps)) {
            $adminNoticeError = 'IPs invalidas en whitelist: ' . implode(', ', array_slice($invalidIps, 0, 10));
        } elseif (!empty($invalidUserIds)) {
            $adminNoticeError = 'IDs invalidos: ' . implode(', ', array_slice($invalidUserIds, 0, 10));
        } elseif (!empty($invalidEmails)) {
            $adminNoticeError = 'Emails invalidos: ' . implode(', ', array_slice($invalidEmails, 0, 10));
        } elseif ($whitelistEnabled === '1' && empty($allowedIps)) {
            $adminNoticeError = 'Activas la whitelist pero no hay IPs validas.';
        } elseif ($strictMode === '1' && $requireSuperadmin !== '1' && empty($allowedUserIds) && empty($allowedEmails)) {
            $adminNoticeError = 'Con modo estricto activo debes permitir superadmin o definir IDs/emails autorizados.';
        } else {
            $autoAddedIp = '';
            if (
                $whitelistEnabled === '1' &&
                function_exists('programmit_control_security_is_valid_ip') &&
                programmit_control_security_is_valid_ip($controlSecurityCurrentIp) &&
                !in_array($controlSecurityCurrentIp, $allowedIps, true)
            ) {
                $allowedIps[] = $controlSecurityCurrentIp;
                $autoAddedIp = $controlSecurityCurrentIp;
            }

            programmit_control_security_set_setting($db, 'control_admin_strict_mode', $strictMode);
            programmit_control_security_set_setting($db, 'control_admin_require_superadmin', $requireSuperadmin);
            programmit_control_security_set_setting($db, 'control_admin_ip_whitelist_enabled', $whitelistEnabled);
            programmit_control_security_set_setting($db, 'control_admin_ip_whitelist', implode(', ', $allowedIps));
            programmit_control_security_set_setting($db, 'control_admin_allowed_user_ids', implode(', ', $allowedUserIds));
            programmit_control_security_set_setting($db, 'control_admin_allowed_emails', implode(', ', $allowedEmails));
            programmit_control_security_set_setting($db, 'control_admin_allow_register', $allowRegister);
            programmit_control_security_set_setting($db, 'control_admin_allow_magic_login', $allowMagicLogin);

            admin_clear_render_caches();
            $successMessage = 'Seguridad del host de control guardada.';
            if ($autoAddedIp !== '') {
                $successMessage .= ' IP actual agregada automaticamente: ' . $autoAddedIp . '.';
            }
            admin_redirect_with_flash($baseUrl, 'cfg-security', 'success', $successMessage);
        }

        if ($adminNoticeError !== '') {
            admin_redirect_with_flash($baseUrl, 'cfg-security', 'error', $adminNoticeError);
        }
    }

    if ($adminNoticeError === '' && isset($_POST['save_client_defaults'])) {
        $newDefaultClientPassword = trim((string)($_POST['client_default_password'] ?? ''));
        $newDefaultClientPasswordConfirm = trim((string)($_POST['client_default_password_confirm'] ?? ''));
        $clearDefaultClientPassword = isset($_POST['clear_client_default_password']) ? 1 : 0;
        $applyToExistingClients = isset($_POST['apply_client_default_password_existing']) ? 1 : 0;

        if ($clearDefaultClientPassword === 1) {
            if (function_exists('programmit_client_default_password_clear') && programmit_client_default_password_clear($db)) {
                $clientDefaultPasswordConfigured = false;
                admin_clear_render_caches();
                admin_redirect_with_flash($baseUrl, 'ops-client-defaults', 'success', 'Contrasena general de clientes eliminada.');
            } else {
                $adminNoticeError = 'No se pudo eliminar la contrasena general.';
            }
        } elseif ($newDefaultClientPassword === '') {
            $adminNoticeError = 'Ingresa una contrasena general o marca eliminar.';
        } elseif (!hash_equals($newDefaultClientPassword, $newDefaultClientPasswordConfirm)) {
            $adminNoticeError = 'La confirmacion de la contrasena general no coincide.';
        } else {
            $passwordSaveError = '';
            if (function_exists('programmit_client_default_password_set') && programmit_client_default_password_set($db, $newDefaultClientPassword, $passwordSaveError)) {
                $clientDefaultPasswordConfigured = true;
                $successMessage = 'Contrasena general de clientes guardada.';
                if ($applyToExistingClients === 1) {
                    $applyResult = array();
                    $applyError = '';
                    if (function_exists('programmit_client_default_password_apply_to_existing') && programmit_client_default_password_apply_to_existing($db, $applyResult, $applyError, (int)$user_id_2, (string)$user_level_2)) {
                        $changed = isset($applyResult['changed']) ? (int)$applyResult['changed'] : 0;
                        $syncUpdated = (isset($applyResult['reconcile']['updated']) ? (int)$applyResult['reconcile']['updated'] : 0);
                        $successMessage .= ' Aplicada a clientes existentes: ' . $changed . '.';
                        $successMessage .= ' Sync VPS: ' . $syncUpdated . '.';
                    } else {
                        $adminNoticeError = ($applyError !== '') ? $applyError : 'La contrasena se guardo, pero no se pudo aplicar a clientes existentes.';
                    }
                }
                admin_clear_render_caches();
                if ($adminNoticeError === '') {
                    admin_redirect_with_flash($baseUrl, 'ops-client-defaults', 'success', $successMessage);
                }
            } else {
                $adminNoticeError = ($passwordSaveError !== '') ? $passwordSaveError : 'No se pudo guardar la contrasena general.';
            }
        }

        if ($adminNoticeError !== '') {
            admin_redirect_with_flash($baseUrl, 'ops-client-defaults', 'error', $adminNoticeError);
        }
    }
}

$dbDriverLabel = isset($DB_driver) ? (string)$DB_driver : '';
$dbHostLabel = isset($DB_host) ? (string)$DB_host : '';
$dbNameLabel = isset($DB_name) ? (string)$DB_name : '';

$userStats = array('total_users'=>0,'live_users'=>0,'suspended_users'=>0,'freeze_users'=>0,'banned_users'=>0,'admin_users'=>0,'reseller_users'=>0,'subreseller_users'=>0,'credits_total'=>0);
$infraStats = array('servers_total'=>0,'servers_active'=>0);
$financeStats = array('recharges_total'=>0,'recharges_pending'=>0,'recharges_paid'=>0,'recharges_failed'=>0,'paid_usd_total'=>0.0,'usd_today'=>0.0,'credits_issued'=>0,'methods_total'=>0,'methods_active'=>0);
$saasStats = array('tenants_total'=>0,'tenants_active'=>0,'tenants_trial'=>0,'tenants_suspended'=>0,'tenants_credits'=>0,'domains_total'=>0,'domains_active'=>0,'domains_primary'=>0);
$securityStats = array('failed_today'=>0,'banned_ips'=>0,'tickets_total'=>0,'tickets_open'=>0,'tickets_answered'=>0);

$recentRecharges = array();
$recentUsers = array();
$topCredits = array();
$recentTenants = array();
if (admin_table_exists('users')) {
    $userStats = admin_fetch_row_or_default(
        "SELECT COUNT(*) AS total_users,
            COALESCE(SUM(CASE WHEN status='live' AND is_active=1 AND is_freeze=0 THEN 1 ELSE 0 END),0) AS live_users,
            COALESCE(SUM(CASE WHEN status='suspended' OR is_active=0 THEN 1 ELSE 0 END),0) AS suspended_users,
            COALESCE(SUM(CASE WHEN status='freeze' OR is_freeze=1 THEN 1 ELSE 0 END),0) AS freeze_users,
            COALESCE(SUM(CASE WHEN is_ban=1 THEN 1 ELSE 0 END),0) AS banned_users,
            COALESCE(SUM(CASE WHEN user_level IN ('superadmin','administrator','subadmin') THEN 1 ELSE 0 END),0) AS admin_users,
            COALESCE(SUM(CASE WHEN user_level='reseller' THEN 1 ELSE 0 END),0) AS reseller_users,
            COALESCE(SUM(CASE WHEN user_level='subreseller' THEN 1 ELSE 0 END),0) AS subreseller_users,
            COALESCE(SUM(credits),0) AS credits_total
         FROM users",
        $userStats
    );

    $recentUserQry = $db->sql_query("SELECT user_id, user_name, user_level, credits, status, is_active, is_freeze, lastlogin
        FROM users ORDER BY lastlogin DESC LIMIT 12");
    if ($recentUserQry) {
        while ($row = $db->sql_fetchrow($recentUserQry)) {
            if (!$row) { continue; }
            $recentUsers[] = array('user_id'=>(int)$row['user_id'],'user_name'=>(string)$row['user_name'],'user_level'=>(string)$row['user_level'],'credits'=>(int)$row['credits'],'status'=>(string)$row['status'],'is_active'=>(int)$row['is_active'],'is_freeze'=>(int)$row['is_freeze'],'lastlogin'=>(string)$row['lastlogin']);
        }
    }

    $topCreditsQry = $db->sql_query("SELECT user_id, user_name, user_level, credits, status
        FROM users ORDER BY credits DESC LIMIT 10");
    if ($topCreditsQry) {
        while ($row = $db->sql_fetchrow($topCreditsQry)) {
            if (!$row) { continue; }
            $topCredits[] = array('user_id'=>(int)$row['user_id'],'user_name'=>(string)$row['user_name'],'user_level'=>(string)$row['user_level'],'credits'=>(int)$row['credits'],'status'=>(string)$row['status']);
        }
    }
}

if (admin_table_exists('server_list')) {
    $infraStats = admin_fetch_row_or_default(
        "SELECT COUNT(*) AS servers_total,
            COALESCE(SUM(CASE WHEN status=1 OR status='1' THEN 1 ELSE 0 END),0) AS servers_active
         FROM server_list",
        $infraStats
    );
}

if (admin_table_exists('finance_recharges')) {
    $financeStats = admin_fetch_row_or_default(
        "SELECT COUNT(*) AS recharges_total,
            COALESCE(SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END),0) AS recharges_pending,
            COALESCE(SUM(CASE WHEN status='paid' THEN 1 ELSE 0 END),0) AS recharges_paid,
            COALESCE(SUM(CASE WHEN status IN ('failed','expired','cancelled') THEN 1 ELSE 0 END),0) AS recharges_failed,
            COALESCE(SUM(CASE WHEN status='paid' THEN total_usd ELSE 0 END),0) AS paid_usd_total,
            COALESCE(SUM(CASE WHEN DATE(created_at)='" . $db->SanitizeForSQL($today) . "' THEN total_usd ELSE 0 END),0) AS usd_today,
            COALESCE(SUM(CASE WHEN status='paid' THEN credits_to_add ELSE 0 END),0) AS credits_issued
         FROM finance_recharges",
        $financeStats
    );

    $recentRechargeQry = $db->sql_query("SELECT r.id, r.recharge_ref, r.user_id, r.method_name, r.total_usd, r.total_bob, r.credits_to_add, r.status, r.created_at,
        u.user_name FROM finance_recharges r LEFT JOIN users u ON u.user_id=r.user_id ORDER BY r.id DESC LIMIT 12");
    if ($recentRechargeQry) {
        while ($row = $db->sql_fetchrow($recentRechargeQry)) {
            if (!$row) { continue; }
            $recentRecharges[] = array('id'=>(int)$row['id'],'recharge_ref'=>(string)$row['recharge_ref'],'user_name'=>trim((string)$row['user_name']) !== '' ? (string)$row['user_name'] : ('UID ' . (int)$row['user_id']),'method_name'=>(string)$row['method_name'],'total_usd'=>(float)$row['total_usd'],'total_bob'=>(float)$row['total_bob'],'credits_to_add'=>(int)$row['credits_to_add'],'status'=>(string)$row['status'],'created_at'=>(string)$row['created_at']);
        }
    }
}

if (admin_table_exists('finance_payment_methods')) {
    $methodStats = admin_fetch_row_or_default(
        "SELECT COUNT(*) AS methods_total,
            COALESCE(SUM(CASE WHEN is_active=1 THEN 1 ELSE 0 END),0) AS methods_active
         FROM finance_payment_methods",
        array('methods_total'=>0,'methods_active'=>0)
    );
    $financeStats['methods_total'] = (int)$methodStats['methods_total'];
    $financeStats['methods_active'] = (int)$methodStats['methods_active'];
}

if (admin_table_exists('saas_tenants')) {
    $saasStats = admin_fetch_row_or_default(
        "SELECT COUNT(*) AS tenants_total,
            COALESCE(SUM(CASE WHEN status='active' THEN 1 ELSE 0 END),0) AS tenants_active,
            COALESCE(SUM(CASE WHEN status='trial' THEN 1 ELSE 0 END),0) AS tenants_trial,
            COALESCE(SUM(CASE WHEN status='suspended' THEN 1 ELSE 0 END),0) AS tenants_suspended,
            COALESCE(SUM(credits_balance),0) AS tenants_credits
         FROM saas_tenants",
        $saasStats
    );

    $tenantQry = $db->sql_query("SELECT id, tenant_key, display_name, status, credits_balance, updated_at
        FROM saas_tenants ORDER BY updated_at DESC, id DESC LIMIT 8");
    if ($tenantQry) {
        while ($row = $db->sql_fetchrow($tenantQry)) {
            if (!$row) { continue; }
            $recentTenants[] = array('id'=>(int)$row['id'],'tenant_key'=>(string)$row['tenant_key'],'display_name'=>(string)$row['display_name'],'status'=>(string)$row['status'],'credits_balance'=>(int)$row['credits_balance'],'updated_at'=>(string)$row['updated_at']);
        }
    }
}

if (admin_table_exists('saas_tenant_domains')) {
    $domainStats = admin_fetch_row_or_default(
        "SELECT COUNT(*) AS domains_total,
            COALESCE(SUM(CASE WHEN is_active=1 THEN 1 ELSE 0 END),0) AS domains_active,
            COALESCE(SUM(CASE WHEN is_primary=1 THEN 1 ELSE 0 END),0) AS domains_primary
         FROM saas_tenant_domains",
        array('domains_total'=>0,'domains_active'=>0,'domains_primary'=>0)
    );
    $saasStats['domains_total'] = (int)$domainStats['domains_total'];
    $saasStats['domains_active'] = (int)$domainStats['domains_active'];
    $saasStats['domains_primary'] = (int)$domainStats['domains_primary'];
}

if (admin_table_exists('login_attempts_logs')) {
    $securityStats = admin_fetch_row_or_default(
        "SELECT COUNT(*) AS failed_today FROM login_attempts_logs WHERE DATE(logs_date)='" . $db->SanitizeForSQL($today) . "'",
        $securityStats
    );
}

if (admin_table_exists('login_banned_ip')) {
    $bannedIpRow = admin_fetch_row_or_default("SELECT COUNT(*) AS banned_ips FROM login_banned_ip", array('banned_ips' => 0));
    $securityStats['banned_ips'] = (int)$bannedIpRow['banned_ips'];
}

if (admin_table_exists('support_ticket')) {
    $ticketRow = admin_fetch_row_or_default(
        "SELECT COUNT(*) AS tickets_total,
            COALESCE(SUM(CASE WHEN ticket_status IN ('open','customer-reply') THEN 1 ELSE 0 END),0) AS tickets_open,
            COALESCE(SUM(CASE WHEN ticket_status='answered' THEN 1 ELSE 0 END),0) AS tickets_answered
         FROM support_ticket",
        array('tickets_total'=>0,'tickets_open'=>0,'tickets_answered'=>0)
    );
    $securityStats['tickets_total'] = (int)$ticketRow['tickets_total'];
    $securityStats['tickets_open'] = (int)$ticketRow['tickets_open'];
    $securityStats['tickets_answered'] = (int)$ticketRow['tickets_answered'];
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo admin_h($panelSiteTitle); ?> | Admin</title>
    <link rel="icon" type="image/png" href="<?php echo admin_h($panelFaviconUrl); ?>">
    <link rel="shortcut icon" type="image/png" href="<?php echo admin_h($panelFaviconUrl); ?>">
    <link rel="stylesheet" href="<?php echo admin_h($baseUrl . 'bootstrap/font/font-awesome/css/font-awesome.min.css'); ?>">
    <style>
        :root {
            --bg: #071220;
            --line: #26486f;
            --txt: #eaf2ff;
            --muted: #97afce;
            --blue: #317ef5;
            --blue2: #5aabff;
            --drawer-top: 104px;
            --drawer-width: 236px;
            --drawer-offset: 236px;
            --content-min-width: 1240px;
            --scrollbar-thumb: rgba(140, 178, 234, .28);
            --scrollbar-thumb-hover: rgba(168, 202, 249, .42);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background:
                radial-gradient(980px 520px at 120% -10%, rgba(49, 126, 245, .22), transparent 62%),
                radial-gradient(760px 420px at -20% 120%, rgba(88, 208, 255, .14), transparent 60%),
                var(--bg);
            color: var(--txt);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
            overflow-y: hidden;
        }
        .layout { min-height: 100vh; display: flex; }
        .side {
            width: var(--drawer-width);
            flex: 0 0 var(--drawer-width);
            border-right: 1px solid #203e63;
            background: linear-gradient(180deg, #0a1728 0%, #0a1626 100%);
            position: fixed;
            left: 0;
            top: var(--drawer-top);
            height: calc(100vh - var(--drawer-top));
            bottom: auto;
            z-index: 30;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            box-shadow: none;
            transform: translateX(-100%);
            transition: transform .18s ease;
        }
        .side.open {
            transform: translateX(0);
        }
        .side-head {
            padding: 14px 14px 12px;
            min-height: 82px;
            border-bottom: 1px solid #24466d;
            position: sticky;
            top: 0;
            background: linear-gradient(180deg, rgba(13, 29, 50, 1) 0%, rgba(10, 23, 40, 1) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .side-brand {
            font-size: 19px;
            text-transform: uppercase;
            letter-spacing: .05em;
            font-weight: 800;
            color: #e6f0ff;
            line-height: 1.1;
        }
        .side-host {
            margin-top: 6px;
            font-size: 11px;
            color: #8fb2df;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-weight: 700;
        }
        .side-group { margin: 12px 0 0; }
        .side-title {
            margin: 0;
            padding: 0 16px 8px;
            color: #8ea7c8;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-weight: 700;
        }
        .side-links { list-style: none; margin: 0; padding: 0; }
        .side-link {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #d0e0f9;
            font-weight: 600;
            border-left: 3px solid transparent;
            padding: 10px 14px 10px 16px;
            transition: background .14s ease, border-color .14s ease;
        }
        .side-link-icon {
            width: 16px;
            flex: 0 0 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #92b8e7;
            font-size: 13px;
            line-height: 1;
        }
        .side-link-label {
            flex: 1 1 auto;
            min-width: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .side-link:hover .side-link-icon,
        .side-link.active .side-link-icon {
            color: #e5f1ff;
        }
        .side-link:hover { background: rgba(45, 88, 143, .24); border-left-color: #5387d1; color: #fff; }
        .side-link.active {
            color: #fff;
            background: linear-gradient(90deg, rgba(53, 126, 235, .34) 0%, rgba(45, 88, 143, .20) 100%);
            border-left-color: #6bb6ff;
        }
        .side-link-danger {
            color: #f87f91;
        }
        .side-link-danger .side-link-icon {
            color: #f87f91;
        }
        .side-link-danger:hover {
            color: #ffd1d8;
            border-left-color: #f87171;
            background: rgba(248, 113, 113, .16);
        }
        .side-link-danger:hover .side-link-icon {
            color: #ffd1d8;
        }
        .side-admin {
            margin-top: auto;
            position: sticky;
            bottom: 0;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 12px 14px;
            border-top: 1px solid #29486d;
            background: linear-gradient(180deg, #293447 0%, #253243 100%);
        }
        .side-admin-name {
            color: #eaf2ff;
            font-weight: 700;
            font-size: 13px;
            line-height: 1.2;
        }
        .side-admin-role {
            margin-top: 2px;
            color: #b4c4dc;
            font-size: 12px;
            line-height: 1.2;
        }
        .side-admin-btn {
            width: 30px;
            height: 30px;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #b5c6df;
            cursor: pointer;
        }
        .side-admin-btn:hover {
            background: rgba(144, 174, 214, .16);
            color: #f4f8ff;
        }
        .main {
            margin-left: 0;
            width: 100%;
            min-height: 100vh;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior-y: contain;
        }
        .header-stack {
            position: sticky;
            top: 0;
            z-index: 40;
            padding-top: 0;
            background: linear-gradient(180deg, rgba(7, 18, 32, .96) 0%, rgba(7, 18, 32, .88) 70%, rgba(7, 18, 32, 0) 100%);
        }
        .topbar-wrap { width: 100%; margin: 0; }
        .topbar {
            min-height: 72px;
            border: 1px solid #274a73;
            border-left: 0;
            border-right: 0;
            border-radius: 0;
            background: linear-gradient(180deg, rgba(17, 35, 59, .98) 0%, rgba(12, 27, 46, .98) 100%);
            box-shadow: none;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .topbar-left {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }
        .top-menu-btn {
            appearance: none;
            border: 1px solid #355a85;
            border-radius: 12px;
            background: linear-gradient(180deg, rgba(16, 39, 67, .95) 0%, rgba(13, 31, 53, .95) 100%);
            color: #f0f6ff;
            padding: 9px 14px;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: inset 0 0 0 1px rgba(119, 167, 232, .08);
        }
        .top-menu-btn:before { content: "\2630"; font-size: 14px; line-height: 1; }
        .topbar-brand {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 0;
        }
        .topbar-brand-title {
            color: #eef5ff;
            font-size: clamp(16px, 2vw, 24px);
            line-height: 1.12;
            letter-spacing: .01em;
            font-weight: 800;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .topbar-brand-sub {
            margin-top: 1px;
            color: #9bc2f1;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-size: clamp(11px, 1.05vw, 14px);
            font-weight: 700;
            white-space: nowrap;
        }
        .topbar-right {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-direction: row;
            gap: 10px;
            text-align: right;
            padding-right: 12px;
        }
        .topbar-context {
            color: #98b6da;
            text-transform: uppercase;
            letter-spacing: .12em;
            font-size: clamp(11px, 1vw, 14px);
            font-weight: 700;
            white-space: nowrap;
        }
        .wrap {
            width: 100%;
            margin: 0;
            border: 1px solid #214365;
            border-radius: 0;
            background: linear-gradient(180deg, rgba(11, 26, 44, .92) 0%, rgba(8, 20, 35, .90) 100%);
            padding: 0;
            flex: 0 0 auto;
            overflow-x: auto;
            overflow-y: visible;
            overscroll-behavior-x: contain;
            -webkit-overflow-scrolling: touch;
            scroll-behavior: auto;
            scrollbar-gutter: auto;
            touch-action: auto;
            scrollbar-width: auto;
            scrollbar-color: auto;
            transition: margin-left .18s ease, width .18s ease;
        }
        .wrap.drawer-open {
            margin-left: var(--drawer-offset);
            width: calc(100% - var(--drawer-offset));
        }
        .wrap::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        .wrap::-webkit-scrollbar-track { background: rgba(18, 36, 60, .35); }
        .wrap::-webkit-scrollbar-thumb {
            background: var(--scrollbar-thumb);
            border-radius: 999px;
            border: none;
        }
        .wrap::-webkit-scrollbar-thumb:hover { background: var(--scrollbar-thumb-hover); }
        .wrap-inner {
            min-width: 100%;
            width: 100%;
            min-height: 100%;
            padding: 14px 16px;
        }
        .side, .table-wrap {
            scrollbar-width: thin;
            scrollbar-color: var(--scrollbar-thumb) transparent;
        }
        .side::-webkit-scrollbar, .table-wrap::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .side::-webkit-scrollbar-track, .table-wrap::-webkit-scrollbar-track { background: transparent; }
        .side::-webkit-scrollbar-thumb, .table-wrap::-webkit-scrollbar-thumb {
            background: var(--scrollbar-thumb);
            border-radius: 999px;
        }
        .side::-webkit-scrollbar-thumb:hover, .table-wrap::-webkit-scrollbar-thumb:hover { background: var(--scrollbar-thumb-hover); }
        .side-overlay {
            display: none !important;
            position: fixed;
            top: var(--drawer-top);
            left: 0;
            right: 0;
            bottom: 0;
            background: transparent !important;
            z-index: 15;
            pointer-events: none !important;
        }
        .side-overlay.open { display: none !important; }
        .hero {
            border: 1px solid var(--line);
            border-radius: 6px;
            background: linear-gradient(180deg, rgba(26, 50, 82, .98) 0%, rgba(15, 30, 49, .98) 100%);
            box-shadow: 0 24px 52px rgba(4, 10, 24, .48);
            padding: 20px;
            display: flex;
            gap: 16px;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
        }
        .badge { display:inline-block; font-size:11px; text-transform:uppercase; letter-spacing:.05em; padding:6px 10px; border-radius:999px; border:1px solid #3a5d88; color:#b7d9ff; margin-bottom:8px; }
        h1 { margin: 0 0 8px; font-size: clamp(28px, 4.4vw, 42px); letter-spacing: .01em; }
        .hero p { margin: 0; color: var(--muted); line-height: 1.45; }
        .meta { margin-top: 8px; font-size: 13px; color: #adc2df; }
        .hero-actions { display: flex; flex-wrap: wrap; gap: 10px; }
        .btn { display:inline-flex; align-items:center; justify-content:center; min-height:40px; padding:0 14px; border-radius:10px; border:1px solid #3f5f8e; text-decoration:none; color:var(--txt); font-weight:600; transition: transform .14s ease, opacity .14s ease; background: rgba(8,20,35,.42); }
        .btn:hover { transform: translateY(-1px); opacity: .96; }
        .btn-primary { border-color:#2e6fd4; background: linear-gradient(180deg, var(--blue2) 0%, var(--blue) 100%); color:#fff; }
        .quick { margin-top: 14px; display:flex; flex-wrap:wrap; gap:8px; }
        .quick a { font-size:12px; border:1px solid #355881; border-radius:999px; padding:7px 11px; text-decoration:none; color:#c4daff; background: rgba(14,27,44,.55); }
        .quick a:hover { color:#fff; border-color:#4f7eb9; }
        .section { margin-top:14px; border:1px solid var(--line); border-radius:6px; background: linear-gradient(180deg, rgba(19, 38, 62, .96) 0%, rgba(16, 30, 48, .96) 100%); box-shadow: 0 16px 34px rgba(3, 9, 20, .34); padding:12px; }
        .section h2 { margin:0 0 12px; font-size:16px; letter-spacing:.02em; color:#cae3ff; }
        .admin-panel { display: none; }
        .admin-panel.is-active-panel { display: block; }
        .hero.admin-panel.is-active-panel { display: flex; }
        .section-config { display: none; }
        .section-config.is-active-panel { display: block; }
        .detail-block { display: none; }
        .detail-block.is-visible { display: block; }
        .section-hint {
            margin: -3px 0 12px;
            color: #94b2d7;
            font-size: 12px;
        }
        .module-embed-wrap {
            border: 1px solid #2b4b74;
            border-radius: 10px;
            overflow: hidden;
            background: linear-gradient(180deg, rgba(15, 31, 52, .96) 0%, rgba(11, 24, 40, .96) 100%);
        }
        .module-embed-frame {
            display: block;
            width: 100%;
            min-height: 280px;
            height: 340px;
            border: 0;
            background: #122543;
            overflow: hidden;
        }
        .detail-toggle-row {
            margin-top: 12px;
            display: flex;
            justify-content: flex-end;
        }
        .kpi-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:8px; }
        .kpi { border:1px solid #2b4a72; border-radius:6px; background: linear-gradient(180deg, rgba(26, 45, 72, .95) 0%, rgba(18, 32, 52, .95) 100%); padding:10px; }
        .kpi .label { font-size:12px; text-transform:uppercase; letter-spacing:.03em; color:#9fbbdd; margin-bottom:8px; }
        .kpi .value { font-size:28px; font-weight:700; line-height:1; color:#f1f7ff; }
        .kpi .sub { margin-top:8px; font-size:12px; color:#8ea8c8; }
        .grid-3 { margin-top:16px; display:grid; grid-template-columns: 1.5fr 1.5fr 1fr; gap:12px; }
        .panel { border:1px solid #28486f; border-radius:6px; background: linear-gradient(180deg, rgba(25, 44, 70, .96) 0%, rgba(17, 31, 50, .96) 100%); overflow:hidden; }
        .panel-head { padding:10px 12px; border-bottom:1px solid #2e4e76; display:flex; align-items:center; justify-content:space-between; gap:8px; }
        .panel-head h3 { margin:0; font-size:14px; color:#d8e8ff; }
        .table-wrap { max-height:400px; overflow:auto; }
        table { width:100%; border-collapse:collapse; }
        th, td { font-size:12px; padding:8px 10px; border-bottom:1px solid #263f61; text-align:left; white-space:nowrap; }
        th { color:#aecdff; background: rgba(18,34,56,.82); position:sticky; top:0; z-index:2; }
        td { color:#e8f0ff; }
        .empty { padding:14px 10px; color:#9eb3cf; font-size:12px; }
        .status { display:inline-block; border-radius:999px; padding:3px 8px; font-size:11px; border:1px solid transparent; }
        .status.ok { background: rgba(24, 201, 140, .16); color: #b9ffe5; border-color: rgba(24, 201, 140, .55); }
        .status.warn { background: rgba(249, 178, 71, .16); color: #ffe5c0; border-color: rgba(249, 178, 71, .55); }
        .status.err { background: rgba(255, 111, 136, .16); color: #ffd8e0; border-color: rgba(255, 111, 136, .55); }
        .status.muted { background: rgba(163, 181, 207, .16); color: #dce8f9; border-color: rgba(163, 181, 207, .45); }
        .alert {
            margin-top: 10px;
            border-radius: 8px;
            padding: 10px 12px;
            border: 1px solid transparent;
            font-size: 13px;
            font-weight: 700;
        }
        .alert-ok {
            color: #d5fff2;
            background: rgba(24, 201, 140, .16);
            border-color: rgba(24, 201, 140, .5);
        }
        .alert-err {
            color: #ffe1e8;
            background: rgba(255, 111, 136, .16);
            border-color: rgba(255, 111, 136, .5);
        }
        .cfg-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }
        .cfg-card {
            border: 1px solid #2a4a71;
            border-radius: 8px;
            background: linear-gradient(180deg, rgba(20, 37, 60, .94) 0%, rgba(15, 28, 46, .94) 100%);
            padding: 12px;
        }
        .cfg-card h3 {
            margin: 0 0 10px;
            font-size: 14px;
            color: #d7e8ff;
        }
        .cfg-row { margin-bottom: 10px; }
        .cfg-row:last-child { margin-bottom: 0; }
        .cfg-row label {
            display: block;
            margin-bottom: 5px;
            color: #aecdff;
            font-size: 12px;
            font-weight: 700;
        }
        .cfg-input-wrap {
            position: relative;
        }
        .cfg-input {
            width: 100%;
            min-height: 38px;
            border: 1px solid #35567e;
            border-radius: 8px;
            background: rgba(8, 19, 34, .74);
            color: #eaf3ff;
            padding: 8px 10px;
            font-size: 13px;
            outline: none;
        }
        .cfg-input-wrap .cfg-input {
            padding-right: 46px;
        }
        .cfg-input:focus {
            border-color: #5fa7ff;
            box-shadow: 0 0 0 2px rgba(95, 167, 255, .22);
        }
        .cfg-input-toggle {
            position: absolute;
            top: 50%;
            right: 6px;
            transform: translateY(-50%);
            width: 32px;
            height: 28px;
            border: 1px solid #3c618c;
            border-radius: 7px;
            background: rgba(16, 31, 50, .92);
            color: #b8d7ff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background .16s ease, border-color .16s ease, color .16s ease;
        }
        .cfg-input-toggle:hover {
            background: rgba(25, 48, 76, .98);
            border-color: #5fa7ff;
            color: #eef6ff;
        }
        .cfg-input-toggle:focus {
            outline: none;
            border-color: #5fa7ff;
            box-shadow: 0 0 0 2px rgba(95, 167, 255, .18);
        }
        .cfg-help {
            margin-top: 4px;
            font-size: 11px;
            color: #87a9cf;
        }
        .cfg-divider {
            margin: 12px 0;
            border-top: 1px solid rgba(72, 108, 149, .6);
        }
        .cfg-checkbox-line {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 10px;
            padding: 10px 12px;
            border: 1px solid #2f5078;
            border-radius: 8px;
            background: rgba(11, 24, 40, .45);
        }
        .cfg-checkbox-line input[type="checkbox"] {
            margin-top: 2px;
            width: 16px;
            height: 16px;
        }
        .cfg-checkbox-copy strong {
            display: block;
            color: #eaf3ff;
            font-size: 12px;
        }
        .cfg-checkbox-copy span {
            display: block;
            margin-top: 2px;
            color: #8fb0d2;
            font-size: 11px;
        }
        .cfg-preview {
            display: flex;
            gap: 10px;
            align-items: center;
            margin: 8px 0 10px;
            flex-wrap: wrap;
        }
        .cfg-logo-preview {
            max-width: 160px;
            max-height: 52px;
            border: 1px solid #2f4f76;
            border-radius: 6px;
            background: rgba(10, 23, 40, .65);
            padding: 4px 8px;
        }
        .cfg-favicon-preview {
            width: 32px;
            height: 32px;
            border: 1px solid #2f4f76;
            border-radius: 6px;
            background: rgba(10, 23, 40, .65);
            padding: 3px;
            object-fit: contain;
        }
        .foot { margin-top:14px; color:#93abc9; font-size:12px; text-align:right; }
        @media (max-width: 1180px) {
            :root { --content-min-width: 1120px; }
            .grid-3 { grid-template-columns: 1fr; }
            .cfg-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 980px) {
            :root { --content-min-width: 980px; }
            .side {
                width: min(84vw, var(--drawer-width));
                max-width: 88vw;
            }
            .wrap {
                transition: none;
                will-change: auto;
            }
            .side-overlay {
                background: rgba(2, 9, 20, .42);
                pointer-events: auto;
            }
            .side-overlay.open { display: block; }
            .topbar-wrap { margin-top: 0; }
            .topbar { min-height: 68px; }
            .topbar-brand-title { font-size: 20px; }
            .topbar-brand-sub { font-size: 12px; }
            .topbar-context { font-size: 12px; }
            .wrap { width: 100%; margin: 0; }
            .wrap-inner { padding: 10px; }
        }
        @media (max-width: 700px) {
            :root { --content-min-width: 860px; }
            .hero { padding:16px; }
            .section { padding:12px; }
            .topbar { padding: 11px 12px; border-radius: 0; }
            .topbar-right { justify-content: flex-start; }
            .topbar-brand-title { font-size: 18px; }
            .topbar-brand-sub { font-size: 11px; }
            .topbar-context { font-size: 11px; letter-spacing: .09em; }
            .wrap { width: 100%; margin: 0; }
            .wrap.drawer-open { margin-left: 0; width: 100%; }
            .wrap-inner { padding: 8px; }
            .side-overlay {
                background: rgba(2, 9, 20, .42);
            }
        }
        @media (max-width: 520px) {
            :root { --content-min-width: 760px; }
            .topbar-context { display: none; }
            .topbar { min-height: 64px; padding: 9px 10px; }
            .topbar-left { gap: 8px; }
            .top-menu-btn { padding: 7px 10px; }
        }
    </style>
</head>
<body>
    <div class="layout">
        <aside class="side" id="adminSide">
            <div class="side-head">
                <div class="side-brand"><?php echo admin_h($panelHeaderTitle); ?></div>
                <div class="side-host"><?php echo admin_h($panelBrandName); ?></div>
            </div>
            <div class="side-group">
                <p class="side-title">Inicio</p>
                <ul class="side-links">
                    <li><a class="side-link js-section-link active" href="#dashboard-main" data-target="dashboard-main"><span class="side-link-icon"><i class="fa fa-tachometer"></i></span><span class="side-link-label">Dashboard</span></a></li>
                </ul>
            </div>
            <div class="side-group">
                <p class="side-title">Clientes</p>
                <ul class="side-links">
                    <li><a class="side-link" href="<?php echo admin_h($baseUrl . 'index.php?p=users'); ?>"><span class="side-link-icon"><i class="fa fa-users"></i></span><span class="side-link-label">Usuarios</span></a></li>
                    <li><a class="side-link js-section-link" href="#support-main" data-target="support-main"><span class="side-link-icon"><i class="fa fa-life-ring"></i></span><span class="side-link-label">Soporte</span></a></li>
                </ul>
            </div>
            <div class="side-group">
                <p class="side-title">Operaciones</p>
                <ul class="side-links">
                    <li><a class="side-link js-section-link" href="#vpn-control-main" data-target="vpn-control-main"><span class="side-link-icon"><i class="fa fa-plus-circle"></i></span><span class="side-link-label">Agregar servidor</span></a></li>
                    <li><a class="side-link js-section-link" href="#server-status-main" data-target="server-status-main"><span class="side-link-icon"><i class="fa fa-server"></i></span><span class="side-link-label">Estado servidor</span></a></li>
                    <li><a class="side-link js-section-link" href="#vpn-control-main" data-target="vpn-control-main"><span class="side-link-icon"><i class="fa fa-random"></i></span><span class="side-link-label">Gestion VPS</span></a></li>
                    <li><a class="side-link js-section-link" href="#server-update-main" data-target="server-update-main"><span class="side-link-icon"><i class="fa fa-refresh"></i></span><span class="side-link-label">Actualizacion servidor</span></a></li>
                    <li><a class="side-link js-section-link" href="#notice-update-main" data-target="notice-update-main"><span class="side-link-icon"><i class="fa fa-bullhorn"></i></span><span class="side-link-label">Avisos</span></a></li>
                    <li><a class="side-link js-section-link" href="#ops-client-defaults" data-target="ops-client-defaults"><span class="side-link-icon"><i class="fa fa-key"></i></span><span class="side-link-label">Clave general clientes</span></a></li>
                    <li><a class="side-link js-section-link" href="#credit-logs-main" data-target="credit-logs-main"><span class="side-link-icon"><i class="fa fa-list-alt"></i></span><span class="side-link-label">Historial de creditos</span></a></li>
                    <li><a class="side-link js-section-link" href="#stats-main" data-target="stats-main" data-open-detail="1"><span class="side-link-icon"><i class="fa fa-line-chart"></i></span><span class="side-link-label">Estadisticas</span></a></li>
                </ul>
            </div>
            <div class="side-group">
                <p class="side-title">Configuracion</p>
                <ul class="side-links">
                    <li><a class="side-link js-section-link" href="#cfg-general" data-target="cfg-general"><span class="side-link-icon"><i class="fa fa-cogs"></i></span><span class="side-link-label">Ajustes</span></a></li>
                    <li><a class="side-link js-section-link" href="#cfg-security" data-target="cfg-security"><span class="side-link-icon"><i class="fa fa-shield"></i></span><span class="side-link-label">Seguridad control</span></a></li>
                    <li><a class="side-link js-section-link" href="#payment-methods-main" data-target="payment-methods-main"><span class="side-link-icon"><i class="fa fa-credit-card"></i></span><span class="side-link-label">Metodos de pago</span></a></li>
                    <li><a class="side-link js-section-link" href="#cfg-appearance" data-target="cfg-appearance"><span class="side-link-icon"><i class="fa fa-paint-brush"></i></span><span class="side-link-label">Apariencia</span></a></li>
                </ul>
            </div>
            <div class="side-group">
                <p class="side-title">Sesion</p>
                <ul class="side-links">
                    <li><a class="side-link side-link-danger" href="<?php echo admin_h($logoutUrl); ?>"><span class="side-link-icon"><i class="fa fa-sign-out"></i></span><span class="side-link-label">Cerrar sesion</span></a></li>
                </ul>
            </div>
            <div class="side-admin">
                <div>
                    <div class="side-admin-name">Administrador</div>
                    <div class="side-admin-role">Panel admin</div>
                </div>
                <button type="button" class="side-admin-btn" aria-label="Opciones admin" title="Opciones admin">
                    <i class="fa fa-ellipsis-v"></i>
                </button>
            </div>
        </aside>
        <div class="side-overlay" id="adminOverlay"></div>
        <main class="main">
            <div class="header-stack" id="adminHeaderStack">
                <div class="topbar-wrap">
                    <div class="topbar">
                        <div class="topbar-left">
                            <button type="button" class="top-menu-btn" id="adminMenuToggle">Menu</button>
                            <div class="topbar-brand">
                                <span class="topbar-brand-title"><?php echo admin_h($panelHeaderTitle); ?></span>
                                <span class="topbar-brand-sub"><?php echo admin_h($panelHeaderSubtitle); ?></span>
                            </div>
                        </div>
                        <div class="topbar-right">
                            <span class="topbar-context"><?php echo admin_h($panelHeaderContext); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="wrap">
                <div class="wrap-inner">
        <section class="hero admin-panel is-active-panel" id="dashboard-main">
            <div>
                <span class="badge"><?php echo admin_h($panelBrandName); ?></span>
                <h1>Admin Control Center</h1>
                <p>Tablero general de <strong><?php echo admin_h($currentHost); ?></strong> con estado operativo, usuarios, finanzas, SaaS y seguridad.</p>
                <div class="quick">
                    <a href="<?php echo admin_h($baseUrl . 'index.php?p=users'); ?>">Usuarios</a>
                    <a href="<?php echo admin_h($baseUrl . 'index.php?p=finance-admin'); ?>">Finanzas</a>
                    <a href="<?php echo admin_h($financeMethodsUrl); ?>">Metodos pago</a>
                    <a href="<?php echo admin_h($baseUrl . 'index.php?p=saas-tenants'); ?>">SaaS Tenants</a>
                    <a href="<?php echo admin_h($baseUrl . 'index.php?p=saas-control'); ?>">SaaS Control</a>
                    <a href="#cfg-security">Seguridad control</a>
                    <a href="<?php echo admin_h($baseUrl . 'index.php?p=supportticket'); ?>">Soporte</a>
                </div>
            </div>
            <div class="hero-actions">
                <a class="btn btn-primary" href="<?php echo admin_h($dashboardUrl); ?>">Abrir dashboard usuario</a>
                <a class="btn" href="<?php echo admin_h($refreshUrl); ?>">Refrescar</a>
                <a class="btn" href="<?php echo admin_h($logoutUrl); ?>">Cerrar sesion</a>
            </div>
        </section>

        <section class="section section-config admin-panel" id="cfg-general">
            <h2>Configuracion general</h2>
            <p class="section-hint">Control del nombre, titulos y etiquetas del panel admin.</p>
            <?php if ($adminNoticeSuccess !== '' && $adminNoticeScope === 'cfg-general'): ?>
                <div class="alert alert-ok"><?php echo admin_h($adminNoticeSuccess); ?></div>
            <?php endif; ?>
            <?php if ($adminNoticeError !== '' && $adminNoticeScope === 'cfg-general'): ?>
                <div class="alert alert-err"><?php echo admin_h($adminNoticeError); ?></div>
            <?php endif; ?>

            <div class="cfg-grid">
                <div class="cfg-card">
                    <h3>Configuracion general</h3>
                    <form method="post" action="<?php echo admin_h($baseUrl . 'admin.php#cfg-general'); ?>">
                        <input type="hidden" name="save_panel_identity" value="1">
                        <div class="cfg-row">
                            <label for="panel_brand_name">Nombre del panel</label>
                            <input class="cfg-input" id="panel_brand_name" name="panel_brand_name" value="<?php echo admin_h($panelBrandName); ?>" required>
                        </div>
                        <div class="cfg-row">
                            <label for="panel_site_title">Titulo del sitio</label>
                            <input class="cfg-input" id="panel_site_title" name="panel_site_title" value="<?php echo admin_h($panelSiteTitle); ?>" required>
                        </div>
                        <div class="cfg-row">
                            <label for="panel_admin_header_title">Encabezado admin (titulo)</label>
                            <input class="cfg-input" id="panel_admin_header_title" name="panel_admin_header_title" value="<?php echo admin_h($panelHeaderTitle); ?>" required>
                        </div>
                        <div class="cfg-row">
                            <label for="panel_admin_header_subtitle">Encabezado admin (subtitulo)</label>
                            <input class="cfg-input" id="panel_admin_header_subtitle" name="panel_admin_header_subtitle" value="<?php echo admin_h($panelHeaderSubtitle); ?>" required>
                        </div>
                        <div class="cfg-row">
                            <label for="panel_admin_context_label">Etiqueta de contexto</label>
                            <input class="cfg-input" id="panel_admin_context_label" name="panel_admin_context_label" value="<?php echo admin_h($panelHeaderContext); ?>" required>
                        </div>
                        <button class="btn btn-primary" type="submit">Guardar configuracion</button>
                    </form>
                </div>
            </div>
        </section>

        <section class="section section-config admin-panel" id="cfg-security">
            <h2>Seguridad de control</h2>
            <p class="section-hint">Reglas globales para <strong><?php echo admin_h($controlSecurityControlHost); ?></strong>: cuentas autorizadas, whitelist por IP real y accesos tecnicos del admin.</p>
            <?php if ($adminNoticeSuccess !== '' && $adminNoticeScope === 'cfg-security'): ?>
                <div class="alert alert-ok"><?php echo admin_h($adminNoticeSuccess); ?></div>
            <?php endif; ?>
            <?php if ($adminNoticeError !== '' && $adminNoticeScope === 'cfg-security'): ?>
                <div class="alert alert-err"><?php echo admin_h($adminNoticeError); ?></div>
            <?php endif; ?>

            <div class="cfg-grid">
                <div class="cfg-card">
                    <h3>Host de control y whitelist</h3>
                    <div class="cfg-help">Host actual: <strong><?php echo admin_h($currentHost); ?></strong> | Host central: <strong><?php echo admin_h($controlSecurityControlHost); ?></strong> | IP real detectada: <strong><?php echo admin_h($controlSecurityCurrentIp); ?></strong> | Contexto: <strong><?php echo $controlSecurityIsHost ? 'control host' : 'host secundario'; ?></strong>.</div>
                    <form method="post" action="<?php echo admin_h($baseUrl . 'admin.php#cfg-security'); ?>" autocomplete="off">
                        <input type="hidden" name="save_control_security" value="1">
                        <div class="cfg-row">
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                <input type="checkbox" name="control_admin_strict_mode" value="1" <?php echo $controlSecuritySettings['strict_mode'] ? 'checked' : ''; ?>>
                                <span>Activar modo estricto para admin.php en el host de control</span>
                            </label>
                            <div class="cfg-help">Cuando esta activo, solo entran cuentas permitidas por ID/email o superadmin segun la regla inferior.</div>
                        </div>
                        <div class="cfg-row">
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                <input type="checkbox" name="control_admin_require_superadmin" value="1" <?php echo $controlSecuritySettings['require_superadmin'] ? 'checked' : ''; ?>>
                                <span>Exigir superadmin/owner como regla base</span>
                            </label>
                            <div class="cfg-help">Si lo desactivas, debes definir IDs o emails autorizados en modo estricto.</div>
                        </div>
                        <div class="cfg-row">
                            <label for="control_admin_allowed_user_ids">User IDs autorizados</label>
                            <input class="cfg-input" id="control_admin_allowed_user_ids" name="control_admin_allowed_user_ids" value="<?php echo admin_h($controlSecuritySettings['allowed_user_ids']); ?>" placeholder="1, 7, 15">
                            <div class="cfg-help">Lista separada por comas. El user_id 1 suele ser el owner principal.</div>
                        </div>
                        <div class="cfg-row">
                            <label for="control_admin_allowed_emails">Emails autorizados</label>
                            <input class="cfg-input" id="control_admin_allowed_emails" name="control_admin_allowed_emails" value="<?php echo admin_h($controlSecuritySettings['allowed_emails']); ?>" placeholder="admin@programmit.com, ops@programmit.com">
                        </div>
                        <div class="cfg-row">
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                <input type="checkbox" name="control_admin_ip_whitelist_enabled" value="1" <?php echo $controlSecuritySettings['ip_whitelist_enabled'] ? 'checked' : ''; ?>>
                                <span>Activar whitelist por IP real</span>
                            </label>
                            <div class="cfg-help">Se detecta la IP real detras de Nginx Proxy Manager usando cabeceras de proxy con fallback seguro.</div>
                        </div>
                        <div class="cfg-row">
                            <label for="control_admin_ip_whitelist">IPs autorizadas</label>
                            <textarea class="cfg-input" id="control_admin_ip_whitelist" name="control_admin_ip_whitelist" rows="5" placeholder="104.152.50.156, 186.x.x.x"><?php echo admin_h($controlSecuritySettings['ip_whitelist']); ?></textarea>
                            <div class="cfg-help">Puedes usar comas, espacios o saltos de linea. Si activas la whitelist, tu IP actual se agrega automaticamente al guardar si todavia no esta.</div>
                        </div>
                        <div class="cfg-row">
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                <input type="checkbox" name="control_admin_allow_register" value="1" <?php echo $controlSecuritySettings['allow_register'] ? 'checked' : ''; ?>>
                                <span>Permitir registro desde el host de control</span>
                            </label>
                        </div>
                        <div class="cfg-row">
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                <input type="checkbox" name="control_admin_allow_magic_login" value="1" <?php echo $controlSecuritySettings['allow_magic_login'] ? 'checked' : ''; ?>>
                                <span>Permitir magic login en el host de control</span>
                            </label>
                        </div>
                        <button class="btn btn-primary" type="submit">Guardar seguridad de control</button>
                    </form>
                </div>
            </div>
        </section>

        <section class="section section-config admin-panel" id="ops-client-defaults">
            <h2>Operaciones de clientes</h2>
            <p class="section-hint">Funciones operativas del flujo de clientes que impactan altas, reactivaciones y sincronizacion.</p>
            <?php if ($adminNoticeSuccess !== '' && $adminNoticeScope === 'ops-client-defaults'): ?>
                <div class="alert alert-ok"><?php echo admin_h($adminNoticeSuccess); ?></div>
            <?php endif; ?>
            <?php if ($adminNoticeError !== '' && $adminNoticeScope === 'ops-client-defaults'): ?>
                <div class="alert alert-err" id="client-default-password-alert"><?php echo admin_h($adminNoticeError); ?></div>
            <?php endif; ?>
            <div class="cfg-grid">
                <div class="cfg-card">
                    <h3>Contrasena general de clientes</h3>
                    <div class="cfg-help">Contrasena actual: <strong><?php echo $clientDefaultPasswordConfigured ? 'Configurada' : 'No configurada'; ?></strong>.</div>
                    <form method="post" action="<?php echo admin_h($baseUrl . 'admin.php#ops-client-defaults'); ?>" autocomplete="off" onsubmit="var applyBox=document.getElementById('apply_client_default_password_existing'); if(applyBox && applyBox.checked){ return confirm('Se guardara la contrasena general nueva y tambien se aplicara a los clientes normales existentes, sincronizando luego a las VPS. ¿Continuar?'); } return true;">
                        <input type="hidden" name="save_client_defaults" value="1">
                        <div class="cfg-row">
                            <label for="client_default_password">Nueva contrasena general</label>
                            <div class="cfg-input-wrap">
                                <input class="cfg-input" id="client_default_password" name="client_default_password" type="password" autocomplete="new-password" placeholder="Minimo 8 caracteres">
                                <button class="cfg-input-toggle js-password-toggle" type="button" data-target="client_default_password" aria-label="Mostrar contrasena" title="Mostrar contrasena">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="cfg-row">
                            <label for="client_default_password_confirm">Confirmar contrasena general</label>
                            <div class="cfg-input-wrap">
                                <input class="cfg-input" id="client_default_password_confirm" name="client_default_password_confirm" type="password" autocomplete="new-password" placeholder="Repite la contrasena">
                                <button class="cfg-input-toggle js-password-toggle" type="button" data-target="client_default_password_confirm" aria-label="Mostrar contrasena" title="Mostrar contrasena">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="cfg-row">
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                <input type="checkbox" name="clear_client_default_password" value="1">
                                <span>Eliminar contrasena general actual</span>
                            </label>
                        </div>
                        <div class="cfg-checkbox-line">
                            <input type="checkbox" id="apply_client_default_password_existing" name="apply_client_default_password_existing" value="1">
                            <label class="cfg-checkbox-copy" for="apply_client_default_password_existing" style="margin:0; cursor:pointer;">
                                <strong>Aplicar tambien a clientes existentes</strong>
                                <span>Actualiza <?php echo admin_h($clientDefaultPasswordScopeLabel); ?> en PostgreSQL aunque esten activos, inactivos o congelados, y luego sincroniza a las VPS. Detectados: <?php echo admin_fmt_int($clientDefaultPasswordSummary['targeted']); ?> | Por actualizar: <?php echo admin_fmt_int($clientDefaultPasswordSummary['needs_update']); ?></span>
                            </label>
                        </div>
                        <button class="btn btn-primary" type="submit">Guardar contrasena general</button>
                    </form>
                </div>
            </div>
        </section>

        <section class="section section-config admin-panel" id="cfg-appearance">
            <h2>Apariencia</h2>
            <p class="section-hint">Logo panel/login, favicon e imagenes de marca.</p>
            <?php if ($adminNoticeSuccess !== '' && $adminNoticeScope === 'cfg-appearance'): ?>
                <div class="alert alert-ok"><?php echo admin_h($adminNoticeSuccess); ?></div>
            <?php endif; ?>
            <?php if ($adminNoticeError !== '' && $adminNoticeScope === 'cfg-appearance'): ?>
                <div class="alert alert-err"><?php echo admin_h($adminNoticeError); ?></div>
            <?php endif; ?>
            <div class="cfg-card">
                    <h3>Apariencia (logo, iconos y login)</h3>
                    <div class="cfg-preview">
                        <img class="cfg-logo-preview" src="<?php echo admin_h($panelLogoUrl); ?>" alt="logo panel">
                        <img class="cfg-logo-preview" src="<?php echo admin_h($panelLoginLogoUrl); ?>" alt="logo login">
                        <img class="cfg-favicon-preview" src="<?php echo admin_h($panelFaviconUrl); ?>" alt="favicon">
                    </div>
                    <form method="post" action="<?php echo admin_h($baseUrl . 'admin.php#cfg-appearance'); ?>" enctype="multipart/form-data">
                        <input type="hidden" name="save_panel_appearance" value="1">
                        <div class="cfg-row">
                            <label for="panel_logo_url">URL logo panel</label>
                            <input class="cfg-input" id="panel_logo_url" name="panel_logo_url" value="<?php echo admin_h($panelLogoStored); ?>" placeholder="logo/icon_panel.png o https://...">
                        </div>
                        <div class="cfg-row">
                            <label for="panel_login_logo_url">URL logo login</label>
                            <input class="cfg-input" id="panel_login_logo_url" name="panel_login_logo_url" value="<?php echo admin_h($panelLoginLogoStored); ?>" placeholder="logo/icon_panel.png o https://...">
                        </div>
                        <div class="cfg-row">
                            <label for="panel_favicon_url">URL favicon</label>
                            <input class="cfg-input" id="panel_favicon_url" name="panel_favicon_url" value="<?php echo admin_h($panelFaviconStored); ?>" placeholder="logo/favicon2.png o https://...">
                        </div>
                        <div class="cfg-row">
                            <label for="panel_logo_file">Subir logo panel (png/jpg/webp)</label>
                            <input class="cfg-input" type="file" id="panel_logo_file" name="panel_logo_file" accept=".png,.jpg,.jpeg,.webp">
                        </div>
                        <div class="cfg-row">
                            <label for="panel_login_logo_file">Subir logo login (png/jpg/webp)</label>
                            <input class="cfg-input" type="file" id="panel_login_logo_file" name="panel_login_logo_file" accept=".png,.jpg,.jpeg,.webp">
                        </div>
                        <div class="cfg-row">
                            <label for="panel_favicon_file">Subir favicon (png/ico/webp)</label>
                            <input class="cfg-input" type="file" id="panel_favicon_file" name="panel_favicon_file" accept=".png,.ico,.webp,.jpg,.jpeg">
                            <div class="cfg-help">Los archivos se publican en <code>logo/branding/</code>.</div>
                        </div>
                        <button class="btn btn-primary" type="submit">Guardar apariencia</button>
                    </form>
            </div>
        </section>

        <section class="section admin-panel" id="stats-main">
            <h2>Dashboard Ejecutivo</h2>
            <p class="section-hint">Resumen clave del estado actual del panel. Solo lo esencial para decisiones rapidas.</p>
            <div class="kpi-grid">
                <div class="kpi"><div class="label">Usuarios Live</div><div class="value"><?php echo admin_fmt_int($userStats['live_users']); ?></div><div class="sub">Activos operativos</div></div>
                <div class="kpi"><div class="label">Usuarios Totales</div><div class="value"><?php echo admin_fmt_int($userStats['total_users']); ?></div><div class="sub">Base completa</div></div>
                <div class="kpi"><div class="label">Tickets Abiertos</div><div class="value"><?php echo admin_fmt_int($securityStats['tickets_open']); ?></div><div class="sub">Pendientes de respuesta</div></div>
                <div class="kpi"><div class="label">Recargas Pendientes</div><div class="value"><?php echo admin_fmt_int($financeStats['recharges_pending']); ?></div><div class="sub">Esperando confirmacion</div></div>
                <div class="kpi"><div class="label">USD Hoy</div><div class="value"><?php echo admin_fmt_money($financeStats['usd_today']); ?></div><div class="sub">Monto diario</div></div>
                <div class="kpi"><div class="label">Tenants Activos</div><div class="value"><?php echo admin_fmt_int($saasStats['tenants_active']); ?></div><div class="sub">SaaS operativo</div></div>
                <div class="kpi"><div class="label">Servidores Activos</div><div class="value"><?php echo admin_fmt_int($infraStats['servers_active']); ?></div><div class="sub">Infraestructura online</div></div>
                <div class="kpi"><div class="label">Intentos Fallidos Hoy</div><div class="value"><?php echo admin_fmt_int($securityStats['failed_today']); ?></div><div class="sub">login_attempts_logs</div></div>
            </div>
            <div class="detail-toggle-row">
                <button type="button" class="btn" id="statsDetailToggle" data-open="0">Ver detalle avanzado</button>
            </div>
        </section>

        <section class="section admin-panel" id="payment-methods-main">
            <h2>Metodos de pago</h2>
            <p class="section-hint">Gestion directa dentro del panel administrativo (sin salir del admin).</p>
            <div class="module-embed-wrap">
                <iframe
                    id="financeMethodsFrame"
                    class="module-embed-frame"
                    title="Modulo metodos de pago"
                    src="about:blank"
                    data-src="<?php echo admin_h($financeMethodsEmbedUrl); ?>"
                    loading="lazy"
                    scrolling="no"
                ></iframe>
            </div>
        </section>

        <section class="section admin-panel" id="vpn-control-main">
            <h2>VPN Multi-VPS</h2>
            <p class="section-hint">Gestion directa de VPS y sincronizacion dentro del panel administrativo.</p>
            <div class="module-embed-wrap">
                <iframe
                    id="vpnControlFrame"
                    class="module-embed-frame"
                    title="Modulo VPN Multi-VPS"
                    src="about:blank"
                    data-src="<?php echo admin_h($vpnControlEmbedUrl); ?>"
                    loading="lazy"
                    scrolling="no"
                ></iframe>
            </div>
        </section>

        <section class="section admin-panel" id="server-status-main">
            <h2>Estado Servidor</h2>
            <p class="section-hint">Monitoreo legacy y estado operativo de servidores sin salir del admin.</p>
            <div class="module-embed-wrap">
                <iframe
                    id="serverStatusFrame"
                    class="module-embed-frame"
                    title="Modulo estado servidor"
                    src="about:blank"
                    data-src="<?php echo admin_h($serverStatusEmbedUrl); ?>"
                    loading="lazy"
                    scrolling="no"
                ></iframe>
            </div>
        </section>

        <section class="section admin-panel" id="server-update-main">
            <h2>Actualizacion Servidor</h2>
            <p class="section-hint">Gestion de actualizaciones y recursos tecnicos dentro del admin central.</p>
            <div class="module-embed-wrap">
                <iframe
                    id="serverUpdateFrame"
                    class="module-embed-frame"
                    title="Modulo actualizacion servidor"
                    src="about:blank"
                    data-src="<?php echo admin_h($serverUpdateEmbedUrl); ?>"
                    loading="lazy"
                    scrolling="no"
                ></iframe>
            </div>
        </section>

        <section class="section admin-panel" id="notice-update-main">
            <h2>Avisos</h2>
            <p class="section-hint">Administracion de avisos y publicaciones sin salir del panel admin.</p>
            <div class="module-embed-wrap">
                <iframe
                    id="noticeUpdateFrame"
                    class="module-embed-frame"
                    title="Modulo avisos"
                    src="about:blank"
                    data-src="<?php echo admin_h($noticeUpdateEmbedUrl); ?>"
                    loading="lazy"
                    scrolling="no"
                ></iframe>
            </div>
        </section>

        <section class="section admin-panel" id="credit-logs-main">
            <h2>Historial de Creditos</h2>
            <p class="section-hint">Consulta de movimientos y trazabilidad de creditos desde el admin central.</p>
            <div class="module-embed-wrap">
                <iframe
                    id="creditLogsFrame"
                    class="module-embed-frame"
                    title="Modulo historial de creditos"
                    src="about:blank"
                    data-src="<?php echo admin_h($creditLogsEmbedUrl); ?>"
                    loading="lazy"
                    scrolling="no"
                ></iframe>
            </div>
        </section>

        <div class="detail-block" id="stats-detail">
        <section class="section">
            <h2>Finanzas, SaaS e Infra</h2>
            <div class="kpi-grid">
                <div class="kpi"><div class="label">Recargas Totales</div><div class="value"><?php echo admin_fmt_int($financeStats['recharges_total']); ?></div><div class="sub">finance_recharges</div></div>
                <div class="kpi"><div class="label">Pendientes</div><div class="value"><?php echo admin_fmt_int($financeStats['recharges_pending']); ?></div><div class="sub">Esperando confirmacion</div></div>
                <div class="kpi"><div class="label">Pagadas</div><div class="value"><?php echo admin_fmt_int($financeStats['recharges_paid']); ?></div><div class="sub">Estado paid</div></div>
                <div class="kpi"><div class="label">Fallidas</div><div class="value"><?php echo admin_fmt_int($financeStats['recharges_failed']); ?></div><div class="sub">failed/expired/cancelled</div></div>
                <div class="kpi"><div class="label">USD Pagado Total</div><div class="value"><?php echo admin_fmt_money($financeStats['paid_usd_total']); ?></div><div class="sub">Solo recargas paid</div></div>
                <div class="kpi"><div class="label">USD Hoy</div><div class="value"><?php echo admin_fmt_money($financeStats['usd_today']); ?></div><div class="sub">DATE(created_at)=hoy</div></div>
                <div class="kpi"><div class="label">Creditos Emitidos</div><div class="value"><?php echo admin_fmt_int($financeStats['credits_issued']); ?></div><div class="sub">Desde recargas paid</div></div>
                <div class="kpi"><div class="label">Metodos Activos</div><div class="value"><?php echo admin_fmt_int($financeStats['methods_active']); ?></div><div class="sub"><?php echo admin_fmt_int($financeStats['methods_total']); ?> metodos registrados</div></div>
                <div class="kpi"><div class="label">Servidores Config</div><div class="value"><?php echo admin_fmt_int($infraStats['servers_total']); ?></div><div class="sub">Tabla server_list</div></div>
                <div class="kpi"><div class="label">Servidores Activos</div><div class="value"><?php echo admin_fmt_int($infraStats['servers_active']); ?></div><div class="sub">status=1</div></div>
                <div class="kpi"><div class="label">Tenants Totales</div><div class="value"><?php echo admin_fmt_int($saasStats['tenants_total']); ?></div><div class="sub"><?php echo admin_fmt_int($saasStats['tenants_active']); ?> activos / <?php echo admin_fmt_int($saasStats['tenants_trial']); ?> trial</div></div>
                <div class="kpi"><div class="label">Dominios SaaS Activos</div><div class="value"><?php echo admin_fmt_int($saasStats['domains_active']); ?></div><div class="sub"><?php echo admin_fmt_int($saasStats['domains_total']); ?> total / <?php echo admin_fmt_int($saasStats['domains_primary']); ?> primary</div></div>
            </div>
        </section>
        <div class="grid-3">
            <section class="panel">
                <div class="panel-head"><h3>Ultimas Recargas</h3></div>
                <div class="table-wrap">
                    <?php if (!empty($recentRecharges)): ?>
                    <table>
                        <thead><tr><th>ID</th><th>Usuario</th><th>Metodo</th><th>USD</th><th>Creditos</th><th>Estado</th><th>Fecha</th></tr></thead>
                        <tbody>
                            <?php foreach ($recentRecharges as $row): ?>
                            <tr>
                                <td><?php echo admin_h($row['id']); ?></td>
                                <td><?php echo admin_h($row['user_name']); ?></td>
                                <td><?php echo admin_h($row['method_name']); ?></td>
                                <td><?php echo admin_h(admin_fmt_money($row['total_usd'])); ?></td>
                                <td><?php echo admin_h(admin_fmt_int($row['credits_to_add'])); ?></td>
                                <td><span class="status <?php echo admin_h(admin_recharge_badge_class($row['status'])); ?>"><?php echo admin_h(strtoupper((string)$row['status'])); ?></span></td>
                                <td><?php echo admin_h(admin_fmt_datetime($row['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?><div class="empty">Sin registros de recargas.</div><?php endif; ?>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head"><h3>Usuarios Recientes</h3></div>
                <div class="table-wrap">
                    <?php if (!empty($recentUsers)): ?>
                    <table>
                        <thead><tr><th>ID</th><th>User</th><th>Rol</th><th>Creditos</th><th>Estado</th><th>Last Login</th></tr></thead>
                        <tbody>
                            <?php foreach ($recentUsers as $row): ?>
                            <?php
                                $stateText = (string)$row['status'];
                                if ((int)$row['is_freeze'] === 1) { $stateText = 'freeze'; }
                                elseif ((int)$row['is_active'] === 0 && strtolower((string)$row['status']) !== 'suspended') { $stateText = 'inactive'; }
                            ?>
                            <tr>
                                <td><?php echo admin_h($row['user_id']); ?></td>
                                <td><?php echo admin_h($row['user_name']); ?></td>
                                <td><?php echo admin_h($row['user_level']); ?></td>
                                <td><?php echo admin_h(admin_fmt_int($row['credits'])); ?></td>
                                <td><?php echo admin_h(strtolower($stateText)); ?></td>
                                <td><?php echo admin_h(admin_fmt_datetime($row['lastlogin'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?><div class="empty">Sin datos de usuarios.</div><?php endif; ?>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head"><h3>Top Creditos</h3></div>
                <div class="table-wrap">
                    <?php if (!empty($topCredits)): ?>
                    <table>
                        <thead><tr><th>Usuario</th><th>Rol</th><th>Creditos</th></tr></thead>
                        <tbody>
                            <?php foreach ($topCredits as $row): ?>
                            <tr>
                                <td><?php echo admin_h($row['user_name']); ?></td>
                                <td><?php echo admin_h($row['user_level']); ?></td>
                                <td><?php echo admin_h(admin_fmt_int($row['credits'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?><div class="empty">Sin datos de creditos.</div><?php endif; ?>
                </div>
            </section>
        </div>

        <div class="grid-3">
            <section class="panel" style="grid-column: span 2;">
                <div class="panel-head"><h3>SaaS Tenants Recientes</h3></div>
                <div class="table-wrap">
                    <?php if (!empty($recentTenants)): ?>
                    <table>
                        <thead><tr><th>ID</th><th>Tenant Key</th><th>Display</th><th>Status</th><th>Credits</th><th>Updated</th></tr></thead>
                        <tbody>
                            <?php foreach ($recentTenants as $row): ?>
                            <tr>
                                <td><?php echo admin_h($row['id']); ?></td>
                                <td><?php echo admin_h($row['tenant_key']); ?></td>
                                <td><?php echo admin_h($row['display_name']); ?></td>
                                <td><?php echo admin_h($row['status']); ?></td>
                                <td><?php echo admin_h(admin_fmt_int($row['credits_balance'])); ?></td>
                                <td><?php echo admin_h(admin_fmt_datetime($row['updated_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?><div class="empty">Sin datos de tenants o modulo SaaS no inicializado.</div><?php endif; ?>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head"><h3>System Info</h3></div>
                <div class="table-wrap">
                    <table><tbody>
                        <tr><th>PHP</th><td><?php echo admin_h(PHP_VERSION); ?></td></tr>
                        <tr><th>DB Driver</th><td><?php echo admin_h($dbDriverLabel !== '' ? $dbDriverLabel : 'n/a'); ?></td></tr>
                        <tr><th>DB Host</th><td><?php echo admin_h($dbHostLabel !== '' ? $dbHostLabel : 'n/a'); ?></td></tr>
                        <tr><th>DB Name</th><td><?php echo admin_h($dbNameLabel !== '' ? $dbNameLabel : 'n/a'); ?></td></tr>
                        <tr><th>Host</th><td><?php echo admin_h($currentHost); ?></td></tr>
                        <tr><th>Control Host</th><td><?php echo admin_h($controlSecurityControlHost); ?></td></tr>
                        <tr><th>Admin IP Real</th><td><?php echo admin_h($controlSecurityCurrentIp); ?></td></tr>
                        <tr><th>Admin User</th><td><?php echo admin_h($user_name_2 . ' (' . $user_level_2 . ')'); ?></td></tr>
                        <tr><th>Generated</th><td><?php echo admin_h($generatedAt); ?></td></tr>
                        <tr><th>Support Tickets</th><td><?php echo admin_h(admin_fmt_int($securityStats['tickets_total']) . ' total / ' . admin_fmt_int($securityStats['tickets_answered']) . ' answered'); ?></td></tr>
                        <tr><th>Tenants Credits</th><td><?php echo admin_h(admin_fmt_int($saasStats['tenants_credits'])); ?></td></tr>
                    </tbody></table>
                </div>
            </section>
        </div>
        </div>

        <section class="section admin-panel" id="support-main">
            <h2>Soporte</h2>
            <div class="kpi-grid">
                <div class="kpi">
                    <div class="label">Tickets Totales</div>
                    <div class="value"><?php echo admin_fmt_int($securityStats['tickets_total']); ?></div>
                    <div class="sub">Todos los tickets registrados</div>
                </div>
                <div class="kpi">
                    <div class="label">Tickets Abiertos</div>
                    <div class="value"><?php echo admin_fmt_int($securityStats['tickets_open']); ?></div>
                    <div class="sub">Estados open / customer-reply</div>
                </div>
                <div class="kpi">
                    <div class="label">Tickets Respondidos</div>
                    <div class="value"><?php echo admin_fmt_int($securityStats['tickets_answered']); ?></div>
                    <div class="sub">Estado answered</div>
                </div>
                <div class="kpi">
                    <div class="label">Centro de soporte</div>
                    <div class="value">-></div>
                    <div class="sub"><a class="btn" href="<?php echo admin_h($baseUrl . 'index.php?p=supportticket'); ?>">Abrir soporte completo</a></div>
                </div>
            </div>
        </section>

        <div class="foot">Admin dashboard cache: 15s | Build: <?php echo admin_h($generatedAt); ?></div>
                </div>
            </div>
        </main>
    </div>
    <script>
    (function () {
        var side = document.getElementById('adminSide');
        var overlay = document.getElementById('adminOverlay');
        var toggle = document.getElementById('adminMenuToggle');
        var contentWrap = document.querySelector('.wrap');
        var mainScroll = document.querySelector('.main');
        var headerStack = document.getElementById('adminHeaderStack');
        var sectionLinks = Array.prototype.slice.call(document.querySelectorAll('.js-section-link'));
        var panels = Array.prototype.slice.call(document.querySelectorAll('.admin-panel'));
        var statsDetail = document.getElementById('stats-detail');
        var statsDetailToggle = document.getElementById('statsDetailToggle');
        var statsDetailToggleRow = statsDetailToggle ? statsDetailToggle.parentElement : null;
        var financeMethodsFrame = document.getElementById('financeMethodsFrame');
        var vpnControlFrame = document.getElementById('vpnControlFrame');
        var activeTarget = 'dashboard-main';
        if (!side || !overlay || !toggle || !contentWrap || !mainScroll) { return; }

        function syncDrawerTop() {
            if (!headerStack) { return; }
            var height = headerStack.getBoundingClientRect().height;
            if (!isFinite(height) || height < 0) { height = 0; }
            document.documentElement.style.setProperty('--drawer-top', Math.ceil(height) + 'px');
            var sideWidth = side.getBoundingClientRect().width;
            if (isFinite(sideWidth) && sideWidth > 0) {
                document.documentElement.style.setProperty('--drawer-offset', Math.ceil(sideWidth) + 'px');
            }
        }

        function closeSide() {
            side.classList.remove('open');
            overlay.classList.remove('open');
            contentWrap.classList.remove('drawer-open');
        }
        function openSide() {
            side.classList.add('open');
            overlay.classList.add('open');
            contentWrap.classList.add('drawer-open');
        }

        function setStatsDetailOpen(opened) {
            if (!statsDetail || !statsDetailToggle) { return; }
            var canOpen = activeTarget === 'stats-main';
            var shouldOpen = canOpen && opened;
            if (statsDetailToggleRow) {
                statsDetailToggleRow.style.display = canOpen ? 'flex' : 'none';
            }
            if (shouldOpen) {
                statsDetail.classList.add('is-visible');
                statsDetailToggle.setAttribute('data-open', '1');
                statsDetailToggle.textContent = 'Ocultar detalle avanzado';
            } else {
                statsDetail.classList.remove('is-visible');
                statsDetailToggle.setAttribute('data-open', '0');
                statsDetailToggle.textContent = 'Ver detalle avanzado';
            }
        }

        function ensureEmbedLoaded(targetId) {
            var targetFrame = null;
            if (targetId === 'payment-methods-main') {
                targetFrame = financeMethodsFrame;
            } else if (targetId === 'vpn-control-main') {
                targetFrame = vpnControlFrame;
            } else if (targetId === 'server-status-main') {
                targetFrame = serverStatusFrame;
            } else if (targetId === 'server-update-main') {
                targetFrame = serverUpdateFrame;
            } else if (targetId === 'notice-update-main') {
                targetFrame = noticeUpdateFrame;
            } else if (targetId === 'credit-logs-main') {
                targetFrame = creditLogsFrame;
            }
            if (!targetFrame) { return; }
            var nextSrc = (targetFrame.getAttribute('data-src') || '').trim();
            if (nextSrc === '') { return; }
            var currentSrc = (targetFrame.getAttribute('src') || '').trim();
            if (currentSrc === '' || currentSrc === 'about:blank') {
                targetFrame.setAttribute('src', nextSrc);
            }
        }

        function activateSection(targetId, opts) {
            var found = false;
            panels.forEach(function (panel) {
                var match = panel && panel.id === targetId;
                panel.classList.toggle('is-active-panel', match);
                if (match) { found = true; }
            });
            if (!found) { return false; }

            activeTarget = targetId;
            ensureEmbedLoaded(targetId);
            markSectionLink(targetId);
            if (mainScroll && typeof mainScroll.scrollTo === 'function') {
                mainScroll.scrollTo({ top: 0, left: 0, behavior: 'auto' });
            } else {
                mainScroll.scrollTop = 0;
            }
            setStatsDetailOpen(!!(opts && opts.openDetail));
            if (window.history && typeof window.history.replaceState === 'function') {
                var nextHash = '#' + targetId;
                if (window.location.hash !== nextHash) {
                    window.history.replaceState(null, '', nextHash);
                }
            } else if (window.location.hash !== '#' + targetId) {
                window.location.hash = targetId;
            }
            return true;
        }

        function applyEmbedFrameHeight(frame, rawHeight, selectors, minSourceHeight, minHeight, maxHeight) {
            if (!frame) { return; }
            var height = parseInt(rawHeight, 10);
            if (!isFinite(height) || height < 120) { height = 0; }
            var directHeight = 0;
            try {
                var doc = frame.contentDocument || (frame.contentWindow ? frame.contentWindow.document : null);
                if (doc) {
                    var probe = null;
                    if (doc.querySelector && selectors && selectors.length) {
                        selectors.forEach(function (selector) {
                            if (!probe && selector) {
                                probe = doc.querySelector(selector);
                            }
                        });
                    }
                    if (!probe) {
                        probe = doc.body;
                    }
                    if (probe) {
                        directHeight = Math.max(
                            probe.scrollHeight ? Math.ceil(probe.scrollHeight) : 0,
                            probe.getBoundingClientRect ? Math.ceil(probe.getBoundingClientRect().height) : 0
                        );
                    }
                }
            } catch (err) {
                directHeight = 0;
            }
            var sourceHeight = Math.max(directHeight, height);
            if (!isFinite(sourceHeight) || sourceHeight < minSourceHeight) { return; }
            var current = parseInt(frame.style.height || '0', 10);
            var nextHeight = Math.max(minHeight, Math.min(maxHeight, sourceHeight + 8));
            if (isFinite(current) && current > 0 && Math.abs(nextHeight - current) < 8) { return; }
            frame.style.height = String(nextHeight) + 'px';
        }

        function bindEmbedFrame(frame, messageType, selectors, minSourceHeight, minHeight, maxHeight) {
            if (!frame) { return; }
            frame.addEventListener('load', function () {
                setTimeout(function () {
                    try {
                        var doc = frame.contentDocument || (frame.contentWindow ? frame.contentWindow.document : null);
                        if (!doc) { return; }
                        var probe = null;
                        if (doc.querySelector && selectors && selectors.length) {
                            selectors.forEach(function (selector) {
                                if (!probe && selector) {
                                    probe = doc.querySelector(selector);
                                }
                            });
                        }
                        if (!probe) {
                            probe = doc.body;
                        }
                        if (!probe) { return; }
                        var nextHeight = probe.getBoundingClientRect ? Math.ceil(probe.getBoundingClientRect().height) : 0;
                        if (!nextHeight || nextHeight < 1) {
                            nextHeight = probe.scrollHeight ? probe.scrollHeight : 0;
                        }
                        applyEmbedFrameHeight(frame, nextHeight, selectors, minSourceHeight, minHeight, maxHeight);
                    } catch (err) {
                        // ignore cross-frame sizing errors
                    }
                }, 140);
            });
        }

        window.addEventListener('message', function (event) {
            var data = event.data || {};
            if (!data) { return; }
            if (financeMethodsFrame && event.source === financeMethodsFrame.contentWindow && data.type === 'finance_embed_height') {
                applyEmbedFrameHeight(financeMethodsFrame, data.height, ['.pay-card', '.pay-shell'], 220, 260, 2800);
                return;
            }
            if (vpnControlFrame && event.source === vpnControlFrame.contentWindow && data.type === 'vpn_embed_height') {
                applyEmbedFrameHeight(vpnControlFrame, data.height, ['.vpn-embed-shell', '.container-fluid', '.page-content'], 220, 260, 6400);
                return;
            }
            if (data.type === 'programmit_admin_embed_height') {
                if (serverStatusFrame && event.source === serverStatusFrame.contentWindow) {
                    applyEmbedFrameHeight(serverStatusFrame, data.height, ['.page-content', '.container-fluid', 'body'], 260, 320, 2600);
                    return;
                }
                if (serverUpdateFrame && event.source === serverUpdateFrame.contentWindow) {
                    applyEmbedFrameHeight(serverUpdateFrame, data.height, ['.page-content', '.container-fluid', 'body'], 260, 320, 4600);
                    return;
                }
                if (noticeUpdateFrame && event.source === noticeUpdateFrame.contentWindow) {
                    applyEmbedFrameHeight(noticeUpdateFrame, data.height, ['.page-content', '.container-fluid', 'body'], 260, 320, 4600);
                    return;
                }
                if (creditLogsFrame && event.source === creditLogsFrame.contentWindow) {
                    applyEmbedFrameHeight(creditLogsFrame, data.height, ['.page-content', '.container-fluid', 'body'], 260, 320, 3600);
                }
            }
        });

        bindEmbedFrame(financeMethodsFrame, 'finance_embed_height', ['.pay-card', '.pay-shell'], 220, 260, 2800);
        bindEmbedFrame(vpnControlFrame, 'vpn_embed_height', ['.vpn-embed-shell', '.container-fluid', '.page-content'], 220, 260, 6400);
        bindEmbedFrame(serverStatusFrame, 'programmit_admin_embed_height', ['.page-content', '.container-fluid', 'body'], 260, 320, 2600);
        bindEmbedFrame(serverUpdateFrame, 'programmit_admin_embed_height', ['.page-content', '.container-fluid', 'body'], 260, 320, 4600);
        bindEmbedFrame(noticeUpdateFrame, 'programmit_admin_embed_height', ['.page-content', '.container-fluid', 'body'], 260, 320, 4600);
        bindEmbedFrame(creditLogsFrame, 'programmit_admin_embed_height', ['.page-content', '.container-fluid', 'body'], 260, 320, 3600);

        function markSectionLink(targetId) {
            sectionLinks.forEach(function (link) {
                var match = (link.getAttribute('data-target') || '') === targetId;
                if (match) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        }

        toggle.addEventListener('click', function () {
            if (side.classList.contains('open')) {
                closeSide();
            } else {
                syncDrawerTop();
                openSide();
            }
        });
        sectionLinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                var targetId = (link.getAttribute('data-target') || '').trim();
                if (targetId === '') { return; }
                var openDetail = (link.getAttribute('data-open-detail') || '') === '1';
                activateSection(targetId, { openDetail: openDetail });
            });
        });
        if (statsDetailToggle) {
            statsDetailToggle.addEventListener('click', function () {
                var opened = statsDetailToggle.getAttribute('data-open') === '1';
                setStatsDetailOpen(!opened);
            });
            setStatsDetailOpen(false);
        }
        overlay.addEventListener('click', closeSide);
        var hasInitialView = false;
        if (window.location.hash) {
            var hashTarget = window.location.hash.replace('#', '').trim();
            if (hashTarget !== '') {
                hasInitialView = activateSection(hashTarget, { openDetail: hashTarget === 'stats-main' });
            }
        }
        if (!hasInitialView) {
            activateSection('dashboard-main', { openDetail: false });
        }
        syncDrawerTop();
        if (window.innerWidth <= 980) {
            closeSide();
        }
        window.addEventListener('load', syncDrawerTop);
        setTimeout(syncDrawerTop, 120);
        window.addEventListener('resize', function () {
            syncDrawerTop();
            if (window.innerWidth > 980) {
                closeSide();
            }
        });

        Array.prototype.slice.call(document.querySelectorAll('.js-password-toggle')).forEach(function (button) {
            button.addEventListener('click', function () {
                var targetId = (button.getAttribute('data-target') || '').trim();
                if (targetId === '') { return; }
                var input = document.getElementById(targetId);
                if (!input) { return; }
                var nextType = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', nextType);
                var icon = button.querySelector('i');
                var isVisible = nextType === 'text';
                if (icon) {
                    icon.className = isVisible ? 'fa fa-eye-slash' : 'fa fa-eye';
                }
                button.setAttribute('aria-label', isVisible ? 'Ocultar contrasena' : 'Mostrar contrasena');
                button.setAttribute('title', isVisible ? 'Ocultar contrasena' : 'Mostrar contrasena');
            });
        });

        var clientDefaultsAlert = document.getElementById('client-default-password-alert');
        if (clientDefaultsAlert) {
            ['client_default_password', 'client_default_password_confirm'].forEach(function (id) {
                var input = document.getElementById(id);
                if (!input) { return; }
                input.addEventListener('input', function () {
                    clientDefaultsAlert.style.display = 'none';
                });
            });
            var clearCheckbox = document.querySelector('input[name="clear_client_default_password"]');
            if (clearCheckbox) {
                clearCheckbox.addEventListener('change', function () {
                    clientDefaultsAlert.style.display = 'none';
                });
            }
            var applyCheckbox = document.getElementById('apply_client_default_password_existing');
            if (applyCheckbox) {
                applyCheckbox.addEventListener('change', function () {
                    clientDefaultsAlert.style.display = 'none';
                });
            }
        }
    })();
    </script>
</body>
</html>
<?php
if ($cacheEnabled && $cacheFile !== '') {
    $body = ob_get_contents();
    if (is_string($body) && $body !== '') {
        @file_put_contents($cacheFile, $body, LOCK_EX);
    }
    ob_end_flush();
}
?>
