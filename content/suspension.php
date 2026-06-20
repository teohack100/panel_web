<?php
chkSession();
if($user_id_2 == 1 || $user_level_2 !='normal'){
	
}else{
	header("Location: /myaccount");	
}

######### Suspended Account ##########
if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
	$suspended_qry = $db->sql_query("SELECT user_id FROM users");
}else{
	$suspended_qry = $db->sql_query("SELECT user_id FROM users WHERE upline=$user_id_2");
}
while($suspended_row = $db->sql_fetchrow($suspended_qry)){

	$chk_suspended = $db->sql_query("SELECT * FROM suspension_logs WHERE is_suspended!=0 AND client_id='".$suspended_row['user_id']."'");
	while($chksuspended_row = $db->sql_fetchrow($chk_suspended)){
		$suspended_client = $db->sql_query("SELECT user_name FROM users WHERE user_id='".$chksuspended_row['client_id']."'");
		$suspended_client_row = $db->sql_fetchrow($suspended_client);
		
		$suspended_suspender = $db->sql_query("SELECT user_name FROM users WHERE user_id='".$chksuspended_row['user_id']."'");
		$suspended_suspender_suspender_row = $db->sql_fetchrow($suspended_suspender);	
		
		$suspended[] = '<tr>';
		$suspended[] .= '<td class="text-center">'.$suspended_client_row['user_name'].'</td>';
		$suspended[] .= '<td class="text-center">'.$suspended_suspender_suspender_row['user_name'].'</td>';
		$suspended[] .= '<td class="text-center">'.$chksuspended_row['offense'].'</td>';
		$suspended[] .= '<td class="text-center">'.date('F d, Y h:i:s A', strtotime($chksuspended_row['logs_date'])).'</td>';
		$suspended[] .= '</tr>';
		$smarty->assign("suspended", $suspended);
	}
	
	$chk_unsuspended = $db->sql_query("SELECT * FROM suspension_recovery_logs WHERE is_unsuspended!=0 AND client_id='".$suspended_row['user_id']."'");
	while($chkunsuspended_row = $db->sql_fetchrow($chk_unsuspended)){
		$unsuspended_client = $db->sql_query("SELECT user_name FROM users WHERE user_id='".$chkunsuspended_row['client_id']."'");
		$unsuspended_client_row = $db->sql_fetchrow($unsuspended_client);
		
		$unsuspended_suspender = $db->sql_query("SELECT user_name FROM users WHERE user_id='".$chkunsuspended_row['user_id']."'");
		$unsuspended_suspender_suspender_row = $db->sql_fetchrow($unsuspended_suspender);	
		
		$unsuspended[]  = '<tr>';
		$unsuspended[] .= '<td class="text-center">'.$unsuspended_client_row['user_name'].'</td>';
		$unsuspended[] .= '<td class="text-center">'.$unsuspended_suspender_suspender_row['user_name'].'</td>';
		$unsuspended[] .= '<td class="text-center">'.$chkunsuspended_row['offense'].'</td>';
		$unsuspended[] .= '<td class="text-center">'.date('F d, Y h:i:s A', strtotime($chkunsuspended_row['suspend_date'])).'</td>';
		$unsuspended[] .= '<td class="text-center">'.date('F d, Y h:i:s A', strtotime($chkunsuspended_row['logs_date'])).'</td>';
		$unsuspended[] .= '</tr>';
		$smarty->assign("unsuspended", $unsuspended);
	}
}


######### Suspended Account ##########

$smarty->display("suspension.tpl");
?>