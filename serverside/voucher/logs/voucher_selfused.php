<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../../includes/functions.php';
chkSession();

$requestData= $_REQUEST;
if(empty($requestData)){
	$db->RedirectToURL($db->base_url());
	exit;	
}
$columns = array( 
	0	=> 'code_name',
	1	=> 'client_name',
	2	=> 'is_qty',
	3	=> 'category',
	4	=> 'date_used'
);

if($user_id_2 > 1 && $user_level_2 == 'normal')
{
	$db->RedirectToURL($db->base_url());
	exit;	
}
	$qry = "SELECT * FROM vouchers";
	$chks = "reseller_id='".$user_id_2."' AND is_used!=0 AND user_id='".$user_id_2."' ";


$sql = $qry ." WHERE ". $chks . " ORDER BY IF(date_used = DATE(NOW()), 0, 1), date_used DESC";
$query = $db->sql_query($sql) or die();
$totalData = $db->sql_numrows($query);
$totalFiltered = $totalData;

$sql = $qry ." WHERE 1=1 AND ".$chks." ";
if( !empty($requestData['search']['value']) ) {
	$sql.=" AND ( code_name LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR client_name LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR is_qty LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR category LIKE '%".$requestData['search']['value']."%' "; 
	$sql.=" OR date_used LIKE '%".$requestData['search']['value']."%' ) ";
}

$query = $db->sql_query($sql) or die();
$totalFiltered = $db->sql_numrows($query);
$sql.="ORDER BY IF(date_used = DATE(NOW()), 0, 1), ". $columns[$requestData['order'][0]['column']]."  ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

$query = $db->sql_query($sql) or die();

$data = array();
while( $row = $db->sql_fetchrow($query) ) {
	$nestedData=array(); 
	
	if($row['date_used'] == '0000-00-00 00:00:00'){
		$dt = stripslashes($row['date_used']);
	}else{
		$dt = date('F d, Y h:i:s A', strtotime($row['date_used']));
	}
	
	$nestedData[] = '<font color="green">'.$row['code_name'].'</font>';
	$nestedData[] = $row['client_name'];
	$nestedData[] = $row['is_qty'];
	$nestedData[] = $row['category'];
	$nestedData[] = $dt;
	
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