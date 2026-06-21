<?php
chkSession();
$embed_raw = isset($_GET['embed']) ? strtolower(trim((string)$_GET['embed'])) : '';
$credit_logs_embed_admin = in_array($embed_raw, array('1', 'admin', 'yes'), true);
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'subadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller'){
	
}else{
	header("Location: /dashboard");	
	exit;
}
################################################################################
#------------------------------------------------------------------------------#
#  User List Page
#------------------------------------------------------------------------------#
################################################################################
$duration_sql = programmit_duration_select_options();
$smarty->assign("duration", $duration_sql);
$smarty->assign("credit_logs_embed_admin", $credit_logs_embed_admin ? 1 : 0);

$smarty->display("credit-logs.tpl");
?>
