<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/config.php';

if(function_exists('programmit_control_is_host') && programmit_control_is_host($db)){
	if(!function_exists('programmit_control_security_allow_register') || !programmit_control_security_allow_register($db)){
		$db->HandleError('Registro deshabilitado en host de control.');
		echo $db->GetErrorMessage();
		exit;
	}
}

function programmit_render_error($db, $message){
	$db->HandleError($message);
	echo $db->GetErrorMessage();
	exit;
}

function programmit_render_success($db, $message){
	$db->HandleSuccess($message);
	echo $db->GetSuccessMessage();
	exit;
}

function programmit_generate_uuid_v4(){
	if(function_exists('random_bytes')){
		$data = random_bytes(16);
	}else{
		$data = openssl_random_pseudo_bytes(16);
	}
	if($data === false || strlen($data) < 16){
		$data = md5(uniqid((string)mt_rand(), true), true).md5(uniqid((string)mt_rand(), true), true);
		$data = substr($data, 0, 16);
	}
	$data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
	$data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
	return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

if(!isset($_POST['submitted'])){
	$db->RedirectToURL($db->base_url());
	exit;
}

$user_email = strtolower(trim((string)($_POST['user_email'] ?? '')));
$user_pass = trim((string)($_POST['user_pass'] ?? ''));
$user_pass2 = trim((string)($_POST['user_pass2'] ?? ''));

if($user_email === ''){
	programmit_render_error($db, 'Email is required.');
}

if(!filter_var($user_email, FILTER_VALIDATE_EMAIL)){
	programmit_render_error($db, 'Please enter a valid email address.');
}

if($user_pass === '' || $user_pass2 === ''){
	programmit_render_error($db, 'Password and confirmation are required.');
}

if(strlen($user_pass) < 8){
	programmit_render_error($db, 'Password must be at least 8 characters.');
}

if(!preg_match('/[0-9\W_]/', $user_pass)){
	programmit_render_error($db, 'Password must include at least one number or symbol.');
}

if($user_pass !== $user_pass2){
	programmit_render_error($db, 'Passwords do not match.');
}

$email_exists_qry = $db->sql_query("SELECT user_id
	FROM users
	WHERE LOWER(TRIM(user_email)) = '".$db->SanitizeForSQL($user_email)."'
	LIMIT 1");
if($email_exists_qry && $db->sql_numrows($email_exists_qry) > 0){
	programmit_render_error($db, 'This email is already registered.');
}

$local_part = explode('@', $user_email);
$candidate = isset($local_part[0]) ? $local_part[0] : 'user';
$candidate = preg_replace('/[^a-zA-Z0-9._-]/', '', $candidate);
$candidate = trim($candidate, '._-');
if(strlen($candidate) < 3){
	$candidate = 'user'.substr(md5($user_email), 0, 6);
}
if(strlen($candidate) > 32){
	$candidate = substr($candidate, 0, 32);
}

$user_name = $candidate;
$attempt = 0;
while($attempt < 60){
	$user_chk = $db->sql_query("SELECT user_id FROM users WHERE user_name='".$db->SanitizeForSQL($user_name)."' LIMIT 1");
	if(!$user_chk || $db->sql_numrows($user_chk) < 1){
		break;
	}
	$attempt++;
	$suffix = (string)random_int(100, 9999);
	$max_root = 32 - strlen($suffix);
	$root = substr($candidate, 0, $max_root > 3 ? $max_root : 3);
	$user_name = $root.$suffix;
}

if($attempt >= 60){
	programmit_render_error($db, 'Unable to generate a unique username. Please try again.');
}

$password_encrypted = $db->encrypt_key($db->encryptor('encrypt', $user_pass));
$auth_vpn = md5($user_pass);
$code = (string)random_int(10000000, 999999999);
$uuid = programmit_generate_uuid_v4();
$user_ip = $db->get_client_ip();
$now = date('Y-m-d H:i:s');

$values_map = array(
	'user_name' => $user_name,
	'user_pass' => $password_encrypted,
	'uuid' => $uuid,
	'auth_vpn' => $auth_vpn,
	'user_email' => $user_email,
	'full_name' => $user_name,
	'regdate' => $now,
	'is_groupname' => 'administrator',
	'is_active' => 1,
	'is_freeze' => 0,
	'user_level' => 'administrator',
	'code' => $code,
	'is_ban' => 0,
	'is_validated' => 1,
	'upline' => 1,
	'duration' => 0,
	'role_duration' => 0,
	'status' => 'live',
	'login_status' => 'offline',
	'password' => $user_pass,
	'pass_plain' => $user_pass,
	'ss_id' => '',
	'ssl_id' => 'ssl',
	'attribute' => 'MD5-Password',
	'op' => ':=',
	'ipaddress' => $user_ip,
	'timestamp' => 0,
	'is_passchange' => 0,
	'freeze_status' => 0,
	'last_freeze_date' => '1970-01-01 00:00:00',
	'is_connected' => 0,
	'is_offense' => 0,
	'suspended_date' => '1970-01-01 00:00:00',
	'vip_duration' => 0,
	'is_vip' => 0,
	'private_duration' => 0,
	'is_private' => 0,
	'private_slot' => 0,
	'private_control' => 0,
	'credits' => 0,
	'last_active_time' => $now,
	'bandwidth' => 0,
	'bandwidth_premium' => 0,
	'bandwidth_vip' => 0,
	'bandwidth_ph' => 0,
	'bandwidth_private' => 0,
	'bandwidth_free' => 0,
	'device_connected' => 0
);

$columns_set = array();
$users_cols_qry = $db->sql_query("SHOW COLUMNS FROM users");
while($col_row = $db->sql_fetchrow($users_cols_qry)){
	if(isset($col_row['Field'])){
		$columns_set[$col_row['Field']] = true;
	}
}

$insert_cols = array();
$insert_vals = array();
foreach($values_map as $col => $val){
	if(isset($columns_set[$col])){
		$insert_cols[] = "`".$col."`";
		$insert_vals[] = "'".$db->SanitizeForSQL((string)$val)."'";
	}
}

if(count($insert_cols) < 8){
	programmit_render_error($db, 'Users table structure is incompatible with this registration flow.');
}

$insert_sql = "INSERT INTO users (".implode(", ", $insert_cols).")
	VALUES (".implode(", ", $insert_vals).")";
$insert_ok = $db->sql_query($insert_sql);
if(!$insert_ok){
	programmit_render_error($db, 'Failed to create account. Please try again.');
}

$insert_id = (int)$db->sql_nextid();
if($insert_id > 0){
	$db->sql_query("INSERT INTO users_profile (profile_id) VALUES ('".$db->SanitizeForSQL($insert_id)."')");
	if(function_exists('programmit_panel_access_bootstrap_user')){
		programmit_panel_access_bootstrap_user($db, $insert_id, 'administrator', 0);
	}
}

$safe_user = htmlentities($user_name, ENT_QUOTES, 'UTF-8');
programmit_render_success(
	$db,
	"Account created successfully. Your username is ".$safe_user.". Redirecting to login..."
);
