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
if($user_id_2 == 1  || $user_level_2 = 'superadmin'){
	if(isset($_POST['submitted']))
	{
		$category = $db->encryptor('decrypt', $_POST['category']);
		$category_ext = $db->encryptor('decrypt', $_POST['category_ext']);
		if($category == 'premium'){
			$ext1 = 'Premium Duration';
		}elseif($category == 'vip'){
			$ext1 = 'VIP Duration';
		}
		
		if($category_ext == 'add'){
			$ext2 = 'Added ';
		}elseif($category_ext == 'substract'){
			$ext2 = 'Substracted ';
		}
		
		if(!isset($_POST['ext']) && !isset($_POST['extcode']) || empty($_POST['ext']) || empty($_POST['extcode'])){
			$db->HandleError('Sorry! you cannot extend!..');
		}else{

			$get_days = base64_decode($_POST['dayss']);
			$get_days = $db->decrypt_key($db->decrypt_key($get_days));
			$days = $get_days * 86400;
			$get_hours = urldecode($_POST['hourss']);
			$get_hours = $db->decrypt_key($db->decrypt_key($get_hours));
			$hours = $get_hours * 3600;
			$duration = $db->Sanitize($days) + $db->Sanitize($hours);
			if($duration > 0)	{
				$uid = $db->encryptor('decrypt',$_POST['extcode']);
				$uid_ext = $db->Sanitize($uid);
				$uname_ext = $db->Sanitize($_POST['ext']);
				
				$chkUsers = $db->sql_fetchrow($db->sql_query("SELECT * FROM users
				WHERE user_name='".$db->SanitizeForSQL($uname_ext)."' AND user_id='".$db->SanitizeForSQL($uid_ext)."'"));
				
				if($category_ext == 'add'){
					if($category == 'premium'){
						$ext = $db->sql_query("UPDATE users
						SET
						duration=duration+".$db->SanitizeForSQL($duration)." 
						WHERE 
						user_name='".$db->SanitizeForSQL($uname_ext)."' AND
						user_id='".$db->SanitizeForSQL($uid_ext)."'");
					}
					else
					if($category == 'vip'){
						$ss_id = md5($chkUsers['code']);
						$ss_id = rand(0,65535);
						$ext = $db->sql_query("UPDATE users
						SET
						is_vip=1, vip_duration=vip_duration+".$db->SanitizeForSQL($duration)." 
						WHERE 
						user_name='".$db->SanitizeForSQL($uname_ext)."' AND
						user_id='".$db->SanitizeForSQL($uid_ext)."'");
					}else{
						$db->HandleSuccess('Sorry! Invalid Category!...');
					}
				}
				elseif($category_ext == 'substract'){
					if($category == 'premium'){
						$ext = $db->sql_query("UPDATE users
						SET
						duration=duration-".$db->SanitizeForSQL($duration)." 
						WHERE 
						user_name='".$db->SanitizeForSQL($uname_ext)."' AND
						user_id='".$db->SanitizeForSQL($uid_ext)."'");
					}
					else
					if($category == 'vip'){
						$ext = $db->sql_query("UPDATE users
						SET
						is_vip=1, vip_duration=vip_duration-".$db->SanitizeForSQL($duration)." 
						WHERE 
						user_name='".$db->SanitizeForSQL($uname_ext)."' AND
						user_id='".$db->SanitizeForSQL($uid_ext)."'");
					}else{
						$db->HandleSuccess('Sorry! Invalid Category!...');
					}
				}else{
					$db->HandleSuccess('Sorry! Invalid Category!...');
				}
				if($ext){
					$db->HandleSuccess('Successfully! '.$ext2.$ext1);
				}else{
					$db->HandleError('Failed! to '.$ext2.$ext1);
				}

			} else {
				$db->HandleError('Sorry! Invalid duration!..');
			}	
		}
		echo $db->GetSuccessMessage();
		echo $db->GetErrorMessage();
	}else{
		$code = $db->encryptor('decrypt',$_POST['extcode']);
		$code = $db->Sanitize($code);
		if(empty($_POST['ext'])){
			$db->RedirectToURL($db->base_url());
			exit;	
		}
		if(empty($_POST['extcode'])){
			$db->RedirectToURL($db->base_url());
			exit;	
		}
		if($user_id_2 != $code){
			$db->RedirectToURL($db->base_url());
			exit;	
		}
	}
}
?>