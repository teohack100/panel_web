<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin'  || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator')
{
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
				$chk_qry = $db->sql_query("SELECT * FROM login_banned_ip WHERE id='".$chk_id."'");
				while($chk_rows = $db->sql_fetchrow($chk_qry))
				{
					$user_query = $db->sql_query("DELETE FROM login_banned_ip WHERE id='".$chk_rows['id']."'");
					if($user_query)
					{
						$db->HandleSuccess('Successfully! Deleted Banned IP!...');
					}else{
						$db->HandleError('Sorry! Delete Banned IP is Failed!');
					}
				}
			}
		}
		echo $db->GetSuccessMessage();
		echo $db->GetErrorMessage();
	}else{
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}
}else{
	echo '<script>alert("Sorry! You dont have Permission to Access this Page!...");</script>';
	$db->RedirectToURL($db->base_url());
	exit;
}
?>