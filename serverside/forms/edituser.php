<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();

$valid = true;	
if(isset($_POST['submitted']))
{
	$uid = $db->encryptor('decrypt', $_POST['secret']);
	$get_id = $db->encryptor('decrypt', $uid);
	$get_id = $db->Sanitize($get_id);
	$user_name = $db->Sanitize(trim($_POST['user_name']));
	$user_pass = $db->Sanitize(trim($_POST['user_pass']));
	$resellers = $db->Sanitize(trim($_POST['resellers']));
	$v2ray = $db->Sanitize(trim($_POST['v2ray_id']));
	$category = $db->encryptor('decrypt',(trim($_POST['client_type'])));
	$category = $db->Sanitize($category);
    
    
    if(empty($v2ray) || $v2ray == 'No V2Ray ID, Please generate and save.')
	{
		$db->HandleError('V2Ray is empty!');
		$valid = false;
	}
	
	if(empty($user_pass))
	{
		$db->HandleError('Password is empty!');
		$valid = false;
	}
	else if(preg_match('/[^_a-z-A-Z-0-9 ]/', $user_pass))
	{
		$db->HandleError('Invalid password!');
		$valid = false;
	}
	else if(strlen($user_pass)<8)
	{
		$db->HandleError('Yor Password is too short!');
		$valid = false;
	}

	if(empty($user_name))
	{
		$db->HandleError('Username is empty!');
		$valid = false;
	}
	else if(preg_match('/[^_a-z-A-Z-0-9 ]/', $user_name))
	{
		$db->HandleError('Invalid Username!');
		$valid = false;
	}
	
	$c_result = $db->sql_query("SELECT user_name, user_email, user_level, duration, vip_duration, private_duration, upline FROM users WHERE user_id='".$db->SanitizeForSQL($get_id)."'");
	$row = $db->sql_fetchrow($c_result);
	$db_user_name = $row['user_name'];
	$db_user_level = $row['user_level'];
	$db_upline = $row['upline'];

	$u_dur = $row['duration'];
	$u_vip = $row['vip_duration'];	
	$u_priv = $row['private_duration'];	

	if($user_name != $db_user_name) {
		$u_result = $db->sql_query("SELECT user_name FROM users WHERE user_name='$user_name'");
		$user_name_check = $db->sql_numrows($u_result);
		if($user_name_check > 0) {
			$db->HandleError($user_name.' is already in our database!');
			$valid = false;
		}
	}
	
	$role = $_POST['role'];
	if($user_id_2 == 1)
	{
		if($role == 1){
			$user_level = 'normal';
			$is_groupname = 'normal';
		}
		elseif($role == 2){
			$user_level = 'subreseller';
			$is_groupname = 'subreseller';
			$role_dur = 2592000;
		}
		elseif($role == 3){
			$user_level = 'reseller';
			$is_groupname = 'reseller';
			$role_dur = 2592000;
		}
		elseif($role == 4){
			$user_level = 'administrator';
			$is_groupname = 'administrator';
		}
		elseif($role == 5){
			$user_level = 'subadmin';
			$is_groupname = 'subadmin';
			$role_dur = 2592000;
		}
		elseif($role == 99){
			$user_level = 'superadmin';
			$is_groupname = 'superadmin';
		}else{
			$db->HandleError('Sorry! Invalid Role Management');
			$valid = false;
		}
	}
	elseif($user_level_2 == 'superadmin')
	{
		if($role == 1){
			$user_level = 'normal';
			$is_groupname = 'normal';
		}
		elseif($role == 2){
			$user_level = 'subreseller';
			$is_groupname = 'subreseller';
			$role_dur = 2592000;
		}
		elseif($role == 3){
			$user_level = 'reseller';
			$is_groupname = 'reseller';
			$role_dur = 2592000;
		}
		elseif($role == 4){
			$user_level = 'administrator';
			$is_groupname = 'administrator';
			$role_dur = 2592000;
		}
		elseif($role == 5){
			$user_level = 'subadmin';
			$is_groupname = 'subadmin';
			$role_dur = 2592000;
		}
		else{
			$db->HandleError('Sorry! Invalid Role Management');
			$valid = false;
		}
	}
	elseif($user_level_2 == 'administrator')
	{
		if($role == 1){
			$user_level = 'normal';
			$is_groupname = 'normal';
		}
		elseif($role == 2){
			$user_level = 'subreseller';
			$is_groupname = 'subreseller';
			$role_dur = 2592000;
		}
		elseif($role == 3){
			$user_level = 'reseller';
			$is_groupname = 'reseller';
			$role_dur = 2592000;
		}
		elseif($role == 5){
			$user_level = 'subadmin';
			$is_groupname = 'subadmin';
			$role_dur = 2592000;
		}else{
			$db->HandleError('Sorry! Invalid Role Management');
			$valid = false;
		}
	}
	elseif($user_level_2 == 'subadmin')
	{
		if($role == 1){
			$user_level = 'normal';
			$is_groupname = 'normal';
		}
		elseif($role == 2){
			$user_level = 'subreseller';
			$is_groupname = 'subreseller';
			$role_dur = 2592000;
		}
		elseif($role == 3){
			$user_level = 'reseller';
			$is_groupname = 'reseller';
			$role_dur = 2592000;
		}else{
			$db->HandleError('Sorry! Invalid Role Management');
			$valid = false;
		}
	}
	elseif($user_level_2 == 'reseller')
	{
		if($role == 1){
			$user_level = 'normal';
			$is_groupname = 'normal';
		}
		elseif($role == 2){
			$user_level = 'subreseller';
			$is_groupname = 'subreseller';
			$role_dur = 2592000;
		}else{
			$db->HandleError('Sorry! Invalid Role Management');
			$valid = false;
		}
	}
	elseif($user_level_2 == 'subreseller')
	{
		if($role == 1){
			$user_level = 'normal';
			$is_groupname = 'normal';
		}else{
			$db->HandleError('Sorry! Invalid Role Management');
			$valid = false;
		}
	}
	elseif($user_level_2 == 'normal')
	{
		$db->HandleError('Sorry! You are not Authorized to create on this page!...');
		$valid = false;
	}
	else
	{
		echo "<script> swal('Sorry! You Cannot Access this Website'); window.location.href='http://youjizz.com'; </script>";//fix soon
		$db->HandleError('Sorry! You are not Authorized to create on this page!...');
		$valid = false;
	}
	
		
	if($valid)
	{	
		if($category == 'premium')
		{
			$type = 'VIP and Private';
			$transfer = $db->sql_query("SELECT duration, vip_duration, private_duration FROM users WHERE user_id = '".$get_id."' AND duration='".$u_dur."' AND vip_duration='".$u_vip."' AND private_duration='".$u_priv."'");
			$trans_row = $db->sql_fetchrow($transfer);
			$trans_dur = $trans_row['vip_duration'];
			$trans_dur2 = $trans_row['private_duration'];
			$trans_dur3 = $trans_dur2 * 3;
			$to_premium = ($trans_dur * 2) + $trans_dur3;
		}
		elseif($category == 'vip')
		{
			$type = 'Premium and Private';
			$transfer = $db->sql_query("SELECT duration, vip_duration, private_duration FROM users WHERE user_id = '".$get_id."' AND duration='".$u_dur."' AND vip_duration='".$u_vip."' AND private_duration='".$u_priv."'");
			$trans_row = $db->sql_fetchrow($transfer);
			$trans_dur = $trans_row['duration'];
			$trans_dur2 = $trans_row['private_duration'];
			$trans_dur3 = $trans_dur2 * 3;
			$trans_dur4 = $trans_dur3 / 2;
			$to_vip = ($trans_dur / 2) + $trans_dur4;
		}
		elseif($category == 'private')
		{
			$type = 'Premium and VIP';
			$transfer = $db->sql_query("SELECT duration, vip_duration, private_duration FROM users WHERE user_id = '".$get_id."' AND duration='".$u_dur."' AND vip_duration='".$u_vip."' AND private_duration='".$u_priv."'");
			$trans_row = $db->sql_fetchrow($transfer);
			$trans_dur = $trans_row['duration'];
			$trans_dur2 = $trans_row['vip_duration'];
			$trans_dur3 = ($trans_dur2 * 2) / 3;
			$to_private = ($trans_dur / 3) + $trans_dur3;
		}

		$vip_calc = $db->calc_time($to_vip);
		$vipdur1 = $vip_calc['days'] . " day(s), " . $vip_calc['hours'] . " hour(s) and " . $vip_calc['minutes'] . " minutes";
					
		$prem_calc = $db->calc_time($to_premium);
		$premur1 = $prem_calc['days'] . " day(s), " . $prem_calc['hours'] . " hour(s) and " . $prem_calc['minutes'] . " minutes";

		$private_calc = $db->calc_time($to_private);
		$privatedur1 = $private_calc['days'] . " day(s), " . $private_calc['hours'] . " hour(s) and " . $private_calc['minutes'] . " minutes";

		if($category == 'premium')
		{
			if($trans_dur >= 0 || $trans_dur2 >= 0 )
			{
				$update = $db->sql_query("UPDATE users SET is_connected=0, vip_duration=vip_duration-'".$trans_dur."', private_duration=private_duration-'".$trans_dur2."', 
				is_vip=0, is_private=0,  
				duration = duration+'".$to_premium."' WHERE user_id='".$get_id."'");
				if($update){
					$db->sql_query("INSERT INTO conversion_logs
					(client_id, premium, vip, private, description, logs_date, ipaddress)
					VALUES
					('".$user_id_2."','".$premur1."','".$vipdur1."','".$privatedur1."','Convert to Premium',NOW(),'".$db->get_client_ip()."')
					");
				}	
			}elseif($to_premium >= 0 )
			{
				$db->HandleSuccess('Durations Not Converted');

			}else{
				$db->HandleError('Insufficient '.$type.' Durations');
			}
		}
		elseif($category == 'vip')
		{
			if($trans_dur >= 0 || $trans_dur2 >= 0 )
			{
				$update = $db->sql_query("UPDATE users SET is_connected=0, duration = duration-'".$trans_dur."', private_duration=private_duration-'".$trans_dur2."', 
				is_vip=1, is_private=0, vip_duration = vip_duration+'".$to_vip."' WHERE user_id='".$get_id."'");
				if($update){
					$db->sql_query("INSERT INTO conversion_logs
					(client_id, premium, vip, private, description, logs_date, ipaddress)
					VALUES
					('".$user_id_2."','".$premur1."','".$vipdur1."','".$privatedur1."','Convert to VIP',NOW(),'".$db->get_client_ip()."')
					");
				}
			}elseif($to_vip >= 0 )
			{
				$db->HandleSuccess('Durations Not Converted');

			}else{
				$db->HandleError('Insufficient '.$type.' Durations');
			}	
		}
		elseif($category == 'private')
		{
			if($trans_dur >= 0 || $trans_dur2 >= 0 )
			{
				$update = $db->sql_query("UPDATE users SET is_connected=0, duration = duration-'".$trans_dur."', 
				is_vip=0, is_private=1, vip_duration = vip_duration-'".$trans_dur2."', private_duration = private_duration+'".$to_private."' WHERE user_id='".$get_id."'");
				if($update){
					$db->sql_query("INSERT INTO conversion_logs
					(client_id, premium, vip, private, description, logs_date, ipaddress)
					VALUES
					('".$user_id_2."','".$premur1."','".$vipdur1."','".$privatedur1."','Convert to Private',NOW(),'".$db->get_client_ip()."')
					");
				}
			}elseif($to_private >= 0 )
			{
				$db->HandleSuccess('Durations Not Converted');

			}else{
				$db->HandleError('Insufficient '.$type.' Durations');
			}	
		}else{
			$db->HandleError('Invalid Conversion Type!');
		}

		$password = $db->encrypt_key($db->encryptor('encrypt',$user_pass));
		$auth_vpn = md5($user_pass);
		if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
		$update = $db->sql_query("UPDATE users SET 
			user_name='".$db->SanitizeForSQL($user_name)."', full_name='".$db->SanitizeForSQL($full_name)."',
			uuid='".$db->SanitizeForSQL($v2ray)."',
			user_email='".$db->SanitizeForSQL($user_email)."', is_groupname='".$db->SanitizeForSQL($is_groupname)."', 
			user_level='".$db->SanitizeForSQL($user_level)."', upline='".$db->SanitizeForSQL($resellers)."', 
			user_pass='".$db->SanitizeForSQL($password)."', auth_vpn='".$db->SanitizeForSQL($auth_vpn)."', is_passchange=1 WHERE user_id='".$db->SanitizeForSQL($get_id)."'");			
		}else{
		$update = $db->sql_query("UPDATE users SET 
			user_name='".$db->SanitizeForSQL($user_name)."', full_name='".$db->SanitizeForSQL($full_name)."',
			uuid='".$db->SanitizeForSQL($v2ray)."',
			user_email='".$db->SanitizeForSQL($user_email)."', is_groupname='".$db->SanitizeForSQL($is_groupname)."', 
			user_level='".$db->SanitizeForSQL($user_level)."', 
			user_pass='".$db->SanitizeForSQL($password)."', auth_vpn='".$db->SanitizeForSQL($auth_vpn)."', is_passchange=1 WHERE user_id='".$db->SanitizeForSQL($get_id)."'");			
		}

		
		if($update){
			$db->sql_query("INSERT into username_logs
			(old_username, new_username, 
			 old_level, new_level,
			 old_upline, new_upline,
			 client_id, user_id,
			 logs_date, ipaddress)
			VALUES
			('".$db->SanitizeForSQL($db_user_name)."','".$db->SanitizeForSQL($user_name)."',
			 '".$db->SanitizeForSQL($db_user_level)."','".$db->SanitizeForSQL($user_level)."',
			 '".$db->SanitizeForSQL($db_upline)."','".$db->SanitizeForSQL($resellers)."',
			 '".$db->SanitizeForSQL($get_id)."','".$db->SanitizeForSQL($user_id_2)."', 
			 NOW(), '".$_SERVER['REMOTE_ADDR']."')
			");	
			echo "<script> 
					        swal({
					        type: 'success',
					        title: '$user_name',
                            html: 'Account Updated Successfully!',
                            showConfirmButton: false,
                            customClass: 'animated bounceIn swal2-popup',
                            animation: false,
                            timer: 3000
					        }).then(function() 
					        { location.reload(); 
					        });  
					       </script>";			
		}else{
			echo "<script> 
					        swal({
					        type: 'error',
					        title: '$user_name',
                            html: 'Updating account failed!',
                            showConfirmButton: false,
                            customClass: 'animated bounceIn swal2-popup',
                            animation: false,
                            timer: 3000
					        }).then(function() 
					        { location.reload(); 
					        });  
					       </script>";	
		}
	}
	echo $db->GetSuccessMessage();
	echo $db->GetErrorMessage();
}else{
if(!empty($_POST['user_name'])){
	$db->RedirectToURL($db->base_url());
	exit;
}
if(!empty($_POST['user_pass'])){
	$db->RedirectToURL($db->base_url());
	exit;
}
if(!empty($_POST['user_pass2'])){
	$db->RedirectToURL($db->base_url());
	exit;
}
if(empty($_POST['full_name'])){
	$db->RedirectToURL($db->base_url());
	exit;
}
if(empty($_POST['user_email'])){
	$db->RedirectToURL($db->base_url());
	exit;
}
if(empty($_POST['client_type'])){
	$db->RedirectToURL($db->base_url());
	exit;
}
}
?>	