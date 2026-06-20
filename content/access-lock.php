<?php
chkSession();

if(!programmit_is_panel_restricted_user()){
	header("Location: ".$db->base_url()."index.php?p=dashboard");
	exit;
}

$smarty->assign("lock_title", "Acceso no disponible");
$smarty->assign("lock_subtitle", "Tu cuenta fue creada con 0 creditos. Activa un plan para habilitar la gestion completa del panel.");
$smarty->assign("lock_credits", (int)$credits_2);
$smarty->assign("lock_user", $user_name_2);
$smarty->assign("lock_reason", $panel_lock_reason_2);

// En produccion usamos templates compilados; solo recompilar en localhost.
$host = isset($_SERVER['HTTP_HOST']) ? strtolower((string)$_SERVER['HTTP_HOST']) : '';
$host = preg_replace('/:\d+$/', '', $host);
$isLocalHost = ($host === 'localhost' || $host === '127.0.0.1');
if($isLocalHost){
	if(method_exists($smarty, 'clearCompiledTemplate')){
		$smarty->clearCompiledTemplate('access-lock.tpl');
	}
	$smarty->compile_check = true;
}

$smarty->display("access-lock.tpl");
?>
