<?php
if(is_logged_in($user)) {
	header("Location: ".$db->base_url()."index.php?p=dashboard");
	exit;
}

if(function_exists('programmit_control_is_host') && programmit_control_is_host($db)){
	if(!function_exists('programmit_control_security_allow_register') || !programmit_control_security_allow_register($db)){
		header("Location: ".$db->base_url()."index.php?p=login&control=register_blocked");
		exit;
	}
}

require_once __DIR__ . '/../includes/social_auth.php';
$social_host_blocked = function_exists('programmit_social_host_allows_oauth') ? !programmit_social_host_allows_oauth($db) : false;
$smarty->assign('social_host_blocked', $social_host_blocked ? 1 : 0);
$smarty->assign('register_notice_text', $social_host_blocked ? 'Registro con redes sociales deshabilitado en este host.' : '');
$smarty->assign('register_notice_class', 'danger');

// En produccion usamos templates compilados; solo recompilar en localhost.
$host = isset($_SERVER['HTTP_HOST']) ? strtolower((string)$_SERVER['HTTP_HOST']) : '';
$host = preg_replace('/:\d+$/', '', $host);
$isLocalHost = ($host === 'localhost' || $host === '127.0.0.1');
if($isLocalHost){
	if(method_exists($smarty, 'clearCompiledTemplate')){
		$smarty->clearCompiledTemplate("register.tpl");
	}
	$smarty->compile_check = true;
}
$smarty->display("register.tpl");
?>	
