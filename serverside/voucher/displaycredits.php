<?php
chkSession();
if(!empty($_POST['category']))
{
	$category = $db->encryptor('decrypt', $_POST['category']);
	$info = '';
	$result = $db->sql_query("SELECT credits FROM users WHERE user_id='".$user_id_2."' LIMIT 1");
	$row = $db->sql_fetchrow($result);
	$credits = $row['credits'];
	if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
		$status = '';
	}else{
		if($credits < 1){
			$status = 'disabled="disabled"';
		}elseif($credits == 0){
			$status = 'disabled="disabled"';
		}
	}
	
	if($category == 'premium')
	{
		$info .= '<button type="button" class="btn btn-success btn-block" 
				name="generate_code" onclick="generate_voucher()" '.$status.'>
				Generate Premium Voucher
				</button>
				';
				
	}
	else
	if($category == 'vip')
	{	
		$info .= '<button type="button" class="btn btn-success btn-block" 
				name="generate_code" onclick="generate_voucher()" '.$status.'>
				Generate VIP Voucher
				</button>
				';
	}
	else
	if($category != 'premium' || $category != 'vip'){
		$db->RedirectToURL($db->base_url());
		exit;
	}
	
	if($credits > 0){
		$c = '<strong><font color="green">'.$credits.'</font></strong>';
	}else{
		$c = '<strong><font color="red">'.$credits.'</font></strong>';
	}
	echo $info;
	echo '<p>
			<h5 class="text-center">
				You have '.$c.' credit(s) left
			</h5>
		</p>';
}else{
	$db->RedirectToURL($db->base_url());
	exit;
}
?>
