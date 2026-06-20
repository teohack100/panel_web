<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
$valid = true;	
if(isset($_POST['submitted']))
{
	$user_name = $db->Sanitize(trim($_POST['user_name']));
	$user_pass = $db->Sanitize(trim($_POST['user_pass']));
	$user_pass2 = $db->Sanitize(trim($_POST['user_pass2']));
	$full_name = $db->Sanitize(trim($_POST['full_name']));
	$user_email = $db->Sanitize(trim($_POST['user_email']));
	$category = $db->encryptor('decrypt',(trim($_POST['client_type'])));
	$category = $db->Sanitize($category);
	
	if(empty($user_name))
	{
		$db->HandleError('El nombre de usuario está vacío!');
		$valid = false;
	}
	else if(preg_match('/[^_a-z-A-Z-0-9 ]/', $user_name))
	{
		$db->HandleError('Nombre de usuario no válido!');
		$valid = false;
	}
	else
	{
		$username_chk = $db->sql_numrows($db->sql_query("SELECT user_name FROM users WHERE user_name='".$db->SanitizeForSQL($user_name)."'"));
		if($username_chk > 0){
			$db->HandleError($user_name.' is already taken!');
			$valid = false;
		}
	}

	if(empty($user_pass))
	{
		$db->HandleError('La contraseña esta vacía!');
		$valid = false;
	}
	else if(preg_match('/[^_a-z-A-Z-0-9 ]/', $user_pass))
	{
		$db->HandleError('Contraseña invalida!');
		$valid = false;
	}
	else if(strlen($user_pass)<8)
	{
		$db->HandleError('Tu contraseña es demasiado corta!');
		$valid = false;
	}
	
	if(empty($user_pass2))
	{
		$db->HandleError('Retype password!');
		$valid = false;
	}
	else if(preg_match('/[^_a-z-A-Z-0-9 ]/', $user_pass2))
	{
		$db->HandleError('Contraseña invalida!');
		$valid = false;
	}
	else if(strlen($user_pass2)<8)
	{
		$db->HandleError('Tu contraseña es demasiado corta!');
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
	else
	{
		$email_chk = $db->sql_numrows($db->sql_query("SELECT user_email FROM users WHERE user_email='".$db->SanitizeForSQL($user_email)."'"));
		if($email_chk > 0){
			$db->HandleError($user_email.' ya esta registrado!');
			$valid = false;
		}
	}
	$code = md5(time());
	$code = rand(0,999999999);
	
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
	elseif($user_level_2 == 'subadmin' || $user_level_2 == 'administrator')
	{
		if($role == 1){
			$user_level = 'normal';
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
		if($category == 'premium'){
			$result = $db->sql_query("INSERT INTO users 
			( user_name, user_pass, auth_vpn, user_email, full_name, regdate, is_active, user_level, code, is_validated, upline, duration)
			VALUES
			('".$db->SanitizeForSQL($user_name)."','".$db->SanitizeForSQL($password)."','".$db->SanitizeForSQL($auth_vpn)."',
			 '".$db->SanitizeForSQL($user_email)."','".$db->SanitizeForSQL($full_name)."', '".date('Y-m-d h:i:s')."', 1,
			 '".$db->SanitizeForSQL($user_level)."','".$db->SanitizeForSQL($code)."', 1, '".$user_id_2."', 7200)");
			$insert_id = $db->sql_nextid();
			$insert_profile = $db->sql_query("INSERT INTO users_profile (profile_id) VALUES ('".$insert_id."')");
			$db->HandleSuccess('¡Registrado exitosamente! Cuenta premium: '.$user_name);
		}else{
			$db->HandleError('Sorry! Adding Record is FAILED!...');
		}
	}
	echo $db->GetSuccessMessage();
	echo $db->GetErrorMessage();
}else{
	if(empty($_POST['user_name'])){
		$db->RedirectToURL($db->base_url());
		exit;
	}
	if(empty($_POST['user_pass'])){
		$db->RedirectToURL($db->base_url());
		exit;
	}
	if(empty($_POST['user_pass2'])){
		$db->RedirectToURL($db->base_url());
		exit;
	}
	if(trim($_POST['full_name'])){
		$db->RedirectToURL($db->base_url());
		exit;
	}
	if(trim($_POST['user_email'])){
		$db->RedirectToURL($db->base_url());
		exit;
	}
}
?>	