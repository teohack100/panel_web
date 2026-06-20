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
<title>{$siteTitle} - DNS Record Creator</title>
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
				DNS Record Creator
			</h1>
			<ol class="breadcrumb">
				<li><a href="{$base_url}"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active">DNS Record Creator</li>
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
										DNS Records
									</div>
								</h4>
							</legend>
                            <div id="success"></div>
                            
							<div class="btn-group m-r-10" role="group">
							<div class="dropdown" role="group">
							    <button type="button" class="btn btn-outline btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"> <span class="caret"></span> <i class="fa fa-fw fa-list" aria-hidden="true"></i> Users Menu <span> <i class="fa fa-caret-down" aria-hidden="true"></i></span></button>
								<ul class="dropdown-menu animated flipInX" role="menu">
                                        <li>
                                            <button type="button" class="btn btn-info btn-block waves-effect waves-light text-left" onclick="dns_add()" id="dns_add">
					                            <i class="glyphicon glyphicon-plus"></i> Add Records
				                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="btn btn-primary btn-block waves-effect waves-light text-left" onClick="delete_dns_beta();" id="delete_dns" disabled>
					                           <i class="glyphicon glyphicon-remove-circle"></i> Multiple Delete
				                            </button>
                                        </li>
								</ul>
							</div>
							</div>
							
							
							<form method="post" id="frm" name="frm">
								<div class="panel">
									<div class="panel-body">
									<table id="dns-serverside" class="table table-striped table-bordered" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th class="text-center"><input type="checkbox" class="select-all" /></th>
												<th class="text-center">Host Name</th>
												<th class="text-center">Domain Name</th>
												<th class="text-center">IP Address</th>
												<th class="text-center">Record Type</th>
												<th class="text-center">Status</th>
												<th class="text-center">Controls</th>
											</tr>
										</thead>
										<tbody class="text-center">
											{foreach item=i from=$server}
												{$i}
											{/foreach}
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
									<form id="dns_frm" name="dns_frm">
										<input type="hidden" id="server_id" name="server_id">
										<input type="hidden" id="submitted" name="submitted" value="Server Upload">
										<div class="row">
											<div class="form-group control-group col-md-12">
												<div class="controls input-group">
													<label class="control-label input-group-addon" for="host_name">Hostname</label>
													<input class="form-control" id="host_name" name="host_name" placeholder="www.viber.com" />
												</div>
											</div>
											<div class="form-group control-group col-md-12">
												<div class="controls input-group">
													<label class="control-label input-group-addon" for="ip_address">IPv4 Address</label>
													<input class="form-control" id="ip_address" name="ip_address" placeholder="127.0.0.1" />
												</div>
											</div>
											<div class="form-group control-group col-md-12">
												<div class="controls input-group">
													<label class="control-label input-group-addon" for="domain_name">Domain</label>
													<select class="form-control" id="domain_name" name="domain_name">	
                                                        {$domain_list}
                                                    </select>
												</div>
											</div>
											<div class="form-group control-group col-md-12">
												<div class="controls input-group">
													<label class="control-label input-group-addon" for="record_type">Record Type</label>
													<select class="form-control" id="record_type" name="record_type">
														<option value="A" selected="selected">A Record</option>
														<!--option value="CNAME">CNAME</option-->
													</select>
												</div>
											</div>
										</div>
										<div class="control-group form-group">
											<div class="modal-footer">
												<button type="submit" id="submitDNS" name="submitDNS" class="btn btn-success">Create</button>
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

function dns_add(){
    save_method = 'add';
    $('#dns_frm')[0].reset();
	$('#dns_frm').trigger('reset');
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();
	$('#dns_frm').formValidation('resetForm', true);
    $('#modal_form').modal('show');
	$('.modal-title').text('DNS Record Form');
	$('#submitDNS').html("Create");
	$('#submitted').val("Create");
	$('#server_parser').html('');
}

function delete_dns() 
{
	$(".ajs-header").html('{$siteTitle} - Alert Message');
	alertify.confirm('Are you sure do you want to delete?',function(){
		$.ajax({
			type: "POST",
			url: "{$base_url}serverside/dns/dns_delete.php",
			data: $('form').serialize(),
			success: function(data) {
				reload_table();
				$('#delete_dns').prop('disabled', true);
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
		$("#delete_dns").prop('disabled', true);
		$(".chk-box").prop('checked', false);
		$(".select-all").prop('checked', false);
	}).setting('labels',{literal}{'ok':'Accept', 'cancel': 'Decline'}{/literal});
}

function dns_edit_beta(id){
	save_method = 'update';
	$('#dns_frm').trigger('reset');
	$('#dns_frm').formValidation('resetForm', true);
	$('#submitted').val("DNS Update");
	$('.form-group').removeClass('has-error');
	$('.help-block').empty();
	$.ajax({
		url : "{$base_url}serverside/dns/dns_edit.php",
		data: "dns_id="+id,
		type: "GET",		
		dataType: "JSON",
		cache: false,
		success: function(data)
		{
			$('#dns_frm').formValidation('resetForm', true);
			$('#dns_id').val(data.dns_id);
			$('#host_name').val(data.host_name);
			$('#domain_name').val(data.domain_name);
			$('#ip_address').val(data.ip_address);
			$('#record_type').val(data.record_type);
			$('#modal_form').modal('show'); // show bootstrap modal when complete loaded
			$('.modal-title').text('Edit DNS: ' +data.host_name+data.domain_name); // Set title to Bootstrap modal title
			$('#submitDNS').html("DNS Update");
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert('Error get data from ajax');
		}
	});
}

$('document').ready(function()
{
	table = $('#dns-serverside').dataTable({
		responsive: true,
        "bProcessing": true,
        "bServerSide": true,
        "bStateSave": true,
        "ajax": {
            "url": "{$base_url}serverside/dns/dns_serverside.php",
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

	$('#dns_frm').formValidation({
        framework: 'bootstrap',
		icon: null,
		fields: 
		{
			host_name:
			{
				valid: true,
				message: 'The hostname is not valid',
				validators: 
				{
                    notEmpty: 
					{
						message: 'The hostname is required and can\'t be empty'
                    },
                    stringLength: 
					{
                        min: 3,
                        message: 'The hostname must be more than 3'
                    }
				}
			},
			ip_address:
			{
				valid: true,
				message: 'The ip address is not valid',
				validators: 
				{
                    notEmpty: 
					{
						message: 'The ip address is required and can\'t be empty'
                    },
					ip: 
					{
						message: 'The value is not a valid ip address'
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
			url: "{$base_url}serverside/dns/dns_upload.php",
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
				$("#delete_dns").prop('disabled', false);
            });
        }else{
            $('.chk-box').each(function() {
                this.checked = false;
            });
			if($(".select-all").prop('checked') == false){
				$("#delete_dns").prop('disabled', true);
			}
        }
    });
	
	if($(".select-all").prop('checked') == false){
		$("#delete_dns").prop('disabled', true);
	}
	
	$('body').delegate('.chk-box','click',function(event){
		if ($('.chk-box').is(':checked') == true){
			$("#delete_dns").prop('disabled', false);
			console.log('checked');
		} else {
			$("#delete_dns").prop('disabled', true);
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