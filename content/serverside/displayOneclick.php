<?php
chkSession();
if(!empty($_POST['category_selected']))
{
	$info = '';
	$category_selected = $_POST['category_selected'];
	if($category_selected == 'credits_selected')
	{
		$info .=
		'<div class="form-group input-group">
			<label class="input-group-addon control-label" for="add_credits"><i class="glyphicon glyphicon-barcode"></i></label>
			<input class="form-control" type="text" id="add_credits" name="add_credits">
		</div>
		<div class="form-group">
			<select class="form-control" id="category" name="category">
				<option value="'.$add_encrypt.'" selected="selected">Add Credits</option>
				<option value="'.$substract_encrypt.'">Substract Credits</option>
			</select>
		</div>
		';
	}
	
	if($category_selected == 'duration_selected')
	{
		$info .=
		'<div class="form-group has-feedback">
			<label class="control-label" for="dayss"><i class="glyphicon glyphicon-time"></i> Day(s):</label>
			<select class="form-control" id="dayss" name="dayss">
				'.$encrypt_days.'
			</select>
		</div>
		<div class="form-group has-feedback">
			<label class="control-label" for="hourss"><i class="glyphicon glyphicon-time"></i> Hour(s):</label>
			<select class="form-control" id="hourss" name="hourss">
				'.$encrypt_hours.'
			</select>
		</div>
		<div class="form-group input-group">
			<label class="input-group-addon control-label" for="category_ext"><i class="glyphicon glyphicon-list"></i>: </label>
			<select class="form-control" id="category_ext" name="category_ext">
				<option value="'.$add_encrypt.'">Add Duration</option>
				<option value="'.$substract_encrypt.'">Substract Duration</option>
			</select>
		</div>
		<div class="form-group input-group">
			<label class="input-group-addon control-label" for="category"><i class="glyphicon glyphicon-list"></i>: </label>
			<select class="form-control credits" id="category" name="category">
				<option value="'.$premium_encrypt.'">Premium</option>
				<option value="'.$vip_encrypt.'">VIP</option>
				<option value="'.$private_encrypt.'">Private</option>
			</select>
		</div>
		';
	}
	
	if($category_selected == 'delete_selected')
	{
		$qry = $db->sql_query("SELECT user_name FROM users WHERE duration < 0 AND vip_duration < 0 AND private_duration < 0");
		$numrows = $db->sql_numrows($qry);
		if($numrows > 0){
			$info .= '<p class="text-center"><font color="red">'.$numrows.'</font> In-Active Users</p>';
		}else{
			$info .= '<p class="text-center"><font color="green">'.$numrows.'</font> In-Active Users</p>';
		}
	}
	echo $info;
}else{
	$db->RedirectToURL($db->base_url());
	exit;
}
?>
