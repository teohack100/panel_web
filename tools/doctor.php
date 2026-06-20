<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit('CLI only');
}

require_once __DIR__ . '/../includes/config.php';

function programmit_doctor_write($status, $label, $detail = '')
{
    $line = str_pad('[' . $status . ']', 8, ' ', STR_PAD_RIGHT) . ' ' . $label;
    if ($detail !== '') {
        $line .= ' -> ' . $detail;
    }
    fwrite(STDOUT, $line . PHP_EOL);
}

function programmit_doctor_env($key)
{
    if (function_exists('programmit_env_get')) {
        return trim((string)programmit_env_get($key));
    }
    return '';
}

function programmit_doctor_setting($db, $key, $default = '')
{
    if (function_exists('programmit_saas_get_setting')) {
        return trim((string)programmit_saas_get_setting($db, $key, $default));
    }
    return trim((string)$default);
}

function programmit_doctor_is_truthy($value)
{
    $value = strtolower(trim((string)$value));
    return in_array($value, array('1', 'true', 'yes', 'on', 'enabled'), true);
}

function programmit_doctor_runtime_user()
{
    if (DIRECTORY_SEPARATOR === '\\') {
        $windowsUser = getenv('USERNAME');
        return is_string($windowsUser) && $windowsUser !== '' ? $windowsUser : 'windows-user';
    }

    if (function_exists('posix_geteuid') && function_exists('posix_getpwuid')) {
        $processInfo = @posix_getpwuid(posix_geteuid());
        if (is_array($processInfo) && !empty($processInfo['name'])) {
            return (string)$processInfo['name'];
        }
    }

    $unixUser = getenv('USER');
    return is_string($unixUser) && $unixUser !== '' ? $unixUser : 'unknown';
}

function programmit_doctor_can_write_dir($absolutePath)
{
    if (!is_dir($absolutePath) || !is_writable($absolutePath)) {
        return false;
    }

    $probePath = @tempnam($absolutePath, 'doctor_');
    if (!is_string($probePath) || $probePath === '') {
        return false;
    }

    $written = @file_put_contents($probePath, 'ok');
    $deleted = @unlink($probePath);

    return $written !== false && $deleted;
}

function programmit_doctor_resolve_host($host)
{
    $host = trim((string)$host);
    if ($host === '') {
        return '';
    }

    $resolved = @gethostbyname($host);
    if (!is_string($resolved) || $resolved === '' || strcasecmp($resolved, $host) === 0) {
        return '';
    }

    return $resolved;
}

$projectRoot = dirname(__DIR__);
$failures = 0;
$warnings = 0;
$runtimeUser = programmit_doctor_runtime_user();

programmit_doctor_write('INFO', 'Proyecto', $projectRoot);
programmit_doctor_write('INFO', 'DB activa', $DB_driver . '://' . $DB_host . ':' . $DB_port . '/' . $DB_name);
programmit_doctor_write('INFO', 'Usuario CLI', $runtimeUser);

if (DIRECTORY_SEPARATOR !== '\\' && $runtimeUser === 'root') {
    $warnings++;
    programmit_doctor_write('WARN', 'Permisos de runtime', 'Estas corriendo el doctor como root; repite con sudo -u www-data php tools/doctor.php para validar escritura real');
}

$envLocalPath = $projectRoot . DIRECTORY_SEPARATOR . '.env.local';
$envPath = $projectRoot . DIRECTORY_SEPARATOR . '.env';
if (is_file($envLocalPath)) {
    programmit_doctor_write('OK', '.env.local detectado', $envLocalPath);
} elseif (is_file($envPath)) {
    programmit_doctor_write('OK', '.env detectado', $envPath);
} else {
    $failures++;
    programmit_doctor_write('FAIL', 'Archivo de entorno', 'Falta .env.local o .env');
}

$gitignorePath = $projectRoot . DIRECTORY_SEPARATOR . '.gitignore';
$gitignoreRaw = is_file($gitignorePath) ? (string)@file_get_contents($gitignorePath) : '';
if ($gitignoreRaw !== '' && strpos($gitignoreRaw, '.env.*') !== false) {
    programmit_doctor_write('OK', '.env.* ignorado por git');
} else {
    $warnings++;
    programmit_doctor_write('WARN', 'Gitignore de secretos', 'Revisa que .env.local no suba al repo');
}

$requiredExtensions = array('openssl', 'json');
foreach ($requiredExtensions as $extension) {
    if (extension_loaded($extension)) {
        programmit_doctor_write('OK', 'Extension PHP', $extension);
    } else {
        $failures++;
        programmit_doctor_write('FAIL', 'Extension PHP faltante', $extension);
    }
}

if ($DB_driver === 'mysql') {
    if (extension_loaded('mysqli') || class_exists('mysqli')) {
        programmit_doctor_write('OK', 'Driver PHP para MySQL', 'mysqli');
    } else {
        $failures++;
        programmit_doctor_write('FAIL', 'Driver PHP para MySQL', 'mysqli no disponible');
    }
} elseif ($DB_driver === 'pgsql') {
    if (extension_loaded('pdo_pgsql')) {
        programmit_doctor_write('OK', 'Driver PHP para PostgreSQL', 'pdo_pgsql');
    } else {
        $failures++;
        programmit_doctor_write('FAIL', 'Driver PHP para PostgreSQL', 'pdo_pgsql no disponible');
    }
}

$bootstrapScript = $projectRoot . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'bootstrap_admin.php';
if (is_file($bootstrapScript)) {
    programmit_doctor_write('OK', 'Bootstrap admin', $bootstrapScript);
} else {
    $failures++;
    programmit_doctor_write('FAIL', 'Bootstrap admin', 'No existe tools/bootstrap_admin.php');
}

$bootstrapUser = programmit_doctor_env('BOOTSTRAP_ADMIN_USER');
$bootstrapPass = programmit_doctor_env('BOOTSTRAP_ADMIN_PASS');
$bootstrapEmail = programmit_doctor_env('BOOTSTRAP_ADMIN_EMAIL');
$bootstrapName = programmit_doctor_env('BOOTSTRAP_ADMIN_NAME');

if ($bootstrapUser !== '') {
    programmit_doctor_write('OK', 'BOOTSTRAP_ADMIN_USER', $bootstrapUser);
} else {
    $warnings++;
    programmit_doctor_write('WARN', 'BOOTSTRAP_ADMIN_USER', 'No configurado');
}

if ($bootstrapPass !== '') {
    programmit_doctor_write('OK', 'BOOTSTRAP_ADMIN_PASS', 'Configurado');
} else {
    $warnings++;
    programmit_doctor_write('WARN', 'BOOTSTRAP_ADMIN_PASS', 'No configurado');
}

if ($bootstrapEmail !== '') {
    programmit_doctor_write('OK', 'BOOTSTRAP_ADMIN_EMAIL', $bootstrapEmail);
} else {
    $warnings++;
    programmit_doctor_write('WARN', 'BOOTSTRAP_ADMIN_EMAIL', 'No configurado');
}

if ($bootstrapName !== '') {
    programmit_doctor_write('OK', 'BOOTSTRAP_ADMIN_NAME', $bootstrapName);
} else {
    $warnings++;
    programmit_doctor_write('WARN', 'BOOTSTRAP_ADMIN_NAME', 'No configurado');
}

$ownerQry = $db->sql_query("SELECT user_id, user_name, user_email, user_level, is_groupname, is_active, is_validated, is_ban, is_freeze, status
    FROM users
    WHERE user_id='1'
    LIMIT 1");
$ownerRow = $db->sql_fetchrow($ownerQry);

if (!$ownerRow) {
    $failures++;
    programmit_doctor_write('FAIL', 'Owner user_id=1', 'No existe en la tabla users');
} else {
    programmit_doctor_write(
        'OK',
        'Owner user_id=1',
        (string)$ownerRow['user_name'] . ' / ' . (string)$ownerRow['user_level'] . ' / ' . (string)$ownerRow['status']
    );

    if (strtolower(trim((string)$ownerRow['user_level'])) !== 'superadmin') {
        $failures++;
        programmit_doctor_write('FAIL', 'Owner role', 'user_id=1 debe quedar como superadmin');
    }
    if ((int)$ownerRow['is_active'] !== 1 || (int)$ownerRow['is_validated'] !== 1 || (int)$ownerRow['is_ban'] !== 0 || (int)$ownerRow['is_freeze'] !== 0 || strtolower(trim((string)$ownerRow['status'])) !== 'live') {
        $failures++;
        programmit_doctor_write('FAIL', 'Owner status', 'Debe estar activo, validado, sin ban, sin freeze y live');
    }

    if ($bootstrapUser !== '' && strcasecmp($bootstrapUser, (string)$ownerRow['user_name']) !== 0) {
        $warnings++;
        programmit_doctor_write('WARN', 'Bootstrap desincronizado', 'El owner actual no coincide con BOOTSTRAP_ADMIN_USER');
    }
    if ($bootstrapEmail !== '' && strcasecmp($bootstrapEmail, (string)$ownerRow['user_email']) !== 0) {
        $warnings++;
        programmit_doctor_write('WARN', 'Bootstrap email desincronizado', 'Reejecuta bootstrap_admin.php si cambiaste el correo');
    }
}

$controlHost = programmit_doctor_setting($db, 'saas_control_host', 'panel.programmit.com');
$panelHost = programmit_doctor_setting($db, 'saas_default_panel_host', 'panel.programmit.com');
if ($controlHost === '') {
    $warnings++;
    programmit_doctor_write('WARN', 'Control host', 'No definido en saas_settings');
} else {
    $resolvedControlIp = programmit_doctor_resolve_host($controlHost);
    if ($resolvedControlIp !== '') {
        programmit_doctor_write('OK', 'Control host', $controlHost . ' -> ' . $resolvedControlIp);
    } else {
        $warnings++;
        programmit_doctor_write('WARN', 'Control host', $controlHost . ' sin resolucion DNS visible desde este entorno');
    }
}

if ($panelHost === '') {
    $warnings++;
    programmit_doctor_write('WARN', 'Panel host', 'No definido en saas_settings');
} else {
    $resolvedPanelIp = programmit_doctor_resolve_host($panelHost);
    if ($resolvedPanelIp !== '') {
        programmit_doctor_write('OK', 'Panel host', $panelHost . ' -> ' . $resolvedPanelIp);
    } else {
        $warnings++;
        programmit_doctor_write('WARN', 'Panel host', $panelHost . ' sin resolucion DNS visible desde este entorno');
    }
}

$strictMode = programmit_doctor_setting($db, 'control_admin_strict_mode', '1');
$requireSuperadmin = programmit_doctor_setting($db, 'control_admin_require_superadmin', '1');
$allowRegister = programmit_doctor_setting($db, 'control_admin_allow_register', '0');

programmit_doctor_write('OK', 'Control admin strict mode', programmit_doctor_is_truthy($strictMode) ? 'enabled' : 'disabled');
programmit_doctor_write('OK', 'Control admin require superadmin', programmit_doctor_is_truthy($requireSuperadmin) ? 'enabled' : 'disabled');
programmit_doctor_write('OK', 'Control admin allow register', programmit_doctor_is_truthy($allowRegister) ? 'enabled' : 'disabled');

$writablePaths = array(
    'templates_c',
    'profile',
    'serverside/_uploads',
    'logo/branding',
    'logo/metodos'
);

foreach ($writablePaths as $relativePath) {
    $absolutePath = $projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
    if (!is_dir($absolutePath)) {
        $failures++;
        programmit_doctor_write('FAIL', 'Directorio faltante', $relativePath);
        continue;
    }

    if (!programmit_doctor_can_write_dir($absolutePath)) {
        $failures++;
        programmit_doctor_write('FAIL', 'Permiso de escritura', $relativePath . ' no permite crear archivos para el runtime web');
        continue;
    }

    programmit_doctor_write('OK', 'Writable dir', $relativePath);
}

fwrite(STDOUT, PHP_EOL);
if ($failures > 0) {
    programmit_doctor_write('FAIL', 'Resumen', $failures . ' fallo(s), ' . $warnings . ' advertencia(s)');
    exit(1);
}

if ($warnings > 0) {
    programmit_doctor_write('WARN', 'Resumen', '0 fallos, ' . $warnings . ' advertencia(s)');
    exit(0);
}

programmit_doctor_write('OK', 'Resumen', 'Entorno listo sin advertencias');
