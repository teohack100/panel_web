<?php
chkSession();

$chk_active = $db->sql_query("SELECT user_name FROM users WHERE is_active=1 AND upline='".$user_id_2."' AND user_id!='".$user_id_2."'");
$chk_active = $db->sql_numrows($chk_active);
$smarty->assign("chk_active", $chk_active);

$chk_suspend = $db->sql_query("SELECT user_name FROM users WHERE is_active=0 AND upline='".$user_id_2."' AND user_id!='".$user_id_2."'");
$chk_suspend = $db->sql_numrows($chk_suspend);
$smarty->assign("chk_suspend", $chk_suspend);

$chk_banned = $db->sql_query("SELECT user_name FROM users WHERE is_ban=0 AND upline='".$user_id_2."' AND user_id!='".$user_id_2."'");
$chk_banned = $db->sql_numrows($chk_banned);
$smarty->assign("chk_banned", $chk_banned);

$chk_notused = $db->sql_query("SELECT code_name FROM vouchers WHERE is_used=0 AND reseller_id='".$user_id_2."'");
$chk_notused = $db->sql_numrows($chk_notused);
$smarty->assign("chk_notused", $chk_notused);

$chk_used = $db->sql_query("SELECT code_name FROM vouchers WHERE is_used=1 AND reseller_id='".$user_id_2."'");
$chk_used = $db->sql_numrows($chk_used);
$smarty->assign("chk_used", $chk_used);


$profile_qry = $db->sql_query("SELECT * FROM users_profile WHERE profile_id = '".$user_id_2."' LIMIT 1");
$profile_row = $db->sql_fetchrow($profile_qry);	
if($profile_row['profile_address'] == ''){
	$address_2 = "No Address";
}else{
	$address_2 = $profile_row['profile_address'];
}
if($profile_row['profile_number'] == ''){
	$number_2 = "No Contact Number";
}else{
	$number_2 = $profile_row['profile_number'];
}
if($profile_row['profile_image'] == ''){
	$default = $base_url.'profile/default.png';
	$profile_image = '<img src="'.$default.'" alt="'.$username_2.'">';
}else{
	$default = $base_url.'profile/'.$user_id_2.'/'.$profile_row['profile_image'];
	$profile_image = '<img src="'.$default.'" alt="'.$username_2.'">';
}
$smarty->assign('address_2', $address_2);
$smarty->assign('number_2', $number_2);
$smarty->assign('profile_image', $profile_image);
$smarty->display("editaccount.tpl");
?>