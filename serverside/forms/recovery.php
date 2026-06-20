<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';

if(!empty($_POST['menu'])){
	if(!empty($_POST['given']))
	{
		$menu =  $db->Sanitize($_POST['menu']);
		$given =  $db->Sanitize($_POST['given']);
		
		$query = $db->sql_query("SELECT user_id, user_name, user_email FROM users where ".$menu." = '".$given."'");
		$password = $db->gen_id();
		$queryNum = $db->sql_numrows($query);
	 
		if($queryNum == 1)
		{
			$queryRow = $db->sql_fetchrow($query);
			$email = $queryRow['user_email'];
			$code = md5($db->gen_id());
			$link = $db->base_url()."recovery&code=$code";
			
			$update_que = $db->sql_query("UPDATE users SET reset_code='$code' WHERE user_email='$email'");
			
			$msg = "Hello " . $queryRow['user_name'] . "\r\n";
			$msg .= "You are receiving this email because you have (or someone pretending to be you has) requested a new password be sent for your account on My VPN \r\n \r\n";
			$msg .= "Please click the link below to complete the request: \r\n \r\n";
			$msg .= $link . "\r\n \r\n";
			$msg .= "Regards, \r\n";
			$msg .= "Support CyberghostVPN \r\n";
			$msg .= $db->sitename;
			$subject = $db->siteTitle . ' - Account Recovery';
			$recovery_logs = $db->sql_query("INSERT INTO recovery_logs (recovery_menu, recovery_ipaddress, recovery_date) 
											VALUES ('".$given."','".$_SERVER['REMOTE_ADDR']."','".date('Y-m-d H:i:s')."')");
			#set email headers  to aviod spam filters
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			//$headers .= "From: ".$db->siteTitle." <no-reply@".$db->sitename.">".$eol;
			$headers = 'From: support@CyberghostVPN.net' . "\r\n" .
			'Reply-To: support@CyberghostVPN.net' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
			
			
			mail($email, $subject, $msg, $headers);	
			
			$db->HandleSuccess("Successfully!... Requested password link");
			//echo $db->GetSuccessMessage();
		}else{
			$db->HandleError($given.' Not Found!');
			//echo $db->GetErrorMessage();
		}			

	}
}

if(isset($_POST['reset'])){
	if(!isset($_POST['resetcodes']) || empty($_POST['resetcodes'])){
	}else{
		$code = $db->Sanitize(trim($_POST['resetcodes']));
		$query = $db->sql_query("SELECT user_name, user_email FROM users WHERE reset_code='".$code."'");
		$row = $db->sql_fetchrow($query);
		$num_rows = $db->sql_numrows($query);
		if($num_rows > 0) 
		{
			$user_pass = $db->Sanitize(trim($_POST['user_pass']));
			$user_pass2 = $db->Sanitize(trim($_POST['user_pass2']));

			if(empty($user_pass)){
				$db->HandleError('Password is empty!');
				echo $db->GetErrorMessage();
			}
			else if(strlen($user_pass)<8)
			{
				$db->HandleError('Yor Password is too short!');
				echo $db->GetErrorMessage();
			}
			
			if(empty($user_pass2)){
				$db->HandleError('Retype-password is empty!');
				echo $db->GetErrorMessage();
			}
			else if(strlen($user_pass2)<8)
			{
				$db->HandleError('Yor Password is too short!');
				echo $db->GetErrorMessage();
			}
			
			if((!empty($user_pass)) && (!empty($user_pass2))) {
				if($user_pass != $user_pass2)
				{
					$db->HandleError('Password doesn\'t Match!');
					echo $db->GetErrorMessage();
				}
			}
			
			$update = $db->sql_query("UPDATE users SET user_pass='".$db->encrypt_key($db->encryptor('encrypt', $user_pass))."', 
			auth_vpn='".md5($user_pass)."', reset_code=0 WHERE user_name='".$row['user_name']."'");

				$eol ="\r\n";
				$subject = "Your New Password";

				$message = "<html>";
				$message .= "<head>";
				$message .= "<title>".$subject."</title>";
				$message .= "</head>";
				$message .= "<body>";
				$message .= "<h3>Your New Password</h3>";
				$message .= "<br />";
				$message .= "<div>Hello ".$row['user_name']."</div>";
				$message .= "<br />";
				$message .= "<div>You Have Successfully Changed your Password.</div>";
				$message .= "<br />";
				$message .= "<div>Below is the record of your New Password.</div>";
				$message .= "<div style=\"font-weight: bolder;\">New Password: ".$user_pass."</div>";
				$message .= "<br /><br />";
				$message .= "<div>Should you ever forget your Password just clieck Forgot Password.</div>";
				$message .= "<br />";
				$message .= "<div>--</div>";
				$message .= "<div>-Thanks</div>";
				$message .= "<div>".$db->siteTitle."</div>";
				$message .= "<br />";
				$message .= "<div>This email was automatically generated.</div>";
				$message .= "<div>Please do not respond to this email or it will be ignored.</div>";
				$message .= "</body></html>";

				#set email headers  to aviod spam filters
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers .= "From: ".$db->siteTitle." <no-reply@".$db->sitename.">".$eol;
				$headers .= "Reply-To: ".$db->siteTitle." <no-reply@".$db->sitename.">".$eol;
				$headers .= "Return-Path: ".$db->siteTitle." <no-reply@".$db->sitename.">".$eol;

				if(!@mail($row[user_email], $subject, $message, $headers)){
					$db->HandleError("Failed sending registration email, please report this to the webmaster ($site_email)");
					//echo $db->GetErrorMessage();
				}else{
					$db->HandleSuccess("Successfully!... Changing Password");
					//echo $db->GetSuccessMessage();
				}
		}
	}
}
echo $db->GetSuccessMessage();
echo $db->GetErrorMessage();
?>