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
<title>{$siteTitle} - Sub Reseller</title>
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
{include file='css/sweetalert_css.tpl'}
{include file='css/sweetalert2.tpl'}
</head>
<body class="skin-purple-light layout-boxed sidebar-mini">
<!-- Site wrapper -->
<div class="lenz-wrapper-boxed">

{include file='apps/navigation.tpl'}

	<!-- Content Wrapper. Contains page content -->
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>
				All Sub Reseller
			</h1>
			<ol class="breadcrumb">
				<li><a href="{$base_url}"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active">Sub Reseller</li>
			</ol>
		</section>
		<!-- Main content -->
		<section class="content">
			<!-- Default box -->
			<div class="box">
				<div class="box-body">
					<div class="col-lg-12 padding-top-20">
						<fieldset class="padding-20">
							<div id="success"></div>
							<legend class="text-center bg-purple">
								<h4 class="text-white">
									<div class="panel-heading">
										Sub Resellers List
									</div>
								</h4>
							</legend>
							<form id="delflag_frm" name="delflag_frm">
								<input type="hidden" id="submitted" name="submitted" value="Suspend | Delete Submitted">
								<div class="btn-group btn-group-justified" role="group">
									<div class="btn-group" role="group">
										<button type="button" class="btn btn-success btns" id="demoteSubmit" name="demoteSubmit" onclick="demoteSubmitted()" title="Demote Account">
											<i class="icon wb-user" aria-hidden="true"></i><i class="fa fa-level-down" aria-hidden="true"></i>
										</button>
									</div>
									<div class="btn-group" role="group">
										<button type="button" class="btn btn-info btns" id="freezeSubmit" name="freezeSubmit" onclick="freezeSubmitted()"  title="Freeze Account">
											<i class="icon wb-stop" aria-hidden="true"></i>
										</button>
									</div>
									<div class="btn-group" role="group">
										<button type="button" class="btn btn-danger btns" id="deleteSubmit" name="deleteSubmit" onclick="deleteSubmitted()" title="Delete Account">
											<i class="icon wb-trash" aria-hidden="true"></i>
										</button>
									</div>
								</div>
								<table id="users-serverside" class="table table-striped table-bordered" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th class="text-center"><input type="checkbox" class="select-all" /></th>
											<th class="text-center">Username</th>
											<th class="text-center">Credits</th>
											<th class="text-center">Upline</th>
											<th class="text-center">Controls</th>
										</tr>	
									</thead>
									<tbody class="text-center">	
									</tbody>
								</table>
							</form>
						</fieldset>
					</div>
					
					<div class="col-lg-12 padding-top-20">
						<div id="success2"></div>
						<fieldset class="padding-20">
							<legend class="text-center bg-purple">
								<h4 class="text-white">
									<div class="panel-heading">
										<i class="glyphicon glyphicon-user"></i> Credits History
									</div>
								</h4>
							</legend>
							<table id="creditsTbl" class="table table-striped table-bordered display" cellspacing="0" width="100%">
								<thead>
									<tr>
										<th class="text-center">Username</th>
										<th class="text-center">Credits</th>
										<th class="text-center">Date</th>
										<th class="text-center">Time Lapsed</th>
									</tr>
								</thead>
								<tbody class="text-center"></tbody>
							</table>
						</fieldset>					
					</div>
					<!-- Start Bootstrap modal -->
					<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="modal_form" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h3 class="modal-title"></h3>
								</div>
								<div class="modal-body">
									<form id="register">
										<input type="hidden" id="submitted" name="submitted" value="Register Account">
										<div class="summary-errors alert alert-danger alert-dismissible">
											<p>Errors list below: </p>
											<ul></ul>
										</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label class="control-label" for="full_name"><i class="glyphicon glyphicon-user"></i> Full Name:</label>
												<input id="full_name" type="text" class="form-control capitalize" name="full_name" value="" required> 	
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="control-label" for="user_name"><i class="glyphicon glyphicon-user"></i> Username:</label>	
												<input id="user_name" type="text" class="form-control" name="user_name" value="" required>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="control-label" for="user_email"><i class="glyphicon glyphicon-envelope"></i> Email Address:</label>
												<input class="form-control" type="email" id="user_email" name="user_email" value="" required>
											</div>
										</div>
										<div id="client_mode" class="col-md-6">
											<div class="form-group">
												<label class="control-label" for="client_type"><i class="icon wb-users"></i> Client Type:</label>
												<select class="form-control" id="client_type" name="client_type" title="Client Type">
													<option value="{$premium_encrypt}">Premium Client</option>
													<option value="{$vip_encrypt}">VIP Client</option>
													<option value="{$private_encrypt}">Private Client</option>
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label class="control-label" for="user_pass"><i class="glyphicon-lock"></i> Password:</label>
												<div class="input-group">
													<input id="user_pass" type="password" class="form-control" name="user_pass" value=""
													autocomplete="off" ondrop="return false;" onpaste="return false;" required>
													<a class="input-group-addon" href="javascript:;" onclick="toggle_password('user_pass');" id="showhide"><i class="glyphicon glyphicon-eye-open"></i></a>
												</div>
												<div class="progress password-meter" id="signuppwdMeter">
													<div class="progress-bar"></div>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="control-label" for="user_pass2"><i class="glyphicon-lock"></i> Confirm Password:</label>
												<div class="input-group">
													<input id="user_pass2" type="password" class="form-control" name="user_pass2" value=""
													autocomplete="off" ondrop="return false;" onpaste="return false;" required>
													<a class="input-group-addon" href="javascript:;" onclick="new_password('user_pass2');" id="newshowhide"><i class="glyphicon glyphicon-eye-open"></i></a>
												</div>
												<div class="progress password-meter" id="chkpwdMeter">
													<div class="progress-bar"></div>
												</div>
											</div>
										</div>
										<div id="role_mgt" class="col-md-12">
											<div class="form-group">
												<label class="control-label" for="role_acct"><i class="glyphicon glyphicon-stats"></i> Role Management:</label>
												<select class="form-control" id="role_acct" name="role_acct" title="Role Management">
													<option value="1" selected="selected">Free User</option>
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'administrator'}
													<option value="2">Sub Reseller</option>
													{/if}
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator'}
													<option value="3">Reseller</option>
													{/if}
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin'}
													<option value="4">administrator</option>
													<option value="5">Sub-Administrator</option>
													{/if}
												</select>
											</div>
										</div>
									</div>
									<div id="hidden" class="row hidden">
										<div class="col-md-{if $user_id_2 == 1 || $user_level_2 == 'superadmin'}6{else}12{/if}">
											<div class="form-group">
												<label class="control-label" for="role"><i class="glyphicon glyphicon-stats"></i> Role Management:</label>
												<select class="form-control" id="role" name="role" title="Role Management">
													<option value="1" selected="selected">Client</option>
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'administrator'}
													<option value="2">Sub Reseller</option>
													{/if}
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator'}
													<option value="3">Reseller</option>
													{/if}
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin'}
													<option value="4">administrator</option>
													<option value="5">Sub Administrator</option>
													{/if}
												</select>
											</div>
										</div>
										{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator'}
										<div class="col-md-{if $user_id_2 == 1}6{else}12{/if}">
											<div class="form-group">
												<label class="control-label" for="resellers">
												<i class="glyphicon glyphicon-stats"></i> Upline User:</label>
												<select class="form-control" id="resellers" name="resellers" title="Reseller">
												</select>
											</div>
										</div>
										{/if}
										<input type="hidden" id="secret" name="secret">
									</div>	
										<div class="control-group form-group">
											<div class="modal-footer">
												<button type="submit" id="submitRegister" name="submitRegister" class="btn btn-success">Add User</button>
												<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
												<span align="left" id="loading"></span>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					<!-- End Bootstrap modal -->
					<div class="modal fade" id="view_modal" tabindex="-1" role="dialog" aria-labelledby="view_modal" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h3 class="modal-title">Info</h3>
								</div>
								<div class="modal-body">
									<table class="table table-bordered table-responsive">
										<tr>
											<td colspan="2" class="text-center">Shadowsocks Account</td>
										</tr>
										<tr>
											<td><label for="ssport"><i class="glyphicon glyphicon-user"></i> SS Port: </label></td>
											<td><div id="ssport"></div></td>
										</tr>
										<tr>
											<td><label for="sspass"><i class="glyphicon glyphicon-user"></i> SS Password: </label></td>
											<td><div id="sspass"></div></td>
										</tr>
										
										<tr>
											<td colspan="2" class="text-center">Account Info</td>
										</tr>
										<tr>
											<td><label for="fullname"><i class="glyphicon glyphicon-user"></i> Full Name: </label></td>
											<td><div id="fullname"></div></td>
										</tr>
										<tr>
											<td><label for="username"><i class="glyphicon glyphicon-user"></i> Username: </label></td>
											<td><div id="username"></div></td>
										</tr>
										<tr>
											<td><label for="password"><i class="glyphicon glyphicon-lock"></i> Password: </label></td>
											<td><div id="password"></div></td>
										</tr>
										<tr>
											<td><label for="email"><i class="glyphicon glyphicon-envelope"></i> Email Address: </label></td>
											<td><div id="email"></div></td>
										</tr>
										<tr>
											<td><label for="premiumstatus"><i class="glyphicon glyphicon-stats"></i> Premium status: </label></td>
											<td><div id="premiumstatus"></div></td>
										</tr>
										<tr>
											<td><label for="premiumduration"><i class="glyphicon glyphicon-time"></i> Expiration Day: </label></td>
											<td><div id="premiumduration"></div></td>
										</tr>
										<tr>
											<td><label for="regdate"><i class="glyphicon glyphicon-time"></i> Date joined: </label></td>
											<td><div id="regdate"></div></td>
										</tr>
										<tr>
											<td><label for="ipaddress"><i class="glyphicon glyphicon-time"></i> Last login from: </label></td>
											<td><div id="ipaddress"></div></td>
										</tr>
										<tr>
											<td><label for="lastlogin"><i class="glyphicon glyphicon-time"></i> Last login date: </label></td>
											<td><div id="lastlogin"></div></td>
										</tr>
										<tr>
											<td><label for="status"><i class="glyphicon glyphicon-stats"></i> Active Status: </label></td>
											<td><div id="status"></div></td>
										</tr>
									</table>
								</div>
							</div>
						</div>
					</div>
					<!-- End Bootstrap modal -->
					<div class="modal fade" id="credits_form" tabindex="-1" role="dialog" aria-labelledby="credits_form" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h3 class="modal-title">Add Credits</h3>
								</div>
								<div class="modal-body">
									<form id="formCredits" name="formCredits" method="post">
										<div class="row">
											<div class="col-sm-12">
												<div class="form-group input-group">
													<label class="input-group-addon control-label"><i class="glyphicon glyphicon-user"></i></label>
													<div id="get_user" class="form-control"></div>
												</div>
												<div class="form-group input-group">
													<label class="input-group-addon control-label"><i class="glyphicon glyphicon-barcode"></i></label>
													<div id="get_credits" class="form-control"></div>
												</div>
												<div class="form-group input-group">
													<label class="input-group-addon control-label" for="add_credits"><i class="glyphicon glyphicon-barcode"></i></label>
													<input class="form-control" type="text" id="add_credits" name="add_credits" {if $sub_admin_2 == 1}min="1" max="{$credits_2}"{/if}>
												</div>
												<input type="hidden" class="form-control" id="credits_secret" name="credits_secret">
												<input type="hidden" class="form-control" id="credits_code" name="credits_code">
												<input type="hidden" class="form-control" id="submitted" name="submitted" value="Add Credits">
												{if $user_id_2=='1' || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'administrator'}
												<div class="form-group">
													<select class="form-control" id="category" name="category">
														<option value="{$add_encrypt}" selected="selected">Add Credits</option>
														<option value="{$substract_encrypt}">Substract Credits</option>
													</select>
												</div>
												{else}
												<input type="hidden" class="form-control" id="category" name="category" value="{$add_encrypt}">
												{/if}
												<div class="control-group form-group">
													<div class="modal-footer">
														<button type="submit" id="submitReseller" name="submitReseller" class="btn btn-primary">Save</button>
														<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
														<span align="left" id="loading"></span>
													</div>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					<!-- End Bootstrap modal -->
				</div>
			</div>
		</section>
</div>
{include file='js/global_js.tpl'}
{include file='js/jqueryui_js.tpl'}
{include file='js/formvalidation_js.tpl'}
{include file='js/sweetalert_js.tpl'}
<script>
	alertify.defaults.glossary.title = 'CyberghostVPN';
function closed(){
		setTimeout(function () { $('.closedBtn').trigger('click'); }, 10000);
	}
</script>
{include file='js/datatables_js.tpl'}
<script>

var loading = $('#loading');
var save_method;
var table;
var credits_table;

function reload_table()
{
	table.fnReloadAjax(null,false);
	credits_table.fnReloadAjax(null,false);
}

$('document').ready(function()
{
	table = $('#users-serverside').dataTable({
		responsive: true,
        "bProcessing": true,
        "bServerSide": true,
        "bStateSave": true,
        "ajax": {
            "url": "/subreseller-serverside",
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
	
	credits_table = $('#creditsTbl').dataTable({
		responsive: true,
        "bProcessing": true,
        "bServerSide": true,
        "bStateSave": true,
        "ajax": {
            "url": "/credit_logs",
            "type": "POST"
        },
		"aoColumnDefs": [{
			'bSortable': false,
			'aTargets': [-1]
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
});

function getCredits(u,n)
{
	$('#formCredits')[0].reset();
	$('.form-group').removeClass('has-error');
	$('#formCredits').formValidation('resetForm', true);
	$.ajax({
        url: "{$base_url}serverside/users/get-user-data.php",
		data: "uid="+u+"&ucode="+n,
        type: "GET",
        dataType: "JSON",
		cache: false,
        success: function(data)
        {
			if(data.response == 1)
			{
				$('#credits_secret').val(data.secret);
				$('#credits_code').val(data.code);
				$('#get_user').html(data.user_name);
				$('#get_credits').html(data.credits);
				$('#credits_form').modal('show');
				$('.modal-title').text(data.mycredits+' | Username: '+data.user_name);
			}
			if(data.response == 2)
			{
				swal("CyberghostVPN", "Oh Oh! You dont have access to this upline!", "warning");
			}
			if(data.response == 0){
				swal("CyberghostVPN", "Authorization Failed!", "danger");
			}
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            swal("CyberghostVPN", "Error Get Data From Ajax!", "info");
        }
    });
}

$(document).ready(function($){
	$('#formCredits').formValidation
	({
		framework: 'bootstrap',
		excluded: ':disabled',
		icon: null,
		fields: 
		{
			add_credits:
			{
				valid: true,
				message: 'The Quantity is not valid',
				validators:
				{
					notEmpty:
					{
						message: 'The Quantity is required and can\'t be empty'
					},
                    stringLength: 
					{
                        min: 1,
                        message: 'The Quantity must be more than 1'
                    },
                    regexp: 
					{
						regexp: /^[0-9\.]+$/,
						message: 'The Quantity can only consist of numeric number'
                    }
				}
			}
		}
	})
	.on('success.form.fv', function(e, data) {
		e.preventDefault();
		var $form = $(e.target);
		var vouchers_loader = $('#vouchers_loader');
		alertify.confirm('Are you sure? Do you want to Reload Credits for this user?',function(e)
		{
			if(e)
			{	
				$.ajax({
				url: "{$base_url}serverside/credits/reload_credits.php",
				type: "POST",
				data: $form.serialize(),
				cache: false,
				dataType: "JSON",
				beforeSend: function() {
						vouchers_loader.html('Please! Wait!... While Uploading Data...');
				},
				complete: function(){
					vouchers_loader.html('');
				},
				success: function(data) {
					if(data.response == 1)
					{
						$('#credits_form').modal('hide');
						$form.formValidation('resetForm', true);
						reload_table();
						closed();
						$('#success').html(data.message);
						swal({
							  position: 'center',
							  type: 'success',
							  title: 'Your work has been save!',
							  showConfirmButton: false,
							  timer: 1500
							})
					}
					if(data.response == 2)
					{
						$('#success').html(data.message);
						reload_table();
						closed();
						swal({
							  position: 'center',
							  type: 'error',
							  title: 'Fail to save Changes!',
							  showConfirmButton: false,
							  timer: 1500
							})
					}
					if(data.response == 0)
					{
						swal("CyberghostVPN", "Oh Oh! You dont have access to this upline!", "warning");
					}
				}
				});
			}
		},function(){
			alertify.error('Declined');
			reload_table();
			closed();
			displayInActive();
		});
	});
});

function add_user(){
    save_method = 'add';
    $('#register')[0].reset(); 
    $('.form-group').removeClass('has-error'); 
    $('.help-block').empty(); 
	$('#register').formValidation('resetForm', true);
    $('#modal_form').modal('show'); 
	$('.modal-title').text('Add Client'); 
	$('#hidden').addClass('hidden');
	$('#secret').prop('disabled', true);
	$('#resellers').prop('disabled', true);
	$('#submitRegister').html('Add User Submit');
	$('#role_acct').prop('disabled', false);
	$('#role_mgt').removeClass('hidden');
}

function edit_user(u,n)
{
	save_method = 'update';
	$('#register')[0].reset(); // reset form on modals
	$('.form-group').removeClass('has-error'); // clear error class
	$('.help-block').empty(); // clear error string
	$('#register').formValidation('resetForm', true);
	$('#hidden').removeClass('hidden');
	$('#secret').prop('disabled', false);
	$('#resellers').prop('disabled', false);
	$('#role_acct').prop('disabled', true);
	$('#client_type').prop('disabled', false);
	$('#role_mgt').addClass('hidden');
	$('#role').prop('disabled', false);

	$.ajax({
        url: "{$base_url}serverside/users/get-user.php",
		data: "uid="+u+"&ucode="+n,
        type: "GET",
        dataType: "JSON",
		cache: false,
        success: function(data)
        {
			var upline = data.upline;
		{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'administrator'}
			$.ajax
			({
				url: "{$base_url}serverside/users/get-upline.php",
				data: "uid="+u+"&ucode="+n,
				type: "GET",
				dataType: "JSON",
				cache: false,
				success: function(values)
				{
					var optionHtml="";
					$.each(values, function(index,object){
						if(object.user_id == upline){
							var selected = 'selected="selected"';
						}else{
							selected = '';
						}
					optionHtml = optionHtml  +
						"<option value='"+object.user_id+"' "+selected+"> " 
						+object.user_name+
						"</option>";
					});
					
					var optionWithSelect = optionHtml;
					$('#resellers').html(optionWithSelect);

				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					alert('Error get data from ajax');
				}
			});
		{/if}
		
			$('#secret').val(data.secret);
			$('#user_name').val(data.user_name);
            $('#user_email').val(data.user_email);
			$('#user_pass').val(data.user_pass);
			$('#user_pass2').val(data.user_pass);
            $('#full_name').val(data.full_name);
			$("option:selected",$('#client_type').val(data.client_type)).text();
			$("option:selected",$('#role').val(data.role)).text();
			$("option:selected",$('#is_active').val(data.is_active)).text();
            $('#modal_form').modal('show'); 
            $('.modal-title').text('Account Edit Info: ' +data.user_name); 
			$('#submitRegister').html('Update User: ' +data.user_name);
            
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });	
}

function view_info(u,n)
{
	$.ajax({
        url: "{$base_url}serverside/users/get-info.php",
		data: "uid="+u+"&ucode="+n,
        type: "GET",		
        dataType: "JSON",
		cache: false,
        success: function(data)
        {
			if(data.premiumduration > 0){
				var premiumstatus = 'Active';
			}else{
				premiumstatus = 'Trial';
			}

			if(data.status > 0){
				var status = 'Active';
			}else{
				status = 'Deactive';
			}
			
			$('#lastlogin').html(data.lastlogin);
			$('#premiumstatus').html(premiumstatus);
			$('#premiumduration').html(data.premiumdate);
			$('#status').html(status);
			$('#ipaddress').html(data.ipaddress);
			$('#password').html(data.password);
			$('#regdate').html(data.regdate);
			$('#email').html(data.email);
			$('#fullname').html(data.fullname);
			$('#username').html(data.username);
			$('#ssport').html(data.ssport);
			$('#sspass').html(data.sspass);
            $('#view_modal').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Account Info: ' +data.username); // Set title to Bootstrap modal title
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

$('document').ready(function()
{
	$('.summary-errors').hide();
	$('#register').formValidation
	({
        framework: 'bootstrap',
		excluded: ':disabled',
		icon: null,
		fields: 
		{
			full_name:
			{				
				valid: true,
				message: 'The account name is not valid',
				validators: 
				{
                    notEmpty: 
					{
                    message: 'The account name is required and can\'t be empty'
                    },
                    stringLength: 
					{
                        min: 8,
                        message: 'The account name must be more than 8'
                    }
				}
			},			
			user_name:
			{				
				valid: true,
				message: 'The Username is not valid',
				validators: 
				{
                    notEmpty: 
					{
                    message: 'The Username is required and can\'t be empty'
                    },
                    stringLength: 
					{
                        min: 3,
						max: 15,
                        message: 'The Username must be more than 3 and less than 15 characters long'
                    },
					callback: 
					{
                        message: 'The Username is not valid',
                        callback: function(value, validator, $field) 
						{
                            if (value === '') {
                                return true;
                            }
							
                            if (value === value.toUpperCase()) {
                                return {
                                    valid: false,
                                    message: 'It must contain at least one lower case character'
                                }
                            }
							return true;
						}
					}
                }
			},
			user_email:
			{
				valid: true,
				message: 'The email address is not valid',
                validators: 
				{
					user_emailAddress: 
					{
						message: 'The input is not a valid email address'
					},
					notEmpty: 
					{
						message: 'The email is required and can\'t be empty'
                    },
					regexp: 
					{
						regexp: '^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$',
						message: 'The value is not a valid email address'
					}

				}
			},
			user_pass:
			{
				valid: true,
				message: 'The Password is not valid',
				validators: 
				{
					notEmpty: 
					{
                    message: 'The Password is required and can\'t be empty'
                    },
                    stringLength: 
					{
                        min: 4,
						max: 20,
                        message: 'The Password must be more than 8 and less than 20 character long'
                    },
                    identical: 
					{
                        field: 'user_pass2',
                        message: 'The password and its confirm are not the same'
                    },
					callback: 
					{
						callback: function(value, validator, $field) {
						var score = 0;

							if (value === '') {
								return {
									valid: true,
									score: null
								};
							}

							score += ((value.length >= 8) ? 1 : -1);

							if (/[A-Z]/.test(value)) {
								score += 1;
							}

							if (/[a-z]/.test(value)) {
								score += 1;
							}

							if (/[0-9]/.test(value)) {
								score += 1;
							}

							if (/[!#$%&^~*_]/.test(value)) {
								score += 1;
							}

							return {
								valid: true,
								score: score    
							};
						}
					}
                }
			},
			user_pass2:
			{				
				valid: true,
				message: 'The Password is not valid',
				validators: 
				{
					notEmpty: 
					{
                    message: 'The Password is required and can\'t be empty'
                    },
                    stringLength: 
					{
                        min: 4,
						max: 20,
                        message: 'The Password must be more than 8 and less than 20 character long'
                    },
                    identical: 
					{
                        field: 'user_pass',
                        message: 'The password and its confirm are not the same'
                    },
					callback: 
					{
						callback: function(value, validator, $field) {
						var score = 0;

							if (value === '') {
								return {
									valid: true,
									score: null
								};
							}

							score += ((value.length >= 8) ? 1 : -1);

							if (/[A-Z]/.test(value)) {
								score += 1;
							}

							if (/[a-z]/.test(value)) {
								score += 1;
							}
							
							if (/[0-9]/.test(value)) {
								score += 1;
							}

							if (/[!#$%&^~*_]/.test(value)) {
								score += 1;
							}

							return {
								valid: true,
								score: score 
							};
						}
					}
                }
            }
        }
    })
    .on('success.validator.fv', function(e, data) {
			
        if (data.field === 'user_pass' && data.validator === 'callback') {
                
            var score = data.result.score,
                $bar  = $('#signuppwdMeter').find('.progress-bar');

            switch (true) {
                case (score === null):
                    $bar.html('').css('width', '0%').removeClass().addClass('progress-bar');
                    break;

                case (score <= 0):
                    $bar.html('Very weak').css('width', '25%').removeClass().addClass('progress-bar progress-bar-danger');
                    break;

                case (score > 0 && score <= 2):
                    $bar.html('Weak').css('width', '50%').removeClass().addClass('progress-bar progress-bar-warning');
                    break;

                case (score > 2 && score <= 4):
                    $bar.html('Medium').css('width', '75%').removeClass().addClass('progress-bar progress-bar-info');
                    break;

                case (score > 4):
                    $bar.html('Strong').css('width', '100%').removeClass().addClass('progress-bar progress-bar-success');
                    break;

                default:
                    break;
            }
        }
		
        if (data.field === 'user_pass2' && data.validator === 'callback') {
            
            var score = data.result.score,
                $bar  = $('#chkpwdMeter').find('.progress-bar');

            switch (true) {
                case (score === null):
                    $bar.html('').css('width', '0%').removeClass().addClass('progress-bar');
                    break;

                case (score <= 0):
                    $bar.html('Very weak').css('width', '25%').removeClass().addClass('progress-bar progress-bar-danger');
                    break;

                case (score > 0 && score <= 2):
                    $bar.html('Weak').css('width', '50%').removeClass().addClass('progress-bar progress-bar-warning');
                    break;

                case (score > 2 && score <= 4):
                    $bar.html('Medium').css('width', '75%').removeClass().addClass('progress-bar progress-bar-info');
                    break;

                case (score > 4):
                    $bar.html('Strong').css('width', '100%').removeClass().addClass('progress-bar progress-bar-success');
                    break;

                default:
                    break;
            }
        }
	})
		
	.on('success.form.fv', function(e, data) {
          
		$('.summary-errors').html(data);
	})

	.on('err.field.fv', function(e, data) {
        $('.summary-errors').show();

        var messages = data.fv.getMessages(data.element);

        $('.summary-errors').find('li[data-field="' + data.field + '"]').remove();

        for (var i in messages) {
            
        $('<li/>')
            .attr('data-field', data.field)
            .wrapInner(
            $('<a/>')
            .attr('href', 'javascript: void(0);')
            .html(messages[i])
            .on('click', function(e) {
                data.element.focus();
            })
            ).appendTo('.summary-errors > ul');
        }
        data.element
        .data('fv.messages')
        .find('.help-block[data-fv-for="' + data.field + '"]')
        .hide();
	})

	.on('success.field.fv', function(e, data) {
        $('.summary-errors > ul').find('li[data-field="' + data.field +
        '"]').remove();
        if ($('#register').data('formValidation').isValid()) {
        $('.summary-errors').hide();
        }
	})

	.on('success.form.fv', function(e, data) {
		e.preventDefault();	
		var url;
		if(save_method == 'add') 
		{
			url = "{$base_url}/serverside/forms/adduser.php";
		}
		else if(save_method == 'update') 
		{
			url = "{$base_url}serverside/forms/edituser.php";
		}
	
        var $form = $(e.target);
		$.ajax({
			type: "POST",
			url: url,
			data: $form.serialize(),
			beforeSend: function() {
				loading.show();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$('#success').html(data);
				$form.formValidation('resetForm', true);
				$('#modal_form').modal('hide');
				reload_table();
				alertify.error('Failed! Submitted Record');
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
});

function toggle_password(target){
    var d = document;
    var tag = d.getElementById(target);
    var tag2 = d.getElementById("showhide");

    if (tag2.innerHTML == '<i class="glyphicon glyphicon-eye-open"></i>')
	{
        tag.setAttribute('type', 'text');   
        tag2.innerHTML = '<i class="glyphicon glyphicon-eye-close"></i>';

    } else {
        tag.setAttribute('type', 'password');   
        tag2.innerHTML = '<i class="glyphicon glyphicon-eye-open"></i>';
    }
}

function new_password(target){
    var d = document;
    var tag = d.getElementById(target);
    var tag2 = d.getElementById("newshowhide");

    if (tag2.innerHTML == '<i class="glyphicon glyphicon-eye-open"></i>')
	{
        tag.setAttribute('type', 'text');   
        tag2.innerHTML = '<i class="glyphicon glyphicon-eye-close"></i>';

    } else {
        tag.setAttribute('type', 'password');
        tag2.innerHTML = '<i class="glyphicon glyphicon-eye-open"></i>';
    }
}

function demoteSubmitted() 
{
	$(".ajs-header").html('{$siteTitle} - Alert Message');
	alertify.confirm('Are you sure? Do you want to DEMOTE ACCOUNT this selected user?',function(){
		$.ajax({
			type: "POST",
			url: "{$base_url}serverside/users/demote.php",
			data: $('#delflag_frm').serialize(),
			success: function(data) {
				$("#success").html(data);
				reload_table();
				$("#demoteSubmit").prop('disabled', true);
				$("#deleteSubmit").prop('disabled', true);
				$("#freezeSubmit").prop('disabled', true);
				$(".chk-box").prop('checked', false);
				$(".select-all").prop('checked', false);
				alertify.success('Successfully Freezed!...');
			},
			error: function(data){
				$("#success").html(data);
				reload_table();
				$("#demoteSubmit").prop('disabled', true);
				$("#deleteSubmit").prop('disabled', true);
				$("#freezeSubmit").prop('disabled', true);
				$(".chk-box").prop('checked', false);
				$(".select-all").prop('checked', false);
				alertify.error('Failed! to Freezed!...');
			}
		});
	},function(){
		alertify.error('Declined');
		$(".chk-box").prop('checked', false);
		$(".select-all").prop('checked', false);
		$("#demoteSubmit").prop('disabled', true);
		$("#deleteSubmit").prop('disabled', true);
		$("#freezeSubmit").prop('disabled', true);
	}).setting('labels',{literal}{'ok':'Accept', 'cancel': 'Decline'}{/literal});
}

function freezeSubmitted() 
{
	$(".ajs-header").html('{$siteTitle} - Alert Message');
	alertify.confirm('Are you sure? Do you want to freeze this selected user?',function(){
		$.ajax({
			type: "POST",
			url: "{$base_url}serverside/freeze/freeze.php",
			data: $('#delflag_frm').serialize(),
			success: function(data) {
				$("#success").html(data);
				reload_table();
				$("#demoteSubmit").prop('disabled', true);
				$("#suspendSubmit").prop('disabled', true);
				$("#deleteSubmit").prop('disabled', true);
				$("#freezeSubmit").prop('disabled', true);
				$(".chk-box").prop('checked', false);
				$(".select-all").prop('checked', false);
				alertify.success('Successfully Freezed!...');
			},
			error: function(data){
				$("#success").html(data);
				reload_table();
				$("#demoteSubmit").prop('disabled', true);
				$("#suspendSubmit").prop('disabled', true);
				$("#deleteSubmit").prop('disabled', true);
				$("#freezeSubmit").prop('disabled', true);
				$(".chk-box").prop('checked', false);
				$(".select-all").prop('checked', false);
				alertify.error('Failed! to Freezed!...');
			}
		});
	},function(){
		alertify.error('Declined');
		$(".chk-box").prop('checked', false);
		$(".select-all").prop('checked', false);
		$("#demoteSubmit").prop('disabled', true);
		$("#suspendSubmit").prop('disabled', true);
		$("#deleteSubmit").prop('disabled', true);
		$("#freezeSubmit").prop('disabled', true);
	}).setting('labels',{literal}{'ok':'Accept', 'cancel': 'Decline'}{/literal});
}

function deleteSubmitted() 
{
	$(".ajs-header").html('{$siteTitle} - Alert Message');
	alertify.confirm('Are you sure? Do you want to delete?',function(){
		$.ajax({
			type: "POST",
			url: "{$base_url}serverside/forms/deleteSubmit.php",
			data: $('#delflag_frm').serialize(),
			success: function(data) {
				$("#success").html(data);
				reload_table();
				$("#demoteSubmit").prop('disabled', true);
				$("#suspendSubmit").prop('disabled', true);
				$("#deleteSubmit").prop('disabled', true);
				$("#freezeSubmit").prop('disabled', true);
				$(".chk-box").prop('checked', false);
				$(".select-all").prop('checked', false);
				alertify.success('Successfully Deleted!...');
			},
			error: function(data){
				$("#success").html(data);
				reload_table();
				$("#demoteSubmit").prop('disabled', true);
				$("#suspendSubmit").prop('disabled', true);
				$("#deleteSubmit").prop('disabled', true);
				$("#freezeSubmit").prop('disabled', true);
				$(".chk-box").prop('checked', false);
				$(".select-all").prop('checked', false);
				alertify.error('Failed! to Delete!...');
			}
		});
	},function(){
		alertify.error('Declined');
		$(".chk-box").prop('checked', false);
		$(".select-all").prop('checked', false);
		$("#demoteSubmit").prop('disabled', true);
		$("#suspendSubmit").prop('disabled', true);
		$("#deleteSubmit").prop('disabled', true);
		$("#freezeSubmit").prop('disabled', true);
	}).setting('labels',{literal}{'ok':'Accept', 'cancel': 'Decline'}{/literal});
}

$('document').ready(function()
{
    $('.select-all').click(function(event) {
        if(this.checked) {
            $('.chk-box').each(function() {
                this.checked = true;
				$("#demoteSubmit").prop('disabled', false);
				$("#deleteSubmit").prop('disabled', false);
				$("#freezeSubmit").prop('disabled', false);
            });
        }else{
            $('.chk-box').each(function() {
                this.checked = false;
            }); 
			if($(".select-all").prop('checked') == false){
				$("#demoteSubmit").prop('disabled', true);
				$("#deleteSubmit").prop('disabled', true);
				$("#freezeSubmit").prop('disabled', true);
			}
        }
    });
	
	if($(".select-all").prop('checked') == false){
		$("#demoteSubmit").prop('disabled', true);
		$("#deleteSubmit").prop('disabled', true);
		$("#freezeSubmit").prop('disabled', true);
	}
	
	$('body').delegate('.chk-box','click',function(event){
		if ($('.chk-box').is(':checked') == true){
			$("#demoteSubmit").prop('disabled', false);
			$("#deleteSubmit").prop('disabled', false);
			$("#freezeSubmit").prop('disabled', false);
		} else {
			$("#demoteSubmit").prop('disabled', true);
			$("#deleteSubmit").prop('disabled', true);
			$("#freezeSubmit").prop('disabled', true);
		}	
	});
});
</script>
{include file='apps/liveclock.tpl'}
</body>
</html>