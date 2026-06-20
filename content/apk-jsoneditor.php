<?php
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin'  || $user_level_2 == 'administrator'){
	
}else{
	header("Location: /myaccount");	
}

$file = "sec.json";
$myfile = fopen($file, "r") or die("Unable to open file!");
$editor = fread($myfile,filesize($file));
fwrite($myfile, $editor);
fclose($myfile);

$smarty->assign("editor", $editor);
$smarty->display("apk-jsoneditor.tpl");
?>