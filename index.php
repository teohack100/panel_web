<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');

function programmit_public_page_cache_path($pageKey) {
	$tmpDir = rtrim((string)sys_get_temp_dir(), '/\\');
	if ($tmpDir === '') {
		$tmpDir = '/tmp';
	}
	$cacheDir = $tmpDir . DIRECTORY_SEPARATOR . 'programmit_bootstrap';
	if (!is_dir($cacheDir)) {
		@mkdir($cacheDir, 0775, true);
	}
	$pageKey = preg_replace('/[^a-z0-9_-]/i', '', (string)$pageKey);
	if ($pageKey === '') {
		$pageKey = 'home';
	}
	return $cacheDir . DIRECTORY_SEPARATOR . 'public_page_' . strtolower($pageKey) . '.html';
}

function programmit_has_auth_cookie_present() {
	foreach (array('user', 'user_name', 'user_id', 'full_name', 'user_email', 'panel_admin_auth') as $cookieName) {
		if (isset($_COOKIE[$cookieName]) && (string)$_COOKIE[$cookieName] !== '') {
			return true;
		}
	}
	return false;
}

function programmit_can_use_public_cache($page) {
	if (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'GET') {
		return false;
	}
	if (programmit_has_auth_cookie_present()) {
		return false;
	}
	$page = trim((string)$page);
	if ($page === '') {
		return (count($_GET) === 0);
	}
	if ($page === 'login') {
		return (count($_GET) === 1 && isset($_GET['p']));
	}
	return false;
}

$programmit_page_candidate = isset($_GET['p']) ? trim((string)$_GET['p']) : '';
$programmit_cache_enabled = false;
$programmit_cache_file = '';
if (programmit_can_use_public_cache($programmit_page_candidate)) {
	$programmit_cache_enabled = true;
	$programmit_cache_file = programmit_public_page_cache_path($programmit_page_candidate === '' ? 'home' : $programmit_page_candidate);
	$cacheTtlSeconds = 300;
	clearstatcache(true, $programmit_cache_file);
	if (is_file($programmit_cache_file) && (time() - (int)@filemtime($programmit_cache_file)) <= $cacheTtlSeconds) {
		if (!headers_sent()) {
			header('Content-Type: text/html; charset=UTF-8');
		}
		readfile($programmit_cache_file);
		exit;
	}
	ob_start();
}

include './includes/functions.php';
$p = $_GET['p']; // ?page=
$ex = "php"; // File extension
$folder_content = "content"; //
$main = "index"; // Main page
$error = "404"; // 404 error page

$p = isset($p) ? trim((string)$p) : '';
if(function_exists('programmit_is_panel_restricted_user') && function_exists('programmit_can_access_panel_page')){
	if(programmit_is_panel_restricted_user() && !programmit_can_access_panel_page($p)){
		header("Location: ".$db->base_url()."index.php?p=access-lock");
		exit;
	}
}

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

if ($programmit_cache_enabled) {
	$programmit_body = ob_get_contents();
	if (is_string($programmit_body) && $programmit_body !== '') {
		@file_put_contents($programmit_cache_file, $programmit_body, LOCK_EX);
	}
	ob_end_flush();
}
?>
