<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/config.php';
require_once '../../includes/auth_magic_links.php';

if(function_exists('programmit_control_is_host') && programmit_control_is_host($db)){
	if(!function_exists('programmit_control_security_allow_magic_login') || !programmit_control_security_allow_magic_login($db)){
		$db->HandleError('Magic link deshabilitado en host de control.');
		echo $db->GetErrorMessage();
		exit;
	}
}

function programmit_magic_alert($type, $message, $extra = ''){
	$type = preg_replace('/[^a-z]/', '', strtolower((string)$type));
	if($type === ''){
		$type = 'info';
	}
	return "<div class='alert alert-".$type."'><strong>".htmlentities($message)."</strong>".$extra."</div>";
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
	$db->RedirectToURL($db->base_url());
	exit;
}

$input_identifier = trim((string)($_POST['user_email'] ?? ''));
if($input_identifier === ''){
	echo programmit_magic_alert('danger', 'Ingresa un email o usuario.');
	exit;
}

programmit_ensure_magic_links_table($db);

$id_sql = $db->SanitizeForSQL(strtolower($input_identifier));
if(filter_var($input_identifier, FILTER_VALIDATE_EMAIL)){
	$qry = $db->sql_query("SELECT user_id, user_name, user_email, is_active, is_ban, status
		FROM users
		WHERE LOWER(TRIM(user_email))='".$id_sql."'
		ORDER BY user_id DESC
		LIMIT 1");
}else{
	$qry = $db->sql_query("SELECT user_id, user_name, user_email, is_active, is_ban, status
		FROM users
		WHERE LOWER(TRIM(user_name))='".$id_sql."'
		ORDER BY user_id DESC
		LIMIT 1");
}
$row = $db->sql_fetchrow($qry);

if(!$row){
	echo programmit_magic_alert('success', 'Si el usuario existe, el enlace magico fue generado.');
	exit;
}

$is_live = isset($row['status']) && strtolower(trim((string)$row['status'])) === 'live';
if((int)$row['is_active'] !== 1 || (int)$row['is_ban'] === 1 || !$is_live){
	echo programmit_magic_alert('success', 'Si el usuario existe, el enlace magico fue generado.');
	exit;
}

try{
	$token = bin2hex(random_bytes(32));
}catch(Exception $e){
	$token = hash('sha256', uniqid('mgc', true).$row['user_id'].microtime(true));
}
$token_hash = hash('sha256', $token);
$user_id = (int)$row['user_id'];

$db->sql_query("UPDATE auth_magic_links
	SET used_at=NOW()
	WHERE user_id='".$db->SanitizeForSQL($user_id)."'
		AND used_at IS NULL");

$db->sql_query("INSERT INTO auth_magic_links
	(user_id, user_email, token_hash, created_ip, created_at, expires_at, used_at)
	VALUES
	('".$db->SanitizeForSQL($user_id)."',
	 '".$db->SanitizeForSQL($row['user_email'])."',
	 '".$db->SanitizeForSQL($token_hash)."',
	 '".$db->SanitizeForSQL($db->get_client_ip())."',
	 NOW(),
	 DATE_ADD(NOW(), INTERVAL 15 MINUTE),
	 NULL)");

$magic_url = $db->base_url()."index.php?p=magic-login&token=".$token;
$safe_url = htmlentities($magic_url, ENT_QUOTES, 'UTF-8');

$subject = "Magic link login - ".$db->siteTitle;
$message = "<html><body>";
$message .= "<h3>Login request</h3>";
$message .= "<p>Use this link to login. It expires in 15 minutes.</p>";
$message .= "<p><a href='".$safe_url."'>".$safe_url."</a></p>";
$message .= "<p>If you did not request this link, ignore this email.</p>";
$message .= "</body></html>";

$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: ".$db->siteTitle." <no-reply@".$db->sitename.">\r\n";
$headers .= "Reply-To: no-reply@".$db->sitename."\r\n";

$sent = @mail($row['user_email'], $subject, $message, $headers);

if($sent){
	echo programmit_magic_alert('success', 'Enlace magico enviado. Revisa tu correo.');
	exit;
}

// Local/dev fallback when email service is not configured.
$host = isset($_SERVER['HTTP_HOST']) ? strtolower((string)$_SERVER['HTTP_HOST']) : '';
$is_local = ($host === 'localhost' || strpos($host, '127.0.0.1') !== false);
if($is_local){
	$extra = "<br><small>Email service is off. Open directly: <a href='".$safe_url."'>".$safe_url."</a></small>";
	echo programmit_magic_alert('warning', 'Enlace magico generado.', $extra);
	exit;
}

echo programmit_magic_alert('success', 'Enlace magico generado.');
?>
