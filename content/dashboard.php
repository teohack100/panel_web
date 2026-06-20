<?php
chkSession();

function programmit_dashboard_cache_file($userId) {
	$userId = (int)$userId;
	if ($userId <= 0) {
		return '';
	}
	$tmpDir = rtrim((string)sys_get_temp_dir(), '/\\');
	if ($tmpDir === '') {
		$tmpDir = '/tmp';
	}
	$cacheDir = $tmpDir . DIRECTORY_SEPARATOR . 'programmit_bootstrap';
	if (!is_dir($cacheDir)) {
		@mkdir($cacheDir, 0775, true);
	}
	return $cacheDir . DIRECTORY_SEPARATOR . 'dashboard_user_' . $userId . '.html';
}

$programmit_dashboard_cache_enabled = ((int)$user_id_2 > 0 && strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'GET');
$programmit_dashboard_cache_file = '';
if($programmit_dashboard_cache_enabled){
	$programmit_dashboard_cache_file = programmit_dashboard_cache_file((int)$user_id_2);
	$programmit_dashboard_cache_ttl = 20;
	clearstatcache(true, $programmit_dashboard_cache_file);
	if($programmit_dashboard_cache_file !== '' && is_file($programmit_dashboard_cache_file) && (time() - (int)@filemtime($programmit_dashboard_cache_file)) <= $programmit_dashboard_cache_ttl){
		if(!headers_sent()){
			header('Content-Type: text/html; charset=UTF-8');
		}
		readfile($programmit_dashboard_cache_file);
		return;
	}
	ob_start();
}

$dashboard_user_id_sql = $db->SanitizeForSQL($user_id_2);
$dashboard_is_global_client_scope = ((int)$user_id_2 === 1 || $user_level_2 === 'superadmin');

$stats_qry = $db->sql_query("SELECT
	COALESCE(SUM(CASE WHEN user_id!=1 AND user_id!='".$dashboard_user_id_sql."' THEN 1 ELSE 0 END), 0) AS managed_total_all,
	COALESCE(SUM(CASE WHEN user_id!=1 AND user_id!='".$dashboard_user_id_sql."' AND COALESCE(is_active, 0)!=0 AND COALESCE(is_freeze, 0)!=1 AND status='live' THEN 1 ELSE 0 END), 0) AS managed_active_all,
	COALESCE(SUM(CASE WHEN user_id!=1 AND user_id!='".$dashboard_user_id_sql."' AND (COALESCE(is_active, 0)=0 OR COALESCE(is_freeze, 0)=1 OR status IS NULL OR status<>'live') THEN 1 ELSE 0 END), 0) AS managed_inactive_all,
	COALESCE(SUM(CASE WHEN (duration > 0 OR vip_duration > 0 OR private_duration > 0) AND user_level='normal' AND user_id!='".$dashboard_user_id_sql."' THEN 1 ELSE 0 END), 0) AS active_users2_normal,
	COALESCE(SUM(CASE WHEN duration <= 0 AND vip_duration <= 0 AND private_duration <= 0 AND user_level='normal' AND user_id!='".$dashboard_user_id_sql."' THEN 1 ELSE 0 END), 0) AS inactive_users2_normal,
	COALESCE(SUM(CASE WHEN (duration > 0 OR vip_duration > 0 OR private_duration > 0) AND user_level='normal' AND upline='".$dashboard_user_id_sql."' AND user_id!='".$dashboard_user_id_sql."' THEN 1 ELSE 0 END), 0) AS active_users,
	COALESCE(SUM(CASE WHEN duration <= 0 AND vip_duration <= 0 AND private_duration <= 0 AND user_level='normal' AND upline='".$dashboard_user_id_sql."' AND user_id!='".$dashboard_user_id_sql."' THEN 1 ELSE 0 END), 0) AS inactive_users
	FROM users");
$stats_row = $db->sql_fetchrow($stats_qry);

$active_users2 = $stats_row ? (int)$stats_row['active_users2_normal'] : 0;
$inactive_users2 = $stats_row ? (int)$stats_row['inactive_users2_normal'] : 0;
$active_users = $stats_row ? (int)$stats_row['active_users'] : 0;
$inactive_users = $stats_row ? (int)$stats_row['inactive_users'] : 0;

$smarty->assign("active_users2", $active_users2);
$smarty->assign("inactive_users2", $inactive_users2);
$smarty->assign("active_users", $active_users);
$smarty->assign("inactive_users", $inactive_users);

$chk_clients2 = $dashboard_is_global_client_scope
	? ($active_users2 + $inactive_users2)
	: ($active_users + $inactive_users);
$smarty->assign("clients2", $chk_clients2);

$chk_clients = $active_users + $inactive_users;
$smarty->assign("clients", $chk_clients);

$profile_image_name = isset($profile_image_2) ? (string)$profile_image_2 : '';
if($profile_image_name == ''){
	$default = $base_url.'profile/default.png';
	$profile_image = '<img class="img-fluid px-3 px-sm-4 mt-3 mb-3" style="width: auto; height: 15rem;" src="'.$default.'" alt="'.$user_name_2.'">';
}else{
	$default = $base_url.'profile/'.$user_id_2.'/'.$profile_image_name;
	$profile_image = '<img class="img-fluid px-3 px-sm-4 mt-3 mb-3" style="width: auto; height: 15rem;" src="'.$default.'" alt="'.$user_name_2.'">';
}

$smarty->assign("credits", $credits_2);
$smarty->assign("ss_id", $ss_id_2);
$smarty->assign("user_name", $user_name_2);
$smarty->assign("user_level", $user_level_2);
$smarty->assign('profile_image', $profile_image);

//List Of Durations
$duration_sql = array();
$duration_qry = $db->sql_query("SELECT id, duration_name FROM duration ORDER BY id ASC");
while($durrows = $db->sql_fetchrow($duration_qry))
{
	$duration_sql[] = '<option value="'.urlencode($db->encryptor('encrypt',$durrows['id'])).'">'.$durrows['duration_name'].'</option>';
}
$smarty->assign("duration", $duration_sql);

//List Of Notices
$download = array();
$query = $db->sql_query("SELECT id, download_title, download_msg, download_network, download_date, download_file, download_device
	FROM download
	ORDER BY download_date DESC
	LIMIT 40");
while($row = $db->sql_fetchrow($query)){
	$id = $row['id'];
	$title = nl2br($row['download_title']);
	$msg = nl2br($row['download_msg']);
	$network = $row['download_network'];
	$dt = date("F d, Y h:i:s", strtotime($row['download_date']));
	$file = $db->base_url() . '_uploads/'.$row['download_file'];
	if($row['download_file'] == ""){
		$DLfiles = "";
	}else{
		$DLfiles = "<a href='".$file."'>Click Here to DOWNLOAD</a>";
	}
	
	if($row['download_network'] == 'NOTICE'){
	    $ico = 'icon-success';
	    $ttl = 'text-success';
	}else
	if($row['download_network'] == 'UPDATE'){
	    $ico = 'icon-primary';
	    $ttl = 'text-primary';
	}
	
	if($row['download_device'] == 'ANDROID'){
	    $icon = 'mdi mdi-android';
	}else
	if($row['download_device'] == 'IOS'){
	    $icon = 'mdi mdi-apple';
	}else
	if($row['download_device'] == 'WINDOWS'){
	    $icon = 'mdi mdi-windows';
	}else
	if($row['download_device'] == 'CONFIG'){
	    $icon = 'mdi mdi-folder-network-outline';
	}else
	if($row['download_device'] == 'OTHERS'){
	    $icon = 'mdi mdi-shield-check';
	}
	
	$download[]  = '<i class="'.$icon.' '.$ico.'"></i>';
	$download[] .= '<div class="time-item">';
	$download[] .= '<div class="item-info">';
	$download[] .= '<div class="d-flex justify-content-between align-items-center">';
	$download[] .= '<h6 class="m-0 '.$ttl.'">'.$title.'</h6>';
	$download[] .= '<span class="text-muted">'.$dt.'</span>';
	$download[] .= '</div>';
	
	$download[] .= '<p class="text-muted mt-3">';
	$download[] .= ''.$msg.'';
	$download[] .= '</p>';
	
    $download[] .= '<div>';
    $download[] .= '<span class="badge badge-soft-secondary text-secondary">'.$DLfiles.'</span>';
    $download[] .= '</div>';
    $download[] .= '</div>';
    $download[] .= '</div>';
}

$smarty->assign('download', $download);

$smarty->display("dashboard.tpl");

if($programmit_dashboard_cache_enabled && $programmit_dashboard_cache_file !== ''){
	$programmit_dashboard_html = ob_get_contents();
	if(is_string($programmit_dashboard_html) && $programmit_dashboard_html !== ''){
		@file_put_contents($programmit_dashboard_cache_file, $programmit_dashboard_html, LOCK_EX);
	}
	ob_end_flush();
}
?>
