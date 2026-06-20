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
	2	=> 'duration',
	3	=> 'credits',
	4	=> 'user_level',
	5	=> 'upline',
	6	=> null
);

$sql = "SELECT user_id, user_name, duration, vip_duration, private_duration, credits, user_level, status, upline FROM users WHERE user_id!=1 AND is_active!=0 AND is_freeze!=1  AND status='live' ORDER BY user_name ASC";
$query = $db->sql_query($sql) or die();
$totalData = $db->sql_numrows($query);
$totalFiltered = $totalData;
if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
	$sql = "SELECT user_id, user_name, code, duration, vip_duration, private_duration, credits, user_level, status, upline FROM users WHERE 1=1 AND user_id!=1 AND is_active!=0 AND is_freeze!=1 AND status='live' AND upline='".$user_id_2."'";
}elseif($user_level_2 == 'administrator'){
	$sql = "SELECT user_id, user_name, code, duration, vip_duration, private_duration, credits, user_level, status, upline FROM users WHERE 1=1 AND user_id!=1 AND is_active!=0 AND is_freeze!=1 AND status='live' AND upline='".$user_id_2."' AND user_level!='superadmin' AND user_id!='".$user_id_2."' ";
}elseif($user_level_2 == 'subadmin'){
	$sql = "SELECT user_id, user_name, code, duration, vip_duration, private_duration, credits, user_level, status, upline FROM users WHERE 1=1 AND user_id!=1 AND is_active!=0 AND is_freeze!=1 AND status='live' AND upline='".$user_id_2."' AND user_level!='superadmin' AND user_id!='".$user_id_2."' ";
}else{
	$sql = "SELECT user_id, user_name, code, duration, vip_duration, private_duration, credits, user_level, status, upline FROM users WHERE 1=1 AND user_id!=1 AND is_active!=0 AND is_freeze!=1 AND status='live' AND upline='".$user_id_2."' AND user_level!='superadmin' AND user_id!='".$user_id_2."' ";
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
	if($row['duration'] == 0 && $row['vip_duration'] == 0 && $row['private_duration'] == 0 && $row['credits'] == 0 ){
		$user_name = '<font color="red">'.$row['user_name'].'</font>';
	}else{
		$user_name = '<font color="green">'.$row['user_name'].'</font>';
	}
		
	$dur = $db->calc_time($row['duration']);	
	if($row['duration'] == 0){
		$premuim_duration = "<font color='red'>". $dur['days'] . "</font> Day(s), <font color='red'>" . $dur['hours'] . "</font> Hour(s) and <font color='red'>" . $dur['minutes'] . "</font> Minutes Left.";
	}elseif($row['duration'] < 3600){
		$premuim_duration = "<font color='red'>". $dur['days'] . "</font> Day(s), <font color='red'>" . $dur['hours'] . "</font> Hour(s) and <font color='orange'>" . $dur['minutes'] . "</font> Minutes Left.";	
	}else{
		$premuim_duration = "<font color='green'>". $dur['days'] . "</font> Day(s), <font color='green'>" . $dur['hours'] . "</font> Hour(s) and <font color='green'>" . $dur['minutes'] . "</font> Minutes Left.";
	}
	
	$dur2 = $db->calc_time($row['vip_duration']);
	if($row['vip_duration'] == 0){
		$vip_duration = "<font color='red'>". $dur2['days'] . "</font> Day(s), <font color='red'>" . $dur2['hours'] . "</font> Hour(s) and <font color='red'>" . $dur2['minutes'] . "</font> Minutes Left.";
	}elseif($row['vip_duration'] < 3600){
		$vip_duration = "<font color='red'>". $dur2['days'] . "</font> Day(s), <font color='red'>" . $dur2['hours'] . "</font> Hour(s) and <font color='orange'>" . $dur2['minutes'] . "</font> Minutes Left.";	
	}else{
		$vip_duration = "<font color='green'>". $dur2['days'] . "</font> Day(s), <font color='green'>" . $dur2['hours'] . "</font> Hour(s) and <font color='green'>" . $dur2['minutes'] . "</font> Minutes Left.";
	}

	$dur3 = $db->calc_time($row['private_duration']);
	if($row['private_duration'] == 0){
		$private_duration = "<font color='red'>". $dur3['days'] . "</font> Day(s), <font color='red'>" . $dur3['hours'] . "</font> Hour(s) and <font color='red'>" . $dur3['minutes'] . "</font> Minutes Left.";
	}elseif($row['private_duration'] < 3600){
		$private_duration = "<font color='red'>". $dur3['days'] . "</font> Day(s), <font color='red'>" . $dur3['hours'] . "</font> Hour(s) and <font color='orange'>" . $dur3['minutes'] . "</font> Minutes Left.";	
	}else{
		$private_duration = "<font color='green'>". $dur3['days'] . "</font> Day(s), <font color='green'>" . $dur3['hours'] . "</font> Hour(s) and <font color='green'>" . $dur3['minutes'] . "</font> Minutes Left.";
	}

	$dur4 = '<label class="label label-success">Premium: </label>'
			. $premuim_duration .'<br>'.'
			<label class="label label-success">VIP: </label>'
			. $vip_duration .'<br>'.'
			<label class="label label-success">Private: </label>' 
			. $private_duration;

	if($row['user_level'] == 'superadmin'){
		$user_level = 'Administrator';
		if($credits == 0)
		{
			$credits_label = '<font color="red">Zero Credits</font>';
		}else{
			$credits_label = '<font color="green">'.$credits.'</font>';
		}
	}
	else
	if($row['user_level'] == 'subadmin'){
		$user_level = '<font color="darkblue">Sub Administrator</font><br>
						<span class="glyphicon glyphicon-star"></span>
						<span class="glyphicon glyphicon-star"></span>
						<span class="glyphicon glyphicon-star"></span>';
		if($credits == 0)
		{
			$credits_label = '<font color="red">Zero Credits</font>';
		}else{
			$credits_label = '<font color="green">'.$credits.'</font>';
		}
	}
	else
	if($row['user_level'] == 'administrator'){
		$user_level = '<font color="darkblue">[Super Sub Administrator]</font><br>
						<span class="glyphicon glyphicon-star"></span>
						<span class="glyphicon glyphicon-star"></span>
						<span class="glyphicon glyphicon-globe"></span>
						<span class="glyphicon glyphicon-star"></span>
						<span class="glyphicon glyphicon-star"></span>';
		if($credits == 0)
		{
			$credits_label = '<font color="red">Zero Credits</font>';
		}else{
			$credits_label = '<font color="green">'.$credits.'</font>';
		}
	}
	else
	if($row['user_level'] == 'reseller'){
		$user_level = '<font color="darkblue">Reseller</font><br>
						<span class="glyphicon glyphicon-star"></span>';
		if($credits == 0)
		{
			$credits_label = '<font color="red">Zero Credits</font>';
		}else{
			$credits_label = '<font color="green">'.$credits.'</font>';
		}
	}
	else
	if($row['user_level'] == 'subreseller'){
		$user_level = '<font color="darkblue">Sub Reseller</font><br>
						<span class="glyphicon glyphicon-heart"></span>';
		if($credits == 0)
		{
			$credits_label = '<font color="red">Zero Credits</font>';
		}else{
			$credits_label = '<font color="green">'.$credits.'</font>';
		}
	}else{
		$user_level = '<font color="darkblue">Member</font><br>
						<span class="glyphicon glyphicon-user"></span>';
		$credits_label = '<font color="gray">Not Available</font>';
	}
	
	$uplineName = get_upline_name_cached($row['upline']);
	
	$nestedData[] = '<input type="checkbox" name="chk[]" class="chk-box" value="'.$db->encryptor('encrypt',$id).'">';
	$nestedData[] = $user_name;
	$nestedData[] = $dur4;
	$nestedData[] = $credits_label;
	$nestedData[] = $user_level;
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
									<i class="glyphicon glyphicon-pencil"></i></a>

									<a class="btn btn-block btn-info btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="Apply Premium or VIP"  onclick="getVoucher('.$id.','.$code.')">
									<i class="glyphicon glyphicon-barcode"></i></a>

									'.programmit_mduration_compact_button($id, $code);

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
									<i class="glyphicon glyphicon-share"></i></a>

									<a class="btn btn-block btn-info btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="Apply Premium or VIP"  onclick="getVoucher('.$id.','.$code.')">
									<i class="glyphicon glyphicon-barcode"></i></a>

									'.programmit_mduration_compact_button($id, $code);
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
								 <i class="glyphicon glyphicon-pencil"></i></a>

								 <a class="btn btn-block btn-info btn-sm"  href="javascript:void(0)" data-toggle="tooltip" title="Apply Premium or VIP"  onclick="getVoucher('.$id.','.$code.')">
								 <i class="glyphicon glyphicon-barcode"></i></a>

								 '.programmit_mduration_compact_button($id, $code);
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
