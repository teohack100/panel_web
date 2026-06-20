<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
$root = __DIR__;
while (!is_file($root . '/includes/functions.php')) {
    $parent = dirname($root);
    if ($parent === $root) { break; }
    $root = $parent;
}
require $root . '/includes/functions.php';
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator'){
	if(!isset($_GET['id']) || empty($_GET['id']))
	{
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}else{
		$data = array();
		$id = $db->Sanitize($_GET['id']);
		$qry = $db->sql_query("SELECT * FROM download WHERE id='".$db->SanitizeForSQL($id)."' LIMIT 1");
		$row = $db->sql_fetchrow($qry);
		$data['id'] = $row['id'];
		$data['download_category'] = $row['download_category'];
		$data['download_title'] = $row['download_title'];
		$data['download_msg'] = $row['download_msg'];
		$data['download_network'] = $row['download_network'];
		$data['download_device'] = $row['download_device'];
		$data['download_file'] = $row['download_file'];
		if($row['download_file'] == '')
		{
			$data['download_url'] = $row['download_file'];
		}else{
			$data['download_url'] = '<a href="'.$db->base_url().'_uploads/'.$row['download_file'].'" class="text-center">Click here to Download</a>';
		}		
		echo json_encode($data);
	}
}else{
	echo '<script>alert("Sorry! You dont have Permission to Access this Page!...");</script>';
	$db->RedirectToURL($db->base_url());
	exit;
}
?>

