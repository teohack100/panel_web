<?php
//skip the functions file if somebody call it directly from the browser.
if (preg_match("/config.php/i", $_SERVER['SCRIPT_NAME'])) {
    Header("Location: /"); die();
}

require 'smarty/Smarty.class.php';

$smarty = new Smarty;

include 'db_config.php';
require "mysql.class.php";
$db = new mysql_db();
$db->InitDB($DB_host,$DB_user,$DB_pass,$DB_name);
$db->SetWebsiteName('vicath-vpn.info');
$db->SetWebsiteTitle('PROGRAMMIT PANEL');

$ua = $db->getBrowser();
$browser = "" . $ua['name'] . " " . $ua['version'] . "" ; 
$ipadd = "" . $db->get_client_ip() . ""; 
$base_url = $db->base_url();
$smarty->assign('getIP', $ipadd);
$smarty->assign('getBrowser', $browser);
$smarty->assign('base_url', $db->base_url());
$smarty->assign('GetSelfScript', $db->GetSelfScript());
$smarty->assign('siteTitle', $db->siteTitle);
$smarty->assign('sitename', $db->sitename);

$date = new DateTime();
$current_timestamp = $date->getTimestamp();
$smarty->assign('current_timestamp', $current_timestamp);

$premium_encrypt = $db->encryptor('encrypt', 'premium');
$smarty->assign("premium_encrypt", $premium_encrypt);

$vip_encrypt = $db->encryptor('encrypt', 'vip');
$smarty->assign("vip_encrypt", $vip_encrypt);

$private_encrypt = $db->encryptor('encrypt', 'private');
$smarty->assign("private_encrypt", $private_encrypt);

$role_encrypt = $db->encryptor('encrypt', 'role');
$smarty->assign("role_encrypt", $role_encrypt);

$add_encrypt = $db->encryptor('encrypt', 'add');
$smarty->assign("add_encrypt", $add_encrypt);

$substract_encrypt = $db->encryptor('encrypt', 'substract');
$smarty->assign("substract_encrypt", $substract_encrypt);

$login_encrypt = $db->encryptor('encrypt', 'Login Account');
$smarty->assign("login_encrypt", $login_encrypt);

$unfreeze_encrypt = $db->encryptor('encrypt', 'Unfreeze Account');
$smarty->assign("unfreeze_encrypt", $unfreeze_encrypt);

for($i=0; $i<366; $i++)
{
	$encrypt_days .= '<option value="'.base64_encode($db->encrypt_key($db->encrypt_key($i))).'">';
	$encrypt_days .= $i;
	$encrypt_days .= '</option>';
	$smarty->assign("encrypt_days", $encrypt_days);
}

for($i=0; $i<25; $i++)
{
	$encrypt_hours .= '<option value="'.urlencode($db->encrypt_key($db->encrypt_key($i))).'">';
	$encrypt_hours .= $i;
	$encrypt_hours .= '</option>';
	$smarty->assign("encrypt_hours", $encrypt_hours);
}

$dns_list_array=array(
			1=>array("octaviavpn.com","884fb13c9e199c4fe0f5e40dd71f839b","20f989db3c168115b85e51e03b07b9c7eb85b","harulenz@gmail.com"),
			2=>array("octaviavpn.info","744641ac0aedb9a8e48262dd6883de03","20f989db3c168115b85e51e03b07b9c7eb85b","harulenz@gmail.com"),
			3=>array("octaviavpn.net","5d4b5f36e83e90745aa0f4323fd04f16","20f989db3c168115b85e51e03b07b9c7eb85b","harulenz@gmail.com")
	);

for($row = 1;$row < 101; $row++){
		if(!empty($dns_list_array[$row][0])){
		$domain_list .= '<option value="'.$dns_list_array[$row][0].'">';
		$domain_list .= $dns_list_array[$row][0];
		$domain_list .= '</option>';
		} else {
			break;
		}
	}
	
$smarty->assign('domain_list', $domain_list);
$smarty->assign('dns_list', $dns_list_array);

$year_now = date('Y');
$smarty->assign("year_now", $year_now);
?>