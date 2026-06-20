<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin'  || $user_level_2 == 'administrator'){
}else{
	echo '<script>alert("Sorry! You dont have Permission to Access this Page!...");</script>';
	$db->RedirectToURL($db->base_url());
	exit;
}

header('Content-Type: application/json; charset=utf-8');

$server_id = isset($_GET['server_id']) ? (int)$db->Sanitize((string)$_GET['server_id']) : 0;
if($server_id <= 0){
	echo json_encode(array('error' => 'invalid_server_id'));
	exit;
}

$server_id_sql = $db->SanitizeForSQL((string)$server_id);
$qry = $db->sql_query("SELECT server_id, server_name, server_category, server_ip, server_port, server_parser, status FROM server_list WHERE server_id='".$server_id_sql."' LIMIT 1");
$data = $qry ? $db->sql_fetchrow($qry) : array();

if(!$data){
	echo json_encode(array('error' => 'server_not_found'));
	exit;
}

echo json_encode($data);
?>
