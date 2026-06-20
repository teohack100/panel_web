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
		if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller'){
			$freeze_qry = $db->sql_query("DELETE FROM freeze_request WHERE client_id='".$chk_id."' AND status='pending'");
		}else{
			echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
			exit;
		}
		if($freeze_qry)
		{
			$db->HandleSuccess('Successfully! Freeze Request is Deleted');
		}else{
			$db->HandleError('Sorry! Freeze Request is Failed!');	
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