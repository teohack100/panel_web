<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/db_config.php';
require_once __DIR__ . '/../includes/mysql.class.php';

$db = new mysql_db();
$db->InitDB($DB_host, $DB_user, $DB_pass, $DB_name);
if (!$db->DBLogin()) {
  echo json_encode(["ok" => 0, "error" => "db_connect"]);
  exit();
}

$username = isset($_GET['username']) ? trim($_GET['username']) : '';
$password = isset($_GET['password']) ? trim($_GET['password']) : '';

if ($username === '' || $password === '') {
  echo json_encode(["ok" => 0, "error" => "missing_params"]);
  exit();
}

function calc_time_api($seconds) {
  $seconds = max(0, (int)$seconds);
  $days = (int)($seconds / 86400);
  $seconds -= ($days * 86400);

  $hours = 0;
  $minutes = 0;

  if ($seconds > 0) {
    $hours = (int)($seconds / 3600);
    $seconds -= ($hours * 3600);
  }
  if ($seconds > 0) {
    $minutes = (int)($seconds / 60);
    $seconds -= ($minutes * 60);
  }

  return [
    "days" => (int)$days,
    "hours" => (int)$hours,
    "minutes" => (int)$minutes,
    "seconds" => (int)$seconds
  ];
}

$safeUser = $db->SanitizeForSQL($username);
$safePass = $db->SanitizeForSQL($password);
$sql = "
SELECT
  COALESCE(duration,0) AS duration,
  COALESCE(vip_duration,0) AS vip_duration,
  COALESCE(private_duration,0) AS private_duration,
  COALESCE(is_ban,0) AS is_ban,
  COALESCE(is_freeze,0) AS is_freeze,
  status,
  user_id
FROM users
WHERE LOWER(TRIM(user_name)) = LOWER(TRIM('".$safeUser."'))
  AND auth_vpn = MD5('".$safePass."')
  AND status = 'live'
  AND COALESCE(is_freeze,0) = 0
  AND COALESCE(is_ban,0) = 0
ORDER BY GREATEST(COALESCE(duration,0), COALESCE(vip_duration,0), COALESCE(private_duration,0)) DESC, user_id DESC
LIMIT 1
";
$res = $db->sql_query($sql);

if (!$res || $db->sql_numrows($res) < 1) {
  echo json_encode(["ok" => 0, "error" => "user_or_pass_invalid"]);
  exit();
}

$row = $db->sql_fetchrow($res);
$duration = (int)$row['duration'];
$vip = (int)$row['vip_duration'];
$priv = (int)$row['private_duration'];

$plan = "NONE";
$remaining = 0;

if ($priv > 0) {
  $plan = "PRIVATE";
  $remaining = $priv;
} elseif ($vip > 0) {
  $plan = "VIP";
  $remaining = $vip;
} elseif ($duration > 0) {
  $plan = "PRE";
  $remaining = $duration;
}

if ($remaining <= 0) {
  echo json_encode([
    "ok" => 0,
    "error" => "no_duration",
    "duration" => $duration,
    "vip_duration" => $vip,
    "private_duration" => $priv
  ]);
  exit();
}

$t = calc_time_api($remaining);

echo json_encode([
  "ok" => 1,
  "plan" => $plan,
  "remaining_seconds" => $remaining,
  "duration" => $duration,
  "vip_duration" => $vip,
  "private_duration" => $priv,
  "days" => $t["days"],
  "hours" => $t["hours"],
  "minutes" => $t["minutes"],
  "seconds" => $t["seconds"]
]);

