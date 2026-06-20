<?php

chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator'){
	
}else{
	header("Location: /myaccount");	
}

$requestData= $_REQUEST;
if(empty($requestData)){
	$db->RedirectToURL($db->base_url());
	exit;	
}

$columns = array( 
	0	=> 'user_id',
	1	=> 'user_name',
	2	=> 'credits',
	3	=> 'upline',
	4	=> null
);

$sql = "SELECT user_id, user_name, duration, vip_duration, private_duration, is_connected, credits, user_level, status, upline FROM users WHERE user_id!=1 AND is_active!=0 AND is_freeze!=1  AND status='live' ORDER BY user_name ASC";
$query = $db->sql_query($sql) or die();
$totalData = $db->sql_numrows($query);
$totalFiltered = $totalData;

if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
	$sql = "SELECT user_id, user_name, code, duration, vip_duration, private_duration, is_connected, credits, user_level, status, upline FROM users WHERE 1=1 AND user_id!=1 AND is_active!=0 AND is_freeze!=1 AND status='live' AND user_level='subadmin' ";
}
if($user_level_2 == 'administrator'){
	$sql = "SELECT user_id, user_name, code, duration, vip_duration, private_duration, is_connected, credits, user_level, status, upline FROM users WHERE 1=1 AND user_id!=1 AND is_active!=0 AND is_freeze!=1 AND status='live' AND user_level='subadmin' AND user_id!='".$user_id_2."' AND upline='".$user_id_2."' ";
}

if( !empty($requestData['search']['value']) ) { 
	$sql.=" AND ( user_id LIKE '%".$requestData['search']['value']."%' "; 
	$sql.=" OR user_name LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR credits LIKE '%".$requestData['search']['value']."%' ";
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
	
	if($row['credits'] == 0 ){
		$user_name = '<font color="red"><strong>'.$row['user_name'].'</strong></font>';
	}else{
		$user_name = '<strong>'.$row['user_name'].'</strong>';
	}
	
	if($row['user_level'] == 'superadmin'){
		if($credits == 0)
		{
			$credits_label = '<span class="badge badge-danger"><span class="fas fa-coins"></span> Zero Credit/s</span>';
		}else{
			$credits_label = '<span class="badge badge-success"><span class="fas fa-coins"></span> '.$credits.' Credit/s</span>';
		}
	}
	else
	if($row['user_level'] == 'subadmin'){
		if($credits == 0)
		{
			$credits_label = '<span class="badge badge-danger"><span class="fas fa-coins"></span> Zero Credit/s</span>';
		}else{
			$credits_label = '<span class="badge badge-success"><span class="fas fa-coins"></span> '.$credits.' Credit/s</span>';
		}
	}
	else
	if($row['user_level'] == 'administrator'){
		if($credits == 0)
		{
			$credits_label = '<span class="badge badge-danger"><span class="fas fa-coins"></span> Zero Credit/s</span>';
		}else{
			$credits_label = '<span class="badge badge-success"><span class="fas fa-coins"></span> '.$credits.' Credit/s</span>';
		}
	}
	else
	if($row['user_level'] == 'reseller'){
		if($credits == 0)
		{
			$credits_label = '<span class="badge badge-danger"><span class="fas fa-coins"></span> Zero Credit/s</span>';
		}else{
			$credits_label = '<span class="badge badge-success"><span class="fas fa-coins"></span> '.$credits.' Credit/s</span>';
		}
	}
	else
	if($row['user_level'] == 'subreseller'){
		if($credits == 0)
		{
			$credits_label = '<span class="badge badge-danger"><span class="fas fa-coins"></span> Zero Credit/s</span>';
		}else{
			$credits_label = '<span class="badge badge-success"><span class="fas fa-coins"></span> '.$credits.' Credit/s</span>';
		}
	}else{
		$credits_label = '<font color="gray">Not Available</font>';
	}
	
	if($row['is_connected'] == '1'){
	    $stat = '<span class="badge badge-success"><span class="fas fa-power-off"></span> Online</span>';
	    $stat2 = '<span class="fas fa-power-off" style="color: #1ecab8;"></span>&nbsp';
	}else{
	    $stat = '<span class="badge badge-danger"><span class="fas fa-power-off"></span> Offline</span>';
	    $stat2 = '<span class="fas fa-power-off" style="color: #f1646c;"></span>&nbsp';
	}
	
	$uplineName = get_upline_name_cached($row['upline']);
	$nestedData[] = '<input type="checkbox" name="chk[]" class="chk-box" value="'.$db->encryptor('encrypt',$id).'">';
	$nestedData[] = $stat2.$user_name;
	$nestedData[] = $stat;
	$nestedData[] = $credits_label;
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
                                      <button type="button" class="btn btn-warning" onclick="getCredits('.$id.','.$code.')"><a><i class="fas fa-coins"></i></i><span>CREDITS</span></a></button>
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