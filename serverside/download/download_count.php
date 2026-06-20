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

if(!isset($_GET['id']) || empty($_GET['id']))
{
	echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
	exit;
}else{
	$data = array();
	$id = $db->Sanitize($_GET['id']);
	$qry = $db->sql_query("SELECT views, downloaded  FROM download WHERE id='".$db->SanitizeForSQL($id)."' LIMIT 1");
	if($db->sql_numrows($qry) > 0)
	{
		$row = $db->sql_fetchassoc($qry);
		$data['views'] = $row['views'];
		$data['downloaded'] = $row['downloaded'];
	}
	echo json_encode($data);
}
?>
