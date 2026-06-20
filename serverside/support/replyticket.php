<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
$uid = $db->encryptor('decrypt', $_POST['secret']);
if($user_id_2 == $uid)
{
	$db->reply_ticket();	
}else{
	echo '<script> alert("Invalid Transaction"); </script>';
	$db->RedirectToURL($db->base_url().'myaccount');
	exit;
}
?>