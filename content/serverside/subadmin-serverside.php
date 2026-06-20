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
	2   => 'is_connected',
	3	=> 'credits',
	4	=> 'upline',
	5	=> null
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
	
	if($row['duration'] == 0 && $row['vip_duration'] == 0 && $row['private_duration'] == 0 && $row['credits'] == 0 ){
		$user_name = '<font color="red">'.$row['user_name'].'</font>';
	}else{
		$user_name = '<font color="green">'.$row['user_name'].'</font>';
	}
	
	if($row['user_level'] == 'superadmin'){
		if($credits == 0)
		{
			$credits_label = '<font color="red">Zero Credits</font>';
		}else{
			$credits_label = '<font color="green">'.$credits.'</font>';
		}
	}
	else
	if($row['user_level'] == 'subadmin'){
		if($credits == 0)
		{
			$credits_label = '<font color="red">Zero Credits</font>';
		}else{
			$credits_label = '<font color="green">'.$credits.'</font>';
		}
	}
	else
	if($row['user_level'] == 'administrator'){
		if($credits == 0)
		{
			$credits_label = '<font color="red">Zero Credits</font>';
		}else{
			$credits_label = '<font color="green">'.$credits.'</font>';
		}
	}
	else
	if($row['user_level'] == 'reseller'){
		if($credits == 0)
		{
			$credits_label = '<font color="red">Zero Credits</font>';
		}else{
			$credits_label = '<font color="green">'.$credits.'</font>';
		}
	}
	else
	if($row['user_level'] == 'subreseller'){
		if($credits == 0)
		{
			$credits_label = '<font color="red">Zero Credits</font>';
		}else{
			$credits_label = '<font color="green">'.$credits.'</font>';
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
	$nestedData[] = $uplineName;

	if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller')
	{

		if($credits_2 < 1 &&  $user_id_2 != 1 && $user_level_2 != 'superadmin'){
			if($row['upline'] == $user_id_2)
			{
				$nestedData[] = '<a class="btn btn-block btn-info btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="View Account" onclick="view_info('.$id.','.$code.')">
								 <i class="glyphicon glyphicon-file"></i></a> 

								 <a class="btn btn-block btn-primary btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="Edit Account"  onclick="edit_user('.$id.','.$code.')">
								 <i class="glyphicon glyphicon-pencil"></i></a>';
			}else
			{
				$nestedData[] = '<a class="btn btn-block btn-info btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="View Account" onclick="view_info('.$id.','.$code.')">
								 <i class="glyphicon glyphicon-file"></i></a> 

								 <a class="btn btn-block btn-primary btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="Edit Account"  onclick="getVoucher('.$id.','.$code.')">
								 <i class="glyphicon glyphicon-lock"></i></a>';
			}
		}else{

			if($user_level == 'Member'){
				if($row['upline'] == $user_id_2 || $user_id_2 == 1 || $user_level_2 == 'superadmin')
				{
					$nestedData[] = '<a class="btn btn-block btn-info btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="View Account" onclick="view_info('.$id.','.$code.')">
									<i class="glyphicon glyphicon-file"></i></a> 

									<a class="btn btn-block btn-primary btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="Edit Account"  onclick="edit_user('.$id.','.$code.')">
									<i class="glyphicon glyphicon-pencil"></i></a>';

				}elseif($user_level_2 == 'administrator')
				{
					$nestedData[] = '<a class="btn btn-block btn-info btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="View Account" onclick="view_info('.$id.','.$code.')">
									<i class="glyphicon glyphicon-file"></i></a> 

									<a class="btn btn-block btn-primary btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="Edit Account"  onclick="edit_user('.$id.','.$code.')">
									<i class="glyphicon glyphicon-pencil"></i></a>';

				}else{
					$nestedData[] = '<a class="btn btn-block btn-info btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="View Account" onclick="view_info('.$id.','.$code.')">
									<i class="glyphicon glyphicon-file"></i></a> 

									<a class="btn btn-block btn-primary btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="Edit Account"  onclick="getVoucher('.$id.','.$code.')">
									<i class="glyphicon glyphicon-lock"></i></a>';

				}
			}else{
				if($row['upline'] == $user_id_2 || $user_id_2 == 1 || $user_level_2 == 'superadmin')
				{
					$nestedData[] = '<a class="btn btn-block btn-info btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="View Account" onclick="view_info('.$id.','.$code.')">
									<i class="glyphicon glyphicon-file"></i></a> 

									<a class="btn btn-block btn-primary btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="Edit Account"  onclick="edit_user('.$id.','.$code.')">
									<i class="glyphicon glyphicon-pencil"></i></a>

									<a class="btn btn-block btn-warning btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="Reload Credits"  onclick="getCredits('.$id.','.$code.')">
									<i class="glyphicon glyphicon-share"></i></a>';
				}elseif($user_level_2 == 'administrator')
				{
					$nestedData[] = '<a class="btn btn-block btn-info btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="View Account" onclick="view_info('.$id.','.$code.')">
									<i class="glyphicon glyphicon-file"></i></a> 

									<a class="btn btn-block btn-primary btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="Edit Account"  onclick="edit_user('.$id.','.$code.')">
									<i class="glyphicon glyphicon-pencil"></i></a>
									
									<a class="btn btn-block btn-warning btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="Reload Credits"  onclick="getCredits('.$id.','.$code.')">
									<i class="glyphicon glyphicon-share"></i></a>';
				
				}else{
					$nestedData[] = '<a class="btn btn-block btn-info btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="View Account" onclick="view_info('.$id.','.$code.')">
									<i class="glyphicon glyphicon-file"></i></a> 

									<a class="btn btn-block btn-primary btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="Edit Account"  onclick="getVoucher('.$id.','.$code.')">
									<i class="glyphicon glyphicon-lock"></i></a>';

				}
			}
		}
	}else{
	//subreseller
		if($user_level_2 == 'subreseller')
		{
			if($credits_2 < 1){
				$nestedData[] = '<a class="btn btn-block btn-info btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="View Account" onclick="view_info('.$id.','.$code.')">
								 <i class="glyphicon glyphicon-file"></i></a> 

								 <a class="btn btn-block btn-primary btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="Edit Account"  onclick="edit_user('.$id.','.$code.')">
								 <i class="glyphicon glyphicon-pencil"></i></a>';
			}else{

				$nestedData[] = '<a class="btn btn-block btn-info btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="View Account" onclick="view_info('.$id.','.$code.')">
								 <i class="glyphicon glyphicon-file"></i></a> 

 								 <a class="btn btn-block btn-primary btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="Edit Account"  onclick="edit_user('.$id.','.$code.')">
								 <i class="glyphicon glyphicon-pencil"></i></a>';
			}
		}else{
			$nestedData[] = '<a class="btn btn-block btn-info btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="View Account">Hacker Me!!</a> 

							<a class="btn btn-block btn-primary btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="Edit Account">Hacker Me!!</a>';
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