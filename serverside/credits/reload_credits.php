<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');

require_once '../../includes/functions.php';
chkSession();

if (!headers_sent()) {
    header('Content-Type: application/json; charset=UTF-8');
}

$actorFlags = function_exists('programmit_panel_actor_flags')
    ? programmit_panel_actor_flags($user_id_2, $user_level_2)
    : array(
        'can_adjust_credits' => ($user_id_2 == 1 || in_array($user_level_2, array('superadmin', 'administrator', 'subadmin', 'reseller', 'subreseller'), true)),
        'can_subtract_credits' => ($user_id_2 == 1 || in_array($user_level_2, array('superadmin', 'administrator', 'subadmin', 'reseller', 'subreseller'), true))
    );

function programmitCreditsJson($response, $message = '')
{
    echo json_encode(array(
        'response' => (int)$response,
        'message' => (string)$message
    ));
    exit;
}

function programmitCreditsDbBegin($db)
{
    if (isset($db->connection) && is_object($db->connection) && method_exists($db->connection, 'begin_transaction')) {
        return $db->connection->begin_transaction();
    }

    return ($db->sql_query('BEGIN') !== false);
}

function programmitCreditsDbCommit($db)
{
    if (isset($db->connection) && is_object($db->connection) && method_exists($db->connection, 'commit')) {
        return $db->connection->commit();
    }

    return ($db->sql_query('COMMIT') !== false);
}

function programmitCreditsDbRollback($db)
{
    if (isset($db->connection) && is_object($db->connection) && method_exists($db->connection, 'rollback')) {
        return $db->connection->rollback();
    }

    return ($db->sql_query('ROLLBACK') !== false);
}

function programmitCreditsLogColumns($db)
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

function programmitCreditsInsertLog($db, $actorId, $actorName, $targetId, $targetUserName, $qty, $loggedAt)
{
    $columns = programmitCreditsLogColumns($db);
    $payload = array(
        'credits_id' => (string)$actorId,
        'credits_id2' => (string)$targetId,
        'credits_username' => (string)$targetUserName,
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
         '" . $db->SanitizeForSQL((string)$targetId) . "',
         '" . $db->SanitizeForSQL((string)$targetUserName) . "',
         '" . $db->SanitizeForSQL((string)$qty) . "',
         '" . $db->SanitizeForSQL((string)$loggedAt) . "',
         '" . $db->SanitizeForSQL((string)$actorName) . "')";

    $queries[] = "INSERT INTO credits_logs
        (`credits_id`, `credits_id2`, `credits_username`, `credits_qty`, `credits_date`)
        VALUES
        ('" . $db->SanitizeForSQL((string)$actorId) . "',
         '" . $db->SanitizeForSQL((string)$targetId) . "',
         '" . $db->SanitizeForSQL((string)$targetUserName) . "',
         '" . $db->SanitizeForSQL((string)$qty) . "',
         '" . $db->SanitizeForSQL((string)$loggedAt) . "')";

    $queries = array_values(array_unique($queries));
    foreach ($queries as $query) {
        if ($db->sql_query($query)) {
            return true;
        }
    }

    error_log('programmit credits log insert failed: ' . (isset($db->connection->error) ? (string)$db->connection->error : 'unknown'));
    return false;
}

if (!$actorFlags['can_adjust_credits']) {
    if (isset($_POST['submitted'])) {
        $db->HandleError('Sorry! You dont have Permission to Access this Page!...');
        programmitCreditsJson(2, $db->GetErrorMessage());
    }

    $db->RedirectToURL($db->base_url());
    exit;
}

if (!isset($_POST['submitted'])) {
    $db->RedirectToURL($db->base_url());
    exit;
}

if (
    empty($_POST['add_credits']) ||
    empty($_POST['credits_secret']) ||
    empty($_POST['credits_code']) ||
    empty($_POST['category'])
) {
    $db->HandleError('Sorry! the transaction is inavalid!..');
    programmitCreditsJson(2, $db->GetErrorMessage());
}

$category = $db->Sanitize($db->encryptor('decrypt', $_POST['category']));
$targetUsername = $db->Sanitize($db->encryptor('decrypt', $db->encryptor('decrypt', $_POST['credits_secret'])));
$targetUserId = (int)$db->Sanitize($db->encryptor('decrypt', $db->encryptor('decrypt', $_POST['credits_code'])));
$creditsRaw = trim((string)$_POST['add_credits']);
$credits = (int)$db->Sanitize($creditsRaw);
$actorUserName = isset($user_name_2) ? (string)$user_name_2 : '';

if ($credits <= 0 || preg_match('/[^0-9]/', $creditsRaw)) {
    $db->HandleError('Invalid input!');
    programmitCreditsJson(2, $db->GetErrorMessage());
}

if (!in_array($category, array('add', 'substract'), true)) {
    $db->HandleError('Sorry! the transaction is inavalid!..');
    programmitCreditsJson(2, $db->GetErrorMessage());
}

if ($category === 'substract' && !$actorFlags['can_subtract_credits']) {
    $db->HandleError('Sorry! You dont have Permission to Access this Page!...');
    programmitCreditsJson(2, $db->GetErrorMessage());
}

$targetResult = $db->sql_query(
    "SELECT user_id, user_name, user_level, credits, upline
     FROM users
     WHERE user_id='" . $db->SanitizeForSQL($targetUserId) . "'
       AND user_name='" . $db->SanitizeForSQL($targetUsername) . "'
     LIMIT 1"
);
$target = $db->sql_fetchrow($targetResult);

if (empty($target['user_id'])) {
    $db->HandleError('Invalid target account!');
    programmitCreditsJson(2, $db->GetErrorMessage());
}

if ((string)$target['user_level'] === 'normal') {
    $db->HandleError('Sorry Your Request Cannot be Processed to a Member!');
    programmitCreditsJson(2, $db->GetErrorMessage());
}

if (function_exists('programmit_panel_target_is_in_scope')) {
    $isAllowedTarget = programmit_panel_target_is_in_scope(
        $user_id_2,
        $user_level_2,
        (int)$target['user_id'],
        (string)$target['user_level'],
        (int)$target['upline']
    );
} else {
    $isAllowedTarget = ($user_id_2 == 1 || $user_level_2 == 'superadmin' || (int)$target['upline'] === (int)$user_id_2);
}

if (!$isAllowedTarget) {
    $db->HandleError('Sorry! You dont have Permission to Access this Account!');
    programmitCreditsJson(2, $db->GetErrorMessage());
}

$actorCreditsResult = $db->sql_query(
    "SELECT credits FROM users WHERE user_id='" . $db->SanitizeForSQL($user_id_2) . "' LIMIT 1"
);
$actorCreditsRow = $db->sql_fetchrow($actorCreditsResult);
$actorCredits = (int)($actorCreditsRow['credits'] ?? 0);
$targetCredits = (int)$target['credits'];
$hasUnlimitedCredits = function_exists('programmit_panel_has_unlimited_credits')
    ? programmit_panel_has_unlimited_credits($user_id_2, $user_level_2)
    : ($user_id_2 == 1 || $user_level_2 == 'superadmin');

if ($category === 'add' && !$hasUnlimitedCredits && $actorCredits < $credits) {
    $db->HandleError("Sorry! You don't have much Credits!");
    programmitCreditsJson(2, $db->GetErrorMessage());
}

if ($category === 'substract' && $targetCredits < $credits) {
    $db->HandleError('Sorry! ' . $credits . ' Decreasing Credits Failed! Transaction...');
    programmitCreditsJson(2, $db->GetErrorMessage());
}

$targetNewCredits = ($category === 'add')
    ? ($targetCredits + $credits)
    : ($targetCredits - $credits);

if (!programmitCreditsDbBegin($db)) {
    $db->HandleError('Credits Failed! Transaction open error...');
    programmitCreditsJson(2, $db->GetErrorMessage());
}

try {
    $targetUpdate = $db->sql_query(
        "UPDATE users
         SET credits='" . $db->SanitizeForSQL($targetNewCredits) . "'
         WHERE user_id='" . $db->SanitizeForSQL($target['user_id']) . "'
           AND user_name='" . $db->SanitizeForSQL($target['user_name']) . "'"
    );

    if (!$targetUpdate) {
        throw new Exception($credits . ' Credits Failed! Transaction...');
    }

    if (!$hasUnlimitedCredits) {
        if ($category === 'add') {
            $actorUpdate = $db->sql_query(
                "UPDATE users
                 SET credits = credits - '" . $db->SanitizeForSQL($credits) . "'
                 WHERE user_id='" . $db->SanitizeForSQL($user_id_2) . "'"
            );
        } else {
            $actorUpdate = $db->sql_query(
                "UPDATE users
                 SET credits = credits + '" . $db->SanitizeForSQL($credits) . "'
                 WHERE user_id='" . $db->SanitizeForSQL($user_id_2) . "'"
            );
        }

        if (!$actorUpdate) {
            throw new Exception('Credits Failed! Actor balance update error...');
        }
    }

    $signedQty = ($category === 'add') ? $credits : (0 - $credits);
    $logInsert = programmitCreditsInsertLog(
        $db,
        $user_id_2,
        $actorUserName,
        (int)$target['user_id'],
        (string)$target['user_name'],
        $signedQty,
        date('Y-m-d H:i:s')
    );

    if (!$logInsert) {
        throw new Exception('Credits Failed! Log insert error...');
    }

    if (!programmitCreditsDbCommit($db)) {
        throw new Exception('Credits Failed! Commit error...');
    }
} catch (Exception $e) {
    programmitCreditsDbRollback($db);
    $db->HandleError($e->getMessage());
    programmitCreditsJson(2, $db->GetErrorMessage());
}

if ($category === 'add') {
    $db->HandleSuccess($credits . ' Credits Successfully! Added to ' . $target['user_name']);
} else {
    $db->HandleSuccess($credits . ' Credits Successfully! Substracted to ' . $target['user_name']);
}

programmitCreditsJson(1, $db->GetSuccessMessage());
