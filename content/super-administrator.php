<?php
chkSession();
if($user_id_2 == 1){
	
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

$smarty->display("super-administrator.tpl");
?>