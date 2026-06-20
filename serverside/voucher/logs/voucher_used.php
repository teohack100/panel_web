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
if($user_id_2 == 1 
	|| $user_level_2 == 'administrator' 
	|| $user_level_2 == 'reseller' 
	|| $user_level_2 == 'subadmin' 
	|| $user_level_2 == 'subreseller'
	|| $user_level_2 == 'normal')
{
	$columns = array( 
		0	=> 'code_name',
		1	=> 'client_name',
		2	=> 'is_qty',
		3	=> 'category',
		4	=> 'permission',
		5	=> 'date_used'
	);
	$qry = "SELECT * FROM vouchers";	
	$chks = "reseller_id='".$user_id_2."' AND is_used!=0 AND user_id!='".$user_id_2."'";
}else{
	$columns = array( 
		0	=> 'code_name',
		1	=> 'reseller_name',
		2	=> 'is_qty',
		3	=> 'category',
		4	=> 'permission',
		5	=> 'date_used'
	);
	$qry = "SELECT * FROM vouchers";
	$chks = "user_id='".$user_id_2."' AND is_used!=0";
}

$sql = $qry ." WHERE ". $chks . " ORDER BY IF(date_used = DATE(NOW()), 0, 1), date_used DESC";
$query = $db->sql_query($sql) or die();
$totalData = $db->sql_numrows($query);
$totalFiltered = $totalData;

$sql = $qry ." WHERE 1=1 AND ".$chks." ";
if( !empty($requestData['search']['value']) ) {
	if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'subadmin' || $user_level_2 == 'subreseller' || $user_level_2 == 'administrator')
	{
		$sql.=" AND ( code_name LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR client_name LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR is_qty LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR category LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR permission LIKE '%".$requestData['search']['value']."%' "; 
		$sql.=" OR date_used LIKE '%".$requestData['search']['value']."%' ) ";
	}else{
		$sql.=" AND ( code_name LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR reseller_name LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR is_qty LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR category LIKE '%".$requestData['search']['value']."%' "; 
		$sql.=" OR permission LIKE '%".$requestData['search']['value']."%' "; 
		$sql.=" OR date_used LIKE '%".$requestData['search']['value']."%' ) ";		
	}
}

$query = $db->sql_query($sql) or die();
$totalFiltered = $db->sql_numrows($query);
$sql.="ORDER BY ". $columns[$requestData['order'][0]['column']]."  ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

$query = $db->sql_query($sql) or die();

$data = array();
while( $row = $db->sql_fetchrow($query) ) {
	$nestedData=array(); 
	
	if($row['date_used'] == '0000-00-00 00:00:00'){
		$dt = stripslashes($row['date_used']);
	}else{
		$dt = date('F d, Y h:i:s A', strtotime($row['date_used']));
	}
	if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller' || $user_level_2 == 'subadmin' || $user_level_2 == 'subreseller')
	{	
		$chk_user = '<strong>'.$row['client_name'].'</stong>';
	}else{
		$chk_user = '<strong>'.$row['reseller_name'].'</stong>';
	}
	if($row['permission'] == 1){
		$permission = 'Activated';
	}elseif($row['permission'] == 0){
		$permission = 'Deactivated';
	}
	if($row['category'] == 'premium'){
	    $cat = 'PREMIUM';
	}else
	if($row['category'] == 'vip'){
	    $cat = 'VIP';
	}else
	if($row['category'] == 'private'){
	    $cat = 'PRIVATE';
	}

    $nestedData[] = '<strong>'.$row['code_name'].'</strong>';
	$nestedData[] = '<span class="badge badge-info"><span class="fas fa-user"></span> '.$chk_user.'</span>';
	$nestedData[] = '<span class="badge badge-info"><span class="fas fa-gem"></span> '.$row['is_qty'].' Voucher(s) used</span>';
	$nestedData[] = '<span class="badge badge-info"><span class="fas fa-user-tag"></span> '.$cat.'</span>';
	$nestedData[] = '<span class="badge badge-info"><span class="fas fa-clock"></span> '.$dt.'</span>';
	
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