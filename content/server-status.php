<?php
chkSession();

$embed_raw = isset($_GET['embed']) ? strtolower(trim((string)$_GET['embed'])) : '';
$server_status_embed_admin = in_array($embed_raw, array('1', 'admin', 'yes'), true);
$server_status_admin_access = (
	(int)$user_id_2 === 1 ||
	$user_level_2 === 'superadmin' ||
	$user_level_2 === 'administrator' ||
	$user_level_2 === 'subadmin'
);

if (!$server_status_embed_admin && $server_status_admin_access) {
	header("Location: ".$db->base_url()."admin.php#server-status-main");
	exit;
}

$smarty->assign("server_status_embed_admin", $server_status_embed_admin ? 1 : 0);

ini_set('max_execution_time', 150); //300 seconds = 5 minutes

if (function_exists('programmit_legacy_server_probes_enabled') && !programmit_legacy_server_probes_enabled()) {
	$legacy_notice = '<tr><td class="text-center" colspan="3">Monitoreo legacy desactivado por configuracion.</td></tr>';
	$smarty->assign("server", array($legacy_notice));
	$smarty->assign("premium_parser", $legacy_notice);
	$smarty->assign("vip_parser", $legacy_notice);
	$smarty->assign("private_parser", $legacy_notice);
	$smarty->assign("free_parser", $legacy_notice);
	$smarty->display("server-status.tpl");
	return;
}

$premium_parser = '';
$premium = $mysqli->query("SELECT * FROM server_list WHERE server_category = 'premium' ORDER BY server_name ASC");
while($premium_row = $premium->fetch_assoc())
{
	$server_ip = $premium_row['server_ip'];
	$servers = @fsockopen($server_ip, 80, $errno, $errstr, 2);
		
	$premium_parser .="<tr>";
	$premium_parser .="<td class=\"text-center\">".$premium_row['server_name']."</td>";
	$premium_parser .="<td class=\"text-center\">∞</td>";
	if(!$servers)
	{
		$premium_parser .="<td class=\"text-center\"><span class=\"label label-danger\">Offline</span></td>";
	}else{
		$premium_parser .="<td class=\"text-center\"><span class=\"label label-success\">Online</span></td>";
	}
	$premium_parser .="</tr>";
	$smarty->assign("premium_parser", $premium_parser);
}

$vip_parser = '';
$vip = $mysqli->query("SELECT * FROM server_list WHERE server_category = 'vip' ORDER BY server_name ASC");
while($vip_row = $vip->fetch_assoc())
{
	$server_ip = $vip_row['server_ip'];
	$servers = @fsockopen($server_ip, 80, $errno, $errstr, 2);
		
	$vip_parser .="<tr>";
	$vip_parser .="<td class=\"text-center\">".$vip_row['server_name']."</td>";
	$vip_parser .="<td class=\"text-center\">∞</td>";
	if(!$servers)
	{
		$vip_parser .="<td class=\"text-center\"><span class=\"label label-danger\">Offline</span></td>";
	}else{
		$vip_parser .="<td class=\"text-center\"><span class=\"label label-success\">Online</span></td>";
	}
	$vip_parser .="</tr>";
	$smarty->assign("vip_parser", $vip_parser);
}

$private_parser = '';
$private = $mysqli->query("SELECT * FROM server_list WHERE server_category = 'private' ORDER BY server_name ASC");
while($private_row = $private->fetch_assoc())
{
	$server_ip = $private_row['server_ip'];
	$servers = @fsockopen($server_ip, 80, $errno, $errstr, 2);
		
	$private_parser .="<tr>";
	$private_parser .="<td class=\"text-center\">".$private_row['server_name']."</td>";
	$private_parser .="<td class=\"text-center\">∞</td>";
	if(!$servers)
	{
		$private_parser .="<td class=\"text-center\"><span class=\"label label-danger\">Offline</span></td>";
	}else{
		$private_parser .="<td class=\"text-center\"><span class=\"label label-success\">Online</span></td>";
	}
	$private_parser .="</tr>";
	$smarty->assign("private_parser", $private_parser);
}

$free_parser = '';
$free = $mysqli->query("SELECT * FROM server_list WHERE server_category = 'free' ORDER BY server_name ASC");
while($free_row = $free->fetch_assoc())
{
	$server_ip = $free_row['server_ip'];
	$servers = @fsockopen($server_ip, 80, $errno, $errstr, 2);
		
	$free_parser .="<tr>";
	$free_parser .="<td class=\"text-center\">".$free_row['server_name']."</td>";
	$free_parser .="<td class=\"text-center\">∞</td>";
	if(!$servers)
	{
		$free_parser .="<td class=\"text-center\"><span class=\"label label-danger\">Offline</span></td>";
	}else{
		$free_parser .="<td class=\"text-center\"><span class=\"label label-success\">Online</span></td>";
	}
	$free_parser .="</tr>";
	$smarty->assign("free_parser", $free_parser);
}
$smarty->display("server-status.tpl");
?>
