<?php
define('DOC_ROOT_PATH', $_SERVER['DOCUMENT_ROOT'].'/');
include DOC_ROOT_PATH . './includes/config.php';
if(isset($_COOKIE['user'])) {
	$user =  $_COOKIE['user'];
}

if(isset($user)){
	$user = $db->decrypt_key($user);
	$user = addslashes($user);
	$user = $db->encrypt_key($user);
}

function is_logged_in($user) {
	global $user, $db;
	$read_cookie = explode("|", $db->decrypt_key($user));
	$result = $db->sql_query("SELECT user_name FROM users WHERE user_name='$read_cookie[1]' AND user_pass='$read_cookie[2]'");
	$num_row = $db->sql_numrows($result);
	if($num_row > 0) {
		return 1;
	}
	return 0;
}
global $user, $db;
$read_cookie_2 = explode("|", $db->decrypt_key($user));
$user_id_2 = $db->Sanitize($read_cookie_2[0]);

setcookie("user_name", $read_cookie_2[1], time()+3600, "/");

$result_2 = $db->sql_query("SELECT credits, 
								   code,
								   ss_id,
								   vip_duration,
								   private_duration,
								   private_control,
								   duration, 
								   user_level,
								   lastlogin,
								   full_name,
								   user_pass,
								   user_email,
								   user_name,
								   upline,
								   is_groupname
							FROM users WHERE user_id='$user_id_2'");
$legal_name = 'Firenet VPN';
$row_2 = $db->sql_fetchrow($result_2);
$ss_id_2 = $row_2['ss_id'];
$code_2 = $row_2['code'];
$user_level_2 = $row_2['user_level'];
$credits_2 = $row_2['credits'];
$upline_2 = $row_2['upline'];
$auth_2 = $row_2['user_pass'];
$duration_2 = $row_2['duration'];
$vip_duration_2 = $row_2['vip_duration'];
$private_duration_2 = $row_2['private_duration'];
$private_control_2 = $row_2['private_control'];
$full_name_2 = $row_2['full_name'];
$user_name_2 = $row_2['user_name'];
$user_email_2 = $row_2['user_email'];
$is_groupname_2 = $row_2['is_groupname'];
$lastlogin = date('F d, Y h:i', strtotime($row_2['lastlogin']));
$smarty->assign("ss_id_2", $ss_id_2);
$smarty->assign("code_2", $code_2);
$smarty->assign("user_name_2", $user_name_2);
$smarty->assign("user_email", $user_email_2);
$smarty->assign("full_name_2", $full_name_2);
$smarty->assign("lastlogin", $lastlogin);
$smarty->assign("user_id_2", $user_id_2);
$smarty->assign("user_level_2", $user_level_2);
$smarty->assign("credits_2", $credits_2);
$smarty->assign("duration_2", $duration_2);
$smarty->assign('vip_duration_2', $vip_duration_2);
$smarty->assign('private_duration_2', $private_duration_2);
$smarty->assign('private_control_2', $private_control_2);
$smarty->assign("auth_2", $auth_2);
$smarty->assign("upline_2", $upline_2);
$smarty->assign("is_groupname_2", $is_groupname_2);
$smarty->assign("encrypt_user_id", $db->encryptor('encrypt',$user_id_2));
$smarty->assign("encrypt_dur", $db->encryptor('encrypt',$duration_2));
$smarty->assign("encrypt_vip", $db->encryptor('encrypt',$vip_duration_2));

$secret = $db->encryptor('encrypt',$user_id_2);
$secret = urlencode($secret);
$smarty->assign("secret", $secret);

$profile_query = $db->sql_query("SELECT profile_image FROM users_profile WHERE profile_id='$user_id_2'");
$profile_row = $db->sql_fetchrow($profile_query);
$profile_image = $profile_row['profile_image'];
$default = $base_url.'profile/default.png';
$profile = $base_url.'profile/'.$user_id_2.'/'.$profile_image;

if($user_level_2 == 'superadmin'){
    $credits_bal = '&infin;';
}else{
    $credits_bal = $row_2['credits'];
}
$smarty->assign("credits_bal", $credits_bal);

if($user_level_2 == 'subreseller'){
	$rank = 'Sub Reseller';
	$rank2 ='<span class="glyphicon glyphicon-heart"></span>';
}elseif($user_level_2 == 'reseller'){
	$rank = 'Reseller';
	$rank2 ='<span class="glyphicon glyphicon-star"></span>';
}elseif($user_level_2 == 'subadmin'){
	$rank = 'Sub Administrator';
	$rank2 ='<span class="glyphicon glyphicon-star"></span>
			<span class="glyphicon glyphicon-star"></span>
			<span class="glyphicon glyphicon-star"></span>';
}elseif($user_level_2 == 'administrator'){
	$rank = '[Administrator]';
	$rank2 ='<span class="glyphicon glyphicon-star"></span>
	        <span class="glyphicon glyphicon-star"></span>
			<span class="glyphicon glyphicon-globe"></span>
			<span class="glyphicon glyphicon-star"></span>
			<span class="glyphicon glyphicon-star"></span>';
}elseif($user_level_2 == 'superadmin'){
	$rank = '[Super Administrator/developer]';
	$rank2 ='<span class="glyphicon glyphicon-star"></span>
			<span class="glyphicon glyphicon-star"></span>
			<span class="glyphicon glyphicon-star"></span>
			<span class="glyphicon glyphicon-star"></span>
			<span class="glyphicon glyphicon-star"></span>';
}else{
	$rank = 'Member Only';
	$rank2 ='<span class="glyphicon glyphicon-user"></span>';
}
$smarty->assign("rank", $rank);

if($is_groupname_2 == 'free'){
	$rank2 = 3;
}
$smarty->assign("rank2", $rank2);

if($profile_image === ''){
	$avatar = '<img class="img-circle" height="20" width="20" src="'.$default.'" alt="default">';
}else{
	$avatar = '<img class="img-circle" height="20" width="20" src="'.$profile.'" alt="'.$full_name_2.'">';
}
$smarty->assign("avatar", $avatar);

if(!is_logged_in($user)) {
	setcookie("user", NULL, time()-3600, "/"); 
	unset($_COOKIE['user']);
	$user = "";
	unset($user);
}

function chkSession() {
	global $user;
	if(!is_logged_in($user)) {
		header("Location: /login");
	}
}

function calc_time($seconds) {
	$days = (int)($seconds / 86400);
	$seconds -= ($days * 86400);
	if ($seconds) {
		$hours = (int)($seconds / 3600);
		$seconds -= ($hours * 3600);
	}
	if ($seconds) {
		$minutes = (int)($seconds / 60);
		$seconds -= ($minutes * 60);
	}
	$time = array('days'=>(int)$days,
			'hours'=>(int)$hours,
			'minutes'=>(int)$minutes,
			'seconds'=>(int)$seconds);
	return $time;
}

function ran_code() {
	$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	srand((double)microtime()*1000000);
	$i = 0;
	while ($i <= 4)
	{
		$num = rand() % 33;
		$tmp = substr($chars, $num, 1);
		$pwd = $pwd . $tmp;
		$i++;
	}
	return $pwd;
}

$dur = $db->calc_time($duration_2);
$dur2 = $db->calc_time($vip_duration_2);
$dur3 = $db->calc_time($private_duration_2);

$pre_duration = "". $dur['days'] . " Day(s) | " . $dur['hours'] . " Hour(s) and " . $dur['minutes'] . " Minute(s)";
$vip_duration = "". $dur2['days'] . " Day(s) | " . $dur2['hours'] . " Hour(s) and " . $dur2['minutes'] . " Minute(s)";
$pri_duration = "". $dur3['days'] . " Day(s) | " . $dur3['hours'] . " Hour(s) and " . $dur3['minutes'] . " Minute(s)";

$pre_days = $dur['days'];
$pre_hours = $dur['hours'];
$pre_minutes = $dur['minutes'];

$smarty->assign("pre_days", $pre_days);
$smarty->assign("pre_hours", $pre_hours);
$smarty->assign("pre_minutes", $pre_minutes);

$vip_days = $dur2['days'];
$vip_hours = $dur2['hours'];
$vip_minutes = $dur2['minutes'];

$smarty->assign("vip_days", $vip_days);
$smarty->assign("vip_hours", $vip_hours);
$smarty->assign("vip_minutes", $vip_minutes);

$pri_days = $dur3['days'];
$pri_hours = $dur3['hours'];
$pri_minutes = $dur3['minutes'];

$smarty->assign("pri_days", $pri_days);
$smarty->assign("pri_hours", $pri_hours);
$smarty->assign("pri_minutes", $pri_minutes);

$smarty->assign("pre_duration", $pre_duration);
$smarty->assign("vip_duration", $vip_duration);
$smarty->assign("pri_duration", $pri_duration);

//chat_status='seen' AND chat_id1='$user_id_2' OR
$chat_support =  $db->sql_query("SELECT * FROM chat WHERE chat_status='seen' AND chat_id2 = '$user_id_2'");
if($db->sql_numrows($chat_support) > 0){
	$alert_chat = '<span class="badge badge-info up">'.$db->sql_numrows($chat_support).'</span>';
}else{
	$alert_chat = '';
}
$smarty->assign("alert_chat", $alert_chat);

if($user_id_2 == 1 || $user_id_2 == 5){
	$staff_support =  $db->sql_query("SELECT * FROM support_ticket WHERE ticket_status = 'customer-reply' OR ticket_status = 'open'");
}else{
	$staff_support =  $db->sql_query("SELECT * FROM support_ticket WHERE ticket_user_id='$user_id_2' AND ticket_status = 'answered'");
}

if($db->sql_numrows($staff_support) > 0){
	$alert_message = '<span class="label label-round label-info">'.$db->sql_numrows($staff_support).'</span>';
}else{
	$alert_message = '';
}
$smarty->assign("alert_message", $alert_message);
?>