<?php
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
	
}else{
	header("Location: /myaccount");	
}

$requestData= $_REQUEST;
if(empty($requestData)){
	echo '<script> alert("Invalid Transaction"); </script>';
	$db->RedirectToURL($db->base_url().'404');
	exit;	
}

if($user_id_2 == 1 || $user_level_2 == 'superadmin'){	
}else{
	echo '<script> alert("Invalid Transaction"); </script>';
	$db->RedirectToURL($db->base_url().'404');
	exit;		
}

$columns = array( 
	0	=> 'user_name',
	1	=> 'private_duration', 
	2	=> 'private_slot',
	3	=> null
);

$qry = "SELECT user_id, user_name, private_duration, private_slot, user_level FROM users";

$sql = $qry ." WHERE user_id!=1 AND is_active=1 AND status='live' AND is_private=1 ORDER BY user_name ASC";
$query = $db->sql_query($sql) or die();
$totalData = $db->sql_numrows($query);
$totalFiltered = $totalData;

$sql = $qry ." WHERE 1=1 AND user_id!=1 AND is_active=1 AND status='live' AND is_private=1 ";
if( !empty($requestData['search']['value']) ) { 
	$sql.=" AND ( user_id LIKE '%".$requestData['search']['value']."%' "; 
	$sql.=" OR user_name LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR private_duration LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR private_slot LIKE '%".$requestData['search']['value']."%' ) ";
}

$query = $db->sql_query($sql) or die();
$totalFiltered = $db->sql_numrows($query);
$sql.="ORDER BY ". $columns[$requestData['order'][0]['column']]."  ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

$query = $db->sql_query($sql) or die();

$data = array();
while( $row = $db->sql_fetchrow($query) )
{
	$nestedData=array(); 
	$id = $row['user_id'];
	$private_slot = $row['private_slot'];
	if($row['private_duration'] >= 1296000){
		$username = '<font color="green">'.$row['user_name'].'</font>';
	}elseif($row['private_duration'] >= 864000){
		$username = '<font color="blue">'.$row['user_name'].'</font>';
	}elseif($row['private_duration'] >= 432000){
		$username = '<font color="orange">'.$row['user_name'].'</font>';
	}elseif($row['private_duration'] <= 86400){
		$username = '<font color="red">'.$row['user_name'].'</font>';
	}
			
	$dur = $db->calc_time($row['private_duration']);
			
	if($row['private_duration'] == 0){
		$private_duration = "<font color='red'>". $dur['days'] . "</font> Day(s), <font color='red'>" . $dur['hours'] . "</font> Hour(s) and <font color='red'>" . $dur['minutes'] . "</font> Minutes Left.";
	}elseif($row['private_duration'] < 3600){
		$private_duration = "<font color='red'>". $dur['days'] . "</font> Day(s), <font color='red'>" . $dur['hours'] . "</font> Hour(s) and <font color='orange'>" . $dur['minutes'] . "</font> Minutes Left.";	
	}else{
		$private_duration = "<font color='green'>". $dur['days'] . "</font> Day(s), <font color='green'>" . $dur['hours'] . "</font> Hour(s) and <font color='green'>" . $dur['minutes'] . "</font> Minutes Left.";
	}
		
	$nestedData[] = $username;
	$nestedData[] = $private_duration;
	$nestedData[] = $private_slot;
	if($private_slot == 0){
		$nestedData[] = '[ <a href="javascript:void(0)" onclick="view_info('.$id.')"><i class="glyphicon glyphicon-open-file"></i></a> ]';					
	}else{
		$nestedData[] = '[ <a href="javascript:void(0)" onclick="view_info('.$id.')"><i class="glyphicon glyphicon-open-file"></i></a> ] 
		[ <a href="javascript:void(0)" onclick="edit_user('.$id.')"><i class="glyphicon glyphicon-pencil"></i></a> ]';					
	}
			
	$data[] = $nestedData;
}

$json_data = array(
			"draw"            => intval( $requestData['draw'] )? intval( $_REQUEST['draw'] ) : 0,   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => ($data )  // total data array
			);

echo json_encode($json_data);  // send data as json format
?>