<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once 'config.php';

    //Durations every 5min (300sec = 5min)
	$disco = $db->sql_query("UPDATE users SET is_connected=0 WHERE user_id > 0");
	
	if($disco){
		echo "Disconnect Success <br />";
	}else{
		echo "Disconnect Failed <br />";
	}
	


?>

