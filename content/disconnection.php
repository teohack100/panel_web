<?php
ini_set('max_execution_time', 150);
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller'){
	
}else{
	header("Location: /myaccount");	
}


// admin
if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
	$admin_query = $db->sql_query("SELECT * FROM users WHERE duration<=432000 AND user_id!=1");	
}else{
	$admin_query = $db->sql_query("SELECT * FROM users WHERE duration<=432000 AND upline='".$user_id_2."' AND user_id!=1");	
}

while($admin_row = $db->sql_fetchrow($admin_query)){
	$regdate = $admin_row['regdate'];
	$credits = $admin_row['credits'];
	$duration = $admin_row['duration'];
	$username = $admin_row['user_name'];
	$lastlogin = date("F d, Y H:i:s", strtotime($admin_row['lastlogin']));
	$time = strtotime($admin_row['lastlogin']);
	$user = '';
	if($duration == 0){
		$user .= '<font color="red">'.$username.'</font>';
	}else{
		$user .= '<font color="green">'.$username.'</font>';
	}
	$premium_xy = $duration % 86400;
	$premium_yz = $premium_xy % 3600;
	$premium_days = ($duration - $premium_xy) / 86400; 
	$premium_hours = ($premium_xy - $premium_yz) / 3600; 

	$list_disconnect[]  = '<tr>';
	$list_disconnect[] .= '<td class="text-center"><input type="checkbox" name="chk[]" class="chk-box" value="'.$admin_row['user_id'].'"></td>';
	$list_disconnect[] .= '<td class="text-center">'.$user.'</td>';
	$list_disconnect[] .= '<td class="text-center">'.$premium_days.' Day(s) and '.$premium_hours.' Hours left</td>';
	$list_disconnect[] .= '<td class="text-center">'.$credits.'</td>';
	$list_disconnect[] .= '<td class="text-center">'.$lastlogin.'</td>';
	$list_disconnect[] .= '<td class="text-center">'.$db->time_elapsed_string($time).'</td>';
	$list_disconnect[] .= '</tr>';
	$smarty->assign("list_disconnect", $list_disconnect);
}
$smarty->display("disconnection.tpl");
?>