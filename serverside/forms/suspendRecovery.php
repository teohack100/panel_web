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
			if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
				$suspendRecovery = $db->sql_query("UPDATE users SET is_active=1, status='live' WHERE user_id='".$chk_id."'");
			}else{
				$suspendRecovery = $db->sql_query("UPDATE users SET is_active=1, status='live' WHERE user_id='".$chk_id."' AND upline='".$user_id_2."'");
			}
			if($suspendRecovery){
				$chk_suspend = $db->sql_query("SELECT logs_date, offense, client_username FROM suspension_logs WHERE client_id='".$chk_id."'");
				while($chk_row = $db->sql_fetchrow($chk_suspend))
				{
					$due_date = $chk_row['logs_date'];
					$offense = $chk_row['offense'];
				
					$chk_recovery = $db->sql_query("SELECT * FROM suspension_recovery_logs WHERE client_id='".$chk_id."'");
					if($db->sql_numrows($chk_recovery) > 0){
						$db->sql_query("UPDATE suspension_recovery_logs SET is_unsuspended=1, user_id='".$user_id_2."',suspend_date='".$due_date."',offense='".$offense."',logs_date=NOW()");
						$db->sql_query("UPDATE suspension_logs SET is_suspended=0 WHERE client_id='".$chk_id."'");
					}else{
						$db->sql_query("UPDATE suspension_logs SET is_suspended=0 WHERE client_id='".$chk_id."'");
						$db->sql_query("INSERT INTO suspension_recovery_logs 
						(is_unsuspended, client_id, client_username, user_id, username, suspend_date, offense, logs_date, ipaddress) 
						VALUES 
						(1,'".$chk_id."','".$chk_row['client_username']."','".$user_id_2."','".$user_name_2."','".$due_date."','".$offense."',NOW(), '".$_SERVER['REMOTE_ADDR']."') 
						");					
					}
				}
				$db->HandleSuccess('Successfully! Unsuspended Account');
			}else{
				$db->HandleError('Sorry! Unsuspended Account is Failed!');	
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