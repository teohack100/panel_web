<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();

$requestData= $_REQUEST;
		
if(empty($requestData)){
	echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
	exit;
}

if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'subadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller' || $user_level_2 == 'normal'){
}else{
	$db->RedirectToURL($db->base_url().'404');
	exit;
}
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'normal')
{
	$columns = array(
		0	=> 'client_id',
		1	=> 'client_name', 
		2	=> 'client_ipaddress',
		3	=> 'status',
		4	=> 'request_date'
	);
}else{
	$columns = array(
		0	=> 'fr.client_id',
		1	=> 'fr.client_name', 
		2	=> 'fr.client_ipaddress',
		3	=> 'fr.status',
		4	=> 'fr.request_date'
	);	
}
if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
	$qry = "SELECT client_id, client_name, client_ipaddress, status, request_date FROM freeze_request";
	$chks = "status='pending' ";
	$orderby = 'client_name';
}elseif($user_level_2 == 'administrator' || $user_level_2 == 'subadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller'){
	$qry = "SELECT fr.client_id, fr.client_name, fr.client_ipaddress, fr.status, fr.request_date FROM 
	users as u INNER JOIN freeze_request as fr on u.user_id = fr.client_id";
	$chks = "fr.client_id!='".$user_id_2."' AND u.upline='".$user_id_2."' AND fr.status='pending' ";
	$orderby = 'fr.client_name';
}else{
	$qry = "SELECT client_id, client_name, client_ipaddress, status, request_date FROM freeze_request";
	$chks = "client_id='".$user_id_2."' AND status='pending' ";
	$orderby = 'client_name';
}

$sql = $qry . " WHERE " . $chks ." ORDER BY ".$orderby." ASC";
$query = $db->sql_query($sql) or die();
$totalData = $db->sql_numrows($query);
$totalFiltered = $totalData;

$sql = $qry . " WHERE 1=1 AND ".$chks." ";

if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'normal'){
if( !empty($requestData['search']['value']) ) {
	$sql.=" AND ( client_id LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR client_name LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR client_ipaddress LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR status LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR request_date LIKE '%".$requestData['search']['value']."%' ) ";
}
}else{
if( !empty($requestData['search']['value']) ) {
	$sql.=" AND ( fr.client_id LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR fr.client_name LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR fr.client_ipaddress LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR fr.status LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR fr.request_date LIKE '%".$requestData['search']['value']."%' ) ";
}	
}
$query = $db->sql_query($sql) or die();
$totalFiltered = $db->sql_numrows($query);
$sql.="ORDER BY ". $columns[$requestData['order'][0]['column']]."  ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
$query = $db->sql_query($sql) or die();

$data = array();
while( $row = $db->sql_fetchrow($query) ) {
	$nestedData=array(); 
	$client_id = $db->encryptor('encrypt',$row['client_id']);
	$client_username = $row['client_name'];
	$client_ipaddress = $row['client_ipaddress'];
	$status = '<label class="label label-danger">'.$row['status'].'</label>';
	$request_date = date('F d, Y h:i:s', strtotime($row['request_date']));

	$nestedData[] = '<input type="checkbox" name="chk[]" class="chk-box" value="'.$client_id.'">';
	$nestedData[] = $client_username;
	$nestedData[] = $client_ipaddress;
	$nestedData[] = $status;
	$nestedData[] = $request_date;
		
	$data[] = $nestedData;
}

$json_data = array(
	"draw"            => intval( $requestData['draw'] )? intval( $_REQUEST['draw'] ) : 0,
	"recordsTotal"    => intval( $totalData ),
	"recordsFiltered" => intval( $totalFiltered ),
	"data"            => ($data)
);

echo json_encode($json_data);
?>