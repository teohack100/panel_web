<?php
date_default_timezone_set('America/Mexico_City');

if (!function_exists('programmit_env_get')) {
	function programmit_env_get($key) {
		$value = getenv((string)$key);
		if ($value !== false && $value !== '') {
			return (string)$value;
		}
		if (isset($_ENV[$key]) && (string)$_ENV[$key] !== '') {
			return (string)$_ENV[$key];
		}
		if (isset($_SERVER[$key]) && (string)$_SERVER[$key] !== '') {
			return (string)$_SERVER[$key];
		}
		return '';
	}
}

if (!function_exists('programmit_load_env_file')) {
	function programmit_load_env_file($path, $override = false) {
		if (!is_file($path) || !is_readable($path)) {
			return;
		}

		$lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		if (!is_array($lines)) {
			return;
		}

		foreach ($lines as $line) {
			$line = trim((string)$line);
			if ($line === '' || strpos($line, '#') === 0) {
				continue;
			}

			$delimiterPos = strpos($line, '=');
			if ($delimiterPos === false) {
				continue;
			}

			$key = trim(substr($line, 0, $delimiterPos));
			$value = trim(substr($line, $delimiterPos + 1));
			if ($key === '') {
				continue;
			}

			if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') || (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
				$value = substr($value, 1, -1);
			}

			if (!$override && programmit_env_get($key) !== '') {
				continue;
			}

			putenv($key . '=' . $value);
			$_ENV[$key] = $value;
			$_SERVER[$key] = $value;
		}
	}
}

$projectRoot = dirname(__DIR__);
programmit_load_env_file($projectRoot . DIRECTORY_SEPARATOR . '.env', false);
programmit_load_env_file($projectRoot . DIRECTORY_SEPARATOR . '.env.local', true);

$DB_driver = strtolower(trim(programmit_env_get('DB_DRIVER')));
if ($DB_driver === '') {
	$DB_driver = (DIRECTORY_SEPARATOR === '\\') ? 'mysql' : 'pgsql';
}

$DB_host = trim(programmit_env_get('DB_HOST'));
if ($DB_host === '') {
	$DB_host = '127.0.0.1';
}

$DB_port = (int)programmit_env_get('DB_PORT');
if ($DB_port <= 0) {
	$DB_port = ($DB_driver === 'pgsql') ? 5432 : 3306;
}

$DB_user = trim(programmit_env_get('DB_USER'));
if ($DB_user === '') {
	$DB_user = ($DB_driver === 'pgsql') ? 'programm_panel_pg' : 'programm_panel';
}

$DB_pass = programmit_env_get('DB_PASS');
if ($DB_pass === '') {
	$DB_pass = ($DB_driver === 'pgsql') ? 'PgmPanelPG_2026_R4h7KqN2' : 'admin123';
}

$DB_name = trim(programmit_env_get('DB_NAME'));
if ($DB_name === '') {
	$DB_name = 'programm_panel';
}

$DB_schema = trim(programmit_env_get('DB_SCHEMA'));
if ($DB_schema === '') {
	$DB_schema = ($DB_driver === 'pgsql') ? 'programm_panel' : '';
}

// Backward compatibility placeholder. Real connection is created in mysql.class.php.
$mysqli = null;

