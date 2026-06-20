<?php
$wasAdminSession = (isset($_COOKIE['panel_admin_auth']) && trim((string)$_COOKIE['panel_admin_auth']) !== '');

if(!empty($user)){
	$read_cookie = explode("|", $db->decrypt_key($user));
	if(isset($read_cookie[1], $read_cookie[2])){
		$db->sql_query("UPDATE users SET login_status='offline' WHERE user_name='".$db->SanitizeForSQL($read_cookie[1])."' AND user_pass='".$db->SanitizeForSQL($read_cookie[2])."'");
	}
}

clear_auth_cookies();
$user = "";
unset($user);
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
$redirectUrl = $db->base_url();
if($wasAdminSession){
	$redirectUrl = $db->base_url()."admin";
}
header("Location: ".$redirectUrl);
exit;
?>
