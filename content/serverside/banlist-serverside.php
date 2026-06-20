<?php
ini_set('max_execution_time', 150);
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator'){
}else{
	$db->RedirectToURL($db->base_url());
	exit;
}

$requestData= $_REQUEST;
if(empty($requestData)){
	$db->RedirectToURL($db->base_url());
	exit;	
}
$columns = array( 
	0	=> 'user_id',
	1	=> 'user_name', 
	3	=> 'duration',
	4	=> 'vip_duration',
	5	=> 'credits',
	6	=> 'suspended_date'
);


$chks = "is_ban=0 AND is_offense>2";

$sql = "SELECT * FROM users WHERE " . $chks ." ORDER BY user_name ASC";
$query = $db->sql_query($sql) or die();
$totalData = $db->sql_numrows($query);
$totalFiltered = $totalData;

$sql = "SELECT * FROM users WHERE 1=1 AND ".$chks." ";
if( !empty($requestData['search']['value']) ) {
	$sql.=" AND ( user_id LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR user_name LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR duration LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR vip_duration LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR credits LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR suspended_date LIKE '%".$requestData['search']['value']."%' ) ";
}

$query = $db->sql_query($sql) or die();
$totalFiltered = $db->sql_numrows($query);
$sql.="ORDER BY ". $columns[$requestData['order'][0]['column']]."  ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
$query = $db->sql_query($sql) or die();


$data = array();

while($row = $db->sql_fetchrow($query))
{
	$nestedData=array(); 
	$username = $row['user_name'];
	$credits = $row['credits'];
	$premium_duration = $row['duration'];
	$vip_duration = $row['vip_duration'];
	$suspended_date = date("F d, Y H:i:s", strtotime($row['suspended_date']));
	$time = strtotime($row['suspended_date']);

	$premium_xy = $premium_duration % 86400;
	$premium_yz = $premium_xy % 3600;
	$premium_days = ($premium_duration - $premium_xy) / 86400; 
	$premium_hours = ($premium_xy - $premium_yz) / 3600; 

	$vip_xy = $vip_duration % 86400;
	$vip_yz = $vip_xy % 3600;
	$vip_days = ($vip_duration - $vip_xy) / 86400; 
	$vip_hours = ($vip_xy - $vip_yz) / 3600; 
	
	if($premium_duration == 0 || $vip_duration == 0){
		$user = '<font color="red">'.$username.'</font>';
		$premium_days = '<font color="red">'.$premium_days.'</font>';
		$premium_hours = '<font color="red">'.$premium_hours.'</font>';
		$vip_days = '<font color="red">'.$vip_days.'</font>';
		$vip_hours = '<font color="red">'.$vip_hours.'</font>';
	}else{
		$premium_days = '<font color="green">'.$premium_days.'</font>';
		$premium_hours = '<font color="green">'.$premium_hours.'</font>';
		$vip_days = '<font color="green">'.$vip_days.'</font>';
		$vip_hours = '<font color="green">'.$vip_hours.'</font>';
		$user = '<font color="green">'.$username.'</font>';
	}

	$premium = $premium_days.'day(s) and '.$premium_hours.' Hours left';
	$vip = $vip_days.'day(s) and '.$vip_hours.' Hours left';
	$nestedData[] = '<input type="checkbox" name="chk[]" class="chk-box" value="'.$db->encryptor('encrypt', $row['user_id']).'">';
	$nestedData[] = $user;
	$nestedData[] = $premium;
	$nestedData[] = $vip;
	$nestedData[] = $credits;
	$nestedData[] = $suspended_date;
	$nestedData[] = $db->time_elapsed_string($time);

	$data[] = $nestedData;		
}

$json_data = array(
			"draw"            => intval( $requestData['draw'] ) ? intval( $requestData['draw'] ) : 0,
			"recordsTotal"    => intval( $totalData ),
			"recordsFiltered" => intval( $totalFiltered ),
			"data"            => ($data)
			);

echo json_encode($json_data);

?>