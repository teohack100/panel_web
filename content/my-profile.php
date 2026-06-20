<?php
chkSession();

$chk_active = $db->sql_query("SELECT user_name FROM users WHERE is_active=1 AND upline='".$user_id_2."' AND user_id!='".$user_id_2."'");
$chk_active = $db->sql_numrows($chk_active);
$smarty->assign("chk_active", $chk_active);

$chk_freeze = $db->sql_query("SELECT user_name FROM users WHERE is_freeze=1 AND status='freeze' AND upline='".$user_id_2."' AND user_id!='".$user_id_2."'");
$chk_freeze = $db->sql_numrows($chk_freeze);
$smarty->assign("chk_freeze", $chk_freeze);

$chk_suspend = $db->sql_query("SELECT user_name FROM users WHERE is_active=0 AND status='suspended' AND upline='".$user_id_2."' AND user_id!='".$user_id_2."'");
$chk_suspend = $db->sql_numrows($chk_suspend);
$smarty->assign("chk_suspend", $chk_suspend);

$chk_banned = $db->sql_query("SELECT user_name FROM users WHERE is_ban=1 AND upline='".$user_id_2."' AND user_id!='".$user_id_2."'");
$chk_banned = $db->sql_numrows($chk_banned);
$smarty->assign("chk_banned", $chk_banned);

$chk_notused = $db->sql_query("SELECT code_name FROM vouchers WHERE is_used=0 AND reseller_id='".$user_id_2."'");
$chk_notused = $db->sql_numrows($chk_notused);
$smarty->assign("chk_notused", $chk_notused);

$chk_used = $db->sql_query("SELECT code_name FROM vouchers WHERE is_used=1 AND reseller_id='".$user_id_2."'");
$chk_used = $db->sql_numrows($chk_used);
$smarty->assign("chk_used", $chk_used);


$read_cookie = explode("|", $db->decrypt_key($user));
$userdata = $db->sql_query("SELECT * FROM users WHERE user_name='$read_cookie[1]' AND user_pass='$read_cookie[2]'");
$row = $db->sql_fetchrow($userdata);

$smarty->assign("credits", $row['credits']);
$smarty->assign("ss_id", $row['ss_id']);
$smarty->assign("user_name", $row['user_name']);
$smarty->assign("user_level", $row['user_level']);
//List Of Durations
$duration_sql = programmit_duration_select_options();
$smarty->assign("duration", $duration_sql);





$smarty->display("my-profile.tpl");
?>