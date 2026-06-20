<?php
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
	
}else{
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
	2	=> 'credits'
);

if($user_id_2 == 1 || $user_level_2 == 'superadmin')
{
	$chks = "user_id!=1 AND user_level!='superadmin' AND user_level='administrator' AND is_active=1 AND is_validated=1 AND status='live'";	
}else{
	$chks = "user_id!=1 AND user_level!='superadmin' AND upline='".$user_id_2."' AND user_level='administrator' AND is_active=1 AND is_validated=1 AND status='live'";	
}

$sql = "SELECT user_id, user_name, credits FROM users WHERE " . $chks ." ORDER BY credits ASC";
$query = $db->sql_query($sql) or die();
$totalData = $db->sql_numrows($query);
$totalFiltered = $totalData;

$sql = "SELECT user_id, user_name, code, credits FROM users WHERE 1=1 AND ".$chks." ";
if( !empty($requestData['search']['value']) ) {
	$sql.=" AND ( user_name LIKE '%".$requestData['search']['value']."%' "; 
	$sql.=" OR credits LIKE '%".$requestData['search']['value']."%' ) ";
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
	$credits = $row['credits'];
	$code = $row['code'];
		
	$nestedData[] = '<input type="checkbox" name="chk[]" class="chk-box" value="'.$db->encryptor('encrypt',$id).'">';
	$nestedData[] = $user_name;
	$nestedData[] = $credits;
	$nestedData[] = '[ <a href="javascript:void(0)" onclick="addcredits('.$id.','.$code.')"><i class="glyphicon glyphicon-open-file"></i></a> ]';

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