<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator'){
}else{
	echo '<script>alert("Sorry! You dont have Permission to Access this Page!...");</script>';
	$db->RedirectToURL($db->base_url());
	exit;
}

if(isset($_POST['submitted']))
{
	$chk = $_POST['chk'];
	$chkcount = count($chk);
			
	if(!isset($chk))
	{
		echo "<script> alert('You need to check the checkbox! At least one checkbox Must be Selected !!!'); </script>";
		$db->RedirectToURL($db->base_url().'404');
		exit;
	}
	else
	{
		for($i=0; $i<$chkcount; $i++)
		{			
			$chk_id = $chk[$i];
			$chk_id = $db->encryptor('decrypt',$chk_id);
			$chk_qry = $db->sql_query("DELETE FROM dns WHERE dns_id='".$chk_id."'");
					
			if($chk_qry)
			{
				$db->HandleSuccess('Successfully! Deleted DNS Record(s)!...');
			}else{
				$db->HandleError('Sorry! Deleting DNS Record(s) Failed!');
			}
		}
	}
	echo $db->GetSuccessMessage();
	echo $db->GetErrorMessage();
}else{
	echo '<script> alert("Invalid Transaction"); </script>';
	$db->RedirectToURL($db->base_url().'404');
	exit;
}
?>
