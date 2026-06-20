<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}

require_once '../../includes/functions.php';
chkSession();

if (!($user_id_2 == 1 || in_array($user_level_2, array('superadmin', 'subadmin', 'administrator', 'reseller', 'subreseller'), true))) {
    $db->HandleError('No tienes permisos para generar cuentas de prueba.');
    if (!headers_sent()) {
        http_response_code(403);
    }
    echo $db->GetErrorMessage();
    exit;
}

function programmit_trial_guid_v4($data = null)
{
    $data = $data ?? random_bytes(16);
    if (strlen($data) !== 16) {
        return '';
    }
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function programmit_trial_random_int($min, $max)
{
    if (function_exists('random_int')) {
        return random_int((int)$min, (int)$max);
    }
    return mt_rand((int)$min, (int)$max);
}

function programmit_trial_table_exists($db, $tableName)
{
    $tableName = trim((string)$tableName);
    if ($tableName === '') {
        return false;
    }
    $qry = $db->sql_query("SHOW TABLES LIKE '" . $db->SanitizeForSQL($tableName) . "'");
    return ($qry && $db->sql_numrows($qry) > 0);
}

function programmit_trial_columns($db, $tableName)
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
    $qry = $db->sql_query("SHOW COLUMNS FROM `" . $tableName . "`");
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

function programmit_trial_user_exists($db, $userName)
{
    $userName = trim((string)$userName);
    if ($userName === '') {
        return false;
    }

    $qry = $db->sql_query("SELECT user_id
        FROM users
        WHERE user_name='" . $db->SanitizeForSQL($userName) . "'
        LIMIT 1");
    return ($qry && $db->sql_numrows($qry) > 0);
}

function programmit_trial_unique_username($db, $prefix)
{
    $prefix = preg_replace('/[^a-zA-Z0-9._-]+/', '', trim((string)$prefix));
    if ($prefix === '') {
        $prefix = 'test';
    }
    $prefix = substr($prefix, 0, 8);

    for ($attempt = 0; $attempt < 30; $attempt++) {
        $userName = $prefix . programmit_trial_random_int(10000, 99999);
        if (!programmit_trial_user_exists($db, $userName)) {
            return $userName;
        }
    }

    return '';
}

function programmit_trial_insert_user($db, $values)
{
    $columns = programmit_trial_columns($db, 'users');
    if (empty($columns)) {
        $db->HandleError('No se pudo leer la estructura de la tabla users.');
        return 0;
    }

    $insertCols = array();
    $insertVals = array();
    foreach ((array)$values as $column => $value) {
        if (!isset($columns[$column])) {
            continue;
        }
        $insertCols[] = "`" . $column . "`";
        if ($value === null) {
            $insertVals[] = "NULL";
        } else {
            $insertVals[] = "'" . $db->SanitizeForSQL((string)$value) . "'";
        }
    }

    if (count($insertCols) < 12) {
        $db->HandleError('La estructura actual no es compatible con cuentas de prueba.');
        return 0;
    }

    $sql = "INSERT INTO users (" . implode(', ', $insertCols) . ")
        VALUES (" . implode(', ', $insertVals) . ")";
    $ok = $db->sql_query($sql);
    if (!$ok) {
        $db->HandleDBError('No se pudo crear la cuenta de prueba.');
        return 0;
    }

    $insertId = (int)$db->sql_nextid();
    if ($insertId > 0) {
        return $insertId;
    }

    if (!empty($values['user_name'])) {
        $qry = $db->sql_query("SELECT user_id
            FROM users
            WHERE user_name='" . $db->SanitizeForSQL((string)$values['user_name']) . "'
            ORDER BY user_id DESC
            LIMIT 1");
        $row = $db->sql_fetchrow($qry);
        if ($row && isset($row['user_id'])) {
            return (int)$row['user_id'];
        }
    }

    return 0;
}

function programmit_trial_ensure_profile($db, $userId)
{
    $userId = (int)$userId;
    if ($userId <= 0 || !programmit_trial_table_exists($db, 'users_profile')) {
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

function programmit_trial_upsert_radius($db, $userName, $plainPassword, $groupName)
{
    $userName = trim((string)$userName);
    $plainPassword = trim((string)$plainPassword);
    $groupName = trim((string)$groupName);
    if ($userName === '' || $plainPassword === '') {
        return;
    }

    if (programmit_trial_table_exists($db, 'radcheck')) {
        $safeUser = $db->SanitizeForSQL($userName);
        $db->sql_query("DELETE FROM radcheck WHERE username='" . $safeUser . "'");
        $db->sql_query("INSERT INTO radcheck (username, attribute, op, value)
            VALUES ('" . $safeUser . "', 'Cleartext-Password', ':=', '" . $db->SanitizeForSQL($plainPassword) . "')");
    }

    if ($groupName !== '' && programmit_trial_table_exists($db, 'radusergroup')) {
        $safeUser = $db->SanitizeForSQL($userName);
        $db->sql_query("DELETE FROM radusergroup WHERE username='" . $safeUser . "'");
        $db->sql_query("INSERT INTO radusergroup (username, groupname, priority)
            VALUES ('" . $safeUser . "', '" . $db->SanitizeForSQL($groupName) . "', 1)");
    }
}

function programmit_trial_trigger_sync($db)
{
    if (function_exists('programmit_vpn_reconcile_users')) {
        programmit_vpn_reconcile_users($db, true);
    }
}

function programmit_trial_payload($db, $userName, $plainPassword, $category, $uplineId)
{
    $now = date('Y-m-d H:i:s');
    $passwordEncrypted = $db->encrypt_key($db->encryptor('encrypt', $plainPassword));
    $authVpn = md5($plainPassword);
    $isPrivate = ($category === 'private') ? 1 : 0;
    $duration = ($category === 'premium') ? 54000 : 0;
    $privateDuration = ($category === 'private') ? 54000 : 0;
    $ssId = $isPrivate ? (string)programmit_trial_random_int(10000, 65535) : '';

    return array(
        'password' => $plainPassword,
        'code' => (string)programmit_trial_random_int(10000000, 999999999),
        'ss_id' => $ssId,
        'ssl_id' => 'ssl',
        'uuid' => programmit_trial_guid_v4(),
        'user_name' => $userName,
        'user_pass' => $passwordEncrypted,
        'pass_plain' => $plainPassword,
        'attribute' => 'MD5-Password',
        'op' => ':=',
        'auth_vpn' => $authVpn,
        'user_email' => $userName . '@gmail.com',
        'full_name' => $userName,
        'regdate' => $now,
        'ipaddress' => method_exists($db, 'get_client_ip') ? $db->get_client_ip() : '0.0.0.0',
        'lastlogin' => '1970-01-01 00:00:00',
        'timestamp' => 0,
        'reset_code' => '0',
        'is_groupname' => 'bulk',
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
        'duration' => $duration,
        'vip_duration' => 0,
        'is_vip' => 0,
        'private_duration' => $privateDuration,
        'is_private' => $isPrivate,
        'role_duration' => 0,
        'private_slot' => 0,
        'private_control' => 0,
        'credits' => 0,
        'upline' => (int)$uplineId,
        'login_status' => 'offline',
        'last_active_time' => $now,
        'user_level' => 'normal',
        'status' => 'live',
        'bandwidth' => 0,
        'bandwidth_premium' => 0,
        'bandwidth_vip' => 0,
        'bandwidth_ph' => 0,
        'bandwidth_private' => 0,
        'bandwidth_free' => 0,
        'device_connected' => 0,
        'tenant_id' => 0,
        'is_tenant_owner' => 0
    );
}

if (!isset($_POST['submitted'])) {
    $db->RedirectToURL($db->base_url());
    exit;
}

$count = (int)$db->Sanitize(trim((string)($_POST['add_users'] ?? '0')));
if ($count < 1) {
    $count = 1;
}
if ($count > 5) {
    $count = 5;
}

$category = trim((string)$db->encryptor('decrypt', trim((string)($_POST['generate_type'] ?? ''))));
if (!in_array($category, array('premium', 'private'), true)) {
    $category = 'premium';
}

$prefix = preg_replace('/[^a-zA-Z0-9._-]+/', '', trim((string)($_POST['prefix'] ?? '')));
if ($prefix === '' || strlen($prefix) < 2) {
    $db->HandleError('El prefijo es obligatorio y debe tener al menos 2 caracteres.');
    if (!headers_sent()) {
        http_response_code(422);
    }
    echo $db->GetErrorMessage();
    exit;
}

$createdCount = 0;
$createdNames = array();
$groupName = ($category === 'private') ? 'private' : 'premium';

for ($num = 0; $num < $count; $num++) {
    $userName = programmit_trial_unique_username($db, $prefix);
    if ($userName === '') {
        $db->HandleError('No se pudo generar un username unico para la prueba.');
        break;
    }

    $plainPassword = $userName;
    $payload = programmit_trial_payload($db, $userName, $plainPassword, $category, $user_id_2);
    $insertId = programmit_trial_insert_user($db, $payload);
    if ($insertId <= 0) {
        break;
    }

    programmit_trial_ensure_profile($db, $insertId);
    programmit_trial_upsert_radius($db, $userName, $plainPassword, $groupName);
    $createdCount++;
    $createdNames[] = $userName;
}

if ($createdCount > 0) {
    programmit_trial_trigger_sync($db);
    $db->HandleSuccess('Successfully generated ' . $createdCount . ' trial account/s!');
}

if ($createdCount === 0) {
    if (!headers_sent()) {
        http_response_code(500);
    }
} elseif ($createdCount < $count && $db->GetErrorMessage() === '') {
    $db->HandleError('Solo se generaron ' . $createdCount . ' de ' . $count . ' cuentas solicitadas.');
}

echo $db->GetSuccessMessage();
echo $db->GetErrorMessage();
?>
