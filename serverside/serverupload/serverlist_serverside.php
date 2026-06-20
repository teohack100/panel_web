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

$requestData = is_array($_REQUEST) ? $_REQUEST : array();

$columns = array(
	0 => 'server_id',
	1 => 'server_name',
	2 => 'server_category',
	3 => 'server_ip',
	4 => 'server_port',
	5 => 'status'
);

$draw = isset($requestData['draw']) ? (int)$requestData['draw'] : 0;
$start = isset($requestData['start']) ? (int)$requestData['start'] : 0;
$length = isset($requestData['length']) ? (int)$requestData['length'] : 10;
$start = ($start < 0) ? 0 : $start;
if($length <= 0 || $length > 500){
	$length = 10;
}

$orderColumnIndex = isset($requestData['order'][0]['column']) ? (int)$requestData['order'][0]['column'] : 1;
$orderDir = isset($requestData['order'][0]['dir']) ? strtolower((string)$requestData['order'][0]['dir']) : 'asc';
if($orderDir !== 'asc' && $orderDir !== 'desc'){
	$orderDir = 'asc';
}
$orderColumn = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'server_name';

$searchValue = trim((string)($requestData['search']['value'] ?? ''));
$searchSql = $db->SanitizeForSQL($searchValue);

$whereSql = " WHERE 1=1 ";
if($searchValue !== ''){
	$whereSql .= " AND (";
	$whereSql .= "server_id LIKE '%".$searchSql."%' ";
	$whereSql .= "OR server_name LIKE '%".$searchSql."%' ";
	$whereSql .= "OR server_category LIKE '%".$searchSql."%' ";
	$whereSql .= "OR server_port LIKE '%".$searchSql."%' ";
	$whereSql .= "OR server_ip LIKE '%".$searchSql."%' ";
	$whereSql .= "OR status LIKE '%".$searchSql."%')";
}

$totalQry = $db->sql_query("SELECT COUNT(*) AS total FROM server_list");
$totalRow = $totalQry ? $db->sql_fetchrow($totalQry) : array('total' => 0);
$totalData = isset($totalRow['total']) ? (int)$totalRow['total'] : 0;

$filteredQry = $db->sql_query("SELECT COUNT(*) AS total FROM server_list".$whereSql);
$filteredRow = $filteredQry ? $db->sql_fetchrow($filteredQry) : array('total' => 0);
$totalFiltered = isset($filteredRow['total']) ? (int)$filteredRow['total'] : 0;

$dataSql = "SELECT server_id, server_name, server_category, server_ip, server_port, status FROM server_list".$whereSql;
$dataSql .= " ORDER BY ".$orderColumn." ".$orderDir;
$dataSql .= " LIMIT ".$start.", ".$length;
$query = $db->sql_query($dataSql);

$data = array();
while($query && ($row = $db->sql_fetchrow($query))){
	$nestedData = array();
	$server_id = (int)$row['server_id'];
	$server_name = htmlspecialchars((string)$row['server_name'], ENT_QUOTES, 'UTF-8');
	$server_category_raw = (string)$row['server_category'];
	$server_category = htmlspecialchars(ucfirst($server_category_raw), ENT_QUOTES, 'UTF-8');
	$server_ip = htmlspecialchars((string)$row['server_ip'], ENT_QUOTES, 'UTF-8');
	$server_port = htmlspecialchars((string)$row['server_port'], ENT_QUOTES, 'UTF-8');

	if((int)$row['status'] === 1){
		$status = '<span class="badge badge-success"><span class="fas fa-power-off"></span> Online</span>';
	}else{
		$status = '<span class="badge badge-danger"><span class="fas fa-power-off"></span> Offline</span>';
	}

	$controls = '<div class="btn-group btn-group-sm" role="group">';
	$controls .= '<button type="button" class="btn btn-warning" onclick="server_edit('.$server_id.')" title="Edit Server">';
	$controls .= '<i class="glyphicon glyphicon-pencil"></i>';
	$controls .= '</button>';
	$controls .= '</div>';

	$nestedData[] = '<input type="checkbox" name="chk[]" class="chk-box" value="'.$db->encryptor('encrypt', $server_id).'">';
	$nestedData[] = '<strong>'.$server_name.'</strong>';
	$nestedData[] = '<span class="badge badge-primary">'.$server_category.'</span>';
	$nestedData[] = '<span class="badge badge-info"><span class="fas fa-server"></span> '.$server_ip.'</span>';
	$nestedData[] = '<span class="badge badge-info"><span class="fas fa-network-wired"></span> '.$server_port.'</span>';
	$nestedData[] = $status;
	$nestedData[] = $controls;

	$data[] = $nestedData;
}

$json_data = array(
	"draw" => $draw,
	"recordsTotal" => $totalData,
	"recordsFiltered" => $totalFiltered,
	"data" => $data
);
echo json_encode($json_data);
		
?>
