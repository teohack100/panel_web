<?php
$qry = $db->sql_query("SELECT
u.user_name, u.user_level, u.full_name, u.credits, u.login_status,
up.profile_address, up.profile_fb, up.profile_number
FROM 
users as u 
INNER JOIN users_profile as up 
ON u.user_id = up.profile_id
WHERE 
u.user_level='reseller' AND u.credits!=0
OR
u.user_level='subadmin' AND u.credits!=0
OR
u.user_level='administrator' AND u.credits!=0
ORDER BY u.full_name ASC");
								
while($data = $db->sql_fetchrow($qry))
{
	$user_name = $data['user_name'];
	$full_name = $data['full_name'];
	$credits_qty = $data['credits'];
	$profile_fb = $data['profile_fb'];
	$user_level_data = $data['user_level'];

	if($data['profile_address'] == '' || $data['profile_number'] == ''){
		$profile_address = '<span class="label label-danger">No Address Details Uploaded</span>';
		$profile_number = '<span class="label label-danger">No Contact Details Uploaded</span>';
	}else{
		$profile_address = $data['profile_address'];
		$profile_number = $data['profile_number'];
	}
	
	if($data['login_status'] == 'online'){
		$login_status = '<span class="label label-success">Online</span>';
	}else{
		$login_status = '<span class="label label-danger">Offline</span>';
	}

	$is_reseller[]  = "<tr data-child-address='$profile_address' data-child-number='$profile_number'>";
	$is_reseller[] .= '<td class="details-control text-center"></td>';
	$is_reseller[] .= '<td class="text-center">'.$full_name.'</td>';
	$is_reseller[] .= '<td class="text-center">'.$user_name.'</td>';

	if($user_level_data == 'subadmin' || $user_level_data == 'administrator')
	{
		$is_reseller[] .= '<td class="text-center"><font color="darkblue">['.$legal_name.' Protected]</font></td>';
		
	}elseif($user_level_data == 'reseller' && $credits_qty > 5){
	    $is_reseller[] .= '<td class="text-center"><font color="green">['.$legal_name.' Protected]</font></td>';
	    
	}else{
		$is_reseller[] .= '<td class="text-center"><font color="red">'.$credits_qty.'</font></td>';
	}
	$is_reseller[] .= '<td class="text-center">'.$login_status.'</td>';
	if($profile_fb == ''){
		$is_reseller[] .= '<td class="text-center"><label class="label label-danger">No Uploaded</label></td>';
	}else{
		$is_reseller[] .= '<td class="text-center"><a class="text text-link" href="'.$profile_fb.'">Visit Facebook Profile</a></td>';
	}
	$is_reseller[] .= '</tr>';
}
$smarty->assign("reseller", $is_reseller);
$smarty->display("resellers.tpl");
?>