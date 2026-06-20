<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit('CLI only');
}

require_once __DIR__ . '/../includes/config.php';

function programmit_bootstrap_admin_is_pgsql($db)
{
    return (method_exists($db, 'is_pgsql') && $db->is_pgsql());
}

function programmit_bootstrap_admin_identifier($db, $column)
{
    $column = str_replace(array('"', '`'), '', (string)$column);
    if (programmit_bootstrap_admin_is_pgsql($db)) {
        return '"' . $column . '"';
    }
    return '`' . $column . '`';
}

function programmit_bootstrap_admin_write($stream, $message)
{
    fwrite($stream, (string)$message . PHP_EOL);
}

function programmit_bootstrap_admin_fail($message, $code = 1)
{
    programmit_bootstrap_admin_write(STDERR, 'ERROR: ' . (string)$message);
    exit((int)$code);
}

function programmit_bootstrap_admin_parse_args($argv)
{
    $options = array();
    foreach (array_slice((array)$argv, 1) as $arg) {
        $arg = (string)$arg;
        if (strpos($arg, '--') !== 0) {
            continue;
        }

        $eqPos = strpos($arg, '=');
        if ($eqPos === false) {
            $options[substr($arg, 2)] = true;
            continue;
        }

        $key = substr($arg, 2, $eqPos - 2);
        $value = substr($arg, $eqPos + 1);
        $options[$key] = $value;
    }

    return $options;
}

function programmit_bootstrap_admin_option($options, $key, $envKey, $default = '')
{
    if (isset($options[$key]) && $options[$key] !== '') {
        return trim((string)$options[$key]);
    }

    if (function_exists('programmit_env_get')) {
        $envValue = trim((string)programmit_env_get($envKey));
        if ($envValue !== '') {
            return $envValue;
        }
    }

    return (string)$default;
}

function programmit_bootstrap_admin_help()
{
    $lines = array(
        'Uso:',
        '  C:\\xampp\\php\\php.exe tools\\bootstrap_admin.php',
        '  C:\\xampp\\php\\php.exe tools\\bootstrap_admin.php --username=owner --password=ChangeMe123! --email=owner@example.com --name="Project Owner"',
        '',
        'Variables soportadas en .env.local o entorno:',
        '  BOOTSTRAP_ADMIN_USER',
        '  BOOTSTRAP_ADMIN_PASS',
        '  BOOTSTRAP_ADMIN_EMAIL',
        '  BOOTSTRAP_ADMIN_NAME',
        '',
        'Opciones:',
        '  --username=...   Usuario del owner (user_id=1)',
        '  --password=...   Contrasena inicial del owner',
        '  --email=...      Email del owner',
        '  --name=...       Nombre visible del owner',
        '  --dry-run        Valida y muestra lo que hara, sin guardar',
        '  --help           Muestra esta ayuda'
    );

    foreach ($lines as $line) {
        programmit_bootstrap_admin_write(STDOUT, $line);
    }
}

function programmit_bootstrap_admin_table_exists($db, $tableName)
{
    $tableName = trim((string)$tableName);
    if ($tableName === '') {
        return false;
    }

    if (programmit_bootstrap_admin_is_pgsql($db)) {
        $qry = $db->sql_query("SELECT table_name
            FROM information_schema.tables
            WHERE table_schema = current_schema()
              AND table_name='" . $db->SanitizeForSQL($tableName) . "'
            LIMIT 1");
    } else {
        $qry = $db->sql_query("SHOW TABLES LIKE '" . $db->SanitizeForSQL($tableName) . "'");
    }
    return ($qry && $db->sql_numrows($qry) > 0);
}

function programmit_bootstrap_admin_columns($db, $tableName)
{
    static $cache = array();

    $tableName = trim((string)$tableName);
    if ($tableName === '') {
        return array();
    }

    if (isset($cache[$tableName])) {
        return $cache[$tableName];
    }

    $columns = array();
    if (programmit_bootstrap_admin_is_pgsql($db)) {
        $qry = $db->sql_query("SELECT column_name AS \"Field\"
            FROM information_schema.columns
            WHERE table_schema = current_schema()
              AND table_name='" . $db->SanitizeForSQL($tableName) . "'");
    } else {
        $qry = $db->sql_query("SHOW COLUMNS FROM " . programmit_bootstrap_admin_identifier($db, $tableName));
    }
    if ($qry) {
        while ($row = $db->sql_fetchrow($qry)) {
            if ($row && isset($row['Field'])) {
                $columns[(string)$row['Field']] = true;
            }
        }
    }

    $cache[$tableName] = $columns;
    return $columns;
}

function programmit_bootstrap_admin_insert_row($db, $tableName, $values)
{
    $columns = programmit_bootstrap_admin_columns($db, $tableName);
    if (empty($columns)) {
        return false;
    }

    $insertCols = array();
    $insertVals = array();

    foreach ((array)$values as $column => $value) {
        if (!isset($columns[$column])) {
            continue;
        }

        $insertCols[] = programmit_bootstrap_admin_identifier($db, $column);
        if ($value === null) {
            $insertVals[] = "NULL";
            continue;
        }

        $insertVals[] = "'" . $db->SanitizeForSQL((string)$value) . "'";
    }

    if (empty($insertCols)) {
        return false;
    }

    $sql = "INSERT INTO " . programmit_bootstrap_admin_identifier($db, $tableName) . " (" . implode(', ', $insertCols) . ")
        VALUES (" . implode(', ', $insertVals) . ")";

    return (bool)$db->sql_query($sql);
}

function programmit_bootstrap_admin_update_row($db, $tableName, $values, $whereSql, $skipColumns = array())
{
    $columns = programmit_bootstrap_admin_columns($db, $tableName);
    if (empty($columns)) {
        return false;
    }

    $assignments = array();
    foreach ((array)$values as $column => $value) {
        if (isset($skipColumns[$column]) || !isset($columns[$column])) {
            continue;
        }

        if ($value === null) {
            $assignments[] = programmit_bootstrap_admin_identifier($db, $column) . "=NULL";
            continue;
        }

        $assignments[] = programmit_bootstrap_admin_identifier($db, $column) . "='" . $db->SanitizeForSQL((string)$value) . "'";
    }

    if (empty($assignments)) {
        return false;
    }

    $sql = "UPDATE " . programmit_bootstrap_admin_identifier($db, $tableName) . " SET
        " . implode(",\n        ", $assignments) . "
        WHERE " . $whereSql;

    return (bool)$db->sql_query($sql);
}

function programmit_bootstrap_admin_random_digits($min, $max)
{
    if (function_exists('random_int')) {
        return (string)random_int((int)$min, (int)$max);
    }
    return (string)mt_rand((int)$min, (int)$max);
}

function programmit_bootstrap_admin_random_token($length = 16)
{
    $length = max(8, (int)$length);
    if (function_exists('random_bytes')) {
        return bin2hex(random_bytes((int)ceil($length / 2)));
    }
    return md5(uniqid('bootstrap_owner_', true));
}

function programmit_bootstrap_admin_valid_username($userName)
{
    $userName = trim((string)$userName);
    if ($userName === '') {
        return false;
    }
    return (bool)preg_match('/^[A-Za-z0-9_.-]{3,128}$/', $userName);
}

function programmit_bootstrap_admin_valid_datetime($value)
{
    $value = trim((string)$value);
    if ($value === '' || $value === '0000-00-00 00:00:00') {
        return false;
    }
    return (bool)preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value);
}

function programmit_bootstrap_admin_owner_row($db)
{
    $qry = $db->sql_query("SELECT * FROM users WHERE user_id='1' LIMIT 1");
    $row = $db->sql_fetchrow($qry);
    return $row ? $row : null;
}

function programmit_bootstrap_admin_username_in_use($db, $userName, $excludeUserId = 1)
{
    $userName = trim((string)$userName);
    if ($userName === '') {
        return null;
    }

    $qry = $db->sql_query("SELECT user_id, user_name
        FROM users
        WHERE user_name='" . $db->SanitizeForSQL($userName) . "'
          AND user_id<>'" . $db->SanitizeForSQL((int)$excludeUserId) . "'
        LIMIT 1");
    $row = $db->sql_fetchrow($qry);
    return $row ? $row : null;
}

function programmit_bootstrap_admin_delete_radius_identity($db, $userName)
{
    $userName = trim((string)$userName);
    if ($userName === '') {
        return;
    }

    $safeUser = $db->SanitizeForSQL($userName);
    if (programmit_bootstrap_admin_table_exists($db, 'radcheck')) {
        $db->sql_query("DELETE FROM radcheck WHERE username='" . $safeUser . "'");
    }
    if (programmit_bootstrap_admin_table_exists($db, 'radusergroup')) {
        $db->sql_query("DELETE FROM radusergroup WHERE username='" . $safeUser . "'");
    }
}

function programmit_bootstrap_admin_sync_radius($db, $userName, $plainPassword, $groupName)
{
    $userName = trim((string)$userName);
    $plainPassword = trim((string)$plainPassword);
    $groupName = trim((string)$groupName);
    if ($userName === '' || $plainPassword === '') {
        return;
    }

    $safeUser = $db->SanitizeForSQL($userName);
    if (programmit_bootstrap_admin_table_exists($db, 'radcheck')) {
        $db->sql_query("DELETE FROM radcheck WHERE username='" . $safeUser . "'");
        $db->sql_query("INSERT INTO radcheck (username, attribute, op, value)
            VALUES ('" . $safeUser . "', 'Cleartext-Password', ':=', '" . $db->SanitizeForSQL($plainPassword) . "')");
    }

    if ($groupName !== '' && programmit_bootstrap_admin_table_exists($db, 'radusergroup')) {
        $db->sql_query("DELETE FROM radusergroup WHERE username='" . $safeUser . "'");
        $db->sql_query("INSERT INTO radusergroup (username, groupname, priority)
            VALUES ('" . $safeUser . "', '" . $db->SanitizeForSQL($groupName) . "', 1)");
    }
}

function programmit_bootstrap_admin_ensure_profile($db, $userId)
{
    $userId = (int)$userId;
    if ($userId <= 0 || !programmit_bootstrap_admin_table_exists($db, 'users_profile')) {
        return;
    }

    $qry = $db->sql_query("SELECT profile_id
        FROM users_profile
        WHERE profile_id='" . $db->SanitizeForSQL($userId) . "'
        LIMIT 1");
    if ($qry && $db->sql_numrows($qry) > 0) {
        return;
    }

    $db->sql_query("INSERT INTO users_profile (profile_id)
        VALUES ('" . $db->SanitizeForSQL($userId) . "')");
}

function programmit_bootstrap_admin_payload($db, $existingRow, $userName, $plainPassword, $email, $fullName)
{
    $existingRow = is_array($existingRow) ? $existingRow : array();
    $now = date('Y-m-d H:i:s');
    $encryptedPassword = $db->encrypt_key($db->encryptor('encrypt', $plainPassword));

    $regdate = programmit_bootstrap_admin_valid_datetime($existingRow['regdate'] ?? '')
        ? (string)$existingRow['regdate']
        : $now;
    $lastlogin = programmit_bootstrap_admin_valid_datetime($existingRow['lastlogin'] ?? '')
        ? (string)$existingRow['lastlogin']
        : '1970-01-01 00:00:00';
    $userCode = trim((string)($existingRow['code'] ?? ''));
    if ($userCode === '') {
        $userCode = programmit_bootstrap_admin_random_digits(10000000, 999999999);
    }
    $uuid = trim((string)($existingRow['uuid'] ?? ''));
    if ($uuid === '') {
        $uuid = 'bootstrap-owner-' . programmit_bootstrap_admin_random_token(16);
    }
    $ssId = trim((string)($existingRow['ss_id'] ?? ''));
    if ($ssId === '') {
        $ssId = 'owner';
    }
    $roleDuration = (int)($existingRow['role_duration'] ?? 0);
    if ($roleDuration < 2592000) {
        $roleDuration = 2592000;
    }

    return array(
        'user_id' => 1,
        'password' => $plainPassword,
        'code' => $userCode,
        'ss_id' => $ssId,
        'ssl_id' => trim((string)($existingRow['ssl_id'] ?? '')) !== '' ? (string)$existingRow['ssl_id'] : 'ssl',
        'uuid' => $uuid,
        'user_name' => $userName,
        'user_pass' => $encryptedPassword,
        'pass_plain' => $plainPassword,
        'attribute' => 'MD5-Password',
        'op' => ':=',
        'auth_vpn' => md5($plainPassword),
        'user_email' => $email,
        'full_name' => $fullName,
        'regdate' => $regdate,
        'ipaddress' => '0.0.0.0',
        'lastlogin' => $lastlogin,
        'timestamp' => (int)($existingRow['timestamp'] ?? 0),
        'reset_code' => '0',
        'is_groupname' => 'superadmin',
        'is_active' => 1,
        'is_freeze' => 0,
        'is_passchange' => 0,
        'freeze_status' => 0,
        'last_freeze_date' => '1970-01-01 00:00:00',
        'is_validated' => 1,
        'is_connected' => 0,
        'is_offense' => 0,
        'is_ban' => 0,
        'suspended_date' => '1970-01-01 00:00:00',
        'duration' => (int)($existingRow['duration'] ?? 0),
        'vip_duration' => (int)($existingRow['vip_duration'] ?? 0),
        'is_vip' => 0,
        'private_duration' => (int)($existingRow['private_duration'] ?? 0),
        'is_private' => 0,
        'role_duration' => $roleDuration,
        'private_slot' => (int)($existingRow['private_slot'] ?? 0),
        'private_control' => (int)($existingRow['private_control'] ?? 0),
        'credits' => (int)($existingRow['credits'] ?? 0),
        'upline' => 1,
        'login_status' => 'offline',
        'last_active_time' => $now,
        'user_level' => 'superadmin',
        'status' => 'live',
        'bandwidth' => (int)($existingRow['bandwidth'] ?? 0),
        'bandwidth_premium' => (int)($existingRow['bandwidth_premium'] ?? 0),
        'bandwidth_vip' => (int)($existingRow['bandwidth_vip'] ?? 0),
        'bandwidth_ph' => (int)($existingRow['bandwidth_ph'] ?? 0),
        'bandwidth_private' => (int)($existingRow['bandwidth_private'] ?? 0),
        'bandwidth_free' => (int)($existingRow['bandwidth_free'] ?? 0),
        'device_connected' => 0,
        'tenant_id' => (int)($existingRow['tenant_id'] ?? 0),
        'is_tenant_owner' => 1
    );
}

$options = programmit_bootstrap_admin_parse_args($argv);
if (isset($options['help'])) {
    programmit_bootstrap_admin_help();
    exit(0);
}

$userName = programmit_bootstrap_admin_option($options, 'username', 'BOOTSTRAP_ADMIN_USER', 'owner');
$plainPassword = programmit_bootstrap_admin_option($options, 'password', 'BOOTSTRAP_ADMIN_PASS', '');
$email = programmit_bootstrap_admin_option($options, 'email', 'BOOTSTRAP_ADMIN_EMAIL', '');
$fullName = programmit_bootstrap_admin_option($options, 'name', 'BOOTSTRAP_ADMIN_NAME', 'Project Owner');
$dryRun = isset($options['dry-run']);

if (!programmit_bootstrap_admin_valid_username($userName)) {
    programmit_bootstrap_admin_fail('El usuario debe usar solo letras, numeros, punto, guion o underscore.');
}

$passwordError = function_exists('programmit_client_password_validation_error')
    ? programmit_client_password_validation_error($plainPassword)
    : '';
if ($plainPassword === '' || $passwordError !== '') {
    programmit_bootstrap_admin_fail($passwordError !== '' ? $passwordError : 'La contrasena del owner es obligatoria.');
}
if (strlen($plainPassword) > 50) {
    programmit_bootstrap_admin_fail('La contrasena del owner no puede exceder 50 caracteres por compatibilidad del esquema.');
}

if ($email === '') {
    $email = $userName . '@example.com';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    programmit_bootstrap_admin_fail('El email del owner no es valido.');
}
if (strlen($email) > 50) {
    programmit_bootstrap_admin_fail('El email del owner no puede exceder 50 caracteres.');
}
if (strlen($fullName) > 50) {
    programmit_bootstrap_admin_fail('El nombre visible del owner no puede exceder 50 caracteres.');
}

$ownerRow = programmit_bootstrap_admin_owner_row($db);
$usernameConflict = programmit_bootstrap_admin_username_in_use($db, $userName, 1);
if ($usernameConflict) {
    programmit_bootstrap_admin_fail(
        'El usuario "' . $userName . '" ya existe en user_id=' . (int)$usernameConflict['user_id'] . '. Usa otro nombre.'
    );
}

$payload = programmit_bootstrap_admin_payload($db, $ownerRow, $userName, $plainPassword, $email, $fullName);
$mode = $ownerRow ? 'update' : 'insert';

programmit_bootstrap_admin_write(STDOUT, 'Bootstrap owner: ' . ($mode === 'insert' ? 'creara' : 'actualizara') . ' user_id=1');
programmit_bootstrap_admin_write(STDOUT, 'Usuario: ' . $userName);
programmit_bootstrap_admin_write(STDOUT, 'Email: ' . $email);
programmit_bootstrap_admin_write(STDOUT, 'Rol final: superadmin');
programmit_bootstrap_admin_write(STDOUT, 'Estado final: activo, validado, sin freeze, sin ban');

if ($dryRun) {
    programmit_bootstrap_admin_write(STDOUT, 'Dry run completado. No se guardaron cambios.');
    exit(0);
}

$oldUserName = is_array($ownerRow) ? trim((string)($ownerRow['user_name'] ?? '')) : '';
if ($ownerRow) {
    $saved = programmit_bootstrap_admin_update_row(
        $db,
        'users',
        $payload,
        "user_id='1'",
        array('user_id' => true)
    );
} else {
    $saved = programmit_bootstrap_admin_insert_row($db, 'users', $payload);
}

if (!$saved) {
    programmit_bootstrap_admin_fail('No se pudo guardar el owner user_id=1 en la tabla users.');
}

if ($oldUserName !== '' && strcasecmp($oldUserName, $userName) !== 0) {
    programmit_bootstrap_admin_delete_radius_identity($db, $oldUserName);
}

programmit_bootstrap_admin_ensure_profile($db, 1);
programmit_bootstrap_admin_sync_radius($db, $userName, $plainPassword, 'superadmin');

if (function_exists('programmit_panel_access_bootstrap_user')) {
    programmit_panel_access_bootstrap_user($db, 1, 'superadmin', 0);
}

programmit_bootstrap_admin_write(STDOUT, 'Owner aplicado correctamente.');
programmit_bootstrap_admin_write(STDOUT, 'Login admin: /admin-login.php');
programmit_bootstrap_admin_write(STDOUT, 'Siguiente paso recomendado: guardar estas credenciales fuera del repo y cambiar BOOTSTRAP_ADMIN_PASS despues del alta.');
