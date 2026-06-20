<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if($user_level_2 == 'normal' || $user_level_2 == 'subreseller'){	
	echo '<script> alert("Invalid Transaction"); </script>';
	$db->RedirectToURL($db->base_url().'404');
	exit;
}else{
		$valid = true;	
		if(isset($_POST['submitted']))
		{
			$chk = $_POST['chk'];
			$chkcount = count($chk);
				for($i=0; $i<$chkcount; $i++)
				{			
					$chk_id = $chk[$i];
					$chk_id = $db->encryptor('decrypt',$chk_id);
					if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'admin'){
						$update = $db->sql_query("UPDATE users SET user_level='normal'
						WHERE user_id!=1 AND user_id='".$chk_id."'");
					}else{
						$update = $db->sql_query("UPDATE users SET user_level='normal'
						WHERE user_id!=1 AND user_id='".$chk_id."'");
					}

					if($update){
						$db->HandleSuccess('Successfully Demoted');
					}else{
						$db->HandleError('Sorry! Demoted Account is Failed!');
					}
				}
			echo $db->GetSuccessMessage();
			echo $db->GetErrorMessage();
		}else{
			if(empty($_POST['chk']))
			{
				$db->RedirectToURL($db->base_url().'404');
				exit;
			}
		}
}
?>