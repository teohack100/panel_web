<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');

require_once __DIR__ . '/../includes/functions.php';

$ok = false;
if (method_exists($db, 'DBLogin')) {
    $ok = $db->DBLogin();
}

header('Content-Type: application/json; charset=utf-8');
if ($ok) {
    $res = $db->sql_query("SELECT 1 AS ok");
    $row = $db->sql_fetchrow($res);
    echo json_encode(['ok' => 1]);
} else {
    $err = $db->GetErrorMessage();
    echo json_encode(['ok' => 0, 'error' => $err]);
}
?>
