<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="title" content="{$siteTitle} - My Account">
<meta name="description" content="Fast, Stable and Secure VPN Service">
<meta name="keywords" content="{$siteTitle}">
<meta name="author" content="Lenz Scott Kennedy">
<meta name="owner" content="Firenet Philippines">
<meta name="copyright" content="{$siteTitle}">
<title>{$siteTitle} - Notice Update</title>
<link rel="apple-touch-icon" href="{$base_url}logo/favicon.ico">
<link rel="shortcut icon" href="{$base_url}logo/favicon.ico" type="image/x-icon">
<link rel="icon" href="{$base_url}logo/favicon.png">
<link rel="icon" sizes="32x32" href="{$base_url}logo/favicon-32x32.png">
<link rel="icon" sizes="57x57" href="{$base_url}logo/favicon-57x57.png">
<link rel="icon" sizes="72x72" href="{$base_url}logo/favicon-72x72.png">
<link rel="icon" sizes="76x76" href="{$base_url}logo/favicon-76x76.png">
<link rel="icon" sizes="114x114" href="{$base_url}logo/favicon-114x114.png">
<link rel="icon" sizes="120x120" href="{$base_url}logo/favicon-120x120.png">
<link rel="icon" sizes="144x144" href="{$base_url}logo/favicon-144x144.png">
<link rel="icon" sizes="152x152" href="{$base_url}logo/favicon-152x152.png">

<meta name="msapplication-TileColor" content="#000000">	
<meta name="msapplication-TileImage" content="{$base_url}logo/favicon-144x144.png">
<meta name="application-name" content="{$siteTitle}">
{include file='css/global_css.tpl'}
{include file='css/style_css.tpl'}
{include file='css/jqueryui_css.tpl'}
{include file='css/datatables_css.tpl'}
{include file='css/formvalidation_css.tpl'}
</head>
<body class="skin-purple-light layout-boxed sidebar-mini">
<!-- Site wrapper -->
<div class="lenz-wrapper-boxed">

{include file='apps/navigation.tpl'}

	<!-- Content Wrapper. Contains page content -->
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>
				Download & Tutorials
			</h1>
			<ol class="breadcrumb">
				<li><a href="{$base_url}"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active">Notice Update</li>
			</ol>
		</section>
	
		<!-- Main content -->
		<section class="content">
			<!-- Default box -->
			<div class="box">
				<div class="box-body">
					<div class="col-md-12">						
						<fieldset class="padding-20">
							<div id="success" class="success"></div>
							<legend class="text-center bg-purple">
								<h4 class="text-white">
									<div class="panel-heading">
										Download Content
									</div>
								</h4>
							</legend>
							<div class="btn-group btn-group-justified" role="group">
								<div class="btn-group" role="group">
									<button type="button" class="btn btn-info" onclick="download_add()" id="download_add" >
										<i class="glyphicon glyphicon-plus"></i> &nbsp; Add Download Records
									</button>
								</div>
								<div class="btn-group" role="group">
									<button class="btn btn-danger" onClick="delete_download();" id="delete_download">
										<i class="glyphicon glyphicon-remove-circle"></i> &nbsp; Multiple Delete
									</button>
								</div>
							</div>

							<form method="post" id="dl_frm" name="dl_frm">
								<input type="hidden" id="submitted" name="submitted" value="Download Delete">
								<table id="download_serverside" class="table table-striped table-bordered" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th class="text-center"><input type="checkbox" class="select-alls" /></th>
											<th class="text-center">Category</th>
											<th class="text-center">Title</th>
											<th class="text-center">Network</th>
											<th class="text-center">Device</th>
											<th class="text-center">Issue</th>
											<th class="text-center">Controls</th>
										</tr>
									</thead>
									<tbody class="text-center">
									</tbody>
								</table>
							</form>
						</fieldset>
					</div>

					<!-- Bootstrap modal -->
					<div class="modal fade" id="download_view" tabindex="-1" role="dialog" aria-labelledby="download_view" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h3 class="modal-title">Download Message Form</h3>
								</div>
								<div class="modal-body">
									<div class="row">
										<div class="col-md-12">
											<div id="output_msg"></div>
											<hr />
											<div id="output_file" class="text-center"></div>
										</div>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-danger" data-dismiss="modal">
											Exit
										</button>
									</div>
								</div>
							</div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
					
					<div class="modal fade" id="download_form" tabindex="-1" role="dialog" aria-labelledby="download_form" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h3 class="modal-title dl-title">Download Message Form</h3>
								</div>
								<div class="modal-body">
									<form id="dlform" name="dlform" accept-charset="UTF-8" enctype="multipart/form-data" novalidate>
										<input type="hidden" id="download_id" name="download_id">
										<input type="hidden" class="dl_frm" id="submitted" name="submitted" value="Download Upload">
										<div class="row">
											<div class="col-md-12">
												<div class="control-group form-group">
													<div class="controls">
														<label class="control-label" for="download_title">
															<i class="glyphicon glyphicon-home"></i> &nbsp; Title
														</label>
														<input type="text" id="download_title" name="download_title" class="form-control">
													</div>
												</div>
											</div>
											<div class="col-md-6">
												<div class="control-group form-group">
													<div class="controls">
														<label class="control-label" for="download_category">
															<i class="glyphicon glyphicon-list"></i> &nbsp; Category
														</label>
														<select id="download_category" name="download_category" class="form-control">
															<option value="public">Public</option>
															<option value="seller">Seller</option>
														</select>
													</div>
												</div>
											</div>
											<div class="col-md-3">
												<div class="control-group form-group">
													<div class="controls">
														<label class="control-label" for="download_network">
															<i class="glyphicon glyphicon-signal"></i> &nbsp; Network
														</label>
														<select id="download_network" name="download_network" class="form-control">
															<option value="ALLINONE">ALL IN ONE</option>
															<option value="GTM">Globe / TM</option>
															<option value="SUN">Sun</option>
															<option value="SMART">Smart</option>
															<option value="TNT">TNT</option>
														</select>
													</div>
												</div>
											</div>
											<div class="col-md-3">
												<div class="control-group form-group">
													<div class="controls">
														<label class="control-label" for="download_device">
															<i class="glyphicon glyphicon-phone"></i> &nbsp; Device
														</label>
														<select id="download_device" name="download_device" class="form-control">
															<option value="ANDROID">Android</option>
															<option value="IOS">IOS</option>
															<option value="WINDOWS">PC WINDOWS</option>
															<option value="CONFIG">CONFIG</option>
														</select>
													</div>
												</div>
											</div>
											<div class="col-md-12">
												<div class="control-group form-group">
													<div class="controls">
														<input type="file" class="custom-file-upload" id="download_file" name="download_file" data-btn-text="Select a File" >
														<small class="text-muted block"> Max File Upload: 20MB (APK, EXE, MSI, ZIP, RAR)</small>
													</div>
												</div>
											</div>
											<div class="col-md-12">
												<div class="control-group form-group">
													<div class="controls">
														<label class="control-label" for="download_msg">
															<i class="glyphicon glyphicon-envelope"></i> &nbsp; Download Message
														</label>
														<textarea id="download_msg" name="download_msg" class="form-control" rows="6" wrap="hard" placeholder="Download Message"></textarea>
													</div>
												</div>
											</div>
										</div>
										<div class="modal-footer">
											
											<div class="percent text-center">0% Uploaded...</div>
											
											<button type="submit" class="btn btn-info" id="submitdownload" name="submitdownload">
												<i class="glyphicon glyphicon-check"></i> Confirmation 
											</button>
											<button type="button" class="btn btn-danger" data-dismiss="modal">
												Exit
											</button>
											<span id="loading" class='loading text-left'></span>
										</div>
										<div class="progress">
												<div class="progress-bar progress-bar-striped"></div>
											</div>
									</form>
								</div>
							</div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
					<!-- End Bootstrap modal -->
				</div>
			</div>
		</section>
</div>
{include file='js/global_js.tpl'}
{include file='js/jqueryui_js.tpl'}
{include file='js/formvalidation_js.tpl'}
{include file='js/datatables_js.tpl'}
{include file='js/ckeditor_js.tpl'}
<script>

var download_tbl;
var download_method;
var loading = $('#loading');
var progressbox     = $('.progress');
var progressbar     = $('.progress-bar');
var statustxt       = $('.percent');
var submitbutton    = $("#submitdownload");
var completed       = '0%';

function reload_table()
{
	download_tbl.fnReloadAjax(null,false);
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


// add
function download_add(){
    download_method = 'add';
    $('#dlform')[0].reset();
    $('.form-group').removeClass('has-error');
	$('#dlform').formValidation('resetForm', true);
    $('#download_form').modal('show');
	$('.dl_frm').val('Download Upload');
	statustxt.empty();
}

// edit
function download_edit(id){
	download_method = 'update';
	$('#dlform').trigger('reset');
	$('#dlform').formValidation('resetForm', true);
	$('.form-group').removeClass('has-error');
	$.ajax({
		url : "{$base_url}serverside/download/download_edit.php",
		data: "id="+id,
		type: "GET",
		dataType: "JSON",
		cache: false,
		success: function(datas)
		{
			$('#download_id').val(datas.id);
			$('#download_category').val(datas.download_category);
			$("#download_title").val(datas.download_title);
			$('#download_msg').val(datas.download_msg);
			$("option:selected",$('#download_network').val(datas.download_network)).text();
			$("option:selected",$('#download_device').val(datas.download_device)).text();
			$('#download_form').modal('show'); // show bootstrap modal when complete loaded
			$('.dl-title').text('Edit Download Form: ' +datas.download_title); // Set title to Bootstrap modal title
			$('.dl_frm').val('Download Update');
			statustxt.empty();
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert('Error get data from ajax');
		}
	});
}

// view
function download_view(id){
	$.ajax({
		url : "{$base_url}serverside/download/download_edit.php",
		data: "id="+id,
		type: "GET",
		dataType: "JSON",
		cache: false,
		success: function(data)
		{
			$('#output_msg').html(data.download_msg);
			$('#output_file').html(data.download_url);
			$('#download_view').modal('show');
			$('.modal-title').text('Edit Download Form: ' +data.download_title);
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert('Error get data from ajax');
		}
	});	
}

// delete
function delete_download() 
{
	$(".ajs-header").html('{$siteTitle} - Alert Message');
	alertify.confirm('Are you sure do you want to delete?',function(){
		$.ajax({
			type: "POST",
			url: "{$base_url}serverside/download/download_delete.php",
			data: $('#dl_frm').serialize(),
			success: function(data)
			{
				reload_table();
				$('#delete_download').prop('disabled', true);
				$('.success').html(data);
				$(".select-alls").prop('checked', false);
				$(".chk-boxs").prop('checked', false);
				alertify.success('Successfully Deleted');
			},
			error: function(data)
			{
				reload_table();
				$('.success').html(data);
				$(".select-alls").prop('checked', false);
				$(".chk-boxs").prop('checked', false);
				alertify.error('Failed to Deleted');
			}
		});
	},function(){
		alertify.error('Declined');
		$("#delete_download").prop('disabled', true);
		$(".chk-boxs").prop('checked', false);
		$(".select-alls").prop('checked', false);
	}).setting('labels',{literal}{'ok':'Accept', 'cancel': 'Decline'}{/literal});
}

$('#dlform').formValidation
({
	framework: 'bootstrap',
	icon: null,
	fields: 
	{
		download_title:
		{
			valid: true,
			message: 'The download title is not valid',
			validators:
			{
				notEmpty:
				{
					message: 'The download title is required and can\'t be empty'
				},
				stringLength:
				{
					min: 3,
					message: 'The download title must be more than 3'
				}
			}
		},
		download_file:
		{
			validators: 
			{
				file:
				{
					maxSize: 20 * 2024 * 2024,
					message: 'The file must be in .APK, .EXE, .MSI, .ZIP, .RAR format and must not exceed 10MB in size'
				}
			}
		},
		download_msg:
		{
			valid: true,
			message: 'The download message is not valid',
			validators:
			{
				notEmpty:
				{
					message: 'The download message is required and can\'t be empty'
				},
				stringLength:
				{
					min: 3,
					message: 'The download message must be more than 3'
				},
				callback:
				{
					message: 'The download message is must be less than 99999 characters long',
					callback: function(value, validator, $field)
					{
						if (value === '')
						{
							return true;
						}
						var div  = $('<div/>').html(value).get(0),
						text = div.textContent || div.innerText;
						return text.length <= 99999;
					}
				}
			}
		}
	}
})
.on('success.form.fv', function(e, data) {
	statustxt.empty();
	var $forms = $(e.target);
	$forms.ajaxForm({
		type: "POST",
		url: "{$base_url}serverside/download/download_upload.php",
		data: $forms.serialize(),
		cache: false,
		contentType: false,
		beforeSend: function()
		{
			loading.show();
		},
		uploadProgress: function(event, position, total, percentComplete) {
			progressbar.width(percentComplete + '%')
			statustxt.html(percentComplete + '%');
			if(percentComplete>20)
			{
				statustxt.css('color','#E9FC04');
			}
			if(percentComplete>40)
			{
				statustxt.css('color','#FC4B04');
			}
			if(percentComplete>60)
			{
				statustxt.css('color','#1E04FC');
			}
			if(percentComplete>80)
			{
				statustxt.css('color','#4BFC04');
			}
		},
		complete: function()
		{
			loading.hide();
			submitbutton.removeAttr('disabled');
			//progressbox.slideUp();
			statustxt.empty();
		},
		success: function(data)
		{
			$('.success').html(data);
			$forms.formValidation('resetForm', true);
			reload_table();
			$('#download_form').modal('hide');
			alertify.success('Successfully Submitted Record');
		},
		error: function()
		{
			$('.success').html(data);
			$('#download_form').modal('hide');
			$forms.formValidation('resetForm', true);
			alertify.success('Failed! Submitted Record');
		}
	});
});

$.fn.modal.Constructor.prototype.enforceFocus = function () {
	modal_this = this
	$(document).on('focusin.modal', function (e) {
		if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length
		&&
		!$(e.target.parentNode).hasClass('cke_dialog_ui_input_select') && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_text')) {
			modal_this.$element.focus()
		}
	})
};

$('document').ready(function()
{
    $('.select-alls').click(function(event) {
        if(this.checked) {
            $('.chk-boxs').each(function() {
                this.checked = true;
				$("#delete_download").prop('disabled', false);
            });
        }else{
            $('.chk-boxs').each(function() {
                this.checked = false;
            }); 
			if($(".select-alls").prop('checked') == false){
				$("#delete_download").prop('disabled', true);
			}
        }
    });

	if($(".select-alls").prop('checked') == false){
		$("#delete_download").prop('disabled', true);
	}

	$('body').delegate('.chk-boxs','click',function(event){
		if ($('.chk-boxs').is(':checked') == true){
			$("#delete_download").prop('disabled', false);
		} else {
			$("#delete_download").prop('disabled', true);
		}
	});

	download_tbl = $('#download_serverside').dataTable({
		responsive: true,
        "bProcessing": true,
        "bServerSide": true,
        "bStateSave": true,
        "ajax": {
            "url": "{$base_url}serverside/download/download_serverside.php",
            "type": "POST"
        },
		"aoColumnDefs": [{
			'bSortable': false,
			'aTargets': [0,-1]
		}],
		order: [[ 0, 'desc' ], [ 0, 'asc' ]],
		"iDisplayLength": 5,
		"aLengthMenu": [
				[5, 10, 25, 50, 100, -1],
				[5, 10, 25, 50, 100, "ALL"]
		],
		"sPaginationType": "full_numbers",
		language: {
			"sSearchPlaceholder": "Search..",
			"lengthMenu": "_MENU_",
			"search": "_INPUT_",
			"oPaginate":
			{
				"sFirst":'<i class="glyphicon glyphicon-backward"></i>',
				"sLast": '<i class="glyphicon glyphicon-forward"></i>',
				"sNext": '<i class="glyphicon glyphicon-chevron-right"></i>',
				"sPrevious": '<i class="glyphicon glyphicon-chevron-left"></i>'
			},
			"sInfo":'Showing _START_ to _END_ of _TOTAL_ entries',
			"sZeroRecords": "No matching records found"
		}
	});
});
</script>
{include file='apps/liveclock.tpl'}
</body>
</html>