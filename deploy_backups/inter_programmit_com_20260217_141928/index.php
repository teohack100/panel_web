<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
include './includes/functions.php';
$p = $_GET['p']; // ?page=
$ex = "php"; // File extension
$folder_content = "content"; //
$main = "index"; // Main page
$error = "404"; // 404 error page



if(empty($p)) {
	include("$folder_content/$main.$ex");
} else if(file_exists("$folder_content/$p.$ex")) {
	include("$folder_content/$p.$ex");
} else if(file_exists("$folder_content/serverside/$p.$ex")) {
	include("$folder_content/serverside/$p.$ex");
} else if(file_exists("$folder_content/serverside/credits/$p.$ex")) {
	include("$folder_content/serverside/credits/$p.$ex");
} else {
	include("$folder_content/$error.$ex");
}
?>