<?php
chkSession();
if(!isset($_GET['id']) && !isset($_GET['user']) || empty($_GET['id']) || empty($_GET['user'])){
	$error = 2;
	$err = '<div class="container">
			<div class="row">
				<div class="col-md-12 text-center">
					<h4>Sorry! you dont have permission to access this page...  </h4>
					<button type="button" class="btn btn-warning" onClick="javascript:history.go(-1)"> Return to Previous Page </button>
				</div>
			</div>
			</div>';
}else{
	$get_id = urldecode($_GET['id']);
	$get_id = $db->encryptor('decrypt',$get_id );
	$get_user = trim($_GET['user']);
	$get_user = $db->Sanitize($get_user);
	$ticket_query = $db->sql_query("SELECT * FROM support_ticket WHERE id='".$get_id."' AND ticket_name='".$db->SanitizeForSQL($get_user)."' LIMIT 1");
	if($db->sql_numrows($ticket_query) == 1)
	{
		$error = 0;
		$rst = $db->sql_fetchrow($ticket_query);
		$get_ticket = $db->encryptor('encrypt', $get_id);
		$get_ticket_user = $db->encryptor('encrypt', $get_user);
		if($rst['ticket_status'] == 'open')
		{
			$ticket_status = '<label class="label label-success">Open</label>';
			$reply_buttons = '<button class="btn btn-info" onclick="create()" id="ticketCreate" name="ticketCreate">
								<i class="glyphicon glyphicon-plus"></i> Reply Ticket
							</button>';
			$close_buttons = '<button onclick="ticketClosed()" id="ticketClose" name="ticketClose" class="btn btn-danger">
								<i class="glyphicon glyphicon-ban-circle"></i> Closed Ticket
							</button>';	
		}
		elseif($rst['ticket_status'] == 'customer-reply')
		{
			$ticket_status = '<label class="label label-primary">Customer Reply</label>';
			$reply_buttons = '<button class="btn btn-info" onclick="create()" id="ticketCreate" name="ticketCreate">
								<i class="glyphicon glyphicon-plus"></i> Reply Ticket
							</button>';
			$close_buttons = '<button onclick="ticketClosed()" id="ticketClose" name="ticketClose" class="btn btn-danger">
								<i class="glyphicon glyphicon-ban-circle"></i> Closed Ticket
							</button>';	
		}
		elseif($rst['ticket_status'] == 'answered')
		{
			$ticket_status = '<label class="label label-info">Answered</label>';
			$reply_buttons = '<button class="btn btn-info" onclick="create()" id="ticketCreate" name="ticketCreate">
								<i class="glyphicon glyphicon-plus"></i> Reply Ticket
							</button>';
			$close_buttons = '<button onclick="ticketClosed()" id="ticketClose" name="ticketClose" class="btn btn-danger">
								<i class="glyphicon glyphicon-ban-circle"></i> Closed Ticket
							</button>';	
		}elseif($rst['ticket_status'] == 'closed'){
			$ticket_status = '<label class="label label-default">Closed</label>';
			$reply_buttons = '<button class="btn btn-info" id="ticketCreate" name="ticketCreate" disabled>
								<i class="glyphicon glyphicon-plus"></i> Reply Ticket
							</button>';
			$close_buttons = '<button class="btn btn-danger" id="ticketClose" name="ticketClose" disabled>
								<i class="glyphicon glyphicon-ban-circle"></i> Closed Ticket
							</button>';
		}
		$support_ticket = '<div class="row alert alert-info">' .
		'<div class="col-md-12 text-center">Ticket #'.$get_id.' Status: '.$ticket_status.'</div>' .
		'<div class="col-md-6 text-left capitalize">'.$get_user.' ('.$rst['ticket_groupname'].')</div><div class="col-md-6 text-right">'.$rst['ticket_subject'].'</div>' .
		'<div class="col-md-12">Message: '.nl2br($rst['ticket_message']).'</div>' .
		'<div class="col-md-6 text-left">Date Submitted: '.date("m-d-Y H:i A", strtotime($rst['ticket_date'])).'</div><div class="col-md-6 text-right">Last Reply: '.date("m-d-Y H:i A", strtotime($rst['ticket_update'])).'</div>' .
		'</div>';
				
		$support_message =
		'<div class="row">
			<div class="col-md-12">				
				<div class="pull-left">
					<form id="frm" name="frm">	
						<input type="hidden" id="submitted" name="submitted" value="Closed Submitted">
						<input type="hidden" id="closed_id" name="closed_id" value="'.$get_ticket.'">
						<input type="hidden" id="closed_user" name="closed_user" value="'.$get_ticket_user.'">
						'.$close_buttons.'					
					</form>
				</div>
				<div class="pull-right">
					'.$reply_buttons.'
					<button type="button" class="btn btn-warning" onclick="window.location.href=\'/support\'">
						`<i class="glyphicon glyphicon-home"></i> Home 
					</button>
				</div>
			</div>
		</div>';
	}else{
		$error = 1;
		$err = '<div class="container">
					<div class="row">
						<div class="col-md-12 text-center">
							<h4>Sorry! you dont have permission to access this page...  </h4>
							<button type="button" class="btn btn-warning" onClick="javascript:history.go(-1)"> Return to Previous Page </button>
						</div>
					</div>
				</div>';
	}
}
$smarty->assign('support_ticket',$support_ticket);
$smarty->assign('support_message',$support_message);
$smarty->assign('get_user',$get_user);
$smarty->assign('get_ticket',$get_ticket);
$smarty->assign('get_ticket_user',$get_ticket_user);
$smarty->assign('get_id',$get_id);
$smarty->assign('err',$err);
$smarty->assign('error',$error);		
$smarty->display("supportticket.tpl");
?>