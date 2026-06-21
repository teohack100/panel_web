<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once 'config.php';

if (function_exists('programmit_legacy_server_probes_enabled') && !programmit_legacy_server_probes_enabled()) {
	return;
}

$ip = $db->get_client_ip();
if($db->get_client_ip() == 'UNKNOWN'){
	$premium = $db->sql_query("SELECT * FROM server_list WHERE server_category = 'premium' ORDER BY server_name ASC");
	while($premium_row= $db->sql_fetchrow($premium ))
	{
		$server_ip = $premium_row['server_ip'];
		$servers = @fsockopen($server_ip, 22, $errno, $errstr, 5);
		if(!$servers)
		{
			$chk_premium_parser = '0';
		}else{
			$chk_premium_parser = '1';
		}
		$db->sql_query("UPDATE server_list SET status = '".$chk_premium_parser."' WHERE server_category = 'premium' AND server_ip = '".$server_ip."'");
	}
	
	$vip= $db->sql_query("SELECT * FROM server_list WHERE server_category = 'vip' ORDER BY server_name ASC");
	while($vip_row = $db->sql_fetchrow($vip))
	{
		$server_ip = $vip_row['server_ip'];
		$servers = @fsockopen($server_ip, 22, $errno, $errstr, 2);
		if(!$servers)
		{
			$chk_vip_parser = '0';
		}else{
			$chk_vip_parser = '1';
		}
		$db->sql_query("UPDATE server_list SET status = '".$chk_vip_parser."' WHERE server_category = 'vip' AND server_ip = '".$server_ip."'");
	}
	
	$ph = $db->sql_query("SELECT * FROM server_list WHERE server_category = 'ph' ORDER BY server_name ASC");
	while($ph_row = $db->sql_fetchrow($ph ))
	{
		$server_ip = $ph_row['server_ip'];
		$servers = @fsockopen($server_ip, 22, $errno, $errstr, 2);
		if(!$servers)
		{
			$chk_ph_parser = '0';
		}else{
			$chk_ph_parser = '1';
		}
		$db->sql_query("UPDATE server_list SET status = '".$chk_ph_parser ."' WHERE server_category = 'ph' AND server_ip = '".$server_ip."'");
	}
	
	$private = $db->sql_query("SELECT * FROM server_list WHERE server_category = 'private' ORDER BY server_name ASC");
	while($private_row = $db->sql_fetchrow($private))
	{
		$server_ip = $private_row['server_ip'];
		$servers = @fsockopen($server_ip, 22, $errno, $errstr, 2);
		if(!$servers)
		{
			$chk_private_parser = '0';
		}else{
			$chk_private_parser = '1';
		}
		$db->sql_query("UPDATE server_list SET status = '".$chk_private_parser ."' WHERE server_category = 'private' AND server_ip = '".$server_ip."'");
	}
	
	$free= $db->sql_query("SELECT * FROM server_list WHERE server_category = 'free' ORDER BY server_name ASC");
	while($free_row = $db->sql_fetchrow($free))
	{
		$server_ip = $free_row ['server_ip'];
		$servers = @fsockopen($server_ip, 22, $errno, $errstr, 2);
		if(!$servers)
		{
			$chk_free_parser = '0';
		}else{
			$chk_free_parser = '1';
		}
		$db->sql_query("UPDATE server_list SET status = '".$chk_free_parser ."' WHERE server_category = 'free' AND server_ip = '".$server_ip."'");
	}
}else{
	echo '<script> alert("You are a Mother Fucker! Damn shit!... Your IP Address: '.$ip.'");
	window.location.href="http://www.google.com"; </script>';
}
?>
