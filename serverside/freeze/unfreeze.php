<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';

chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller'){
	
}else{
	header("Location: /dashboard");	
}


if(!isset($_POST['uid']) && !isset($_POST['ucode']) && empty($_POST['uid']) || empty($_POST['ucode'])){
	$db->RedirectToURL($db->base_url());
	exit;	
}else{
	$u = $db->Sanitize($_POST['uid']);
	$n = $db->Sanitize($_POST['ucode']);

	$query = $db->sql_query("UPDATE users SET is_active=1, is_freeze=0, freeze_status=0, status='live' WHERE user_id!=1 AND user_level!='superadmin' AND user_id='".$u."' AND code='".$n."'");
    if($query)
	{
		$data['response'] = 1;
	}else{
		$data['response'] = 0;
	}
	echo json_encode($data);
}
?>