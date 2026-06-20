<?php
date_default_timezone_set('America/Mexico_City');
$DB_driver = strtolower(trim((string)getenv('DB_DRIVER')));
if ($DB_driver === '') {
	$DB_driver = 'pgsql';
}

$DB_host = getenv('DB_HOST');
if ($DB_host === false || $DB_host === '') {
	$DB_host = '127.0.0.1';
}

$DB_port = (int)getenv('DB_PORT');
if ($DB_port <= 0) {
	$DB_port = ($DB_driver === 'pgsql') ? 5432 : 3306;
}

$DB_user = getenv('DB_USER');
if ($DB_user === false || $DB_user === '') {
	$DB_user = ($DB_driver === 'pgsql') ? 'programm_panel_pg' : 'programm_panel';
}

$DB_pass = getenv('DB_PASS');
if ($DB_pass === false || $DB_pass === '') {
	$DB_pass = ($DB_driver === 'pgsql') ? 'PgmPanelPG_2026_R4h7KqN2' : 'admin123';
}

$DB_name = getenv('DB_NAME');
if ($DB_name === false || $DB_name === '') {
	$DB_name = 'programm_panel';
}

$DB_schema = getenv('DB_SCHEMA');
if ($DB_schema === false || $DB_schema === '') {
	$DB_schema = ($DB_driver === 'pgsql') ? 'programm_panel' : '';
}

// Backward compatibility placeholder. Real connection is created in mysql.class.php.
$mysqli = null;

