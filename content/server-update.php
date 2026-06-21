<?php
chkSession();
$embed_raw = isset($_GET['embed']) ? strtolower(trim((string)$_GET['embed'])) : '';
$server_update_embed_admin = in_array($embed_raw, array('1', 'admin', 'yes'), true);
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator'){
	
}else{
	header("Location: /myaccount");	
	exit;
}

if(!$server_update_embed_admin){
	header("Location: ".$db->base_url()."admin.php#server-update-main");
	exit;
}

$smarty->assign("server_update_embed_admin", $server_update_embed_admin ? 1 : 0);
$smarty->display('server-update.tpl');
?>	
