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
	if(!isset($_POST['add_credits']) && !isset($_POST['secret']) && !isset($_POST['code']) || empty($_POST['add_credits']) || empty($_POST['secret']) || empty($_POST['code'])){
		$errors = 'Sorry! the transaction is inavalid!..';
	}else{
		$category = $db->encryptor('decrypt',$_POST['category']);
		$category = $db->Sanitize($category);
		
		$get_secret = $db->encryptor('decrypt',$_POST['secret']);
		$get_secret = $db->encryptor('decrypt',$get_secret);
		$get_secret = $db->Sanitize($get_secret);
	
		$get_code = $db->encryptor('decrypt',$_POST['code']);
		$get_code = $db->encryptor('decrypt',$get_code);
		$get_code = $db->Sanitize($get_code);

		$credits = trim($_POST['add_credits']);
		$credits = $db->Sanitize($credits);

		if(preg_match('/[^0-9]/', $credits)) {
			$db->HandleError('Invalid input!');
		}

		$result_add = $db->sql_query("SELECT user_id, user_name, credits FROM users WHERE user_id='".$db->SanitizeForSQL($get_code)."' AND user_name='".$db->SanitizeForSQL($get_secret)."'");
		$row_add = $db->sql_fetchrow($result_add);
		$client_credits = $row_add['credits'];
		$add_credits = (int)$row_add['credits'] + (int)$credits;
		$subs_credits = (int)$row_add['credits'] - (int)$credits;
			
		if($user_id_2 == 1 || $user_level_2 == 'superadmin')
		{
			if($credits > 0)
			{
				if($category == 'add')
				{
					$update = $db->sql_query("UPDATE users SET credits='".$db->SanitizeForSQL($add_credits)."' 
					WHERE user_id='".$db->SanitizeForSQL($get_code)."' AND user_name='".$db->SanitizeForSQL($get_secret)."'");
					if($update)
					{
						$db->sql_query("INSERT INTO credits_logs 
						(credits_id, credits_id2, credits_username, credits_qty, credits_date) 
						VALUES 
						('".$db->SanitizeForSQL($user_id_2)."','".$db->SanitizeForSQL($row_add['user_id'])."','".$db->SanitizeForSQL($row_add['user_name'])."','+".$db->SanitizeForSQL($credits)."','".date('Y-m-d H:i:s')."')");
						$db->HandleSuccess($credits ." Credits Successfully! Added to " .$row_add['user_name']);
					}else{
						$db->HandleError($credits ." Credits Failed! Transaction...");
					}
				}elseif($category == 'substract'){
					$update = $db->sql_query("UPDATE users SET credits='".$db->SanitizeForSQL($subs_credits)."' 
					WHERE user_id='".$db->SanitizeForSQL($get_code)."' AND user_name='".$db->SanitizeForSQL($get_secret)."'");
					if($update)
					{
						$db->sql_query("INSERT INTO credits_logs 
						(credits_id, credits_id2, credits_username, credits_qty, credits_date) 
						VALUES 
						('".$db->SanitizeForSQL($user_id_2)."','".$db->SanitizeForSQL($row_add['user_id'])."','".$db->SanitizeForSQL($row_add['user_name'])."','-".$db->SanitizeForSQL($credits)."','".date('Y-m-d H:i:s')."')");
						$db->HandleSuccess($credits ." Credits Successfully! Substracted to " .$row_add['user_name']);
					}else{
						$db->HandleError($credits ." Credits Failed! Transaction...");
					}
				}else{
					$db->HandleError('Sorry! the transaction is inavalid!..');
				}
			}
		}
		elseif($user_level_2 == 'administrator' || $user_level_2 == 'subadmin' || $user_level_2 == 'reseller')
		{
			$chk_credits = $db->sql_query("SELECT credits FROM users WHERE user_id='".$db->SanitizeForSQL($user_id_2)."'");
			$chk_rows = $db->sql_fetchrow($chk_credits);
			$my_credits = $chk_rows['credits'];
			if($credits > 0)
			{
				if($category == 'add')
				{
					if($my_credits < $credits)
					{
						$db->HandleError("Sorry! You don't have much Credits!");
					}else{
						$update = $db->sql_query("UPDATE users SET credits='".$db->SanitizeForSQL($add_credits)."' 
						WHERE user_id='".$db->SanitizeForSQL($get_code)."' AND user_name='".$db->SanitizeForSQL($get_secret)."'");
						if($update)
						{
							$db->sql_query("UPDATE users SET credits = credits - '".$credits."' 
							WHERE user_id='".$db->SanitizeForSQL($user_id_2)."'");
							$db->sql_query("INSERT INTO credits_logs 
							(credits_id, credits_id2, credits_username, credits_qty, credits_date) 
							VALUES 
							('".$db->SanitizeForSQL($user_id_2)."','".$db->SanitizeForSQL($row_add['user_id'])."','".$db->SanitizeForSQL($row_add['user_name'])."','+".$db->SanitizeForSQL($credits)."','".date('Y-m-d H:i:s')."')");
							$db->HandleSuccess($credits ." Credits Successfully! Added to " .$row_add['user_name']);
						}else{
							$db->HandleError($credits ." Credits Failed! Transaction...");
						}
					}
				}
				elseif($category == 'substract')
				{
					if($client_credits < $credits)
					{
						$db->HandleError("Sorry! ". $credits ." Decreasing Credits Failed! Transaction...");
					}else{
						$update = $db->sql_query("UPDATE users SET credits='".$db->SanitizeForSQL($subs_credits)."' 
						WHERE user_id='".$db->SanitizeForSQL($get_code)."' AND user_name='".$db->SanitizeForSQL($get_secret)."'");
						if($update)
						{
							$db->sql_query("UPDATE users SET credits = credits + '".$credits."' 
							WHERE user_id='".$db->SanitizeForSQL($user_id_2)."'");
							$db->sql_query("INSERT INTO credits_logs 
							(credits_id, credits_id2, credits_username, credits_qty, credits_date) 
							VALUES 
							('".$db->SanitizeForSQL($user_id_2)."','".$db->SanitizeForSQL($row_add['user_id'])."','".$db->SanitizeForSQL($row_add['user_name'])."','-".$db->SanitizeForSQL($credits)."','".date('Y-m-d H:i:s')."')");
							$db->HandleSuccess($credits ." Credits Successfully! Substracted to " .$row_add['user_name']);
						}else{
							$db->HandleError($credits ." Credits Failed! Transaction...");
						}
					}
				}else{
					$db->HandleError('Sorry! the transaction is inavalid!..');
				}
			}
		}
	}
	echo $db->GetSuccessMessage();
	echo $db->GetErrorMessage();
}else{
	if(empty($_POST['add_credits'])){
		$db->RedirectToURL($db->base_url());
		exit;
	}

	if(empty($_POST['secret'])){
		$db->RedirectToURL($db->base_url());
		exit;	
	}

	if(empty($_POST['code'])){
		$db->RedirectToURL($db->base_url());
		exit;
	}

	if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller'){
	}else{
		$db->RedirectToURL($db->base_url());
		exit;
	}
}
?>