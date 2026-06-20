<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin'  || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator')
{
	$regex = "((https?|ftp)\:\/\/)?"; // SCHEME
	$regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
	$regex .= "([a-z0-9-.]*)\.([a-z]{2,4})"; // Host or IP
	$regex .= "(\:[0-9]{2,5})?"; // Port
	$regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
	$regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
	$regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor
	$regex .= "(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})"; //IPv4
	
	if(isset($_POST['submitted']))
	{
		if(empty($_POST['ip'])){
			$db->HandleError("Empty IP Address");
			return false;
		}

		if(empty($_POST['attempt'])){
			$db->HandleError("Empty Attempt");
			return false;
		}

		if(preg_match('/^$regex$/', $_POST['ip'])){
			$db->HandleError("Invalid IP Address");
			return false;
		}
			
		$ip = $db->Sanitize($_POST['ip']);
		$attempt = $db->Sanitize($_POST['attempt']);

		$chk_server = $db->sql_query("SELECT ip FROM login_banned_ip WHERE ip='".$db->SanitizeForSQL($ip)."'");
		$server_row = $db->sql_fetchrow($chk_server);
		$db_ip = $server_row['ip'];

		if($ip != $db_ip) {
			$u_result = $db->sql_query("SELECT ip FROM login_banned_ip WHERE ip='".$db->SanitizeForSQL($ip)."'");
			if($db->sql_numrows($u_result) > 0) {
				$db->HandleError($db_ip.' is already in our database!');
			}
		}
			
		$sql_upload = "INSERT INTO login_banned_ip
				(ip, attempts, logs_date)
				VALUES
				('".$db->SanitizeForSQL($ip)."','".$db->SanitizeForSQL($attempt)."','".date('Y-m-d h:i:s')."')
			   ";
		$upload = $db->sql_query($sql_upload);
		
		if($upload)
		{
			$db->HandleSuccess("Successfully Added Banned IP!...");
		}else{
			$db->HandleError("Sorry! Failed to add Banned IP!..");
		}
		echo $db->GetSuccessMessage();
		echo $db->GetErrorMessage();
	}else{
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}
}else{
	echo '<script>alert("Sorry! You dont have Permission to Access this Page!...");</script>';
	$db->RedirectToURL($db->base_url());
	exit;
}
?>