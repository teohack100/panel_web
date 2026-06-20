<?php
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator'){
	
}else{
	header("Location: /myaccount");	
}
$smarty->display('serverupload.tpl');
?>	