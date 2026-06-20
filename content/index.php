<?php
function programmit_home_cache_file_path() {
	$tmpDir = rtrim((string)sys_get_temp_dir(), '/\\');
	if ($tmpDir === '') {
		$tmpDir = '/tmp';
	}
	$cacheDir = $tmpDir . DIRECTORY_SEPARATOR . 'programmit_bootstrap';
	if (!is_dir($cacheDir)) {
		@mkdir($cacheDir, 0775, true);
	}
	return $cacheDir . DIRECTORY_SEPARATOR . 'home_public_cache.json';
}

function programmit_home_cache_read($ttlSeconds = 60) {
	$file = programmit_home_cache_file_path();
	clearstatcache(true, $file);
	if (!is_file($file)) {
		return null;
	}
	$age = time() - (int)@filemtime($file);
	if ($age < 0 || $age > (int)$ttlSeconds) {
		return null;
	}
	$raw = @file_get_contents($file);
	if (!is_string($raw) || $raw === '') {
		return null;
	}
	$data = json_decode($raw, true);
	if (!is_array($data)) {
		return null;
	}
	return $data;
}

function programmit_home_cache_write($payload) {
	if (!is_array($payload)) {
		return false;
	}
	$file = programmit_home_cache_file_path();
	$json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	if (!is_string($json) || $json === '') {
		return false;
	}
	return (bool)@file_put_contents($file, $json, LOCK_EX);
}

$canUsePublicCache = ((int)$user_id_2 <= 0);
if ($canUsePublicCache) {
	$cachedHome = programmit_home_cache_read(60);
	if (is_array($cachedHome)) {
		$smarty->assign("count_servers", (int)($cachedHome['count_servers'] ?? 0));
		$smarty->assign("count", (int)($cachedHome['count'] ?? 0));
		$smarty->assign("resellers", (int)($cachedHome['resellers'] ?? 0));
		$smarty->assign("suspended", (int)($cachedHome['suspended'] ?? 0));
		$smarty->assign('downloads', (isset($cachedHome['downloads']) && is_array($cachedHome['downloads'])) ? $cachedHome['downloads'] : array());
		$smarty->display("index.tpl");
		return;
	}
}

$statsQuery = $db->sql_query("SELECT
	(SELECT COUNT(*) FROM server_list WHERE status=1) AS count_servers,
	(SELECT COUNT(*) FROM users) AS count_users,
	(SELECT COUNT(*) FROM users WHERE user_level='reseller') AS count_resellers,
	(SELECT COUNT(*) FROM users WHERE is_active=0 AND is_offense > 0) AS count_suspended");
$statsRow = $db->sql_fetchrow($statsQuery);

$count_servers = $statsRow ? (int)$statsRow['count_servers'] : 0;
$countmember = $statsRow ? (int)$statsRow['count_users'] : 0;
$reseller_users = $statsRow ? (int)$statsRow['count_resellers'] : 0;
$suspendedmember = $statsRow ? (int)$statsRow['count_suspended'] : 0;

$smarty->assign("count_servers", $count_servers);
$smarty->assign("count", $countmember);
$smarty->assign("resellers", $reseller_users);
$smarty->assign("suspended", $suspendedmember);

if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator' 
	|| $user_level_2 == 'subadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller'){
	$dl_query = $db->sql_query("SELECT download_title, download_msg, download_date, download_file, download_device, download_network
		FROM download
		ORDER BY download_date DESC");
}else{
	$dl_query = $db->sql_query("SELECT download_title, download_msg, download_date, download_file, download_device, download_network
		FROM download
		WHERE download_category='public'
		ORDER BY download_date DESC");
}

$downloads = array();
while($rows = $db->sql_fetchrow($dl_query))
{
	$title = nl2br($rows['download_title']);
	$msg = nl2br($rows['download_msg']);
	$dt = date("F d, Y h:i:s", strtotime($rows['download_date']));
	$file = $db->base_url() . '_uploads/'.$rows['download_file'];
	$device =  $rows['download_device'];
	
	if($rows['download_file'] == ""){
		$DLfiles = "";
	}else{
		$DLfiles = "<a class='text-primary' href='".$file."'>Click Here to DOWNLOAD</a>";
	}
	
	if($rows['download_network'] == 'NOTICE'){
	    $ico = 'icon-success';
	    $ttl = 'text-success';
	}else
	if($rows['download_network'] == 'UPDATE'){
	    $ico = 'icon-primary';
	    $ttl = 'text-primary';
	}
	
	if($rows['download_device'] == 'ANDROID'){
	    $icon = '/images/info.png';
	}else
	if($rows['download_device'] == 'IOS'){
	    $icon = '/images/info.png';
	}else
	if($rows['download_device'] == 'WINDOWS'){
	    $icon = '/images/info.png';
	}else
	if($rows['download_device'] == 'CONFIG'){
	    $icon = '/images/info.png';
	}else
	if($rows['download_device'] == 'OTHERS'){
	    $icon = '/images/info.png';
	}else{
		$icon = '/images/info.png';
	}
    $downloads[]  = '<div class="slider__item">';
    $downloads[] .= '<div class="user-thumbnail">';
    $downloads[] .= '<img src="'.$icon.'" class="img-fluid">';
    $downloads[] .= '</div>';
    $downloads[] .= '<blockquote class="blockquote text-center">';
    $downloads[] .= '<h4 class="text-light text-uppercase">'.$title.'</h4>';
    $downloads[] .= '<h6 class="mb-0 text-light">'.$msg.'</h6><h6><br>'.$DLfiles.'</h6>';
    $downloads[] .= '<div></div>';
    $downloads[] .= '</blockquote>';
    $downloads[] .= '</div>';
}

if (!empty($downloads)) {
    $smarty->assign('downloads', $downloads);
}

if ($canUsePublicCache) {
	programmit_home_cache_write(array(
		'count_servers' => $count_servers,
		'count' => $countmember,
		'resellers' => $reseller_users,
		'suspended' => $suspendedmember,
		'downloads' => $downloads
	));
}

$smarty->display("index.tpl");
?>
