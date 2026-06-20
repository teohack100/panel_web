<?php
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
	
}else{
	header("Location: /dashboard");	
}
################################################################################
#------------------------------------------------------------------------------#
#  administrator List Page
#------------------------------------------------------------------------------#
################################################################################
$duration_sql = programmit_duration_select_options();
$smarty->assign("duration", $duration_sql);

$smarty->display("administrator.tpl");
?>