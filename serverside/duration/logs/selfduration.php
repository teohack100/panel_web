<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
$root = __DIR__;
while (!is_file($root . '/includes/functions.php')) {
    $parent = dirname($root);
    if ($parent === $root) { break; }
    $root = $parent;
}
require $root . '/includes/functions.php';
chkSession();
if($user_level_2 == 'normal')
{
	echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
	exit;
}

$requestData= $_REQUEST;
if(empty($requestData)){
	echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
	exit;
}

$columns = array( 
	0	=> 'duration_username',
	1	=> 'duration_item',
	2	=> 'duration_qty',
	3	=> 'duration_type',
	4	=> 'duration_date'
);

if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'admin') {
	$chk = " ";
	$chk2 = "1=1 ";
}else{
	$chk = "WHERE duration_id = '".$user_id_2."' ";
	$chk2 = "1=1 AND duration_id = '".$user_id_2."' ";
}

$sql = "SELECT * FROM duration_logs ".$chk;
$query = $db->sql_query($sql) or die();
$totalData = $db->sql_numrows($query);
$totalFiltered = $totalData;

$sql = "SELECT * FROM duration_logs WHERE ".$chk2;
if( !empty($requestData['search']['value']) ) {
	$sql.=" AND ( duration_username LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR duration_item LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR duration_qty LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR duration_type LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR duration_date LIKE '%".$requestData['search']['value']."%' ) ";
}

$query = $db->sql_query($sql) or die();
$totalFiltered = $db->sql_numrows($query);

$sql.="ORDER BY IF(duration_date = DATE(NOW()), 0, 1), ". $columns[$requestData['order'][0]['column']]."  ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

$query = $db->sql_query($sql) or die();

$data = array();
while( $row = $db->sql_fetchrow($query) ) {
	$nestedData=array(); 
	$id = $row['id'];
	$username = $row['duration_username'];
	$type = $row['duration_type'];
	$qty = $row['duration_qty'];
	$item = $row['duration_item'];
	$logs_date = strtotime($row['duration_date']);
	$date = date('F d, Y h:i:s A', $logs_date);
	$elapse = $db->time_elapsed_string($logs_date);

	$nestedData[] = $username;
	$nestedData[] = $item;
	$nestedData[] = $qty;
	$nestedData[] = $type;
	$nestedData[] = $date;
			
	$data[] = $nestedData;		
}

$json_data = array(
"draw"            => intval( $requestData['draw'] )? intval( $_REQUEST['draw'] ) : 0,
"recordsTotal"    => intval( $totalData ),
"recordsFiltered" => intval( $totalFiltered ),
"data"            => ( $data )
);

echo json_encode($json_data);
?>
