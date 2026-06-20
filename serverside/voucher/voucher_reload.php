<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller'){
}else{
	echo '<script>alert("Sorry! You dont have Permission to Access this Page!...");</script>';
	$db->RedirectToURL($db->base_url());
	exit;
}

if(isset($_POST['submitted']))
{
		
	$category = $db->encryptor('decrypt', $_POST['category']);
	$voucher = trim($_POST['voucher']);
	$voucher = $db->Sanitize($voucher);
	$result_val = $db->sql_query("SELECT code_name, is_used, user_id, reseller_id, reseller_name, is_qty, duration 
	FROM vouchers WHERE category='".$category."' AND code_name='".$db->SanitizeForSQL($voucher)."'");
	$chk_val = $db->sql_numrows($result_val);
	$row_val = $db->sql_fetchrow($result_val);
	if($chk_val > 0)
	{
		if($row_val['is_used'] < 1)
		{
			if(!isset($_POST['reseller_uname']) && !isset($_POST['reload_code']) 
				|| empty($_POST['reseller_uname']) || empty($_POST['reload_code']))
			{
				$db->HandleError('this '. $voucher .' is valid but it cannot be applied!
														 but the username is empty...');
			}else{
				if($user_id_2 != 0) 
				{
					$uid2 = $db->encryptor('decrypt',$_POST['reload_code']);
					$uid2 = $db->Sanitize($uid2);
					$username = $db->Sanitize($_POST['reseller_uname']);
					$check = $db->sql_query("SELECT user_name FROM users
					WHERE user_id='".$db->SanitizeForSQL($uid2)."' AND 
					user_name='".$db->SanitizeForSQL($username)."'");
					if($db->sql_numrows($check) > 0)
					{
						if($category == 'premium'){
							$row_duration = $db->sql_fetchrow($db->sql_query("SELECT code, duration FROM users 
							WHERE 
							user_name='".$db->SanitizeForSQL($username)."' AND user_id='".$db->SanitizeForSQL($uid2)."'"));
							$row_add_duration = ($row_duration['duration'] + $row_val['duration']);							
						}elseif($category == 'vip'){
							$row_duration = $db->sql_fetchrow($db->sql_query("SELECT code, vip_duration FROM users 
							WHERE 
							user_name='".$db->SanitizeForSQL($username)."' AND user_id='".$db->SanitizeForSQL($uid2)."'"));
							$row_add_duration = ($row_duration['vip_duration'] + $row_val['duration']);	
						}elseif($category == 'private'){
							$row_duration = $db->sql_fetchrow($db->sql_query("SELECT code, private_duration FROM users 
							WHERE 
							user_name='".$db->SanitizeForSQL($username)."' AND user_id='".$db->SanitizeForSQL($uid2)."'"));
							$row_add_duration = ($row_duration['private_duration'] + $row_val['duration']);	
						}

						if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'subadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller')
						{
							if($user_id_2 == $row_val['reseller_id'])
							{
								if($category == 'premium'){
									$update = $db->sql_query("UPDATE users SET duration='$row_add_duration' 
									WHERE user_name='".$db->SanitizeForSQL($username)."' AND user_id='".$db->SanitizeForSQL($uid2)."'");
								}elseif($category == 'vip'){
									$ss_id = md5($row_duration['code']);
									$ss_id = rand(0,65535);
									$update = $db->sql_query("UPDATE users SET ss_id='".$ss_id."', is_vip=1, vip_duration='$row_add_duration' 
									WHERE user_name='".$db->SanitizeForSQL($username)."' AND user_id='".$db->SanitizeForSQL($uid2)."'");
								}elseif($category == 'private'){
									$update = $db->sql_query("UPDATE users SET is_private=1, private_duration='$row_add_duration' 
									WHERE user_name='".$db->SanitizeForSQL($username)."' AND user_id='".$db->SanitizeForSQL($uid2)."'");
								}

								if($update)
								{
									$db->sql_query("INSERT INTO voucher_logs
									(code_name, user_id, client_name,
									 reseller_id, reseller_name, is_qty,
									 is_used, date_used, is_date, category)
									VALUES
									('".$db->SanitizeForSQL($voucher)."','".$db->SanitizeForSQL($uid2)."','".$db->SanitizeForSQL($username)."',
									 '".$row_val['reseller_id']."','".$row_val['reseller_name']."','".$row_val['is_qty']."',
									 1,'".date('Y-m-d H:i:s')."','".date('Y-m-d')."','".$db->SanitizeForSQL($category)."')
									");

									$db->sql_query("UPDATE vouchers SET 
									user_id='".$db->SanitizeForSQL($uid2)."', client_name='".$db->SanitizeForSQL($username)."', 
									is_used=1, date_used='".date('Y-m-d H:i:s')."' 
									WHERE category='".$db->SanitizeForSQL($category)."' AND code_name='".$db->SanitizeForSQL($voucher)."'");
									$db->HandleSuccess('this '. $voucher .' is successfully applied');
								}else{
									$db->HandleError('this '. $voucher .' is valid but it cannot be applied!
									 but the username is empty...');
								}
							}else{
								$db->HandleError('this '. $voucher .' is valid but it cannot be applied!');
							}
						}else{
							if($category == 'premium'){
								$update = $db->sql_query("UPDATE users SET duration='$row_add_duration' 
								WHERE use_rname='".$db->SanitizeForSQL($username)."' AND user_id='".$db->SanitizeForSQL($uid2)."'");
							}elseif($category == 'vip'){
								$update = $db->sql_query("UPDATE users SET is_vip=1, vip_duration='$row_add_duration' 
								WHERE user_name='".$db->SanitizeForSQL($username)."' AND user_id='".$db->SanitizeForSQL($uid2)."'");
							}
							if($update)
							{
								$db->sql_query("INSERT INTO voucher_logs
								(code_name, user_id, client_name,
								 reseller_id, reseller_name, is_qty,
								 is_used, date_used, is_date, category)
								VALUES
								('".$db->SanitizeForSQL($voucher)."','".$db->SanitizeForSQL($uid2)."','".$db->SanitizeForSQL($username)."',
								 '".$row_val['reseller_id']."','".$row_val['reseller_name']."','".$row_val['is_qty']."',
								 1,'".date('Y-m-d H:i:s')."','".date('Y-m-d')."','".$db->SanitizeForSQL($category)."')
								");

								$db->sql_query("UPDATE vouchers SET 
								user_id='".$db->SanitizeForSQL($uid2)."', client_name='".$db->SanitizeForSQL($username)."', 
								is_used=1, date_used='".date('Y-m-d H:i:s')."' 
								WHERE category='".$db->SanitizeForSQL($category)."' AND code_name='".$db->SanitizeForSQL($voucher)."'");
								$db->HandleSuccess('this '. $voucher .' is successfully applied');
							}else{
								$db->HandleError('this '. $voucher .' is valid but it cannot be applied!');
							}
						}
					}else{
						$db->HandleError('this '. $voucher .' is valid but it cannot be applied!
						but the username is empty...');
					}
				}
			}
		}else{
			$db->HandleError('this '. $voucher .' is not valid it cannot be applied!');
		}
	}else{
		$db->HandleError('this '. $voucher .' is not a '.$category.' voucher...');
	}
	echo $db->GetSuccessMessage();
	echo $db->GetErrorMessage();
}else{
	if(empty($_POST['reload_code'])){
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}

	if(empty($_POST['voucher'])){
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}

	if(empty($_POST['reseller_uname'])){
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}
}
?>