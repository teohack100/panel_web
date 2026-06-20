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
	$nestedData[] = '<input type="checkbox" name="chk[]" class="chk-box" value="'.$db->encryptor('encrypt', $row['id']).'">';
	$nestedData[] = $row['user_name'];
	$nestedData[] = $user_level;
	$nestedData[] = $uplinerow['user_name'];

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