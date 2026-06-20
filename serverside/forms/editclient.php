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
	$user_pass2 = $db->Sanitize(trim($_POST['user_pass2']));
	$full_name = $db->Sanitize(trim($_POST['full_name']));
	$user_email = $db->Sanitize(trim($_POST['user_email']));
	
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
			
	if(empty($user_pass2))
	{
		$db->HandleError('Retype password!');
		$valid = false;
	}
	else if(preg_match('/[^_a-z-A-Z-0-9 ]/', $user_pass2))
	{
		$db->HandleError('Invalid password!');
		$valid = false;
	}
	else if(strlen($user_pass2)<8)
	{
		$db->HandleError('Yor Password is too short!');
		$valid = false;
	}
	
	if((!empty($user_pass)) && (!empty($user_pass2)))
	{
		//this code will check if the 2 passwords are match or not.
		if($user_pass != $user_pass2)
		{
			$db->HandleError('Password doesn\'t match!');
			$valid = false;
		}
	}

	if(empty($full_name)) {
		$db->HandleError('Full name is empty!');
		$valid = false;
	} else if(preg_match('/[^\/._a-zA-Z0-9 ]/', $full_name)) {
		$db->HandleError('Invalid Full name!!');
		$valid = false;
	}
		
	if(empty($user_email))
	{
		$db->HandleError('Email is empty!');
		$valid = false;
	}
	else if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $user_email))
	{
		//print the error message and load the form.
		$db->HandleError('Invalid Email address!');
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
	
	$c_result = $db->sql_query("SELECT user_name, user_email, user_level, upline FROM users WHERE user_id='".$db->SanitizeForSQL($get_id)."'");
	$row = $db->sql_fetchrow($c_result);
	$db_user_name = $row['user_name'];
	$db_user_email = $row['user_email'];
	$db_user_level = $row['user_level'];
	$db_upline = $row['upline'];
	
	if($user_name != $db_user_name) {
		$u_result = $db->sql_query("SELECT user_name FROM users WHERE user_name='$user_name'");
		$user_name_check = $db->sql_numrows($u_result);
		if($user_name_check > 0) {
			$db->HandleError($user_name.' is already in our database!');
			$valid = false;
		}
	}
	else if($user_email != $db_user_email) {
		$u_result = $db->sql_query("SELECT user_email FROM users WHERE user_email='$user_email'");
		$user_email_check = $db->sql_numrows($u_result);
		if($user_email_check > 0) {
			$db->HandleError($user_email.' is already in our database!');
			$valid = false;
		}
	}
	
	$role = $db->Sanitize($_POST['role']);
	if($user_id_2 == 1 || $user_level_2 == 'superadmin')
	{
		if($role == 1){
			$user_level = 'normal';
		}
		elseif($role == 2){
			$user_level = 'subreseller';
		}
		elseif($role == 3){
			$user_level = 'reseller';
		}
		elseif($role == 4){
			$user_level = 'administrator';
		}
		elseif($role == 5){
			$user_level = 'subadmin';
		}else{
			$db->HandleError('Sorry! Invalid Role Management');
			$valid = false;
		}
	}
	elseif($user_level_2 == 'administrator')
	{
		if($role == 1){
			$user_level = 'normal';
		}
		elseif($role == 2){
			$user_level = 'subreseller';
		}
		elseif($role == 3){
			$user_level = 'reseller';
			}
		elseif($role == 5){
			$user_level = 'subadmin';
		}else{
			$db->HandleError('Sorry! Invalid Role Management');
			$valid = false;
		}
	}
	elseif($user_level_2 == 'subadmin')
	{
		if($role == 1){
			$user_level = 'normal';
		}
		elseif($role == 2){
			$user_level = 'subreseller';
		}
		elseif($role == 3){
			$user_level = 'reseller';
		}else{
			$db->HandleError('Sorry! Invalid Role Management');
			$valid = false;
		}
	}
	elseif($user_level_2 == 'reseller')
	{
		if($role == 1){
			$user_level = 'normal';
		}
		elseif($role == 2){
			$user_level = 'subreseller';
		}else{
			$db->HandleError('Sorry! Invalid Role Management');
			$valid = false;
		}
	}
	elseif($user_level_2 == 'subreseller')
	{
		if($role == 1){
			$user_level = 'normal';
		}else{
			$db->HandleError('Sorry! Invalid Role Management');
			$valid = false;
		}
	}
	else
	{
		$db->HandleError('Sorry! You are not Authorized to create on this page!...');
		$valid = false;
	}
	
	if($valid)
	{	
		$password = $db->encrypt_key($db->encryptor('encrypt',$user_pass));
		$auth_vpn = md5($user_pass);
		if($user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller'){
		$update = $db->sql_query("UPDATE users SET 
			user_name='".$db->SanitizeForSQL($user_name)."', full_name='".$db->SanitizeForSQL($full_name)."',
			user_email='".$db->SanitizeForSQL($user_email)."', user_level='".$db->SanitizeForSQL($user_level)."', 
			user_pass='".$db->SanitizeForSQL($password)."', auth_vpn='".$db->SanitizeForSQL($auth_vpn)."'
			WHERE user_id='".$db->SanitizeForSQL($get_id)."'");
		}
		if($update)
		{
			$db->sql_query("INSERT into username_logs
			(old_username, new_username, 
			 old_level, new_level,
			 old_upline, new_upline,
			 client_id, user_id,
			 logs_date, ipaddress)
			VALUES
			('".$db->SanitizeForSQL($db_user_name)."','".$db->SanitizeForSQL($user_name)."',
			 '".$db->SanitizeForSQL($db_user_level)."','".$db->SanitizeForSQL($user_level)."',
			 '".$db->SanitizeForSQL($db_upline)."','".$db->SanitizeForSQL($db_upline)."',
			 '".$db->SanitizeForSQL($get_id)."','".$db->SanitizeForSQL($user_id_2)."', 
			 NOW(), '".$_SERVER['REMOTE_ADDR']."')
			");
			$db->HandleSuccess('Successfully Updated! Account: ' .$user_name);
		}else{
			$db->HandleError('Sorry Failed to Update! Account: ' .$user_name);	
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
}
?>	