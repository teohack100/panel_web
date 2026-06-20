<?php
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller'){
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
	3	=> 'vip_duration',
	4	=> 'credits',
	5	=> 'lastlogin'
);
if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
	$chks = "duration<=432000 AND vip_duration<=432000 AND user_id!=1";
}else{
	$chks = "duration<=432000 AND vip_duration<=432000 AND upline='".$user_id_2."' AND user_id!=1";
}
$sql = "SELECT user_id, user_name, code, credits, regdate, duration, vip_duration, lastlogin FROM users WHERE " . $chks . " ORDER BY user_name ASC";
$query = $db->sql_query($sql) or die();
$totalData = $db->sql_numrows($query);
$totalFiltered = $totalData;

$sql = "SELECT user_id, user_name, code, credits, regdate, duration, vip_duration, lastlogin FROM users WHERE 1=1 AND ".$chks." ";
if( !empty($requestData['search']['value']) ) {
	$sql.=" AND ( user_name LIKE '%".$requestData['search']['value']."%' "; 
	$sql.=" OR duration LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR vip_duration LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR regdate LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR lastlogin LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR credits LIKE '%".$requestData['search']['value']."%' ) ";
}

$query =  $db->sql_query($sql) or die();
$totalFiltered = $db->sql_numrows($query);
$sql.="ORDER BY ". $columns[$requestData['order'][0]['column']]."  ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

$query =  $db->sql_query($sql) or die();
$data = array();
while( $row = $db->sql_fetchrow($query) ) {
	$nestedData=array(); 
	$id = $row['user_id'];
	$code = $row['code'];
	$regdate = $row['regdate'];
	$credits = $row['credits'];
	$duration = $row['duration'];
	$vip_duration = $row['vip_duration'];
	$username = $row['user_name'];
	$lastlogin = date("F d, Y H:i:s", strtotime($row['lastlogin']));
	$time = strtotime($row['lastlogin']);

	if($duration == 0 && $vip_duration == 0)
	{
		$user_name = '<font color="red">'.$username.'</font>';
	}else{
		$user_name = '<font color="green">'.$username.'</font>';
	}
	$premium_xy = $duration % 86400;
	$premium_yz = $premium_xy % 3600;
	$premium_days = ($duration - $premium_xy) / 86400; 
	$premium_hours = ($premium_xy - $premium_yz) / 3600; 

	$vip_xy = $vip_duration % 86400;
	$vip_yz = $vip_xy % 3600;
	$vip_days = ($vip_duration - $vip_xy) / 86400; 
	$vip_hours = ($vip_xy - $vip_yz) / 3600;
	
	$premium =	$premium_days.'day(s) and '.$premium_hours.' Hours left';
	$vip = $vip_days.'day(s) and '.$vip_days.' Hours left';
	
	$nestedData[] = '<input type="checkbox" name="chk[]" class="chk-box" value="'.$db->encryptor('encrypt',$id).'">';
	$nestedData[] = '<a style="text-decoration:none" href="javascript:void(0)" onclick="view_info('.$id.','.$code.')">'.$user_name.'</a>';
	$nestedData[] = $premium;
	$nestedData[] = $vip;
	$nestedData[] = $credits;
	$nestedData[] = $lastlogin;
	$nestedData[] = $db->time_elapsed_string($time);

	
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