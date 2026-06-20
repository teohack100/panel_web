<?php
include __DIR__ . '/../db_config.php';
if (!isset($DB_driver)) {
	$DB_driver = strtolower(trim((string)getenv('DB_DRIVER')));
}
if ($DB_driver === '') {
	$DB_driver = 'pgsql';
}

if ($DB_driver === 'pgsql') {
	require __DIR__ . '/../mysql.class.php';
} else {
	require __DIR__ . '/mysql.class.php';
}

$db = new mysql_db();
$db->InitDB($DB_host,$DB_user,$DB_pass,$DB_name);
$db->SetWebsiteName('panel.programmit.com');
$db->SetWebsiteTitle('PROGRAMMIT PANEL');

?>
