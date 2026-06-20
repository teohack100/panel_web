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

if(isset($_POST['id']))
{
	$id = $this->Sanitize($_POST['id']);
	$qry = $this->sql_query("SELECT id FROM download WHERE id='".$this->SanitizeForSQL($id)."' LIMIT 1");
	if($this->sql_numrows($qry) > 0){
		$row = $this->sql_fetchassoc($qry);
		$this->sql_query("Update download SET downloaded=downloaded+1 WHERE id='".$row['id']."'");
	}
}else{
	echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
	exit;
}
?>

