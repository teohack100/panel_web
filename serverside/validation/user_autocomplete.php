<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();

if($user_id_2 == 1 || $user_level_2 == 'superadmin'){	
	if(isset($_GET['term'])){	
		$q = $db->Sanitize($_GET['term']);
		$q = $db->SanitizeForSQL($q);
		$sql = "SELECT user_id, user_name  FROM users 
		WHERE user_name LIKE '%".$q."%' AND user_id!=1 AND is_validated=1 AND status='live' AND is_active=1";
		$result = $db->sql_query($sql);
		$data = array();
		
		while($row = $db->sql_fetchrow($result))
		{
			$data[] = $row['user_name'];
		}
		echo json_encode($data);
	}else{
		header('Location: '.$db->base_url());
	}		
}else{
	if(isset($_GET['term'])){	
		$q = $db->Sanitize($_GET['term']);
		$q = $db->SanitizeForSQL($q);
		$sql = "SELECT user_id, user_name  FROM users 
		WHERE user_name LIKE '%".$q."%' AND user_id!=1 AND upline='".$user_id_2."' AND is_validated=1 AND status='live' AND is_active=1";
		$result = $db->sql_query($sql);
		$data = array();
		
		while($row = $db->sql_fetchrow($result))
		{
			$data[] = $row['user_name'];
		}
		echo json_encode($data);
	}else{
		header('Location: '.$db->base_url());
	}		
}

?>