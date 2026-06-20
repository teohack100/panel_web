<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/config.php';
if(function_exists('programmit_control_is_host') && programmit_control_is_host($db)){
	if(!function_exists('programmit_control_security_allow_register') || !programmit_control_security_allow_register($db)){
		$db->HandleError('Registro deshabilitado en host de control.');
		echo $db->GetErrorMessage();
		exit;
	}
}
$valid = true;	
if(isset($_POST['submitted'])) {
	global $db, $prefix, $error_msg;
	global $site_name, $site_email, $site_url, $validate;
	$userip = $_SERVER['REMOTE_ADDR'];

	$sql = "SELECT * FROM limit_registration WHERE ipaddress = '".$userip."'";
	$result = $mysqli->query($sql);
	$count = $result->num_rows;
	$rows = $result->fetch_assoc();
	$timestamp = $rows['regtime'];
	$oneday = 600;
	$time = time();
	$onedaytime = $time + $oneday;
	$time_check = $timestamp - $time;
	$timedelete = $time - 100;
	$dur = $db->calc_time($time_check);
	$onedayreg = $dur['hours'] . " hour(s) and " . $dur['minutes'] . " minutes " . $dur['seconds'] . " seconds";
	$deletetime = $db->sql_query("DELETE FROM limit_registration WHERE regtime < $time");
	$user_name = $db->Sanitize(trim($_POST['user_name']));
	$user_pass = $db->Sanitize(trim($_POST['user_pass']));
	$user_pass2 = $db->Sanitize(trim($_POST['user_pass2']));
	$full_name = $db->Sanitize(trim($_POST['full_name']));
	$user_email = $db->Sanitize(trim($_POST['user_email']));

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
		$db->HandleError('Password is empty!');
		$valid = false;
	}
	else if(strlen($user_pass2)<8)
	{
		$db->HandleError('Yor Password is too short!');
		$valid = false;
	}
	
	if(empty($user_pass))
	{
		$db->HandleError('Retype password!');
		$valid = false;
	}
	else if(strlen($user_pass2)<8)
	{
		$db->HandleError('Yor Password is too short!');
		$valid = false;
	}
	
	if((!empty($user_pass)) && (!empty($user_pass2)))
	{
		if($user_pass != $user_pass2)
		{
			$db->HandleError('Password doesn\'t match!');
			$valid = false;
		}
	}

	$email = $user_email;
	$whitelist = array("gmail.com", "yahoo.com", "yahoo.com.ph", "live.com", "hotmail.com");
	
	$allowed = $whitelist;
	
	
	if(empty($user_email))
	{
		$db->HandleError('Email is empty!');
		$valid = false;
	}
	else
	if (filter_var($email, FILTER_VALIDATE_EMAIL))
	{
		$explodedEmail = explode('@', $email);
		$domain = array_pop($explodedEmail);

		if(!in_array($domain, $allowed))
		{
			$db->HandleError('Invalid Email address! 
						 Accepted Email List 
						 gmail.com, yahoo.com | .ph, live.com and hotmail.com');
						 $valid = false;
		}
	}
	else if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $user_email))
	{
		$db->HandleError('Invalid Email address!');
		$valid = false;
	}
	else
	{
		$email_chk = $db->sql_numrows($db->sql_query("SELECT user_email FROM users WHERE user_email='".$db->SanitizeForSQL($user_email)."'"));
		if($email_chk > 0){
			$db->HandleError($user_email.' is already registered!');
			$valid = false;
		}
	}
	
	$result_site_options = $db->sql_query("SELECT * FROM site_options");
	$row_site_options = $db->sql_fetchrow($result_site_options);
	$email_validation = $row_site_options['email_validation'];
	$email_validation = 1;
	$code = md5(time());
	$code = rand(0,999999999);
	if($email_validation == 1){
		$is_validated = 0;
		$subject = "Account confirmation";
	}else{
		$is_validated = 1;
		$subject = "Your account information";
	}

	$message = "<html>
			<head>
			<title>$subject</title>
			</head>
			<body>
				<h2>Welcome to ".$db->siteTitle."</h2>
				<div>Please keep this email for your records.</div>
				<br />
				<div>-------------------------------</div>
				<div>Username: $user_name</div>
				<div>Password: $user_pass</div>
				<div>-------------------------------</div>
				<br />";
if($email_validation == 1){
	$message .= "<div>Your account is currently <strong>NOT</strong> active. Please click following link to activate your account now.</div>";
	$message .= "<div><h2>http://".$db->sitename."?p=activate&code=$code&email=$user_email</h2></div>";
} else {
	$message .= "<div>Your account is currently active. You can use it by visiting the following link:</div>";
	$message .= "<div><a href=\"http://".$db->sitename."/login\">Click here to Login</a></div>";
}
	$message .= "<br />";
	$message .= "<div>Please do not forget your password as it has been encrypted in our database and we cannot retrieve it for you. However, you can request a new one which will be sent to your email.</div>";
	$message .= "<div>Thank you for registering.</div>";
	$message .= "<br /><br />";
	$message .= "<div>-------------------------------</div>";
	$message .= "<div>Admin - $db->siteTitle</div>";
	$message .= "<div>http://".$db->sitename."</div>";
	$message .= "<br /><br />";
	$message .= "<div>This email was automatically generated.</div>";
	$message .= "<div>Please do not respond to this email.</div>";
	$message .= "</body></html>";

	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= "From: $db->siteTitle <no-reply@".$db->sitename.">".$eol;
	$headers .= "Reply-To: $db->siteTitle <no-reply@".$db->sitename.">".$eol;
	$headers .= "Return-Path: $db->siteTitle <no-reply@".$db->sitename.">".$eol;

	if($valid)
	{
		if(!@mail($user_email, $subject, $message, $headers)){
			if($email_validation = 1) {
				$error = 1;
			} else {
				$error = 2;
			}
		} else {
			if($email_validation = 1) {
				$error = 3;
			} else {
				$error = 4;
			}
		}
						
		if(!$timestamp < $time_check) {	
			$db->HandleError('Sorry! you cannot register at this time... '. $onedayreg);
			$valid = false;
		} else {

			$insert_limit = $db->sql_query("INSERT INTO limit_registration (ipaddress, regtime) VALUES ('".$userip."', '".$onedaytime."')");
			$password = $db->encrypt_key($db->encryptor('encrypt',$user_pass));
			$auth_vpn = md5($user_pass);	
			$result = $db->sql_query("INSERT INTO users ( user_name, user_pass, auth_vpn, user_email, full_name, regdate, is_groupname, user_level, upline, credits, status, is_active, is_freeze, is_ban, code, is_validated)
			VALUES
			('".$db->SanitizeForSQL($user_name)."','".$db->SanitizeForSQL($password)."','".$db->SanitizeForSQL($auth_vpn)."',
			'".$db->SanitizeForSQL($user_email)."','".$db->SanitizeForSQL($full_name)."', NOW(), 'administrator', 'administrator', 1, 0, 'live', 1, 0, 0,
			'".$db->SanitizeForSQL($code)."', '".$db->SanitizeForSQL($is_validated)."')");
			$insert_id = $db->sql_nextid();
			$insert_profile = $db->sql_query("INSERT INTO users_profile (profile_id) VALUES ('".$insert_id."')");
			if($insert_id > 0 && function_exists('programmit_panel_access_bootstrap_user')){
				programmit_panel_access_bootstrap_user($db, (int)$insert_id, 'administrator', 0);
			}
			$db->HandleSuccess('Successfully registered! Please confirm your e-mail address to activate your FREE account. THANK YOU!');
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
	if(empty($_POST['full_name'])){
		$db->RedirectToURL($db->base_url());
		exit;	
	}
	if(empty($_POST['user_email'])){
		$db->RedirectToURL($db->base_url());
		exit;	
	}
}
	$result_site_options = $db->sql_query("SELECT * FROM site_options");
	$row_site_options = $db->sql_fetchrow($result_site_options);
	//echo $email_validation = $row_site_options['email_validation'];
	$email_validation = $row_site_options['email_validation'];
?>	
