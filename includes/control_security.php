<?php
if (preg_match("/control_security.php/i", $_SERVER['SCRIPT_NAME'])) {
	Header("Location: /");
	die();
}

function programmit_control_security_setting($db, $key, $default = '') {
	$key = strtolower(trim((string)$key));
	if ($key === '') {
		return (string)$default;
	}
	if (function_exists('programmit_saas_get_setting')) {
		return (string)programmit_saas_get_setting($db, $key, (string)$default);
	}
	return (string)$default;
}

function programmit_control_security_set_setting($db, $key, $value) {
	$key = strtolower(trim((string)$key));
	if ($key === '') {
		return false;
	}
	if (function_exists('programmit_saas_set_setting')) {
		return (bool)programmit_saas_set_setting($db, $key, (string)$value);
	}
	return false;
}

function programmit_control_security_bool($value, $default = false) {
	if (is_bool($value)) {
		return $value;
	}
	$v = strtolower(trim((string)$value));
	if ($v === '') {
		return (bool)$default;
	}
	return in_array($v, array('1', 'true', 'yes', 'on', 'enabled'), true);
}

function programmit_control_security_normalize_host($host) {
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

function programmit_control_security_current_host() {
	if (function_exists('programmit_saas_current_host')) {
		return programmit_control_security_normalize_host(programmit_saas_current_host());
	}
	$raw = isset($_SERVER['HTTP_HOST']) ? (string)$_SERVER['HTTP_HOST'] : '';
	return programmit_control_security_normalize_host($raw);
}

function programmit_control_security_control_host($db) {
	if (function_exists('programmit_saas_get_control_host')) {
		return programmit_control_security_normalize_host(programmit_saas_get_control_host($db));
	}
	return 'panel.programmit.com';
}

function programmit_control_security_stamp_path() {
	$tmpDir = rtrim((string)sys_get_temp_dir(), '/\\');
	if ($tmpDir === '') {
		$tmpDir = '/tmp';
	}
	$stampDir = $tmpDir . DIRECTORY_SEPARATOR . 'programmit_bootstrap';
	if (!is_dir($stampDir)) {
		@mkdir($stampDir, 0775, true);
	}
	return $stampDir . DIRECTORY_SEPARATOR . 'control_security.stamp';
}

function programmit_control_is_host($db) {
	$current = programmit_control_security_current_host();
	$control = programmit_control_security_control_host($db);
	if ($current === '' || $control === '') {
		return false;
	}
	return (strcasecmp($current, $control) === 0);
}

function programmit_control_security_bootstrap($db) {
	static $booted = false;
	if ($booted) {
		return true;
	}
	$booted = true;

	$ttlSeconds = 86400;
	$stampFile = programmit_control_security_stamp_path();
	clearstatcache(true, $stampFile);
	if (is_file($stampFile) && (time() - (int)@filemtime($stampFile)) < $ttlSeconds) {
		return true;
	}

	$defaults = array(
		'control_admin_strict_mode' => '1',
		'control_admin_require_superadmin' => '1',
		'control_admin_ip_whitelist_enabled' => '0',
		'control_admin_ip_whitelist' => '',
		'control_admin_allowed_user_ids' => '1',
		'control_admin_allowed_emails' => '',
		'control_admin_allow_register' => '0',
		'control_admin_allow_magic_login' => '0'
	);

	foreach ($defaults as $k => $v) {
		$current = programmit_control_security_setting($db, $k, '');
		if (trim((string)$current) === '') {
			programmit_control_security_set_setting($db, $k, $v);
		}
	}

	@touch($stampFile);

	return true;
}

function programmit_control_security_parse_csv($raw) {
	$raw = trim((string)$raw);
	if ($raw === '') {
		return array();
	}
	$parts = preg_split('/[\s,;]+/', $raw);
	$out = array();
	foreach ((array)$parts as $p) {
		$p = trim((string)$p);
		if ($p !== '') {
			$out[] = $p;
		}
	}
	return array_values(array_unique($out));
}

function programmit_control_security_ip_allowed($db, $ip) {
	$enabled = programmit_control_security_bool(programmit_control_security_setting($db, 'control_admin_ip_whitelist_enabled', '0'), false);
	if (!$enabled) {
		return true;
	}
	$allowRaw = programmit_control_security_setting($db, 'control_admin_ip_whitelist', '');
	$allowed = programmit_control_security_parse_csv($allowRaw);
	if (empty($allowed)) {
		return false;
	}
	$ip = trim((string)$ip);
	return in_array($ip, $allowed, true);
}

function programmit_control_security_user_allowed($db, $userRow) {
	if (!is_array($userRow)) {
		return false;
	}
	$userId = isset($userRow['user_id']) ? (int)$userRow['user_id'] : 0;
	$userLevel = strtolower(trim((string)($userRow['user_level'] ?? '')));
	$userEmail = strtolower(trim((string)($userRow['user_email'] ?? '')));

	$strict = programmit_control_security_bool(programmit_control_security_setting($db, 'control_admin_strict_mode', '1'), true);
	if (!$strict) {
		return true;
	}

	$allowIds = programmit_control_security_parse_csv(programmit_control_security_setting($db, 'control_admin_allowed_user_ids', '1'));
	if (!empty($allowIds) && in_array((string)$userId, $allowIds, true)) {
		return true;
	}

	$allowEmails = array_map('strtolower', programmit_control_security_parse_csv(programmit_control_security_setting($db, 'control_admin_allowed_emails', '')));
	if ($userEmail !== '' && !empty($allowEmails) && in_array($userEmail, $allowEmails, true)) {
		return true;
	}

	$requireSuperadmin = programmit_control_security_bool(programmit_control_security_setting($db, 'control_admin_require_superadmin', '1'), true);
	if ($requireSuperadmin) {
		return ($userId === 1 || $userLevel === 'superadmin');
	}

	return false;
}

function programmit_control_security_allow_register($db) {
	return programmit_control_security_bool(programmit_control_security_setting($db, 'control_admin_allow_register', '0'), false);
}

function programmit_control_security_allow_magic_login($db) {
	return programmit_control_security_bool(programmit_control_security_setting($db, 'control_admin_allow_magic_login', '0'), false);
}
