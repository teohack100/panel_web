<?php
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'subadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller'){
	
}else{
	header("Location: /dashboard");	
}
################################################################################
#------------------------------------------------------------------------------#
#  User List Page
#------------------------------------------------------------------------------#
################################################################################
$duration_sql = programmit_duration_select_options();
$smarty->assign("duration", $duration_sql);

$smarty->display("active-vip.tpl");
?>