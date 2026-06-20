<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';

chkSession();
define('resize_width', 720);
define('resize_height', 480);
$redirect_profile = $db->base_url().'index.php?p=my-profile';

function programmit_profile_ensure_row($db, $uid)
{
	$uid = (int)$uid;
	if($uid <= 0){
		return false;
	}
	$qry = $db->sql_query("SELECT profile_id FROM users_profile WHERE profile_id='".$db->SanitizeForSQL($uid)."' LIMIT 1");
	$row = $db->sql_fetchrow($qry);
	if($row){
		return true;
	}
	return (bool)$db->sql_query("INSERT INTO users_profile (
		profile_id,
		profile_fb,
		profile_address,
		profile_number,
		profile_image
	) VALUES (
		'".$db->SanitizeForSQL($uid)."',
		'',
		'',
		'',
		''
	)");
}

function programmit_profile_has_uploaded_image($files)
{
	if(!isset($files['images']['error']) || !is_array($files['images']['error'])){
		return false;
	}
	foreach($files['images']['error'] as $uploadError){
		if((int)$uploadError !== UPLOAD_ERR_NO_FILE){
			return true;
		}
	}
	return false;
}

if(isset($_POST['submitted']) && (string)$_POST['submitted'] === "Edit Profile")
{
	$get_secret = $db->encryptor('decrypt',$_POST['profile_secret']);
	$get_secret = $db->encryptor('decrypt',$get_secret);
	$uid = $db->Sanitize($get_secret);
	$full_name = $db->Sanitize($_POST['full_name']);
	$profile_number = $db->Sanitize($_POST['profile_number']);
	$profile_fb = $db->Sanitize($_POST['profile_fb']);
	$profile_address = $db->Sanitize($_POST['profile_address']);
				
	$qry = $db->sql_query("SELECT user_email FROM users WHERE user_id='".$db->SanitizeForSQL($uid)."' LIMIT 1");				
	$row = $db->sql_fetchrow($qry);
	$db_email = $row ? $row['user_email'] : '';
	$email = $db_email;
				
	if(!isset($_POST['full_name']) || 
	!isset($_POST['profile_secret']) || 
	!isset($_POST['profile_fb']) || 
	!isset($_POST['profile_number']) || 
	!isset($_POST['profile_address']) ||
	trim((string)$_POST['full_name']) === '' || 
	trim((string)$_POST['profile_fb']) === '' || 
	trim((string)$_POST['profile_number']) === '' || 
	trim((string)$_POST['profile_address']) === '' || 
	trim((string)$_POST['profile_secret']) === '')
	{
		$db->HandleError('Sorry! The activation is failed!..');
	}
	else
	{		
		if(!programmit_profile_ensure_row($db, $uid)){
			$db->HandleError('Failed preparing profile storage.');
		}
		$dirpath = "../../profile/".$uid."/";
		if(is_dir($dirpath) == false)
		{
			mkdir($dirpath, 0777, true) or die('Error: ');
		}
		if(programmit_profile_has_uploaded_image($_FILES))
		{
			$images = restructure_array( $_FILES );
			$allowedExts = array("gif", "jpeg", "jpg", "png");
				
			foreach ( $images as $key => $value)
			{		
				if((int)$value["error"] === UPLOAD_ERR_NO_FILE){
					continue;
				}
				$i = $key+1;
										
				$image_name = $value['name'];
				$ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
				$name = $i*time().'.'.$ext;
				$image_size = $value["size"] / 1024;
				$image_flag = true;
				$max_size = 104857600;
				if( in_array($ext, $allowedExts) && $image_size < $max_size )
				{
					$image_flag = true;
				} 
				else 
				{
					$image_flag = false;
					$db->HandleError('Maybe '.$image_name. ' exceeds max '.$max_size.' KB size or incorrect file extension');
				} 
						
				if( $value["error"] > 0 ){
					$image_flag = false;
					$db->HandleError($image_name.' Image contains error - Error Code : '.$value["error"]);
				}
						
				if($image_flag)
				{
					$pic = $db->sql_query("SELECT * FROM users_profile WHERE profile_id = '".$db->SanitizeForSQL($uid)."'");
					while($rows = $db->sql_fetchrow($pic))
					{
						$profile_image = $rows['profile_image'];
						if($profile_image == '')
						{
							
						}else{
							$path_photo = $dirpath . $profile_image;
							unlink($path_photo);	
						}
					}

					move_uploaded_file($value["tmp_name"], $dirpath.$name);
								
					$original = $name;
					$filename = $dirpath.$name;
					$resized = $dirpath.$name;
					if (resizeImage($filename, resize_width, resize_height, $resized))  
					{  
					}else{  
						$db->HandleError("There was an error resizing your image.");  
					}
							
					$update = $db->sql_query("UPDATE users SET full_name='".$db->SanitizeForSQL($full_name)."' 
					WHERE user_id='".$db->SanitizeForSQL($uid)."'");
					
					if($update)
					{
						$profileUpdate = $db->sql_query("UPDATE users_profile SET profile_address='".$db->SanitizeForSQL($profile_address)."', profile_number='".$db->SanitizeForSQL($profile_number)."', profile_fb='".$db->SanitizeForSQL($profile_fb)."', 
						profile_image='".$original."'  WHERE profile_id='".$db->SanitizeForSQL($uid)."'");
						if($profileUpdate){
							echo '<script type="text/javascript">
                            $(document).ready(function() {
                                swal({ 
                                  title: "Success",
                                   text: "Profile updated successfully",
                                    type: "success" 
                                  }).then(function() {
                                    // Redirect the user
                                    window.location.href = "'.$redirect_profile.'";
                                    })});
                                </script>';
						}else{
							echo '<script type="text/javascript">
                            $(document).ready(function() {
                        swal({ 
                          title: "Error",
                           text: "Failed updating profile",
                            type: "error" 
                          }).then(function() {
                            // Redirect the user
                            window.location.href = "'.$redirect_profile.'";
                            })});
                        </script>';
						}
					}else{
						echo '<script type="text/javascript">
                            $(document).ready(function() {
                        swal({ 
                          title: "Error",
                           text: "Failed updating profile",
                            type: "error" 
                          }).then(function() {
                            // Redirect the user
                            window.location.href = "'.$redirect_profile.'";
                            })});
                        </script>';
					}
				}
			}
		}
		else
		{
			$update = $db->sql_query("UPDATE users SET 
			full_name='".$db->SanitizeForSQL($full_name)."' 
			WHERE user_id='".$db->SanitizeForSQL($uid)."'");
			if($update)
			{
				$profileUpdate = $db->sql_query("UPDATE users_profile SET 
				profile_address='".$db->SanitizeForSQL($profile_address)."', 
				profile_number='".$db->SanitizeForSQL($profile_number)."',
				profile_fb='".$db->SanitizeForSQL($profile_fb)."'
				WHERE
				profile_id='".$db->SanitizeForSQL($uid)."'");
				if($profileUpdate){
					echo '<script type="text/javascript">
                            $(document).ready(function() {
                        swal({ 
                          title: "Success",
                           text: "Profile updated successfully",
                            type: "success" 
                          }).then(function() {
                            // Redirect the user
                            window.location.href = "'.$redirect_profile.'";
                            })});
                        </script>';
				}else{
					echo '<script type="text/javascript">
                            $(document).ready(function() {
                        swal({ 
                          title: "Error",
                           text: "Failed updating profile",
                            type: "error" 
                          }).then(function() {
                            // Redirect the user
                            window.location.href = "'.$redirect_profile.'";
                            })});
                        </script>';
				}
			}
			else
			{
				echo '<script type="text/javascript">
                            $(document).ready(function() {
                        swal({ 
                          title: "Error",
                           text: "Failed updating profile",
                            type: "error" 
                          }).then(function() {
                            // Redirect the user
                            window.location.href = "'.$redirect_profile.'";
                            })});
                        </script>';
			}
		}
	}
	echo $db->GetSuccessMessage();
	echo $db->GetErrorMessage();
}else{
	if(empty($_POST['profile_address'])){
		$db->RedirectToURL($db->base_url());
		exit;
	}
	if(empty($_POST['profile_number'])){
		$db->RedirectToURL($db->base_url());
		exit;
	}
	if(empty($_POST['full_name'])){
		$db->RedirectToURL($db->base_url());
		exit;
	}
	if(empty($_POST['profile_secret'])){
		$db->RedirectToURL($db->base_url());
		exit;
	}
}

function restructure_array(array $images)
{
	$result = array();

	foreach ($images as $key => $value) {
		foreach ($value as $k => $val) {
			for ($i = 0; $i < count($val); $i++) {
				$result[$i][$k] = $val[$i];
			}
		}
	}

	return $result;
}

function resizeImage($filename, $max_width, $max_height, $newfilename="", $withSampling=true)   
{   
   $width = 0;  
   $height = 0;  
  
   $newwidth = 0;  
   $newheight = 0;  
  
	// If no new filename was specified then use the original filename  
	if($newfilename == "")   
	{  
		$newfilename = $filename;   
	}  
      
	// Get original sizes   
	list($width, $height) = getimagesize($filename);   
      
	if($width > $height)  
	{  
		// We're dealing with max_width  
		if($width > $max_width)  
		{  
			$newwidth = $width * ($max_width / $width);  
			$newheight = $height * ($max_width / $width);  
		}else{  
			// No need to resize  
			$newwidth = $width;  
			$newheight = $height;  
		}  
	}else{  
		// Deal with max_height  
		if($height > $max_height)  
		{  
			$newwidth = $width * ($max_height / $height);  
			$newheight = $height * ($max_height / $height);  
		}else{  
			// No need to resize  
			$newwidth = $width;  
			$newheight = $height;  
		}  
	}  
  
	// Create a new image object   
	$thumb = imagecreatetruecolor($newwidth, $newheight);   
	imagealphablending($thumb, false);
	imagesavealpha($thumb, true);
	
	// Load the original based on it's extension  
	$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));  
  
	if($ext=='jpg' || $ext=='jpeg'){  
		$source = imagecreatefromjpeg($filename);   
	}elseif($ext=='gif'){  
		$source = imagecreatefromgif($filename);
		imagealphablending($source, true);		
	}elseif($ext=='png'){   
		$source = imagecreatefrompng($filename); 
		imagealphablending($source, true);		
	}else{  
		// Fail because we only do JPG, JPEG, GIF and PNG  
		return FALSE;  
	}  
      
	// Resize the image with sampling if specified  
	if($withSampling)   
	{  
		imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);   
	}else{     
		imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);   
	}  
          
	$imageQuality = 100;       
	// Save the new image   
	if($ext=='jpg' || $ext=='jpeg'){  
		return imagejpeg($thumb, $newfilename);   
	}elseif($ext=='gif'){  
      return imagegif($thumb, $newfilename);   
	}elseif($ext=='png'){  
		$scaleQuality = round(($imageQuality/100) * 9);
		$invertScaleQuality = 9 - $scaleQuality;
		return imagepng($thumb, $newfilename);   
	}
	imagedestroy($thumb);
}		
?>	
