<?php
if (ob_get_level() === 0) {
	ob_start();
}

chkSession();

if (!headers_sent()) {
	header('Content-Type: application/json; charset=UTF-8');
}

$programmitCreditLogsCutoff = '2026-01-01 00:00:00';

function programmit_credit_logs_json($payload) {
	while (ob_get_level() > 0) {
		@ob_end_clean();
	}
	echo json_encode($payload);
	exit;
}

if (!($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller')) {
	programmit_credit_logs_json(array(
		'draw' => 0,
		'recordsTotal' => 0,
		'recordsFiltered' => 0,
		'data' => array(),
		'error' => 'forbidden'
	));
}

$requestData = is_array($_REQUEST) ? $_REQUEST : array();
$draw = isset($requestData['draw']) ? (int)$requestData['draw'] : 0;
$start = isset($requestData['start']) ? max(0, (int)$requestData['start']) : 0;
$length = isset($requestData['length']) ? max(1, (int)$requestData['length']) : 10;
$searchValue = '';
if (isset($requestData['search']) && is_array($requestData['search']) && isset($requestData['search']['value'])) {
	$searchValue = trim((string)$requestData['search']['value']);
}

$columns = array(
	0 => 'id',
	1 => 'credits_username',
	2 => 'credits_qty',
	3 => 'credits_date',
	4 => 'credits_id'
);

$orderColumnIndex = isset($requestData['order'][0]['column']) ? (int)$requestData['order'][0]['column'] : 0;
$orderColumn = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'id';
$orderDir = (isset($requestData['order'][0]['dir']) && strtolower((string)$requestData['order'][0]['dir']) === 'asc') ? 'ASC' : 'DESC';

$baseSql = "FROM credits_logs
	WHERE credits_id='" . $db->SanitizeForSQL($user_id_2) . "'
	  AND credits_date >= '" . $db->SanitizeForSQL($programmitCreditLogsCutoff) . "'";
if ($searchValue !== '') {
	$safeLike = $db->SanitizeForSQL($searchValue);
	$baseSql .= " AND (credits_username LIKE '%" . $safeLike . "%' OR credits_qty LIKE '%" . $safeLike . "%')";
}

$countSql = "SELECT COUNT(*) AS total " . $baseSql;
$countQry = $db->sql_query($countSql);
$countRow = $db->sql_fetchrow($countQry);
$totalFiltered = (int)($countRow['total'] ?? 0);

$totalSql = "SELECT COUNT(*) AS total
	FROM credits_logs
	WHERE credits_id='" . $db->SanitizeForSQL($user_id_2) . "'
	  AND credits_date >= '" . $db->SanitizeForSQL($programmitCreditLogsCutoff) . "'";
$totalQry = $db->sql_query($totalSql);
$totalRow = $db->sql_fetchrow($totalQry);
$totalData = (int)($totalRow['total'] ?? 0);

$sql = "SELECT * " . $baseSql . " ORDER BY " . $orderColumn . " " . $orderDir . " LIMIT " . $start . ", " . $length;
$query = $db->sql_query($sql);

$data = array();
while ($row = $db->sql_fetchrow($query)) {
	$nestedData = array();
	$username = '<strong>' . $row['credits_username'] . '</strong>';
	$credits = (int)$row['credits_qty'];
	$logsDate = strtotime((string)$row['credits_date']);
	$date = ($logsDate > 0) ? date('F d, Y h:i', $logsDate) : '';
	$elapse = ($logsDate > 0) ? $db->time_elapsed_string($logsDate) : '-';

	if ($credits > 0) {
		$cred = '<span class="badge badge-info"><span class="fas fa-coins"></span> Added ' . $credits . ' Credit/s</span>';
	} else {
		$cred = '<span class="badge badge-info"><span class="fas fa-coins"></span> Deducted ' . $credits . ' Credit/s</span>';
	}

	$nestedData[] = $username;
	$nestedData[] = $cred;
	$nestedData[] = '<span class="badge badge-info"><span class="fas fa-calendar"></span> ' . $date . '</span>';
	$nestedData[] = '<span class="badge badge-info"><span class="fas fa-clock"></span> ' . $elapse . '</span>';

	$data[] = $nestedData;
}

programmit_credit_logs_json(array(
	'draw' => $draw,
	'recordsTotal' => $totalData,
	'recordsFiltered' => $totalFiltered,
	'data' => $data
));
