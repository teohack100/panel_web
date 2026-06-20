<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
define('DOC_PATH', $_SERVER['DOCUMENT_ROOT'].'/');
require DOC_PATH . './includes/config.php';

if(isset($_POST['submitted']))
{
	if(!isset($_POST['email']) && empty($_POST['email']))
	{
		$db->HandleError("Sorry! Invalid Transaction...");
		$valid = false;
	}else{
		$email = $db->Sanitize($_POST['email']);
		if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $email))
		{
			$db->HandleError('Invalid Email address!');
			$valid = false;
		}
		
		$code = md5(time());
		$code = rand(0,999999999);
		
		$chk_user = $db->sql_query("SELECT user_id, user_email, is_validated FROM users WHERE user_email='".$db->SanitizeForSQL($email)."'LIMIT 1");
		$chk_row = $db->sql_fetchrow($chk_user);
		$is_validated = $chk_row['is_validated'];
		$user_email = $chk_row['user_email'];
		
		if($user_email != $email)
		{
			$db->HandleError("Sorry! Email Address is not exist!...");
			$valid = false;
		}
		
		if($user_email == $email && $is_validated  == 1)
		{
			$db->HandleError("Sorry! Your account is already validated!...");
			$valid = false;
		}
		
		if($user_email == $email && $is_validated  == 0)
		{
			$db->HandleSuccess("Successfully!... Submitted your Request.
			Please check your email inbox / spam to check your activation link...");
			
			$db->sql_query("UPDATE users SET code='".$code."' WHERE user_email='".$user_email."'");

			$link = $db->base_url()."?p=activate&code=".$code."&email=".$user_email;
			
			$mailer = new PHPMailer();
			$mailer->CharSet = 'utf-8';
			$mailer->AddAddress($user_email);
			$mailer->Subject = "Account Activation";
			$mailer->From = "no-reply@".$db->sitename;
			$mailer->FromName	= "Activation - " . $db->siteTitle;
			$mailer->Body ="Please click the link to complete the activation process.\r\n".
			"Activation Link: ".$link." \r\n".
			"Regards,\r\n".
			"OctaviaVPN\r\n";
			if(!$mailer->Send())
			{
				return false;
			}
			$valid = true;
		}
	}
	
	echo $db->GetSuccessMessage();
	echo $db->GetErrorMessage();
}else{
	$db->RedirectToURL($db->base_url());
	exit;
}
?>