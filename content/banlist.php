<?php
ini_set('max_execution_time', 150);
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
	
}else{
	header("Location: /myaccount");	
}
$smarty->display("banlist.tpl");
?>