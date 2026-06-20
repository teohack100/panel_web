<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
$root = __DIR__;
while (!is_file($root . '/includes/functions.php')) {
    $parent = dirname($root);
    if ($parent === $root) { break; }
    $root = $parent;
}
require $root . '/includes/functions.php';
chkSession();

function programmitLegacyDurationError($db, $message)
{
	$db->HandleError($message);
	echo $db->GetSuccessMessage();
	echo $db->GetErrorMessage();
	exit;
}
if($user_level_2 == 'normal')
{
	echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
	exit;
}

if(isset($_POST['submitted']))
{
	if(!isset($_POST['user_name']) && !isset($_POST['scode']) && !isset($_POST['duration']) || empty($_POST['user_name']) || empty($_POST['scode']) || empty($_POST['duration'])){
		$db->HandleError('No se pudo procesar la solicitud.');
	}else{				
		$category = $db->encryptor('decrypt', $_POST['category']);
				
		$get_dur = urldecode($_POST['duration']);
		$get_dur = $db->encryptor('decrypt',$get_dur);
		$durid = $db->Sanitize($get_dur);
		// duration
		$d_qry = $db->sql_query("SELECT * FROM duration WHERE id = '".$db->SanitizeForSQL($durid)."'");
		$d_row = $db->sql_fetchrow($d_qry);
		$d_time = $d_row['duration_time'];
		$d_name = $d_row['duration_name'];
		$d_label = function_exists('programmit_translate_duration_name') ? programmit_translate_duration_name($d_name) : $d_name;

		// users 
		$code = urldecode($_POST['scode']);
		$code = $db->encryptor('decrypt',$code);
		$uid = $db->Sanitize($code);
		$uname = $db->Sanitize($_POST['user_name']);
				
		if($category == 'premium'){
			$status = 'Premium';
		}elseif($category == 'vip'){
			$status = 'VIP';
		}else{
			$db->RedirectToURL($db->base_url());
			exit;
		}

		if($user_id_2 == 1 || $user_level_2 == 'superadmin')
		{
			if($d_time > 0)
			{
				$check = $db->sql_query("SELECT user_name FROM users 
				WHERE user_id='".$db->SanitizeForSQL($uid)."' AND 
				user_name='".$db->SanitizeForSQL($uname)."'");
				if($db->sql_numrows($check) > 0)
				{
					if($category == 'premium'){
						$update = $db->sql_query("UPDATE users SET duration=duration+'".$d_time."' 
						WHERE user_id='".$db->SanitizeForSQL($uid)."' AND user_name='".$db->SanitizeForSQL($uname)."'");
					}
					elseif($category == 'vip'){
						$update = $db->sql_query("UPDATE users SET vip_duration=vip_duration+'".$d_time."' 
						WHERE user_id='".$db->SanitizeForSQL($uid)."' AND user_name='".$db->SanitizeForSQL($uname)."'");
					}

					if($update)
					{
						$db->sql_query("INSERT INTO reloadduration_logs 
						(duration_id, duration_id2, duration_username, duration_item, duration_date, duration_type, ipaddress) 
						VALUES 
						('".$user_id_2."','".$db->SanitizeForSQL($uid)."','".$uname."','".$d_name."','".date('Y-m-d H:i:s')."', '".$category."','".$db->get_client_ip()."')");
						$db->HandleSuccess('Listo. Se recargo '.$d_label.' '.$status.' a '.$uname.'.');
					}else{
						$db->HandleError('No se pudo recargar la duracion '.$status.'.');
					}
				}else{
					$db->HandleError('No se pudo recargar la duracion '.$status.'.');
				}
			}else{
				$db->HandleError('Transaccion invalida para '.$status.'.');
			}
		}
		elseif($user_level_2 == 'administrator' || $user_level_2 == 'subadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller')
		{
			$check = $db->sql_query("SELECT user_name FROM users 
			WHERE user_id='".$db->SanitizeForSQL($uid)."' AND 
			user_name='".$db->SanitizeForSQL($uname)."'");
			if($db->sql_numrows($check) > 0)
			{
				if($category == 'premium')
				{
					if($duration_2 == 0)
					{
						$db->HandleError('No tienes saldo suficiente de duracion '.$status.'.');
					}
					else
					if($duration_2 < $d_time)
					{
						$db->HandleError('No tienes saldo suficiente de duracion '.$status.'.');
					}else{
						if($d_time > 0)
						{
							$update = $db->sql_query("UPDATE users SET duration=duration+'".$d_time."' 
							WHERE user_id='".$db->SanitizeForSQL($uid)."' AND 
							user_name='".$db->SanitizeForSQL($uname)."'");
						}
					}
				}
				elseif($category == 'vip')
				{
					if($vip_duration_2 == 0)
					{
						$db->HandleError('No tienes saldo suficiente de duracion '.$status.'.');
					}
					else
					if($vip_duration_2 < $d_time)
					{
						$db->HandleError('No tienes saldo suficiente de duracion '.$status.'.');
					}else{
						if($d_time > 0)
						{
							$update = $db->sql_query("UPDATE users SET vip_duration = vip_duration+'".$d_time."' 
							WHERE user_id='".$db->SanitizeForSQL($uid)."' AND 
							user_name='".$db->SanitizeForSQL($uname)."'");
						}
					}
				}else{
					$db->HandleError('Transaccion invalida para '.$status.'.');
				}
				if($update)
				{
					if($category == 'premium'){
						$db->sql_query("UPDATE users SET duration = duration - '".$d_time."' 
						WHERE user_id='".$user_id_2."'");
					}
					elseif($category == 'vip'){
						$db->sql_query("UPDATE users SET vip_duration = vip_duration - '".$d_time."'
						WHERE user_id='".$user_id_2."'");
					}
					$db->sql_query("INSERT INTO reloadduration_logs 
					(duration_id, duration_id2, duration_username, duration_item, duration_date, duration_type, ipaddress) 
					VALUES 
					('".$user_id_2."','".$db->SanitizeForSQL($uid)."','".$uname."','".$d_name."','".date('Y-m-d H:i:s')."', '".$category."','".$db->get_client_ip()."')");
					$db->HandleSuccess('Listo. Se recargo '.$d_label.' '.$status.' a '.$uname.'.');
				}else{
					$db->HandleError('No se pudo recargar la duracion '.$status.'.');
				}
			}else{
				$db->HandleError('No se pudo recargar la duracion '.$status.'.');
			}
		}
		else
		{
			$db->HandleError('Transaccion invalida para '.$status.'.');
		}
	}
	echo $db->GetSuccessMessage();
	echo $db->GetErrorMessage();
}else{
	if(empty($_POST['duration'])){
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;	
	}
	if(empty($_POST['scode'])){
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}
	if(empty($_POST['user_name'])){
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}
}
?>
