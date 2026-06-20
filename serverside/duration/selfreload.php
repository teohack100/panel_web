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

function programmitSelfReloadDurationText($seconds)
{
	$time = $GLOBALS['db']->calc_time((int)$seconds);
	return $time['days'] . ' dia(s), ' . $time['hours'] . ' hora(s) y ' . $time['minutes'] . ' minuto(s)';
}

if($user_level_2 == 'normal')
{
	echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
	exit;
}
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'subadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller'){
	$code = urldecode($_POST['code']);
	$code = $db->encryptor('decrypt',$code);
	if(isset($_POST['submitted']))
	{	
		if($credits_2 > 0)
		{
			if(!isset($_POST['qty']) && !isset($_POST['code'])
			|| empty($_POST['qty'])
			|| empty($_POST['code']))
			{

				$db->HandleError('No se pudo procesar la solicitud.');

			}else{
				$category = $db->encryptor('decrypt', $_POST['category']);
				$qty = $db->Sanitize($_POST['qty']);
				$get_dur = base64_decode($_POST['category']);
				$get_dur = urldecode($get_dur);
				$get_dur = $db->encryptor('decrypt',$get_dur);
				$durid = $db->Sanitize($get_dur);
				// duration
				$duration = '';
				if($category == 'premium')
				{	
					$duration = 36;
				}
				elseif($category == 'vip')
				{
					$duration = 34;

				}elseif($category == 'private')
				{
					$duration = 33;

				}else{
					echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
					$response = 0;
					exit;
				}
				$d_qry = $db->sql_query("SELECT * FROM duration WHERE id = '".$db->SanitizeForSQL($duration)."'");
				$d_row = $db->sql_fetchrow($d_qry);
				$d_time = $d_row['duration_time'];
				$d_name = $d_row['duration_name'];

				// users 
				$u_qry = $db->sql_query("SELECT user_name, is_groupname FROM users WHERE user_id = '".$code."'");
				$u_row = $db->sql_fetchrow($u_qry);
				$uid = $db->Sanitize($code);
				$uname = $u_row['user_name'];
				$is_group = $row['is_groupname'];

				$thirtydays = 2592000;
				$fiftenndays = $thirtydays / 2;
				$tendays = $thirtydays / 3;

				if($category == 'premium')
				{

					$status = 'Premium';

					if($d_time == $thirtydays)
					{

						$d_time = $d_time * $qty;//

					}else{

						$db->HandleError('Duracion invalida.');
						$response = 2;
					}

				}elseif($category == 'vip'){

					$status = 'VIP';

					if($d_time == $fiftenndays){

						$d_time = $d_time * $qty;

					}else{
						$db->HandleError('Duracion invalida.');
						$response = 2;
					}
				}elseif($category == 'private'){

					$status = 'Private';

					if($d_time == $tendays){

						$d_time = $d_time * $qty;

					}else{
						$db->HandleError('Duracion invalida.');
						$response = 2;
					}
				}else{
					echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
					$response = 0;
					exit;
				}
					
				$code1 = ran_code();
				$code2 = ran_code();
				$code3 = ran_code();
				$gen = $code2 . '-' . $code1 . '-' . $code3;
				$result = $db->sql_query("SELECT code_name FROM vouchers WHERE code_name='".$gen."'");
				$chk = $db->sql_fetchrow($result);
				if($chk != 1)
				{
					if($credits_2 == 0 && $user_id_2 != 1 && $user_level_2 != 'superadmin'
					|| $credits_2 < $qty && $user_id_2 != 1 && $user_level_2 != 'superadmin')
					{
						$db->HandleError('No tienes creditos suficientes.');
					}
					elseif($qty > 0)
					{
						if($user_id_2 == 1 || $user_level_2 == 'superadmin')
						{
							if($d_time > 0)
							{
								$insert = "insert into vouchers
								(code_name,
								 user_id,
								 client_name,
								 reseller_id,
								 reseller_name,
								 is_qty,
								 is_used,
								 duration,
								 gen_date,
								 date_used,
								 category)
								values
								('".$db->SanitizeForSQL($gen)."', 
								 '".$db->SanitizeForSQL($uid)."',
								 '".$db->SanitizeForSQL($uname)."',
								 '".$db->SanitizeForSQL($uid)."',
								 '".$db->SanitizeForSQL($uname)."',
								 '".$qty."',
								 1,
								 '".$db->SanitizeForSQL($d_time)."',
								 '".date('Y-m-d H:i:s')."',
								 '".date('Y-m-d H:i:s')."',
								 '".$category."')
								 ";
								if($db->sql_query($insert))
								{
									$db->sql_query("INSERT INTO voucher_logs
									(code_name, user_id, client_name,
									 reseller_id, reseller_name, is_qty,
									 is_used, date_used, is_date, category)
									VALUES
									('".$db->SanitizeForSQL($gen)."','".$db->SanitizeForSQL($uid)."','".$db->SanitizeForSQL($uname)."',
									'".$db->SanitizeForSQL($uid)."','".$db->SanitizeForSQL($uname)."','".$qty."',
									 1,'".date('Y-m-d H:i:s')."','".date('Y-m-d')."','".$db->SanitizeForSQL($category)."')
									");

									if($category == 'premium')
									{
										if($is_group == 'free')
										{
											$db->sql_query("UPDATE users SET is_groupname='normal', SET duration=duration+'".$d_time."' 
														WHERE user_id='".$db->SanitizeForSQL($uid)."'");
										}else{
											$db->sql_query("UPDATE users SET duration=duration+'".$d_time."' 
														WHERE user_id='".$db->SanitizeForSQL($uid)."'");
										}
								
									}elseif($category == 'vip')
									{
										$ss_id = md5($chkUsers['code']);
										$ss_id = rand(0,65535);
										if($is_group == 'free')
										{
											$db->sql_query("UPDATE users SET is_groupname='normal', ss_id='".$ss_id."', is_vip=1, vip_duration=vip_duration+'".$d_time."' 
															WHERE user_id='".$db->SanitizeForSQL($uid)."'");
										}else{
											$db->sql_query("UPDATE users SET ss_id='".$ss_id."', is_vip=1, vip_duration=vip_duration+'".$d_time."' 
															WHERE user_id='".$db->SanitizeForSQL($uid)."'");
										}
									}elseif($category == 'private')
									{
										$ss_id = md5($chkUsers['code']);
										$ss_id = rand(0,65535);
										if($is_group == 'free')
										{
											$db->sql_query("UPDATE users SET is_groupname='normal', ss_id='".$ss_id."', is_private=1, private_duration=private_duration+'".$d_time."' 
															WHERE user_id='".$db->SanitizeForSQL($uid)."'");
										}else{
											$db->sql_query("UPDATE users SET ss_id='".$ss_id."', is_private=1, private_duration=private_duration+'".$d_time."' 
															WHERE user_id='".$db->SanitizeForSQL($uid)."'");
										}
									}

									$db->sql_query("INSERT INTO duration_logs 
									(duration_id, duration_id2, duration_username, duration_qty, duration_item, duration_date, duration_type, ipaddress) 
									VALUES 
									('".$db->SanitizeForSQL($uid)."','".$db->SanitizeForSQL($uid)."','".$uname."','".$qty."','".$d_name."','".date('Y-m-d H:i:s')."', '".$category."','".$db->get_client_ip()."')");
									
									$dur_info = programmitSelfReloadDurationText($d_time);
									$db->HandleSuccess('Listo. ('.$qty.') '.$status.': '.$dur_info.' recargado a tu cuenta '.$uname.'. Codigo generado: '.$gen);
									
								}else{
									$db->HandleError('No se pudo recargar '.$status.' a tu cuenta.');
								}
							}else{
								$db->HandleError('Transaccion invalida.');
							}
						}
						else if($user_level_2 == 'administrator' || $user_level_2 == 'subadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller')
						{
							if($credits_2 == 0)
							{
								$db->HandleError('No tienes creditos suficientes.');
							}else
							if($credits_2 < 0)
							{
								$db->HandleError('No tienes creditos suficientes.');
							}else{
								if($d_time > 0)
								{
									$insert = "insert into vouchers
									(code_name,
									 user_id,
									 client_name,
									 reseller_id,
									 reseller_name,
									 is_qty,
									 is_used,
									 duration,
									 gen_date,
									 date_used,
									 category)
									values
									('".$db->SanitizeForSQL($gen)."', 
									 '".$db->SanitizeForSQL($uid)."',
									 '".$db->SanitizeForSQL($uname)."',
									 '".$db->SanitizeForSQL($uid)."',
									 '".$db->SanitizeForSQL($uname)."',
									 '".$qty."',
									 1,
									 '".$db->SanitizeForSQL($d_time)."',
									 '".date('Y-m-d H:i:s')."',
									 '".date('Y-m-d H:i:s')."',
									 '".$category."')
									 ";
									if($db->sql_query($insert))
									{
										$db->sql_query("INSERT INTO voucher_logs
										(code_name, user_id, client_name,
										 reseller_id, reseller_name, is_qty,
										 is_used, date_used, is_date, category)
										VALUES
										('".$db->SanitizeForSQL($gen)."','".$db->SanitizeForSQL($uid)."','".$db->SanitizeForSQL($uname)."',
										'".$db->SanitizeForSQL($uid)."','".$db->SanitizeForSQL($uname)."','".$qty."',
										 1,'".date('Y-m-d H:i:s')."','".date('Y-m-d')."','".$db->SanitizeForSQL($category)."')
										");
										
										if($category == 'premium'){
											if($is_group == 'free')
											{
												$db->sql_query("UPDATE users SET is_groupname='normal', duration = duration+'".$d_time."' 
												WHERE user_id='".$db->SanitizeForSQL($uid)."'");
											}else{
												$db->sql_query("UPDATE users SET duration = duration+'".$d_time."' 
												WHERE user_id='".$db->SanitizeForSQL($uid)."'");
											}
												$db->sql_query("UPDATE users SET credits = credits-'".$qty."' 
												WHERE user_id='".$db->SanitizeForSQL($uid)."'");

										}elseif($category == 'vip'){
											if($is_group == 'free')
											{
												$db->sql_query("UPDATE users SET is_groupname='normal', is_vip=1, vip_duration = vip_duration+'".$d_time."' 
												WHERE user_id='".$db->SanitizeForSQL($uid)."'");
											}else{
												$db->sql_query("UPDATE users SET is_vip=1, vip_duration = vip_duration+'".$d_time."' 
												WHERE user_id='".$db->SanitizeForSQL($uid)."'");
											}
												$db->sql_query("UPDATE users SET credits = credits-'".$qty."' 
												WHERE user_id='".$db->SanitizeForSQL($uid)."'");

										}elseif($category == 'private'){
											if($is_group == 'free')
											{
												$db->sql_query("UPDATE users SET is_groupname='normal', is_private=1, private_duration = private_duration+'".$d_time."' 
												WHERE user_id='".$db->SanitizeForSQL($uid)."'");
											}else{
												$db->sql_query("UPDATE users SET is_private=1, private_duration = private_duration+'".$d_time."' 
												WHERE user_id='".$db->SanitizeForSQL($uid)."'");
											}
												$db->sql_query("UPDATE users SET credits = credits-'".$qty."' 
												WHERE user_id='".$db->SanitizeForSQL($uid)."'");

										}

										$db->sql_query("INSERT INTO duration_logs 
										(duration_id, duration_id2, duration_username, duration_qty, duration_item, duration_date, duration_type, ipaddress) 
										VALUES 
										('".$db->SanitizeForSQL($uid)."','".$db->SanitizeForSQL($uid)."','".$uname."','1','".$d_name."','".date('Y-m-d H:i:s')."', '".$category."','".$db->get_client_ip()."')");
										
										$dur_info = programmitSelfReloadDurationText($d_time);
										$db->HandleSuccess('Listo. ('.$qty.') '.$status.': '.$dur_info.' recargado a tu cuenta '.$uname.'. Codigo generado: '.$gen);
									}else{
										$db->HandleError('No se pudo recargar '.$status.' a tu cuenta.');
									}
								}else{
									$db->HandleError('Transaccion invalida.');
								}
							}
						}else{
							$db->HandleError('No tienes creditos suficientes.');
						}
					}else{
						$db->HandleError('Cantidad invalida.');
					}
				}else{
					$db->HandleError('No se pudo generar el codigo.');
				}
			}
		}else{
			$db->HandleError('No tienes creditos suficientes.');
		}
		echo $db->GetSuccessMessage();
		echo $db->GetErrorMessage();
	}else{
		if(empty($_POST['selfdurations'])){
			echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
			exit;
		}
		if(empty($_POST['code'])){
			echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
			exit;
		}
		if(empty($_POST['qty'])){
			echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
			exit;
		}
		if($user_id_2 != $code){
			echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
			exit;
		}
	}
}else{
	echo '<script> alert("Invalid Transaction"); </script>';
	$db->RedirectToURL($db->base_url().'404');
	exit;
}

?>
