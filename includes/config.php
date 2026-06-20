<?php
//skip the functions file if somebody call it directly from the browser.
if (preg_match("/config.php/i", $_SERVER['SCRIPT_NAME'])) {
    Header("Location: /"); die();
}

require __DIR__ . '/smarty/Smarty.class.php';

$smarty = new Smarty;
if(isset($_SERVER['HTTP_HOST'])){
	$hostOnly = preg_replace('/:\d+$/', '', (string)$_SERVER['HTTP_HOST']);
	if($hostOnly === 'localhost' || $hostOnly === '127.0.0.1' || DIRECTORY_SEPARATOR === '\\'){
		$smarty->compile_check = true;
		$smarty->force_compile = true;
		$smarty->caching = false;
		if (method_exists($smarty, 'clearCompiledTemplate')) {
			$smarty->clearCompiledTemplate();
		}
	}
}

include __DIR__ . '/db_config.php';
require __DIR__ . '/mysql.class.php';
$db = new mysql_db();
$db->InitDB($DB_host,$DB_user,$DB_pass,$DB_name);
if (isset($mysqli) && is_object($mysqli) && method_exists($mysqli, 'query')) {
	$db->connection = $mysqli;
}
if(!$db->DBLogin()){
	die('Database Login failed!');
}
$mysqli = $db->connection;
$db->SetWebsiteName('panel.programmit.com');
$db->SetWebsiteTitle('PROGRAMMIT PANEL');
require_once __DIR__ . '/panel_access.php';
programmit_panel_access_ensure_table($db);
programmit_panel_access_guard_forms($db);
require_once __DIR__ . '/panel_roles.php';
require_once __DIR__ . '/finance.php';
programmit_finance_ensure_tables($db);
require_once __DIR__ . '/saas.php';
programmit_saas_ensure_tables($db);
require_once __DIR__ . '/client_defaults.php';
require_once __DIR__ . '/vpn_control.php';
programmit_vpn_ensure_tables($db);
require_once __DIR__ . '/control_security.php';
programmit_control_security_bootstrap($db);
$programmit_control_is_host = (function_exists('programmit_control_is_host') && programmit_control_is_host($db));
$smarty->assign('control_is_host', $programmit_control_is_host ? 1 : 0);
$programmit_saas_ctx = programmit_saas_get_tenant_context($db);
programmit_saas_apply_context($db, $smarty, $programmit_saas_ctx);
$base_url = $db->base_url();
$programmit_current_host = function_exists('programmit_saas_current_host') ? (string)programmit_saas_current_host() : (string)(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
$programmit_control_host = function_exists('programmit_saas_get_control_host') ? (string)programmit_saas_get_control_host($db) : 'panel.programmit.com';
$programmit_is_control_brand_host = ($programmit_current_host !== '' && $programmit_control_host !== '' && strcasecmp($programmit_current_host, $programmit_control_host) === 0);

$panel_brand_name = trim((string)programmit_saas_get_setting($db, 'panel_brand_name', 'panel.programmit.com'));
if ($panel_brand_name === '') { $panel_brand_name = 'panel.programmit.com'; }
$panel_site_title = trim((string)programmit_saas_get_setting($db, 'panel_site_title', 'PROGRAMMIT PANEL'));
if ($panel_site_title === '') { $panel_site_title = 'PROGRAMMIT PANEL'; }
$panel_logo_raw = trim((string)programmit_saas_get_setting($db, 'panel_logo_url', ''));
$panel_login_logo_raw = trim((string)programmit_saas_get_setting($db, 'panel_login_logo_url', ''));
$panel_favicon_raw = trim((string)programmit_saas_get_setting($db, 'panel_favicon_url', ''));

$programmit_resolve_asset = function($raw, $fallback) use ($base_url) {
	$value = trim((string)$raw);
	if ($value === '') { $value = trim((string)$fallback); }
	if ($value === '') { return ''; }
	if (preg_match('#^https?://#i', $value) === 1 || strpos($value, '//') === 0) {
		return $value;
	}
	return rtrim((string)$base_url, '/') . '/' . ltrim($value, '/');
};

$programmit_version_asset = function($resolvedUrl, $rawOrFallbackPath) {
	$resolvedUrl = trim((string)$resolvedUrl);
	if ($resolvedUrl === '') {
		return '';
	}

	$version = '';
	$rawPath = trim((string)$rawOrFallbackPath);
	if ($rawPath !== '' && preg_match('#^https?://#i', $rawPath) !== 1 && strpos($rawPath, '//') !== 0) {
		$localPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . ltrim(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $rawPath), DIRECTORY_SEPARATOR);
		if (is_file($localPath)) {
			$mtime = @filemtime($localPath);
			if ($mtime) {
				$version = (string)$mtime;
			}
		}
	}

	if ($version === '') {
		$version = substr(md5($resolvedUrl), 0, 12);
	}

	$separator = (strpos($resolvedUrl, '?') !== false) ? '&' : '?';
	return $resolvedUrl . $separator . 'v=' . rawurlencode($version);
};

$panel_logo_url = $programmit_resolve_asset($panel_logo_raw, 'logo/icon_panel.png');
$panel_login_logo_url = $programmit_resolve_asset(($panel_login_logo_raw !== '' ? $panel_login_logo_raw : $panel_logo_raw), 'logo/icon_panel.png');
$panel_favicon_source = ($panel_favicon_raw !== '' ? $panel_favicon_raw : 'logo/favicon2.png');
$panel_favicon_url = $programmit_version_asset($programmit_resolve_asset($panel_favicon_raw, 'logo/favicon2.png'), $panel_favicon_source);

if ($programmit_is_control_brand_host || strcasecmp($programmit_current_host, 'panel.programmit.com') === 0) {
	$db->SetWebsiteName($panel_brand_name);
	$db->SetWebsiteTitle($panel_site_title);
}

$smarty->assign('panel_brand_name', $panel_brand_name);
$smarty->assign('panel_site_title', $panel_site_title);
$smarty->assign('panel_logo_url', $panel_logo_url);
$smarty->assign('panel_login_logo_url', $panel_login_logo_url);
$smarty->assign('panel_favicon_url', $panel_favicon_url);
$smarty->assign('client_default_password_enabled', (function_exists('programmit_client_default_password_is_configured') && programmit_client_default_password_is_configured($db)) ? 1 : 0);

$ua = $db->getBrowser();
$browser = "" . $ua['name'] . " " . $ua['version'] . "" ; 
$ipadd = "" . $db->get_client_ip() . ""; 
$smarty->assign('getIP', $ipadd);
$smarty->assign('getBrowser', $browser);
$smarty->assign('base_url', $db->base_url());
$smarty->assign('GetSelfScript', $db->GetSelfScript());
$smarty->assign('siteTitle', $db->siteTitle);
$smarty->assign('sitename', $db->sitename);

$date = new DateTime();
$current_timestamp = $date->getTimestamp();
$smarty->assign('current_timestamp', $current_timestamp);

$premium_encrypt = $db->encryptor('encrypt', 'premium');
$smarty->assign("premium_encrypt", $premium_encrypt);

$vip_encrypt = $db->encryptor('encrypt', 'vip');
$smarty->assign("vip_encrypt", $vip_encrypt);

$private_encrypt = $db->encryptor('encrypt', 'private');
$smarty->assign("private_encrypt", $private_encrypt);

$role_encrypt = $db->encryptor('encrypt', 'role');
$smarty->assign("role_encrypt", $role_encrypt);

$add_encrypt = $db->encryptor('encrypt', 'add');
$smarty->assign("add_encrypt", $add_encrypt);

$substract_encrypt = $db->encryptor('encrypt', 'substract');
$smarty->assign("substract_encrypt", $substract_encrypt);

$login_encrypt = $db->encryptor('encrypt', 'Login Account');
$smarty->assign("login_encrypt", $login_encrypt);

$unfreeze_encrypt = $db->encryptor('encrypt', 'Unfreeze Account');
$smarty->assign("unfreeze_encrypt", $unfreeze_encrypt);

$encrypt_days = '';
$encrypt_hours = '';
$domain_list = '';

for($i=0; $i<366; $i++)
{
	$encrypt_days .= '<option value="'.base64_encode($db->encrypt_key($db->encrypt_key($i))).'">';
	$encrypt_days .= $i;
	$encrypt_days .= '</option>';
	$smarty->assign("encrypt_days", $encrypt_days);
}

for($i=0; $i<25; $i++)
{
	$encrypt_hours .= '<option value="'.urlencode($db->encrypt_key($db->encrypt_key($i))).'">';
	$encrypt_hours .= $i;
	$encrypt_hours .= '</option>';
	$smarty->assign("encrypt_hours", $encrypt_hours);
}

$dns_list_array=array(
			1=>array("octaviavpn.com","884fb13c9e199c4fe0f5e40dd71f839b","20f989db3c168115b85e51e03b07b9c7eb85b","harulenz@gmail.com"),
			2=>array("octaviavpn.info","744641ac0aedb9a8e48262dd6883de03","20f989db3c168115b85e51e03b07b9c7eb85b","harulenz@gmail.com"),
			3=>array("octaviavpn.net","5d4b5f36e83e90745aa0f4323fd04f16","20f989db3c168115b85e51e03b07b9c7eb85b","harulenz@gmail.com")
	);

for($row = 1;$row < 101; $row++){
		if(!empty($dns_list_array[$row][0])){
		$domain_list .= '<option value="'.$dns_list_array[$row][0].'">';
		$domain_list .= $dns_list_array[$row][0];
		$domain_list .= '</option>';
		} else {
			break;
		}
	}
	
$smarty->assign('domain_list', $domain_list);
$smarty->assign('dns_list', $dns_list_array);

$year_now = date('Y');
$smarty->assign("year_now", $year_now);
?>
