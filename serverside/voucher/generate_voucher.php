<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();

$code = urldecode($_POST['code']);
$code = $db->encryptor('decrypt',$code);

if(isset($_POST['submitted']))
{
	$category = $db->encryptor('decrypt', $_POST['category']);
	
	if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
		$get_days = base64_decode($_POST['dayss']);
		$get_days = $db->decrypt_key($db->decrypt_key($get_days));
		$days = $get_days * 86400;
		$get_hours = urldecode($_POST['hourss']);
		$get_hours = $db->decrypt_key($db->decrypt_key($get_hours));
		$hours = $get_hours * 3600;
		$durations = $db->Sanitize($days) + $db->Sanitize($hours);	
	}else{
		if($category == 'premium'){
			$durations = 2592000;
		}elseif($category == 'vip' || $category == 'private'){
			$durations = 2592000 / 2;
		}else{
			$db->HandleError("Sorry! Invalid Transaction!...");
		}		
	}
	$duration = $durations;
	$code1 = ran_code();
	$code2 = ran_code();
	$code3 = ran_code();
		
		$gen = $code2 . '-' . $code1 . '-' . $code3;
		$result = $db->sql_query("SELECT code_name FROM vouchers WHERE code_name='".$gen."'");
		$chk = $db->sql_fetchrow($result);
		if($chk != 1)
		{
			$chk_user = $db->sql_query("SELECT credits FROM users WHERE user_id = '".$code."'");
			$chk_rows = $db->sql_fetchrow($chk_user);
			if($chk_rows['credits'] <= 0 && $user_level_2 == 'subadmin'
			|| $chk_rows['credits'] <= 0 && $user_level_2 == 'administrator'
			|| $chk_rows['credits'] <= 0 && $user_level_2 == 'reseller'
			|| $chk_rows['credits'] <= 0 && $user_level_2 == 'subreseller')
			{
				$db->HandleError("Sorry! You don't have enough Credits!");
			}
			elseif($chk_rows['credits'] > 0)
			{
				if($user_id_2 > 1 && $chk_rows['credits'] > 0 
				|| $user_id_2 == 1 
				|| $user_level_2 == 'superadmin')
				{
					if($duration >0)
					{
						$insert = "insert into vouchers
						(code_name,
						 reseller_id,
						 reseller_name,
						 is_qty,
						 is_used,
						 duration,
						 gen_date,
						 category)
						values
						('".$db->SanitizeForSQL($gen)."',
						 '".$db->SanitizeForSQL($user_id_2)."',
						 '".$db->SanitizeForSQL($user_name_2)."',
						 1,
						 0,
						 '".$db->SanitizeForSQL($duration)."',
						 '".date("Y-m-d H:i:s")."',
						 '".$category."')";
						$qry = $db->sql_query($insert);	
						
						if($qry)
						{
							if($user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller')
							{								
								$update = $db->sql_query("UPDATE users SET credits=credits-1 WHERE user_id='".$user_id_2."'");		
							}
							
							$db->HandleSuccess('Success! Please Copy your Voucher Code: 
								' . $gen);
								
							echo '<button class="btn btn-info btn-block" id="'.$gen.'">
									'.$gen.' <i class="glyphicon glyphicon-copy"></i>
								 </button>';
						}else{	
							$db->HandleDBError("Error inserting data to the table\nquery: $insert");
						}
					}else{
						$db->HandleError('Invalid duration!');
					}
				}else{	
					$db->HandleError("Not enough credits");
				}
			}else{
				$db->HandleError("Not enough credits");
			}
		}else{	
			$db->HandleError('Invalid Generate!');
		}
	echo $db->GetSuccessMessage();
	echo $db->GetErrorMessage();
}else{
	if(empty($_POST['code'])){
		$db->RedirectToURL($db->base_url());
		exit;
	}
	if($user_id_2 != $code){
		$db->RedirectToURL($db->base_url());
		exit;	
	}
	if($user_id_2 > 1 && $user_level_2 == 'normal'){
		$db->RedirectToURL($db->base_url());
		exit;
	}
}
?>