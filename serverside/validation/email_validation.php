<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
if(!empty($_POST['user_email'])){
	$isAvailable  = true; // or false
	$data = array();
	$signup_email = $db->Sanitize($_POST['user_email']);	
	$email = $db->SanitizeForSQL($signup_email);
	$querys = "SELECT user_email FROM users WHERE UPPER(user_email)='".$email."'";
	$results = $db->sql_query($querys);

	if($db->sql_numrows($results) == 0)
	{
		while ($rows = $db->sql_fetchrow($results))
		{
			$email_add = $row['user_email'];
			array_push($data, $email_add);
		}
	}else{
		$isAvailable  = false;
	}
		
	echo json_encode(array('valid' => $isAvailable ));		
}else{
	header('Location: '.$db->base_url());
}
?>