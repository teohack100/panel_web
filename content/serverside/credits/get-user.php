<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller'){
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
	}elseif($user_level_2 == 'subadmin' || $user_level_2 == 'administrator'){
		$chk_qry = "user_id!=1 AND user_id='".$db->SanitizeForSQL($get_id)."' AND code='".$db->SanitizeForSQL($get_code)."' AND upline='".$user_id_2."' AND user_level='reseller' LIMIT 1";
	}elseif($user_level_2 == 'reseller'){
		$chk_qry = "user_id!=1 AND user_id='".$db->SanitizeForSQL($get_id)."' AND code='".$db->SanitizeForSQL($get_code)."' AND upline='".$user_id_2."' AND user_level='subreseller' LIMIT 1";
	}
	$qry = $db->sql_query("SELECT user_id, user_name, credits FROM users WHERE ".$chk_qry) OR die();
	$row = $db->sql_fetchrow($qry);
	$values = array();	
	if($row){
		$secret = $db->encryptor('encrypt',$row['user_name']);
		$secret = $db->encryptor('encrypt',$secret);
		$code = $db->encryptor('encrypt',$row['user_id']);
		$code = $db->encryptor('encrypt',$code);
		$values['secret'] = $secret;
		$values['code'] = $code;
		$values['user_name'] = $row['user_name'];
		$values['credits'] = $row['credits'];
	}
	
	echo json_encode($values);	
}
?>