<?php
$query = $db->sql_query("SELECT * FROM download ORDER BY download_date DESC");
while($row = $db->sql_fetchrow($query)){
	$id = $row['id'];
	$title = nl2br($row['download_title']);
	$msg = nl2br($row['download_msg']);
	$dt = date("F d, Y h:i:s", strtotime($row['download_date']));
	$file = $db->base_url() . '_uploads/'.$row['download_file'];
	if($row['download_file'] == ""){
		$DLfiles = "";
	}else{
		$DLfiles = "<a href='".$file."'>Click Here to Download</a>";
	}
	$download[]  = '<div class="blog-post link-post">';
	
	$download[] .= '<div class="post-content">';
	
	$download[] .= '<div class="post-type"><i class="glyphicon glyphicon-link"></i></div>';
	$download[] .= '<h2 class="page-title text-success">'.$title.'</h2>';
	
	$download[] .= '<ul class="post-meta">';
	$download[] .= '<li>Date Info: '.$dt.'</li>';
	$download[] .= '</ul>';
	
	$download[] .= '<p>'.$msg.'</p>';
	$download[] .= '<p>'.$DLfiles.'</p>';
	$download[] .= '</div>';
	
	$download[] .= '</div>';
	$smarty->assign('download', $download);	
}	

$smarty->display("download.tpl");


?>