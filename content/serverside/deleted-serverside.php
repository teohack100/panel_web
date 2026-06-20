<?php
ini_set('max_execution_time', 150);
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller'){
	
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
// datatable column index  => database column name
	0	=> 'id',
	1	=> 'user_name', 
	2	=> 'user_level',
	3	=> 'upline'
);


if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
	$chks = "user_id!=1";	
}else{
	$chks = "user_id!=1 AND upline='".$user_id_2."'";		
}

$sql = "SELECT * FROM users_delete WHERE " . $chks ." ORDER BY user_name ASC";
$query = $db->sql_query($sql) or die();
$totalData = $db->sql_numrows($query);
$totalFiltered = $totalData;

$sql = "SELECT * FROM users_delete WHERE 1=1 AND ".$chks." ";
if( !empty($requestData['search']['value']) ) {
	$sql.=" AND ( user_name LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR user_level LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR upline LIKE '%".$requestData['search']['value']."%' ) ";
}

$query = $db->sql_query($sql) or die();
$totalFiltered = $db->sql_numrows($query);
$sql.="ORDER BY ". $columns[$requestData['order'][0]['column']]."  ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
$query = $db->sql_query($sql) or die();


$data = array();

while($row = $db->sql_fetchrow($query))
{
	$nestedData=array(); 
	$upline = $db->sql_query("SELECT user_name FROM users WHERE user_id='".$row['upline']."'");
	$uplinerow = $db->sql_fetchrow($upline);
	if($row['user_level'] == 'superadmin'){
		$user_level = 'Superadministrador';
	}
	else
	if($row['user_level'] == 'subadmin'){
		$user_level = 'Subadministrador';
	}
	else
	if($row['user_level'] == 'administrator'){
		$user_level = 'Administrador';
	}
	else
	if($row['user_level'] == 'reseller'){
		$user_level = 'Revendedor';
	}
	else
	if($row['user_level'] == 'subreseller'){
		$user_level = 'Subrevendedor';
	}else{
		$user_level = 'Normal';
	}	
	
	$timestamp = $db->sql_query("SELECT delete_timestamp FROM users_delete WHERE delete_timestamp > 0");
	$timess = $db->sql_fetchrow($timestamp);
	
	$dur = $db->calc_time($timess['delete_timestamp']);	
	if($timess['delete_timestamp'] == 0){
		$delete_duration = '<span class="badge badge-info"><span class="fas fa-times"></span> Eliminando permanentemente...</span>';
	}else{
		$delete_duration = '<span class="badge badge-info"><span class="fas fa-times"></span> Eliminando permanentemente despues de &nbsp;<strong>'. $dur['days'] . '</strong> Dia(s), <strong>' . $dur['hours'] . '</strong> Hora(s), <strong>' . $dur['minutes'] . '</strong> Minuto(s)</span>';
	}
	
	$nestedData[] = '<input type="checkbox" name="chk[]" class="chk-box" value="'.$db->encryptor('encrypt', $row['id']).'">';
	$nestedData[] = $row['user_name'];
	$nestedData[] = $delete_duration;
	$nestedData[] = '<span class="badge badge-info"><span class="fas fa-user"></span> '.$user_level.'</span>';
	$nestedData[] = '<span class="badge badge-info"><span class="fas fa-user-shield"></span> '.$uplinerow['user_name'].'</span>';

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
