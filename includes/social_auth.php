<?php
if (preg_match("/social_auth.php/i", $_SERVER['SCRIPT_NAME'])) {
	Header("Location: /");
	die();
}

function programmit_social_default_config() {
	return array(
		'google' => array(
			'enabled' => false,
			'client_id' => '',
			'client_secret' => '',
			'scope' => 'openid email profile'
		),
		'facebook' => array(
			'enabled' => false,
			'client_id' => '',
			'client_secret' => '',
			'scope' => 'email public_profile'
		),
		'apple' => array(
			'enabled' => false,
			'client_id' => '',
			'team_id' => '',
			'key_id' => '',
			'private_key' => '',
			'scope' => 'name email'
		),
		'default_upline' => 1
	);
}

function programmit_social_bool($value, $default = false) {
	if (is_bool($value)) {
		return $value;
	}
	if ($value === null) {
		return (bool)$default;
	}
	$v = strtolower(trim((string)$value));
	if ($v === '') {
		return (bool)$default;
	}
	return in_array($v, array('1', 'true', 'yes', 'on', 'enabled'), true);
}

function programmit_social_env($name) {
	$v = getenv((string)$name);
	if ($v === false) {
		return '';
	}
	return trim((string)$v);
}

function programmit_social_provider($provider) {
	$provider = strtolower(trim((string)$provider));
	if (!in_array($provider, array('google', 'facebook', 'apple'), true)) {
		return '';
	}
	return $provider;
}

function programmit_social_table_exists($db, $tableName) {
	$table = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$tableName);
	if ($table === '') {
		return false;
	}
	$qry = $db->sql_query("SHOW TABLES LIKE '" . $db->SanitizeForSQL($table) . "'");
	return ($qry && $db->sql_numrows($qry) > 0);
}

function programmit_social_file_config() {
	static $cache = null;
	if ($cache !== null) {
		return $cache;
	}

	$file = __DIR__ . '/social_config.php';
	$config = array();
	if (is_file($file)) {
		$loaded = include $file;
		if (is_array($loaded)) {
			$config = $loaded;
		}
	}

	$cache = $config;
	return $cache;
}

function programmit_social_accounts_table_sql() {
	return "CREATE TABLE IF NOT EXISTS auth_social_accounts (
		id INT(11) NOT NULL AUTO_INCREMENT,
		user_id INT(11) NOT NULL,
		provider VARCHAR(20) NOT NULL,
		provider_uid VARCHAR(191) NOT NULL,
		provider_email VARCHAR(191) NOT NULL DEFAULT '',
		provider_name VARCHAR(191) NOT NULL DEFAULT '',
		created_at DATETIME NOT NULL,
		last_login_at DATETIME DEFAULT NULL,
		PRIMARY KEY (id),
		UNIQUE KEY uniq_provider_uid (provider, provider_uid),
		KEY idx_social_user (user_id),
		KEY idx_social_provider (provider)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

function programmit_social_providers_table_sql() {
	return "CREATE TABLE IF NOT EXISTS auth_oauth_providers (
		provider VARCHAR(20) NOT NULL,
		enabled TINYINT(1) NOT NULL DEFAULT 0,
		client_id VARCHAR(255) NOT NULL DEFAULT '',
		client_secret VARCHAR(255) NOT NULL DEFAULT '',
		scope VARCHAR(255) NOT NULL DEFAULT '',
		created_at DATETIME NOT NULL,
		updated_at DATETIME DEFAULT NULL,
		PRIMARY KEY (provider)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

function programmit_social_sessions_table_sql() {
	return "CREATE TABLE IF NOT EXISTS auth_oauth_sessions (
		id INT(11) NOT NULL AUTO_INCREMENT,
		provider VARCHAR(20) NOT NULL,
		state_hash CHAR(64) NOT NULL,
		pkce_verifier VARCHAR(191) NOT NULL DEFAULT '',
		redirect_uri VARCHAR(500) NOT NULL DEFAULT '',
		request_ip VARCHAR(64) NOT NULL DEFAULT '',
		request_ua VARCHAR(255) NOT NULL DEFAULT '',
		created_at DATETIME NOT NULL,
		expires_at DATETIME NOT NULL,
		consumed_at DATETIME DEFAULT NULL,
		fail_count TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
		PRIMARY KEY (id),
		UNIQUE KEY uniq_state_hash (state_hash),
		KEY idx_oauth_provider_exp (provider, expires_at),
		KEY idx_oauth_consumed (consumed_at)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

function programmit_social_audit_table_sql() {
	return "CREATE TABLE IF NOT EXISTS auth_oauth_audit (
		id INT(11) NOT NULL AUTO_INCREMENT,
		provider VARCHAR(20) NOT NULL,
		event_name VARCHAR(40) NOT NULL,
		status VARCHAR(20) NOT NULL,
		details VARCHAR(255) NOT NULL DEFAULT '',
		request_ip VARCHAR(64) NOT NULL DEFAULT '',
		request_ua VARCHAR(255) NOT NULL DEFAULT '',
		created_at DATETIME NOT NULL,
		PRIMARY KEY (id),
		KEY idx_audit_provider (provider),
		KEY idx_audit_event (event_name),
		KEY idx_audit_created (created_at)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

function programmit_social_seed_provider_rows($db) {
	$defaults = programmit_social_default_config();
	$list = array('google', 'facebook', 'apple');
	foreach ($list as $provider) {
		$scope = isset($defaults[$provider]['scope']) ? (string)$defaults[$provider]['scope'] : '';
		$db->sql_query("INSERT IGNORE INTO auth_oauth_providers
			(provider, enabled, client_id, client_secret, scope, created_at, updated_at)
			VALUES
			('" . $db->SanitizeForSQL($provider) . "', 0, '', '', '" . $db->SanitizeForSQL($scope) . "', NOW(), NOW())");
	}
}

function programmit_social_ensure_tables($db) {
	$db->sql_query(programmit_social_accounts_table_sql());
	$db->sql_query(programmit_social_providers_table_sql());
	$db->sql_query(programmit_social_sessions_table_sql());
	$db->sql_query(programmit_social_audit_table_sql());
	programmit_social_seed_provider_rows($db);
	return true;
}

function programmit_social_audit($db, $provider, $eventName, $status, $details = '') {
	$provider = programmit_social_provider($provider);
	if ($provider === '') {
		$provider = 'unknown';
	}
	$eventName = preg_replace('/[^a-zA-Z0-9_\-]/', '', (string)$eventName);
	$status = preg_replace('/[^a-zA-Z0-9_\-]/', '', (string)$status);
	$details = trim((string)$details);
	$ip = isset($db) && method_exists($db, 'get_client_ip') ? $db->get_client_ip() : '';
	$ua = isset($_SERVER['HTTP_USER_AGENT']) ? substr((string)$_SERVER['HTTP_USER_AGENT'], 0, 255) : '';
	$db->sql_query("INSERT INTO auth_oauth_audit
		(provider, event_name, status, details, request_ip, request_ua, created_at)
		VALUES
		('" . $db->SanitizeForSQL($provider) . "',
		 '" . $db->SanitizeForSQL($eventName) . "',
		 '" . $db->SanitizeForSQL($status) . "',
		 '" . $db->SanitizeForSQL($details) . "',
		 '" . $db->SanitizeForSQL($ip) . "',
		 '" . $db->SanitizeForSQL($ua) . "',
		 NOW())");
}

function programmit_social_load_db_provider($db, $provider) {
	if (!programmit_social_table_exists($db, 'auth_oauth_providers')) {
		return array();
	}
	$qry = $db->sql_query("SELECT enabled, client_id, client_secret, scope
		FROM auth_oauth_providers
		WHERE provider='" . $db->SanitizeForSQL($provider) . "'
		LIMIT 1");
	$row = $db->sql_fetchrow($qry);
	if (!$row) {
		return array();
	}
	return array(
		'enabled' => programmit_social_bool(isset($row['enabled']) ? $row['enabled'] : 0, false),
		'client_id' => trim((string)($row['client_id'] ?? '')),
		'client_secret' => trim((string)($row['client_secret'] ?? '')),
		'scope' => trim((string)($row['scope'] ?? ''))
	);
}

function programmit_social_provider_config($db, $provider) {
	$provider = programmit_social_provider($provider);
	if ($provider === '') {
		return null;
	}

	$defaults = programmit_social_default_config();
	$file = programmit_social_file_config();
	$dbCfg = programmit_social_load_db_provider($db, $provider);

	$cfg = isset($defaults[$provider]) && is_array($defaults[$provider]) ? $defaults[$provider] : array();
	if (!empty($dbCfg)) {
		$cfg = array_replace($cfg, $dbCfg);
	}
	if (isset($file[$provider]) && is_array($file[$provider])) {
		$fileProv = $file[$provider];
		if (array_key_exists('enabled', $fileProv)) {
			if (programmit_social_bool($fileProv['enabled'], false)) {
				$cfg['enabled'] = true;
			}
		}
		if (isset($fileProv['client_id']) && trim((string)$fileProv['client_id']) !== '') {
			$cfg['client_id'] = trim((string)$fileProv['client_id']);
		}
		if (isset($fileProv['client_secret']) && trim((string)$fileProv['client_secret']) !== '') {
			$cfg['client_secret'] = trim((string)$fileProv['client_secret']);
		}
		if (isset($fileProv['scope']) && trim((string)$fileProv['scope']) !== '') {
			$cfg['scope'] = trim((string)$fileProv['scope']);
		}
	}

	$envPrefix = 'PM_SOCIAL_' . strtoupper($provider) . '_';
	$envEnabled = programmit_social_env($envPrefix . 'ENABLED');
	$envClientId = programmit_social_env($envPrefix . 'CLIENT_ID');
	$envClientSecret = programmit_social_env($envPrefix . 'CLIENT_SECRET');
	$envScope = programmit_social_env($envPrefix . 'SCOPE');

	if ($envEnabled !== '') {
		$cfg['enabled'] = programmit_social_bool($envEnabled, false);
	}
	if ($envClientId !== '') {
		$cfg['client_id'] = $envClientId;
	}
	if ($envClientSecret !== '') {
		$cfg['client_secret'] = $envClientSecret;
	}
	if ($envScope !== '') {
		$cfg['scope'] = $envScope;
	}

	$cfg['enabled'] = programmit_social_bool(isset($cfg['enabled']) ? $cfg['enabled'] : false, false);
	$cfg['client_id'] = trim((string)($cfg['client_id'] ?? ''));
	$cfg['client_secret'] = trim((string)($cfg['client_secret'] ?? ''));
	$cfg['scope'] = trim((string)($cfg['scope'] ?? ''));

	if ($cfg['scope'] === '') {
		$cfg['scope'] = isset($defaults[$provider]['scope']) ? (string)$defaults[$provider]['scope'] : '';
	}

	return $cfg;
}

function programmit_social_default_upline() {
	$config = programmit_social_default_config();
	$file = programmit_social_file_config();
	$upline = isset($config['default_upline']) ? (int)$config['default_upline'] : 1;
	if (isset($file['default_upline'])) {
		$upline = (int)$file['default_upline'];
	}
	if ($upline <= 0) {
		$upline = 1;
	}
	return $upline;
}

function programmit_social_normalize_host($host) {
	$host = trim((string)$host);
	if ($host === '') {
		return '';
	}
	$host = preg_replace('#^https?://#i', '', $host);
	$slashPos = strpos($host, '/');
	if ($slashPos !== false) {
		$host = substr($host, 0, $slashPos);
	}
	$host = preg_replace('/:\d+$/', '', $host);
	return strtolower(trim((string)$host));
}

function programmit_social_current_host() {
	if (function_exists('programmit_saas_current_host')) {
		return programmit_social_normalize_host(programmit_saas_current_host());
	}
	$raw = isset($_SERVER['HTTP_HOST']) ? (string)$_SERVER['HTTP_HOST'] : '';
	return programmit_social_normalize_host($raw);
}

function programmit_social_control_host($db) {
	if (function_exists('programmit_saas_get_control_host')) {
		return programmit_social_normalize_host(programmit_saas_get_control_host($db));
	}
	return 'panel.programmit.com';
}

function programmit_social_setting_value($db, $key, $default = '') {
	$key = trim((string)$key);
	if ($key === '') {
		return (string)$default;
	}
	if (function_exists('programmit_saas_get_setting')) {
		return (string)programmit_saas_get_setting($db, $key, (string)$default);
	}
	return (string)$default;
}

function programmit_social_is_control_host($db) {
	$currentHost = programmit_social_current_host();
	$controlHost = programmit_social_control_host($db);
	if ($currentHost === '' || $controlHost === '') {
		return false;
	}
	return (strcasecmp($currentHost, $controlHost) === 0);
}

function programmit_social_host_allows_oauth($db) {
	$blockEnv = programmit_social_env('PM_SOCIAL_BLOCK_ON_CONTROL_HOST');
	if ($blockEnv !== '') {
		$blockOnControl = programmit_social_bool($blockEnv, true);
	} else {
		$blockOnControl = programmit_social_bool(
			programmit_social_setting_value($db, 'social_oauth_block_on_control_host', '1'),
			true
		);
	}
	if (!$blockOnControl) {
		return true;
	}
	return !programmit_social_is_control_host($db);
}

function programmit_social_signup_enabled($db) {
	$signupEnv = programmit_social_env('PM_SOCIAL_SIGNUP_ENABLED');
	if ($signupEnv !== '') {
		return programmit_social_bool($signupEnv, false);
	}
	return programmit_social_bool(programmit_social_setting_value($db, 'social_oauth_signup_enabled', '0'), false);
}

function programmit_social_callback_url($db, $provider) {
	return $db->base_url() . 'index.php?p=social-callback&provider=' . urlencode($provider);
}

function programmit_social_login_url($db, $state) {
	return $db->base_url() . 'index.php?p=login&social=' . urlencode($state);
}

function programmit_social_random_token($bytes = 24) {
	$bytes = (int)$bytes;
	if ($bytes < 16) {
		$bytes = 16;
	}
	try {
		return bin2hex(random_bytes($bytes));
	} catch (Exception $e) {
		return bin2hex(openssl_random_pseudo_bytes($bytes));
	}
}

function programmit_social_base64url($raw) {
	return rtrim(strtr(base64_encode((string)$raw), '+/', '-_'), '=');
}

function programmit_social_generate_pkce_verifier() {
	try {
		$raw = random_bytes(64);
	} catch (Exception $e) {
		$raw = openssl_random_pseudo_bytes(64);
	}
	$verifier = programmit_social_base64url($raw);
	if (strlen($verifier) < 43) {
		$verifier .= substr(programmit_social_random_token(12), 0, 43 - strlen($verifier));
	}
	if (strlen($verifier) > 128) {
		$verifier = substr($verifier, 0, 128);
	}
	return $verifier;
}

function programmit_social_pkce_challenge($verifier) {
	$hash = hash('sha256', (string)$verifier, true);
	return programmit_social_base64url($hash);
}

function programmit_social_create_oauth_session($db, $provider, $redirectUri, $pkceVerifier = '') {
	$provider = programmit_social_provider($provider);
	if ($provider === '') {
		return '';
	}
	$state = programmit_social_random_token(32);
	$stateHash = hash('sha256', $state);
	$ip = $db->get_client_ip();
	$ua = isset($_SERVER['HTTP_USER_AGENT']) ? substr((string)$_SERVER['HTTP_USER_AGENT'], 0, 255) : '';
	$redirectUri = trim((string)$redirectUri);
	$pkceVerifier = substr(trim((string)$pkceVerifier), 0, 191);

	$db->sql_query("DELETE FROM auth_oauth_sessions
		WHERE expires_at < DATE_SUB(NOW(), INTERVAL 1 DAY) OR consumed_at IS NOT NULL");

	$ok = $db->sql_query("INSERT INTO auth_oauth_sessions
		(provider, state_hash, pkce_verifier, redirect_uri, request_ip, request_ua, created_at, expires_at, consumed_at, fail_count)
		VALUES
		('" . $db->SanitizeForSQL($provider) . "',
		 '" . $db->SanitizeForSQL($stateHash) . "',
		 '" . $db->SanitizeForSQL($pkceVerifier) . "',
		 '" . $db->SanitizeForSQL($redirectUri) . "',
		 '" . $db->SanitizeForSQL($ip) . "',
		 '" . $db->SanitizeForSQL($ua) . "',
		 NOW(),
		 DATE_ADD(NOW(), INTERVAL 10 MINUTE),
		 NULL,
		 0)");

	return $ok ? $state : '';
}

function programmit_social_consume_oauth_session($db, $provider, $state) {
	$provider = programmit_social_provider($provider);
	$state = trim((string)$state);
	if ($provider === '' || !preg_match('/^[a-f0-9]{64}$/', $state)) {
		return null;
	}

	$stateHash = hash('sha256', $state);
	$qry = $db->sql_query("SELECT id, provider, pkce_verifier, redirect_uri, request_ip, request_ua, created_at, expires_at, consumed_at, fail_count
		FROM auth_oauth_sessions
		WHERE provider='" . $db->SanitizeForSQL($provider) . "'
		AND state_hash='" . $db->SanitizeForSQL($stateHash) . "'
		LIMIT 1");
	$row = $db->sql_fetchrow($qry);
	if (!$row) {
		return null;
	}

	$isUsed = !empty($row['consumed_at']) && $row['consumed_at'] !== '0000-00-00 00:00:00';
	$isExpired = (strtotime((string)$row['expires_at']) !== false && strtotime((string)$row['expires_at']) < time());
	if ($isUsed || $isExpired) {
		$db->sql_query("UPDATE auth_oauth_sessions
			SET fail_count=fail_count+1
			WHERE id='" . $db->SanitizeForSQL((int)$row['id']) . "'");
		return null;
	}

	$db->sql_query("UPDATE auth_oauth_sessions
		SET consumed_at=NOW()
		WHERE id='" . $db->SanitizeForSQL((int)$row['id']) . "'
		AND consumed_at IS NULL");

	return $row;
}

function programmit_social_http($url, $method = 'GET', $params = array(), $headers = array()) {
	$method = strtoupper(trim((string)$method));
	$params = is_array($params) ? $params : array();
	$headers = is_array($headers) ? $headers : array();

	$query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
	$requestUrl = $url;
	$body = null;

	if ($method === 'GET' && $query !== '') {
		$requestUrl .= (strpos($requestUrl, '?') === false ? '?' : '&') . $query;
	} elseif ($method !== 'GET') {
		$body = $query;
	}

	$headers[] = 'User-Agent: ProgrammitPanel/2.0 OAuthClient';

	if (function_exists('curl_init')) {
		$ch = curl_init($requestUrl);
		$httpHeaders = $headers;
		if ($method !== 'GET') {
			$httpHeaders[] = 'Content-Type: application/x-www-form-urlencoded';
			curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 12);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);

		if ($method === 'POST') {
			curl_setopt($ch, CURLOPT_POST, true);
		} elseif ($method !== 'GET') {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		}

		$responseBody = curl_exec($ch);
		$error = curl_error($ch);
		$status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		return array(
			'ok' => ($error === '' && $status >= 200 && $status < 300),
			'status' => $status,
			'body' => is_string($responseBody) ? $responseBody : '',
			'error' => $error
		);
	}

	$contextHeaders = $headers;
	if ($method !== 'GET') {
		$contextHeaders[] = 'Content-Type: application/x-www-form-urlencoded';
	}

	$options = array(
		'http' => array(
			'method' => $method,
			'ignore_errors' => true,
			'timeout' => 20,
			'header' => implode("\r\n", $contextHeaders)
		)
	);
	if ($method !== 'GET') {
		$options['http']['content'] = $body;
	}

	$context = stream_context_create($options);
	$responseBody = @file_get_contents($requestUrl, false, $context);
	$status = 0;
	if (isset($http_response_header) && is_array($http_response_header)) {
		foreach ($http_response_header as $line) {
			if (preg_match('#^HTTP/\d+(?:\.\d+)?\s+(\d+)#i', $line, $m)) {
				$status = (int)$m[1];
				break;
			}
		}
	}

	return array(
		'ok' => ($responseBody !== false && $status >= 200 && $status < 300),
		'status' => $status,
		'body' => ($responseBody !== false ? $responseBody : ''),
		'error' => ($responseBody === false ? 'request_failed' : '')
	);
}

function programmit_social_parse_json($raw) {
	$data = json_decode((string)$raw, true);
	return is_array($data) ? $data : array();
}

function programmit_social_can_login_user($row) {
	if (!$row || !is_array($row)) {
		return false;
	}
	$isLive = isset($row['status']) && strtolower(trim((string)$row['status'])) === 'live';
	return ((int)($row['is_active'] ?? 0) === 1)
		&& ((int)($row['is_ban'] ?? 0) === 0)
		&& $isLive;
}

function programmit_social_fetch_user_by_id($db, $userId) {
	$userId = (int)$userId;
	if ($userId <= 0) {
		return null;
	}
	$qry = $db->sql_query("SELECT user_id, user_name, user_pass, full_name, user_email, user_level, is_active, is_ban, status, ipaddress, lastlogin
		FROM users
		WHERE user_id='" . $db->SanitizeForSQL($userId) . "'
		LIMIT 1");
	$row = $db->sql_fetchrow($qry);
	return $row ? $row : null;
}

function programmit_social_fetch_user_by_email($db, $email) {
	$email = strtolower(trim((string)$email));
	if ($email === '') {
		return null;
	}
	$qry = $db->sql_query("SELECT user_id, user_name, user_pass, full_name, user_email, user_level, is_active, is_ban, status, ipaddress, lastlogin
		FROM users
		WHERE LOWER(TRIM(user_email))='" . $db->SanitizeForSQL($email) . "'
		LIMIT 1");
	$row = $db->sql_fetchrow($qry);
	return $row ? $row : null;
}

function programmit_social_make_username($db, $email, $provider, $providerUid) {
	$local = explode('@', (string)$email);
	$base = isset($local[0]) ? $local[0] : '';
	$base = preg_replace('/[^a-zA-Z0-9._-]/', '', $base);
	$base = trim($base, '._-');
	if (strlen($base) < 3) {
		$base = $provider . '_' . substr(md5((string)$providerUid), 0, 8);
	}
	if (strlen($base) > 32) {
		$base = substr($base, 0, 32);
	}

	$username = $base;
	for ($i = 0; $i < 60; $i++) {
		$qry = $db->sql_query("SELECT user_id FROM users WHERE user_name='" . $db->SanitizeForSQL($username) . "' LIMIT 1");
		if (!$qry || $db->sql_numrows($qry) < 1) {
			return $username;
		}
		$suffix = (string)random_int(100, 9999);
		$rootLen = 32 - strlen($suffix);
		if ($rootLen < 3) {
			$rootLen = 3;
		}
		$username = substr($base, 0, $rootLen) . $suffix;
	}

	return '';
}

function programmit_social_create_user($db, $provider, $providerUid, $email, $fullName) {
	$email = strtolower(trim((string)$email));
	if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return null;
	}

	$userName = programmit_social_make_username($db, $email, $provider, $providerUid);
	if ($userName === '') {
		return null;
	}

	$plainPassword = substr(programmit_social_random_token(20), 0, 20) . '!9';
	$passwordEncrypted = $db->encrypt_key($db->encryptor('encrypt', $plainPassword));
	$authVpn = md5($plainPassword);
	$code = (string)random_int(10000000, 999999999);
	$now = date('Y-m-d H:i:s');
	$userIp = $db->get_client_ip();
	$upline = programmit_social_default_upline();

	$fullName = trim((string)$fullName);
	if ($fullName === '') {
		$fullName = $userName;
	}

	$valuesMap = array(
		'user_name' => $userName,
		'user_pass' => $passwordEncrypted,
		'auth_vpn' => $authVpn,
		'user_email' => $email,
		'full_name' => $fullName,
		'regdate' => $now,
		'is_groupname' => 'administrator',
		'is_active' => 1,
		'is_freeze' => 0,
		'user_level' => 'administrator',
		'code' => $code,
		'is_ban' => 0,
		'is_validated' => 1,
		'upline' => $upline,
		'duration' => 0,
		'role_duration' => 0,
		'status' => 'live',
		'login_status' => 'offline',
		'password' => $plainPassword,
		'pass_plain' => $plainPassword,
		'ss_id' => '',
		'ssl_id' => 'ssl',
		'attribute' => 'MD5-Password',
		'op' => ':=',
		'ipaddress' => $userIp,
		'timestamp' => 0,
		'is_passchange' => 0,
		'freeze_status' => 0,
		'last_freeze_date' => '1970-01-01 00:00:00',
		'is_connected' => 0,
		'is_offense' => 0,
		'suspended_date' => '1970-01-01 00:00:00',
		'vip_duration' => 0,
		'is_vip' => 0,
		'private_duration' => 0,
		'is_private' => 0,
		'private_slot' => 0,
		'private_control' => 0,
		'credits' => 0,
		'last_active_time' => $now,
		'bandwidth' => 0,
		'bandwidth_premium' => 0,
		'bandwidth_vip' => 0,
		'bandwidth_ph' => 0,
		'bandwidth_private' => 0,
		'bandwidth_free' => 0,
		'device_connected' => 0
	);

	$columnsSet = array();
	$colsQry = $db->sql_query("SHOW COLUMNS FROM users");
	while ($colRow = $db->sql_fetchrow($colsQry)) {
		if (isset($colRow['Field'])) {
			$columnsSet[$colRow['Field']] = true;
		}
	}

	$insertCols = array();
	$insertVals = array();
	foreach ($valuesMap as $col => $val) {
		if (isset($columnsSet[$col])) {
			$insertCols[] = "`" . $col . "`";
			$insertVals[] = "'" . $db->SanitizeForSQL((string)$val) . "'";
		}
	}

	if (count($insertCols) < 8) {
		return null;
	}

	$insertSql = "INSERT INTO users (" . implode(", ", $insertCols) . ")
		VALUES (" . implode(", ", $insertVals) . ")";
	$ok = $db->sql_query($insertSql);
	if (!$ok) {
		return null;
	}

	$newId = (int)$db->sql_nextid();
	if ($newId > 0) {
		if (programmit_social_table_exists($db, 'users_profile')) {
			$db->sql_query("INSERT INTO users_profile (profile_id) VALUES ('" . $db->SanitizeForSQL($newId) . "')");
		}
		if(function_exists('programmit_panel_access_bootstrap_user')){
			programmit_panel_access_bootstrap_user($db, $newId, 'administrator', 0);
		}
		return programmit_social_fetch_user_by_id($db, $newId);
	}

	return null;
}

function programmit_social_get_link($db, $provider, $providerUid) {
	$qry = $db->sql_query("SELECT id, user_id
		FROM auth_social_accounts
		WHERE provider='" . $db->SanitizeForSQL($provider) . "'
		AND provider_uid='" . $db->SanitizeForSQL($providerUid) . "'
		LIMIT 1");
	$row = $db->sql_fetchrow($qry);
	return $row ? $row : null;
}

function programmit_social_upsert_link($db, $provider, $providerUid, $userId, $email, $name) {
	$userId = (int)$userId;
	if ($userId <= 0) {
		return false;
	}

	$existing = programmit_social_get_link($db, $provider, $providerUid);
	if ($existing) {
		$db->sql_query("UPDATE auth_social_accounts
			SET user_id='" . $db->SanitizeForSQL($userId) . "',
				provider_email='" . $db->SanitizeForSQL((string)$email) . "',
				provider_name='" . $db->SanitizeForSQL((string)$name) . "',
				last_login_at=NOW()
			WHERE id='" . $db->SanitizeForSQL((int)$existing['id']) . "'");
		return true;
	}

	$db->sql_query("INSERT INTO auth_social_accounts
		(user_id, provider, provider_uid, provider_email, provider_name, created_at, last_login_at)
		VALUES
		('" . $db->SanitizeForSQL($userId) . "',
		 '" . $db->SanitizeForSQL($provider) . "',
		 '" . $db->SanitizeForSQL($providerUid) . "',
		 '" . $db->SanitizeForSQL((string)$email) . "',
		 '" . $db->SanitizeForSQL((string)$name) . "',
		 NOW(), NOW())");
	return true;
}

function programmit_social_set_cookie($name, $value, $expire) {
	$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
	$params = array(
		'expires' => (int)$expire,
		'path' => '/',
		'secure' => $secure,
		'httponly' => true,
		'samesite' => 'Lax'
	);
	if (PHP_VERSION_ID >= 70300) {
		setcookie((string)$name, (string)$value, $params);
		return;
	}
	$path = '/; samesite=Lax';
	setcookie((string)$name, (string)$value, (int)$expire, $path, '', $secure, true);
}

function programmit_social_login_user($db, $userRow) {
	$lastLogin = explode(' ', (string)$userRow['lastlogin']);
	$lastDate = isset($lastLogin[0]) && $lastLogin[0] !== '' ? $lastLogin[0] : date('Y-m-d');
	$lastTime = isset($lastLogin[1]) && $lastLogin[1] !== '' ? $lastLogin[1] : date('H:i:s');
	$ip = isset($userRow['ipaddress']) && trim((string)$userRow['ipaddress']) !== '' ? $userRow['ipaddress'] : $db->get_client_ip();

	$cookiePayload = $db->encrypt_key(
		$userRow['user_id'] . "|" . $userRow['user_name'] . "|" . $userRow['user_pass'] . "|" . $ip . "|" . $lastDate . "|" . $lastTime . "|" . $userRow['user_level']
	);
	$exp = time() + 86400;
	programmit_social_set_cookie('user', $cookiePayload, $exp);
	programmit_social_set_cookie('user_id', $db->encrypt_key($userRow['user_id']), $exp);
	programmit_social_set_cookie('full_name', $db->encrypt_key($userRow['full_name']), $exp);
	programmit_social_set_cookie('user_email', $db->encrypt_key($userRow['user_email']), $exp);

	$db->sql_query("UPDATE users
		SET ipaddress='" . $db->SanitizeForSQL($db->get_client_ip()) . "',
			lastlogin=NOW(),
			login_status='online',
			last_active_time=NOW()
		WHERE user_id='" . $db->SanitizeForSQL((int)$userRow['user_id']) . "'");
}
