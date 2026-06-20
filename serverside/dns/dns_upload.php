<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if($user_id_2 == 1 || $user_level_2 == 'superadmin'  || $user_level_2 == 'administrator'){
}else{
	echo '<script>alert("Sorry! You dont have Permission to Access this Page!...");</script>';
	$db->RedirectToURL($db->base_url());
	exit;
}

$regex = "((https?|ftp)\:\/\/)?"; // SCHEME
$regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
$regex .= "([a-z0-9-.]*)\.([a-z]{2,4})"; // Host or IP
$regex .= "(\:[0-9]{2,5})?"; // Port
$regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
$regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
$regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor
$regex .= "(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})"; //IPv4

if(isset($_POST['submitted']))
{
	if(empty($_POST['host_name'])){
		$db->HandleError("Empty Host Name");
		return false;
	}

	if(empty($_POST['domain_name'])){
		$db->HandleError("Empty Domain");
		return false;
	}

	if(empty($_POST['ip_address'])){
		$db->HandleError("Empty IP Address");
		return false;
	}

	if(empty($_POST['record_type'])){
		$db->HandleError("Empty Record Type");
		return false;
	}

	if(preg_match('/^$regex$/', $_POST['ip_address'])){
		$db->HandleError("Invalid IP Address");
		return false;
	}

	$host_name = $db->Sanitize($_POST['host_name']);
	$domain_name = $db->Sanitize($_POST['domain_name']);
	$ip_address = $db->Sanitize($_POST['ip_address']);
	$record_type = $db->Sanitize($_POST['record_type']);
	$dnscode = rand(0,65535);
	$dns_id = $dnscode;
	
	
	for ($row = 1; $row < 101; $row++)
    {
    if ($db->Sanitize($_POST['domain_name']) == $dns_list_array[$row][0])
    {
        $domain = $dns_list_array[$row][0];
        $zone1= $dns_list_array[$row][1];
        $global1= $dns_list_array[$row][2];
        $e_mail= $dns_list_array[$row][3];
        break;
    }
    }
	
	$zone_key = $zone1;
	$global_key = $global1;
	$email = $e_mail;
	
	$chk_dns = $db->sql_query("SELECT host_name, ip_address FROM dns WHERE ip_address='".$db->SanitizeForSQL($ip_address)."'");

	$db_dns_name = $server_row['host_name'];
	$db_ip_address = $server_row['ip_address'];

	//if($host_name != $db_dns_name) {
	//	$u_result = $db->sql_query("SELECT host_name FROM dns WHERE host_name='".$db->SanitizeForSQL($host_name)."'");
	//	if($db->sql_numrows($u_result) > 0) {
	//		$db->HandleError($host_name.' is already in our database!');
	//		//return false;
	//	}
	//}

	//if($ip_address != $db_ip_address) {
	//	$u_result = $db->sql_query("SELECT ip_address FROM dns WHERE ip_address='".$db->SanitizeForSQL($ip_address)."'");
	//	if($db->sql_numrows($u_result) > 0) {
	//		$db->HandleError($ip_address.' is already in our database!');
	//		//return false;
	//	}
	//}

	$submitted = $_POST['submitted'];
	if($submitted == 'Create')

	{
	    
		$sql_upload = "INSERT INTO dns
		(dns_id, host_name, ip_address, domain_name, record_type, global_api, zone_id, email, status)
		VALUES
		('".$db->SanitizeForSQL($dns_id)."','".$db->SanitizeForSQL($host_name)."','".$db->SanitizeForSQL($ip_address)."','".$db->SanitizeForSQL($domain_name)."',
		'".$db->SanitizeForSQL($record_type)."','".$db->SanitizeForSQL($global_key)."','".$db->SanitizeForSQL($zone_key)."','".$db->SanitizeForSQL($email)."','1')
						   ";
		$upload = $db->sql_query($sql_upload);
		if($upload)
		{
		    
		    /* Cloudflare.com | APİv4 | Api Ayarları */
            $apikey		= $global_key; // Cloudflare Global API
            $email 		= $email; // Cloudflare Email Adress
            $domain 	= $domain_name;  // zone_name // Cloudflare Domain Name
            $zoneid 	= $zone_key; // zone_id // Cloudflare Domain Zone ID
            $type       = $record_type; 
            $hostname   = $host_name;
            $ipadd      = $ip_address;
            
            // A-record oluşturur DNS sistemi için.
            $ch = curl_init("https://api.cloudflare.com/client/v4/zones/".$zoneid."/dns_records");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Auth-Email: '.$email.'',
            'X-Auth-Key: '.$apikey.'',
            'Cache-Control: no-cache',
            // 'Content-Type: multipart/form-data; charset=utf-8',
            'Content-Type:application/json',
            'purge_everything: true'
            
            ));
            
            // -d curl parametresi.
            $data = array(
            
            	'type' => ''.$type.'',
            	'name' => ''.$hostname.'',
            	'content' => ''.$ipadd.'',
            	'zone_name' => ''.$domain.'',
            	'zone_id' => ''.$zoneid.'',
            	'proxied' => false,
            	'ttl' => '120'
            );
            
            $data_string = json_encode($data);
            
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);	
            //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data_string));
            
            $sonuc = curl_exec($ch);
            
            /*
            	//print_r($sonuc);
            */
            
            curl_close($ch);
		    
			$db->HandleSuccess("$hostname.$domain successfully pointed to $ipadd!");
			
			
		}else{
			$db->HandleError("Pointing $hostname.$domain to $ipadd failed!");
		}
	}
	elseif($submitted == 'DNS Update')
	{
		$dns_id = $db->Sanitize($_POST['dns_id']);
		$sql_update = "UPDATE dns SET
		host_name='".$host_name."',
		ip_address='".$ip_address."',
		domain_name='".$domain_name."',
		record_type='".$record_type."',
		global_api='".$global_key."',
		zone_id='".$zone_key."',
		email='".$email."'
		WHERE
		dns_id='".$dns_id."'
	    ";
	    
	    
		$update = $db->sql_query($sql_update);
		if($update)
		{
			$db->HandleSuccess("Successfully Updated DNS Record!...");
		}else{
			$db->HandleError("Sorry! Failed to update DNS Record!..");
		}
	}else{
		$db->HandleError("Sorry! Invalid DNS Record!..");
	}
			
	echo $db->GetSuccessMessage();
	echo $db->GetErrorMessage();	
}else{
	if(empty($_POST['host_name'])){
		echo '<script> alert("Invalid Transaction"); </script>';
		$db->RedirectToURL($db->base_url().'404');
		exit;
	}

	if(empty($_POST['domain_name'])){
		echo '<script> alert("Invalid Transaction"); </script>';
		$db->RedirectToURL($db->base_url().'404');
		exit;
	}

	if(empty($_POST['ip_address'])){
		echo '<script> alert("Invalid Transaction"); </script>';
		$db->RedirectToURL($db->base_url().'404');
		exit;
	}


	if(empty($_POST['record_type'])){
		echo '<script> alert("Invalid Transaction"); </script>';
		$db->RedirectToURL($db->base_url().'404');
		exit;
	}	
}
?>
