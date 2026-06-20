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

function programmitConversionDurationText($seconds)
{
	$time = $GLOBALS['db']->calc_time((int)$seconds);
	return $time['days'] . ' dia(s), ' . $time['hours'] . ' hora(s) y ' . $time['minutes'] . ' minuto(s)';
}

$category = $db->encryptor('decrypt', $_POST['category']);
$uid = $db->encryptor('decrypt',$_POST['secret']);
$u_dur = $db->encryptor('decrypt',$_POST['qcode']);
$u_vip = $db->encryptor('decrypt',$_POST['rcode']);
$u_priv = $db->encryptor('decrypt',$_POST['pcode']);
if(isset($_POST['submitted']))
{
	if(!isset($_POST['category']) && !isset($_POST['secret']) && !isset($_POST['qcode']) && !isset($_POST['rcode']) && !isset($_POST['pcode'])
	|| empty($_POST['category'])
	|| empty($_POST['secret'])
	|| empty($_POST['qcode'])
	|| empty($_POST['rcode'])
	|| empty($_POST['pcode'])){
		$db->HandleError('Conversion invalida.');

	}else{		

		if($user_id_2 == $uid)
		{
			if($category == 'premium')
			{
				$type = 'VIP y Private';
				$transfer = $db->sql_query("SELECT duration, vip_duration, private_duration FROM users WHERE user_id = '".$uid."' AND duration='".$u_dur."' AND vip_duration='".$u_vip."' AND private_duration='".$u_priv."'");
				$trans_row = $db->sql_fetchrow($transfer);
				$trans_dur = $trans_row['vip_duration'];
				$trans_dur2 = $trans_row['private_duration'];
				$trans_dur3 = $trans_dur2 * 3;
				$to_premium = ($trans_dur * 2) + $trans_dur3;
			}
			elseif($category == 'vip')
			{
				$type = 'Premium y Private';
				$transfer = $db->sql_query("SELECT duration, vip_duration, private_duration FROM users WHERE user_id = '".$uid."' AND duration='".$u_dur."' AND vip_duration='".$u_vip."' AND private_duration='".$u_priv."'");
				$trans_row = $db->sql_fetchrow($transfer);
				$trans_dur = $trans_row['duration'];
				$trans_dur2 = $trans_row['private_duration'];
				$trans_dur3 = $trans_dur2 * 3;
				$trans_dur4 = $trans_dur3 / 2;
				$to_vip = ($trans_dur / 2) + $trans_dur4;
			}
			elseif($category == 'private')
			{
				$type = 'Premium y VIP';
				$transfer = $db->sql_query("SELECT duration, vip_duration, private_duration FROM users WHERE user_id = '".$uid."' AND duration='".$u_dur."' AND vip_duration='".$u_vip."' AND private_duration='".$u_priv."'");
				$trans_row = $db->sql_fetchrow($transfer);
				$trans_dur = $trans_row['duration'];
				$trans_dur2 = $trans_row['vip_duration'];
				$trans_dur3 = ($trans_dur2 * 2) / 3;
				$to_private = ($trans_dur / 3) + $trans_dur3;
			}
		
			$vipdur1 = programmitConversionDurationText($to_vip);
					
			$premur1 = programmitConversionDurationText($to_premium);

			$privatedur1 = programmitConversionDurationText($to_private);

			if($category == 'premium')
			{
				if($trans_dur > 0 || $trans_dur2 > 0 )
				{
					$update = $db->sql_query("UPDATE users SET vip_duration=vip_duration-'".$trans_dur."', private_duration=private_duration-'".$trans_dur2."', 
					is_vip=0, is_private=0,  
					duration = duration+'".$to_premium."' WHERE user_id='".$uid."'");
					if($update){
						$db->sql_query("INSERT INTO conversion_logs
						(client_id, premium, vip, private, description, logs_date, ipaddress)
						VALUES
						('".$user_id_2."','".$premur1."','".$vipdur1."','".$privatedur1."','Convertido a Premium',NOW(),'".$db->get_client_ip()."')
						");
						$db->HandleSuccess('Listo. Se convirtio correctamente a Premium.');
					}	
				}elseif($to_premium > 0 )
				{
					$db->HandleSuccess('No hubo duraciones para convertir.');

				}else{
					$db->HandleError('No tienes duraciones suficientes de '.$type.'.');
				}
			}
			elseif($category == 'vip')
			{
				if($trans_dur > 0 || $trans_dur2 > 0 )
				{
					$update = $db->sql_query("UPDATE users SET duration = duration-'".$trans_dur."', private_duration=private_duration-'".$trans_dur2."', 
					is_vip=1, is_private=0, vip_duration = vip_duration+'".$to_vip."' WHERE user_id='".$uid."'");
					if($update){
						$db->sql_query("INSERT INTO conversion_logs
						(client_id, premium, vip, private, description, logs_date, ipaddress)
						VALUES
						('".$user_id_2."','".$premur1."','".$vipdur1."','".$privatedur1."','Convertido a VIP',NOW(),'".$db->get_client_ip()."')
						");
						$db->HandleSuccess('Listo. Se convirtio correctamente a VIP.');
					}
				}elseif($to_vip > 0 )
				{
					$db->HandleSuccess('No hubo duraciones para convertir.');

				}else{
					$db->HandleError('No tienes duraciones suficientes de '.$type.'.');
				}	
			}
			elseif($category == 'private')
			{
				if($trans_dur > 0 || $trans_dur2 > 0 )
				{
					$update = $db->sql_query("UPDATE users SET duration = duration-'".$trans_dur."', 
					is_vip=0, is_private=1, vip_duration = vip_duration-'".$trans_dur2."', private_duration = private_duration+'".$to_private."' WHERE user_id='".$uid."'");
					if($update){
						$db->sql_query("INSERT INTO conversion_logs
						(client_id, premium, vip, private, description, logs_date, ipaddress)
						VALUES
						('".$user_id_2."','".$premur1."','".$vipdur1."','".$privatedur1."','Convertido a Private',NOW(),'".$db->get_client_ip()."')
						");
						$db->HandleSuccess('Listo. Se convirtio correctamente a Private.');
					}
				}elseif($to_private > 0 )
				{
					$db->HandleSuccess('No hubo duraciones para convertir.');

				}else{
					$db->HandleError('No tienes duraciones suficientes de '.$type.'.');
				}	
			}else{
				$db->HandleError('Tipo de conversion invalido.');
			}
		}
	}
	echo $db->GetSuccessMessage();
	echo $db->GetErrorMessage();
}else{
	if(empty($_POST['secret'])){
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}
	
	if(empty($_POST['category'])){
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}
	
	if(empty($_POST['qcode'])){
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}
	
	if(empty($_POST['rcode'])){
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}

	if(empty($_POST['pcode'])){
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}
}
?>
