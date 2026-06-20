<?php
require_once __DIR__ . '/../includes/social_auth.php';

function programmit_social_debug_detail($resp, $fallback){
	$fallback = trim((string)$fallback);
	if (!is_array($resp)) {
		return substr($fallback, 0, 240);
	}
	$status = isset($resp['status']) ? (int)$resp['status'] : 0;
	$body = isset($resp['body']) ? (string)$resp['body'] : '';
	$data = json_decode($body, true);
	if (is_array($data)) {
		$err = isset($data['error']) ? trim((string)$data['error']) : '';
		$desc = isset($data['error_description']) ? trim((string)$data['error_description']) : '';
		$msg = isset($data['message']) ? trim((string)$data['message']) : '';
		$parts = array();
		$parts[] = 'http=' . $status;
		if ($err !== '') {
			$parts[] = 'err=' . $err;
		}
		if ($desc !== '') {
			$parts[] = 'desc=' . $desc;
		} elseif ($msg !== '') {
			$parts[] = 'msg=' . $msg;
		}
		$joined = trim(implode(' ', $parts));
		if ($joined !== '') {
			return substr($joined, 0, 240);
		}
	}
	if ($fallback !== '') {
		return substr($fallback . ' http=' . $status, 0, 240);
	}
	return substr('http=' . $status, 0, 240);
}

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
	programmit_social_audit($db, $provider, 'callback', 'host_blocked', 'oauth_blocked_on_control_host');
	header("Location: " . programmit_social_login_url($db, 'host_blocked'));
	exit;
}

programmit_social_ensure_tables($db);
$cfg = programmit_social_provider_config($db, $provider);
if (!$cfg || empty($cfg['enabled'])) {
	programmit_social_audit($db, $provider, 'callback', 'disabled', 'provider_disabled');
	header("Location: " . programmit_social_login_url($db, 'disabled'));
	exit;
}

if ($provider === 'apple') {
	programmit_social_audit($db, $provider, 'callback', 'not_ready', 'apple_not_ready');
	header("Location: " . programmit_social_login_url($db, 'apple_not_ready'));
	exit;
}

if ($cfg['client_id'] === '' || $cfg['client_secret'] === '') {
	programmit_social_audit($db, $provider, 'callback', 'bad_config', 'missing_client_keys');
	header("Location: " . programmit_social_login_url($db, 'config_missing'));
	exit;
}

$oauthError = isset($_GET['error']) ? strtolower(trim((string)$_GET['error'])) : '';
if ($oauthError !== '') {
	programmit_social_audit($db, $provider, 'callback', 'denied', $oauthError);
	header("Location: " . programmit_social_login_url($db, 'denied'));
	exit;
}

$state = isset($_GET['state']) ? trim((string)$_GET['state']) : '';
$oauthSession = programmit_social_consume_oauth_session($db, $provider, $state);
if (!$oauthSession) {
	programmit_social_audit($db, $provider, 'callback', 'state_error', 'invalid_or_expired_state');
	header("Location: " . programmit_social_login_url($db, 'state_error'));
	exit;
}

$code = isset($_GET['code']) ? trim((string)$_GET['code']) : '';
if ($code === '') {
	programmit_social_audit($db, $provider, 'callback', 'code_missing', 'provider_no_code');
	header("Location: " . programmit_social_login_url($db, 'code_missing'));
	exit;
}

$redirectUri = programmit_social_callback_url($db, $provider);
$socialUserId = '';
$socialEmail = '';
$socialName = '';

if ($provider === 'google') {
	$tokenRes = programmit_social_http(
		'https://oauth2.googleapis.com/token',
		'POST',
		array(
			'code' => $code,
			'client_id' => $cfg['client_id'],
			'client_secret' => $cfg['client_secret'],
			'redirect_uri' => $redirectUri,
			'grant_type' => 'authorization_code'
		),
		array('Accept: application/json')
	);
	if (empty($tokenRes['ok'])) {
		programmit_social_audit($db, $provider, 'callback', 'token_error', programmit_social_debug_detail($tokenRes, 'google_token_request_failed'));
		header("Location: " . programmit_social_login_url($db, 'token_error'));
		exit;
	}
	$tokenData = programmit_social_parse_json($tokenRes['body']);
	$accessToken = trim((string)($tokenData['access_token'] ?? ''));
	if ($accessToken === '') {
		programmit_social_audit($db, $provider, 'callback', 'token_error', programmit_social_debug_detail($tokenRes, 'google_token_empty'));
		header("Location: " . programmit_social_login_url($db, 'token_error'));
		exit;
	}

	$userRes = programmit_social_http(
		'https://openidconnect.googleapis.com/v1/userinfo',
		'GET',
		array(),
		array(
			'Accept: application/json',
			'Authorization: Bearer ' . $accessToken
		)
	);
	if (empty($userRes['ok'])) {
		programmit_social_audit($db, $provider, 'callback', 'profile_error', programmit_social_debug_detail($userRes, 'google_profile_request_failed'));
		header("Location: " . programmit_social_login_url($db, 'profile_error'));
		exit;
	}

	$profile = programmit_social_parse_json($userRes['body']);
	$socialUserId = trim((string)($profile['sub'] ?? ''));
	$socialEmail = strtolower(trim((string)($profile['email'] ?? '')));
	$socialName = trim((string)($profile['name'] ?? ''));
} elseif ($provider === 'facebook') {
	$tokenRes = programmit_social_http(
		'https://graph.facebook.com/v20.0/oauth/access_token',
		'GET',
		array(
			'client_id' => $cfg['client_id'],
			'client_secret' => $cfg['client_secret'],
			'redirect_uri' => $redirectUri,
			'code' => $code
		),
		array('Accept: application/json')
	);
	if (empty($tokenRes['ok'])) {
		programmit_social_audit($db, $provider, 'callback', 'token_error', programmit_social_debug_detail($tokenRes, 'facebook_token_request_failed'));
		header("Location: " . programmit_social_login_url($db, 'token_error'));
		exit;
	}
	$tokenData = programmit_social_parse_json($tokenRes['body']);
	$accessToken = trim((string)($tokenData['access_token'] ?? ''));
	if ($accessToken === '') {
		programmit_social_audit($db, $provider, 'callback', 'token_error', programmit_social_debug_detail($tokenRes, 'facebook_token_empty'));
		header("Location: " . programmit_social_login_url($db, 'token_error'));
		exit;
	}

	$userRes = programmit_social_http(
		'https://graph.facebook.com/me',
		'GET',
		array(
			'fields' => 'id,name,email',
			'access_token' => $accessToken
		),
		array('Accept: application/json')
	);
	if (empty($userRes['ok'])) {
		programmit_social_audit($db, $provider, 'callback', 'profile_error', programmit_social_debug_detail($userRes, 'facebook_profile_request_failed'));
		header("Location: " . programmit_social_login_url($db, 'profile_error'));
		exit;
	}
	$profile = programmit_social_parse_json($userRes['body']);
	$socialUserId = trim((string)($profile['id'] ?? ''));
	$socialEmail = strtolower(trim((string)($profile['email'] ?? '')));
	$socialName = trim((string)($profile['name'] ?? ''));
}

if ($socialUserId === '') {
	programmit_social_audit($db, $provider, 'callback', 'profile_error', 'empty_provider_uid');
	header("Location: " . programmit_social_login_url($db, 'profile_error'));
	exit;
}

if ($socialEmail === '' || !filter_var($socialEmail, FILTER_VALIDATE_EMAIL)) {
	programmit_social_audit($db, $provider, 'callback', 'no_email', 'provider_email_missing');
	header("Location: " . programmit_social_login_url($db, 'no_email'));
	exit;
}

$link = programmit_social_get_link($db, $provider, $socialUserId);

$userRow = null;
if ($link && isset($link['user_id'])) {
	$userRow = programmit_social_fetch_user_by_id($db, (int)$link['user_id']);
}

if (!$userRow) {
	$userRow = programmit_social_fetch_user_by_email($db, $socialEmail);
}

if (!$userRow) {
	if (!programmit_social_signup_enabled($db)) {
		programmit_social_audit($db, $provider, 'callback', 'signup_blocked', 'social_signup_disabled');
		header("Location: " . programmit_social_login_url($db, 'signup_disabled'));
		exit;
	}
	$userRow = programmit_social_create_user($db, $provider, $socialUserId, $socialEmail, $socialName);
}

if (!$userRow || !isset($userRow['user_id'])) {
	programmit_social_audit($db, $provider, 'callback', 'user_error', 'link_or_create_failed');
	header("Location: " . programmit_social_login_url($db, 'user_error'));
	exit;
}

if (!programmit_social_can_login_user($userRow)) {
	programmit_social_audit($db, $provider, 'callback', 'account_blocked', 'user_not_allowed');
	header("Location: " . programmit_social_login_url($db, 'account_blocked'));
	exit;
}

programmit_social_upsert_link(
	$db,
	$provider,
	$socialUserId,
	(int)$userRow['user_id'],
	$socialEmail,
	$socialName
);

programmit_social_login_user($db, $userRow);
programmit_social_audit($db, $provider, 'callback', 'ok', 'login_success');
header("Location: " . $db->base_url() . "index.php?p=dashboard");
exit;
