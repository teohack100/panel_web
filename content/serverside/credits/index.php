<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
$base_url = $db->base_url();
header('Location: '. $base_url);
?>