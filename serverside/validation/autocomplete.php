<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();

if(!empty($_POST['type'])){
	$type = $_POST['type'];
	$names = $_POST['name_startsWith'];
	if($user_id_2 == 1 || $user_level_2 == 'superadmin'){	
		$query = "SELECT user_id, user_name FROM users WHERE UPPER($type) LIKE '%".$names."%' 
		AND user_id!=1  AND user_level!='superadmin' 
		AND is_validated=1 AND status='live' AND is_active=1";
	}else{
		$query = "SELECT user_id, user_name FROM users WHERE UPPER($type) LIKE '%".$names."%' 
		AND user_id!=1  AND user_level!='superadmin' AND upline='".$user_id_2."'
		AND is_validated=1 AND status='live' AND is_active=1";		
	}	
	$result = $db->sql_query($query);
	$data = array();

	while ($row = $db->sql_fetchrow($result))
	{
		$names = $db->encryptor('encrypt',$row['user_id'])."|".$row['user_name'];
		array_push($data, $names);
	}	
	echo json_encode($data);
}else{
	header('Location: '.$db->base_url());
}	

?>