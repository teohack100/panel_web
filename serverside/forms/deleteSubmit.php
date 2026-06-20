<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if(isset($_POST['submitted']))
{
	$chk = $_POST['chk'];
	$chkcount = count($chk);
	
	if(!isset($chk))
	{
        echo "<script> alert('You need to check the checkbox! At least one checkbox Must be Selected !!!'); </script>";
		$db->RedirectToURL($db->base_url());
		exit;
	}
	else
	{
		for($i=0; $i<$chkcount; $i++)
		{			
			$chk_id = $chk[$i];
			$chk_id = $db->encryptor('decrypt',$chk_id);
			$chk_qry = $db->sql_query("SELECT * FROM users WHERE user_id!=1 AND user_id='".$chk_id."'");
			while($chk_rows = $db->sql_fetchrow($chk_qry))
			{
				if($chk_rows['credits'] > 0)
				{
					echo "<script> 
					        swal({
					        type: 'error',
					        title: 'FAILED',
                            html: 'You cannot delete accounts with remaining credits!',
                            showConfirmButton: false,
                            customClass: 'animated bounceIn swal2-popup',
                            animation: false,
                            timer: 3000
					        }).then(function() 
					        { location.reload(); 
					        });  
					       </script>";
				}else
				if(($chk_rows['duration'] > 0 || $chk_rows['vip_duration'] > 0 || $chk_rows['private_duration']) && $chk_rows['status'] != 'freeze')
				{
					echo "<script> 
					        swal({
					        type: 'error',
					        title: 'FAILED',
                            html: 'You cannot delete accounts with remaining duration, just use the freeze instead!',
                            showConfirmButton: false,
                            customClass: 'animated bounceIn swal2-popup',
                            animation: false,
                            timer: 3000
					        }).then(function() 
					        { location.reload(); 
					        });  
					       </script>";
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
					if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator'){
						$user_query = $db->sql_query("DELETE FROM users WHERE user_id!=1 AND user_id='".$chk_id."'");
					}elseif($user_level_2 == 'subadmin'){
						$user_query = $db->sql_query("DELETE FROM users WHERE user_id!=1 AND user_level!='superadmin' AND user_level!='administrator' AND  user_id!='".$user_id_2."' AND user_id='".$chk_id."'");
					}else{
						$user_query = $db->sql_query("DELETE FROM users WHERE user_id!=1 AND user_id!='".$user_id_2."' AND user_id='".$chk_id."' AND upline='".$user_id_2."'");
					}
					$profile_query = $db->sql_query("DELETE FROM users_profile WHERE profile_id!=1 AND profile_id='".$chk_id."'");

					if($user_query && $profile_query)
					{
						$thirtydays = 1296000;
						$db->sql_query("INSERT INTO users_delete
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
						'".$chk_rows['user_id']."',
						'".$chk_rows['user_name']."',
						'".$chk_rows['user_pass']."',
						'".$chk_rows['auth_vpn']."',
						'".$chk_rows['user_email']."',
						'".$chk_rows['full_name']."',
						'".$chk_rows['regdate']."',
						'".$chk_rows['ipaddress']."',
						'".$chk_rows['lastlogin']."',
						'".$chk_rows['timestamp']."',
						'".$chk_rows['code']."',
						'".$chk_rows['reset_code']."',
						'".$chk_rows['is_groupname']."',
						'".$chk_rows['is_active']."',
						'".$chk_rows['is_freeze']."',
						'".$chk_rows['last_freeze_date']."',
						'".$chk_rows['is_validated']."',
						'".$chk_rows['is_connected']."',
						'".$chk_rows['is_offense']."',
						'".$chk_rows['is_ban']."',
						'".$chk_rows['suspended_date']."',
						'".$chk_rows['duration']."',
						'".$chk_rows['vip_duration']."',
						'".$chk_rows['is_vip']."',
						'".$chk_rows['private_duration']."',
						'".$chk_rows['is_private']."',
						'".$chk_rows['private_slot']."',
						'".$chk_rows['private_control']."',
						'".$chk_rows['credits']."',
						'".$chk_rows['upline']."',
						'".$chk_rows['login_status']."',
						'".$chk_rows['last_active_time']."',
						'".$chk_rows['user_level']."',
						'".$chk_rows['status']."')");	
						echo "<script> 
					        swal({
					        type: 'success',
					        title: 'SUCCESS',
                            html: 'Account Deleted Successfully!',
                            showConfirmButton: false,
                            customClass: 'animated bounceIn swal2-popup',
                            animation: false,
                            timer: 3000
					        }).then(function() 
					        { location.reload(); 
					        });  
					       </script>";
					}else{
						echo "<script> 
					        swal({
					        type: 'error',
					        title: 'FAILED',
                            html: 'Deleting account failed!',
                            showConfirmButton: false,
                            customClass: 'animated bounceIn swal2-popup',
                            animation: false,
                            timer: 3000
					        }).then(function() 
					        { location.reload(); 
					        });  
					       </script>";
					}
				}
			}
		}		
	}
		echo $db->GetSuccessMessage();
		echo $db->GetErrorMessage();
}else{
	$db->RedirectToURL($db->base_url());
	exit;
}
?>
