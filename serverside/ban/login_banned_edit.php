<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin'  || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator')
{
	if(!isset($_GET['ip']) && empty($_GET['ip'])){
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}else{
		$uid = $db->Sanitize($_GET['ip']);
		if($user_id_2 == 1 || $user_level_2 == 'subadmin'){
			$qry = $db->sql_query("SELECT * FROM login_banned_ip WHERE id='".$db->SanitizeForSQL($uid)."' LIMIT 1");
		}else{
			echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
			exit;
		}

		$row = $db->sql_fetchrow($qry);
		$values = array();
		if($row)
		{
			$id = $db->encryptor('encrypt',$row['id']);
			$values['id'] = $id;
			$values['ip'] = $row['ip'];
			$values['attempt'] = $row['attempts'];
		}
		echo json_encode($values);
	}
}else{
	echo '<script>alert("Sorry! You dont have Permission to Access this Page!...");</script>';
	$db->RedirectToURL($db->base_url());
	exit;
}
?>