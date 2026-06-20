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
	4	=> 'c.recovery_date'
);

if($user_id_2 == 1 || $user_level_2 == 'superadmin'){	
	$chks = " u.user_id ='$user_id_2' AND u.user_email=c.recovery_menu ";
	$chks2 = "AND u.user_id ='$user_id_2' AND u.user_email=c.recovery_menu";
}else{
	$chks = " u.user_id ='$user_id_2' AND u.user_id!=1 AND u.user_email=c.recovery_menu";
	$chks2 = " AND u.user_id!=1 AND u.user_id ='$user_id_2' AND u.user_email=c.recovery_menu";
}

$qry = "SELECT u.user_name, u.user_id, u.user_email, c.recovery_menu, c.recovery_ipaddress, c.recovery_date
FROM users as u INNER JOIN recovery_logs as c on u.user_id ='$user_id_2' ";	

$sql = $qry . " WHERE " . $chks . " ORDER BY IF(c.recovery_date = DATE(NOW()), 0, 1), c.recovery_date DESC";
$query = $db->sql_query($sql) or die();
$totalData = $db->sql_numrows($query);
$totalFiltered = $totalData;

$sql = $qry . " WHERE 1=1 " . $chks2 . " ";
if( !empty($requestData['search']['value']) ) {
	$sql.=" OR c.recovery_menu LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR c.recovery_ipaddress LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR c.recovery_date LIKE '%".$requestData['search']['value']."%' ) ";
}

$query = $db->sql_query($sql) or die();
$totalFiltered = $db->sql_numrows($query);
$sql.=" ORDER BY IF(c.recovery_date = DATE(NOW()), 0, 1), ". $columns[$requestData['order'][0]['column']]."  ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
$query = $db->sql_query($sql) or die();

$data = array();
while( $row = $db->sql_fetchrow($query) ) {
	$nestedData=array(); 
	$recovery_menu = $row['recovery_menu'];
	$recovery_ipaddress = $row['recovery_ipaddress'];
	$recovery_date = $row['recovery_date'];
				
	$nestedData[] = $recovery_menu;
	$nestedData[] = $recovery_ipaddress;
	$nestedData[] = $recovery_date;
	$nestedData[] = date('F d, Y h:i:s A', strtotime($recovery_date));

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
