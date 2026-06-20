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
if(isset($_POST['submitted'])){
	if($db->Contact()){
		echo $db->HandleSuccess("Your Message has been Sent! Successfully...");
		echo $db->GetSuccessMessage();
	}else{
		echo $db->GetErrorMessage();
	};
}
?>
