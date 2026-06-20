<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();

$valid = true;
if(isset($_POST['submitted']))
{
	$chk = $_POST['chk'];
	$chkcount = count($chk);
	for($i=0; $i<$chkcount; $i++)
	{
		$chk_id = $chk[$i];
		$chk_id = $db->encryptor('decrypt',$chk_id);
		if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
			$freeze_qry = $db->sql_query("UPDATE users SET 
			is_active=1, is_freeze=1, freeze_status=1, last_freeze_date='".date('Y-m-d H:i:s')."', status='freeze' WHERE user_id!=1 AND user_level!='superadmin' AND user_id='".$chk_id."'");
		}elseif($user_level_2 == 'reseller' || $user_level_2 == 'subreseller' || $user_level_2 == 'administrator' || $user_level_2 == 'subadmin'){
			$freeze_qry = $db->sql_query("UPDATE users SET 
			is_active=1, is_freeze=1, freeze_status=1, last_freeze_date='".date('Y-m-d H:i:s')."', status='freeze' WHERE user_id!=1 AND user_level!='superadmin' AND user_id='".$chk_id."' AND upline='".$user_id_2."'");
		}else{
			echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
			exit;
		}
		if($freeze_qry)
		{
			$db->sql_query("UPDATE freeze_request SET 
			status='approved', 
			reseller_id='".$user_id_2."', 
			reseller_name='".$user_name_2."', 
			reseller_ipaddress='".$db->get_client_ip()."', 
			process_date='".date('Y-m-d H:i:s')."'
			WHERE
			client_id='".$chk_id."'");
		}else{
			$db->HandleError('Sorry! Freezing account failed!');	
		}
	}
	echo $db->GetSuccessMessage();
	echo $db->GetErrorMessage();
}else{
	if(empty($_POST['chk']))
	{
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}
}
?>