<?php
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator'){
	
}else{
	header("Location: /myaccount");	
}
################################################################################
#------------------------------------------------------------------------------#
#  Reseller List Page
#------------------------------------------------------------------------------#
################################################################################
$duration_sql = programmit_duration_select_options();
$smarty->assign("duration", $duration_sql);

$smarty->display("reseller.tpl");
?>