<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();

if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
	if(!empty($_POST['type']))
	{
		$type = $_POST['type'];
		$names = $_POST['name_startsWith'];
		$query = "SELECT user_id, user_name FROM users WHERE 
		UPPER($type) LIKE '%".$names."%'
		AND user_id!=1 AND is_active=1
		AND private_duration>0 AND is_private=1 
		AND private_slot=0 AND status='live' ";
		$result = $db->sql_query($query);
		$data = array();

		while($row = $db->sql_fetchrow($result))
		{
			$id = $db->encryptor('encrypt',$row['user_id']);
			$id = $db->encryptor('encrypt',$id);
			$names = $id."|".$row['user_name'];
			array_push($data, $names);
		}	
		echo json_encode($data);
	}else{
		echo '<script> alert("Invalid Transaction"); </script>';
		$db->RedirectToURL($db->base_url().'404');
		exit;
	}
}else{
	echo '<script> alert("Invalid Transaction"); </script>';
	$db->RedirectToURL($db->base_url().'404');
	exit;
}	

?>