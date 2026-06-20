<?php
if($_GET['code'] != "") {
	$code = $db->Sanitize(trim($_GET['code']));
	
	$query = $db->sql_query("SELECT user_name, user_email FROM users WHERE reset_code='".$code."'");
	$row = $db->sql_fetchrow($query);
	$num_rows = $db->sql_numrows($query);
	$error_code = 1;
	if($num_rows > 0) {
		$error_code = 0;
		if($_POST['reset']) {

			$user_pass = $db->Sanitize(trim($_POST['user_pass']));
			$user_pass2 = $db->Sanitize(trim($_POST['user_pass2']));

			if(empty($user_pass))
				$errors[] = 'Password is empty!';

			if(empty($user_pass2))
				$errors[] = 'Retype-password is empty!';

			if((!empty($user_pass)) && (!empty($user_pass2))) {
				//this code will check if the 2 passwords are match or not.
				if($user_pass != $user_pass2)
					$errors[] = 'Password doesn\'t Match!';
			}

			if(is_array($errors) == false) {
				$query = $db->sql_query("UPDATE users SET user_pass='".$db->encrypt_key($db->encryptor('encrypt', $user_pass))."', 
				auth_vpn='".md5($user_pass)."', reset_code=0 WHERE user_name='".$row['user_name']."'");
				$smarty->assign("error_code_post", 1);

				$eol ="\r\n";
				$subject = "Your New Password";

				$message = "<html>";
				$message .= "<head>";
				$message .= "<title>$subject</title>";
				$message .= "</head>";
				$message .= "<body>";
				$message .= "<h3>Your New Password</h3>";
				$message .= "<br />";
				$message .= "<div>Hello $row[user_name], </div>";
				$message .= "<br />";
				$message .= "<div>You Have Successfully Changed your Password.</div>";
				$message .= "<br />";
				$message .= "<div>Below is the record of your New Password.</div>";
				$message .= "<div style=\"font-weight: bolder;\">New Password: $user_pass</div>";
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
				$headers .= "From: ".$db->siteTitle." transyu9@gmail.com>".$eol;
				$headers .= "Reply-To: ".$db->siteTitle." <transyu9@gmail.com>".$eol;
				$headers .= "Return-Path: ".$db->siteTitle." <transyu9@gmail.com>".$eol;

				if(!@mail($row[user_email], $subject, $message, $headers)){
					die ("Failed sending registration email, please report this to the webmaster ($site_email)");
				}

			} else {
				$smarty->assign("user_pass", $user_pass);
				$smarty->assign("user_pass2", $user_pass2);
			}
		}
	}
	$smarty->assign("error_code", $error_code);
	$smarty->assign("err", $errors);
}
$smarty->display("recovery.tpl");
?>	