<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
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
		echo "<script>
				alert('You need to check the checkbox! At least one checkbox Must be Selected !!!');
			</script>";
	}
	else
	{
		for($i=0; $i<$chkcount; $i++)
		{			
			$chk_id = $chk[$i];
			$chk_id = $db->encryptor('decrypt',$chk_id);
			$sql = $db->sql_query("UPDATE users SET is_offense=0, is_ban=0, is_active=1, status='live' WHERE user_id='".$chk_id."'");	
		}
		
		if($sql)
		{
			$db->HandleSuccess("Successfully! Unbanned!...");
		}else{
			$db->HandleError("Failed! Unbanned!...");
		}
		echo $db->GetSuccessMessage();
		echo $db->GetErrorMessage();	
	}
}	
?>
