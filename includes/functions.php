<?php
if (!defined('DOC_ROOT_PATH')) {
	define('DOC_ROOT_PATH', dirname(__DIR__) . '/');
}
require __DIR__ . '/config.php';
if (!function_exists('each')) {
	function each(&$array) {
		$key = key($array);
		if ($key === null) return false;
		$value = current($array);
		next($array);
		return array(1 => $value, 'value' => $value, 0 => $key, 'key' => $key);
	}
}
function programmit_normalize_user_cookie_value($rawValue) {
	global $db;
	$rawValue = trim((string)$rawValue);
	if ($rawValue === '') {
		return '';
	}
	$decoded = $db->decrypt_key($rawValue);
	if (!is_string($decoded) || $decoded === '') {
		return '';
	}
	$decoded = addslashes($decoded);
	return $db->encrypt_key($decoded);
}

function programmit_collect_user_cookie_candidates() {
	$candidates = array();
	if (isset($_COOKIE['user']) && trim((string)$_COOKIE['user']) !== '') {
		$candidates[] = (string)$_COOKIE['user'];
	}
	if (!empty($_SERVER['HTTP_COOKIE'])) {
		$cookieParts = explode(';', (string)$_SERVER['HTTP_COOKIE']);
		foreach ($cookieParts as $cookiePart) {
			$cookiePart = trim((string)$cookiePart);
			if (stripos($cookiePart, 'user=') === 0) {
				$candidates[] = rawurldecode(substr($cookiePart, 5));
			}
		}
	}
	return array_values(array_unique(array_filter($candidates, 'strlen')));
}

function programmit_is_admin_embed_request() {
	$embedRaw = isset($_GET['embed']) ? strtolower(trim((string)$_GET['embed'])) : '';
	return in_array($embedRaw, array('1', 'admin', 'yes'), true);
}

function programmit_admin_embed_user_cookie_candidate() {
	global $db;
	if (!programmit_is_admin_embed_request()) {
		return '';
	}
	if (!isset($_COOKIE['panel_admin_auth']) || trim((string)$_COOKIE['panel_admin_auth']) === '') {
		return '';
	}
	$raw = $db->decrypt_key((string)$_COOKIE['panel_admin_auth']);
	if (!is_string($raw) || $raw === '') {
		return '';
	}
	$parts = explode('|', $raw);
	if (!isset($parts[0], $parts[1], $parts[2])) {
		return '';
	}
	$userId = (int)$parts[0];
	$userName = trim((string)$parts[1]);
	$userPass = trim((string)$parts[2]);
	if ($userId <= 0 || $userName === '' || $userPass === '') {
		return '';
	}
	return $db->encrypt_key(addslashes($userId . '|' . $userName . '|' . $userPass));
}

$programmit_user_cookie_candidates = programmit_collect_user_cookie_candidates();
$programmit_embed_admin_user_cookie = programmit_admin_embed_user_cookie_candidate();
if ($programmit_embed_admin_user_cookie !== '') {
	$programmit_user_cookie_candidates[] = $programmit_embed_admin_user_cookie;
}
$programmit_user_cookie_candidates = array_values(array_unique(array_filter($programmit_user_cookie_candidates, 'strlen')));
$user = '';
foreach ($programmit_user_cookie_candidates as $_userCookieCandidate) {
	$_normalizedUserCookie = programmit_normalize_user_cookie_value($_userCookieCandidate);
	if ($_normalizedUserCookie !== '') {
		$user = $_normalizedUserCookie;
		break;
	}
}

function clear_auth_cookies() {
	$cookieNames = array('user', 'user_name', 'user_id', 'full_name', 'user_email', 'panel_admin_auth');
	$paths = array('/', '/serverside', '/serverside/forms');

	$scriptName = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', $_SERVER['SCRIPT_NAME']) : '';
	$scriptDir = $scriptName !== '' ? rtrim(dirname($scriptName), '/') : '';
	if($scriptDir !== '' && $scriptDir !== '.'){
		$paths[] = $scriptDir;
		$paths[] = $scriptDir . '/serverside';
		$paths[] = $scriptDir . '/serverside/forms';
	}

	if(isset($GLOBALS['db']) && method_exists($GLOBALS['db'], 'base_url')){
		$basePath = parse_url($GLOBALS['db']->base_url(), PHP_URL_PATH);
		$basePath = is_string($basePath) ? rtrim($basePath, '/') : '';
		if($basePath !== ''){
			$paths[] = $basePath;
			$paths[] = $basePath . '/serverside';
			$paths[] = $basePath . '/serverside/forms';
		}
	}

	$paths = array_unique(array_filter($paths));
	$domains = array('');
	$host = isset($_SERVER['HTTP_HOST']) ? preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST']) : '';
	if(!empty($host) && strtolower($host) !== 'localhost'){
		$domains[] = $host;
		$domains[] = '.' . $host;
	}
	$domains = array_unique($domains);

	foreach($cookieNames as $cookieName){
		foreach($paths as $cookiePath){
			setcookie($cookieName, '', time()-3600, $cookiePath);
			foreach($domains as $cookieDomain){
				if($cookieDomain !== ''){
					setcookie($cookieName, '', time()-3600, $cookiePath, $cookieDomain);
				}
			}
		}
		unset($_COOKIE[$cookieName]);
	}
}

function is_logged_in($user) {
	global $db;
	static $cache = array();

	$user = (string)$user;
	if($user === '') {
		return 0;
	}
	if(isset($GLOBALS['user']) && (string)$GLOBALS['user'] === $user && isset($GLOBALS['programmit_is_logged'])) {
		return ((int)$GLOBALS['programmit_is_logged'] === 1) ? 1 : 0;
	}
	if(array_key_exists($user, $cache)) {
		return (int)$cache[$user];
	}

	$decoded = $db->decrypt_key($user);
	if(!is_string($decoded) || $decoded === '') {
		$cache[$user] = 0;
		return 0;
	}
	$read_cookie = explode("|", $decoded);
	if(!isset($read_cookie[0], $read_cookie[1], $read_cookie[2])) {
		$cache[$user] = 0;
		return 0;
	}

	$user_id = $db->SanitizeForSQL((int)$read_cookie[0]);
	$user_name = $db->SanitizeForSQL($read_cookie[1]);
	$user_pass = $db->SanitizeForSQL($read_cookie[2]);
	$result = $db->sql_query("SELECT user_id FROM users WHERE user_id='$user_id' AND user_name='$user_name' AND user_pass='$user_pass' LIMIT 1");
	$cache[$user] = ($result && $db->sql_numrows($result) > 0) ? 1 : 0;
	return (int)$cache[$user];
}

if (!empty($programmit_user_cookie_candidates)) {
	foreach ($programmit_user_cookie_candidates as $_userCookieCandidate) {
		$_normalizedUserCookie = programmit_normalize_user_cookie_value($_userCookieCandidate);
		if ($_normalizedUserCookie === '') {
			continue;
		}
		if (is_logged_in($_normalizedUserCookie)) {
			$user = $_normalizedUserCookie;
			break;
		}
	}
}

if ($user !== '' && programmit_is_admin_embed_request() && !isset($_COOKIE['user'])) {
	if (function_exists('programmit_secure_set_cookie')) {
		programmit_secure_set_cookie('user', $user, time() + 86400, '/');
	} else {
		setcookie('user', $user, time() + 86400, '/');
	}
	$_COOKIE['user'] = $user;
}
global $user, $db;
$user_id_2 = 0;
$ss_id_2 = '';
$code_2 = '';
$user_level_2 = '';
$credits_2 = 0;
$upline_2 = '';
$auth_2 = '';
$duration_2 = 0;
$vip_duration_2 = 0;
$private_duration_2 = 0;
$private_control_2 = 0;
$full_name_2 = '';
$user_name_2 = '';
$user_email_2 = '';
$profile_image_2 = '';
$is_groupname_2 = '';
$lastlogin = '';
$programmit_is_logged = 0;
$read_cookie_2 = array();
if(!empty($user)){
	$decoded_cookie_2 = $db->decrypt_key($user);
	if(is_string($decoded_cookie_2) && $decoded_cookie_2 !== ''){
		$read_cookie_2 = explode("|", $decoded_cookie_2);
	}
}

if(isset($read_cookie_2[0], $read_cookie_2[1], $read_cookie_2[2])) {
	$user_id_2 = (int)$read_cookie_2[0];
	$user_name_2_raw = (string)$read_cookie_2[1];
	$user_pass_2_raw = (string)$read_cookie_2[2];

	$result_2 = $db->sql_query("SELECT u.user_id,
									   u.credits, 
									   u.code,
									   u.ss_id,
									   u.vip_duration,
									   u.private_duration,
									   u.private_control,
									   u.duration, 
									   u.user_level,
									   u.lastlogin,
									   u.full_name,
									   u.user_pass,
									   u.user_email,
									   u.user_name,
									   u.upline,
									   u.is_groupname,
									   up.profile_image
								FROM users u
								LEFT JOIN users_profile up ON up.profile_id=u.user_id
								WHERE u.user_id='".$db->SanitizeForSQL($user_id_2)."'
								  AND u.user_name='".$db->SanitizeForSQL($user_name_2_raw)."'
								  AND u.user_pass='".$db->SanitizeForSQL($user_pass_2_raw)."'
								LIMIT 1");
	$legal_name = 'Firenet VPN';
	$row_2 = $db->sql_fetchrow($result_2);
	if($row_2){
		$programmit_is_logged = 1;
		$user_id_2 = $db->Sanitize((int)$row_2['user_id']);
		setcookie("user_name", $row_2['user_name'], time()+3600, "/");
		$ss_id_2 = $row_2['ss_id'];
		$code_2 = $row_2['code'];
		$user_level_2 = $row_2['user_level'];
		$credits_2 = $row_2['credits'];
		$upline_2 = $row_2['upline'];
		$auth_2 = $row_2['user_pass'];
		$duration_2 = $row_2['duration'];
		$vip_duration_2 = $row_2['vip_duration'];
		$private_duration_2 = $row_2['private_duration'];
		$private_control_2 = $row_2['private_control'];
		$full_name_2 = $row_2['full_name'];
		$user_name_2 = $row_2['user_name'];
		$user_email_2 = $row_2['user_email'];
		$profile_image_2 = isset($row_2['profile_image']) ? (string)$row_2['profile_image'] : '';
		$is_groupname_2 = $row_2['is_groupname'];
		$lastlogin = date('F d, Y h:i', strtotime($row_2['lastlogin']));
	}
}
$smarty->assign("ss_id_2", $ss_id_2);
$smarty->assign("code_2", $code_2);
$smarty->assign("user_name_2", $user_name_2);
$smarty->assign("user_email", $user_email_2);
$smarty->assign("full_name_2", $full_name_2);
$smarty->assign("lastlogin", $lastlogin);
$smarty->assign("user_id_2", $user_id_2);
$smarty->assign("user_level_2", $user_level_2);
$smarty->assign("credits_2", $credits_2);
$smarty->assign("duration_2", $duration_2);
$smarty->assign('vip_duration_2', $vip_duration_2);
$smarty->assign('private_duration_2', $private_duration_2);
$smarty->assign('private_control_2', $private_control_2);
$smarty->assign("auth_2", $auth_2);
$smarty->assign("upline_2", $upline_2);
$smarty->assign("is_groupname_2", $is_groupname_2);
$smarty->assign("encrypt_user_id", $db->encryptor('encrypt',$user_id_2));
$smarty->assign("encrypt_dur", $db->encryptor('encrypt',$duration_2));
$smarty->assign("encrypt_vip", $db->encryptor('encrypt',$vip_duration_2));

$secret = $db->encryptor('encrypt',$user_id_2);
$secret = urlencode($secret);
$smarty->assign("secret", $secret);

$profile_image = $profile_image_2;
$default = $base_url.'profile/default.png';
$profile = $base_url.'profile/'.$user_id_2.'/'.$profile_image;
$_default_fs = dirname(__DIR__).'/profile/default.png';
$_profile_fs = dirname(__DIR__).'/profile/'.$user_id_2.'/'.$profile_image;

if((int)$user_id_2 === 1 || $user_level_2 == 'superadmin'){
    $credits_bal = '&infin;';
    $wallet_balance_display_2 = '&infin;';
}else{
    $credits_bal = $credits_2;
    $wallet_balance_display_2 = number_format((float)$credits_2, 0, '.', ',');
}
$smarty->assign("credits_bal", $credits_bal);
$smarty->assign("wallet_balance_display_2", $wallet_balance_display_2);
$smarty->assign("wallet_balance_2", (int)$credits_2);

if($user_level_2 == 'subreseller'){
	$rank = 'Sub Reseller';
	$rank2 ='<span class="glyphicon glyphicon-heart"></span>';
}elseif($user_level_2 == 'reseller'){
	$rank = 'Reseller';
	$rank2 ='<span class="glyphicon glyphicon-star"></span>';
}elseif($user_level_2 == 'subadmin'){
	$rank = 'Sub Administrator';
	$rank2 ='<span class="glyphicon glyphicon-star"></span>
			<span class="glyphicon glyphicon-star"></span>
			<span class="glyphicon glyphicon-star"></span>';
}elseif($user_level_2 == 'administrator'){
	$rank = '[Administrator]';
	$rank2 ='<span class="glyphicon glyphicon-star"></span>
	        <span class="glyphicon glyphicon-star"></span>
			<span class="glyphicon glyphicon-globe"></span>
			<span class="glyphicon glyphicon-star"></span>
			<span class="glyphicon glyphicon-star"></span>';
}elseif($user_level_2 == 'superadmin'){
	$rank = '[Super Administrator/developer]';
	$rank2 ='<span class="glyphicon glyphicon-star"></span>
			<span class="glyphicon glyphicon-star"></span>
			<span class="glyphicon glyphicon-star"></span>
			<span class="glyphicon glyphicon-star"></span>
			<span class="glyphicon glyphicon-star"></span>';
}else{
	$rank = 'Member Only';
	$rank2 ='<span class="glyphicon glyphicon-user"></span>';
}
$smarty->assign("rank", $rank);

if($is_groupname_2 == 'free'){
	$rank2 = 3;
}
$smarty->assign("rank2", $rank2);

if($profile_image === '' || !is_file($_profile_fs)){
	$avatar = '<img class="img-circle" height="20" width="20" src="'.$default.'" alt="">';
}else{
	$avatar = '<img class="img-circle" height="20" width="20" src="'.$profile.'" alt="">';
}
$smarty->assign("avatar", $avatar);

if(!$programmit_is_logged) {
	$hasAuthCookies = false;
	foreach(array('user', 'user_name', 'user_id', 'full_name', 'user_email', 'panel_admin_auth') as $_cookieName){
		if(isset($_COOKIE[$_cookieName]) && (string)$_COOKIE[$_cookieName] !== ''){
			$hasAuthCookies = true;
			break;
		}
	}
	if($hasAuthCookies){
		clear_auth_cookies();
	}
	$user = "";
	unset($user);
}

function programmit_is_panel_restricted_user() {
	global $db, $user_id_2, $user_level_2, $credits_2;

	if((int)$user_id_2 <= 0){
		return false;
	}

	if(function_exists('programmit_panel_access_is_allowed')){
		return !programmit_panel_access_is_allowed($db, (int)$user_id_2, (string)$user_level_2, (int)$credits_2);
	}

	if((int)$user_id_2 === 1 || $user_level_2 === 'superadmin'){
		return false;
	}

	return ((int)$credits_2 <= 0);
}

function programmit_can_access_panel_page($page) {
	$page = strtolower(trim((string)$page));
	if($page === ''){
		return true;
	}

	$allowed = array(
		'dashboard',
		'my-profile',
		'finance-add',
		'finance-history',
		'finance-checkout',
		'finance-methods',
		'finance-admin',
		'finance-webhook',
		'support',
		'supportticket',
		'update',
		'access-lock',
		'logout'
	);

	return in_array($page, $allowed, true);
}

$panel_restricted_2 = programmit_is_panel_restricted_user();
$panel_lock_reason_2 = '';
if($panel_restricted_2 && isset($user_id_2) && (int)$user_id_2 > 0 && function_exists('programmit_panel_access_lock_reason')){
	$panel_lock_reason_2 = programmit_panel_access_lock_reason($db, (int)$user_id_2);
}
$smarty->assign("panel_restricted_2", $panel_restricted_2);
$smarty->assign("panel_lock_reason_2", $panel_lock_reason_2);
if (function_exists('programmit_panel_assign_smarty_flags')) {
	programmit_panel_assign_smarty_flags($smarty, (int)$user_id_2, (string)$user_level_2);
}

function chkSession() {
	global $programmit_is_logged;
	if(!$programmit_is_logged) {
		$isAjax = false;
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
			$isAjax = (strcasecmp((string)$_SERVER['HTTP_X_REQUESTED_WITH'], 'XMLHttpRequest') === 0);
		}
		if (!$isAjax && isset($_SERVER['HTTP_ACCEPT'])) {
			$isAjax = (stripos((string)$_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
		}
		if ($isAjax) {
			if (!headers_sent()) {
				http_response_code(401);
				header('Content-Type: text/html; charset=UTF-8');
			}
			echo "<div class='alert alert-danger'><strong>Tu sesion ha expirado. Inicia sesion nuevamente.</strong></div>";
			exit;
		}
		header("Location: ".$GLOBALS['db']->base_url()."index.php?p=login");
		exit;
	}
}

function calc_time($seconds) {
	$days = (int)($seconds / 86400);
	$seconds -= ($days * 86400);
	if ($seconds) {
		$hours = (int)($seconds / 3600);
		$seconds -= ($hours * 3600);
	}
	if ($seconds) {
		$minutes = (int)($seconds / 60);
		$seconds -= ($minutes * 60);
	}
	$time = array('days'=>(int)$days,
			'hours'=>(int)$hours,
			'minutes'=>(int)$minutes,
			'seconds'=>(int)$seconds);
	return $time;
}

function get_upline_name_cached($uplineId) {
	global $db;
	static $uplineNameCache = array();

	$uplineKey = (string)(int)$uplineId;
	if ($uplineKey === '' || $uplineKey === '0') {
		return '';
	}

	if (array_key_exists($uplineKey, $uplineNameCache)) {
		return $uplineNameCache[$uplineKey];
	}

	$safeId = $db->SanitizeForSQL($uplineKey);
	$qry = $db->sql_query("SELECT user_name FROM users WHERE user_id='".$safeId."' LIMIT 1");
	$row = $db->sql_fetchrow($qry);
	$uplineNameCache[$uplineKey] = ($row && isset($row['user_name'])) ? $row['user_name'] : '';

	return $uplineNameCache[$uplineKey];
}

function programmit_can_use_mduration() {
	global $user_id_2, $user_level_2;

	return ((int)$user_id_2 === 1 || in_array($user_level_2, array('superadmin', 'administrator', 'subadmin', 'reseller', 'subreseller'), true));
}

function programmit_mduration_markup($userId, $userCode, $variant = 'sidebar') {
	if (!programmit_can_use_mduration()) {
		return '';
	}

	$userId = (int)$userId;
	$userCode = (int)$userCode;

	if ($variant === 'dropdown') {
		return '<button type="button" class="btn btn-info btn-block waves-effect waves-light text-left" href="javascript:void(0)" data-toggle="tooltip" title="Apply Self Duration" onclick="getDuration('.$userId.','.$userCode.')"><i class="glyphicon glyphicon-time"></i> Apply Self Duration</button>';
	}

	if ($variant === 'compact') {
		return '<a class="btn btn-block btn-success btn-sm" href="javascript:void(0)" data-toggle="tooltip" title="Apply Self Duration" onclick="getDuration('.$userId.','.$userCode.')"><i class="glyphicon glyphicon-time"></i></a>';
	}

	return '<button type="button" class="btn btn-info" onclick="getDuration('.$userId.','.$userCode.')"><a><i class="fas fa-user-clock" aria-hidden="true"></i><span>MDURATION</span></a></button>';
}

function programmit_mduration_button($userId, $userCode) {
	return programmit_mduration_markup($userId, $userCode, 'sidebar');
}

function programmit_mduration_dropdown_button($userId, $userCode) {
	return programmit_mduration_markup($userId, $userCode, 'dropdown');
}

function programmit_mduration_compact_button($userId, $userCode) {
	return programmit_mduration_markup($userId, $userCode, 'compact');
}

function programmit_translate_duration_name($durationName) {
	$durationName = trim((string)$durationName);
	if ($durationName === '') {
		return '';
	}

	$isNegative = false;
	if (strpos($durationName, '-') === 0) {
		$isNegative = true;
		$durationName = ltrim(substr($durationName, 1));
	}

	if (preg_match('/^(\d+)\s+(Hour|Hours|Day|Days)$/i', $durationName, $matches)) {
		$amount = (int)$matches[1];
		$unit = strtolower($matches[2]);
		$translatedUnit = '';

		if ($unit === 'hour' || $unit === 'hours') {
			$translatedUnit = ($amount === 1) ? 'Hora' : 'Horas';
		} elseif ($unit === 'day' || $unit === 'days') {
			$translatedUnit = ($amount === 1) ? 'Dia' : 'Dias';
		}

		if ($translatedUnit !== '') {
			return ($isNegative ? '-' : '') . $amount . ' ' . $translatedUnit;
		}
	}

	return ($isNegative ? '-' : '') . $durationName;
}

function programmit_duration_select_options() {
	global $db, $user_id_2, $user_level_2;
	static $cache = array();

	$includeNegative = ((int)$user_id_2 === 1 || $user_level_2 === 'superadmin');
	$cacheKey = $includeNegative ? 'all' : 'positive';

	if (isset($cache[$cacheKey])) {
		return $cache[$cacheKey];
	}

	$sql = "SELECT id, duration_name FROM duration";
	if (!$includeNegative) {
		$sql .= " WHERE duration_time > 0";
	}
	$sql .= " ORDER BY id ASC";

	$options = array();
	$qry = $db->sql_query($sql);
	if ($qry) {
		while ($row = $db->sql_fetchrow($qry)) {
			$options[] = '<option value="' . urlencode($db->encryptor('encrypt', $row['id'])) . '">' . programmit_translate_duration_name($row['duration_name']) . '</option>';
		}
	}

	$cache[$cacheKey] = $options;
	return $options;
}

function table_exists_cached($tableName) {
	global $db;
	static $tableCache = array();

	$key = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$tableName);
	if ($key === '') {
		return false;
	}

	if (array_key_exists($key, $tableCache)) {
		return $tableCache[$key];
	}

	$qry = $db->sql_query("SHOW TABLES LIKE '".$db->SanitizeForSQL($key)."'");
	$tableCache[$key] = ($qry && $db->sql_numrows($qry) > 0);

	return $tableCache[$key];
}

function programmit_user_metric_cache_path($userId, $metricKey) {
	$userId = (int)$userId;
	$metricKey = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$metricKey);
	if ($userId <= 0 || $metricKey === '') {
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
	return $cacheDir . DIRECTORY_SEPARATOR . 'metric_' . $userId . '_' . strtolower($metricKey) . '.json';
}

function programmit_user_metric_cache_get($userId, $metricKey, $ttlSeconds = 20) {
	$file = programmit_user_metric_cache_path($userId, $metricKey);
	if ($file === '') {
		return null;
	}
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
	if (!is_array($data) || !isset($data['value'])) {
		return null;
	}
	return (int)$data['value'];
}

function programmit_user_metric_cache_set($userId, $metricKey, $value) {
	$file = programmit_user_metric_cache_path($userId, $metricKey);
	if ($file === '') {
		return false;
	}
	$payload = json_encode(array('value' => (int)$value), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	if (!is_string($payload) || $payload === '') {
		return false;
	}
	return (bool)@file_put_contents($file, $payload, LOCK_EX);
}

function programmit_runtime_table_exists($db, $tableName) {
	static $cache = array();

	$tableName = strtolower(trim((string)$tableName));
	if ($tableName === '' || !is_object($db) || !method_exists($db, 'sql_query') || !method_exists($db, 'sql_numrows') || !method_exists($db, 'SanitizeForSQL')) {
		return false;
	}

	$driver = isset($GLOBALS['DB_driver']) ? strtolower(trim((string)$GLOBALS['DB_driver'])) : 'mysql';
	$dbName = isset($GLOBALS['DB_name']) ? trim((string)$GLOBALS['DB_name']) : '';
	$dbSchema = isset($GLOBALS['DB_schema']) ? trim((string)$GLOBALS['DB_schema']) : '';
	$cacheKey = $driver . '|' . $dbName . '|' . $dbSchema . '|' . $tableName;
	if (array_key_exists($cacheKey, $cache)) {
		return (bool)$cache[$cacheKey];
	}

	$exists = false;
	if ($driver === 'pgsql') {
		if ($dbSchema === '') {
			$dbSchema = 'public';
		}
		$result = $db->sql_query(
			"SELECT table_name
			FROM information_schema.tables
			WHERE table_schema='" . $db->SanitizeForSQL($dbSchema) . "'
			AND table_name='" . $db->SanitizeForSQL($tableName) . "'
			LIMIT 1"
		);
	} else {
		if ($dbName === '') {
			$dbName = 'programm_panel';
		}
		$result = $db->sql_query(
			"SELECT table_name
			FROM information_schema.tables
			WHERE table_schema='" . $db->SanitizeForSQL($dbName) . "'
			AND table_name='" . $db->SanitizeForSQL($tableName) . "'
			LIMIT 1"
		);
	}

	if ($result && $db->sql_numrows($result) > 0) {
		$exists = true;
	}

	$cache[$cacheKey] = $exists;
	return $exists;
}

function ran_code() {
	$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	srand((double)microtime()*1000000);
	$i = 0;
	while ($i <= 4)
	{
		$num = rand() % 33;
		$tmp = substr($chars, $num, 1);
		$pwd = $pwd . $tmp;
		$i++;
	}
	return $pwd;
}

$dur = $db->calc_time($duration_2);
$dur2 = $db->calc_time($vip_duration_2);
$dur3 = $db->calc_time($private_duration_2);

$pre_duration = "". $dur['days'] . " Day(s) | " . $dur['hours'] . " Hour(s) and " . $dur['minutes'] . " Minute(s)";
$vip_duration = "". $dur2['days'] . " Day(s) | " . $dur2['hours'] . " Hour(s) and " . $dur2['minutes'] . " Minute(s)";
$pri_duration = "". $dur3['days'] . " Day(s) | " . $dur3['hours'] . " Hour(s) and " . $dur3['minutes'] . " Minute(s)";

$pre_days = $dur['days'];
$pre_hours = $dur['hours'];
$pre_minutes = $dur['minutes'];

$smarty->assign("pre_days", $pre_days);
$smarty->assign("pre_hours", $pre_hours);
$smarty->assign("pre_minutes", $pre_minutes);

$vip_days = $dur2['days'];
$vip_hours = $dur2['hours'];
$vip_minutes = $dur2['minutes'];

$smarty->assign("vip_days", $vip_days);
$smarty->assign("vip_hours", $vip_hours);
$smarty->assign("vip_minutes", $vip_minutes);

$pri_days = $dur3['days'];
$pri_hours = $dur3['hours'];
$pri_minutes = $dur3['minutes'];

$smarty->assign("pri_days", $pri_days);
$smarty->assign("pri_hours", $pri_hours);
$smarty->assign("pri_minutes", $pri_minutes);

$smarty->assign("pre_duration", $pre_duration);
$smarty->assign("vip_duration", $vip_duration);
$smarty->assign("pri_duration", $pri_duration);

//chat_status='seen' AND chat_id1='$user_id_2' OR
$chat_count = 0;
if((int)$user_id_2 > 0){
	$chat_cached = programmit_user_metric_cache_get((int)$user_id_2, 'chat_seen', 20);
	if($chat_cached !== null){
		$chat_count = (int)$chat_cached;
	}else if(programmit_runtime_table_exists($db, 'chat')){
		$chat_support = $db->sql_query("SELECT COUNT(*) AS cnt FROM chat WHERE chat_status='seen' AND chat_id2 = '".$db->SanitizeForSQL($user_id_2)."'");
		if($chat_support){
			$chat_row = $db->sql_fetchrow($chat_support);
			$chat_count = isset($chat_row['cnt']) ? (int)$chat_row['cnt'] : 0;
		}
		programmit_user_metric_cache_set((int)$user_id_2, 'chat_seen', (int)$chat_count);
	}else{
		programmit_user_metric_cache_set((int)$user_id_2, 'chat_seen', 0);
	}
}
if($chat_count > 0){
	$alert_chat = '<span class="badge badge-info up">'.$chat_count.'</span>';
}else{
	$alert_chat = '';
}
$smarty->assign("alert_chat", $alert_chat);

$staff_count = 0;
if((int)$user_id_2 > 0){
	$staffCacheKey = (($user_id_2 == 1 || $user_id_2 == 5) ? 'support_staff' : 'support_user');
	$staff_cached = programmit_user_metric_cache_get((int)$user_id_2, $staffCacheKey, 20);
	if($staff_cached !== null){
		$staff_count = (int)$staff_cached;
	}else if(programmit_runtime_table_exists($db, 'support_ticket')){
		if($user_id_2 == 1 || $user_id_2 == 5){
			$staff_support = $db->sql_query("SELECT COUNT(*) AS cnt FROM support_ticket WHERE ticket_status IN ('customer-reply','open')");
		}else{
			$staff_support = $db->sql_query("SELECT COUNT(*) AS cnt FROM support_ticket WHERE ticket_id_user='".$db->SanitizeForSQL($user_id_2)."' AND ticket_status='answered'");
		}
		if($staff_support){
			$staff_row = $db->sql_fetchrow($staff_support);
			$staff_count = isset($staff_row['cnt']) ? (int)$staff_row['cnt'] : 0;
		}
		programmit_user_metric_cache_set((int)$user_id_2, $staffCacheKey, (int)$staff_count);
	}else{
		programmit_user_metric_cache_set((int)$user_id_2, $staffCacheKey, 0);
	}
}
if($staff_count > 0){
	$alert_message = '<span class="label label-round label-info">'.$staff_count.'</span>';
}else{
	$alert_message = '';
}
$smarty->assign("alert_message", $alert_message);
?>

