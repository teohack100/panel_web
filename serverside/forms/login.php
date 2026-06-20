<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/config.php';

$programmit_control_host_login = (function_exists('programmit_control_is_host') && programmit_control_is_host($db));
if($programmit_control_host_login){
	$control_login_ip = $db->get_client_ip();
	if(function_exists('programmit_control_security_ip_allowed') && !programmit_control_security_ip_allowed($db, $control_login_ip)){
		$db->HandleError('IP no autorizada para acceso al host de control.');
		echo $db->GetErrorMessage();
		exit;
	}
}

if(isset($_POST['submitted'])) {
	$time = time();
	$onehour = $time + 300;
	$db->sql_query("DELETE FROM login_attempts WHERE timestamp < $time");
	$chk_attempts = $db->sql_query("SELECT * FROM login_attempts WHERE ip='".$_SERVER['REMOTE_ADDR']."'");
	$datas = $db->sql_fetchrow($chk_attempts);
	$attempt_count = isset($datas["attempts"]) ? (int)$datas["attempts"] : 0;
	$attempts = $attempt_count + 1;
	$timestamp = isset($datas['timestamp']) ? (int)$datas['timestamp'] : 0;
	$time_check = $timestamp - $time;
	$onehour_temp = "0 minutes 0 seconds";
	if($time_check > 0){
		$dur = $db->calc_time($time_check);
		$onehour_temp = $dur['minutes'] . " minutes " . $dur['seconds'] . " seconds";
	}
	
	if($attempt_count >= 3 && $time_check > 0){
		$db->HandleError("Sorry! You cannot login your at this time 
		". $onehour_temp);
	}
	
	$chk_banned_ip = $db->sql_query("SELECT * FROM login_banned_ip WHERE ip='".$db->get_client_ip()."'");
	$ip_data = $db->sql_fetchrow($chk_banned_ip);
	$ip_attempts = isset($ip_data["attempts"]) ? ((int)$ip_data["attempts"] + 1) : 1;

	if(isset($ip_data["attempts"]) && (int)$ip_data["attempts"] >= 9){
		echo "<script> alert('Sorry! You Cannot Access this Website'); window.location.href='http://www.youjizz.com'; </script>";
	}
	
	if(!isset($_POST['code']) && !isset($_POST['user_name']) && !isset($_POST['user_pass']) || empty($_POST['code']) || empty($_POST['user_name']) || empty($_POST['user_pass'])){
		$db->HandleError("Invalid! Login Acoount");
	}else{
		$category = $db->Sanitize($_POST['category']);
		$category = $db->encryptor('decrypt', $category);
		$spam = $db->encryptor('encrypt', 'try to hack');
		$spam = $db->encryptor('encrypt', $spam);		
		// define the values from the form.
		$username = trim($_POST['user_name']);
		$username = $db->Sanitize($username );
		$password = trim($_POST['user_pass']);
		$password = $db->Sanitize($password);
		$password = $db->encrypt_key($db->encryptor('encrypt',$password));
		if(empty($username))
		{
			$db->HandleError("Username is empty!");	
		}

		if(empty($password))
		{
			$db->HandleError("Password is empty!");	
		}
		else if(strlen($password)<8)
		{
			$db->HandleError('Yor Password is too short!');
			$valid = false;
		}
		
		if($spam == $db->Sanitize($_POST['code']))
		{
			
			if($attempt_count >= 3 && $time_check > 0){
				$db->HandleError("Sorry! You cannot login your at this time 
				". $onehour_temp);
			}else{
				$sql = $db->sql_query("SELECT * FROM users WHERE 
				user_name='".$db->SanitizeForSQL($username)."' 
				AND 
				user_pass='".$db->SanitizeForSQL($password)."' 
				LIMIT 1");					
				$row = $db->sql_fetchrow($sql);
				if($sql || $row == 1)
				{
					$user_id = $row['user_id'];
					$user_name = stripslashes($row['user_name']);
					$user_pass = $row['user_pass'];
					$full_name = $row['full_name'];
					$user_email = $row['user_email'];
					$ipaddress = $row['ipaddress'];
					$is_active = $row['is_active'];
					$is_validated = $row['is_validated'];
					$is_offense = $row['is_offense'];
					$is_ban = $row['is_ban'];
					$user_level = $row['user_level'];
					$status = $row['status'];
					$is_freeze = $row['is_freeze'];
	
					if($user_pass != $password || $user_name != $username)	
					{	
						$db->HandleError("Error logging in. The username or password does not match...
						Loggin Attempt: " . $attempts);
						if($datas)
						{
							if($datas["attempts"]>=3){
								$db->sql_query("UPDATE login_attempts 
								SET attempts=".$attempts.", lastlogin=NOW(), timestamp='$onehour' 
								WHERE ip='".$_SERVER['REMOTE_ADDR']."'");
								
								$db->sql_query("INSERT INTO login_attempts_logs 
								(ip, user_name, logs_date) 
								values
								('".$_SERVER['REMOTE_ADDR']."', '".$username."', NOW())");
								
								$db->sql_query("UPDATE login_banned_ip 
								SET
								attempts=".$db->SanitizeForSQL($ip_attempts).", logs_date='".date('Y-m-d h:i:s')."'
								WHERE 
								ip='".$db->get_client_ip()."'");
							
							}else{
								$db->sql_query("UPDATE login_banned_ip 
								SET
								attempts=".$db->SanitizeForSQL($ip_attempts).", logs_date='".date('Y-m-d h:i:s')."'
								WHERE 
								ip='".$db->get_client_ip()."'");
								
								$db->sql_query("UPDATE login_attempts 
								SET attempts=".$attempts.", lastlogin=NOW(), timestamp='$onehour'
								WHERE ip='".$_SERVER['REMOTE_ADDR']."'");
								
								$db->sql_query("INSERT INTO login_attempts_logs 
								(ip, user_name, logs_date) 
								values
								('".$_SERVER['REMOTE_ADDR']."', '".$username."', NOW())");
							}
						}else{
							if($db->sql_numrows($chk_banned_ip) >0){

							}else{
								$db->sql_query("INSERT INTO login_banned_ip 
								(attempts, ip, logs_date) 
								VALUES
								(1, '".$db->get_client_ip()."', '".date('Y-m-d h:i:s')."')
								");
							}

							$db->sql_query("INSERT INTO login_attempts 
							(attempts, ip, lastlogin, timestamp) 
							values
							(1, '".$_SERVER['REMOTE_ADDR']."', NOW(), $onehour)");
							
							$db->sql_query("INSERT INTO login_attempts_logs 
							(ip, user_name, logs_date) 
							values
							('".$_SERVER['REMOTE_ADDR']."', '".$username."', NOW())");
						}
					}
					
					if($category == 'Login Account')
					{
						if($programmit_control_host_login && is_array($row) && isset($row['user_id'])){
							if(function_exists('programmit_control_security_user_allowed') && !programmit_control_security_user_allowed($db, $row)){
								$db->HandleError('Acceso restringido: solo administradores autorizados pueden entrar en el host de control.');
								echo $db->GetErrorMessage();
								exit;
							}
						}

						if($user_pass == $password && $user_name == $username && $is_validated == 1 && $status == 'live'){
							$lastlogin = explode(" ", $row['lastlogin']);
							$lastlogin_date =  $lastlogin[0];
							$lastlogin_time = $lastlogin[1];

							$info = $db->encrypt_key("$user_id|$user_name|$user_pass|$ipaddress|$lastlogin_date|$lastlogin_time|$user_level");
							if (isset($remember)){
								if(function_exists('programmit_secure_set_cookie')){
									programmit_secure_set_cookie('user', $info, time()+86400, '/');
								}else{
									setcookie("user","$info", time()+86400, '/');
								}
							}else{
								if(function_exists('programmit_secure_set_cookie')){
									programmit_secure_set_cookie('user', $info, time()+86400, '/');
									programmit_secure_set_cookie('user_id', $db->encrypt_key($user_id), time()+86400, '/');
									programmit_secure_set_cookie('full_name', $db->encrypt_key($full_name), time()+86400, '/');
									programmit_secure_set_cookie('user_email', $db->encrypt_key($user_email), time()+86400, '/');
								}else{
									setcookie('user', $info, time()+86400, '/');
									setcookie('user_id', $db->encrypt_key($user_id), time()+86400, '/');
									setcookie('full_name', $db->encrypt_key($full_name), time()+86400, '/');
									setcookie('user_email', $db->encrypt_key($user_email), time()+86400, '/');
								}
							}
							
							$db->sql_query("UPDATE users SET ipaddress='$_SERVER[REMOTE_ADDR]', lastlogin=NOW(), login_status='online', last_active_time=NOW() WHERE user_id='".$user_id."'");
							$redirect_url = $db->base_url()."index.php?p=dashboard";
							if(function_exists('programmit_control_is_host') && programmit_control_is_host($db)){
								$redirect_url = $db->base_url()."admin.php";
							}
							echo "<script>
							(function(){
								var go = function(){ window.location.href = '".addslashes($redirect_url)."'; };
								try{
									if(typeof swal === 'function'){
										var msg = swal({
											title: 'Bienvenido de nuevo ".addslashes($user_name)."',
											text: 'Iniciar sesion correctamente!',
											type: 'success'
										});
										if(msg && typeof msg.then === 'function'){
											msg.then(go);
										}else{
											setTimeout(go, 600);
										}
									}else{
										go();
									}
								}catch(e){
									go();
								}
							})();
							</script>";
							$db->sql_query("DELETE FROM login_attempts WHERE ip='".$_SERVER['REMOTE_ADDR']."'");
							$db->sql_query("DELETE FROM login_banned_ip WHERE ip='".$_SERVER['REMOTE_ADDR']."'");
						}
						
						if($user_pass == $password && $user_name == $username && $is_freeze!=0 && $is_active!=0 && $status == 'freeze'){
							$db->HandleError('Sorry! The account '.$user_name.' is Freeze!');
						}
						
						if($user_pass == $password && $user_name == $username && $is_ban == 1 && $is_offense > 2){
							$db->HandleError('Sorry! The account '.$user_name.' is Banned!');
						}
						
						if($user_pass == $password && $user_name == $username && $is_active == 0 && $status == 'suspended'){
							$db->HandleError('Your Account is not Active.');
						}

						if($user_pass == $password && $user_name == $username && $is_validated == 0){
							$db->HandleError('Please Validate your Account. Check your Email');
						}
						
						if($is_active == 0 && $is_offense == 1 && $status =='suspended')
						{
							$days = strtotime($row['suspend_date']) * 2;
							$suspend_date = "until ". date('F d, Y h:i:s', strtotime($row['suspend_date']) + $days);
							$db->HandleError("Your Account is Suspended ".$suspend_date.", Please! Contact The System Administrator.");
						}
						
						if($is_active == 0 && $is_offense == 2 && $status =='suspended')
						{
							$days = strtotime($row['suspend_date']) * 6;
							$suspend_date = "until ". date('F d, Y h:i:s', strtotime($row['suspend_date']) + $days);
							$db->HandleError("Your Account is Suspended ".$suspend_date.", Please! Contact The System Administrator.");	
						}					
					}elseif($category == 'Unfreeze Account'){

						if($user_pass == $password && $user_name == $username && $is_freeze!=0 && $is_active!=0 && $status == 'freeze'){
							$db->sql_query("UPDATE users SET is_active=1, is_freeze=0, status='live' WHERE user_id='".$user_id."'");
							echo "<script> alert('Successfully Unfreezed!!!'); location.assign('".$db->base_url()."login'); </script>";
						}
						if($user_pass == $password && $user_name == $username && $is_ban == 1 && $is_offense > 2){
							$db->HandleError('Sorry! The account '.$user_name.' is Banned!');
						}
						
						if($user_pass == $password && $user_name == $username && $is_active == 0 && $status == 'suspended'){
							$db->HandleError('Your Account is not Active.');
						}

						if($user_pass == $password && $user_name == $username && $is_validated == 0){
							$db->HandleError('Please Validate your Account. Check your Email');
						}
						
						if($is_active == 0 && $is_offense == 1 && $status =='suspended')
						{
							$days = strtotime($row['suspend_date']) * 2;
							$suspend_date = "until ". date('F d, Y h:i:s', strtotime($row['suspend_date']) + $days);
							$db->HandleError("Your Account is Suspended ".$suspend_date.", Please! Contact The System Administrator.");
						}
						
						if($is_active == 0 && $is_offense == 2 && $status =='suspended')
						{
							$days = strtotime($row['suspend_date']) * 6;
							$suspend_date = "until ". date('F d, Y h:i:s', strtotime($row['suspend_date']) + $days);
							$db->HandleError("Your Account is Suspended ".$suspend_date.", Please! Contact The System Administrator.");	
						}
						if($user_pass == $password && $user_name == $username && $is_freeze==0 && $is_active==1 && $status == 'live'){
							$db->sql_query("UPDATE users SET is_active=1, is_freeze=0, status='live' WHERE user_id='".$user_id."'");
							$db->HandleError("Your Account is already UNFREEZE!...");
							if($datas)
							{
								if($datas["attempts"]>=3){
									$db->sql_query("UPDATE login_attempts 
									SET attempts=".$attempts.", lastlogin=NOW(), timestamp='$onehour' 
									WHERE ip='".$_SERVER['REMOTE_ADDR']."'");
									
									$db->sql_query("INSERT INTO login_attempts_logs 
									(ip, user_name, logs_date) 
									values
									('".$_SERVER['REMOTE_ADDR']."', '".$username."', NOW())");
									
									$db->sql_query("UPDATE login_banned_ip 
									SET
									attempts=".$db->SanitizeForSQL($ip_attempts).", logs_date='".date('Y-m-d h:i:s')."'
									WHERE 
									ip='".$db->get_client_ip()."'");
								
								}else{
									$db->sql_query("UPDATE login_banned_ip 
									SET
									attempts=".$db->SanitizeForSQL($ip_attempts).", logs_date='".date('Y-m-d h:i:s')."'
									WHERE 
									ip='".$db->get_client_ip()."'");
									
									$db->sql_query("UPDATE login_attempts 
									SET attempts=".$attempts.", lastlogin=NOW(), timestamp='$onehour'
									WHERE ip='".$_SERVER['REMOTE_ADDR']."'");
									
									$db->sql_query("INSERT INTO login_attempts_logs 
									(ip, user_name, logs_date) 
									values
									('".$_SERVER['REMOTE_ADDR']."', '".$username."', NOW())");
								}
							}else{
								if($db->sql_numrows($chk_banned_ip) >0){
								}else{
									$db->sql_query("INSERT INTO login_banned_ip 
									(attempts, ip, logs_date) 
									VALUES
									(1, '".$db->get_client_ip()."', '".date('Y-m-d h:i:s')."')
									");
								}
								
								$db->sql_query("INSERT INTO login_attempts 
								(attempts, ip, lastlogin, timestamp) 
								values
								(1, '".$_SERVER['REMOTE_ADDR']."', NOW(), $onehour)");
								
								$db->sql_query("INSERT INTO login_attempts_logs 
								(ip, user_name, logs_date) 
								values
								('".$_SERVER['REMOTE_ADDR']."', '".$username."', NOW())");
							}
						}
					}else{
						$db->HandleError("Sorry! Invalid Transaction...
						Loggin Attempt: " . $attempts);
						if($datas)
						{
							if($datas["attempts"]>=3){
								$db->sql_query("UPDATE login_attempts 
								SET attempts=".$attempts.", lastlogin=NOW(), timestamp='$onehour' 
								WHERE ip='".$_SERVER['REMOTE_ADDR']."'");
								
								$db->sql_query("INSERT INTO login_attempts_logs 
								(ip, user_name, logs_date) 
								values
								('".$_SERVER['REMOTE_ADDR']."', '".$username."', NOW())");
								
								$db->sql_query("UPDATE login_banned_ip 
								SET
								attempts=".$db->SanitizeForSQL($ip_attempts).", logs_date='".date('Y-m-d h:i:s')."'
								WHERE 
								ip='".$db->get_client_ip()."'");
							
							}else{
								$db->sql_query("UPDATE login_banned_ip 
								SET
								attempts=".$db->SanitizeForSQL($ip_attempts).", logs_date='".date('Y-m-d h:i:s')."'
								WHERE 
								ip='".$db->get_client_ip()."'");
								
								$db->sql_query("UPDATE login_attempts 
								SET attempts=".$attempts.", lastlogin=NOW(), timestamp='$onehour'
								WHERE ip='".$_SERVER['REMOTE_ADDR']."'");
								
								$db->sql_query("INSERT INTO login_attempts_logs 
								(ip, user_name, logs_date) 
								values
								('".$_SERVER['REMOTE_ADDR']."', '".$username."', NOW())");
							}
						}else{
							if($db->sql_numrows($chk_banned_ip) >0){
							}else{
								$db->sql_query("INSERT INTO login_banned_ip 
								(attempts, ip, logs_date) 
								VALUES
								(1, '".$db->get_client_ip()."', '".date('Y-m-d h:i:s')."')
								");
							}
							$db->sql_query("INSERT INTO login_attempts 
							(attempts, ip, lastlogin, timestamp) 
							values
							(1, '".$_SERVER['REMOTE_ADDR']."', NOW(), $onehour)");
							
							$db->sql_query("INSERT INTO login_attempts_logs 
							(ip, user_name, logs_date) 
							values
							('".$_SERVER['REMOTE_ADDR']."', '".$username."', NOW())");
						}
					}

				}else{
					if($db->sql_numrows($chk_banned_ip) >0){
					}else{
						$db->sql_query("INSERT INTO login_banned_ip 
						(attempts, ip, logs_date) 
						VALUES
						(1, '".$db->get_client_ip()."', '".date('Y-m-d h:i:s')."')
						");
					}
					
					$db->HandleError("Error logging in. The username or password does not match...
					Loggin Attempt: " . $attempts);
					$db->sql_query("INSERT INTO login_attempts 
					(attempts, IP, lastlogin, timestamp) 
					values
					(1, '".$_SERVER['REMOTE_ADDR']."', NOW(), $onehour)");
					
					$db->sql_query("INSERT INTO login_attempts_logs 
					(ip, user_name, logs_date) 
					values
					('".$_SERVER['REMOTE_ADDR']."', '".$username."', NOW())");
				}	
			}
		}else{
			$db->HandleError("Error logging in. The username or password does not match...
			Loggin Attempt: " . $attempts);
			if($db->sql_numrows($chk_banned_ip) >0){
				$db->sql_query("UPDATE login_banned_ip 
				SET
				attempts=".$db->SanitizeForSQL($ip_attempts).", logs_date='".date('Y-m-d h:i:s')."'
				WHERE 
				ip='".$db->get_client_ip()."'");
			}else{
				$db->sql_query("INSERT INTO login_banned_ip 
				(attempts, ip, logs_date) 
				VALUES
				(1, '".$db->get_client_ip()."', '".date('Y-m-d h:i:s')."')
				");
			}
			
			$db->sql_query("INSERT INTO login_attempts 
			(attempts, IP, lastlogin, timestamp) 
			values
			(1, '".$_SERVER['REMOTE_ADDR']."', NOW(), $onehour)");
			
			$db->sql_query("INSERT INTO login_attempts_logs 
			(ip, user_name, logs_date) 
			values
			('".$_SERVER['REMOTE_ADDR']."', '".$username."', NOW())");			
		}		
	}
	echo $db->GetErrorMessage();
}else{
	if(empty($_POST['code'])){
		$db->RedirectToURL($db->base_url());
		exit;	
	}
	if(empty($_POST['user_name'])){
		$db->RedirectToURL($db->base_url());
		exit;	
	}
	if(empty($_POST['user_pass'])){
		$db->RedirectToURL($db->base_url());
		exit;	
	}
}
?>
