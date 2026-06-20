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

$requestData= $_REQUEST;
if(empty($requestData)){
	echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
	exit;
}

$columns = array( 
	0	=> 'u.user_name',
	1	=> 'c.premium', 
	2	=> 'c.vip',
	3	=> 'c.description',
	4	=> 'c.logs_date'
);

if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin'  || $user_level_2 == 'administrator' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller' || $user_level_2 == 'normal'){	
	$chks = " u.user_id = '".$user_id_2."' ";
	$chks2 = "AND u.user_id='".$user_id_2."' ";
}else{
	$chks = " u.user_id  = '".$user_id_2."' ";
	$chks2 = " AND u.user_id!=1 AND u.user_id='".$user_id_2."' ";
}

$qry = "SELECT u.user_name, c.premium, c.vip, c.description, c.logs_date
FROM users as u INNER JOIN conversion_logs as c on u.user_id = c.client_id";	

$sql = $qry . " WHERE " . $chks . " ORDER BY IF(c.logs_date = DATE(NOW()), 0, 1), c.logs_date DESC";
$query = $db->sql_query($sql) or die();
$totalData = $db->sql_numrows($query);
$totalFiltered = $totalData;

$sql = $qry . " WHERE 1=1 " . $chks2 . " ";
if( !empty($requestData['search']['value']) ) {
	$sql.=" AND ( u.user_name LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR c.premium LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR c.vip LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR c.description LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR c.logs_date LIKE '%".$requestData['search']['value']."%' ) ";
}

$query = $db->sql_query($sql) or die();
$totalFiltered = $db->sql_numrows($query);
$sql.=" ORDER BY IF(c.logs_date = DATE(NOW()), 0, 1), ". $columns[$requestData['order'][0]['column']]."  ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
$query = $db->sql_query($sql) or die();

$data = array();
while( $row = $db->sql_fetchrow($query) ) {
	$nestedData=array(); 
	$user_name = $row['user_name'];
	$premium = $row['premium'];
	$vip = $row['vip'];
	$description = $row['description'];
	$logs_date = $row['logs_date'];
				
	$nestedData[] = $user_name;
	$nestedData[] = $premium;
	$nestedData[] = $vip;
	$nestedData[] = $description;
	$nestedData[] = date('F d, Y h:i:s A', strtotime($logs_date));

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
