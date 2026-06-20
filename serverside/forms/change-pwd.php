<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();

$read_cookie = explode("|", $db->decrypt_key($user));
$user_name = isset($read_cookie[1]) ? $read_cookie[1] : '';
$cookie_user_id = isset($read_cookie[0]) ? $db->Sanitize($read_cookie[0]) : 0;
$effective_user_id = !empty($user_id_2) ? $user_id_2 : $cookie_user_id;

$valid = true;	
if(isset($_POST['submitted']))
{
	$old_user_pass_raw = isset($_POST['old_user_pass']) ? $db->Sanitize($_POST['old_user_pass']) : '';
	if(empty($old_user_pass_raw))
	{
		$db->HandleError('La contraseña actual está vacía');
		$valid = false;
	}
	else
	{
		$old_user_pass = $db->encrypt_key($db->encryptor('encrypt',$old_user_pass_raw));
	}
	
	$new_user_pass = $db->Sanitize($_POST['new_user_pass']);
	$new_user_pass2 = $db->Sanitize($_POST['new_user_pass2']);
	
	if($valid)
	{
		$row = $db->sql_fetchrow($db->sql_query("SELECT user_pass FROM users WHERE user_id='".$db->SanitizeForSQL($effective_user_id)."'"));
		if(!$row || $old_user_pass != $row['user_pass']) {
			$db->HandleError('La contraseña actual no coincide');
			$valid = false;
		}
	}

	if(empty($new_user_pass))
	{
		$db->HandleError('La nueva contraseña está vacía');
		$valid = false;
	}
	else if(strlen($new_user_pass)<8)
	{
		$db->HandleError('La contraseña es muy corta (mínimo 8 caracteres)');
		$valid = false;
	}
	
	if(empty($new_user_pass2))
	{
		$db->HandleError('Vuelve a escribir la nueva contraseña');
		$valid = false;
	}
	else if(strlen($new_user_pass2)<8)
	{
		$db->HandleError('La contraseña es muy corta (mínimo 8 caracteres)');
		$valid = false;
	}
	
	if((!empty($new_user_pass)) && (!empty($new_user_pass2)))
	{
		//this code will check if the 2 passwords are match or not.
		if($new_user_pass != $new_user_pass2)
		{
			$db->HandleError('Las contraseñas no coinciden');
			$valid = false;
		}
	}

	
	if($valid)
	{

		$user_pass = $db->encrypt_key($db->encryptor('encrypt',$new_user_pass));
		$auth_vpn = md5($new_user_pass);

		$update = $db->sql_query("UPDATE users SET 
		user_pass='".$db->SanitizeForSQL($user_pass)."', 
		auth_vpn='".$db->SanitizeForSQL($auth_vpn)."', is_passchange=1 
		WHERE user_id='".$db->SanitizeForSQL($effective_user_id)."'");
		
		if($update)
		{
			$info = $db->encrypt_key("$read_cookie[0]|$read_cookie[1]|$user_pass|$read_cookie[3]|$read_cookie[4]|$read_cookie[5]|$read_cookie[6]");
			setcookie("user", "$info", time()+86400, "/");
			$db->HandleSuccess('Contraseña actualizada correctamente. Cuenta: ' .$user_name);			
		}else{
			$db->HandleError('No se pudo actualizar la contraseña. Cuenta: ' .$user_name);	
		}
	}
	echo $db->GetSuccessMessage();
	echo $db->GetErrorMessage();
}else{
	if(!empty($_POST['old_user_pass'])){
		$db->RedirectToURL($db->base_url());
		exit;
	}
	if(!empty($_POST['new_user_pass'])){
		$db->RedirectToURL($db->base_url());
		exit;
	}
	if(!empty($_POST['new_user_pass2'])){
		$db->RedirectToURL($db->base_url());
		exit;
	}
}
?>	
