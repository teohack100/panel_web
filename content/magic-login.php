<?php
require_once __DIR__ . '/../includes/auth_magic_links.php';
require_once __DIR__ . '/../includes/social_auth.php';
$_programmit_magic_redirect = $db->base_url()."index.php?p=dashboard";

if(is_logged_in($user)) {
	header("Location: ".$_programmit_magic_redirect);
	exit;
}

if(function_exists('programmit_control_is_host') && programmit_control_is_host($db)){
	if(!function_exists('programmit_control_security_allow_magic_login') || !programmit_control_security_allow_magic_login($db)){
		header("Location: ".$db->base_url()."index.php?p=login&control=magic_blocked");
		exit;
	}
}

programmit_ensure_magic_links_table($db);

$token = isset($_GET['token']) ? trim((string)$_GET['token']) : '';
if(!preg_match('/^[a-f0-9]{64}$/', $token)) {
	header("Location: ".$db->base_url()."index.php?p=login&magic=invalid");
	exit;
}

$token_hash = hash('sha256', $token);
$token_qry = $db->sql_query("SELECT id, user_id, expires_at, used_at
	FROM auth_magic_links
	WHERE token_hash='".$db->SanitizeForSQL($token_hash)."'
	LIMIT 1");
$token_row = $db->sql_fetchrow($token_qry);

if(!$token_row){
	header("Location: ".$db->base_url()."index.php?p=login&magic=invalid");
	exit;
}

$is_used = !empty($token_row['used_at']) && $token_row['used_at'] !== '0000-00-00 00:00:00';
$is_expired = (strtotime((string)$token_row['expires_at']) !== false && strtotime((string)$token_row['expires_at']) < time());
if($is_used || $is_expired){
	header("Location: ".$db->base_url()."index.php?p=login&magic=expired");
	exit;
}

$user_id = (int)$token_row['user_id'];
$user_qry = $db->sql_query("SELECT user_id, user_name, user_pass, full_name, user_email, user_level, is_active, is_ban, status, ipaddress, lastlogin
	FROM users
	WHERE user_id='".$db->SanitizeForSQL($user_id)."'
	LIMIT 1");
$user_row = $db->sql_fetchrow($user_qry);

if(!$user_row){
	header("Location: ".$db->base_url()."index.php?p=login&magic=invalid");
	exit;
}

$is_live = (isset($user_row['status']) && strtolower(trim((string)$user_row['status'])) === 'live');
if((int)$user_row['is_active'] !== 1 || (int)$user_row['is_ban'] === 1 || !$is_live){
	header("Location: ".$db->base_url()."index.php?p=login&magic=invalid");
	exit;
}

$lastlogin_parts = explode(" ", (string)$user_row['lastlogin']);
$lastlogin_date = isset($lastlogin_parts[0]) && $lastlogin_parts[0] !== '' ? $lastlogin_parts[0] : date('Y-m-d');
$lastlogin_time = isset($lastlogin_parts[1]) && $lastlogin_parts[1] !== '' ? $lastlogin_parts[1] : date('H:i:s');
$ipaddress = isset($user_row['ipaddress']) ? $user_row['ipaddress'] : $db->get_client_ip();

$cookie_payload = $db->encrypt_key(
	$user_row['user_id']."|".$user_row['user_name']."|".$user_row['user_pass']."|".$ipaddress."|".$lastlogin_date."|".$lastlogin_time."|".$user_row['user_level']
);

$exp = time() + 86400;
if(function_exists('programmit_social_set_cookie')){
	programmit_social_set_cookie('user', $cookie_payload, $exp);
	programmit_social_set_cookie('user_id', $db->encrypt_key($user_row['user_id']), $exp);
	programmit_social_set_cookie('full_name', $db->encrypt_key($user_row['full_name']), $exp);
	programmit_social_set_cookie('user_email', $db->encrypt_key($user_row['user_email']), $exp);
}else{
	setcookie('user', $cookie_payload, $exp, '/');
	setcookie('user_id', $db->encrypt_key($user_row['user_id']), $exp, '/');
	setcookie('full_name', $db->encrypt_key($user_row['full_name']), $exp, '/');
	setcookie('user_email', $db->encrypt_key($user_row['user_email']), $exp, '/');
}

$db->sql_query("UPDATE auth_magic_links SET used_at=NOW() WHERE id='".$db->SanitizeForSQL((int)$token_row['id'])."'");
$db->sql_query("UPDATE users
	SET ipaddress='".$db->SanitizeForSQL($db->get_client_ip())."',
		lastlogin=NOW(),
		login_status='online',
		last_active_time=NOW()
	WHERE user_id='".$db->SanitizeForSQL($user_id)."'");

header("Location: ".$_programmit_magic_redirect);
exit;
?>
