<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin'  || $user_level_2 == 'administrator'){
}else{
	echo '<script>alert("Sorry! You dont have Permission to Access this Page!...");</script>';
	$db->RedirectToURL($db->base_url());
	exit;
}

if(!isset($_GET['dns_id']) || empty($_GET['dns_id']))
{
	echo '<script> alert("Invalid Transaction"); </script>';
	$db->RedirectToURL($db->base_url().'404');
	exit;
}else{
	$data = array();
	$dns_id = $db->Sanitize($_GET['dns_id']);
	$qry = $db->sql_query("SELECT * FROM dns WHERE dns_id='".$db->SanitizeForSQL($dns_id)."' LIMIT 1");
	$data = $db->sql_fetchrow($qry);
			
	echo json_encode($data);
}
?>
