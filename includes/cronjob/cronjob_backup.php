<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require "config.php";
ini_set('max_execution_time', 150);
$ip = $db->get_client_ip();
	$path = '../backup/';
	$para = array(
		'db_host'=> $DB_host,
		'db_uname' => $DB_user,
		'db_password' => $DB_pass,
		'db_to_backup' => $DB_name,
		'db_backup_path' => $path,
		'db_exclude_tables' => array()
	);
	

if($db->get_client_ip() == 'UNKNOWN'){
	
	$db->__backup_mysql_database($para);
}else{
	echo '<script> alert("You are a Mother Fucker! Damn shit!... Your IP Address: '.$ip.'");
	window.location.href="http://www.google.com"; </script>';
}
?>