<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if(!isset($_GET['uid']) && isset($_GET['uid2']) || empty($_GET['uid']) || empty($_GET['uid2'])){
	$db->RedirectToURL($db->base_url());
	exit;	
}else
if(isset($_GET['uid2']) != $user_id_2){
	$db->RedirectToURL($db->base_url());
	exit;	
}
else
{
	$uid = $db->Sanitize($_GET['uid']);
	$uid2 = $db->Sanitize($_GET['uid2']);
	$qry = $db->sql_query("SELECT * FROM vouchers WHERE id='".$db->SanitizeForSQL($uid)."' 
	AND is_used=0 AND reseller_id='".$db->SanitizeForSQL($uid2)."' LIMIT 1");
	$row = $db->sql_fetchrow($qry);
	$values = array();	
	if($row){
		if($row['permission'] == 1){
			$permission = 'y';
		}else
		if($row['permission'] == 0){
			$permission = 'n';
		}
		
		$code = $db->encryptor('encrypt',$row['code_name']);
		$code = $db->encryptor('encrypt',$code);
		$values['secret'] = $code;
		$values['is_permission'] = $permission;		
		$values['code_name'] = $row['code_name'];
	}
	echo json_encode($values);	
}
?>