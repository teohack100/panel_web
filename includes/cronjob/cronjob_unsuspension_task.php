<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once 'config.php';
ini_set('max_execution_time', 150);
$ip = $db->get_client_ip();
if($db->get_client_ip() == 'UNKNOWN')
{
///////////////////////////////////////
//UNSUSPENSION TASK EVERY 30 MIN TASK//
//////////////////////////////////////
	$thirtydays = 2764800;
	$ninetydays = 2764800*3;
	$time = time();
	$timedelete = $time - 100;
	$db->sql_query("DELETE FROM limit_registration WHERE regtime < $time");
	$db->sql_query("DELETE FROM login_attempts WHERE timestamp < $time");

	$user_query = $db->sql_query("SELECT user_id, lastlogin, regdate, is_offense, suspended_date FROM users");
	while($user_rst = $db->sql_fetchrow($user_query)){
		$lastlogin = $user_rst['lastlogin'];
		if($lastlogin == '0000-00-00 00:00:00'){
			$user_update = strtotime($user_rst['regdate']);
		}else{
			$user_update = strtotime($user_rst['lastlogin']);
		}
		$days = strtotime($user_rst['suspended_date']);
		if($user_rst['is_offense'] == 1){
			//3days
			$twodays = 172800;
			$days3 = $days + $twodays;
			if($days3 < $timedelete){
				$db->sql_query("UPDATE users SET is_active=1, status='live' WHERE user_id='".$user_rst['user_id']."' AND is_offense=1");
			}
		}
		if($user_rst['is_offense'] == 2){
			//7days
			$sixdays = 518400;
			$days7 = $days + $sixdays;
			if($days7 < $timedelete){
				$db->sql_query("UPDATE users SET is_active=1, status='live' WHERE user_id='".$user_rst['user_id']."' AND is_offense=2");
			}
		}
	}
}else{
	echo '<script> alert("You are a Mother Fucker! Damn shit!... Your IP Address: '.$ip.'");
	window.location.href="http://www.google.com"; </script>';
}
?>