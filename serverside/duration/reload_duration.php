<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');

$root = __DIR__;
while (!is_file($root . '/includes/functions.php')) {
    $parent = dirname($root);
    if ($parent === $root) {
        break;
    }
    $root = $parent;
}
require $root . '/includes/functions.php';
chkSession();

$programmitDurationActorCanUse = (
    (int)$user_id_2 === 1 ||
    in_array($user_level_2, array('superadmin', 'administrator', 'subadmin', 'reseller', 'subreseller'), true)
);
$programmitDurationActorIsSuper = ((int)$user_id_2 === 1 || $user_level_2 === 'superadmin');

function programmitDurationJson($response, $message)
{
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=UTF-8');
    }

    echo json_encode(array(
        'response' => (int)$response,
        'message' => (string)$message
    ));
    exit;
}

function programmitDurationDbBegin($db)
{
    if (method_exists($db, 'begin_transaction')) {
        return $db->begin_transaction();
    }

    return ($db->sql_query('BEGIN') !== false);
}

function programmitDurationDbCommit($db)
{
    if (method_exists($db, 'commit')) {
        return $db->commit();
    }

    return ($db->sql_query('COMMIT') !== false);
}

function programmitDurationDbRollback($db)
{
    if (method_exists($db, 'rollback')) {
        return $db->rollback();
    }

    return ($db->sql_query('ROLLBACK') !== false);
}

function programmitDurationCreditLogColumns($db)
{
    static $columns = null;

    if (is_array($columns)) {
        return $columns;
    }

    $columns = array();
    $qry = $db->sql_query("SHOW COLUMNS FROM credits_logs");
    if ($qry) {
        while ($row = $db->sql_fetchrow($qry)) {
            if ($row && isset($row['Field'])) {
                $columns[(string)$row['Field']] = true;
            }
        }
    }

    return $columns;
}

function programmitDurationInsertCreditLog($db, $actorId, $actorName, $qty, $loggedAt)
{
    $columns = programmitDurationCreditLogColumns($db);
    $payload = array(
        'credits_id' => (string)$actorId,
        'credits_id2' => (string)$actorId,
        'credits_username' => (string)$actorName,
        'credits_qty' => (string)$qty,
        'credits_date' => (string)$loggedAt
    );

    if (isset($columns['seller'])) {
        $payload['seller'] = (string)$actorName;
    }

    $queries = array();

    if (!empty($columns)) {
        $insertCols = array();
        $insertVals = array();
        foreach ($payload as $column => $value) {
            if (!isset($columns[$column])) {
                continue;
            }

            $insertCols[] = "`" . $column . "`";
            $insertVals[] = "'" . $db->SanitizeForSQL($value) . "'";
        }

        if (count($insertCols) >= 4) {
            $queries[] = "INSERT INTO credits_logs (" . implode(', ', $insertCols) . ")
                VALUES (" . implode(', ', $insertVals) . ")";
        }
    }

    $queries[] = "INSERT INTO credits_logs
        (`credits_id`, `credits_id2`, `credits_username`, `credits_qty`, `credits_date`, `seller`)
        VALUES
        ('" . $db->SanitizeForSQL((string)$actorId) . "',
         '" . $db->SanitizeForSQL((string)$actorId) . "',
         '" . $db->SanitizeForSQL((string)$actorName) . "',
         '" . $db->SanitizeForSQL((string)$qty) . "',
         '" . $db->SanitizeForSQL((string)$loggedAt) . "',
         '" . $db->SanitizeForSQL((string)$actorName) . "')";

    $queries[] = "INSERT INTO credits_logs
        (`credits_id`, `credits_id2`, `credits_username`, `credits_qty`, `credits_date`)
        VALUES
        ('" . $db->SanitizeForSQL((string)$actorId) . "',
         '" . $db->SanitizeForSQL((string)$actorId) . "',
         '" . $db->SanitizeForSQL((string)$actorName) . "',
         '" . $db->SanitizeForSQL((string)$qty) . "',
         '" . $db->SanitizeForSQL((string)$loggedAt) . "')";

    $queries = array_values(array_unique($queries));
    foreach ($queries as $query) {
        if ($db->sql_query($query)) {
            return true;
        }
    }

    return false;
}

function programmitDurationDecodeDouble($db, $value)
{
    $decoded = $db->encryptor('decrypt', $value);
    return $db->encryptor('decrypt', $decoded);
}

function programmitDurationBalanceColumn($category)
{
    if ($category === 'premium') {
        return 'duration';
    }
    if ($category === 'vip') {
        return 'vip_duration';
    }
    if ($category === 'private') {
        return 'private_duration';
    }

    return '';
}

function programmitDurationStatusLabel($category)
{
    if ($category === 'premium') {
        return 'Premium';
    }
    if ($category === 'vip') {
        return 'VIP';
    }
    if ($category === 'private') {
        return 'Private';
    }

    return '';
}

function programmitDurationRemainingText($db, $seconds)
{
    $time = $db->calc_time(max(0, (int)$seconds));

    return $time['days'] . ' dia(s), ' . $time['hours'] . ' hora(s) y ' . $time['minutes'] . ' minuto(s)';
}

if (!$programmitDurationActorCanUse) {
    if (isset($_POST['submitted'])) {
        $db->HandleError('No tienes permisos para acceder a esta pagina.');
        programmitDurationJson(0, $db->GetErrorMessage());
    }

    $db->RedirectToURL($db->base_url());
    exit;
}

if (isset($_POST['submitted'])) {
    if (
        empty($_POST['duration_secret']) ||
        empty($_POST['duration_code']) ||
        empty($_POST['duration']) ||
        empty($_POST['category'])
    ) {
        $db->HandleError('No se pudo procesar la solicitud.');
        programmitDurationJson(0, $db->GetErrorMessage());
    }

    $category = $db->encryptor('decrypt', $_POST['category']);
    $balanceColumn = programmitDurationBalanceColumn($category);
    $status = programmitDurationStatusLabel($category);
    if ($balanceColumn === '' || $status === '') {
        $db->HandleError('Transaccion invalida.');
        programmitDurationJson(0, $db->GetErrorMessage());
    }

    $durid = (int)$db->Sanitize($db->encryptor('decrypt', urldecode((string)$_POST['duration'])));
    $d_qry = $db->sql_query("SELECT id, duration_name, duration_time FROM duration WHERE id = '" . $db->SanitizeForSQL($durid) . "' LIMIT 1");
    $d_row = $db->sql_fetchrow($d_qry);
    if (!$d_row || !isset($d_row['duration_time'])) {
        $db->HandleError('Duracion invalida.');
        programmitDurationJson(0, $db->GetErrorMessage());
    }

    $d_time = (int)$d_row['duration_time'];
    $d_name = (string)$d_row['duration_name'];
    $d_label = function_exists('programmit_translate_duration_name') ? programmit_translate_duration_name($d_name) : $d_name;
    $uid = (int)$db->Sanitize(programmitDurationDecodeDouble($db, (string)$_POST['duration_code']));
    $uname = (string)$db->Sanitize(programmitDurationDecodeDouble($db, (string)$_POST['duration_secret']));

    $targetQry = $db->sql_query(
        "SELECT user_id, user_name, user_level, upline, is_groupname
         FROM users
         WHERE user_id='" . $db->SanitizeForSQL($uid) . "'
           AND user_name='" . $db->SanitizeForSQL($uname) . "'
         LIMIT 1"
    );
    $target = $db->sql_fetchrow($targetQry);
    if (!$target || empty($target['user_id'])) {
        $db->HandleError('No se pudo recargar la duracion ' . $status . '.');
        programmitDurationJson(0, $db->GetErrorMessage());
    }

    if (
        !$programmitDurationActorIsSuper &&
        function_exists('programmit_panel_target_is_in_scope') &&
        !programmit_panel_target_is_in_scope(
            (int)$user_id_2,
            (string)$user_level_2,
            (int)$target['user_id'],
            (string)$target['user_level'],
            (int)$target['upline']
        )
    ) {
        $db->HandleError('No tienes permisos para acceder a esta cuenta.');
        programmitDurationJson(0, $db->GetErrorMessage());
    }

    $loggedAt = date('Y-m-d H:i:s');
    $ipAddress = $db->get_client_ip();
    $ssId = rand(0, 65535);
    $isFreeGroup = ((string)$target['is_groupname'] === 'free');

    if ($d_time > 0) {
        if ($category === 'premium') {
            $targetUpdate = ($isFreeGroup ? "is_groupname='normal', " : '') .
                "duration=GREATEST(0, duration+" . $d_time . ")";
        } elseif ($category === 'vip') {
            $targetUpdate = ($isFreeGroup ? "is_groupname='normal', " : '') .
                "ss_id='" . $db->SanitizeForSQL($ssId) . "', is_vip=1, vip_duration=GREATEST(0, vip_duration+" . $d_time . ")";
        } else {
            $targetUpdate = ($isFreeGroup ? "is_groupname='normal', " : '') .
                "ss_id='" . $db->SanitizeForSQL($ssId) . "', is_private=1, private_duration=GREATEST(0, private_duration+" . $d_time . ")";
        }
    } else {
        if ($category === 'premium') {
            $targetUpdate = "duration=GREATEST(0, duration+" . $d_time . ")";
        } elseif ($category === 'vip') {
            $targetUpdate = "vip_duration=GREATEST(0, vip_duration+" . $d_time . ")";
        } else {
            $targetUpdate = "private_duration=GREATEST(0, private_duration+" . $d_time . ")";
        }
    }

    if ($programmitDurationActorIsSuper) {
        if ($d_time < -2592000) {
            $db->HandleError('Transaccion invalida para ' . $status . '.');
            programmitDurationJson(0, $db->GetErrorMessage());
        }

        if (!programmitDurationDbBegin($db)) {
            $db->HandleError('No se pudo iniciar la transaccion de duracion ' . $status . '.');
            programmitDurationJson(0, $db->GetErrorMessage());
        }

        try {
            $update = $db->sql_query(
                "UPDATE users SET " . $targetUpdate . "
                 WHERE user_id='" . $db->SanitizeForSQL($uid) . "'
                   AND user_name='" . $db->SanitizeForSQL($uname) . "'"
            );
            if (!$update) {
                throw new Exception('target_update_failed');
            }

            $logInsert = $db->sql_query(
                "INSERT INTO reloadduration_logs
                (duration_id, duration_id2, duration_username, duration_item, duration_date, duration_type, ipaddress)
                VALUES
                ('" . $db->SanitizeForSQL($user_id_2) . "',
                 '" . $db->SanitizeForSQL($uid) . "',
                 '" . $db->SanitizeForSQL($uname) . "',
                 '" . $db->SanitizeForSQL($d_name) . "',
                 '" . $db->SanitizeForSQL($loggedAt) . "',
                 '" . $db->SanitizeForSQL($category) . "',
                 '" . $db->SanitizeForSQL($ipAddress) . "')"
            );
            if (!$logInsert) {
                throw new Exception('log_insert_failed');
            }

            if (!programmitDurationDbCommit($db)) {
                throw new Exception('commit_failed');
            }
        } catch (Exception $e) {
            programmitDurationDbRollback($db);
            $db->HandleError('No se pudo recargar la duracion ' . $status . '.');
            programmitDurationJson(0, $db->GetErrorMessage());
        }

        $db->HandleSuccess('Listo. Se recargo ' . $d_label . ' ' . $status . ' al usuario ' . $uname . '.');
        programmitDurationJson(1, $db->GetSuccessMessage());
    }

    if ($d_time <= 0) {
        $db->HandleError('Solo el Super-Administrador puede remover tiempo con MDURATION.');
        programmitDurationJson(0, $db->GetErrorMessage());
    }

    $actorQry = $db->sql_query(
        "SELECT user_id, user_name, credits, duration, vip_duration, private_duration
         FROM users
         WHERE user_id='" . $db->SanitizeForSQL($user_id_2) . "'
         LIMIT 1"
    );
    $actor = $db->sql_fetchrow($actorQry);
    if (!$actor || empty($actor['user_id'])) {
        $db->HandleError('Cuenta del operador invalida.');
        programmitDurationJson(0, $db->GetErrorMessage());
    }

    $actorCredits = (int)$actor['credits'];
    $actorBalance = (int)$actor[$balanceColumn];
    $packSeconds = 604800;
    $creditsNeeded = 0;
    if ($actorBalance < $d_time) {
        $creditsNeeded = (int)ceil(($d_time - $actorBalance) / $packSeconds);
    }

    if ($creditsNeeded > 0 && $actorCredits < $creditsNeeded) {
        $db->HandleError('Creditos insuficientes. 1 credito desbloquea 7 dias para MDURATION. Necesitas ' . $creditsNeeded . ' credito(s).');
        programmitDurationJson(0, $db->GetErrorMessage());
    }

    $topupSeconds = $creditsNeeded * $packSeconds;
    $remainingBalance = max(0, $actorBalance + $topupSeconds - $d_time);

    if (!programmitDurationDbBegin($db)) {
        $db->HandleError('No se pudo iniciar la transaccion de duracion ' . $status . '.');
        programmitDurationJson(0, $db->GetErrorMessage());
    }

    try {
        $actorUpdate = $db->sql_query(
            "UPDATE users SET
                credits = credits - '" . $db->SanitizeForSQL($creditsNeeded) . "',
                " . $balanceColumn . " = GREATEST(0, " . $balanceColumn . " + '" . $db->SanitizeForSQL($topupSeconds) . "' - '" . $db->SanitizeForSQL($d_time) . "')
             WHERE user_id='" . $db->SanitizeForSQL($user_id_2) . "'"
        );
        if (!$actorUpdate) {
            throw new Exception('actor_update_failed');
        }

        $targetUpdateResult = $db->sql_query(
            "UPDATE users SET " . $targetUpdate . "
             WHERE user_id='" . $db->SanitizeForSQL($uid) . "'
               AND user_name='" . $db->SanitizeForSQL($uname) . "'"
        );
        if (!$targetUpdateResult) {
            throw new Exception('target_update_failed');
        }

        $logInsert = $db->sql_query(
            "INSERT INTO reloadduration_logs
            (duration_id, duration_id2, duration_username, duration_item, duration_date, duration_type, ipaddress)
            VALUES
            ('" . $db->SanitizeForSQL($user_id_2) . "',
             '" . $db->SanitizeForSQL($uid) . "',
             '" . $db->SanitizeForSQL($uname) . "',
             '" . $db->SanitizeForSQL($d_name) . "',
             '" . $db->SanitizeForSQL($loggedAt) . "',
             '" . $db->SanitizeForSQL($category) . "',
             '" . $db->SanitizeForSQL($ipAddress) . "')"
        );
        if (!$logInsert) {
            throw new Exception('duration_log_failed');
        }

        if ($creditsNeeded > 0) {
            if (!programmitDurationInsertCreditLog($db, (int)$actor['user_id'], (string)$actor['user_name'], '-' . $creditsNeeded, $loggedAt)) {
                throw new Exception('credits_log_failed');
            }
        }

        if (!programmitDurationDbCommit($db)) {
            throw new Exception('commit_failed');
        }
    } catch (Exception $e) {
        programmitDurationDbRollback($db);
        $db->HandleError('No se pudo recargar la duracion ' . $status . '.');
        programmitDurationJson(0, $db->GetErrorMessage());
    }

    $message = 'Listo. Se recargo ' . $d_label . ' ' . $status . ' al usuario ' . $uname . '.';
    if ($creditsNeeded > 0) {
        $message .= ' Se desconto ' . $creditsNeeded . ' credito(s) y se cargaron ' . ($creditsNeeded * 7) . ' dia(s) a tu saldo de MDURATION.';
    }
    $message .= ' Saldo restante: ' . programmitDurationRemainingText($db, $remainingBalance) . '.';

    $db->HandleSuccess($message);
    programmitDurationJson(1, $db->GetSuccessMessage());
}

if (empty($_POST['duration']) || empty($_POST['duration_code']) || empty($_POST['duration_secret'])) {
    echo '<script> alert("Invalid Transaction"); location.assign("' . $db->base_url() . '404")</script>';
    exit;
}
?>
