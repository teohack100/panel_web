<?php
require_once __DIR__ . '/../includes/social_auth.php';

if (is_logged_in($user)) {
	header("Location: " . $db->base_url() . "index.php?p=dashboard");
	exit;
}

$provider = programmit_social_provider(isset($_GET['provider']) ? $_GET['provider'] : '');
if ($provider === '') {
	header("Location: " . programmit_social_login_url($db, 'bad_provider'));
	exit;
}

if (!programmit_social_host_allows_oauth($db)) {
	programmit_social_audit($db, $provider, 'start', 'host_blocked', 'oauth_blocked_on_control_host');
	header("Location: " . programmit_social_login_url($db, 'host_blocked'));
	exit;
}

programmit_social_ensure_tables($db);
$cfg = programmit_social_provider_config($db, $provider);
if (!$cfg || empty($cfg['enabled'])) {
	programmit_social_audit($db, $provider, 'start', 'disabled', 'provider_disabled');
	header("Location: " . programmit_social_login_url($db, 'disabled'));
	exit;
}

if ($provider === 'apple') {
	programmit_social_audit($db, $provider, 'start', 'not_ready', 'apple_not_ready');
	header("Location: " . programmit_social_login_url($db, 'apple_not_ready'));
	exit;
}

if ($cfg['client_id'] === '' || $cfg['client_secret'] === '') {
	programmit_social_audit($db, $provider, 'start', 'bad_config', 'missing_client_keys');
	header("Location: " . programmit_social_login_url($db, 'config_missing'));
	exit;
}

$redirectUri = programmit_social_callback_url($db, $provider);
$state = programmit_social_create_oauth_session($db, $provider, $redirectUri, '');
if ($state === '') {
	programmit_social_audit($db, $provider, 'start', 'error', 'session_create_failed');
	header("Location: " . programmit_social_login_url($db, 'session_error'));
	exit;
}

if ($provider === 'google') {
	$params = array(
		'client_id' => $cfg['client_id'],
		'redirect_uri' => $redirectUri,
		'response_type' => 'code',
		'scope' => $cfg['scope'] !== '' ? $cfg['scope'] : 'openid email profile',
		'state' => $state,
		'prompt' => 'select_account'
	);
	programmit_social_audit($db, $provider, 'start', 'ok', 'redirect_google');
	$url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
	header("Location: " . $url);
	exit;
}

if ($provider === 'facebook') {
	$params = array(
		'client_id' => $cfg['client_id'],
		'redirect_uri' => $redirectUri,
		'response_type' => 'code',
		'scope' => $cfg['scope'] !== '' ? $cfg['scope'] : 'email public_profile',
		'state' => $state
	);
	programmit_social_audit($db, $provider, 'start', 'ok', 'redirect_facebook');
	$url = 'https://www.facebook.com/v20.0/dialog/oauth?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
	header("Location: " . $url);
	exit;
}

programmit_social_audit($db, $provider, 'start', 'error', 'unknown_provider');
header("Location: " . programmit_social_login_url($db, 'bad_provider'));
exit;
