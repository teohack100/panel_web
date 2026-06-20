<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator'){
}else{
	echo '<script>alert("Sorry! You dont have Permission to Access this Page!...");</script>';
	$db->RedirectToURL($db->base_url());
	exit;
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
	$db->HandleError('Invalid transaction');
	echo $db->GetErrorMessage();
	exit;
}

$chk = isset($_POST['chk']) && is_array($_POST['chk']) ? $_POST['chk'] : array();
if(empty($chk)){
	$db->HandleError('No server selected for deletion.');
	echo $db->GetErrorMessage();
	exit;
}

$deletedCount = 0;
foreach($chk as $encryptedId){
	$encryptedId = trim((string)$encryptedId);
	if($encryptedId === ''){
		continue;
	}

	$decryptedId = $db->encryptor('decrypt', $encryptedId);
	$serverId = (int)$decryptedId;
	if($serverId <= 0){
		continue;
	}

	$serverIdSql = $db->SanitizeForSQL((string)$serverId);
	$deleteQry = $db->sql_query("DELETE FROM server_list WHERE server_id='".$serverIdSql."' LIMIT 1");
	if($deleteQry){
		$deletedCount++;
	}
}

if($deletedCount > 0){
	$db->HandleSuccess('Successfully deleted '.$deletedCount.' server(s).');
}else{
	$db->HandleError('No servers were deleted.');
}

echo $db->GetSuccessMessage();
echo $db->GetErrorMessage();
?>
