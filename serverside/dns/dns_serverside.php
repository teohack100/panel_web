<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin'  || $user_level_2 == 'administrator'){
}else{
	echo '<script>alert("Sorry! You dont have Permission to Access this Page!...");</script>';
	$db->RedirectToURL($db->base_url());
	exit;
}

$requestData= $_REQUEST;
if(empty($requestData)){
	echo '<script> alert("Invalid Transaction"); </script>';
	$db->RedirectToURL($db->base_url().'404');
	exit;
}

$columns = array( 
	0	=> 'dns_id',
	1	=> 'host_name', 
	2	=> 'domain_name',
	3	=> 'ip_address',
	4	=> 'record_type',
	5	=> 'status',
	6	=> null
);

$qry = "SELECT * FROM dns";
$sql = $qry ." ORDER BY host_name ASC";
$query = $db->sql_query($sql) or die();
$totalData = $db->sql_numrows($query);
$totalFiltered = $totalData;
$sql = $qry ." WHERE 1=1 ";

if( !empty($requestData['search']['value']) ) { 
	$sql.=" AND ( dns_id LIKE '%".$requestData['search']['value']."%' "; 
	$sql.=" OR host_name LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR domain_name LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR ip_address LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR record_type LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR status LIKE '%".$requestData['search']['value']."%' ";
}

$query = $db->sql_query($sql) or die();
$totalFiltered = $db->sql_numrows($query);
$sql.="ORDER BY ". $columns[$requestData['order'][0]['column']]."  ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
$query = $db->sql_query($sql) or die();

$data = array();

while( $row = $db->sql_fetchrow($query) )
{
	$nestedData=array(); 
	$dns_id = $row['dns_id'];
	$host_name = $row['host_name'];
	$domain_name = $row['domain_name'];
	$ip_address = $row['ip_address'];
	$record_type = $row['record_type'];
	if($row['status'] == 1)
	{
		$status = '<label class="label label-success">Online</label>';
	}else{
		$status = '<label class="label label-danger">Offline</label>';
	}
		
	$nestedData[] = '<input type="checkbox" name="chk[]" class="chk-box" value="'.$db->encryptor('encrypt',$dns_id).'">';
	$nestedData[] = $host_name;
	$nestedData[] = $domain_name;
	$nestedData[] = $ip_address;
	$nestedData[] = $record_type;
	$nestedData[] = $status;
	$nestedData[] = '
	                <div class="btn-group" role="group">
                                    <div class="dropdown" role="group">
                                    <button type="button" class="btn btn-outline btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"> <span class="caret"></span> <i class="fa fa-fw fa-list" aria-hidden="true"></i><span> <i class="fa fa-caret-down" aria-hidden="true"></i></span></button>
                                    <ul class="dropdown-menu dropdown-menu-right animated flipInX" role="menu">
                                        <li>
                                            <button type="button" class="btn btn-success btn-block waves-effect waves-light text-left" href="javascript:void(0)" disabled onclick="dns_edit('.$dns_id.')">
					                            <i class="glyphicon glyphicon-pencil"></i> Edit Record
				                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="btn btn-primary btn-block waves-effect waves-light text-left" href="javascript:void(0)" disabled onclick="delete_dns('.$dns_id.')">
					                            <i class="glyphicon glyphicon-remove-circle"></i> Delete Record
				                            </button>
                                        </li>
                                    </ul>
                                    </div>
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
		
?>
