<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();

function programmit_profile_escape_html($value)
{
	return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function programmit_profile_normalize_facebook_url($value)
{
	$value = trim((string)$value);
	if($value === ''){
		return '';
	}
	if(preg_match('~^https?://~i', $value)){
		return $value;
	}
	if(preg_match('~^(www\.)?facebook\.com/~i', $value)){
		return 'https://' . ltrim($value, '/');
	}
	$value = ltrim($value, '@/');
	if($value !== '' && preg_match('~^[A-Za-z0-9._/-]+$~', $value)){
		return 'https://www.facebook.com/' . $value;
	}
	return 'https://www.facebook.com/';
}

function programmit_profile_access_identity($userName, $userEmail)
{
	$userName = trim((string)$userName);
	$userEmail = trim((string)$userEmail);

	if($userName !== '' && (filter_var($userName, FILTER_VALIDATE_EMAIL) || strcasecmp($userName, $userEmail) === 0)){
		$value = ($userEmail !== '' ? $userEmail : $userName);
		return array(
			'label' => 'Correo',
			'value' => $value
		);
	}

	$value = ($userName !== '' ? $userName : $userEmail);
	return array(
		'label' => 'Usuario',
		'value' => $value
	);
}

function programmit_profile_role_label($userId, $userLevel)
{
	$userId = (int)$userId;
	$userLevel = strtolower(trim((string)$userLevel));

	if($userId === 1 || $userLevel === 'superadmin'){
		return 'Super Administrador';
	}
	if($userLevel === 'administrator'){
		return 'Administrador';
	}
	if($userLevel === 'subadmin'){
		return 'Sub-Administrador';
	}
	if($userLevel === 'reseller'){
		return 'Reseller';
	}
	if($userLevel === 'subreseller'){
		return 'Sub-Reseller';
	}
	return 'Cliente normal';
}



$qry = $db->sql_query("SELECT 
u.user_id, u.ss_id, u.user_name, u.full_name, u.user_email, u.user_level, u.credits, u.bandwidth_free, u.bandwidth_ph, u.bandwidth_premium, u.bandwidth_private, u.bandwidth_vip, u.duration, u.vip_duration, u.private_duration,
p.profile_image, p.profile_number, p.profile_fb, p.profile_address
FROM 
users as u  
LEFT JOIN
users_profile as p
ON
u.user_id = p.profile_id
WHERE 
u.user_id='".$db->SanitizeForSQL($user_id_2)."' LIMIT 1");
$row = $db->sql_fetchrow($qry);
$values = array();	

if($row)
{	

	$ss_id = $row['ss_id'];
	$credits = $row['credits'];

	$profile_number = $row['profile_number'];
	$profile_address = $row['profile_address'];
	$profile_fb = $row['profile_fb'];
	$profile_email = $row['user_email'];

	$profile_number_3 = $row['profile_number'];
	$profile_address_3 = $row['profile_address'];
	$profile_fb_3 = $row['profile_fb'];

	$default = $base_url.'profile/default.png';
	$profile = $base_url.'profile/'.$user_id_2.'/'.$row['profile_image'];
	if($row['profile_image'] == ''){
		$profile_image = '<img class="rounded-circle" src="'.$default.'" alt="Perfil por defecto" style="width:100%; height:100%; object-fit:cover; display:block; border-radius:50%!important;">';
	}else{
		$profile_image = '<img class="rounded-circle" src="'.$profile.'" alt="Perfil '.$user_id_2.'" style="width:100%; height:100%; object-fit:cover; display:block; border-radius:50%!important;">';
	}
	if($profile_number == ''){
		$profile_number = '<label class="text-success">Sin actualizar</label>';
	}else{
		$profile_number = '<label class="text-success">'.programmit_profile_escape_html($profile_number).'</label>';
	}
	if($profile_address == ''){
		$profile_address = '<label class="text-success">Sin actualizar</label>';
	}else{
		$profile_address = '<label class="text-success">'.programmit_profile_escape_html($profile_address).'</label>';
	}
	if($profile_email == ''){
		$profile_email = '<label class="text-success">Sin actualizar</label>';
	}else{
		$profile_email = '<label class="text-success">'.programmit_profile_escape_html($profile_email).'</label>';
	}
	if($profile_fb == ''){
		$profile_fb = '<label class="text-success">Sin actualizar</label>';
	}else{
		$facebookUrl = programmit_profile_normalize_facebook_url($profile_fb);
		$profile_fb = '<a class="text text-link text-success" href="'.programmit_profile_escape_html($facebookUrl).'" target="_blank" rel="noopener noreferrer">Abrir Facebook</a>';
	}
	if($ss_id == ''){
		$ss_id = '<h1 class="label text-red ">Not Active</h1>';
	}else{
		$ss_id = '<label class="label label-success ">Port:</label> '.$ss_id.
				 ' | '.'<label class="label label-success ">Pass:</label> '.$row['user_name'];
	}
	
	if($user_level_2 == 'superadmin' || $user_id_2 == 1)
	{
		$selfcredits = 'Creditos: Ilimitados';
        $credits = 'Ilimitados';
	}else
	{
		if($credits == 0){
			$selfcredits = 'Sin creditos disponibles';
		}else{
			$selfcredits = 'Tus creditos: '.$credits;
		}
	}

	//duration tables
	$premsec = calc_time($row['duration']);
	$duration = $premsec['days'] . " day(s), " . $premsec['hours'] . " hour(s) and " . $premsec['minutes'] . " minutes";
	$vipsec = calc_time($row['vip_duration']);
	$vip_duration = $vipsec['days'] . " day(s), " . $vipsec['hours'] . " hour(s) and " . $vipsec['minutes'] . " minutes";
	$privatedsec = calc_time($row['private_duration']);
	$private_duration = $privatedsec['days'] . " day(s), " . $privatedsec['hours'] . " hour(s) and " . $privatedsec['minutes'] . " minutes";

	$code = $db->encryptor('encrypt',$row['user_id']);
	$code = $db->encryptor('encrypt',$code);
	$accessIdentity = programmit_profile_access_identity($row['user_name'], $row['user_email']);
	$roleLabel = programmit_profile_role_label((int)$row['user_id'], $row['user_level']);
	$values['secret'] = $code;
	$values['shadowsocks_status'] = $ss_id;
	$values['username'] = $row['user_name'];
	$values['role_label'] = $roleLabel;
	$values['access_label'] = $accessIdentity['label'];
	$values['access_value'] = $accessIdentity['value'];
	$values['access_value_display'] = '<label class="text-success">'.programmit_profile_escape_html($accessIdentity['value']).'</label>';
	$values['name'] = $row['full_name'];
	$values['email'] = $profile_email;
	$values['credits'] = $credits;
	$values['bandwidth_free'] = $row['bandwidth_free'];
	$values['bandwidth_ph'] = $row['bandwidth_ph'];
	$values['bandwidth_premium'] = $row['bandwidth_premium'];
	$values['bandwidth_private'] = $row['bandwidth_private'];
	$values['bandwidth_vip'] = $row['bandwidth_vip'];
	$values['duration'] = $duration;
	$values['vip_duration'] = $vip_duration;
	$values['private_duration'] = $private_duration;
	$values['profile_image'] = $profile_image;
	$values['profile_number'] = $profile_number;
	$values['profile_fb'] = $profile_fb;
	$values['profile_address'] = $profile_address;
	$values['self'] = $selfcredits;
	$values['profile_number_3'] = $profile_number_3;
	$values['profile_fb_3'] = $profile_fb_3;
	$values['profile_address_3'] = $profile_address_3;
	//$("#convBtn").html(data.convBtn);
}
echo json_encode($values);
?>
