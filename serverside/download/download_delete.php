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
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator'){
	if(isset($_POST['submitted']) == 'Delete Delete')
	{
		$chk = $_POST['chk'];
		$chkcount = count($chk);
			
		if(!isset($chk))
		{
			echo "<script> alert('You need to check the checkbox! At least one checkbox Must be Selected !!!'); </script>";
			$db->RedirectToURL($db->base_url().'404');
			exit;
		}
		else
		{
			for($i=0; $i<$chkcount; $i++)
			{
				$chk_id = $chk[$i];
				$chk_id = $db->encryptor('decrypt',$chk_id);
				$chk_files = $db->sql_query("SELECT * FROM download WHERE id = '".$db->SanitizeForSQL($chk_id)."'");
				while($rows = $db->sql_fetchrow($chk_files))
				{
					if($rows['download_file'] == ''){
					}else{
						$dirpath = "../../_uploads/";
						unlink($dirpath . $rows['download_file']);	
					}
					$chk_qry = $db->sql_query("DELETE FROM download WHERE id='".$rows['id']."'");
					
					if($chk_qry)
					{
						$db->HandleSuccess('Successfully! Deleted !...');
					}else{
						$db->HandleError('Sorry! Deleting Record is Failed!');
					}
				}
			}
		}
			echo $db->GetSuccessMessage();
			echo $db->GetErrorMessage();
	}else{
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}
}else{
	echo '<script>alert("Sorry! You dont have Permission to Access this Page!...");</script>';
	$db->RedirectToURL($db->base_url());
	exit;
}

?>
