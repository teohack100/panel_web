<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
		if(isset($_POST['submitted']))
		{
			if(!isset($_POST['secret']) || empty($_POST['secret']))
			{
				$db->HandleError('Sorry! Invalid to Update!..');
			}
			else
			{
				$get_secret = $db->encryptor('decrypt',$_POST['secret']);
				$get_secret = $db->encryptor('decrypt',$get_secret);
				$uid = $db->Sanitize($get_secret);
					
				$bdo = $db->Sanitize($_POST['bdo']);
				$bitcoin = $db->Sanitize($_POST['bitcoin']);
				$bpi = $db->Sanitize($_POST['bpi']);
				$cebuana = $db->Sanitize($_POST['cebuana']);
				$gcash = $db->Sanitize($_POST['gcash']);
				$lbc = $db->Sanitize($_POST['lbc']);
				$meetup = $db->Sanitize($_POST['meetup']);
				$mlkwartapadala = $db->Sanitize($_POST['mlkwartapadala']);
				$palawanexpress = $db->Sanitize($_POST['palawanexpress']);
				$paypal = $db->Sanitize($_POST['paypal']);
				$prepaidload = $db->Sanitize($_POST['prepaidload']);
				$rcbc = $db->Sanitize($_POST['rcbc']);
				$rdperapadala = $db->Sanitize($_POST['rdperapadala']);
				$smartmoney = $db->Sanitize($_POST['smartmoney']);
				$unionbank = $db->Sanitize($_POST['unionbank']);
				$westernunion = $db->Sanitize($_POST['westernunion']);

				
				$qry = $db->sql_query("SELECT profile_id FROM users_profile WHERE profile_id='".$db->SanitizeForSQL($uid)."' LIMIT 1");				
				if($db->sql_numrows($qry) > 0){
					$update = $db->sql_query("UPDATE users_profile SET
					bdo = '".$db->SanitizeForSQL($bdo)."',
					bitcoin = '".$db->SanitizeForSQL($bitcoin)."',
					bpi = '".$db->SanitizeForSQL($bpi)."',
					cebuana = '".$db->SanitizeForSQL($cebuana)."',
					gcash = '".$db->SanitizeForSQL($gcash)."',
					lbc = '".$db->SanitizeForSQL($lbc)."',
					meetup = '".$db->SanitizeForSQL($meetup)."',
					mlkwartapadala = '".$db->SanitizeForSQL($mlkwartapadala)."',
					palawanexpress = '".$db->SanitizeForSQL($palawanexpress)."',
					paypal = '".$db->SanitizeForSQL($paypal)."',
					prepaidload = '".$db->SanitizeForSQL($prepaidload)."',
					rcbc = '".$db->SanitizeForSQL($rcbc)."',
					rdperapadala = '".$db->SanitizeForSQL($rdperapadala)."',
					smartmoney = '".$db->SanitizeForSQL($smartmoney)."',
					unionbank = '".$db->SanitizeForSQL($unionbank)."',
					westernunion = '".$db->SanitizeForSQL($westernunion)."'
					WHERE profile_id='".$db->SanitizeForSQL($uid)."'");
					if($update)
					{
						$db->HandleSuccess('Your Payment Method is Successfully Updated!');
					}else{
						$db->HandleError('Sorry! your  payment method is failed to update!..');
					}
				}else{
					$db->HandleError('Sorry! your  payment method is failed to update!..');
				}
			}
			echo $db->GetSuccessMessage();
			echo $db->GetErrorMessage();
		}else{
			if(empty($_POST['bdo']) || empty($_POST['bitcoin']) || empty($_POST['bpi'])
			|| empty($_POST['cebuana']) || empty($_POST['gcash']) || empty($_POST['lbc'])
			|| empty($_POST['meetup']) || empty($_POST['mlkwartapadala']) || empty($_POST['palawanexpress'])
			|| empty($_POST['paypal']) || empty($_POST['prepaidload']) || empty($_POST['rcbc'])
			|| empty($_POST['rdperapadala']) || empty($_POST['smartmoney']) || empty($_POST['unionbank'])
			|| empty($_POST['westernunion']) || empty($_POST['secret'])){
				echo '<script> alert("Invalid Transaction"); </script>';
				$db->RedirectToURL($db->base_url().'404');
				exit;	
			}
			
			if($user_level_2 == 'normal'){
				echo '<script> alert("Invalid Transaction"); </script>';
				$db->RedirectToURL($db->base_url().'404');
				exit;
			}
		}
?>