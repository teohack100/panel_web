<?php
ini_set('max_execution_time', 150); //300 seconds = 5 minutes			
$stats = '';

if (function_exists('programmit_legacy_server_probes_enabled') && !programmit_legacy_server_probes_enabled()) {
	$stats = '<tr><td class="text-center" colspan="8">Monitoreo legacy desactivado por configuracion.</td></tr>';
	$smarty->assign("stats", $stats);
	$smarty->display("privateusers.tpl");
	return;
}

$query = $db->sql_query("SELECT * FROM server_list WHERE status=1 AND server_category='private' ORDER BY server_name ASC");
while($row = $db->sql_fetchrow($query))
{
	$server_name = $row['server_name'];
	$server_id = $row['server_id'];
	$server_ip = $row['server_ip'];
	$server_tcp = $row['server_tcp'];
	$server_port = $row['server_port'];
	$server_parser = $row['server_parser'];
	$servers = @fsockopen($server_ip, $server_port, $errno, $errstr, 2);
	if($servers)
	{
		$logs = $db->openvpnLogs($server_parser);
		foreach($logs['users'] as $user)
		{			
			if(!$logs)
			{
				if($user_id_2 == 1){
					$num = 5;
				}else{
					$num = 4;
				}
				
				$stats .= "<tr>";
				$stats .= "<td class=\"text-center\" colspan='".$num ."'>NO User's Connected</td>";
				$stats .= "</tr>";
			}else{
				$stats .= "<tr>";
				if($user['CommonName'] == 'UNDEF'){
					$stats .= "<td class=\"text-center\"><font color='red'>".$user['CommonName']."</font></td>";
				}elseif($server_name && $user['CommonName'] == $server_name && $user['CommonName']){
					$stats .= "<td class=\"text-center\"><font color='red'>".$user['CommonName']."</font></td>";
				}else{
					$stats .= "<td class=\"text-center\"><font color='green'>".$user['CommonName']."</font></td>";
				}
				
				$chk_user = $db->sql_query("SELECT private_slot FROM users WHERE user_name='".$user['CommonName']."' AND is_private=1");
				$chk_rows = $db->sql_fetchrow($chk_user);
				if($user_id_2 == 1){
					$stats .= "<td class=\"text-center\">".$user['RealAddress']."</td>";
				}
				$stats .= "<td class=\"text-center\">".$db->sizeformat($user['BytesSent'])."</td>";
				$stats .= "<td class=\"text-center\">".$db->sizeformat($user['BytesReceived'])."</td>";
				$stats .= "<td class=\"text-center\">".$user['Since']."</td>";
				$stats .= "<td class=\"text-center\">".date('F d. Y h:i:s A')."</td>";
				$stats .= "<td class=\"text-center\">".$chk_rows['private_slot']."</td>";
				$stats .= "<td class=\"text-center\">".$server_name."</td>";
				$stats .= "</tr>";
			}
		}
	}else{
		$logs = 1;
		return;
	}			

}
$smarty->assign("stats", $stats);
$smarty->display("privateusers.tpl");
?>
