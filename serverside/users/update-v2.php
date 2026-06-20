<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator')
{
	$file = $db->base_url()."update-v2";
	$editor = $db->get_data($file);
	echo $editor;
}else{
	$db->RedirectToURL($db->base_url().'/dashboard');
    exit;
}
?>