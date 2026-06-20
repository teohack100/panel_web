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

function programmitLegacyDurationInfoLabel($label, $seconds, $isPositive)
{
	$color = $isPositive ? 'green' : 'red';
	$time = $GLOBALS['db']->calc_time((int)$seconds);
	$text = $time['days'] . ' dia(s), ' . $time['hours'] . ' hora(s) y ' . $time['minutes'] . ' minuto(s)';

	return array(
		'title' => '<font color="' . $color . '">' . $label . '</font>',
		'value' => $text
	);
}
		
if(empty($_POST['category']))
{
	echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
	exit;
}else{
	$category = $db->encryptor('decrypt', $_POST['category']);
	$info = '';
	$result = $db->sql_query("SELECT duration, vip_duration, private_duration FROM users WHERE user_id='".$user_id_2."' LIMIT 1");
	$row = $db->sql_fetchrow($result);
	$premiumInfo = programmitLegacyDurationInfoLabel('Duracion Premium disponible', $row['duration'], ((int)$row['duration']) > 0);
	$vipInfo = programmitLegacyDurationInfoLabel('Duracion VIP disponible', $row['vip_duration'], ((int)$row['vip_duration']) > 0);
	$privateInfo = programmitLegacyDurationInfoLabel('Duracion Private disponible', $row['private_duration'], ((int)$row['private_duration']) > 0);

	if($category == 'premium')
	{
		$info .= '<div class="form-group">';
		$info .=	'<label for="premiumduration" class="font-weight-bold">'.$vipInfo['title'].':</label>';
		$info .=	'<div class="input-group">';
    	$info .=		'<input class="form-control" value="'.$vipInfo['value'].'" disabled>';
    	$info .=		'<div class="input-group-append">';
        $info .=            '<span class="input-group-text"><i class="fas fa-user-clock"></i></span>';
        $info .=        '</div>';
        $info .=    '</div>';
		$info .='</div>';
		
		$info .= '<div class="form-group">';
		$info .=	'<label for="premiumduration" class="font-weight-bold">'.$privateInfo['title'].':</label>';
		$info .=	'<div class="input-group">';
    	$info .=		'<input class="form-control" value="'.$privateInfo['value'].'" disabled>';
    	$info .=		'<div class="input-group-append">';
        $info .=            '<span class="input-group-text"><i class="fas fa-user-clock"></i></span>';
        $info .=        '</div>';
        $info .=    '</div>';
		$info .='</div>';
		
		$info .= '<small><strong>Nota:</strong> Convertir (VIP x 2) + (PRIVATE x 3) = PREMIUM</small>';
	}
	else
	if($category == 'vip')
	{
		$info .= '<div class="form-group">';
		$info .=	'<label for="premiumduration" class="font-weight-bold">'.$premiumInfo['title'].':</label>';
		$info .=	'<div class="input-group">';
    	$info .=		'<input class="form-control" value="'.$premiumInfo['value'].'" disabled>';
    	$info .=		'<div class="input-group-append">';
        $info .=            '<span class="input-group-text"><i class="fas fa-user-clock"></i></span>';
        $info .=        '</div>';
        $info .=    '</div>';
		$info .='</div>';
		
		$info .= '<div class="form-group">';
		$info .=	'<label for="premiumduration" class="font-weight-bold">'.$privateInfo['title'].':</label>';
		$info .=	'<div class="input-group">';
    	$info .=		'<input class="form-control" value="'.$privateInfo['value'].'" disabled>';
    	$info .=		'<div class="input-group-append">';
        $info .=            '<span class="input-group-text"><i class="fas fa-user-clock"></i></span>';
        $info .=        '</div>';
        $info .=    '</div>';
		$info .='</div>';
		
		$info .= '<small><strong>Nota:</strong> Convertir (PREMIUM / 2) + (PRIVATE / 3 x 2) = VIP</small>';
	}
	else
	if($category == 'private')
	{
		$info .= '<div class="form-group">';
		$info .=	'<label for="premiumduration" class="font-weight-bold">'.$premiumInfo['title'].':</label>';
		$info .=	'<div class="input-group">';
    	$info .=		'<input class="form-control" value="'.$premiumInfo['value'].'" disabled>';
    	$info .=		'<div class="input-group-append">';
        $info .=            '<span class="input-group-text"><i class="fas fa-user-clock"></i></span>';
        $info .=        '</div>';
        $info .=    '</div>';
		$info .='</div>';
		
		$info .= '<div class="form-group">';
		$info .=	'<label for="premiumduration" class="font-weight-bold">'.$vipInfo['title'].':</label>';
		$info .=	'<div class="input-group">';
    	$info .=		'<input class="form-control" value="'.$vipInfo['value'].'" disabled>';
    	$info .=		'<div class="input-group-append">';
        $info .=            '<span class="input-group-text"><i class="fas fa-user-clock"></i></span>';
        $info .=        '</div>';
        $info .=    '</div>';
		$info .='</div>';
		
		$info .= '<small><strong>Nota:</strong> Convertir (PREMIUM / 3) + (VIP / 2) = PRIVATE</small>';

	}elseif($category != 'premium' || $category != 'vip' || $category != 'private'){
		$db->RedirectToURL($db->base_url());
		exit;
	}
	$encrypt_dur = $db->encryptor('encrypt', $row['duration']);
	$encrypt_vip = $db->encryptor('encrypt', $row['vip_duration']);
	$encrypt_priv = $db->encryptor('encrypt', $row['private_duration']);
	echo $info;
	echo '<input type="hidden" class="form-control" id="qcode" name="qcode" value="'.$encrypt_dur.'">';
	echo '<input type="hidden" class="form-control" id="rcode" name="rcode" value="'.$encrypt_vip.'">';
	echo '<input type="hidden" class="form-control" id="pcode" name="pcode" value="'.$encrypt_priv.'">';
}
?>

