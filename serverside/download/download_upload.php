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
if($user_id_2 == 1 || $user_level_2 == 'superadmin'  || $user_level_2 == 'administrator'){
	ini_set('post_max_size','1024M');
	ini_set('upload_max_filesize','1024M');
	$submitted = $_POST['submitted'];
	if(isset($submitted))
	{
		if(!isset($_POST['download_title']) && 
		!isset($_POST['download_msg'])
		|| empty($_POST['download_title'])
		|| empty($_POST['download_msg']))
		{
			$db->HandleError('Sorry! The Transaction is failed!..');
		}
		else
		{
			if($_POST['download_category'] == 'public' || $_POST['download_category'] == 'seller')
			{
				$date = date('Y-m-d H:i:s');
				$title = strip_tags(trim($_POST['download_title']));
				$title = $db->Sanitize($title);
				$category = $db->Sanitize($_POST['download_category']);
				$message = $db->Sanitize($_POST['download_msg']);
				$network = $db->Sanitize($_POST['download_network']);
				$device = $db->Sanitize($_POST['download_device']);				
				$uploadOk = 1;
				$dirpath = "../../_uploads/";
				
				if($network == 'NOTICE')
				{
					$networks = 'Notice';
				}elseif($network == 'UPDATE')
				{
					$networks = 'Update';
				}else{
					$networks = $network;
				}
				
				if($device == 'ANDROID' || $device == 'IOS' || $device == 'WINDOWS' || $device == 'CONFIG' || $device == 'OTHERS'){
					$uploadOk = 1;
				}else{
					$db->HandleError('Sorry! The '.$device.' is invalid!..');
					$uploadOk = 0;
				}
				
				if($network == 'NOTICE' || $network == "UPDATE"){
					$uploadOk = 1;
				}else{
					$db->HandleError('Sorry! The '.$networks.' is invalid!..');
					$uploadOk = 0;
				}
				
				if(is_dir($dirpath) == false)
				{
					mkdir($dirpath, 0777, true) or die('Error: ');
				}
				
				if(!empty( $_FILES['download_file'] ))
				{
					$orginial = basename($_FILES['download_file']['name']);
					$file_name = $_FILES['download_file']['name'];
					$ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
					$target_file = time().'.'.$ext;
					$tmp_name = $_FILES['download_file']['tmp_name'];
					$file_size = $_FILES['download_file']['size'];
					$max_size = 10 * 1024 * 1024;
					if (file_exists($target_file)) {
						$db->HandleError("Sorry, file already exists.");
						$uploadOk = 0;
					}
					
					$allowedExts = array("zip", "rar", "exe", "msi", "apk", "ipa");
					if(in_array($ext, $allowedExts)){
						$uploadOk = 1;
					}else{
						$db->HandleError("Sorry, Maybe ".$orginial." exceeds max ".$max_size." KB size or incorrect file extension only APK, EXE, MSI, ZIP, RAR files are allowed.");
						$uploadOk = 0;
					}
					
					if($uploadOk == 1)
					{
						move_uploaded_file($tmp_name, $dirpath . $target_file);
						
						if($submitted == 'Download Upload')
						{
							$upload = $db->sql_query("INSERT INTO download (download_category, download_title, download_file, download_network, download_device, download_msg, download_date) 
							VALUES 
							('".$db->SanitizeForSQL($category)."','".$db->SanitizeForSQL($title)."', '".$target_file."', '".$network."', '".$device."', '".$db->SanitizeForSQL($message)."', '".$date."')");
							if($upload)
							{
								$db->HandleSuccess('Notice added successfully!');
							}else{
								$db->HandleError('Adding failed!');
							}
						}
						else
						if($submitted == 'Download Update')
						{
							$id = $db->Sanitize($_POST['download_id']);
							
							$chk_files = $db->sql_query("SELECT * FROM download WHERE id = '".$db->SanitizeForSQL($id)."'");
							while($rows = $db->sql_fetchrow($chk_files))
							{
								if($rows['download_file'] == ''){
								}else{
									unlink($dirpath . $rows['download_file']);	
								}
							}
							$update = $db->sql_query("Update download SET
							download_category='".$db->SanitizeForSQL($category)."',
							download_title='".$db->SanitizeForSQL($title)."',
							download_file='".$target_file."',
							download_network='".$network."',
							download_device='".$device."',
							download_msg='".$db->SanitizeForSQL($message)."',
							download_date='".$date."'
							WHERE
							id='".$id."'");
							if($update)
							{
								$db->HandleSuccess('Update successful!');
							}else{
								$db->HandleError('Update failed!');
							}
						}
					} else {
						$db->HandleError("Failed to upload file!");
					}
				}else{
					//if($uploadOk == 1)
					//{
						if($submitted == 'Download Upload')
						{
							$upload = $db->sql_query("INSERT INTO download (download_category,download_title, download_network, download_device, download_msg, download_date) 
							VALUES 
							('".$db->SanitizeForSQL($category)."','".$db->SanitizeForSQL($title)."', '".$network."', '".$device."', '".$db->SanitizeForSQL($message)."', '".$date."')");
							if($upload)
							{
								$db->HandleSuccess('Notice added successfully!');
							}else{
								$db->HandleError('Failed to upload file!');
							}
						}
						else
						if($submitted == 'Download Update')
						{
							$id = $db->Sanitize($_POST['download_id']);
							$update = $db->sql_query("Update download SET
							download_category='".$db->SanitizeForSQL($category)."',
							download_title='".$db->SanitizeForSQL($title)."',
							download_network='".$network."',
							download_device='".$device."',
							download_msg='".$db->SanitizeForSQL($message)."',
							download_date='".$date."'
							WHERE
							id='".$id."'");
							if($update)
							{
								$db->HandleSuccess('Update successful!');
							}else{
								$db->HandleError('Update failed!');
							}
						}
					//}
				}
			}else{
				$db->HandleError('Invalid Category!');
			}
		}
		echo $db->GetSuccessMessage();
		echo $db->GetErrorMessage();
	}else{
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}
}else{
	echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
	exit;
}
?>
