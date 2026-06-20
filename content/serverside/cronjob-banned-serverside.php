<?php
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator'){
}else{
	echo '<script> alert("Invalid Transaction"); </script>';
	$db->RedirectToURL($db->base_url().'404');
	exit;
}

$requestData= $_REQUEST;
if(empty($requestData)){
	echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
	exit;
}

$columns = array(
	0	=> 'id',
	1	=> 'ip', 
	2	=> 'attempts',
	3	=> 'content',
	4	=> 'logs_date'
);

$sql = "SELECT * FROM cronjob_banned_ip ORDER BY ip ASC";

$query = $db->sql_query($sql) or die();
$totalData = $db->sql_numrows($query);
$totalFiltered = $totalData;

$sql = "SELECT * FROM cronjob_banned_ip WHERE 1=1 ";
if( !empty($requestData['search']['value']) ) {
	$sql.=" AND ( content LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR ip LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR attempts LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR logs_date LIKE '%".$requestData['search']['value']."%' ) ";
}

$query = $db->sql_query($sql) or die();
$totalFiltered = $db->sql_numrows($query);
$sql.="ORDER BY ". $columns[$requestData['order'][0]['column']]."  ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
$query = $db->sql_query($sql) or die();

$data = array();
while( $row = $db->sql_fetchrow($query) )
{
	$nestedData=array(); 
	$id = $db->encryptor('encrypt',$row['id']);
	if($row['attempts'] >= 9){
		$attempts = '<font color="red">'.$row['attempts'].'</font>';
		$ipaddress = '<font color="red">'.$row['ip'].'</font>';
	}
	elseif($row['attempts'] >= 6){
		$attempts = '<font color="orange">'.$row['attempts'].'</font>';
		$ipaddress = '<font color="orange">'.$row['ip'].'</font>';
	}
	elseif($row['attempts'] >= 3){
		$attempts = '<font color="yeloow">'.$row['attempts'].'</font>';
		$ipaddress = $row['ip'];
	}else{
		$attempts = '<font color="blue">'.$row['attempts'].'</font>';
		$ipaddress = $row['ip'];
	}
	$nestedData[] = '<input type="checkbox" name="chk[]" class="chk-boxs" value="'.$id.'">';			
	$nestedData[] = '<a href="javascript:void(0)" onclick="cronjobEditIP('.$row['id'].')">'.$ipaddress.'</a>';
	$nestedData[] = $attempts;
	$nestedData[] = $row['content'];
	$nestedData[] = date('F d, Y h:i:s', strtotime($row['logs_date']));
			
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