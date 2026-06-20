<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller'){
}else{
	echo '<script>alert("Sorry! You dont have Permission to Access this Page!...");</script>';
	$db->RedirectToURL($db->base_url());
	exit;
}

if(!isset($_GET['uid']) && !isset($_GET['ucode']) || empty($_GET['uid']) || empty($_GET['ucode'])){
	echo '<script>alert("Error");</script>';
	$db->RedirectToURL($db->base_url());
	exit;	
}else{
	$get_id = $_GET['uid'];
	$get_code = $_GET['ucode'];
	if($user_id_2 == 1 || $user_level_2 == 'superadmin'){

		$chk_qry = "user_id!=1 AND user_id='".$db->SanitizeForSQL($get_id)."' AND code='".$db->SanitizeForSQL($get_code)."' LIMIT 1";
		$valid = true;

	}elseif($user_level_2 == 'administrator'){

		$chk_qry = "user_id!=1 AND user_id='".$db->SanitizeForSQL($get_id)."' AND code='".$db->SanitizeForSQL($get_code)."' AND upline='".$user_id_2."' AND user_level='reseller' 
		            OR user_id!=1 AND user_id='".$db->SanitizeForSQL($get_id)."' AND code='".$db->SanitizeForSQL($get_code)."' AND upline='".$user_id_2."' AND user_level='subadmin'
					OR user_id!=1 AND user_id='".$db->SanitizeForSQL($get_id)."' AND code='".$db->SanitizeForSQL($get_code)."' AND upline='".$user_id_2."' AND user_level='subreseller'
					OR user_id!=1 AND user_id='".$db->SanitizeForSQL($get_id)."' AND code='".$db->SanitizeForSQL($get_code)."' AND upline='".$user_id_2."' AND user_level='normal' LIMIT 1";
		$valid = true;

	}elseif($user_level_2 == 'subadmin'){

		$chk_qry = "user_id!=1 AND user_id='".$db->SanitizeForSQL($get_id)."' AND code='".$db->SanitizeForSQL($get_code)."' AND upline='".$user_id_2."' AND user_level='reseller' 
		            OR user_id!=1 AND user_id='".$db->SanitizeForSQL($get_id)."' AND code='".$db->SanitizeForSQL($get_code)."' AND upline='".$user_id_2."' AND user_level='subreseller'
					OR user_id!=1 AND user_id='".$db->SanitizeForSQL($get_id)."' AND code='".$db->SanitizeForSQL($get_code)."' AND upline='".$user_id_2."' AND user_level='normal' LIMIT 1";
		$valid = true;

	}elseif($user_level_2 == 'reseller'){

		$chk_qry = "user_id!=1 AND user_id='".$db->SanitizeForSQL($get_id)."' AND code='".$db->SanitizeForSQL($get_code)."' AND upline='".$user_id_2."' AND user_level='subreseller' 
					OR user_id!=1 AND user_id='".$db->SanitizeForSQL($get_id)."' AND code='".$db->SanitizeForSQL($get_code)."' AND upline='".$user_id_2."' AND user_level='normal' LIMIT 1";
		$valid = true;

	}elseif($user_level_2 == 'subreseller'){

		$chk_qry = "user_id!=1 AND user_id='".$db->SanitizeForSQL($get_id)."' AND code='".$db->SanitizeForSQL($get_code)."' AND upline='".$user_id_2."' AND user_level='normal' LIMIT 1";
		$valid = true;

	}else{
		$valid = false;
	}
	$qry = $db->sql_query("SELECT user_id, user_name, is_vip, is_private, credits FROM users WHERE ".$chk_qry) OR die();
	$row = $db->sql_fetchrow($qry);
	$values = array();	

	$qry2 = $db->sql_query("SELECT user_id, user_name, credits FROM users WHERE user_id=".$user_id_2) OR die();
	$row2 = $db->sql_fetchrow($qry2);
	$values2 = array();

	if($user_level_2 == 'superadmin' || $user_id_2 == 1)
	{
		$optionx = "Credits: Unlimited";
	}
	else
	{
		$option = $row2['credits'];
		$optionx = "Your Credits: $option";
	}

	if($row){
		$secret = $db->encryptor('encrypt',$row['user_name']);//client_username
		$secret = $db->encryptor('encrypt',$secret);
		$code = $db->encryptor('encrypt',$row['user_id']);//client_user_id
		$code = $db->encryptor('encrypt',$code);
		$values['secret'] = $secret;
		$values['code'] = $code;
		$values['user_name'] = $row['user_name'];
		$values['mycredits'] = $optionx;
		$values['credits'] = $row['credits'];
		$values['response'] = 1;
	}else{
		$values['response'] = 2;
	}
	if($valid == false){
		$values['response'] = 0;
	}
	echo json_encode($values);
}
?>