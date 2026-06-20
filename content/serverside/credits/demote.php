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
if(isset($_POST['submitted']))
{
	$chk = $_POST['chk'];
	$chkcount = count($chk);
	for($i=0; $i<$chkcount; $i++)
	{			
		$chk_id = $chk[$i];
		$chk_id = $this->encryptor('decrypt',$chk_id);
		if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
			$update = $this->sql_query("UPDATE users SET user_level='normal'
			WHERE user_id!=1 AND id_user='".$chk_id."'");
		}else{
			$update = $this->sql_query("UPDATE users SET user_level='normal'
			WHERE user_id!=1 AND id_user='".$chk_id."'");
		}

		if($update){
			$this->HandleSuccess('Successfully Demoted');
		}else{
			$this->HandleError('Sorry! Demoted Account is Failed!');
		}
	}
	
	echo $db->GetSuccessMessage();
	echo $db->GetErrorMessage();
}else{
	if(empty($_POST['chk']))
			{
		$db->RedirectToURL($db->base_url());
		exit;
	}
}
?>