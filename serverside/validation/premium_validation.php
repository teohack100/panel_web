<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if(empty($_POST['voucher'])){
	$db->RedirectToURL($this->base_url());
	exit;
}
if(!empty($_POST['voucher']))
{
	$isAvailable  = true; // or false
	$data = array();
	$item_name = $_POST['voucher'];
	$name = $db->Sanitize(strip_tags(trim($item_name)));
	if(!$user_id_2 > 1 && !$user_level_2 == 'normal'){
		$query = "SELECT code_name FROM vouchers WHERE 
		UPPER(code_name)='".$db->SanitizeForSQL($name)."' AND reseller_id='".$user_id_2."' 
		AND is_used=0 AND category='premium' ORDER BY code_name LIMIT 1";		
	}else{
		$query = "SELECT code_name FROM vouchers WHERE 
		UPPER(code_name)='".$db->SanitizeForSQL($name)."' 
		AND is_used=0 AND category='premium' ORDER BY code_name LIMIT 1";	
	}
	
		$result = $db->sql_query($query);
		$num_rows = $db->sql_numrows($result);

		if(!$result || $num_rows <= 0)
		{
			$isAvailable  = false;	
		}else{

			while ($row = $db->sql_fetchrow($result))
			{
				$names = $row['code_name'];
				array_push($data, $names);
			}
		}
		echo json_encode(array('valid' => $isAvailable ));	
	
}else{
	$db->RedirectToURL($this->base_url());
	exit;
}
?>