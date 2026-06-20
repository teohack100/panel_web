<?php

chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'subadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller'){
	
}else{
	header("Location: /dashboard");	
}

$requestData= $_REQUEST;
if(empty($requestData)){
	$db->RedirectToURL($db->base_url());
	exit;	
}

$columns = array( 
	0	=> 'user_id',
	1	=> 'user_name', 
	2	=> 'duration',
	3	=> 'credits',
	4	=> 'user_level',
	5	=> 'upline',
	6	=> null
);

$sql = "SELECT user_id, user_name, duration, credits, user_level, status, upline FROM users WHERE user_id!=1 AND is_active!=0 AND is_freeze!=1  AND status='live' ORDER BY user_name ASC";
$query = $db->sql_query($sql) or die();
$totalData = $db->sql_numrows($query);
$totalFiltered = $totalData;
if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
	$sql = "SELECT user_id, user_name, code, duration, private_duration, is_private, credits, user_level, status, upline FROM users WHERE 1=1 AND user_id!=1 AND is_active!=0 AND is_groupname!='bulk' AND is_groupname!='superadmin' AND is_groupname!='administrator' AND is_groupname!='subadmin' AND is_groupname!='reseller' AND is_groupname!='subreseller' AND is_vip!='1' AND is_freeze!=1 AND status='live' AND user_id!='".$user_id_2."' AND duration <= '0' AND private_duration <= '0'";
}else{
	$sql = "SELECT user_id, user_name, code, duration, private_duration, is_private, credits, user_level, status, upline FROM users WHERE 1=1 AND user_id!=1 AND is_active!=0 AND is_freeze!=1 AND status='live' AND is_groupname!='bulk' AND is_groupname!='superadmin' AND is_groupname!='administrator' AND is_groupname!='subadmin' AND is_groupname!='reseller' AND is_groupname!='subreseller' AND is_vip!='1' AND user_level!='superadmin' AND user_id!='".$user_id_2."' AND duration <= '0' AND private_duration <= '0' AND upline='".$user_id_2."' ";
}

if( !empty($requestData['search']['value']) ) { 
	$sql.=" AND ( user_id LIKE '%".$requestData['search']['value']."%' "; 
	$sql.=" OR user_name LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR duration LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR credits LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR user_level LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR upline LIKE '%".$requestData['search']['value']."%' ) ";
}

$query = $db->sql_query($sql) or die();
$totalFiltered = $db->sql_numrows($query);
$sql.="ORDER BY ". $columns[$requestData['order'][0]['column']]."  ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']." ";

$query = $db->sql_query($sql) or die();


$data = array();
while( $row = $db->sql_fetchrow($query) ) {
	$nestedData=array();
	$id = $row['user_id'];
	$code = $row['code'];
	$credits = $row['credits'];
	if($row['duration'] == 0 && $row['private_duration'] == 0 ){
		$user_name = '<font color="red"><strong>'.$row['user_name'].'</strong></font>';
	}else{
		$user_name = '<strong>'.$row['user_name'].'</strong>';
	}
		
	$dur = $db->calc_time($row['duration']);	
	if($row['duration'] == 0){
		$premuim_duration = "&nbsp;<strong>". $dur['days'] . "</strong> Day(s) | <strong>" . $dur['hours'] . "</strong> Hour(s) and <strong>" . $dur['minutes'] . "</strong> Minutes Left.";
	}elseif($row['duration'] < 3600){
		$premuim_duration = "&nbsp;<strong>". $dur['days'] . "</strong> Day(s) | <strong>" . $dur['hours'] . "</strong> Hour(s) and <strong>" . $dur['minutes'] . "</strong> Minutes Left.";	
	}else{
		$premuim_duration = "&nbsp;<strong>". $dur['days'] . "</strong> Day(s) | <strong>" . $dur['hours'] . "</strong> Hour(s) and <strong>" . $dur['minutes'] . "</strong> Minute(s)";
	}
	
	$durr = $db->calc_time($row['private_duration']);	
	if($row['private_duration'] == 0){
		$private_duration = "&nbsp;<strong>". $durr['days'] . "</strong> Day(s) | <strong>" . $durr['hours'] . "</strong> Hour(s) and <strong>" . $durr['minutes'] . "</strong> Minutes Left.";
	}elseif($row['private_duration'] < 3600){
		$private_duration = "&nbsp;<strong>". $durr['days'] . "</strong> Day(s) | <strong>" . $durr['hours'] . "</strong> Hour(s) and <strong>" . $durr['minutes'] . "</strong> Minutes Left.";	
	}else{
		$private_duration = "&nbsp;<strong>". $durr['days'] . "</strong> Day(s) | <strong>" . $durr['hours'] . "</strong> Hour(s) and <strong>" . $durr['minutes'] . "</strong> Minute(s)";
	}
	
	$dur4 = $premuim_duration;

	if($row['user_level'] == 'superadmin'){
		$user_level = '<span class="badge badge-info"><span class="fas fa-crown"></span> Super-Admin</span>';
		if($credits == 0)
		{
			$credits_label = '<span class="badge badge-danger"><span class="fas fa-coins"></span> Zero Credit/s</span>';
		}else{
			$credits_label = '<span class="badge badge-success"><span class="fas fa-coins"></span> '.$credits.' Credit/s</span>';
		}
	}
	else
	if($row['user_level'] == 'reseller'){
		$user_level = '<span class="badge badge-info"><span class="far fa-user-circle"></span> Reseller</span>';
		if($credits == 0)
		{
			$credits_label = '<span class="badge badge-danger"><span class="fas fa-coins"></span> Zero Credit/s</span>';
		}else{
			$credits_label = '<span class="badge badge-success"><span class="fas fa-coins"></span> '.$credits.' Credit/s</span>';
		}
	}
	else
	if($row['user_level'] == 'subreseller'){
		$user_level = '<span class="badge badge-info"><span class="fas fa-user-circle"></span> Sub-Reseller</span>';
		if($credits == 0)
		{
			$credits_label = '<span class="badge badge-danger"><span class="fas fa-coins"></span> Zero Credit/s</span>';
		}else{
			$credits_label = '<span class="badge badge-success"><span class="fas fa-coins"></span> '.$credits.' Credit/s</span>';
		}
	}else{
		$user_level = '<span class="badge badge-info"><span class="fas fa-user"></span> Normal</span>';
		$credits_label = '<span class="badge badge-secondary"><span class="fas fa-times"></span> Not Available</span>';
	}
	
	if($row['is_private'] == '1'){
	    $privdur = '<span class="badge badge-info"><span class="fas fa-clock"></span> '.$private_duration.'</span>';;
	}else{
	    $privdur = '';
	}
	
	$uplineName = get_upline_name_cached($row['upline']);
	
	$nestedData[] = '<div class="text-center"><input type="checkbox" name="chk[]" class="chk-box" value="'.$db->encryptor('encrypt',$id).'"></div>';
	$nestedData[] = $user_name;
	$nestedData[] = '<span class="badge badge-info"><span class="fas fa-clock"></span> '.$dur4.'</span><br>
	                '.$privdur.'';
	$nestedData[] = $user_level;
	$nestedData[] = '<span class="badge badge-info"><span class="fas fa-user-shield"></span> '.$uplineName.'</span>';

	if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller')
	{

		if($credits_2 < 1 &&  $user_id_2 != 1 && $user_level_2 != 'superadmin'){

			if($row['upline'] == $user_id_2)
			{
				$nestedData[] = '<div class="btn-group sidebar-social" role="group">
                                      <button type="button" class="btn btn-primary" onclick="view_info('.$id.','.$code.')"><a><i class="fas fa-user-tag" aria-hidden="true"></i><span>DETAILS</span></a></button>
                                      <button type="button" class="btn btn-success" onclick="edit_user('.$id.','.$code.')"><a><i class="fas fa-user-edit"></i></i><span>EDIT</span></a></button>
                                    </div>';
			}else
			{
				$nestedData[] = '<div class="btn-group sidebar-social" role="group">
                                      <button type="button" class="btn btn-primary" onclick="view_info('.$id.','.$code.')"><a><i class="fas fa-user-tag" aria-hidden="true"></i><span>DETAILS</span></a></button>
                                      <button type="button" class="btn btn-secondary" onclick="getVoucher('.$id.','.$code.')"><a><i class="fas fa-user-clock" aria-hidden="true"></i></i><span>DURATION</span></a></button>
                                    </div>';
			}
		}else{

			if($user_level == 'Member'){
				if($row['upline'] == $user_id_2 || $user_id_2 == 1 || $user_level_2 == 'superadmin')
				{
					$nestedData[] = '<div class="btn-group sidebar-social" role="group">
                                      <button type="button" class="btn btn-primary" onclick="view_info('.$id.','.$code.')"><a><i class="fas fa-user-tag" aria-hidden="true"></i><span>DETAILS</span></a></button>
                                      <button type="button" class="btn btn-success" onclick="edit_user('.$id.','.$code.')"><a><i class="fas fa-user-edit"></i></i><span>EDIT</span></a></button>
                                      <button type="button" class="btn btn-secondary" onclick="getVoucher('.$id.','.$code.')"><a><i class="fas fa-user-clock" aria-hidden="true"></i></i><span>DURATION</span></a></button>
                                      '.programmit_mduration_button($id, $code).'
                                    </div>';

				}else{
					$nestedData[] = '<div class="btn-group sidebar-social" role="group">
                                      <button type="button" class="btn btn-primary" onclick="view_info('.$id.','.$code.')"><a><i class="fas fa-user-tag" aria-hidden="true"></i><span>DETAILS</span></a></button>
                                      <button type="button" class="btn btn-secondary" onclick="getVoucher('.$id.','.$code.')"><a><i class="fas fa-user-clock" aria-hidden="true"></i></i><span>DURATION</span></a></button>
                                    </div>';

				}
			}else{
				if($row['upline'] == $user_id_2 || $user_id_2 == 1 || $user_level_2 == 'superadmin')
				{
					$nestedData[] = '<div class="btn-group sidebar-social" role="group">
                                      <button type="button" class="btn btn-primary" onclick="view_info('.$id.','.$code.')"><a><i class="fas fa-user-tag" aria-hidden="true"></i><span>DETAILS</span></a></button>
                                      <button type="button" class="btn btn-success" onclick="edit_user('.$id.','.$code.')"><a><i class="fas fa-user-edit"></i></i><span>EDIT</span></a></button>
                                      <button type="button" class="btn btn-secondary" onclick="getVoucher('.$id.','.$code.')"><a><i class="fas fa-user-clock" aria-hidden="true"></i></i><span>DURATION</span></a></button>
                                      '.programmit_mduration_button($id, $code).'
                                    </div>';
				}else{
					$nestedData[] = '<div class="btn-group sidebar-social" role="group">
					                    <button type="button" class="btn btn-secondary" onclick="getVoucher('.$id.','.$code.')"><a><i class="fas fa-user-clock" aria-hidden="true"></i></i><span>DURATION</span></a></button>
                                    </div>';

				}
			}
		}
	}else{
	//subreseller
		if($user_level_2 == 'subreseller')
		{
			if($credits_2 < 1){
				$nestedData[] = '<div class="btn-group sidebar-social" role="group">
                                      <button type="button" class="btn btn-primary" onclick="view_info('.$id.','.$code.')"><a><i class="fas fa-user-tag" aria-hidden="true"></i><span>DETAILS</span></a></button>
                                      <button type="button" class="btn btn-success" onclick="edit_user('.$id.','.$code.')"><a><i class="fas fa-user-edit"></i></i><span>EDIT</span></a></button>
                                    </div>';
			}else{

				$nestedData[] = '<div class="btn-group sidebar-social" role="group">
                                      <button type="button" class="btn btn-primary" onclick="view_info('.$id.','.$code.')"><a><i class="fas fa-user-tag" aria-hidden="true"></i><span>DETAILS</span></a></button>
                                      <button type="button" class="btn btn-success" onclick="edit_user('.$id.','.$code.')"><a><i class="fas fa-user-edit"></i></i><span>EDIT</span></a></button>
                                      <button type="button" class="btn btn-secondary" onclick="getVoucher('.$id.','.$code.')"><a><i class="fas fa-user-clock" aria-hidden="true"></i></i><span>DURATION</span></a></button>
                                      '.programmit_mduration_button($id, $code).'
                                    </div>';
			}
		}else{
			$nestedData[] = '<div class="btn-group sidebar-social" role="group">
                                      <button type="button" class="btn btn-primary" onclick="view_info('.$id.','.$code.')"><a><i class="fas fa-user-tag" aria-hidden="true"></i><span>DETAILS</span></a></button>
                                      <button type="button" class="btn btn-success" onclick="edit_user('.$id.','.$code.')"><a><i class="fas fa-user-edit"></i></i><span>EDIT</span></a></button>
                                    </div>';
		}
	}
	$data[] = $nestedData;	
}

$json_data = array(
			"draw"            => intval( $requestData['draw'] )? intval( $_REQUEST['draw'] ) : 0,
			"recordsTotal"    => intval( $totalData ),
			"recordsFiltered" => intval( $totalFiltered ),
			"data"            => ($data )
			);

echo json_encode($json_data);
?>