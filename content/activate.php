<?php
if(is_logged_in($user)) {
	header("Location: /myaccount");
}
################################################################################
#------------------------------------------------------------------------------#
#  Activate Function
#------------------------------------------------------------------------------#
################################################################################
$code = $db->Sanitize(trim($_GET['code']));
$code = $db->SanitizeForSQL($code);
$email = $db->Sanitize(trim($_GET['email']));
$email = $db->SanitizeForSQL($email);
if($code != "" AND $email != ""){
	$result = $db->sql_query("SELECT user_id, is_validated FROM users WHERE code='$code' AND user_email='$email'");
	if($db->sql_numrows($result) == 1){
		$row = $db->sql_fetchrow($result);
		if($row['is_validated'] != 1) {
			$sql = $db->sql_query("UPDATE users SET is_validated=1 WHERE user_id='$row[user_id]'");
			$error = 0;
		} else {
			$error = 1;
		}
	} else {
		$error = 2;
	}
} else {
		header("Location: /login");
}
$smarty->assign("error", $error);
$smarty->display("activate.tpl");
?>