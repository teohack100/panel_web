<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/db_config.php';
require_once __DIR__ . '/../includes/mysql.class.php';

$db = new mysql_db();
$db->InitDB($DB_host, $DB_user, $DB_pass, $DB_name);
if (!$db->DBLogin()) {
    echo json_encode(array('error' => 'db_connect'));
    exit();
}

if (empty($_GET['username'])) {
    echo json_encode(array('error' => 'username/password'));
    exit();
}

$user_id = trim($_GET['username']);
$safeUser = $db->SanitizeForSQL($user_id);
$sql = "
SELECT
  COALESCE(duration,0) AS premium,
  COALESCE(vip_duration,0) AS vip,
  COALESCE(private_duration,0) AS private
FROM users
WHERE LOWER(TRIM(user_name)) = LOWER(TRIM('".$safeUser."'))
  AND status='live'
  AND COALESCE(is_freeze,0)=0
  AND COALESCE(is_ban,0)=0
ORDER BY GREATEST(COALESCE(duration,0), COALESCE(vip_duration,0), COALESCE(private_duration,0)) DESC, user_id DESC
LIMIT 1
";
$result1 = $db->sql_query($sql);

if ($result1 && $db->sql_numrows($result1) >= 1) {
    $row = $db->sql_fetchrow($result1);
    echo json_encode($row);
} else {
    echo json_encode(array('error' => 'username/password'));
}
?>
