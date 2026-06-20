<?php
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator'){
	
}else{
	header("Location: /dashboard");	
}
ini_set('max_execution_time', 150); //300 seconds = 5 minutes			
$stats = '';
$query = $db->sql_query("SELECT * FROM server_list WHERE status=1 ORDER BY server_name ASC");
while($row = $db->sql_fetchrow($query))
{
	$server_name = $row['server_name'];
	$server_id = $row['server_id'];
	$server_ip = $row['server_ip'];
	$server_tcp = $row['server_tcp'];
	$server_port = $row['server_port'];
	$server_parser = ''.$row['server_parser'].'/stat/tcp.txt';
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
				$stats .= "<td colspan='".$num ."'>NO User's Connected</td>";
				$stats .= "</tr>";
			}else{
				$stats .= "<tr>";
				if($user['CommonName'] == 'UNDEF'){
					$stats .= "<td><font color='red'><strong>".$user['CommonName']."</strong></font></td>";
				}elseif($server_name && $user['CommonName'] == $server_name && $user['CommonName']){
					$stats .= "<td><font color='red'><strong>".$user['CommonName']."</strong></font></td>";
				}else{
					$stats .= "<td><strong>".$user['CommonName']."</strong></td>";
				}		
                $stats .= "<td><span class=\"badge badge-info\"><span class=\"fas fa-server\"></span> ".$server_name."</span></td>";
			    $stats .= "<td><span class=\"badge badge-info\"><span class=\"fas fa-server\"></span> ".$user['RealAddress']."</span></td>";
				$stats .= "<td><span class=\"badge badge-info\"><span class=\"fas fa-arrow-down\"></span> ".$db->sizeformat($user['BytesSent'])."</span></td>";
				$stats .= "<td><span class=\"badge badge-info\"><span class=\"fas fa-arrow-up\"></span> ".$db->sizeformat($user['BytesReceived'])."</span></td>";
				$stats .= "<td><span class=\"badge badge-info\"><span class=\"fas fa-clock\"></span> ".$user['Since']."</span></td>";
				$stats .= "</tr>";
			}
		}
	}else{
		$logs = 1;
		return;
	}			

}
$smarty->assign("stats", $stats);
$smarty->display("online-users-tcp.tpl");
?>