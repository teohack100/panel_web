<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if(!isset($_GET['uid']) && !isset($_GET['ucode']) && empty($_GET['uid']) || empty($_GET['ucode'])){
	$db->RedirectToURL($db->base_url());
	exit;	
}else{
	$uid = $db->Sanitize($_GET['uid']);
	$ucode = $db->Sanitize($_GET['ucode']);
	if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
		$chk = $db->sql_query("SELECT user_id, user_name, upline 
		FROM users WHERE user_level!='normal' AND code!='".$db->SanitizeForSQL($ucode)."' AND user_id!='".$db->SanitizeForSQL($uid)."' OR user_id=1");
	}
	elseif($user_level_2 == 'administrator'){
		$chk = $db->sql_query("SELECT user_id, user_name, upline 
		FROM users WHERE user_level!='normal' AND user_level!='subadmin' AND code!='".$db->SanitizeForSQL($ucode)."' AND user_id!='".$db->SanitizeForSQL($uid)."'");
	}	
	while($chkrow = $db->sql_fetchrow($chk)){
		$values[] = $chkrow;		
	}
	echo json_encode($values);	
}
?>