<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'administrator'){
}else{
	echo '<script>alert("Sorry! You dont have Permission to Access this Page!...");</script>';
	$db->RedirectToURL($db->base_url());
	exit;
}
if(isset($_POST['submitted']))
{
	if(!isset($_POST['is_permission']) && !isset($_POST['permission_code']) 
	|| empty($_POST['is_permission']) || empty($_POST['permission_code'])){
			$db->HandleError('Sorry! Failed to update!..');
	}else{
		
		if($_POST['is_permission'] == 'y'){
			$permission = 1;
		}else
		if($_POST['is_permission'] == 'n'){
			$permission = 0;
		}
		$code_name = $db->encryptor('decrypt', $_POST['permission_code']);
		$code_name = $db->encryptor('decrypt', $code_name);
		$voucher = $db->Sanitize($code_name);
		$chk = $db->sql_query("SELECT code_name FROM vouchers WHERE 
		is_used=0 AND reseller_id='".$user_id_2."' AND code_name='".$db->SanitizeForSQL($voucher)."'");
		if($db->sql_numrows($chk) > 0){
			$update = $db->sql_query("UPDATE vouchers SET permission='".$permission."' WHERE 
			is_used=0 AND reseller_id='".$user_id_2."' AND code_name='".$db->SanitizeForSQL($voucher)."'");
			if($update){
				$db->HandleSuccess('This '. $voucher .' is successfully permission updated...!');
			}else{
				$db->HandleError('This '. $voucher .' is failed to update!');
			}
		}else{
			$db->HandleError('This '. $voucher .' is not valid!');
		}
	}
	echo $db->GetSuccessMessage();
	echo $db->GetErrorMessage();	
}else{
	if(empty($_POST['is_permission'])){
		$db->RedirectToURL($db->base_url());
		exit;
	}
	if(empty($_POST['permission_code'])){
		$db->RedirectToURL($db->base_url());
		exit;
	}
}
?>