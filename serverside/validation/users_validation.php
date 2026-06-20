<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if(!empty($_POST['reseller_uname'])){
	$isAvailable  = true; // or false
	$data = array();
	$item_name = $_POST['reseller_uname'];
	$name = $item_name;
	if($user_id_2 == 1 || $user_level_2 == 'superadmin'){	
		$query = "SELECT user_name FROM users WHERE UPPER(user_name)='".$name."' AND user_id!=1 AND is_validated=1 AND status='live' AND is_active=1 ";
		$result = $db->sql_query($query);
		$num_rows = $db->num_rows($result);

		if(!$result || $num_rows <= 0)
		{
			$isAvailable  = false;	
		}else{

			while ($row = $result->sql_fetchrow($result))
			{
				$names = $row['user_name'];
				array_push($data, $names);
			}
		}	
		echo json_encode(array('valid' => $isAvailable ));	
	}else{
		$query = "SELECT user_name FROM users WHERE UPPER(user_name)='".$name."' AND user_id!=1 AND upline='".$user_id_2."' AND is_validated=1 AND status='live' AND is_active=1 ";
		$result = $db->sql_query($query);
		$num_rows = $db->num_rows($result);

		if(!$result || $num_rows <= 0)
		{
			$isAvailable  = false;	
		}else{

			while ($row = $result->sql_fetchrow($result))
			{
				$names = $row['user_name'];
				array_push($data, $names);
			}
		}
		echo json_encode(array('valid' => $isAvailable ));	
	}
}else{
	header('Location: '.$db->base_url());
}
?>