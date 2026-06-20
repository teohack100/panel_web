<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller'){
	
}else{
	header("Location: /myaccount");	
}
if(isset($_POST['submitted']))
{
	$chk = $_POST['chk'];
	$chkcount = count($chk);
	if(!isset($chk))
	{
    echo "<script>
			alert('You need to check the checkbox! At least one checkbox Must be Selected !!!');
		</script>";
	}else{
		for($i=0; $i<$chkcount; $i++)
		{			
			$chk_id = $chk[$i];
			$chk_id = $db->encryptor('decrypt',$chk_id);
			$qry = $db->sql_query("SELECT * FROM users_delete WHERE id ='".$chk_id."'");
			while($rst = $db->sql_fetchrow($qry))
			{
				$chk_user = $db->sql_query("SELECT * FROM users WHERE user_id ='".$rst['user_id']."'");
				if($db->sql_numrows($chk_user) > 0){
					$db->HandleError("Sorry! the account is already exists!...");
				}else{
					$thirtydays = time() + 2592000;
					
					$insert = $db->sql_query("INSERT INTO users
					(user_id,
						user_name,
						user_pass,
						auth_vpn,
						user_email,
						full_name,
						regdate,
						ipaddress,
						lastlogin,
						timestamp,
						code,
						reset_code,
						is_groupname,
						is_active,
						is_freeze,
						is_validated,
						is_connected,
						is_offense,
						is_ban,
						suspended_date,
						duration,
						vip_duration,
						is_vip,
						private_duration,
						is_private,
						private_slot,
						private_control,
						credits,
						upline,
						login_status,
						last_active_time,
						last_freeze_date,
						user_level,
						status)
						VALUES
						('".$rst['user_id']."',
						'".$rst['user_name']."',
						'".$rst['user_pass']."',
						'".$rst['auth_vpn']."',
						'".$rst['user_email']."',
						'".$rst['full_name']."',
						'".$rst['regdate']."',
						'".$rst['ipaddress']."',
						'".$rst['lastlogin']."',
						'".$thirtydays."',
						'".$rst['code']."',
						'".$rst['reset_code']."',
						'".$rst['is_groupname']."',
						'".$rst['is_active']."',
						'".$rst['is_freeze']."',
						'".$rst['is_validated']."',
						'".$rst['is_connected']."',
						'".$rst['is_offense']."',
						'".$rst['is_ban']."',
						'".$rst['suspended_date']."',
						'".$rst['duration']."',
						'".$rst['vip_duration']."',
						'".$rst['is_vip']."',
						'".$rst['private_duration']."',
						'".$rst['is_private']."',
						'".$rst['private_slot']."',
						'".$rst['private_control']."',
						'".$rst['credits']."',
						'".$rst['upline']."',
						'".$rst['login_status']."',
						'".$rst['last_active_time']."',
						'".$chk_rows['last_freeze_date']."',
						'".$rst['user_level']."',
						'".$rst['status']."')");				
					if($insert)
					{
						$db->sql_query("INSERT INTO users_profile (profile_id) VALUES ('".$rst['user_id']."')");
						$db->sql_query("DELETE FROM users_delete WHERE id ='".$chk_id."'");
						$db->HandleSuccess("".$rst['user_name']." recovered successfully");
					}else{
						$db->HandleError("".$rst['user_name']." recovery failed!");
					}
				}
			}
		}
		echo $db->GetSuccessMessage();
		echo $db->GetErrorMessage();
	}		
}
?>
