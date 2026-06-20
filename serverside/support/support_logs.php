<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
if(!isset($_REQUEST['uid']) || empty($_REQUEST['uid'])){
	echo '<script> alert("Invalid Transaction"); </script>';
	$db->RedirectToURL($db->base_url().'404');
	exit;
}else{
	$get_id = $db->encryptor('decrypt', $_REQUEST['uid']);
	$support_qry = $db->sql_query("SELECT * FROM support_message WHERE ticket_id='".$get_id."'");
	while($support_rst = $db->sql_fetchrow($support_qry))
	{
		$support_msg = nl2br($support_rst['support_message']);
		$support_dates = date('Y-m-D h:i', $support_rst['support_date']);
		$support_hours = $db->time_elapsed_string(strtotime($support_rst['support_date']));	
		$support_id_user = $support_rst['support_id_user'];
		$customer_profile = $db->sql_query("SELECT user_id, user_name, full_name, user_level FROM users WHERE user_id='".$support_id_user."'");

		$pp_rows = $db->sql_fetchrow($customer_profile);
		
		$pname = $pp_rows['full_name'];
		$puname = $pp_rows['user_name'];
		$pid = $pp_rows['user_id'];

		$profile_query = $db->sql_query("SELECT profile_image FROM users_profile WHERE profile_id='".$pid."'");
		$profile_row = $db->sql_fetchrow($profile_query);
		$profile_image = $profile_row['profile_image'];
		$default = $base_url.'profile/default.png';
		$profile = $base_url.'profile/'.$pid.'/'.$profile_image;
					
		if($profile_image === ''){
			$chat_avatar = '<img class="direct-chat-img" src="'.$default.'" width="160" height="160" alt="default">';
		}else{
			$chat_avatar = '<img class="direct-chat-img" src="'.$profile.'" width="160" height="160" alt="'.$pname.'">';
		}
					
		if($pid == 1 || $pp_rows['user_level'] == 'superadmin')
		{
			$classtime = 'left';
			$placement = 'right';
			$class = 'right';
		}else{
			$classtime = 'right';
			$placement = '';
			$class = 'left';
		}
		
		$msg = '<div class="direct-chat-msg '.$placement.'">
					<div class="direct-chat-info clearfix">
						<span class="direct-chat-name pull-'.$class.'">'.$pname.'</span>
						<span class="direct-chat-timestamp pull-'.$classtime.'">'.$support_hours.'</span>
					</div>
					'.$chat_avatar.'
					<div class="direct-chat-text">
						'.$support_msg.'
					</div>
				</div>';
		echo $msg;
	}
}
?>