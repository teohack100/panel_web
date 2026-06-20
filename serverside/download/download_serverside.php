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
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator'){
	$requestData= $_REQUEST;
	if(empty($requestData)){
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}
	
	if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator'){
	}else{
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}
	
	$columns = array( 
		0	=> 'id',
		1	=> 'download_category', 
		2	=> 'download_title', 
		3	=> 'download_network',
		4	=> 'download_device',
		5	=> 'download_date'
	);

	$qry = "SELECT * FROM download";
	$sql = $qry ." ORDER BY IF(download_date = DATE(NOW()), 0, 1), download_date DESC";
	$query = $db->sql_query($sql) or die();
	$totalData = $db->sql_numrows($query);
	$totalFiltered = $totalData;
	
	$sql = $qry ." WHERE 1=1 ";
	if( !empty($requestData['search']['value']) ) { 
		$sql.=" AND ( id LIKE '%".$requestData['search']['value']."%' "; 
		$sql.=" OR download_category LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR download_title LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR download_network LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR download_device LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR download_date LIKE '%".$requestData['search']['value']."%' ) ";
	}
	
	$query = $db->sql_query($sql) or die();
	$totalFiltered = $db->sql_numrows($query);
	$sql.="ORDER BY IF(download_date = DATE(NOW()), 0, 1), ". $columns[$requestData['order'][0]['column']]."  ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
	$query = $db->sql_query($sql) or die();
	
	$data = array();
	while( $row = $db->sql_fetchrow($query) )
	{
		$nestedData=array(); 
		$id = $row['id'];
		$download_category = $row['download_category'];
		$download_title = $row['download_title'];
		$download_network = $row['download_network'];
		$download_device = $row['download_device'];
		$download_msg = nl2br($row['download_msg']);
		$download_date = date('F d, Y h:i:s A', strtotime($row['download_date']));
		
		
		$nestedData[] = '<input type="checkbox" name="chk[]" class="chk-boxs" value="'.$db->encryptor('encrypt',$id).'">';
		$nestedData[] = $download_title;
		$nestedData[] = '<span class="badge badge-info"><span class="fas fa-clock"></span> '.$download_date.'</span>';
		$nestedData[] = '<span class="badge badge-info"><span class="fas fa-eye"></span> '.$download_category.'</span>';
		$nestedData[] = '<span class="badge badge-info"><span class="fas fa-assistive-listening-systems"></span> '.$download_network.'</span>';
		$nestedData[] = '<span class="badge badge-info"><span class="fab fa-pushed"></span> '.$download_device.'</span>';
		$nestedData[] = '<div class="btn-group sidebar-social" role="group">
                            <button onclick="download_view('.$id.')" type="button" class="btn btn-secondary"><a><i class="fas fa-info-circle" aria-hidden="true"></i><span>VIEW</span></a></button>
                            <button onclick="download_edit('.$id.')" type="button" class="btn btn-info"><a><i class="fas fa-pencil-alt" aria-hidden="true"></i></i><span>EDIT</span></a></button>
                        </div>';
		
		$data[] = $nestedData;
	}
	$json_data = array(
			"draw"            => intval( $requestData['draw'] )? intval( $_REQUEST['draw'] ) : 0,
			"recordsTotal"    => intval( $totalData ),
			"recordsFiltered" => intval( $totalFiltered ),
			"data"            => ( $data )
			);
	echo json_encode($json_data);
}else{
	echo '<script>alert("Sorry! You dont have Permission to Access this Page!...");</script>';
	$db->RedirectToURL($db->base_url());
	exit;
}

?>	
