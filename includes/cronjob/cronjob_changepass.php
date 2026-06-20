<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once 'config.php';
$ip = $db->get_client_ip();
if($db->get_client_ip() == 'UNKNOWN')
{

$db->sql_query("UPDATE users SET is_passchange=0 WHERE is_passchange=1");

}else{
	echo '<script> alert("You are a Mother Fucker! Damn shit!... Your IP Address: '.$ip.'"); window.location.href="http://www.google.com"; </script>';
}
?>

