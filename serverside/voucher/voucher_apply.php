<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();

$code = urldecode($_POST['code']);
$codes = $db->encryptor('decrypt',$code);
if(isset($_POST['submitted']))
{
	if(!isset($_POST['category']) && !isset($_POST['voucher']) || empty($_POST['voucher']) || empty($_POST['category'])){
		$db->HandleError('Sorry! Invalid Transaction!..');
	}else{
		$category = $db->encryptor('decrypt', $_POST['category']);
		$voucher = trim($_POST['voucher']);
		$voucher = $db->Sanitize($voucher);
		$action_voucher = $db->Sanitize($_POST['action_voucher']);
		$result_val = $db->sql_query("SELECT * FROM vouchers WHERE category='".$db->SanitizeForSQL($category)."' AND code_name='".$db->SanitizeForSQL($voucher)."'");
		$chk_val = $db->sql_numrows($result_val);
		$row_val = $db->sql_fetchrow($result_val);
		$code_name = $row_val['code_name'];
		$is_used = $row_val['is_used'];
		$user_id = $row_val['client_name'];
		$reseller_id = $row_val['reseller_name'];
		$dur = $db->calc_time($row_val['duration']);
		$duration = $dur['days'] . " day(s), " . $dur['hours'] . " hour(s) and " . $dur['minutes'] . " minutes";	
		if($user_id_2 == $codes)
		{
			if($action_voucher == "val_voucher")
			{
				if($chk_val == ''){
					$db->HandleError('This '. $voucher .' is not a '.$category.' voucher');	
				}else{
					if($is_used == 1) 
					{
						$db->HandleError("This ".$voucher." code is already used");
						echo '<div class="alert alert-danger">';
						echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>';
						echo '<table class="table table-bordered table-responsive">';				
						echo 	'<tr>
									<td>Voucher code:</td>
									<td>'.$code_name.'</td>
								</tr>';
						echo 	'<tr>
									<td>Duration:</td>
									<td>'.$duration.'</td>
								</tr>';

						echo 	'<tr>
									<td>Category:</td>
									<td>'.$category.'</td>
								</tr>';
						echo 	'<tr>
									<td>Status: </td>
									<td>Already Used</td>
								</tr>';
						echo 	'<tr>
									<td>User: </td>
									<td>'.$user_id.'</td>
								</tr>';
						echo 	'<tr>
									<td>Reseller: </td>
									<td>'.$reseller_id.'</td>
								</tr>';
						echo 	'<tr>
									<td>Date: </td>
									<td>'.date('F d, Y h:i:s A', strtotime($row_val['date_used'])).'</td>
								</tr>';
						echo '</table>';
						echo '</div>';							
					}else{					
						$db->HandleSuccess("This ".$voucher." is not used");
						echo '<div class="alert alert-success">';
						echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button>';
						echo '<table class="table table-bordered">';				
						echo 	'<tr>
									<td>Voucher code:</td>
									<td>'.$code_name.'</td>
								</tr>';
						echo 	'<tr>
									<td>Duration:</td>
									<td>'.$duration.'</td>
								</tr>';
						echo 	'<tr>
									<td>Category:</td>
									<td>'.$category.'</td>
								</tr>';
						echo 	'<tr>
									<td>Status: </td>
									<td>Valid</td>
								</tr>';
						echo 	'<tr>
									<td>Reseller: </td>
									<td>'.$reseller_id.'</td>
								</tr>';
						echo 	'<tr>
									<td>Generate Date: </td>
									<td>'.date('F d, Y h:i:s A', strtotime($row_val['gen_date'])).'</td>
								</tr>';
						echo '</table>';
						echo '</div>';
					}
				}
			} 
			else if($action_voucher == "apply_voucher") 
			{
				if($chk_val > 0)
				{
					if($row_val['is_used'] < 1) 
					{
						if($user_id_2 != 0) 
						{
							if($category == 'premium'){
								$row_duration = $db->sql_fetchrow($db->sql_query("SELECT code, duration FROM users WHERE user_id='".$db->SanitizeForSQL($user_id_2)."'"));
								$row_add_duration = ($row_duration['duration'] + $row_val['duration']);	
							}elseif($category == 'vip'){
								$row_duration = $db->sql_fetchrow($db->sql_query("SELECT code, vip_duration FROM users WHERE user_id='".$db->SanitizeForSQL($user_id_2)."'"));
								$row_add_duration = ($row_duration['vip_duration'] + $row_val['duration']);	
							}elseif($category == 'private'){
								$row_duration = $db->sql_fetchrow($db->sql_query("SELECT code, private_duration FROM users WHERE user_id='".$db->SanitizeForSQL($user_id_2)."'"));
								$row_add_duration = ($row_duration['private_duration'] + $row_val['duration']);	
							}
														
							if($user_level_2 != 'normal')
							{
								if($user_id_2 == $row_val['reseller_id'] || $upline_2 == 1 || $upline_2 == $row_val['reseller_id'])
								{
									if($category == 'premium'){
										$update = $db->sql_query("UPDATE users SET duration='$row_add_duration' WHERE user_id='".$db->SanitizeForSQL($user_id_2)."'");
									}elseif($category == 'vip'){
										$ss_id = md5($row_duration['code']);
										$ss_id = rand(0,65535);
										$update = $db->sql_query("UPDATE users SET ss_id='".$ss_id."', is_vip=1, vip_duration='$row_add_duration' WHERE user_id='".$db->SanitizeForSQL($user_id_2)."'");
									}elseif($category == 'private'){
										$update = $db->sql_query("UPDATE users SET is_private=1, private_duration='$row_add_duration' WHERE user_id='".$db->SanitizeForSQL($user_id_2)."'");
									}
									if($update)
									{
										$db->sql_query("INSERT INTO voucher_logs
										(code_name, user_id, client_name,
										 reseller_id, reseller_name, is_qty,
										 is_used, date_used, is_date, category)
										VALUES
										('".$db->SanitizeForSQL($voucher)."','".$db->SanitizeForSQL($user_id_2)."','".$db->SanitizeForSQL($user_name_2)."',
										 '".$row_val['reseller_id']."','".$row_val['reseller_name']."','".$row_val['is_qty']."',
										 1,'".date('Y-m-d H:i:s')."','".date('Y-m-d')."','".$db->SanitizeForSQL($category)."')
										");
										$db->sql_query("UPDATE vouchers SET 
										user_id='".$db->SanitizeForSQL($user_id_2)."', client_name='".$db->SanitizeForSQL($user_name_2)."',
										is_used=1, date_used='".date('Y-m-d H:i:s')."' 
										WHERE category='".$db->SanitizeForSQL($category)."' AND code_name='".$db->SanitizeForSQL($voucher)."'");
										$db->HandleSuccess('This '. $voucher .' is successfully applied!');
									}else{
										$db->HandleError('This '. $voucher .' is valid but it cannot be applied!');		
									}
								}
								else
								{
									$db->HandleError('This '. $voucher .' is valid but it cannot be applied!');				
								}
							}
							else if($user_level_2 == 'admin')
							{
								if($category == 'premium'){
									$update = $db->sql_query("UPDATE users SET duration='$row_add_duration' WHERE user_id='$user_id_2'");
								}elseif($category == 'vip'){
									$update = $db->sql_query("UPDATE users SET is_vip=1, vip_duration='$row_add_duration' WHERE user_id='$user_id_2'");
								}
								if($update)
								{
									$db->sql_query("INSERT INTO voucher_logs
									(code_name, user_id, client_name,
									 reseller_id, reseller_name, is_qty,
									 is_used, date_used, is_date, category)
									VALUES
									('".$db->SanitizeForSQL($voucher)."','".$db->SanitizeForSQL($user_id_2)."','".$db->SanitizeForSQL($user_name_2)."',
									 '".$row_val['reseller_id']."','".$row_val['reseller_name']."','".$row_val['is_qty']."',
									 1,'".date('Y-m-d H:i:s')."','".date('Y-m-d')."','".$db->SanitizeForSQL($category)."')
									");
									$db->sql_query("UPDATE vouchers SET 
									user_id='".$db->SanitizeForSQL($user_id_2)."', client_name='".$db->SanitizeForSQL($user_name_2)."',
									is_used=1, date_used='".date('Y-m-d H:i:s')."' 
									WHERE category='".$db->SanitizeForSQL($category)."' AND code_name='".$db->SanitizeForSQL($voucher)."'");	
									$db->HandleSuccess('This '. $voucher .' is successfully applied!');
								}else{
									$db->HandleError('This '. $voucher .' is valid but it cannot be applied!');		
								}
							}
							else if($user_level_2 == 'normal')
							{
								if($row_val['permission'] == 1)
								{
									if($upline_2 == 1)
									{
										if($category == 'premium'){
											$update = $db->sql_query("UPDATE users SET duration='$row_add_duration' WHERE user_id='".$db->SanitizeForSQL($user_id_2)."'");
										}elseif($category == 'vip'){
											$update = $db->sql_query("UPDATE users SET is_vip=1, vip_duration='$row_add_duration' WHERE user_id='".$db->SanitizeForSQL($user_id_2)."'");
										}
										if($update)
										{
											$db->sql_query("INSERT INTO voucher_logs
											(code_name, user_id, client_name,
											 reseller_id, reseller_name, is_qty,
											 is_used, date_used, is_date, category)
											VALUES
											('".$db->SanitizeForSQL($voucher)."','".$db->SanitizeForSQL($user_id_2)."','".$db->SanitizeForSQL($user_name_2)."',
											 '".$row_val['reseller_id']."','".$row_val['reseller_name']."','".$row_val['is_qty']."',
											 1,'".date('Y-m-d H:i:s')."','".date('Y-m-d')."','".$db->SanitizeForSQL($category)."')
											");
											$db->sql_query("UPDATE vouchers SET 
											user_id='".$db->SanitizeForSQL($user_id_2)."', client_name='".$db->SanitizeForSQL($user_name_2)."',
											is_used=1, date_used='".date('Y-m-d H:i:s')."' 
											WHERE category='".$db->SanitizeForSQL($category)."' AND code_name='".$db->SanitizeForSQL($voucher)."'");	
											$db->HandleSuccess('This '. $voucher .' is successfully applied!');
										}else{
											$db->HandleError('This '. $voucher .' is valid but it cannot be applied!');		
										}
									}else{
										$db->HandleError("Sorry! You don't have permission to applied this ".$voucher);
									}
								}
								else
								{
									$db->HandleError("Sorry! You don't have permission to applied this ".$voucher);			
								}
							}
							else
							{
								$db->HandleError('This '. $voucher .' is valid but it cannot be applied!');	
							}
						}else{
							$db->HandleError("Sorry! You don't have permission to applied this ".$voucher);
						}
					}else{
						$db->HandleError("This ". $voucher ." is already used ");
					}
				}else{
					$db->HandleError('this '. $voucher .' is not a '.$category.' voucher...');
				}
			}
		}else{
			$db->HandleError('Code is Invalid!');	
		}
	}
	echo $db->GetSuccessMessage();
	echo $db->GetErrorMessage();
}else{
	if(empty($_POST['code'])){
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}

	if(empty($_POST['voucher'])){
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}

	if(empty($_POST['action_voucher'])){
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}

	if($user_id_2 != $codes){
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}
}
?>