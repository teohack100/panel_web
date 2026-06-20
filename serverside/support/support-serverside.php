<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();

$requestData= $_REQUEST;
if(empty($requestData)){
	echo '<script> alert("Invalid Transaction"); </script>';
	$this->RedirectToURL($this->base_url().'404');
	exit;
}

$columns = array( 
	0	=> 'ticket_name',
	1	=> 'ticket_subject', 
	2	=> 'ticket_status',
	3	=> 'ticket_date',
	4	=> 'ticket_update'
);

if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
	$qry = " ";
	$qry2 = "WHERE 1=1 ";
}else{
	$qry = " WHERE ticket_id_user ='".$user_id_2."' ";
	$qry2 = "WHERE 1=1 AND ticket_id_user ='".$user_id_2."' ";
}

$sql = "SELECT * FROM support_ticket ".$qry." ORDER BY IF(ticket_update = DATE(NOW()), 0, 1), ticket_update DESC";
$query = $db->sql_query($sql) or die();
$totalData = $db->sql_numrows($query);
$totalFiltered = $totalData;

$sql = "SELECT * FROM support_ticket ".$qry2;
if( !empty($requestData['search']['value']) ) { 
	$sql.=" AND ( ticket_name LIKE '%".$requestData['search']['value']."%' "; 
	$sql.=" OR ticket_subject LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR ticket_status LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR ticket_date LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR ticket_update LIKE '%".$requestData['search']['value']."%' ) ";
}

$query = $db->sql_query($sql) or die();
$totalFiltered = $db->sql_numrows($query);
$sql.="ORDER BY IF(ticket_update = DATE(NOW()), 0, 1), ". $columns[$requestData['order'][0]['column']]."  ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

$query = $db->sql_query($sql) or die();

$data = array();
while( $row = $db->sql_fetchrow($query) )
{
	$nestedData=array(); 
	$id = $row['id'];
	$ticket_name = $row['ticket_name'];
	$ticket_subject = $row['ticket_subject'];
	$ticket_date = date('F d, Y h:i:s A', strtotime($row['ticket_date']));
	$ticket_update = date('F d, Y h:i:s A', strtotime($row['ticket_date']));
			
	if($row['ticket_status'] == 'open'){
		$ticket_status = '<label class="label label-success">Open</label>';
	}elseif($row['ticket_status'] == 'customer-reply'){
		$ticket_status = '<label class="label label-primary">Customer Reply</label>';
	}elseif($row['ticket_status'] == 'answered'){
		$ticket_status = '<label class="label label-info">Answered</label>';
	}elseif($row['ticket_status'] == 'closed'){
		$ticket_status = '<label class="label label-default">Closed</label>';
	}

	$nestedData[] = '<a href="/supportticket/'.urlencode($db->encryptor('encrypt',$id)).'/'.$ticket_name.'">'.$ticket_name.'</a>';
	$nestedData[] = $ticket_subject;
	$nestedData[] = $ticket_status;
	$nestedData[] = $ticket_date;
	$nestedData[] = $ticket_update;
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