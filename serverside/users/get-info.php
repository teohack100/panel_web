<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if(!isset($_GET['uid']) && !isset($_GET['ucode']) && empty($_GET['uid']) || empty($_GET['ucode'])){
	$db->RedirectToURL($db->base_url());
	exit;	
}else{
	$uid = $db->Sanitize($_GET['uid']);
	$ucode = $db->Sanitize($_GET['ucode']);
	$qry = $db->sql_query("SELECT ss_id, user_name, full_name, uuid, user_level, user_email, is_active, regdate, lastlogin, ipaddress, role_duration, duration, vip_duration, private_duration, user_pass 
	FROM users WHERE user_id!=1 AND user_id='".$db->SanitizeForSQL($uid)."' AND code='".$db->SanitizeForSQL($ucode)."' LIMIT 1");
	$row = $db->sql_fetchrow($qry);
	$values = array();	
	if($row){
	    $v2_id = $row['uuid'];
	    
		$dur = $db->calc_time($row['duration']);
        $dur2 = $db->calc_time($row['vip_duration']);
        $dur3 = $db->calc_time($row['private_duration']);
        $dur4 = $db->calc_time($row['role_duration']);

		$pdays = $dur['days'] . " days";
		$phours = $dur['hours'] . " hours";
		$pminutes = $dur['minutes'] . " minutes";
		$pseconds = $dur['seconds'] . " seconds";
		
		$vipdays = $dur2['days'] . " days";
		$viphours = $dur2['hours'] . " hours";
		$vipminutes = $dur2['minutes'] . " minutes";
		$vipseconds = $dur2['seconds'] . " seconds";
		
		$privdays = $dur3['days'] . " days";
		$privhours = $dur3['hours'] . " hours";
		$privminutes = $dur3['minutes'] . " minutes";
		$pprivseconds = $dur3['seconds'] . " seconds";
		
		$rdays = $dur4['days'] . " days";
		$rhours = $dur4['hours'] . " hours";
		$rminutes = $dur4['minutes'] . " minutes";
		$rseconds = $dur4['seconds'] . " seconds";
		
		if($row['duration'] == 0){
		    if($row['user_level'] == 'superadmin'){
		        $premuim_duration = "<font color='green'>Unlimited Duration</font>";
		    }else{
			    $premuim_duration = "<font color='red'>Not Active</font>";
		    }
		}else{
		    if($row['user_level'] == 'superadmin'){
		        $premuim_duration = "<font color='green'>Unlimited Duration</font>";
		    }else{
    			$premuim_duration = strtotime($pdays . $phours . $pminutes . $pseconds);
    			$premuim_duration = date('F d, Y h:i:s A', $premuim_duration);
		    }
		}
		
        if($row['vip_duration'] == 0){
            if($row['user_level'] == 'superadmin'){
		        $vipduration = "<font color='green'>Unlimited Duration</font>";
		    }else{
    			$vipduration = "<font color='red'>Not Active</font>";
		    }
		}else{
		    if($row['user_level'] == 'superadmin'){
		        $vipduration = "<font color='green'>Unlimited Duration</font>";
		    }else{
    			$vipduration = strtotime($vipdays . $viphours . $vipminutes . $vipseconds);
    			$vipduration = date('F d, Y h:i:s A', $vipduration);
		    }
		}
		
        if($row['private_duration'] == 0){
            if($row['user_level'] == 'superadmin'){
		        $privduration = "<font color='green'>Unlimited Duration</font>";
		    }else{
    			$privduration = "<font color='red'>Not Active</font>";
		    }
		}else{
		    if($row['user_level'] == 'superadmin'){
		        $privduration = "<font color='green'>Unlimited Duration</font>";
		    }else{
    			$privduration = strtotime($privdays . $privhours . $privminutes . $privseconds);
    			$privduration = date('F d, Y h:i:s A', $privduration);
		    }
		}
		
		if($row['role_duration'] == 0){
            if($row['user_level'] == 'superadmin'){
		        $rduration = strtotime($rdays . $rhours . $rminutes . $rseconds);
    			$rduration = date('F d, Y h:i:s A', $rduration);
		    }else{
    			$rduration = "<font color='red'>Not Active</font>";
		    }
		}else{
		    if($row['user_level'] == 'superadmin'){
		        $rduration = strtotime($rdays . $rhours . $rminutes . $rseconds);
    			$rduration = date('F d, Y h:i:s A', $rduration);
		    }else{
    			$rduration = strtotime($rdays . $rhours . $rminutes . $rseconds);
    			$rduration = date('F d, Y h:i:s A', $rduration);
		    }
		}
    
    
		if(empty($row['ss_id'])){
			$ssport = "<font color='red'>Not Active</font>";	
			$sspass = "<font color='red'>Not Active</font>";	
		}else{
			if($row['user_level'] == 'subadmin'){
		        if($user_id_2 == 1 || $user_level_2 == 'superadmin')
				{
		            $ssport = "<font color='green'>".$row['ss_id']."</font>";
					$sspass = "<font color='green'>".$row['user_name']."</font>";

		        }else{
		            $ssport = '<font color="darkblue">['.$legal_name.' Protected]</font>';
					$sspass = '<font color="darkblue">['.$legal_name.' Protected]</font>';
		        }
			}else{
					$ssport = "<font color='green'>".$row['ss_id']."</font>";
					$sspass = "<font color='green'>".$row['user_name']."</font>";
			}
		}
			

		$values['ssport'] = $ssport;
		$values['sspass'] = $sspass;
		
		$user_pass = $db->decrypt_key($row['user_pass']);
		$user_pass = $db->encryptor('decrypt',$user_pass);
		$values['username'] = $row['user_name'];
		
		if($user_id_2 == 1 || $user_level_2 == 'administrator'){
		    
		    $values['password'] = $user_pass;
		    
		}else if($row['user_level'] == 'superadmin' || $row['user_level'] == 'administrator' || $row['user_level'] == 'subadmin'){
		    
		    $values['password'] = '<font color="darkred">[ '.$legal_name.' Protected]</font>';
		    
		}else{
		    
		    $values['password'] = $user_pass;
		    
		}
		
		if($v2_id == ''){
		    $v2rayid = 'No V2Ray ID, Please generate and save.';
		}else{
		    $v2rayid = ''.$row['uuid'].'';
		}
		
		$values['fullname'] = $row['full_name'];
		$values['email'] = $row['user_email'];
		$values['status'] = $row['is_active'];
		$values['regdate'] = date('F d, Y h:i:s', strtotime($row['regdate']));
		$values['lastlogin'] = date('F d, Y h:i:s', strtotime($row['lastlogin']));
		$values['ipaddress'] = $row['ipaddress'];
		$values['premiumduration'] = $row['duration'];
		$values['vippduration'] = $row['vip_duration'];
		$values['v2ray_id'] = $v2rayid;
		$values['privateduration'] = $row['private_duration'];
		$values['premiumdate'] = $premuim_duration;
		$values['vipdate'] = $vipduration;
		$values['privatedate'] = $privduration;
		$values['roledate'] = $rduration;
	}
	echo json_encode($values);	
}
?>