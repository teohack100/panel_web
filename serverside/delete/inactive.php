<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();

	if($user_level_2 == 'superadmin' || $user_id_2 == 1){
		$up = "is_groupname!='subadmin' AND is_groupname!='reseller' AND is_groupname!='subreseller' AND is_groupname!='administrator'";
	}else{
		$up = "is_groupname!='subadmin' AND is_groupname!='reseller' AND is_groupname!='subreseller' AND is_groupname!='administrator' AND upline='".$user_id_2."'";
	}
		$query = $db->sql_query("SELECT * FROM users WHERE user_id!=1 AND user_level!='superadmin' 
		        				AND  duration<1 AND vip_duration<1 AND private_duration<1 AND is_active=1 AND credits=0 AND is_groupname='free' AND status='live' AND user_level='normal' AND $up");
	while($rows = $db->sql_fetchrow($query))
	{
	    $chk_id = $rows['user_id'];
	    
		if($rows['duration'] > 0 || $rows['vip_duration'] > 0 || $rows['private_duration'] > 0 || $rows['credits'] > 0)
		{
			$db->HandleError('Sorry! You cannot Delete this Account...');
			
		}else{
		    
			$qrys = "SELECT profile_id, profile_fb, profile_image FROM users_profile WHERE profile_id ='".$chk_id."'";
			$rsts = $db->sql_query($qrys) or die ("Error in query: $qrys. " . $mysqli->error());
			
			while($row_results = $db->sql_fetchrow($rsts)){
				$ids = $row_results['profile_id'];
				$profile_fb = $row_results['profile_fb'];
				
				if($row_results['profile_image'] == ''){
					
				}else{
					$dirpath = '../profile/'.$ids.'/';
					$profile_image = $dirpath . $row_results['profile_image'];
					unlink($profile_image);			
				}
			}
			
			$user_query = $db->sql_query("DELETE FROM users WHERE user_id!=1 AND user_id='".$chk_id."'");
			$profile_query = $db->sql_query("DELETE FROM users_profile WHERE profile_id!=1 AND profile_id='".$chk_id."'");

			if($user_query && $profile_query)
			{
				$thirtydays = time() + 2592000;
				$query_2 = $db->sql_query("INSERT INTO users_delete
					(delete_timestamp,
					user_id,
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
					last_freeze_date,
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
					user_level,
					status)
					VALUES
					('".$thirtydays."',
					'".$rows['user_id']."',
					'".$rows['user_name']."',
					'".$rows['user_pass']."',
					'".$rows['auth_vpn']."',
					'".$rows['user_email']."',
					'".$rows['full_name']."',
					'".$rows['regdate']."',
					'".$rows['ipaddress']."',
					'".$rows['lastlogin']."',
					'".$rows['timestamp']."',
					'".$rows['code']."',
					'".$rows['reset_code']."',
					'".$rows['is_groupname']."',
					'".$rows['is_active']."',
					'".$rows['is_freeze']."',
					'".$rows['last_freeze_date']."',
					'".$rows['is_validated']."',
					'".$rows['is_connected']."',
					'".$rows['is_offense']."',
					'".$rows['is_ban']."',
					'".$rows['suspended_date']."',
					'".$rows['duration']."',
					'".$rows['vip_duration']."',
					'".$rows['is_vip']."',
					'".$rows['private_duration']."',
					'".$rows['is_private']."',
					'".$rows['private_slot']."',
					'".$rows['private_control']."',
					'".$rows['credits']."',
					'".$rows['upline']."',
					'".$rows['login_status']."',
					'".$rows['last_active_time']."',
					'".$rows['user_level']."',
					'".$rows['status']."')");	
				$results = 1;
			}else{
				$results = 0;
			}
		}
	}
			
	if($query_2)
	{
		$data['response'] = 1;
	}else{
		$data['response'] = 0;
	}
	echo json_encode($data);
?>
