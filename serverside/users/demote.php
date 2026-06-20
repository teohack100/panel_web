<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();

$valid = true;
if(isset($_POST['submitted']))
{
	$chk = $_POST['chk'];
	$chkcount = count($chk);
	for($i=0; $i<$chkcount; $i++)
	{
		$chk_id = $chk[$i];
		$chk_id = $db->encryptor('decrypt',$chk_id);
		if($chkcount > 0)
		{
		    
    		if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator'){
    			$demote_qry = $db->sql_query("UPDATE users SET 
    			is_groupname='normal', user_level='normal' WHERE user_id!=1 AND user_level!='superadmin' AND user_id='".$chk_id."'");
    		}elseif($user_level_2 == 'reseller' || $user_level_2 == 'subadmin'){
    			$demote_qry = $db->sql_query("UPDATE users SET 
    			is_groupname='normal', user_level='normal' WHERE user_id!=1 AND user_level!='superadmin' AND upline='".$user_id_2."' AND user_id='".$chk_id."'");
    		    
    		}else{
    			echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
    			exit;
    		}
    		if($demote_qry)
    		{
    			$db->HandleSuccess('Account Successfully Demoted!');
    			
    		}else{
    			$db->HandleError('Sorry! User Demote Account is Failed!');	
    		}
		}else{
		    
		    $db->HandleError('Fail!, No selected user to be demoted');
		}
	}
	echo $db->GetSuccessMessage();
	echo $db->GetErrorMessage();
}else{
	if(empty($_POST['chk']))
	{
		echo '<script> alert("Invalid Transaction"); location.assign("'.$db->base_url().'404")</script>';
		exit;
	}
}
?>