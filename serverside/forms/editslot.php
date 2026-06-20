<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin')
{
	if(isset($_POST['submitted']))
	{
		if(!isset($_POST['edit_secret']) && !isset($_POST['edit_code']) && !isset($_POST['edit_slot'])
			|| empty($_POST['edit_secret']) || empty($_POST['edit_code']) || empty($_POST['edit_slot']))
		{
			$db->HandleError('Sorry! The Private Slot Registration  is failed!..');
		}
		else
		{
			$edit_secret = $db->encryptor('decrypt',$_POST['edit_secret']);
			$edit_secret = $db->encryptor('decrypt',$edit_secret);
			$username = $db->Sanitize($edit_secret);
				
			$edit_code = $db->encryptor('decrypt',$_POST['edit_code']);
			$edit_code = $db->encryptor('decrypt',$edit_code);
			$uid = $db->Sanitize($edit_code);
				
			$slot = $db->Sanitize($_POST['edit_slot']);
			if(preg_match('/[^0-9]/', $slot)){
				$db->HandleError('Invalid input!');
				return false;
			}

			$qry = $db->sql_query("SELECT user_id, user_name FROM users 
			WHERE user_id='".$db->SanitizeForSQL($uid)."' AND user_name='".$db->SanitizeForSQL($username)."' LIMIT 1");				
			$row = $db->sql_fetchrow($qry);
			if($db->sql_numrows($qry) > 0)
			{
				$update = $db->sql_query("UPDATE users SET private_slot='".$db->SanitizeForSQL($slot)."' WHERE user_id='".$db->SanitizeForSQL($row['user_id'])."'");
				if($update)
				{
					$db->HandleSuccess('The private slot '.$row['user_name'].' is successfully updated');
				}else{
					$db->HandleError('Sorry! Failed to Update!..');
				}
			}else{
				$db->HandleError('Sorry! Invalid Transaction!..');
			}
		}

		echo $db->GetSuccessMessage();
		echo $db->GetErrorMessage();
	}else{
		if(empty($_POST['username'])){
			echo '<script> alert("Invalid Transaction"); </script>';
			$db->RedirectToURL($db->base_url().'404');
			exit;
		}
		if(empty($_POST['slot_code'])){
			echo '<script> alert("Invalid Transaction"); </script>';
			$db->RedirectToURL($db->base_url().'404');
			exit;
		}
		
		if(empty($_POST['slot'])){
			echo '<script> alert("Invalid Transaction"); </script>';
			$db->RedirectToURL($db->base_url().'404');
			exit;
		}
		
		if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
			
		}else{
			echo '<script> alert("Invalid Transaction"); </script>';
			$db->RedirectToURL($db->base_url().'404');
			exit;
		}
	}
}else{
	echo '<script> alert("Invalid Transaction"); </script>';
	$db->RedirectToURL($db->base_url().'404');
	exit;
}
?>	