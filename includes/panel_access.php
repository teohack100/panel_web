<?php
if (preg_match("/panel_access.php/i", $_SERVER['SCRIPT_NAME'])) {
	Header("Location: /");
	die();
}

function programmit_panel_access_table_sql() {
	return "CREATE TABLE IF NOT EXISTS auth_panel_access (
		user_id INT(11) NOT NULL,
		is_enabled TINYINT(1) NOT NULL DEFAULT 0,
		allow_zero_credits TINYINT(1) NOT NULL DEFAULT 0,
		plan_code VARCHAR(64) NOT NULL DEFAULT 'pending',
		lock_reason VARCHAR(191) NOT NULL DEFAULT 'pending_activation',
		created_at DATETIME NOT NULL,
		updated_at DATETIME DEFAULT NULL,
		last_granted_at DATETIME DEFAULT NULL,
		PRIMARY KEY (user_id),
		KEY idx_panel_enabled (is_enabled, updated_at)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

function programmit_panel_access_schema_stamp_path() {
	$tmpDir = rtrim((string)sys_get_temp_dir(), '/\\');
	if ($tmpDir === '') {
		$tmpDir = '/tmp';
	}
	$stampDir = $tmpDir . DIRECTORY_SEPARATOR . 'programmit_bootstrap';
	if (!is_dir($stampDir)) {
		@mkdir($stampDir, 0775, true);
	}
	return $stampDir . DIRECTORY_SEPARATOR . 'panel_access_schema.stamp';
}

function programmit_panel_access_ensure_table($db) {
	static $ready = false;
	if ($ready) {
		return true;
	}

	$ttlSeconds = 86400;
	$stampFile = programmit_panel_access_schema_stamp_path();
	clearstatcache(true, $stampFile);
	if (is_file($stampFile) && (time() - (int)@filemtime($stampFile)) < $ttlSeconds) {
		$ready = true;
		return true;
	}

	$db->sql_query(programmit_panel_access_table_sql());
	@touch($stampFile);
	$ready = true;
	return true;
}

function programmit_panel_access_is_privileged_role($userLevel, $userId = 0) {
	if ((int)$userId === 1) {
		return true;
	}
	$role = strtolower(trim((string)$userLevel));
	return in_array($role, array('superadmin', 'administrator', 'subadmin', 'reseller', 'subreseller'), true);
}

function programmit_panel_access_default_enabled($userLevel, $credits, $userId = 0) {
	if (programmit_panel_access_is_privileged_role($userLevel, $userId)) {
		return 1;
	}
	return ((int)$credits > 0) ? 1 : 0;
}

function programmit_panel_access_bootstrap_user($db, $userId, $userLevel, $credits) {
	$userId = (int)$userId;
	if ($userId <= 0) {
		return false;
	}
	programmit_panel_access_ensure_table($db);

	$isEnabled = programmit_panel_access_default_enabled($userLevel, $credits, $userId);
	$planCode = $isEnabled ? 'enabled' : 'pending';
	$reason = $isEnabled ? 'active' : 'pending_activation';
	$allowZero = programmit_panel_access_is_privileged_role($userLevel, $userId) ? 1 : 0;
	$lastGranted = ($isEnabled === 1) ? "NOW()" : "NULL";

	if (method_exists($db, 'is_pgsql') && $db->is_pgsql()) {
		$sql = "INSERT INTO auth_panel_access
			(user_id, is_enabled, allow_zero_credits, plan_code, lock_reason, created_at, updated_at, last_granted_at)
			VALUES
			('".$db->SanitizeForSQL($userId)."',
			 '".$db->SanitizeForSQL($isEnabled)."',
			 '".$db->SanitizeForSQL($allowZero)."',
			 '".$db->SanitizeForSQL($planCode)."',
			 '".$db->SanitizeForSQL($reason)."',
			 NOW(),
			 NOW(),
			 ".$lastGranted.")
			ON CONFLICT (user_id) DO NOTHING";
	} else {
		$sql = "INSERT INTO auth_panel_access
			(user_id, is_enabled, allow_zero_credits, plan_code, lock_reason, created_at, updated_at, last_granted_at)
			VALUES
			('".$db->SanitizeForSQL($userId)."',
			 '".$db->SanitizeForSQL($isEnabled)."',
			 '".$db->SanitizeForSQL($allowZero)."',
			 '".$db->SanitizeForSQL($planCode)."',
			 '".$db->SanitizeForSQL($reason)."',
			 NOW(),
			 NOW(),
			 ".$lastGranted.")
			ON DUPLICATE KEY UPDATE
				updated_at=updated_at";
	}

	return (bool)$db->sql_query($sql);
}

function programmit_panel_access_get_row($db, $userId) {
	$userId = (int)$userId;
	if ($userId <= 0) {
		return null;
	}
	programmit_panel_access_ensure_table($db);
	$qry = $db->sql_query("SELECT user_id, is_enabled, allow_zero_credits, plan_code, lock_reason, created_at, updated_at, last_granted_at
		FROM auth_panel_access
		WHERE user_id='".$db->SanitizeForSQL($userId)."'
		LIMIT 1");
	$row = $db->sql_fetchrow($qry);
	return $row ? $row : null;
}

function programmit_panel_access_is_allowed($db, $userId, $userLevel, $credits) {
	$userId = (int)$userId;
	if ($userId <= 0) {
		return false;
	}

	if (programmit_panel_access_is_privileged_role($userLevel, $userId)) {
		return true;
	}

	programmit_panel_access_bootstrap_user($db, $userId, $userLevel, $credits);
	$row = programmit_panel_access_get_row($db, $userId);
	if (!$row) {
		return false;
	}

	$isEnabled = (int)($row['is_enabled'] ?? 0);
	if ($isEnabled !== 1 && (int)$credits > 0) {
		$db->sql_query("UPDATE auth_panel_access
			SET is_enabled=1,
				plan_code='enabled',
				lock_reason='active',
				updated_at=NOW(),
				last_granted_at=NOW()
			WHERE user_id='" . $db->SanitizeForSQL($userId) . "'");
		$isEnabled = 1;
	}
	if ($isEnabled !== 1) {
		return false;
	}

	if ((int)$credits <= 0) {
		return false;
	}

	return true;
}

function programmit_panel_access_lock_reason($db, $userId) {
	$row = programmit_panel_access_get_row($db, $userId);
	if (!$row) {
		return 'pending_activation';
	}
	$reason = trim((string)($row['lock_reason'] ?? ''));
	return ($reason !== '') ? $reason : 'pending_activation';
}

function programmit_panel_access_current_user($db) {
	static $cacheReady = false;
	static $cacheRow = null;
	if ($cacheReady) {
		return $cacheRow;
	}
	$cacheReady = true;

	$userCookie = isset($_COOKIE['user']) ? $_COOKIE['user'] : '';
	if ($userCookie === '') {
		$cacheRow = null;
		return null;
	}

	$decoded = $db->decrypt_key($userCookie);
	if (!is_string($decoded) || $decoded === '') {
		$cacheRow = null;
		return null;
	}

	$parts = explode('|', $decoded);
	if (!isset($parts[0], $parts[1], $parts[2])) {
		$cacheRow = null;
		return null;
	}

	$userId = (int)$parts[0];
	$userName = trim((string)$parts[1]);
	$userPass = trim((string)$parts[2]);
	if ($userId <= 0 || $userName === '' || $userPass === '') {
		$cacheRow = null;
		return null;
	}

	$qry = $db->sql_query("SELECT user_id, user_name, user_level, credits, is_active, is_ban, status
		FROM users
		WHERE user_id='".$db->SanitizeForSQL($userId)."'
		AND user_name='".$db->SanitizeForSQL($userName)."'
		AND user_pass='".$db->SanitizeForSQL($userPass)."'
		LIMIT 1");
	$row = $db->sql_fetchrow($qry);
	$cacheRow = $row ? $row : null;
	return $cacheRow;
}

function programmit_panel_access_blocked_message_html($db) {
	$db->HandleError("Acceso restringido. Tu cuenta aun no tiene un plan activo en base de datos.");
	return $db->GetErrorMessage();
}

function programmit_secure_set_cookie($name, $value, $expire, $path = '/') {
	$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
	$params = array(
		'expires' => (int)$expire,
		'path' => (string)$path,
		'secure' => $secure,
		'httponly' => true,
		'samesite' => 'Lax'
	);

	if (PHP_VERSION_ID >= 70300) {
		setcookie((string)$name, (string)$value, $params);
		return;
	}

	$legacyPath = rtrim((string)$path, ';') . '; samesite=Lax';
	setcookie((string)$name, (string)$value, (int)$expire, $legacyPath, '', $secure, true);
}

function programmit_panel_access_guard_forms($db) {
	$scriptName = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', (string)$_SERVER['SCRIPT_NAME']) : '';
	if ($scriptName === '') {
		return;
	}
	$isForms = (strpos($scriptName, '/serverside/forms/') !== false);
	$isUsers = (strpos($scriptName, '/serverside/users/') !== false);
	if (!$isForms && !$isUsers) {
		return;
	}

	$base = strtolower((string)basename($scriptName));
	$publicForms = array(
		'index.php',
		'login.php',
		'register.php',
		'register_modern.php',
		'recovery.php',
		'requestcode.php',
		'magic_link.php',
		'contact_me.php'
	);
	if ($isForms && in_array($base, $publicForms, true)) {
		return;
	}
	$restrictedAllowedForms = array(
		'edit_profile.php',
		'change-pwd.php',
		'finance_create_recharge.php',
		'finance_payment_poll.php'
	);
	$restrictedAllowedUsers = array(
		'get-avatar.php',
		'get-avatar2.php'
	);

	$current = programmit_panel_access_current_user($db);
	if (!$current) {
		http_response_code(401);
		if (!headers_sent()) {
			header('Content-Type: text/html; charset=UTF-8');
			header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
			header('Pragma: no-cache');
		}
		$db->HandleError("Sesion no valida. Inicia sesion para continuar.");
		echo $db->GetErrorMessage();
		exit;
	}

	$allowed = programmit_panel_access_is_allowed(
		$db,
		(int)$current['user_id'],
		(string)$current['user_level'],
		(int)$current['credits']
	);

	if ($allowed) {
		return;
	}
	if ($isForms && in_array($base, $restrictedAllowedForms, true)) {
		return;
	}
	if ($isUsers && in_array($base, $restrictedAllowedUsers, true)) {
		return;
	}

	http_response_code(423);
	if (!headers_sent()) {
		header('Content-Type: text/html; charset=UTF-8');
		header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
		header('Pragma: no-cache');
	}
	echo programmit_panel_access_blocked_message_html($db);
	exit;
}
