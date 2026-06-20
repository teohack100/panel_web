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
	$qry = $db->sql_query("SELECT * FROM users WHERE user_id!=1 AND user_id='".$db->SanitizeForSQL($uid)."' AND code='".$db->SanitizeForSQL($ucode)."' LIMIT 1");
	$row = $db->sql_fetchrow($qry);
	$values = array();	
	if($row){	
	    $v2_id = $row['uuid'];
	    
		$code = $db->encryptor('encrypt',$row['user_id']);
		$code = $db->encryptor('encrypt',$code);
		
		$user_level = $row['user_level'];

		if($user_level == 'normal'){
			$role = 1;
		}
		elseif($user_level == 'subreseller'){
			$role = 2;
		} 
		elseif($user_level == 'reseller'){
			$role = 3;
		}
		elseif($user_level == 'administrator'){
			$role = 4;
		}
		elseif($user_level == 'subadmin'){
			$role = 5;
		}
		elseif($user_level == 'superadmin'){
			$role = 99;
		}

		$is_vip = $row['is_vip'];
		$is_priv = $row['is_private'];
	
		if($is_vip == 1)
		{
			$client_type = "{$vip_encrypt}";
		}else{

			if($is_priv == 1)
			{
				$client_type = "{$private_encrypt}";
			}else
			{
				$client_type = "{$premium_encrypt}";
			}	
		}
		
		if($v2_id == ''){
		    $v2rayid = 'No V2Ray ID, Please generate and save.';
		}else{
		    $v2rayid = ''.$row['uuid'].'';
		}
		

		$values['role'] = $role;
		$user_pass = $db->decrypt_key($row['user_pass']);
		$user_pass = $db->encryptor('decrypt',$user_pass);
		$values['secret'] = $code;
		$values['user_name'] = $row['user_name'];
		$values['user_pass'] = $user_pass;
		$values['lenz'] = $user_pass;
		$values['full_name'] = $row['full_name'];
		$values['user_email'] = $row['user_email'];	
		$values['client_type'] = $client_type;
		$values['is_active'] = $row['is_active'];
		$values['upline'] = $row['upline'];	
		$values['v2ray_id'] = $v2rayid;
	}
	echo json_encode($values);	
}
?>