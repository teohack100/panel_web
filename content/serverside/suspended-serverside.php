<?php
chkSession();
if($user_level_2 == 'normal'){
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
	2	=> 'is_offense',
	3	=> 'suspended_date',
	4	=> null
);

if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
	$chks = "user_id!=1 AND is_offense<=2 AND is_active=0 AND status='suspended'";	
}
else
{
	$chks = "user_id!=1 AND upline='".$user_id_2."' AND is_offense>0 AND is_active=0 AND status='suspended'";		
}

$sql = "SELECT user_id, user_name, duration FROM users WHERE " . $chks . " ORDER BY user_name ASC";
$query = $db->sql_query($sql) or die();
$totalData = $db->sql_numrows($query);
$totalFiltered = $totalData;

$sql = "SELECT user_id, user_name, duration, is_offense, suspended_date FROM users WHERE 1=1 AND ".$chks." ";
if( !empty($requestData['search']['value']) ) {
	$sql.=" AND ( user_name LIKE '%".$requestData['search']['value']."%' AND " .$chks;
	$sql.=" OR is_offense LIKE '%".$requestData['search']['value']."%' AND " .$chks;
	$sql.=" OR suspended_date LIKE '%".$requestData['search']['value']."%' AND " .$chks." ) ";
}

$query = $db->sql_query($sql) or die();
$totalFiltered = $db->sql_numrows($query);

$sql.="ORDER BY ". $columns[$requestData['order'][0]['column']]."  ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

$query = $db->sql_query($sql) or die();


$data = array();
while( $row = $db->sql_fetchrow($query) ) {
	$nestedData=array(); 
	$id = $row['user_id'];
	$user_name = $row['user_name'];
	$duration = $row['duration'];
	$suspended_date = strtotime($row['suspended_date']);
	$date = date('F d, Y h:i A', $suspended_date);
	$elapse = $db->time_elapsed_string($suspended_date);
	
	if($duration == 0){
		$username = '<font color="red">'.$user_name.'</font>';
	}else{
		$username = '<font color="green">'.$user_name.'</font>';
	}
	$premium_xy = $duration % 86400;
	$premium_yz = $premium_xy % 3600;
	$premium_days = ($duration - $premium_xy) / 86400; 
	$premium_hours = ($premium_xy - $premium_yz) / 3600;

	if($row['is_offense'] > 2){
		$offense = '<span class="label label-danger">'.$row['is_offense'].'</span>';	
	}else
	if($row['is_offense'] == 2){
		$offense = '<span class="label label-warning">'.$row['is_offense'].'</span>';	
	}else
	if($row['is_offense'] == 1){
		$offense = '<span class="label label-info">'.$row['is_offense'].'</span>';	
	}
	
	$nestedData[] = '<input type="checkbox" name="chk[]" class="chk-boxs" value="'.$db->encryptor('encrypt',$id).'">';
	$nestedData[] = $username;
	$nestedData[] = $offense;
	$nestedData[] = $date;
	$nestedData[] = $elapse;
	
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