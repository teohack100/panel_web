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

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
	$db->HandleError("Invalid transaction");
	echo $db->GetErrorMessage();
	exit;
}

$submitted = isset($_POST['submitted']) ? trim((string)$_POST['submitted']) : '';
if($submitted !== 'Server Upload' && $submitted !== 'Server Edit'){
	$db->HandleError("Invalid transaction");
	echo $db->GetErrorMessage();
	exit;
}

$server_name = trim($db->Sanitize((string)($_POST['server_name'] ?? '')));
$server_category = strtolower(trim($db->Sanitize((string)($_POST['server_category'] ?? ''))));
$server_ip = trim($db->Sanitize((string)($_POST['server_ip'] ?? '')));
$server_port_raw = trim($db->Sanitize((string)($_POST['server_port'] ?? '')));
$allowed_categories = array('premium', 'vip', 'private', 'free', 'ph');

if($server_name === ''){
	$db->HandleError("Empty Server Name");
	echo $db->GetErrorMessage();
	exit;
}

if(!in_array($server_category, $allowed_categories, true)){
	$db->HandleError("Invalid server category");
	echo $db->GetErrorMessage();
	exit;
}

if($server_ip === ''){
	$db->HandleError("Empty Server IP Address");
	echo $db->GetErrorMessage();
	exit;
}

$is_ipv4 = filter_var($server_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
$is_hostname = (bool)preg_match('/^(?=.{1,253}$)(?!-)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/i', $server_ip);
if(!$is_ipv4 && !$is_hostname){
	$db->HandleError("Invalid Server IP Address");
	echo $db->GetErrorMessage();
	exit;
}

$server_port = (int)$server_port_raw;
if($server_port <= 0 || $server_port > 65535){
	$db->HandleError("Invalid Server Port");
	echo $db->GetErrorMessage();
	exit;
}

$server_id = 0;
if($submitted === 'Server Edit'){
	$server_id = (int)$db->Sanitize((string)($_POST['server_id'] ?? '0'));
	if($server_id <= 0){
		$db->HandleError("Invalid Server ID");
		echo $db->GetErrorMessage();
		exit;
	}
}

$server_name_sql = $db->SanitizeForSQL($server_name);
$server_category_sql = $db->SanitizeForSQL($server_category);
$server_ip_sql = $db->SanitizeForSQL($server_ip);
$server_port_sql = $db->SanitizeForSQL((string)$server_port);
$server_parser_sql = $db->SanitizeForSQL('http://'.$server_ip.':'.$server_port);
$exclude_sql = $server_id > 0 ? " AND server_id <> '".$db->SanitizeForSQL((string)$server_id)."'" : '';

$name_exists_qry = $db->sql_query("SELECT server_id FROM server_list WHERE server_name='".$server_name_sql."'".$exclude_sql." LIMIT 1");
if($name_exists_qry && $db->sql_numrows($name_exists_qry) > 0){
	$db->HandleError($server_name.' is already in our database!');
	echo $db->GetErrorMessage();
	exit;
}

$ip_exists_qry = $db->sql_query("SELECT server_id FROM server_list WHERE server_ip='".$server_ip_sql."'".$exclude_sql." LIMIT 1");
if($ip_exists_qry && $db->sql_numrows($ip_exists_qry) > 0){
	$db->HandleError($server_ip.' is already in our database!');
	echo $db->GetErrorMessage();
	exit;
}

if($submitted === 'Server Upload'){
	$sql_upload = "INSERT INTO server_list
	(server_name, server_category, server_ip, server_port, server_parser, status)
	VALUES
	('".$server_name_sql."','".$server_category_sql."','".$server_ip_sql."','".$server_port_sql."','".$server_parser_sql."','1')";
	$upload = $db->sql_query($sql_upload);
	if($upload){
		$db->HandleSuccess("Server successfully added");
	}else{
		$db->HandleError("Failed to add server");
	}
}else{
	$server_id_sql = $db->SanitizeForSQL((string)$server_id);
	$sql_update = "UPDATE server_list SET
	server_name='".$server_name_sql."',
	server_category='".$server_category_sql."',
	server_ip='".$server_ip_sql."',
	server_port='".$server_port_sql."',
	server_parser='".$server_parser_sql."'
	WHERE server_id='".$server_id_sql."'
	LIMIT 1";
	$update = $db->sql_query($sql_update);
	if($update){
		$db->HandleSuccess("Server updated successfully");
	}else{
		$db->HandleError("Failed to update server");
	}
}

echo $db->GetSuccessMessage();
echo $db->GetErrorMessage();
?>
