<?php

chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'subadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller'){
	
}else{
	header("Location: /dashboard");	
}

$requestData= $_REQUEST;
if(empty($requestData)){
	$db->RedirectToURL($db->base_url());
	exit;	
}

if (!function_exists('programmit_compact_duration_label')) {
	function programmit_compact_duration_label($seconds)
	{
		$seconds = max(0, (int)$seconds);
		$days = floor($seconds / 86400);
		$rem = $seconds % 86400;
		$hours = floor($rem / 3600);
		$rem = $rem % 3600;
		$minutes = floor($rem / 60);
		$secs = $rem % 60;
		return '<strong>'.$days.'d</strong> <strong>'.$hours.'h</strong> <strong>'.$minutes.'m</strong> <strong>'.$secs.'s</strong>';
	}
}

$columns = array(
	0	=> null,
	1	=> 'u.user_id',
	2	=> 'u.user_name',
	3	=> 'u.is_connected',
	4	=> 'u.duration',
	5	=> 'u.user_level',
	6	=> 'u.upline',
	7	=> null
);

$draw = isset($requestData['draw']) ? (int)$requestData['draw'] : 0;
$start = isset($requestData['start']) ? (int)$requestData['start'] : 0;
$length = isset($requestData['length']) ? (int)$requestData['length'] : 10;
if ($start < 0) $start = 0;
if ($length < 1) $length = 10;
if ($length > 1000) $length = 1000;

$orderColIndex = isset($requestData['order'][0]['column']) ? (int)$requestData['order'][0]['column'] : 1;
$orderDir = isset($requestData['order'][0]['dir']) ? strtoupper($requestData['order'][0]['dir']) : 'ASC';
$orderDir = $orderDir === 'DESC' ? 'DESC' : 'ASC';
$orderColumn = isset($columns[$orderColIndex]) ? $columns[$orderColIndex] : 'u.user_name';
if ($orderColumn === null) $orderColumn = 'u.user_name';

$where = "u.user_id!=1
	AND u.is_active!=0
	AND u.is_groupname!='bulk'
	AND u.is_groupname!='superadmin'
	AND u.is_groupname!='administrator'
	AND u.is_groupname!='subadmin'
	AND u.is_groupname!='reseller'
	AND u.is_groupname!='subreseller'
	AND u.is_vip!='1'
	AND u.is_freeze!=1
	AND u.status='live'
	AND u.user_id!='".$db->SanitizeForSQL($user_id_2)."'
	AND (u.duration > '0' OR u.private_duration > '0')";

if(!($user_id_2 == 1 || $user_level_2 == 'superadmin')){
	$where .= " AND u.upline='".$db->SanitizeForSQL($user_id_2)."'";
}

$searchValue = '';
if(isset($requestData['search']['value'])){
	$searchValue = trim((string)$requestData['search']['value']);
}
$searchSql = '';
if($searchValue !== ''){
	$sv = $db->SanitizeForSQL($searchValue);
	$searchSql = " AND (
		u.user_id LIKE '%".$sv."%'
		OR u.user_name LIKE '%".$sv."%'
		OR u.user_level LIKE '%".$sv."%'
		OR u.upline LIKE '%".$sv."%'
	)";
}

$totalSql = "SELECT COUNT(*) AS cnt FROM users u WHERE ".$where;
$totalRow = $db->sql_fetchrow($db->sql_query($totalSql));
$totalData = isset($totalRow['cnt']) ? (int)$totalRow['cnt'] : 0;

$filteredSql = "SELECT COUNT(*) AS cnt FROM users u WHERE ".$where.$searchSql;
$filteredRow = $db->sql_fetchrow($db->sql_query($filteredSql));
$totalFiltered = isset($filteredRow['cnt']) ? (int)$filteredRow['cnt'] : 0;

$sql = "SELECT
		u.user_id,
		u.user_name,
		u.code,
		u.duration,
		u.private_duration,
		u.is_private,
		u.credits,
		u.user_level,
		u.is_connected,
		u.upline,
		up.user_name AS upline_name
	FROM users u
	LEFT JOIN users up ON up.user_id = u.upline
	WHERE ".$where.$searchSql."
	ORDER BY ".$orderColumn." ".$orderDir."
	LIMIT ".$start.",".$length;

$query = $db->sql_query($sql) or die();


$data = array();
while( $row = $db->sql_fetchrow($query) ) {
	$nestedData=array();
	$id = $row['user_id'];
	$code = $row['code'];
	$credits = $row['credits'];
	if($row['duration'] == 0 && $row['private_duration'] == 0 ){
		$user_name = '<font color="red"><strong>'.$row['user_name'].'</strong></font>';
	}else{
		$user_name = '<strong>'.$row['user_name'].'</strong>';
	}
		
	$premiumSeconds = max(0, (int)$row['duration']);
	$privateSeconds = max(0, (int)$row['private_duration']);
	$dur = $db->calc_time($premiumSeconds);
	$durr = $db->calc_time($privateSeconds);
	$durSeconds = $premiumSeconds % 60;
	$privateSecondsRemainder = $privateSeconds % 60;
	$dur4 = '<span class="live-countdown" data-seconds="'.$premiumSeconds.'">'.programmit_compact_duration_label($premiumSeconds).'</span>';
	$private_duration = '<span class="live-countdown" data-seconds="'.$privateSeconds.'">'.programmit_compact_duration_label($privateSeconds).'</span>';

	if($row['user_level'] == 'superadmin'){
		$user_level = '<span class="badge badge-info"><span class="fas fa-crown"></span> Superadministrador</span>';
		if($credits == 0)
		{
			$credits_label = '<span class="badge badge-danger"><span class="fas fa-coins"></span> 0 creditos</span>';
		}else{
			$credits_label = '<span class="badge badge-success"><span class="fas fa-coins"></span> '.$credits.' creditos</span>';
		}
	}
	else
	if($row['user_level'] == 'reseller'){
		$user_level = '<span class="badge badge-info"><span class="far fa-user-circle"></span> Revendedor</span>';
		if($credits == 0)
		{
			$credits_label = '<span class="badge badge-danger"><span class="fas fa-coins"></span> 0 creditos</span>';
		}else{
			$credits_label = '<span class="badge badge-success"><span class="fas fa-coins"></span> '.$credits.' creditos</span>';
		}
	}
	else
	if($row['user_level'] == 'subreseller'){
		$user_level = '<span class="badge badge-info"><span class="fas fa-user-circle"></span> Subrevendedor</span>';
		if($credits == 0)
		{
			$credits_label = '<span class="badge badge-danger"><span class="fas fa-coins"></span> 0 creditos</span>';
		}else{
			$credits_label = '<span class="badge badge-success"><span class="fas fa-coins"></span> '.$credits.' creditos</span>';
		}
	}else{
		$user_level = '<span class="badge badge-info"><span class="fas fa-user"></span> Normal</span>';
		$credits_label = '<span class="badge badge-secondary"><span class="fas fa-times"></span> No disponible</span>';
	}
	
	if($row['is_connected'] == '1'){
	    $stat = '<span class="badge badge-success"><span class="fas fa-power-off"></span> En linea</span>';
	    $stat2 = '<span class="fas fa-power-off" style="color: #1ecab8;"></span>&nbsp';
	}else{
	    $stat = '<span class="badge badge-danger"><span class="fas fa-power-off"></span> Desconectado</span>';
	    $stat2 = '<span class="fas fa-power-off" style="color: #f1646c;"></span>&nbsp';
	}
	
	if($row['is_private'] == '1'){
	    $privdur = '<span class="badge badge-info"><span class="fas fa-clock"></span> '.$private_duration.'</span>';
	}else{
	    $privdur = '';
	}
	 
	$uplineName = !empty($row['upline_name']) ? $row['upline_name'] : '';
	
	$nestedData[] = '';
	$nestedData[] = '<div class="text-center"><input type="checkbox" name="chk[]" class="chk-box" value="'.$db->encryptor('encrypt',$id).'"></div>';
	$nestedData[] = $stat2.$user_name;
	$nestedData[] = $stat;
	$nestedData[] = '<span class="badge badge-info"><span class="fas fa-clock"></span> '.$dur4.'</span><br>
	                '.$privdur.'';
	$nestedData[] = $user_level;
	$nestedData[] = '<span class="badge badge-info"><span class="fas fa-user-shield"></span> '.$uplineName.'</span>';

	if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller')
	{

		if($credits_2 < 1 &&  $user_id_2 != 1 && $user_level_2 != 'superadmin'){

			if($row['upline'] == $user_id_2)
			{
				$nestedData[] = '<div class="btn-group sidebar-social" role="group">
                                      <button type="button" class="btn btn-primary" onclick="view_info('.$id.','.$code.')"><a><i class="fas fa-user-tag" aria-hidden="true"></i><span>DETALLES</span></a></button>
                                      <button type="button" class="btn btn-success" onclick="edit_user('.$id.','.$code.')"><a><i class="fas fa-user-edit"></i></i><span>EDITAR</span></a></button>
                                      <button type="button" class="btn btn-secondary" onclick="getVoucher('.$id.','.$code.')"><a><i class="fas fa-user-clock" aria-hidden="true"></i></i><span>DURACION</span></a></button>
                                      '.programmit_mduration_button($id, $code).'
                                    </div>';
			}else
			{
				$nestedData[] = '<div class="btn-group sidebar-social" role="group">
                                      <button type="button" class="btn btn-primary" onclick="view_info('.$id.','.$code.')"><a><i class="fas fa-user-tag" aria-hidden="true"></i><span>DETALLES</span></a></button>
                                      <button type="button" class="btn btn-secondary" onclick="getVoucher('.$id.','.$code.')"><a><i class="fas fa-user-clock" aria-hidden="true"></i></i><span>DURACION</span></a></button>
                                    </div>';
			}
		}else{

			if($user_level == 'Member'){
				if($row['upline'] == $user_id_2 || $user_id_2 == 1 || $user_level_2 == 'superadmin')
				{
					$nestedData[] = '<div class="btn-group sidebar-social" role="group">
                                      <button type="button" class="btn btn-primary" onclick="view_info('.$id.','.$code.')"><a><i class="fas fa-user-tag" aria-hidden="true"></i><span>DETALLES</span></a></button>
                                      <button type="button" class="btn btn-success" onclick="edit_user('.$id.','.$code.')"><a><i class="fas fa-user-edit"></i></i><span>EDITAR</span></a></button>
                                      <button type="button" class="btn btn-secondary" onclick="getVoucher('.$id.','.$code.')"><a><i class="fas fa-user-clock" aria-hidden="true"></i></i><span>DURACION</span></a></button>
                                      '.programmit_mduration_button($id, $code).'
                                    </div>';

				}else{
					$nestedData[] = '<div class="btn-group sidebar-social" role="group">
                                      <button type="button" class="btn btn-primary" onclick="view_info('.$id.','.$code.')"><a><i class="fas fa-user-tag" aria-hidden="true"></i><span>DETALLES</span></a></button>
                                      <button type="button" class="btn btn-secondary" onclick="getVoucher('.$id.','.$code.')"><a><i class="fas fa-user-clock" aria-hidden="true"></i></i><span>DURACION</span></a></button>
                                    </div>';

				}
			}else{
				if($row['upline'] == $user_id_2 || $user_id_2 == 1 || $user_level_2 == 'superadmin')
				{
					$nestedData[] = '<div class="btn-group sidebar-social" role="group">
                                      <button type="button" class="btn btn-primary" onclick="view_info('.$id.','.$code.')"><a><i class="fas fa-user-tag" aria-hidden="true"></i><span>DETALLES</span></a></button>
                                      <button type="button" class="btn btn-success" onclick="edit_user('.$id.','.$code.')"><a><i class="fas fa-user-edit"></i></i><span>EDITAR</span></a></button>
                                      <button type="button" class="btn btn-secondary" onclick="getVoucher('.$id.','.$code.')"><a><i class="fas fa-user-clock" aria-hidden="true"></i></i><span>DURACION</span></a></button>
                                      '.programmit_mduration_button($id, $code).'
                                    </div>';
				}else{
					$nestedData[] = '<div class="btn-group sidebar-social" role="group">
					                    <button type="button" class="btn btn-secondary" onclick="getVoucher('.$id.','.$code.')"><a><i class="fas fa-user-clock" aria-hidden="true"></i></i><span>DURACION</span></a></button>
                                    </div>';

				}
			}
		}
	}else{
	//subreseller
		if($user_level_2 == 'subreseller')
		{
			if($credits_2 < 1){
				$nestedData[] = '<div class="btn-group sidebar-social" role="group">
                                      <button type="button" class="btn btn-primary" onclick="view_info('.$id.','.$code.')"><a><i class="fas fa-user-tag" aria-hidden="true"></i><span>DETALLES</span></a></button>
                                      <button type="button" class="btn btn-success" onclick="edit_user('.$id.','.$code.')"><a><i class="fas fa-user-edit"></i></i><span>EDITAR</span></a></button>
                                    </div>';
			}else{

				$nestedData[] = '<div class="btn-group sidebar-social" role="group">
                                      <button type="button" class="btn btn-primary" onclick="view_info('.$id.','.$code.')"><a><i class="fas fa-user-tag" aria-hidden="true"></i><span>DETALLES</span></a></button>
                                      <button type="button" class="btn btn-success" onclick="edit_user('.$id.','.$code.')"><a><i class="fas fa-user-edit"></i></i><span>EDITAR</span></a></button>
                                      <button type="button" class="btn btn-secondary" onclick="getVoucher('.$id.','.$code.')"><a><i class="fas fa-user-clock" aria-hidden="true"></i></i><span>DURACION</span></a></button>
                                      '.programmit_mduration_button($id, $code).'
                                    </div>';
			}
		}else{
			$nestedData[] = '<div class="btn-group sidebar-social" role="group">
                                      <button type="button" class="btn btn-primary" onclick="view_info('.$id.','.$code.')"><a><i class="fas fa-user-tag" aria-hidden="true"></i><span>DETALLES</span></a></button>
                                      <button type="button" class="btn btn-success" onclick="edit_user('.$id.','.$code.')"><a><i class="fas fa-user-edit"></i></i><span>EDITAR</span></a></button>
                                    </div>';
		}
	}
	$data[] = $nestedData;	
}

$json_data = array(
			"draw"            => $draw,
			"recordsTotal"    => intval( $totalData ),
			"recordsFiltered" => intval( $totalFiltered ),
			"data"            => ($data )
			);

echo json_encode($json_data);
?>
