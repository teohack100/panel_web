<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();

if($user_level_2 == 'administrator' || $user_level_2 == 'subadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller' || $user_level_2 == 'normal'){
	$chk = $db->sql_query("SELECT * FROM freeze_request WHERE client_id='".$user_id_2."' AND status='pending'");
	if($db->sql_numrows($chk) > 0){
		$query = $db->sql_query("UPDATE freeze_request SET 
		client_ipaddress='".$db->get_client_ip()."', request_date='".date('Y-m-d H:i:s')."'
		WHERE
		client_id='".$user_id_2."'");
	}else{
		$query = $db->sql_query("INSERT into freeze_request 
		(client_id, client_name, client_ipaddress, request_date)
		VALUES
		('".$user_id_2."','".$user_name_2."','".$db->get_client_ip()."','".date('Y-m-d H:i:s')."')");
	}
	if($query){
		echo '<script> alert("Your request is successfully submitted!..."); location.assign("'.$db->base_url().'")</script>';
		exit;
	}else{
		echo '<script> alert("Sorry! Your request is failed to submit.... please try again later"); location.assign("'.$db->base_url().'")</script>';
		exit;
	}
}else{
	echo '<script> alert("Invalid Transaction"); </script>';
	$db->RedirectToURL($db->base_url().'404');
	exit;
}

?>