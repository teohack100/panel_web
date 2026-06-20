<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}

require_once '../../includes/functions.php';
chkSession();

if (!($user_id_2 == 1 || in_array($user_level_2, array('superadmin', 'subadmin', 'administrator', 'reseller', 'subreseller'), true))) {
    $db->HandleError('No tienes permisos para crear usuarios desde esta pagina.');
    echo $db->GetErrorMessage();
    exit;
}

function programmit_adduser_random_int($min, $max)
{
    if (function_exists('random_int')) {
        return random_int((int)$min, (int)$max);
    }
    return mt_rand((int)$min, (int)$max);
}

function programmit_adduser_table_exists($db, $tableName)
{
    $tableName = trim((string)$tableName);
    if ($tableName === '') {
        return false;
    }

    $qry = $db->sql_query("SHOW TABLES LIKE '" . $db->SanitizeForSQL($tableName) . "'");
    return ($qry && $db->sql_numrows($qry) > 0);
}

function programmit_adduser_columns($db, $tableName)
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

function programmit_adduser_build_assignments($db, $tableName, $values, $skipColumns = array())
{
    $columns = programmit_adduser_columns($db, $tableName);
    $parts = array();

    foreach ((array)$values as $column => $value) {
        if (isset($skipColumns[$column]) || !isset($columns[$column])) {
            continue;
        }

        if ($value === null) {
            $parts[] = "`" . $column . "`=NULL";
            continue;
        }

        $parts[] = "`" . $column . "`='" . $db->SanitizeForSQL((string)$value) . "'";
    }

    return $parts;
}

function programmit_adduser_insert_user($db, $values)
{
    $columns = programmit_adduser_columns($db, 'users');
    if (empty($columns)) {
        $db->HandleError('La estructura de users no pudo ser leida.');
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

    if (count($insertCols) < 8) {
        $db->HandleError('La estructura actual de users no es compatible con el alta de clientes.');
        return 0;
    }

    $sql = "INSERT INTO users (" . implode(', ', $insertCols) . ")
        VALUES (" . implode(', ', $insertVals) . ")";
    $ok = $db->sql_query($sql);
    if (!$ok) {
        $db->HandleDBError('No se pudo crear el usuario.');
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

function programmit_adduser_update_user($db, $userId, $values)
{
    $userId = (int)$userId;
    if ($userId <= 0) {
        return false;
    }

    $assignments = programmit_adduser_build_assignments($db, 'users', $values, array('user_id' => true, 'regdate' => true, 'code' => true));
    if (empty($assignments)) {
        $db->HandleError('No hay campos validos para actualizar el usuario.');
        return false;
    }

    $sql = "UPDATE users SET
        " . implode(",\n        ", $assignments) . "
        WHERE user_id='" . $db->SanitizeForSQL($userId) . "'";

    $ok = $db->sql_query($sql);
    if (!$ok) {
        $db->HandleDBError('No se pudo actualizar el usuario.');
        return false;
    }

    return true;
}

function programmit_adduser_ensure_profile($db, $userId)
{
    $userId = (int)$userId;
    if ($userId <= 0 || !programmit_adduser_table_exists($db, 'users_profile')) {
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

function programmit_adduser_upsert_radius($db, $userName, $plainPassword, $groupName)
{
    $userName = trim((string)$userName);
    $plainPassword = trim((string)$plainPassword);
    $groupName = trim((string)$groupName);

    if ($userName === '' || $plainPassword === '') {
        return;
    }

    if (programmit_adduser_table_exists($db, 'radcheck')) {
        $safeUser = $db->SanitizeForSQL($userName);
        $db->sql_query("DELETE FROM radcheck WHERE username='" . $safeUser . "'");
        $db->sql_query("INSERT INTO radcheck (username, attribute, op, value)
            VALUES ('" . $safeUser . "', 'Cleartext-Password', ':=', '" . $db->SanitizeForSQL($plainPassword) . "')");
    }

    if ($groupName !== '' && programmit_adduser_table_exists($db, 'radusergroup')) {
        $safeUser = $db->SanitizeForSQL($userName);
        $db->sql_query("DELETE FROM radusergroup WHERE username='" . $safeUser . "'");
        $db->sql_query("INSERT INTO radusergroup (username, groupname, priority)
            VALUES ('" . $safeUser . "', '" . $db->SanitizeForSQL($groupName) . "', 1)");
    }
}

function programmit_adduser_trigger_sync($db)
{
    if (function_exists('programmit_vpn_reconcile_users')) {
        programmit_vpn_reconcile_users($db, true);
    }
}

function programmit_adduser_clear_dashboard_caches($userIds)
{
    $tmpDir = rtrim((string)sys_get_temp_dir(), '/\\');
    if ($tmpDir === '') {
        $tmpDir = '/tmp';
    }

    $cacheDir = $tmpDir . DIRECTORY_SEPARATOR . 'programmit_bootstrap';
    if (!is_dir($cacheDir)) {
        return;
    }

    foreach ((array)$userIds as $userId) {
        $userId = (int)$userId;
        if ($userId <= 0) {
            continue;
        }

        $targets = array(
            $cacheDir . DIRECTORY_SEPARATOR . 'dashboard_user_' . $userId . '.html',
            $cacheDir . DIRECTORY_SEPARATOR . 'admin_dashboard_user_' . $userId . '.html'
        );

        foreach ($targets as $target) {
            if (is_file($target)) {
                @unlink($target);
            }
        }
    }
}

function programmit_adduser_payload($db, $userName, $plainPassword, $userEmail, $fullName, $uuid, $userLevel, $groupName, $uplineId, $category, $roleDuration)
{
    $now = date('Y-m-d H:i:s');
    $passwordEncrypted = $db->encrypt_key($db->encryptor('encrypt', $plainPassword));
    $authVpn = md5($plainPassword);
    $duration = ($category === 'premium') ? 54000 : 0;
    $vipDuration = ($category === 'vip') ? 54000 : 0;
    $privateDuration = ($category === 'private') ? 54000 : 0;
    $ssId = ($category === 'premium') ? '' : (string)programmit_adduser_random_int(10000, 65535);

    return array(
        'password' => $plainPassword,
        'code' => (string)programmit_adduser_random_int(10000000, 999999999),
        'ss_id' => $ssId,
        'ssl_id' => 'ssl',
        'uuid' => $uuid,
        'user_name' => $userName,
        'user_pass' => $passwordEncrypted,
        'pass_plain' => $plainPassword,
        'attribute' => 'MD5-Password',
        'op' => ':=',
        'auth_vpn' => $authVpn,
        'user_email' => $userEmail,
        'full_name' => $fullName,
        'regdate' => $now,
        'ipaddress' => $db->get_client_ip(),
        'lastlogin' => '1970-01-01 00:00:00',
        'timestamp' => 0,
        'reset_code' => '0',
        'is_groupname' => $groupName,
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
        'vip_duration' => $vipDuration,
        'is_vip' => ($category === 'vip') ? 1 : 0,
        'private_duration' => $privateDuration,
        'is_private' => ($category === 'private') ? 1 : 0,
        'role_duration' => (int)$roleDuration,
        'private_slot' => 0,
        'private_control' => 0,
        'credits' => 0,
        'upline' => (int)$uplineId,
        'login_status' => 'offline',
        'last_active_time' => $now,
        'user_level' => $userLevel,
        'status' => 'live',
        'bandwidth' => 0,
        'bandwidth_premium' => 0,
        'bandwidth_vip' => 0,
        'bandwidth_ph' => 0,
        'bandwidth_private' => 0,
        'bandwidth_free' => 0,
        'device_connected' => 0
    );
}

function programmit_adduser_preserve_existing_state($payload, $row)
{
    if (!is_array($payload) || !is_array($row)) {
        return $payload;
    }

    $preserveColumns = array(
        'duration',
        'vip_duration',
        'private_duration',
        'role_duration',
        'ss_id',
        'is_vip',
        'is_private'
    );

    foreach ($preserveColumns as $column) {
        if (array_key_exists($column, $row)) {
            $payload[$column] = $row[$column];
        }
    }

    return $payload;
}

$valid = true;
$skipInsert = false;

function programmit_adduser_post_value($keys, $default = '')
{
    foreach ((array)$keys as $key) {
        if (isset($_POST[$key])) {
            return trim((string)$_POST[$key]);
        }
    }

    return $default;
}

function programmit_adduser_uses_general_password($roleRaw)
{
    return (trim((string)$roleRaw) === '1');
}

function programmit_adduser_resolve_upline_id($uplineRaw, $currentUserId, $existingRow = null)
{
    $uplineId = (int)trim((string)$uplineRaw);
    if ($uplineId > 0) {
        return $uplineId;
    }

    if (is_array($existingRow) && isset($existingRow['upline']) && (int)$existingRow['upline'] > 0) {
        return (int)$existingRow['upline'];
    }

    return (int)$currentUserId;
}

if (isset($_POST['submitted'])) {
    $user_name_raw = programmit_adduser_post_value(array('user_name', 'username'));
    $role_raw = programmit_adduser_post_value(array('role_acct', 'role', 'user_role'), '1');
    $user_pass_raw = programmit_adduser_post_value(array('user_pass', 'user_password'));
    if (
        $user_pass_raw === '' &&
        programmit_adduser_uses_general_password($role_raw) &&
        function_exists('programmit_client_default_password_get')
    ) {
        $user_pass_raw = programmit_client_default_password_get($db);
    }
    $full_name_raw = programmit_adduser_post_value(array('full_name', 'user_full_name'), $user_name_raw);
    $v2ray_raw = programmit_adduser_post_value(array('v2ray_id', 'uuid'));
    $user_email_raw = programmit_adduser_post_value(array('user_email', 'email'));
    $client_type_raw = programmit_adduser_post_value(array('client_type', 'user_type'));
    $upline_raw = programmit_adduser_post_value(array('resellers', 'upline_user', 'upline'), '0');

    $user_name = $db->Sanitize($user_name_raw);
    $user_pass = $db->Sanitize($user_pass_raw);
    $full_name = $db->Sanitize($full_name_raw !== '' ? $full_name_raw : $user_name_raw);
    $v2ray = $db->Sanitize($v2ray_raw);
    $user_email = $db->Sanitize($user_email_raw !== '' ? $user_email_raw : ($user_name_raw !== '' ? $user_name_raw . '@gmail.com' : ''));
    $category = $db->encryptor('decrypt', $client_type_raw);
    $category = $db->Sanitize((string)$category);

    if ($v2ray === '') {
        $db->HandleError('El UUID de V2Ray esta vacio.');
        $valid = false;
    }

    if ($user_name === '') {
        $db->HandleError('El nombre de usuario esta vacio.');
        $valid = false;
    } elseif (preg_match('/[^_a-z-A-Z-0-9 ]/', $user_name)) {
        $db->HandleError('Nombre de usuario invalido.');
        $valid = false;
    }

    $passwordValidationError = function_exists('programmit_client_password_validation_error')
        ? programmit_client_password_validation_error($user_pass)
        : '';
    if ($passwordValidationError !== '') {
        $db->HandleError($passwordValidationError);
        $valid = false;
    }

    if (!in_array($category, array('premium', 'vip', 'private'), true)) {
        $db->HandleError('Tipo de cliente invalido.');
        $valid = false;
    }

    $role = $db->Sanitize($role_raw);
    $user_level = '';
    $is_groupname = '';
    $role_dur = 0;

    if ($user_id_2 == 1) {
        if ($role == 1) {
            $user_level = 'normal';
            $is_groupname = 'normal';
            $role_dur = 0;
        } elseif ($role == 2) {
            $user_level = 'subreseller';
            $is_groupname = 'subreseller';
            $role_dur = 2592000;
        } elseif ($role == 3) {
            $user_level = 'reseller';
            $is_groupname = 'reseller';
            $role_dur = 2592000;
        } elseif ($role == 4) {
            $user_level = 'administrator';
            $is_groupname = 'administrator';
            $role_dur = 2592000;
        } elseif ($role == 5) {
            $user_level = 'subadmin';
            $is_groupname = 'subadmin';
            $role_dur = 2592000;
        } elseif ($role == 99) {
            $user_level = 'superadmin';
            $is_groupname = 'superadmin';
            $role_dur = 2592000;
        } else {
            $db->HandleError('Rol invalido para este usuario.');
            $valid = false;
        }
    } elseif ($user_level_2 == 'superadmin') {
        if ($role == 1) {
            $user_level = 'normal';
            $is_groupname = 'normal';
            $role_dur = 0;
        } elseif ($role == 2) {
            $user_level = 'subreseller';
            $is_groupname = 'subreseller';
            $role_dur = 2592000;
        } elseif ($role == 3) {
            $user_level = 'reseller';
            $is_groupname = 'reseller';
            $role_dur = 2592000;
        } elseif ($role == 4) {
            $user_level = 'administrator';
            $is_groupname = 'administrator';
            $role_dur = 2592000;
        } elseif ($role == 5) {
            $user_level = 'subadmin';
            $is_groupname = 'subadmin';
            $role_dur = 2592000;
        } else {
            $db->HandleError('Rol invalido para este usuario.');
            $valid = false;
        }
    } elseif ($user_level_2 == 'administrator') {
        if ($role == 1) {
            $user_level = 'normal';
            $is_groupname = 'normal';
            $role_dur = 0;
        } elseif ($role == 2) {
            $user_level = 'subreseller';
            $is_groupname = 'subreseller';
            $role_dur = 2592000;
        } elseif ($role == 3) {
            $user_level = 'reseller';
            $is_groupname = 'reseller';
            $role_dur = 0;
        } elseif ($role == 5) {
            $user_level = 'subadmin';
            $is_groupname = 'subadmin';
            $role_dur = 2592000;
        } else {
            $db->HandleError('Rol invalido para este usuario.');
            $valid = false;
        }
    } elseif ($user_level_2 == 'subadmin') {
        if ($role == 1) {
            $user_level = 'normal';
            $is_groupname = 'normal';
            $role_dur = 0;
        } elseif ($role == 2) {
            $user_level = 'subreseller';
            $is_groupname = 'subreseller';
            $role_dur = 2592000;
        } elseif ($role == 3) {
            $user_level = 'reseller';
            $is_groupname = 'reseller';
            $role_dur = 2592000;
        } else {
            $db->HandleError('Rol invalido para este usuario.');
            $valid = false;
        }
    } elseif ($user_level_2 == 'reseller') {
        if ($role == 1) {
            $user_level = 'normal';
            $is_groupname = 'normal';
            $role_dur = 0;
        } elseif ($role == 2) {
            $user_level = 'subreseller';
            $is_groupname = 'subreseller';
            $role_dur = 2592000;
        } else {
            $db->HandleError('Rol invalido para este usuario.');
            $valid = false;
        }
    } elseif ($user_level_2 == 'subreseller') {
        if ($role == 1) {
            $user_level = 'normal';
            $is_groupname = 'normal';
            $role_dur = 0;
        } else {
            $db->HandleError('Rol invalido para este usuario.');
            $valid = false;
        }
    } else {
        $db->HandleError('No estas autorizado para crear usuarios.');
        $valid = false;
    }

    if ($user_email === '') {
        $db->HandleError('El email esta vacio.');
        $valid = false;
    } else {
        $email_chk = $db->sql_numrows($db->sql_query("
            SELECT user_email
            FROM users
            WHERE user_email='" . $db->SanitizeForSQL($user_email) . "'
              AND user_name<>'" . $db->SanitizeForSQL($user_name) . "'
            LIMIT 1
        "));
        if ($email_chk > 0) {
            $db->HandleError($user_email . ' ya esta registrado.');
            $valid = false;
        }
    }

    if ($valid) {
        $row = $db->sql_fetchrow(
            $db->sql_query("SELECT user_id, user_level, is_active, duration, vip_duration, private_duration, role_duration, ss_id, is_ban, status, is_vip, is_private, is_freeze, upline
                FROM users
                WHERE user_name='" . $db->SanitizeForSQL($user_name) . "'
                ORDER BY user_id DESC
                LIMIT 1")
        );

        if ($row) {
            $rad_exists = programmit_adduser_table_exists($db, 'radcheck')
                ? $db->sql_numrows($db->sql_query("SELECT 1 FROM radcheck WHERE username='" . $db->SanitizeForSQL($user_name) . "' LIMIT 1"))
                : 1;
            $group_exists = programmit_adduser_table_exists($db, 'radusergroup')
                ? $db->sql_numrows($db->sql_query("SELECT 1 FROM radusergroup WHERE username='" . $db->SanitizeForSQL($user_name) . "' LIMIT 1"))
                : 1;

            $status_val = strtolower(trim((string)$row['status']));
            $is_live = ($status_val === 'live');
            $is_vip = ((int)$row['is_vip'] === 1);
            $is_private = ((int)$row['is_private'] === 1);

            if ($category === 'premium') {
                $expired_by_duration = ((int)$row['duration'] < 1 && (int)$row['private_duration'] < 1 && !$is_vip);
            } elseif ($category === 'vip') {
                $expired_by_duration = ((int)$row['vip_duration'] < 1 && $is_vip && !$is_private);
            } else {
                $expired_by_duration = ((int)$row['private_duration'] < 1 && $is_private && !$is_vip);
            }

            $expired_by_flags = ((int)$row['is_active'] === 0) || ((int)$row['is_ban'] === 1) || !$is_live;
            $expired_by_radius = ($rad_exists == 0) || ($group_exists == 0);
            $isExpired = $expired_by_duration || $expired_by_flags || $expired_by_radius;

            if ($isExpired) {
                $effectiveUplineId = programmit_adduser_resolve_upline_id($upline_raw, $user_id_2, $row);
                $payload = programmit_adduser_payload(
                    $db,
                    $user_name,
                    $user_pass,
                    $user_email,
                    $full_name,
                    $v2ray,
                    $user_level,
                    $is_groupname,
                    $effectiveUplineId,
                    $category,
                    $role_dur
                );

                // If the account is only frozen/suspended/inconsistent, keep the remaining time.
                if (!$expired_by_duration) {
                    $payload = programmit_adduser_preserve_existing_state($payload, $row);
                }

                if (programmit_adduser_update_user($db, (int)$row['user_id'], $payload)) {
                    $updatedId = (int)$row['user_id'];
                    programmit_adduser_ensure_profile($db, $updatedId);
                    programmit_adduser_upsert_radius($db, $user_name, $user_pass, $is_groupname);
                    if (function_exists('programmit_panel_access_bootstrap_user') && $user_level !== 'normal') {
                        programmit_panel_access_bootstrap_user($db, $updatedId, $user_level, 0);
                    }
                    programmit_adduser_trigger_sync($db);
                    programmit_adduser_clear_dashboard_caches(array($user_id_2, $effectiveUplineId, $updatedId));
                    $db->HandleSuccess($user_name . ' reactivado correctamente.');
                    $skipInsert = true;
                } else {
                    $valid = false;
                }
            } else {
                $db->HandleError($user_name . ' ya esta agregado y activo.');
                $valid = false;
            }
        }
    }

    if ($valid && !$skipInsert) {
        $effectiveUplineId = programmit_adduser_resolve_upline_id($upline_raw, $user_id_2);
        $payload = programmit_adduser_payload(
            $db,
            $user_name,
            $user_pass,
            $user_email,
            $full_name,
            $v2ray,
            $user_level,
            $is_groupname,
            $effectiveUplineId,
            $category,
            $role_dur
        );

        $insert_id = programmit_adduser_insert_user($db, $payload);
        if ($insert_id > 0) {
            programmit_adduser_ensure_profile($db, $insert_id);
            programmit_adduser_upsert_radius($db, $user_name, $user_pass, $is_groupname);
            if (function_exists('programmit_panel_access_bootstrap_user') && $user_level !== 'normal') {
                programmit_panel_access_bootstrap_user($db, $insert_id, $user_level, 0);
            }
            programmit_adduser_trigger_sync($db);
            programmit_adduser_clear_dashboard_caches(array($user_id_2, $effectiveUplineId, $insert_id));
            $db->HandleSuccess($user_name . ' agregado correctamente.');
        } else {
            $valid = false;
        }
    }

    echo $db->GetSuccessMessage();
    echo $db->GetErrorMessage();
} else {
    http_response_code(400);
    $db->HandleError('Solicitud invalida para alta de cliente.');
    echo $db->GetErrorMessage();
}
?>
