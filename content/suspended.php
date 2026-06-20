<?php
chkSession();
if($user_id_2 == 1 || $user_level_2 != 'normal'){
	
}else{
	header("Location: /myaccount");	
}

######### Suspended Account ##########
if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
	$suspended = $db->sql_query("SELECT * FROM users WHERE user_id!=1 AND is_offense>0 ORDER BY IF(suspended_date = DATE(NOW()), 0, 1), suspended_date DESC");
}else{
	$suspended = $db->sql_query("SELECT * FROM users WHERE user_id!=1 AND upline=$user_id_2 AND is_offense>0 ORDER BY IF(suspended_date = DATE(NOW()), 0, 1), suspended_date DESC");
}
while($suspended_row = $db->sql_fetchrow($suspended)){
	$suspended_date = strtotime($suspended_row['suspended_date']);
	$dur = calc_time($suspended_row['duration']);
	$dur2 = calc_time($suspended_row['vip_duration']);
	
	if($suspended_row['duration'] == 0){
		$premuim_duration = "<font color='red'>". $dur['days'] . "</font> Day(s), <font color='red'>" . $dur['hours'] . "</font> Hour(s) and <font color='red'>" . $dur['minutes'] . "</font> Minutes Left.";
	}elseif($suspended_row['duration'] < 3600){
		$premuim_duration = "<font color='red'>". $dur['days'] . "</font> Day(s), <font color='red'>" . $dur['hours'] . "</font> Hour(s) and <font color='orange'>" . $dur['minutes'] . "</font> Minutes Left.";	
	}else{
		$premuim_duration = "<font color='green'>". $dur['days'] . "</font> Day(s), <font color='green'>" . $dur['hours'] . "</font> Hour(s) and <font color='green'>" . $dur['minutes'] . "</font> Minutes Left.";
	}

	if($suspended_row['vip_duration'] == 0){
		$vip_duration = "<font color='red'>". $dur2['days'] . "</font> Day(s), <font color='red'>" . $dur2['hours'] . "</font> Hour(s) and <font color='red'>" . $dur2['minutes'] . "</font> Minutes Left.";
	}elseif($suspended_row['vip_duration'] < 3600){
		$vip_duration = "<font color='red'>". $dur2['days'] . "</font> Day(s), <font color='red'>" . $dur2['hours'] . "</font> Hour(s) and <font color='orange'>" . $dur2['minutes'] . "</font> Minutes Left.";	
	}else{
		$vip_duration = "<font color='green'>". $dur2['days'] . "</font> Day(s), <font color='green'>" . $dur2['hours'] . "</font> Hour(s) and <font color='green'>" . $dur2['minutes'] . "</font> Minutes Left.";
	}
	
	if($suspended_row['is_offense'] > 2){
		$offense = '<span class="label label-danger">'.$suspended_row['is_offense'].'</span>';	
	}else
	if($suspended_row['is_offense'] == 2){
		$offense = '<span class="label label-warning">'.$suspended_row['is_offense'].'</span>';	
	}else
	if($suspended_row['is_offense'] == 1){
		$offense = '<span class="label label-info">'.$suspended_row['is_offense'].'</span>';	
	}		
	$select_qry = $db->sql_query("SELECT * FROM users WHERE user_id='".$suspended_row['upline']."'");
	$select_row = $db->sql_fetchrow($select_qry);	
	$suspended_client[]  = '<tr>';
	$suspended_client[] .= '<td class="text-center">'.$suspended_row['user_name'].'</td>';
	$suspended_client[] .= '<td class="text-center">'.$premuim_duration.'</td>';
	$suspended_client[] .= '<td class="text-center">'.$vip_duration.'</td>';
	$suspended_client[] .= '<td class="text-center">'.$offense.'</td>';
	
if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
	$suspended_client[] .= '<td class="text-center">'.$select_row['user_name'].'</td>';	
}
	$suspended_client[] .= '<td class="text-center">'.date('F d, Y h:i', $suspended_date).'</td>';
	$suspended_client[] .= '<td class="text-center">'.$db->time_elapsed_string($suspended_date).'</td>';
	$suspended_client[] .= '</tr>';
	
}
$smarty->assign("suspended", $suspended_client);
######### Suspended Account ##########

$smarty->display("suspended.tpl");
?>