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
<title>{$siteTitle} - Server Update</title>
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
				All Servers
			</h1>
			<ol class="breadcrumb">
				<li><a href="{$base_url}"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active">Server Script</li>
			</ol>
		</section>
	
		<!-- Main content -->
		<section class="content">
			<!-- Default box -->
			<div class="box">
				<div class="box-body">
					<div class="col-md-12">
						<fieldset class="padding-20">
							<legend class="text-center bg-purple">
								<h4 class="text-white">
									<div class="panel-heading">
										Server Script
									</div>
								</h4>
							</legend>

							<div class="btn-group btn-group-justified" role="group">
								<div class="btn-group" role="group">
									<button type="button" class="btn btn-info" onclick="" id="server_add" >
										<i class="glyphicon glyphicon-plus"></i> &nbsp; Add Records
									</button>
								</div>
								<div class="btn-group" role="group">
									<button class="btn btn-danger" onClick="" id="delete_records">
										<i class="glyphicon glyphicon-remove-circle"></i> &nbsp; Multiple Delete
									</button>
								</div>
							</div>
							<div id="success"></div>
							<form method="post" id="frm" name="frm">
								<div class="panel">
									<div class="panel-body">
									<table id="script-serverside" class="table table-striped table-bordered lenz-table" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th class="text-center">Type</th>
												<th class="text-center">Platform</th>
												<th class="text-center">Script</th>
											</tr>
										</thead>
										<tbody>
										    <tr>
											   	<td class="text-center">PREMIUM</td>
												<td class="text-center">CENTOS 6</td>
												<td class="text-center">wget http://lenz.ph/OctaviaVPN/premium.sh && chmod +x premium.sh && ./premium.sh</td>
											</tr>
											<tr>
											   	<td class="text-center">VIP</td>
												<td class="text-center">CENTOS 6</td>
												<td class="text-center">http://lenz.ph/OctaviaVPN/vip.sh && chmod +x vip.sh && ./vip.sh</td>
											</tr>
											<tr>
											   	<td class="text-center">PRIVATE</td>
												<td class="text-center">CENTOS 6</td>
												<td class="text-center">http://lenz.ph/OctaviaVPN/private.sh && chmod +x private.sh && ./private.sh</td>
											</tr>
											<tr>
											   	<td class="text-center">3 n 1</td>
												<td class="text-center">DEBIAN 9</td>
												<td class="text-center">wget http://lenz.ph/OctaviaVPN/xdebian9.sh && chmod +x xdebian9.sh && ./xdebian9.sh</td>
											</tr>
										</tbody>
									</table>
									</div>
								</div>
							</form>
						</fieldset>
					</div>
					
					<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="modal_form" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h3 class="modal-title"></h3>
								</div>
								<div class="modal-body">
									<form id="server_frm" name="server_frm">
										<input type="hidden" id="server_id" name="server_id">
										<input type="hidden" id="submitted" name="submitted" value="Server Upload">
										<div class="row">
											<div class="form-group control-group col-md-12">
												<div class="controls input-group">
													<label class="control-label input-group-addon" for="server_name">Server Name</label>
													<input class="form-control" id="server_name" name="server_name" placeholder="Server Name" />
												</div>
											</div>
											<div class="form-group control-group col-md-6">
												<div class="controls input-group">
													<label class="control-label input-group-addon" for="server_category">Server Category</label>
													<select class="form-control" id="server_category" name="server_category">
														<option value="premium" selected="selected">Premium Server</option>
														<option value="vip">VIP Server</option>
														<option value="ph">Philippine Server</option>
														<option value="private">Private Server</option>
														<option value="free">Free Server</option>
													</select>
												</div>
											</div>
											<div class="form-group control-group col-md-6">
												<div class="controls input-group">
													<label class="control-label input-group-addon" for="server_ip">Server IP</label>
													<input class="form-control" id="server_ip" name="server_ip" placeholder="IP Address" />
												</div>
											</div>
										</div>
										<div class="row">											
											<div class="form-group control-group col-md-4">
												<div class="controls input-group">
													<label class="control-label input-group-addon" for="server_port">Port</label>
													<input class="form-control" id="server_port" name="server_port" placeholder="Server Port" />
												</div>
											</div>
											<div class="form-group control-group col-md-4">
												<div class="controls input-group">
													<label class="control-label input-group-addon" for="server_folder">Port</label>
													<input class="form-control" id="server_folder" name="server_folder" placeholder="Server Folder" />
												</div>
											</div>
											<div class="form-group control-group col-md-4">
												<div class="controls input-group">
													<label class="control-label input-group-addon" for="server_tcp">TCP</label>
													<input class="form-control" id="server_tcp" name="server_tcp" placeholder="Server TCP" />
												</div>
											</div>
											<div class="col-md-12 text-center">
												<div id="server_parser" class="form-control col-md-12"></div>
											</div>
										</div>
										<div class="control-group form-group">
											<div class="modal-footer">
												<button type="submit" id="submitServer" name="submitServer" class="btn btn-success">Server Upload</button>
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
		</section>
</div>
{include file='js/global_js.tpl'}
{include file='js/jqueryui_js.tpl'}
{include file='js/formvalidation_js.tpl'}
{include file='js/datatables_js.tpl'}
{include file='js/ckeditor_js.tpl'}
<script>
var save_method;
var loading = $('#loading');
var table;
function reload_table()
{
	table.fnReloadAjax(null,false);
}

function server_add(){
    save_method = 'add';
    $('#server_frm')[0].reset();
	$('#server_frm').trigger('reset');
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();
	$('#server_frm').formValidation('resetForm', true);
    $('#modal_form').modal('show');
	$('.modal-title').text('Server List Form');
	$('#submitServer').html("Server Upload");
	$('#submitted').val("Server Upload");
	$('#server_parser').html('');
}

function delete_records() 
{
	$(".ajs-header").html('{$siteTitle} - Alert Message');
	alertify.confirm('Are you sure do you want to delete?',function(){
		$.ajax({
			type: "POST",
			url: "{$base_url}serverside/serverupload/server_delete.php",
			data: $('form').serialize(),
			success: function(data) {
				reload_table();
				$('#delete_records').prop('disabled', true);
				$('#success').html(data);
				$(".select-all").prop('checked', false);
				$(".chk-box").prop('checked', false);
				alertify.success('Successfully Deleted');
			},
			error: function(data) {	
				reload_table();
				$('#success').html(data);
				$(".select-all").prop('checked', false);
				$(".chk-box").prop('checked', false);
				alertify.error('Failed to Deleted');
			}
		});
	},function(){
		alertify.error('Declined');
		$("#delete_records").prop('disabled', true);
		$(".chk-box").prop('checked', false);
		$(".select-all").prop('checked', false);
	}).setting('labels',{literal}{'ok':'Accept', 'cancel': 'Decline'}{/literal});
}

function server_edit(id){
	save_method = 'update';
	$('#server_frm').trigger('reset');
	$('#server_frm').formValidation('resetForm', true);
	$('#submitted').val("Server Edit");
	$('.form-group').removeClass('has-error');
	$('.help-block').empty();
	$.ajax({
		url : "{$base_url}serverside/serverupload/server_edit.php",
		data: "server_id="+id,
		type: "GET",		
		dataType: "JSON",
		cache: false,
		success: function(data)
		{
			$('#server_frm').formValidation('resetForm', true);
			$('#server_id').val(data.server_id);
			$('#server_name').val(data.server_name);
			$('#server_category').val(data.server_category);
			$('#server_ip').val(data.server_ip);
			$('#server_port').val(data.server_port);
			$('#server_folder').val(data.server_folder);
			$('#server_tcp').val(data.server_tcp);
			$('#server_parser').html(data.server_parser);
			$('#modal_form').modal('show'); // show bootstrap modal when complete loaded
			$('.modal-title').text('Edit Server List Form: ' +data.server_name); // Set title to Bootstrap modal title
			$('#submitServer').html("Server Update");
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert('Error get data from ajax');
		}
	});
}

$('document').ready(function()
{
	table = $('#servers-serverside').dataTable({
		responsive: true,
        "bProcessing": true,
        "bServerSide": true,
        "bStateSave": true,
        "ajax": {
            "url": "{$base_url}serverside/serverupload/serverlist_serverside.php",
            "type": "POST"
        },
		"aoColumnDefs": [{
			'bSortable': false,
			'aTargets': [0,-1]
		}],
		order: [[ 0, 'desc' ], [ 0, 'asc' ]],
		"iDisplayLength": 5,
		"aLengthMenu": [
				[5, 10, 25, 50, 100, 99999999999999],
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

	$('#server_frm').formValidation({
        framework: 'bootstrap',
		icon: null,
		fields: 
		{
			server_name:
			{
				valid: true,
				message: 'The server name is not valid',
				validators: 
				{
                    notEmpty: 
					{
						message: 'The server name is required and can\'t be empty'
                    },
                    stringLength: 
					{
                        min: 3,
                        message: 'The server name must be more than 3'
                    }
				}
			},
			server_category:
			{
				valid: true,
				message: 'The server category is not valid',
				validators: 
				{
                    notEmpty: 
					{
						message: 'The server category  is required and can\'t be empty'
                    }
				}
			},
			server_port:
			{
				valid: true,
				message: 'The server port is not valid',
				validators: 
				{
                    notEmpty: 
					{
						message: 'The server port  is required and can\'t be empty'
                    }
				}
			},
			server_tcp:
			{
				valid: true,
				message: 'The server tcp is not valid',
				validators: 
				{
                    notEmpty: 
					{
						message: 'The server tcp  is required and can\'t be empty'
                    }
				}
			},
			server_ip:
			{
				valid: true,
				message: 'The server ip address is not valid',
				validators: 
				{
                    notEmpty: 
					{
						message: 'The server ip address is required and can\'t be empty'
                    },
					ip: 
					{
						message: 'The value is not a valid server ip address'
					}
				}
			}
		}
    })
	
	.on('success.form.fv', function(e, data) {
		e.preventDefault();	

        var $form = $(e.target);
		$.ajax({
			type: "POST",
			url: "{$base_url}serverside/serverupload/server_upload.php",
			data: $form.serialize(),
			beforeSend: function() {
				loading.show();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$('#success').html(data);
				alertify.success('Failed! Submitted Record');
			},
			success: function(data){
				$('#success').html(data);
				$form.formValidation('resetForm', true);
				$('#modal_form').modal('hide');
				reload_table();
				alertify.success('Successfully Submitted Record');
			},
			complete: function(){
				loading.hide();
			}
		});
	});

    $('.select-all').click(function(event) {
        if(this.checked) {
            $('.chk-box').each(function() {
                this.checked = true;
				$("#delete_records").prop('disabled', false);
            });
        }else{
            $('.chk-box').each(function() {
                this.checked = false;
            });
			if($(".select-all").prop('checked') == false){
				$("#delete_records").prop('disabled', true);
			}
        }
    });
	
	if($(".select-all").prop('checked') == false){
		$("#delete_records").prop('disabled', true);
	}
	
	$('body').delegate('.chk-box','click',function(event){
		if ($('.chk-box').is(':checked') == true){
			$("#delete_records").prop('disabled', false);
			console.log('checked');
		} else {
			$("#delete_records").prop('disabled', true);
			console.log('unchecked');
		}
	});
	
	$('#server_ip').on('input', function (event){
		this.value = this.value.replace(/[^0-9-.]/g, '');
	});
	$('#server_port').on('input', function (event){
		this.value = this.value.replace(/[^0-9]/g, '');
	});
});
</script>
{include file='apps/liveclock.tpl'}
</body>
</html>