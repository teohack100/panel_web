<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
$root = __DIR__;
while (!is_file($root . '/includes/functions.php')) {
    $parent = dirname($root);
    if ($parent === $root) { break; }
    $root = $parent;
}
require $root . '/includes/functions.php';
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin')
{
	if(isset($_POST['submitted']))
	{
		$files = "update-v2";
		$data = $_REQUEST['guicode'];
		$fh = fopen($files, "w");
		fwrite($fh, $data);
		fclose($fh);
		$db->HandleSuccess("Successfully Updated!");
		echo $db->GetSuccessMessage();
	}else{
		$db->RedirectToURL($db->base_url());
		exit;	
	}
}else{
	$db->RedirectToURL($db->base_url().'/dashboard');
    exit;
}

?>
