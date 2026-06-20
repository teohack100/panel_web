<?php
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
	
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
	4	=> 'private_duration',
	5	=> 'credits',
	6	=> 'user_level'
);

$sql = "SELECT user_id, user_name, duration, vip_duration, private_duration, credits, user_level FROM users WHERE user_id!=1 AND is_active!=0 ORDER BY user_name ASC";
$query = $db->sql_query($sql) or die();
$totalData = $db->sql_numrows($query);
$totalFiltered = $totalData;

$sql = "SELECT user_id, user_name, duration, vip_duration, private_duration, credits, user_level FROM users WHERE 1=1 AND user_id!=1 AND is_active!=0 ";

if( !empty($requestData['search']['value']) ) { 
	$sql.=" AND ( user_id LIKE '%".$requestData['search']['value']."%' "; 
	$sql.=" OR user_name LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR duration LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR vip_duration LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR private_duration LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR credits LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR user_level LIKE '%".$requestData['search']['value']."%' ) ";
}

$query = $db->sql_query($sql) or die();
$totalFiltered = $db->sql_numrows($query);
$sql.="ORDER BY ". $columns[$requestData['order'][0]['column']]."  ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

$query = $db->sql_query($sql) or die();


$data = array();
while( $row = $db->sql_fetchrow($query) ) {
	$nestedData=array();
	$id = $row['user_id'];
	$credits = $row['credits'];
	if($row['duration'] == 0 && $row['vip_duration'] == 0 && $row['private_duration'] == 0){
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
		$private_duration = "<font color='red'>". $dur3['days'] . "</font> Day(s), <font color='red'>" . $dur3['hours'] . "</font> Hour(s) and <font color='red'>" . $dur2['minutes'] . "</font> Minutes Left.";
	}elseif($row['private_duration'] < 3600){
		$private_duration = "<font color='red'>". $dur3['days'] . "</font> Day(s), <font color='red'>" . $dur3['hours'] . "</font> Hour(s) and <font color='orange'>" . $dur2['minutes'] . "</font> Minutes Left.";	
	}else{
		$private_duration = "<font color='green'>". $dur3['days'] . "</font> Day(s), <font color='green'>" . $dur3['hours'] . "</font> Hour(s) and <font color='green'>" . $dur2['minutes'] . "</font> Minutes Left.";
	}
	
	if($row['user_level'] == 'superadmin'){
		$user_level = 'Administrator';
	}
	else
	if($row['user_level'] == 'subadmin'){
		$user_level = 'Sub Administrator';
	}
	else
	if($row['user_level'] == 'administrator'){
		$user_level = 'administrator';
	}
	else
	if($row['user_level'] == 'reseller'){
		$user_level = 'Reseller';
	}
	else
	if($row['user_level'] == 'subreseller'){
		$user_level = 'Sub Reseller';
	}else{
		$user_level = 'Member';
	}
	
	$nestedData[] = '<input type="checkbox" name="chk[]" class="chk-box" value="'.$db->encryptor('encrypt',$id).'">';
	$nestedData[] = $user_name;
	$nestedData[] = $premuim_duration;
	$nestedData[] = $vip_duration;
	$nestedData[] = $private_duration;
	$nestedData[] = $credits;
	$nestedData[] = $user_level;

	
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