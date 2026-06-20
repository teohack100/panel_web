<?php
if(is_logged_in($user)) {
	header("Location: /myaccount");
}
$smarty->display("requestcode.tpl");
?>	