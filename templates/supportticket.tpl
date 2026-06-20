<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="title" content="{$siteTitle}">
<meta name="description" content="{$siteTitle} support ticket">
<meta name="keywords" content="{$siteTitle} support ticket">
<meta name="author" content="Jhoe Angeleye">
<meta name="owner" content="{$siteTitle}">
<meta name="copyright" content="Jhoe Angeleye">
<title>Support Ticket</title>
<link rel="apple-touch-icon" href="{$base_url}logo/favicon.ico">
<link rel="shortcut icon" href="{$base_url}logo/favicon.ico" type="image/x-icon">
<link rel="icon" href="{$base_url}logo/favicon.png">
<link rel="icon" sizes="57x57" href="{$base_url}logo/favicon-32x32.png">
<link rel="icon" sizes="57x57" href="{$base_url}logo/favicon-57x57.png">
<link rel="icon" sizes="72x72" href="{$base_url}logo/favicon-72x72.png">
<link rel="icon" sizes="76x76" href="{$base_url}logo/favicon-76x76.png">
<link rel="icon" sizes="114x114" href="{$base_url}logo/favicon-114x114.png">
<link rel="icon" sizes="120x120" href="{$base_url}logo/favicon-120x120.png">
<link rel="icon" sizes="144x144" href="{$base_url}logo/favicon-144x144.png">
<link rel="icon" sizes="152x152" href="{$base_url}logo/favicon-152x152.png">

<meta name="msapplication-TileColor" content="#FFFFFF">	
<meta name="msapplication-TileImage" content="{$base_url}logo/favicon-144x144.png">
<meta name="application-name" content="{$siteTitle}">
{include file='css/global_css.tpl'}
{include file='css/style_css.tpl'}
{include file='css/jqueryui_css.tpl'}
{include file='css/datatables_css.tpl'}
{include file='css/formvalidation_css.tpl'}
{include file='css/sweetalert_css.tpl'}
</head>
<body class="hold-transition skin-main sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">

{include file='apps/navigation.tpl'}

	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>
				Support Ticket
			</h1>
			<ol class="breadcrumb">
				<li><a href="{$base_url}"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active">Support Ticket</li>
			</ol>
		</section>
	
		<!-- Main content -->
		<section class="content">
			<!-- Default box -->
			<div class="box">
				<div class="box-body">
					<div class="col-md-12">
						<div class="panel">
							<div class="panel-body">
								<div id="success"></div> 
								{foreach item=i from=$support_ticket}
									{$i}
								{/foreach}
								
								<div class="row padding-20">
									<div class="chat-box">
										<div class="chats">
											<div id="support_logs"></div>
										</div>
									</div>
								</div>
								<div class="box box-warning direct-chat direct-chat-warning">
									<div class="box-body">
										<div class="direct-chat-messages">
										{foreach item=i from=$support_message}
											{$i}
										{/foreach}
										</div>
									</div>
								</div>
							</div>
						</div>

						<!-- Bootstrap modal -->
						<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="modal_form" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h3 class="modal-title">Reply Ticket # {$get_id}</h3>
									</div>
									<div class="modal-body">
										<form id="replyForm" name="replyForm">
											<div class="control-group form-group">
												<input type="hidden" id="id" name="id" value="{$get_ticket}">
												<input type="hidden" id="user" name="user" value="{$get_ticket_user}">
												<input type="hidden" id="secret" name="secret" value="{$encrypt_user_id}">
												<input type="hidden" id="submitted" name="submitted" value="Reply Ticket">
												<div class="controls">
													<label class="control-label" for="message">Message:</label>
													<textarea class="form-control" id="message" name="message" rows="6" 
													wrap="hard" placeholder="message" required"></textarea>
												</div>
											</div>
											<div class="control-group form-group">
												<div class="modal-footer">
													<button type="submit" id="btnSave" name="btnSave" class="btn btn-primary">Submit</button>
													<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
													<span align="left" id="loading"></span>
												</div>
											</div>
										</form>
									</div>
								</div><!-- /.modal-content -->
							</div><!-- /.modal-dialog -->
						</div><!-- /.modal -->
						<!-- End Bootstrap modal -->
					</div>
				</div>
			</div>
		</section>
	</div>
{include file='apps/footer.tpl'}
</div>
{include file='js/global_js.tpl'}
{include file='js/jqueryui_js.tpl'}
{include file='js/formvalidation_js.tpl'}
{include file='js/datatables_js.tpl'}
{include file='js/sweetalert_js.tpl'}
{include file='js/ckeditor_js.tpl'}
<script>
var loading = $('#loading');
var get_ticket = $('#id').value;
var get_ticket_user = $('#user').value;
function CKupdate(){
    for ( instance in CKEDITOR.instances ){
        CKEDITOR.instances[instance].updateElement();
        CKEDITOR.instances[instance].setData('');
    }
}

function create(){
    $('#replyForm')[0].reset();
    $('.form-group').removeClass('has-error'); 
    $('.help-block').empty();
	$('#replyForm').formValidation('resetForm', true);
    $('#modal_form').modal('show');
}

$('textarea').ckeditor({
	toolbar: 'Full',
	enterMode : CKEDITOR.ENTER_BR,
	shiftEnterMode: CKEDITOR.ENTER_P
});

$.fn.modal.Constructor.prototype.enforceFocus = function () {
	modal_this = this
	$(document).on('focusin.modal', function (e) {
		if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length
		// add whatever conditions you need here:
		&&
		!$(e.target.parentNode).hasClass('cke_dialog_ui_input_select') && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_text')) {
			modal_this.$element.focus()
		}
	})
};

var ticket_id = '{$get_ticket}';
var url = '{$base_url}serverside/support/support_logs.php?uid='+ticket_id;
{literal}
$(document).ready(function(e){
	$.ajaxSetup({cache:false});
	setInterval(function() {$('#support_logs').load(url);}, 2000);
});
{/literal}
(function(document, window, $) {
	$('#replyForm').formValidation({
		framework: 'bootstrap',
		icon: null,
		fields:
		{
			message:
			{
				validators:
				{
					notEmpty:
					{
						message: 'The Message is and can\'t be empty'
					},
					callback:
					{
						message: 'The Message is must be less than 5000 characters long',
						callback: function(value, validator, $field)
						{
							if (value === '')
							{
								return true;
							}
							var div  = $('<div/>').html(value).get(0),
							text = div.textContent || div.innerText;
							return text.length <= 5000;
						}
					}
				}
			}
		}
	})
	.on('success.form.fv', function(e, data) {
		e.preventDefault();
		var $replyfrm = $(e.target);
		$.ajax({
			url: '{$base_url}serverside/support/replyticket.php',
			type: "POST",
			data: $replyfrm.serialize(),
			cache: false,
			beforeSend:function()
			{
				loading.show();
			},
			complete: function(xhr)
			{
				loading.hide();
			},
			success: function(response)
			{
				$('#success').html(response);
				$replyfrm.formValidation('resetForm', true);
				CKupdate();
				loading.hide();
				$('#modal_form').modal('hide');
			},
			error: function(jqXHR, textStatus, errorThrown) {
				loading.hide();
				$('#success').html(response);
				$replyfrm.trigger('reset');
				$('#modal_form').modal('hide');
			}
		});
	})	
	.find('[name="message"]')
	.ckeditor()
	.editor
	.on('change', function()
	{
		$('#replyForm').formValidation('revalidateField', 'message');
		$('message').trigger("reset");
	});
})(document, window, jQuery);

function ticketClosed(){
	$('#frm').formValidation({
		framework: 'bootstrap',
		icon: null,
		fields: 
		{
			closed_id: 
			{
				valid: true,
				message: 'The close id is not valid',
				validators: 
				{
					notEmpty:
					{
						message: 'The close id is required'
					}
				}
			},
			closed_user: 
			{
				valid: true,
				message: 'The close user is not valid',
				validators: 
				{
					notEmpty:
					{
						message: 'The close user is required'
					}
				}
			}
		}
	})
	.on('success.form.fv', function(e, data) {
		e.preventDefault();	
		swal({
			title: "Are you sure?",
			text: "You will not be able to Reply!",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: '#DD6B55',
			confirmButtonText: 'Yes, close it!',
			cancelButtonText: "No, cancel plx!",
			closeOnConfirm: false,
			closeOnCancel: false
		},
		function(isConfirm)
		{
			if(isConfirm)
			{
				$.ajax({
					url: "{$base_url}serverside/support/closedticket.php",
					type: "POST",
					data: $('#frm').serialize(),
					cache: false,
					success: function(response)
					{
						swal("Closed!",
						"The Support Ticket has been closed!",
						"success");
						$('#success').html(response);
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						$('#success').html(response);
					}
				});
			} else {
				swal("Cancelled", "The Support Ticket is still open :)",
				"error");
			}
		});
		
	});
}
</script>
{include file='apps/liveclock.tpl'}
</body>
</html>