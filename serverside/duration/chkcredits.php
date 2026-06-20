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
if($user_level_2 == 'normal')
{
	echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
	exit;
}

$chk_user = $db->sql_query("SELECT credits FROM users WHERE user_id ='".$user_id_2."' LIMIT 1 ");
$chk_row = $db->sql_fetchrow($chk_user);
if($chk_row['credits'] < 1)
{
	$credits = 'disabled="disabled"';
}else{
	$credits = '';
}
		
if($chk_row['credits'] > 0)
{
	$credits_count = '<label class="label label-info">'.$chk_row['credits'].'</b></label>';
}else{
	$credits_count = '<label class="label label-danger">'.$chk_row['credits'].'</b></label>';
}
echo
'<button type="button" class="btn btn-success btn-block" id="applyDuration" name="applyDuration"
onclick="selfreload()" '.$credits.'> Recargar para mi cuenta ~ Tus creditos '.$credits_count.'</button>';
?>

