<?php
chkSession();
$read_cookie = explode("|", $db->decrypt_key($user));
$user_qry = $db->sql_query("SELECT user_id FROM users WHERE user_name='$read_cookie[1]' AND user_pass='$read_cookie[2]'");
$qryrow = $db->sql_fetchrow($user_qry);	
$user_id = $qryrow['user_id'];
if($user_id){
	$db->sql_query("UPDATE users SET last_active_time = NOW(), login_status='online' WHERE user_id = '".$user_id."'") OR die();
}
?>