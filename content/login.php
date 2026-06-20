<?php
if (isset($_GET['force_login']) && $_GET['force_login'] == 1) {
	clear_auth_cookies();
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Pragma: no-cache");
	header("Location: ".$db->base_url()."index.php?p=login");
	exit;
}
$_programmit_control_host = (function_exists('programmit_control_is_host') && programmit_control_is_host($db));
$_post_login_redirect = $db->base_url()."index.php?p=dashboard";
if(is_logged_in($user)) {
	header("Location: ".$_post_login_redirect);
}

require_once __DIR__ . '/../includes/social_auth.php';
$social_host_blocked = function_exists('programmit_social_host_allows_oauth') ? !programmit_social_host_allows_oauth($db) : false;
$smarty->assign('social_host_blocked', $social_host_blocked ? 1 : 0);
$control_is_host = $_programmit_control_host ? 1 : 0;
$control_allow_register = (!$control_is_host || (function_exists('programmit_control_security_allow_register') && programmit_control_security_allow_register($db)));
$control_allow_magic = (!$control_is_host || (function_exists('programmit_control_security_allow_magic_login') && programmit_control_security_allow_magic_login($db)));
$smarty->assign('control_is_host', $control_is_host ? 1 : 0);
$smarty->assign('control_allow_register', $control_allow_register ? 1 : 0);
$smarty->assign('control_allow_magic', $control_allow_magic ? 1 : 0);
$smarty->assign('post_login_redirect', $_post_login_redirect);

$magic_notice_text = '';
$magic_notice_class = 'info';
if(isset($_GET['magic'])){
	$magic_state = trim((string)$_GET['magic']);
	if($magic_state === 'sent'){
		$magic_notice_text = 'Enlace magico generado. Revisa tu email.';
		$magic_notice_class = 'success';
	}elseif($magic_state === 'ok'){
		$magic_notice_text = 'Sesion iniciada correctamente con enlace magico.';
		$magic_notice_class = 'success';
	}elseif($magic_state === 'expired'){
		$magic_notice_text = 'El enlace magico expiro. Solicita uno nuevo.';
		$magic_notice_class = 'danger';
	}elseif($magic_state === 'invalid'){
		$magic_notice_text = 'El enlace magico no es valido.';
		$magic_notice_class = 'danger';
	}
}

if(isset($_GET['social'])){
	$social_state = trim((string)$_GET['social']);
	if($social_state === 'disabled'){
		$magic_notice_text = 'Inicio social desactivado. Activalo en auth_oauth_providers o en includes/social_config.php.';
		$magic_notice_class = 'danger';
	}elseif($social_state === 'config_missing'){
		$magic_notice_text = 'Falta configurar client_id/client_secret en auth_oauth_providers o variables PM_SOCIAL_*.';
		$magic_notice_class = 'danger';
	}elseif($social_state === 'bad_provider'){
		$magic_notice_text = 'Proveedor social invalido.';
		$magic_notice_class = 'danger';
	}elseif($social_state === 'apple_not_ready'){
		$magic_notice_text = 'Apple Login aun no esta habilitado en este panel.';
		$magic_notice_class = 'danger';
	}elseif($social_state === 'state_error'){
		$magic_notice_text = 'Sesion OAuth invalida o expirada. Intenta otra vez.';
		$magic_notice_class = 'danger';
	}elseif($social_state === 'denied'){
		$magic_notice_text = 'Cancelaste el acceso social.';
		$magic_notice_class = 'danger';
	}elseif($social_state === 'code_missing'){
		$magic_notice_text = 'El proveedor no devolvio codigo de autorizacion.';
		$magic_notice_class = 'danger';
	}elseif($social_state === 'token_error'){
		$magic_notice_text = 'No se pudo obtener token del proveedor social.';
		$magic_notice_class = 'danger';
	}elseif($social_state === 'session_error'){
		$magic_notice_text = 'No se pudo crear la sesion OAuth segura. Intenta nuevamente.';
		$magic_notice_class = 'danger';
	}elseif($social_state === 'profile_error'){
		$magic_notice_text = 'No se pudo leer el perfil del proveedor social.';
		$magic_notice_class = 'danger';
	}elseif($social_state === 'no_email'){
		$magic_notice_text = 'Tu cuenta social no envio email. Usa otra cuenta o habilita permiso de email.';
		$magic_notice_class = 'danger';
	}elseif($social_state === 'user_error'){
		$magic_notice_text = 'No se pudo vincular o crear el usuario local.';
		$magic_notice_class = 'danger';
	}elseif($social_state === 'account_blocked'){
		$magic_notice_text = 'La cuenta esta suspendida, congelada o bloqueada.';
		$magic_notice_class = 'danger';
	}elseif($social_state === 'host_blocked'){
		$magic_notice_text = 'Inicio social bloqueado en este subdominio de control por seguridad.';
		$magic_notice_class = 'danger';
	}elseif($social_state === 'signup_disabled'){
		$magic_notice_text = 'Registro social deshabilitado. Solo pueden entrar cuentas sociales ya vinculadas.';
		$magic_notice_class = 'danger';
	}
}

if(isset($_GET['control'])){
	$control_state = trim((string)$_GET['control']);
	if($control_state === 'register_blocked'){
		$magic_notice_text = 'Registro deshabilitado en host de control.';
		$magic_notice_class = 'danger';
	}elseif($control_state === 'magic_blocked'){
		$magic_notice_text = 'Magic link deshabilitado en host de control.';
		$magic_notice_class = 'danger';
	}elseif($control_state === 'access_denied'){
		$magic_notice_text = 'Acceso de administracion restringido para esta cuenta.';
		$magic_notice_class = 'danger';
	}elseif($control_state === 'ip_blocked'){
		$magic_notice_text = 'IP no autorizada para entrar al host de control.';
		$magic_notice_class = 'danger';
	}
}

if($social_host_blocked && $magic_notice_text === ''){
	$magic_notice_text = 'Inicio social deshabilitado en este host.';
	$magic_notice_class = 'info';
}

$spam = $db->encryptor('encrypt', 'try to hack');
$spam = $db->encryptor('encrypt', $spam);
$smarty->assign('code', $spam);
$smarty->assign('magic_notice_text', $magic_notice_text);
$smarty->assign('magic_notice_class', $magic_notice_class);

// Solo forzar recompilacion en entorno local para no penalizar produccion.
$host = isset($_SERVER['HTTP_HOST']) ? strtolower((string)$_SERVER['HTTP_HOST']) : '';
$host = preg_replace('/:\d+$/', '', $host);
$isLocalHost = ($host === 'localhost' || $host === '127.0.0.1');
if($isLocalHost){
	if(method_exists($smarty, 'clearCompiledTemplate')){
		$smarty->clearCompiledTemplate('login.tpl');
	}
	$smarty->compile_check = true;
}

$smarty->display("login.tpl");
?>
