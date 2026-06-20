<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="title" content="{$siteTitle}">
<meta name="description" content="{$siteTitle} support page">
<meta name="keywords" content="{$siteTitle} support page">
<meta name="author" content="Jhoe Angeleye">
<meta name="owner" content="{$siteTitle}">
<meta name="copyright" content="Jhoe Angeleye">
<title>Support Page</title>
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
				Support
			</h1>
			<ol class="breadcrumb">
				<li><a href="{$base_url}"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active">Support</li>
			</ol>
		</section>
	
		<!-- Main content -->
		<section class="content">
			<!-- Default box -->
			<div class="box">
				<div class="box-body">
					<div class="col-md-12">
						{if $user_id_2 == 1 || $user_level_2 == 'superadmin'}
						{else}
						<div class="row">
							<div class="col-sm-6 col-md-12">
								<a href="javascript:void(0)" onclick="support_add()" class="btn btn-large btn-info">
									<i class="glyphicon glyphicon-plus"></i> &nbsp; Create New Ticket
								</a>
							</div>
						</div>
						{/if}
						<div class="panel">
							<div class="panel-body">
								<div id="success"></div>
								<table id="support-tbl" class="table table-striped table-bordered" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th class="text-center">Name</th>
											<th class="text-center">Subject</th>
											<th class="text-center">Status</th>
											<th class="text-center">Date</th>
											<th class="text-center">Updated</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
											<th class="text-center">Name</th>
											<th class="text-center">Subject</th>
											<th class="text-center">Status</th>
											<th class="text-center">Date</th>
											<th class="text-center">Updated</th>
										</tr>
									</tfoot>
									<tbody class="text-center">
									</tbody>
								</table>
							</div>
						</div>
						
						<!-- Bootstrap modal -->
						<div class="modal fade" id="modal_form" role="dialog">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h3 class="modal-title">Support Ticket</h3>
									</div>
									<div class="modal-body form">
										<form id="supportticket" name="supportticket">
											<div class="control-group form-group">
												<div class="controls">
													<label class="control-label" for="subject">Subject:</label>
													<input type="text" class="form-control" id="subject" name="subject" placeholder="subject" required> 
												</div>
											</div>
											<div class="control-group form-group">
												<div class="controls">
													<label class="control-label" for="message">Message:</label>
													<textarea class="form-control" id="message" name="message" rows="6" wrap="hard" placeholder="message"
													required></textarea>
												</div>
											</div>
											<div class="control-group form-group">
												<div class="controls text-right">
													<input type="hidden" id="submitted" name="submitted" value="New Ticket"> 
													<button type="submit" id="submitSupport" name="submitSupport" class="btn btn-success">
														<i class="glyphicon glyphicon-sent"></i> Submit Ticket
													</button>
													<button type="button" class="btn btn-info" id="resetButton">
														Reset
													</button>
													<span id="loading" class='text-left'></span>
												</div>
											</div>
										</form>
									</div><!-- /.modal-body -->
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
var loading			= $('#loading');
var submitbutton    = $("#submitSupport");
var myform          = $("#supportticket");
var table;
function reload_table()
{
	table.fnReloadAjax(null,false);
}

function support_add(){
	myform.trigger('reset'); // reset form on modals
	$('.form-group').removeClass('has-error'); // clear error class
	$('.help-block').empty(); // clear error string
	myform.formValidation('resetForm', true);
	myform.resetForm();
	loading.hide();
	$('#modal_form').modal('show');
}

function CKupdate(){
	for ( instance in CKEDITOR.instances ){
		CKEDITOR.instances[instance].updateElement();
	}
}
	
var editor = $('textarea').ckeditor({
	toolbar: 'Full',
	enterMode : CKEDITOR.ENTER_BR,
	shiftEnterMode: CKEDITOR.ENTER_P
});

editor.on( 'change', function( evt ) {
	console.log( 'Total bytes: ' + evt.editor.getData().length );
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
$(document).ready(function() {
	table = $('#support-tbl').dataTable({
		responsive: true,
        "bProcessing": true,
        "bServerSide": true,
        "bStateSave": true,
        "ajax": {
            "url": "{$base_url}serverside/support/support-serverside.php",
            "type": "POST"
        },
		order: [[ 0, 'desc' ], [ 0, 'asc' ]],
		"iDisplayLength": 5,
		"aLengthMenu": [
				[5, 10, 25, 50, 100, 99999999999999],
				[5, 10, 25, 50, 100, "ALL"]
		],
		language: {
			"sSearchPlaceholder": "Search..",
			"lengthMenu": "_MENU_",
			"search": "_INPUT_",
			"sInfo":'Showing _START_ to _END_ of _TOTAL_ entries',
			"sZeroRecords": "No matching records found"
		}
	});
	
	$('#supportticket').formValidation({
		framework: 'bootstrap',
		excluded: ':disabled',
		icon: null,
		fields:
		{
			subject:
			{
				validators:
				{
					notEmpty:
					{
						message: 'The Subject is and can\'t be empty'
					}
				}
			},
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
	.find('[name="message"]')
	.ckeditor()
	.editor
	.on('change', function() {
		$('#supportticket').formValidation('revalidateField', 'message');
		$('message').trigger("reset");
	});

	var submitbutton    = $("#submitSupport");
	var myform          = $("#supportticket");
	myform.ajaxForm({
		url: '{$base_url}serverside/support/newticket.php',
		type: "POST",
		data: myform.serialize(),
		cache: false,
		beforeSend: function() {
			loading.show();
			submitbutton.attr('disabled', '');
		},
		success: function(data) {	
			$('#success').html(data);
			myform.formValidation('resetForm', true);
			loading.hide();
			CKupdate();	
			myform.resetForm();
			reload_table();
		},
		error: function(jqXHR, textStatus, errorThrown) {
			loading.hide();		
			$('#success').html(data);
			myform.trigger('reset');
			myform.resetForm();
			reload_table();
		},
		complete: function(response) {
			myform.resetForm();
			loading.hide();
			myform.formValidation('resetForm', true);
			submitbutton.removeAttr('disabled');
		}
	});
});
</script>
{include file='apps/liveclock.tpl'}
</body>
</html>