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

function programmitSelfCategoryDurationText($label, $seconds)
{
	$time = $GLOBALS['db']->calc_time((int)$seconds);
	return $label . ': ' . $time['days'] . ' dia(s), ' . $time['hours'] . ' hora(s) y ' . $time['minutes'] . ' minuto(s)';
}

if($user_level_2 == 'normal')
{
	echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
	exit;
}

if(!empty($_POST['category']))
{
	$category = $db->encryptor('decrypt', $_POST['category']);
	$duration = '';
	$result = $db->sql_query("SELECT duration, vip_duration FROM users WHERE user_id='".$user_id_2."' LIMIT 1");
	$row = $db->sql_fetchrow($result);
	$duration_qry = $db->sql_query("SELECT * FROM duration ORDER BY id ASC");
	if($category == 'premium')
	{	
		while($durrows = $db->sql_fetchrow($duration_qry))
		{
			$durs = programmitSelfCategoryDurationText('Premium', $row['duration']);
			$thirtydays = 2592000;
			if($durrows['duration_time'] == $thirtydays)
			{				
				$duration .= 
				'<option class="form-control" value="'.base64_encode(urlencode($db->encryptor('encrypt',$durrows['id']))).'">
				'.(function_exists('programmit_translate_duration_name') ? programmit_translate_duration_name($durrows['duration_name']) : $durrows['duration_name']).'</option>';
			}
		}
	}
	else
	if($category == 'vip')
	{
		while($vipdurrows = $db->sql_fetchrow($duration_qry))
		{
			$durs = programmitSelfCategoryDurationText('VIP', $row['vip_duration']);
			$vipthirtydays = 2592000;
			$fithteendays = $vipthirtydays / 2;
			if($vipdurrows['duration_time'] == $fithteendays)
			{
				$duration .= 
				'<option value="'.base64_encode(urlencode($db->encryptor('encrypt',$vipdurrows['id']))).'">
				'.(function_exists('programmit_translate_duration_name') ? programmit_translate_duration_name($vipdurrows['duration_name']) : $vipdurrows['duration_name']).'</option>';
			}
		}
	}
	else
	if($category == 'private')
	{
		while($privatedurrows = $db->sql_fetchrow($duration_qry))
		{
			$durs = programmitSelfCategoryDurationText('Private', $row['private_duration']);
			$privatethirtydays = 2592000;
			$fithteendays = $privatethirtydays / 2;
			if($privatedurrows['duration_time'] == $fithteendays)
			{
				$duration .= 
				'<option value="'.base64_encode(urlencode($db->encryptor('encrypt',$privatedurrows['id']))).'">
				'.(function_exists('programmit_translate_duration_name') ? programmit_translate_duration_name($privatedurrows['duration_name']) : $privatedurrows['duration_name']).'</option>';
			}
		}
	}elseif($category != 'premium' || $category != 'vip' || $category != 'private'){
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}

	echo '<h4 class="text-center">Te quedan '.$durs.'.</h4>';
	echo '<div class="form-group">';
		echo '<label class="control-label" for="selfdurations">';
			echo '<i class="glyphicon glyphicon-time"></i> Duraciones propias:';
		echo '</label>';
		echo '<select id="selfdurations" name="selfdurations" class="form-control">';
			echo $duration;
		echo '</select>';
	echo '</div>';
}else{
	echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
	exit;
}

?>

