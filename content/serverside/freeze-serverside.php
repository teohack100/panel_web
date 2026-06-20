<?php
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller'){
	
}else{
	header("Location: /myaccount");	
}

$requestData= $_REQUEST;
if(empty($requestData)){
	$db->RedirectToURL($db->base_url());
	exit;	
}

$columns = array( 
	0	=> 'user_name', 
	1	=> 'role_duration',
	2	=> 'vip_duration',
	3	=> 'user_level',
	4	=> 'last_freeze_date',
	5	=> null
);

$sql = "SELECT user_id, user_name, freeze_status, duration, vip_duration, credits, user_level, last_freeze_date, upline FROM users WHERE is_active=1 AND is_freeze=1 AND status='freeze' ORDER BY user_name ASC";
$query = $db->sql_query($sql) or die();
$totalData = $db->sql_numrows($query);
$totalFiltered = $totalData;
if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
	$sql = "SELECT user_id, user_name, code, freeze_status, duration, vip_duration, credits, user_level, last_freeze_date, upline FROM users WHERE 1=1 AND is_active=1 AND is_freeze=1 AND status='freeze' ";
}else{
	$sql = "SELECT user_id, user_name, code, freeze_status, duration, vip_duration, credits, user_level, last_freeze_date, upline FROM users WHERE 1=1 AND upline='".$user_id_2."' AND is_active=1 AND is_freeze=1 AND status='freeze' ";
}

if( !empty($requestData['search']['value']) ) { 
	$sql.=" AND ( user_id LIKE '%".$requestData['search']['value']."%' "; 
	$sql.=" OR user_name LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR freeze_status LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR duration LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR vip_duration LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR credits LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR user_level LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR last_freeze_date LIKE '%".$requestData['search']['value']."%' ) ";
}

$query = $db->sql_query($sql) or die();
$totalFiltered = $db->sql_numrows($query);
$sql.="ORDER BY ". $columns[$requestData['order'][0]['column']]."  ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

$query = $db->sql_query($sql) or die();


$data = array();
while( $row = $db->sql_fetchrow($query) ) {
	$nestedData=array();
	$id = $row['user_id'];
	$code = $row['code'];
	$credits = $row['credits'];
	$user_name = '<strong>'.$row['user_name'].'</strong>';
		
	$dur = $db->calc_time($row['duration']);	
	if($row['duration'] == 0){
		$premuim_duration = "<strong>". $dur['days'] . "</strong> Dia(s) | <strong>" . $dur['hours'] . "</strong> Hora(s) y <strong>" . $dur['minutes'] . "</strong> Minutos restantes.";
	}elseif($row['duration'] < 3600){
		$premuim_duration = "<strong>". $dur['days'] . "</strong> Dia(s) | <strong>" . $dur['hours'] . "</strong> Hora(s) y <strong>" . $dur['minutes'] . "</strong> Minutos restantes.";	
	}else{
		$premuim_duration = "<strong>". $dur['days'] . "</strong> Dia(s) | <strong>" . $dur['hours'] . "</strong> Hora(s) y <strong>" . $dur['minutes'] . "</strong> Minuto(s)";
	}
	
	$dur2 = $db->calc_time($row['vip_duration']);
	if($row['vip_duration'] == 0){
		$vip_duration = "<font color='red'>". $dur2['days'] . "</font> Dia(s), <font color='red'>" . $dur2['hours'] . "</font> Hora(s) y <font color='red'>" . $dur2['minutes'] . "</font> Minutos restantes.";
	}elseif($row['vip_duration'] < 3600){
		$vip_duration = "<font color='red'>". $dur2['days'] . "</font> Dia(s), <font color='red'>" . $dur2['hours'] . "</font> Hora(s) y <font color='orange'>" . $dur2['minutes'] . "</font> Minutos restantes.";	
	}else{
		$vip_duration = "<font color='green'>". $dur2['days'] . "</font> Dia(s), <font color='green'>" . $dur2['hours'] . "</font> Hora(s) y <font color='green'>" . $dur2['minutes'] . "</font> Minutos restantes.";
	}
	
	$dur3 = $premuim_duration;

	if($row['user_level'] == 'superadmin'){
		$user_level = 'Superadministrador';
	}
	else
	if($row['user_level'] == 'subadmin'){
		$user_level = 'Subadministrador';
	}
	else
	if($row['user_level'] == 'administrator'){
		$user_level = 'Administrador';
	}
	else
	if($row['user_level'] == 'reseller'){
		$user_level = 'Revendedor';
	}
	else
	if($row['user_level'] == 'subreseller'){
		$user_level = 'Subrevendedor';
	}else{
		$user_level = 'Normal';
	}
	
	if($row['freeze_status'] == 1 || $row['freeze_status'] == 0){
		$freeze_stat = 'Congelamiento normal';
		$buttonz = '<button type="button" class="btn btn-success" onclick="unfreezed('.$id.','.$code.')"><a><i class="fas fa-undo-alt"></i></i><span>DESCONGELAR</span></a></button>';
	}else
	if($row['freeze_status'] == 2){
		$freeze_stat = 'Multilogin detectado';
		$buttonz = '<button type="button" class="btn btn-success" onclick="unfreezed('.$id.','.$code.')"><a><i class="fas fa-undo-alt"></i></i><span>DESCONGELAR</span></a></button>';
	}else
	if($row['freeze_status'] == 3){
		$freeze_stat = 'Rol vencido';
		$buttonz = '<button type="button" class="btn btn-warning" onclick="unfreezedrole('.$id.','.$code.')"><a><i class="fas fa-undo-alt"></i></i><span>DESCONGELAR</span></a></button>';
	}
	
	$last_freeze_date = strtotime($row['last_freeze_date']);
	$date = date('d/m/Y H:i', $last_freeze_date);

	$uplineName = get_upline_name_cached($row['upline']);
	
	$nestedData[] = '<div class="text-center"><input type="checkbox" name="chk[]" class="chk-box" value="'.$db->encryptor('encrypt',$id).'"></div>';
	$nestedData[] = $user_name;
	$nestedData[] = '<span class="badge badge-info"><span class="fas fa-info-circle"></span> '.$freeze_stat.'</span>';
	$nestedData[] = '<span class="badge badge-info"><span class="fas fa-user"></span> '.$user_level.'</span>';
	$nestedData[] = '<span class="badge badge-info"><span class="fas fa-calendar"></span> '.$date.'</span>';
	$nestedData[] = '<div class="btn-group sidebar-social" role="group">
                        <button type="button" class="btn btn-primary" onclick="view_info('.$id.','.$code.')"><a><i class="fas fa-user-tag" aria-hidden="true"></i><span>DETALLES</span></a></button>
                        '.$buttonz.'
                    </div>	
					';

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
