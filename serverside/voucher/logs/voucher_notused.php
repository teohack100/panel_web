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
	0	=> 'reseller_name',
	1	=> 'code_name',
	2	=> 'is_qty',
	3	=> 'category',
	4	=> 'permission'
);
if($user_id_2 == 1)
{
	$chks = "is_used=0";
}elseif($user_level_2 == 'administrator' 
		|| $user_level_2 == 'reseller' 
		|| $user_level_2 == 'subadmin' 
		|| $user_level_2 == 'subreseller'){
	$chks = "is_used=0 AND reseller_id='".$user_id_2 ."'";
}

$sql = "SELECT * FROM vouchers WHERE ". $chks . " ORDER BY IF(gen_date = DATE(NOW()), 0, 1), gen_date DESC";
$query = $db->sql_query($sql) or die();
$totalData = $db->sql_numrows($query);
$totalFiltered = $totalData;

$sql = "SELECT * FROM vouchers WHERE 1=1 AND ".$chks." ";

if( !empty($requestData['search']['value']) ) {
	if($user_id_2 == 1){
		$sql.=" AND ( code_name LIKE '%".$requestData['search']['value']."%' "; 
		$sql.=" OR is_qty LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR category LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR permission LIKE '%".$requestData['search']['value']."%' "; 
		$sql.=" OR reseller_name LIKE '%".$requestData['search']['value']."%' ) ";
	}else{
		$sql.=" AND ( code_name LIKE '%".$requestData['search']['value']."%' "; 
		$sql.=" OR is_qty LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR category LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR permission LIKE '%".$requestData['search']['value']."%' "; 
		$sql.=" OR reseller_name LIKE '%".$requestData['search']['value']."%' ) ";
	}	
}

$query =  $db->sql_query($sql) or die();
$totalFiltered = $db->sql_numrows($query);
$sql.="ORDER BY ". $columns[$requestData['order'][0]['column']]."  ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

$query =  $db->sql_query($sql) or die();
$data = array();
while( $row = $db->sql_fetchrow($query) ) {  // preparing an array

	$nestedData=array(); 

	if($user_id_2 == 1){
		$nestedData[] = $row['reseller_name'];	
	}
	if($row['permission'] == 1){
		$permission = 'Activated';
	}elseif($row['permission'] == 0){
		$permission = 'Deactivated';
	}
	$reseller_id = $db->Sanitize(strip_tags(trim($row['reseller_id'])));
	$nestedData[] = '<a href="javascript:void(0);" onclick="permission('.$row['id'].', '.$reseller_id.')" style="text-decoration:none" >'
					.$row['code_name'].
					'</a>';
	$nestedData[] = $row['is_qty'];
	$nestedData[] = $row['category'];
	$nestedData[] = 'Not Used';
	$nestedData[] = $permission;
	$nestedData[] = '<button type="button" class="btn btn-primary" id="'.$row['code_name'].'">
						<i class="glyphicon glyphicon-copy"></i>
					 </button>';
	
	$data[] = $nestedData;		
}

$json_data = array(
			"draw"            => intval( $requestData['draw'] ), 
			"recordsTotal"    => intval( $totalData ),
			"recordsFiltered" => intval( $totalFiltered ),
			"data"            => ( $data )
			);

echo json_encode($json_data);
?>