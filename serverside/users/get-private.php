<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if(!isset($_GET['uid']) && !isset($_GET['ucode']) && empty($_GET['uid']) || empty($_GET['ucode'])){
	echo '<script> alert("Invalid Transaction"); </script>';
	$db->RedirectToURL($db->base_url().'404');
	exit;	
}else{
	$uid = $db->Sanitize($_GET['uid']);
	$ucode = $db->Sanitize($_GET['ucode']);
	if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
		$qry = $db->sql_query("SELECT user_id, user_name, private_duration, private_slot, upline 
		FROM users WHERE user_id!=1 AND user_id='".$db->SanitizeForSQL($uid)."' AND code='".$db->SanitizeForSQL($ucode)."' AND is_private=1 LIMIT 1");		
	}else{
		echo '<script> alert("Invalid Transaction"); </script>';
		$db->RedirectToURL($db->base_url().'404');
		exit;
	}

	$row = $db->sql_fetchrow($qry);
	$values = array();	
	if($row)
	{	
		$dur = $db->calc_time($row['private_duration']);				
		$pdays = $dur['days'] . " days";
		$phours = $dur['hours'] . " hours";
		$pminutes = $dur['minutes'] . " minutes";
		$pseconds = $dur['seconds'] . " seconds";
		if($row['private_duration'] == 0){
			$private_duration = "<font color='red'>Not Active</font>";
		}else{
			$private_duration = strtotime($pdays . $phours . $pminutes . $pseconds);
			$private_duration = date('F d, Y h:i:s A', $private_duration);
		}

		$chk_upline = $db->sql_query("SELECT user_name FROM users WHERE upline='".$row['upline']."' LIMIT 1");
		$rows = $db->sql_fetchrow($chk_upline);
				
		$secret = $db->encryptor('encrypt',$row['user_name']);
		$secret = $db->encryptor('encrypt',$secret);
		$code = $db->encryptor('encrypt',$row['user_id']);
		$code = $db->encryptor('encrypt',$code);
		$values['secret'] = $secret;
		$values['code'] = $code;
		$values['username'] = $row['user_name'];
		$values['reseller_name'] = $rows['user_name'];
		$values['private_slot'] = $row['private_slot'];
		$values['private_duration'] = $private_duration;

	}
	echo json_encode($values);	
}
?>