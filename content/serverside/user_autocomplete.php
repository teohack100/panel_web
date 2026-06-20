<?php
chkSession();
$read_cookie = explode("|", $db->decrypt_key($user));
$user_qry = $db->sql_query("SELECT user_id FROM users WHERE user_name='$read_cookie[1]' AND user_pass='$read_cookie[2]'");
$qryrow = $db->sql_fetchrow($user_qry);	
$user_id = $qryrow['user_id'];


if(isset($_GET['term'])){		
	if($user_id_2 == 1){
		$q = $mysqli->real_escape_string($_GET['term']);
		$sql = "SELECT user_id, user_name  FROM users 
		WHERE user_name LIKE '%".$q."%' AND user_id!=1 AND delflag!=0 AND is_active!=0";
		$result = $mysqli->query($sql);
		$data = array();
		
		while($row = $result->fetch_array())
		{
			$data[] = $row['user_name'];
		}
		echo json_encode($data);
	}else{
		$q = $mysqli->real_escape_string($_GET['term']);
		$sql = "SELECT user_id, user_name  FROM users 
		WHERE user_name LIKE '%".$q."%' AND user_id!=1 AND upline=".$db->SanitizeForSQL((int)$user_id_2)." AND delflag!=0 AND is_active!=0";
		$result = $mysqli->query($sql);
		$data = array();
		
		while($row = $result->fetch_array())
		{
			$data[] = $row['user_name'];
		}
		echo json_encode($data);
	}		
}
?>
