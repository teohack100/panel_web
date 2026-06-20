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
		
if(empty($_POST['category']))
{
	echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
	exit;
}else{
	$category = $db->encryptor('decrypt', $_POST['category']);
	$info = '';
	$result = $db->sql_query("SELECT duration, vip_duration, private_duration FROM users WHERE user_id='".$user_id_2."' LIMIT 1");
	$row = $db->sql_fetchrow($result);
	if($row['duration'] > 0){
		$premium_status = '<font color="green">Duracion disponible</font>'; 
		$kunote = '<small><strong>Nota:</strong> La duracion se recargara desde tu saldo disponible.</small>';
	}else{
		$premium_status = '<font color="red">Duracion Premium</font>'; 
		$kunote = '<small><strong><font color="red">Nota:</strong> Tu saldo de duracion es insuficiente.</font></small>';
	}

	if($row['vip_duration'] > 0){
		$vip_status = '<font color="green">Duracion VIP disponible</font>'; 
		$kunote2 = '<small><strong>Nota:</strong> La duracion VIP se recargara desde tu saldo VIP.</small>';
	}else{
		$vip_status = '<font color="red">Duracion VIP</font>'; 
		$kunote2 = '<small><strong><font color="red">Nota:</strong> Tu saldo VIP es insuficiente.</font></small>';
	}

	if($row['private_duration'] > 0){
		$priv_status = '<font color="green">Duracion Private disponible</font>';
		$kunote3 = '<small><strong>Nota:</strong> La duracion Private se recargara desde tu saldo Private.</small>';
	}else{
		$priv_status = '<font color="red">Duracion Private</font>'; 
		$kunote3 = '<small><strong><font color="red">Nota:</strong> Tu saldo Private es insuficiente.</font></small>';
	}

	$packNote = '<small><strong>Regla:</strong> MDURATION usa primero tu saldo de duracion. Si no alcanza, el panel descuenta <strong>1 credito</strong> y carga <strong>7 dias</strong> automaticamente hasta cubrir el tiempo solicitado.</small>';
	$removeNote = '<small><strong>Nota:</strong> La remocion de duracion solo esta disponible para Super-Administradores.</small>';

	if($category == 'premium')
	{
		$dur = $db->calc_time($row['duration']);
		$vip = $dur['days'] . " day(s), " . $dur['hours'] . " hour(s) and " . $dur['minutes'] . " minutes";
		$info .= '<div class="form-group">';
		$info .=	'<label for="premiumduration" class="font-weight-bold">'.$premium_status.':</label>';
		$info .=	'<div class="input-group">';
    	$info .=		'<input class="form-control" value="'.$vip.'" disabled>';
    	$info .=		'<div class="input-group-append">';
        $info .=            '<span class="input-group-text"><i class="fas fa-user-clock"></i></span>';
        $info .=        '</div>';
        $info .=    '</div>';
		$info .='</div>';
		
		$info .= $kunote;
		if(!($user_id_2 == 1 || $user_level_2 == 'superadmin')){
			$info .= '<br>'.$packNote;
		}
		$info .= '<br>'.$removeNote;
	}
	else
	if($category == 'vip')
	{
		$dur = $db->calc_time($row['vip_duration']);
		$premium = $dur['days'] . " day(s), " . $dur['hours'] . " hour(s) and " . $dur['minutes'] . " minute(s)";
		$info .= '<div class="form-group">';
		$info .=	'<label for="premiumduration" class="font-weight-bold">'.$vip_status.':</label>';
		$info .=	'<div class="input-group">';
    	$info .=		'<input class="form-control" value="'.$premium.'" disabled>';
    	$info .=		'<div class="input-group-append">';
        $info .=            '<span class="input-group-text"><i class="fas fa-user-clock"></i></span>';
        $info .=        '</div>';
        $info .=    '</div>';
		$info .='</div>';
		
		$info .= $kunote2;
		if(!($user_id_2 == 1 || $user_level_2 == 'superadmin')){
			$info .= '<br>'.$packNote;
		}
		$info .= '<br>'.$removeNote;
	}
	else
	if($category == 'private')
	{
		$dur2 = $db->calc_time($row['private_duration']);
		$private = $dur2['days'] . " day(s), " . $dur2['hours'] . " hour(s) and " . $dur2['minutes'] . " minutes";
		$info .= '<div class="form-group">';
		$info .=	'<label for="premiumduration" class="font-weight-bold">'.$priv_status.':</label>';
		$info .=	'<div class="input-group">';
    	$info .=		'<input class="form-control" value="'.$private.'" disabled>';
    	$info .=		'<div class="input-group-append">';
        $info .=            '<span class="input-group-text"><i class="fas fa-user-clock"></i></span>';
        $info .=        '</div>';
        $info .=    '</div>';
		$info .='</div>';
		
		$info .= $kunote3;
		if(!($user_id_2 == 1 || $user_level_2 == 'superadmin')){
			$info .= '<br>'.$packNote;
		}
		$info .= '<br>'.$removeNote;

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

