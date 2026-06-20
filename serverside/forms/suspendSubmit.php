<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
$valid = true;	
if(isset($_POST['submitted']))
{
	$chk = $_POST['chk'];
	$chkcount = count($chk);
		for($i=0; $i<$chkcount; $i++)
		{			
			$chk_id = $chk[$i];
			$chk_id = $db->encryptor('decrypt',$chk_id);
			$chk_user = $db->sql_query("SELECT is_offense, user_name FROM users WHERE user_id!=1 AND user_id='".$chk_id."'");
			$chk_row = $db->sql_fetchrow($chk_user);
			if($chk_row['is_offense'] == 1){
				$offense = '3 Days';
			}else
			if($chk_row['is_offense'] == 2){
				$offense = '7 Days';
			}else
			if($chk_row['is_offense'] > 2){
				$offense = 'Permanent Banned';
			}
			
			if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
				if($chk_row['is_offense'] > 2){
					$suspend_qry = $db->sql_query("UPDATE users SET is_active=0, is_ban=1, is_offense=is_offense+1, status='banned', suspended_date='".date('Y-m-d H:i:s')."' 
					WHERE user_id!=1 AND user_id='".$chk_id."'");
				}else{
					$suspend_qry = $db->sql_query("UPDATE users SET is_active=0, is_ban=0, is_offense=is_offense+1, status='suspended', suspended_date='".date('Y-m-d H:i:s')."' 
					WHERE user_id!=1 AND user_id='".$chk_id."'");	
				}
			}elseif($user_level_2 == 'subadmin'){
				if($chk_row['is_offense'] > 2){
					$suspend_qry = $db->sql_query("UPDATE users SET is_active=0, is_ban=1, is_offense=is_offense+1, status='banned', suspended_date='".date('Y-m-d H:i:s')."' 
					WHERE user_id!=1 AND user_level!='superadmin' AND user_level!='administrator' AND user_id!='".$user_id_2."' AND user_id='".$chk_id."'");
				}else{
					$suspend_qry = $db->sql_query("UPDATE users SET is_active=0, is_ban=0, is_offense=is_offense+1, status='suspended', suspended_date='".date('Y-m-d H:i:s')."' 
					WHERE user_id!=1 AND user_level!='superadmin' AND user_level!='administrator' AND user_id!='".$user_id_2."' AND user_id='".$chk_id."'");
				}
			}elseif($user_level_2 == 'administrator'){
				if($chk_row['is_offense'] > 2){
					$suspend_qry = $db->sql_query("UPDATE users SET is_active=0, is_ban=1, is_offense=is_offense+1, status='banned', suspended_date='".date('Y-m-d H:i:s')."' 
					WHERE user_id!=1 AND user_level!='superadmin' AND user_id!='".$user_id_2."' AND user_id='".$chk_id."'");
				}else{
					$suspend_qry = $db->sql_query("UPDATE users SET is_active=0, is_ban=0, is_offense=is_offense+1, status='suspended', suspended_date='".date('Y-m-d H:i:s')."' 
					WHERE user_id!=1 AND user_level!='superadmin' AND user_id!='".$user_id_2."' AND user_id='".$chk_id."'");
				}
			}else{
				if($chk_row['is_offense'] > 2){
					$suspend_qry = $db->sql_query("UPDATE users SET is_active=0, is_ban=1, is_offense=is_offense+1, status='banned', suspended_date='".date('Y-m-d H:i:s')."' 
					WHERE user_id!=1 AND user_id!='".$user_id_2."' AND user_id='".$chk_id."' AND upline='".$user_id_2."'");	
				}else{
					$suspend_qry = $db->sql_query("UPDATE users SET is_active=0, is_ban=0, is_offense=is_offense+1, status='suspended', suspended_date='".date('Y-m-d H:i:s')."' 
					WHERE user_id!=1 AND user_id!='".$user_id_2."' AND user_id='".$chk_id."' AND upline='".$user_id_2."'");	
				}
			}
			
			if($suspend_qry){
				$chk_suspend = $db->sql_query("SELECT * FROM suspension_logs WHERE client_id='".$chk_id."'");
				if($db->sql_numrows($chk_suspend) > 0){
					$db->sql_query("UPDATE suspension_logs SET is_suspended=1, user_id='".$user_id_2."',offense='".$offense."',logs_date=NOW() WHERE client_id='".$chk_id."'");
					$db->sql_query("UPDATE suspension_recovery_logs SET is_unsuspended=0 WHERE client_id='".$chk_id."'");
				}else{
					$db->sql_query("INSERT INTO suspension_logs 
					(is_suspended, client_id, client_username, user_id, username, offense, logs_date, ipaddress) 
					VALUES 
					(1, '".$chk_id."','".$chk_row['user_name']."','".$user_id_2."','".$user_name_2."','".$offense."',NOW(), '".$_SERVER['REMOTE_ADDR']."') 
					");					
				}
				$db->HandleSuccess('Successfully Suspended');
			}else{
				$db->HandleError('Sorry! Suspended Account is Failed!');
			}
		}
	echo $db->GetSuccessMessage();
	echo $db->GetErrorMessage();
}else{
	if(empty($_POST['chk']))
	{
		$db->RedirectToURL($db->base_url());
		exit;
	}
}
?>	