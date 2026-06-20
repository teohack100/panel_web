<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
if(!empty($_POST['user_name'])){
	$isAvailable  = true; // or false
	$data = array();
	$signup_username = $db->Sanitize($_POST['user_name']);	
	$username = $db->SanitizeForSQL($signup_username);
	$query = "SELECT user_name FROM users WHERE UPPER(user_name)='".$username."'";
	$result = $db->sql_query($query);

	if($db->sql_numrows($result) == 0)
	{
		while ($row = $db->sql_fetchrow($result))
		{
			$user = $row['user_name'];
			array_push($data, $user);
		}
	}else{
		$isAvailable  = false;
	}
		
	echo json_encode(array('valid' => $isAvailable ));	
}else{
	header('Location: '.$db->base_url());
}
?>