<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="title" content="{$siteTitle} - Users">
<meta name="description" content="Fast, Stable and Secure VPN Service">
<meta name="keywords" content="{$siteTitle}">
<meta name="author" content="Lenz Scott Kennedy">
<meta name="owner" content="Firenet Philippines">
<meta name="copyright" content="{$siteTitle}">
<title>{$siteTitle} - Users</title>
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
				All Users
			</h1>
			<ol class="breadcrumb">
				<li><a href="{$base_url}"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active">All Users</li>
			</ol>
		</section>
		<!-- Main content -->
		<section class="content">
			<!-- Default box -->
			<div class="box">
				<div class="box-body">
					<div class="col-md-12">
						<fieldset class="padding-20">
							<div id="success"></div>
							<legend class="text-center bg-purple">
								<h4 class="text-white">
									<div class="panel-heading">
										User List
									</div>
								</h4>
							</legend>
							<form id="delflag_frm" name="delflag_frm">
								<input type="hidden" id="submitted" name="submitted" value="Suspend | Delete Submitted">
								<div class="btn-group m-r-10" role="group">
                                <div class="dropdown" role="group">
                                    <button type="button" class="btn btn-outline btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"> <span class="caret"></span> <i class="fa fa-fw fa-list" aria-hidden="true"></i> Users Menu <span> <i class="fa fa-caret-down" aria-hidden="true"></i></span></button>
                                    <ul class="dropdown-menu animated flipInX" role="menu">
                                        <li>
                                            <button type="button" class="btn btn-success btn-block waves-effect waves-light text-left" onclick="add_user()" title="Create Account">
					                            <i class="icon wb-user-add" aria-hidden="true"></i> Create Account
				                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="btn btn-primary btn-block waves-effect waves-light text-left" onclick="instantCreate()" title="Generate Account">
					                           <i class="icon wb-users" aria-hidden="true"></i> Generate Trial
				                            </button>
                                        </li>
                                        {if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller'}
                                        <li>
                                            <button type="button" class="btn btn-danger btn-block waves-effect waves-light text-left" id="inactiveButton"  onclick="inactiveSubmitted()" title="Delete In-Active Account">
					                            <i class="icon wb-users" aria-hidden="true"></i> Delete InActive (<span id="inactive"></span> )
				                            </button>
                                        </li>
                                        {/if}
                                        <li>
                                            <button type="button" class="btn btn-warning btn-block waves-effect waves-light text-left" id="suspendSubmit" name="suspendSubmit" onclick="suspendSubmitted()"  title="Suspend Account">
					                           <i class="icon wb-warning" aria-hidden="true"></i> Suspend Account
				                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="btn btn-info btn-block waves-effect waves-light text-left" id="freezeSubmit" name="freezeSubmit" onclick="freezeSubmitted()"  title="Freeze Account">
					                           <i class="icon wb-stop" aria-hidden="true"></i> Freeze Account
				                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="btn btn-danger btn-block waves-effect waves-light text-left" id="deleteSubmit" name="deleteSubmit" onclick="deleteSubmitted()" title="Delete Account">
					                            <i class="icon wb-trash" aria-hidden="true"></i> Delete Account
				                            </button>
                                        </li>
                                    </ul>
                                </div>
                                </div>
								<table id="users-serverside" class="table table-striped table-bordered" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th class="text-center"><input type="checkbox" class="select-all" /></th>
											<th class="text-center">Username</th>
											<th class="text-center">Duration</th>
											<th class="text-center">Credits</th>
											<th class="text-center">Role</th>
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
					
					<div class="col-md-12 padding-top-20">
						<div id="success2"></div>
						<fieldset class="padding-20">
							<legend class="text-center bg-purple">
								<h4 class="text-white">
									<div class="panel-heading">
										Suspend User List
									</div>
								</h4>
							</legend>
							
							<form method="post" id="frm" name="frm">
								<input type="hidden" id="submitted" name="submitted" value="Unsuspend | Delete Submitted">
								<div class="btn-group m-r-10" role="group">
                                <div class="dropdown" role="group">
                                    <button type="button" class="btn btn-outline btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"> <span class="caret"></span> <i class="fa fa-fw fa-list" aria-hidden="true"></i> Users Menu <span> <i class="fa fa-caret-down" aria-hidden="true"></i></span></button>
                                    <ul class="dropdown-menu animated flipInX" role="menu">
                                        <li>
                                            <button type="button" class="btn btn-danger btn-block waves-effect waves-light text-left" id="deleteSubmit2" name="deleteSubmit2" onclick="deleteSubmitted2()" title="Delete Account">
					                           <i class="icon wb-trash" aria-hidden="true"></i> Delete Account
				                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="btn btn-success btn-block waves-effect waves-light text-left" id="suspendRecovery" name="suspendRecovery" onclick="suspendRecoveries()" title="Unsuspend Account">
					                           <i class="icon wb-warning" aria-hidden="true"></i> Unsuspend Account
				                            </button>
                                        </li>
                                    </ul>
                                </div>
                                </div>
								<table id="suspended-logs" class="table table-striped table-bordered display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th class="text-center"><input type="checkbox" class="select-alls" /></th>
											<th class="text-center">Username</th>
											<th class="text-center">Offense</th>
											<th class="text-center">Suspended Date</th>
											<th class="text-center">Time Elapsed</th>
										</tr>
									</thead>
										<tbody class="text-center">
									</tbody>
								</table>
							</form>
						</fieldset>
					</div>

					<div class="col-md-12 padding-top-20">
						<div id="success3"></div>
						<fieldset class="padding-20">
							<legend class="text-center bg-purple">
								<h4 class="text-white">
									<div class="panel-heading">
										Freezed User List
									</div>
								</h4>
							</legend>
							
							<table id="freeze-logs" class="table table-striped table-bordered display" cellspacing="0" width="100%">
								<thead>
									<tr>
										<th class="text-center">Username</th>
										<th class="text-center">Duration</th>
										<th class="text-center">Credits</th>
										<th class="text-center">Role</th>
										<th class="text-center">Last Freeze Date</th>
										<th class="text-center">Controls</th>
									</tr>
								</thead>
									<tbody class="text-center">
								</tbody>
							</table>
						</fieldset>
					</div>
					
					<div class="col-md-12 padding-top-20">
						<fieldset class="padding-20">
							<legend class="text-center bg-purple">
								<h4 class="text-white">
									<div class="panel-heading">
										Duration History
									</div>
								</h4>
							</legend>
							<table id="reloadduration_logs" class="table table-striped table-bordered display" cellspacing="0" width="100%">
								<thead>
									<tr>
										<th class="text-center">Reloaded Name</th>
										<th class="text-center">Reloaded Item</th>
										<th class="text-center">Category</th>
										<th class="text-center">Date</th>
									</tr>
								</thead>
									<tbody class="text-center">
								</tbody>
							</table>
						</fieldset>
					</div>
					
					<div class="col-md-12 padding-top-20">
						<fieldset class="padding-20">
							<legend class="text-center bg-purple">
								<h4 class="text-white">
									<div class="panel-heading">
										<span class="capitalize">
											History 
											{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller'}
											Client
											{else}
											{/if} 
											Vourcher's
										</span>
									</div>
								</h4>
							</legend>
							<table id="client_used" class="table table-striped table-bordered display" cellspacing="0" width="100%">
								<thead>
									<tr>
										<th class="text-center">Voucher code</th>
										<th class="text-center">
										{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller'}
											Username
										{else}
											Generated By:
										{/if}
										</th>
										<th class="text-center">Qty</th>
										<th class="text-center">Category</th>
										<th class="text-center">Permission</th>
										<th class="text-center">Date</th>
									</tr>
								</thead>
								<tbody class="text-center">
								</tbody>
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
												<label class="control-label" for="user_name"><i class="glyphicon glyphicon-user"></i> Usuario:</label>	
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
												<label class="control-label" for="client_type"><i class="icon wb-users"></i> Tipo de cliente:</label>
												<select class="form-control" id="client_type" name="client_type" title="Tipo de cliente">
													<option value="{$premium_encrypt}">Premium Client</option>
													<option value="{$vip_encrypt}">VIP Client</option>
													<option value="{$private_encrypt}">Cliente Private</option>
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
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator'}
													<option value="2">Sub Reseller</option>
													{/if}
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator'}
													<option value="3">Reseller</option>
													{/if} 
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin'  || $user_level_2 == 'administrator'}
													<option value="5">Sub-Administrator</option>
													{/if}
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin'}
													<option value="4">administrator</option>
													
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
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'reseller' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator'}
													<option value="2">Sub Reseller</option>
													{/if}
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'subadmin' || $user_level_2 == 'administrator'}
													<option value="3">Reseller</option>
													{/if}
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin'  || $user_level_2 == 'administrator'}
													<option value="5">Sub-Administrator</option>
													{/if}
													{if $user_id_2 == 1 || $user_level_2 == 'superadmin'}
													<option value="4">administrator</option>
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
												<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
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
										<!--tr>
											<td colspan="2" class="text-center">Shadowsocks Account</td>
										</tr>
										<tr>
											<td><label for="ssport"><i class="glyphicon glyphicon-user"></i> SS Port: </label></td>
											<td><div id="ssport"></div></td>
										</tr>
										<tr>
											<td><label for="sspass"><i class="glyphicon glyphicon-user"></i> SS Password: </label></td>
											<td><div id="sspass"></div></td>
										</tr-->
										
										<tr>
											<td><label for="fullname"><i class="glyphicon glyphicon-user"></i> Full Name: </label></td>
											<td><div id="fullname"></div></td>
										</tr>
										<tr>
											<td><label for="username"><i class="glyphicon glyphicon-user"></i> Usuario: </label></td>
											<td><div id="username"></div></td>
										</tr>
										<tr>
											<td><label for="password"><i class="glyphicon glyphicon-lock"></i> Password: </label></td>
											<td><div id="password"></div></td>
										</tr>
										<tr>
											<td><label for="emailadd"><i class="glyphicon glyphicon-envelope"></i> Email Address: </label></td>
											<td><div id="emailadd"></div></td>
										</tr>
										<!--tr>
											<td><label for="premiumstatus"><i class="glyphicon glyphicon-stats"></i> Premium Status: </label></td>
											<td><div id="premiumstatus"></div></td>
										</tr-->
										<tr>
											<td><label for="premiumduration"><i class="glyphicon glyphicon-time"></i> Premium Expiration: </label></td>
											<td><div id="premiumduration"></div></td>
										</tr>
										<tr>
											<td><label for="vipduration"><i class="glyphicon glyphicon-time"></i> VIP Expiration: </label></td>
											<td><div id="vipduration"></div></td>
										</tr>
										<tr>
											<td><label for="privateduration"><i class="glyphicon glyphicon-time"></i> Private Expiration: </label></td>
											<td><div id="privateduration"></div></td>
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
					<div class="modal fade" id="instant_form" tabindex="-1" role="dialog" aria-labelledby="instant_form" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h3 class="modal-title"></h3>
								</div>
								<div class="modal-body">
									<form id="formUsers" name="formUsers" method="post" class="form-horizontal padding-20">
										<div class="form-group input-group">
											<label class="input-group-addon control-label" for="add_users"><i class="icon wb-users"></i></label>
											<input class="form-control" type="text" id="add_users" name="add_users" max="4" min="1" placeholder="Number of users">
										</div>
										<div class="form-group input-group">
											<label class="input-group-addon control-label" for="generate_type"><i class="icon wb-users"></i></label>
											<select class="form-control" id="generate_type" name="generate_type" title="Tipo de cliente">
												<option value="{$premium_encrypt}" selected="selected">Premium Client</option>
												<option value="{$vip_encrypt}">VIP Client</option>
												<option value="{$private_encrypt}">Cliente Private</option>
											</select>
										</div>
										<input type="hidden" class="form-control" id="submitted" name="submitted" value="Generate Account">
										<div class="control-group form-group">
											<div class="modal-footer">
												<button type="submit" id="submit" name="submit" class="btn btn-primary">Guardar</button>
												<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
												<div id="generate_loader"></div>
											</div>
										</div>
									</form>
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
														<button type="submit" id="submitReseller" name="submitReseller" class="btn btn-primary">Guardar</button>
														<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
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
					<div class="modal fade" id="voucher_form" tabindex="-1" role="dialog" aria-labelledby="voucher_form" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h3 class="modal-title">Voucher Reload</h3>
								</div>
								<div class="modal-body">
									<form id="formVouchers" name="formVouchers" method="post" class="form-horizontal padding-20">
										<input type="hidden" id="submitted" name="submitted" value="Generate Voucher">
										<div class="form-group input-group">
											<label class="input-group-addon control-label" for="qty"><i class="glyphicon glyphicon-barcode"></i> Qty:</label>
											<input type="text" class="form-control" id="qty" name="qty" min="1" 
											{if $user_level_2 == 'subadmin' || $user_level_2 == 'administrator' || $user_level_2 == 'reseller' || $user_level_2 == 'subreseller'}
											max="{$credits_2}"
										{/if} autocomplete="off" onkeypress="return IsNumeric(event);"
											ondrop="return false;" onpaste="return false;" value="1" required>
										</div>
					
										<div class="form-group input-group">
											<label class="input-group-addon control-label" for="category"><i class="glyphicon glyphicon-list"></i>: </label>
											<select class="form-control credits" id="category" name="category">
												<option value="{$premium_encrypt}">Premium</option>
												<option value="{$vip_encrypt}">VIP</option>
												<option value="{$private_encrypt}">Private</option>
											</select>
										</div>
										<div class="control-group form-group">
											<div class="modal-footer">
												<button type="submit" id="submit" name="submit" class="btn btn-primary">Guardar</button>
												<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
												<input type="hidden" class="form-control" id="voucher_code" name="voucher_code">
												<input type="hidden" class="form-control" id="voucher_secret" name="voucher_secret">
												<div id="vouchers_loader"></div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					<!-- End Bootstrap modal -->
					<div class="modal fade" id="duration_form" tabindex="-1" role="dialog" aria-labelledby="duration_form" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h3 class="modal-title"></h3>
								</div>
								<div class="modal-body">
									<form id="formDuration" name="formDuration">
									<input type="hidden" id="submitted" name="submitted" value="Reload Durations">
									<div class="form-group">
										<label class="control-label" for="duration">
											<i class="glyphicon glyphicon-time"></i> Duracion:
										</label>
										<select id="duration" name="duration" class="form-control">
										<option value="">-- Elegir duracion --</option>
											{foreach from=$duration key=id item=i}
											{$i}
											{/foreach}
										</select>
									</div>
									<div class="form-group input-group">
										<label class="input-group-addon control-label" for="category"><i class="glyphicon glyphicon-list"></i>: </label>
										<select class="form-control category" id="category" name="category">
											<option value="{$premium_encrypt}">Duracion Premium</option>
											<option value="{$vip_encrypt}">Duracion VIP</option>
											<option value="{$private_encrypt}">Duracion Private</option> 
										</select>
									</div>
									<div class="control-group form-group">
										<div class="modal-footer">
											<button type="submit" id="submit" name="submit" class="btn btn-success btn-block">Aplicar duracion</button>
											<button type="button" class="btn btn-danger btn-block" data-dismiss="modal">Cancelar</button>
											<input type="hidden" class="form-control" id="duration_code" name="duration_code">
											<input type="hidden" class="form-control" id="duration_secret" name="duration_secret">
											<div id="duration_loader"></div>
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
	alertify.defaults.glossary.title = 'BoasorteVPN';
function closed(){
		setTimeout(function () { $('.closedBtn').trigger('click'); }, 10000);
	}
</script>
{include file='js/datatables_js.tpl'}
<script>

var loading = $('#loading');
var save_method;
var table;
var suspended_table;
var freeze_table;
var reloadduration_tbl;
var clientused_tbl;

function reload_table()
{
	table.fnReloadAjax(null,false);
	suspended_table.fnReloadAjax(null,false);
	freeze_table.fnReloadAjax(null,false);
	reloadduration_tbl.fnReloadAjax(null, false);
	clientused_tbl.fnReloadAjax(null, false);
}

$('document').ready(function()
{
	table = $('#users-serverside').dataTable({
		responsive: true,
        "bProcessing": true,
        "bServerSide": true,
        "bStateSave": true,
        "ajax": {
            "url": "/users-serverside",
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
	
	suspended_table = $('#suspended-logs').dataTable({
		responsive: true,
        "bProcessing": true,
        "bServerSide": true,
        "bStateSave": true,
        "ajax": {
            "url": "/suspended-serverside",
            "type": "POST"
        },
		"aoColumnDefs": [{
			'bSortable': false,
			'aTargets': [0,-1]
		}],
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
	
	freeze_table = $('#freeze-logs').dataTable({
		responsive: true,
        "bProcessing": true,
        "bServerSide": true,
        "bStateSave": true,
        "ajax": {
            "url": "/freeze-serverside",
            "type": "POST"
        },
		"aoColumnDefs": [{
			'bSortable': false,
			'aTargets': [0,-1]
		}],
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
			"sInfo":'Showing _START_ to _END_ of _TOTAL_ entries',
			"sZeroRecords": "No matching records found"
		}
	});

	reloadduration_tbl = $('#reloadduration_logs').dataTable({
		responsive: true,
		"bProcessing": true,
		"bServerSide": true,
		"bStateSave": true,
		"ajax": {
			"url": "{$base_url}serverside/duration/logs/reloadduration.php",
			"type": "POST"
		},
		order: [[ 0, 'desc' ]],
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

	clientused_tbl = $('#client_used').dataTable({
		responsive: true,
        "bProcessing": true,
        "bServerSide": true,
        "bStateSave": true,
        "ajax": {
            "url": "{$base_url}serverside/voucher/logs/voucher_used.php",
            "type": "POST"
        },
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

function displayInActive() {
	var color_data;
	$.ajax({
		type: "POST",
			url: "{$base_url}serverside/users/get_inactive.php",
			data: $('#inactive').serialize(),
			success: function(data) {
			if(data == 0){
				$("#inactive").html(data).css('color', 'white');
				$("#inactiveButton").prop('disabled', true);
			}else{
				$("#inactive").html(data).css('color', 'yellow');
			}
		}
	});
}
displayInActive();

function getDuration(u,n)
{
	$('#formDuration')[0].reset();
	$('.form-group').removeClass('has-error');
	$('#formDuration').formValidation('resetForm', true);
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
				$('#duration_secret').val(data.secret);
				$('#duration_code').val(data.code);
				$('#get_user').html(data.user_name);
				$('#get_credits').html(data.credits);
				$('#duration_form').modal('show');
				$('.modal-title').text('Recargar duracion | Usuario: '+data.user_name);
				$('#conv2').html('');
				displayConv2();
			}
			if(data.response == 2)
			{
				swal("BoasorteVPN", "No tienes acceso a este upline!", "warning");
			}
			if(data.response == 0){
				swal("BoasorteVPN", "Autorizacion fallida!", "danger");
			}
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            swal("BoasorteVPN", "Error al obtener datos por Ajax!", "info");
        }
    });
}

$(document).ready(function($){
	$('#formDuration').formValidation
	({
		framework: 'bootstrap',
		excluded: ':disabled',
		icon: null,
		fields: 
		{
			duration:
			{
				valid: true,
				message: 'La duracion no es valida',
				validators:
				{
					notEmpty:
					{
						message: 'La duracion es obligatoria'
					},
				}
			}
		}
	})
	.on('success.form.fv', function(e, data) {
		e.preventDefault();
		var duration_loader = $('#duration_loader');
		var $form = $(e.target);
		alertify.confirm('Deseas ampliar la duracion de este usuario?',function(e)
		{
			if(e)
			{
				$.ajax({
					url: "{$base_url}serverside/duration/reload_duration.php",
					type: "POST",
					data: $form.serialize(),
					dataType: "JSON",
					cache: false,
					beforeSend: function() {
						duration_loader.html('Espera por favor... procesando datos...');
					},
					complete: function(){
						duration_loader.html('')
					},
					success: function(data){
						if(data.response == 1)
						{
							$('#success').html(data.message);
							$('#duration_form').modal('hide');
							$form.formValidation('resetForm', true);
							reload_table();
							closed();
							displayInActive();
							swal({
							  position: 'center',
							  type: 'success',
							  title: 'Duracion aplicada',
							  showConfirmButton: false,
							  timer: 1500
							})
						}
						if(data.response == 0){
							$('#success').html(data.message);
							swal({
							  position: 'center',
							  type: 'error',
							  title: 'No se pudo aplicar la duracion',
							  showConfirmButton: false,
							  timer: 1500
							})
						}
					}
				});
			}
		},function(){
			alertify.error('Cancelado');
			reload_table();
			closed();
			displayInActive();
		});
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
				$('.modal-title').text(data.mycredits+' | Usuario: '+data.user_name);
			}
			if(data.response == 2)
			{
				swal("BoasorteVPN", "No tienes acceso a este upline!", "warning");
			}
			if(data.response == 0){
				swal("BoasorteVPN", "Autorizacion fallida!", "danger");
			}
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            swal("BoasorteVPN", "Error al obtener datos por Ajax!", "info");
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
				message: 'La cantidad no es valida',
				validators:
				{
					notEmpty:
					{
						message: 'La cantidad es obligatoria'
					},
                    stringLength: 
					{
                        min: 1,
                        message: 'La cantidad debe ser mayor a 0'
                    },
                    regexp: 
					{
						regexp: /^[0-9\.]+$/,
						message: 'La cantidad solo puede contener numeros'
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
						vouchers_loader.html('Espera por favor... procesando datos...');
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
						displayInActive();
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
						displayInActive();
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
						swal("BoasorteVPN", "No tienes acceso a este upline!", "warning");
					}
				}
				});
			}
		},function(){
			alertify.error('Cancelado');
			reload_table();
			closed();
			displayInActive();
		});
	});
});

function getVoucher(u,n)
{
	$('#formVouchers')[0].reset();
	$('.form-group').removeClass('has-error');
	$('#formVouchers').formValidation('resetForm', true);
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
				$('#voucher_secret').val(data.secret);
				$('#voucher_code').val(data.code);
				$('#voucher_form').modal('show');
				$('.modal-title').text(data.mycredits+' | Usuario: '+data.user_name);
			}
			if(data.response == 2)
			{
				swal("BoasorteVPN", "No tienes acceso a este upline!", "warning");
			}
			if(data.response == 0){
				swal("BoasorteVPN", "Autorizacion fallida!", "danger");
			}
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            swal("BoasorteVPN", "Error al obtener datos por Ajax!", "info");
        }
    });
}

$(document).ready(function($){
	$('#formVouchers').formValidation
	({
		framework: 'bootstrap',
		excluded: ':disabled',
		icon: null,
		fields: 
		{
			duration:
			{
				valid: true,
				message: 'La duracion no es valida',
				validators:
				{
					notEmpty:
					{
						message: 'La duracion es obligatoria'
					},
				}
			}
		}
	})
	.on('success.form.fv', function(e, data) {
		e.preventDefault();
		var duration_loader = $('#duration_loader');
		var $form = $(e.target);
		alertify.confirm('Are you sure? Do you want to Reload a Voucher for this user?',function(e)
		{
			if(e)
			{
				$.ajax({
					url: "{$base_url}serverside/voucher/reload_voucher.php",
					type: "POST",
					data: $form.serialize(),
					cache: false,
					dataType: "JSON",
					beforeSend: function() {
						duration_loader.html('Espera por favor... procesando datos...');
					},
					complete: function(){
						duration_loader.html('');
					},
					success: function(data){
					if(data.response == 1)
					{
						$('#voucher_form').modal('hide');
						$form.formValidation('resetForm', true);
						reload_table();
						closed();
						displayInActive();
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
						displayInActive();
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
						$('#voucher_form').modal('hide');
						reload_table();
						closed();
						displayInActive();
						swal("BoasorteVPN", "Oh Oh! You dont have Authorization!", "warning");
					}
				}
				});
			}
		},function(){
			alertify.error('Cancelado');
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
			$('#emailadd').html(data.email);
			$('#premiumduration').html(data.premiumdate);
			$('#vipduration').html(data.vipdate);
			$('#privateduration').html(data.privatedate);
			$('#status').html(status);
			$('#ipaddress').html(data.ipaddress);
			$('#password').html(data.password);
			$('#regdate').html(data.regdate);
			$('#fullname').html(data.fullname);
			$('#username').html(data.username);
			$('#ssport').html(data.ssport);
			$('#sspass').html(data.sspass);
            $('#view_modal').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('ACCOUNT INFO: ' +data.username); // Set title to Bootstrap modal title
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function unfreezed(u,n)
{
	alertify.confirm('Are you sure? Do you want to Unfreezed this User?',function(e)
	{
		if(e)
		{
			$.ajax({
				type: "POST",
				url: "{$base_url}serverside/freeze/unfreeze.php",
				data: "uid="+u+"&ucode="+n,
				dataType: "JSON",
				cache: false,
				success: function(data) 
				{
					if(data.response == 1)
					{
						alertify.success('Successfully Unfreezed!');
					}else{
						alertify.error("Failed to Unfreezed, please try again later!");
					}
					reload_table();
					closed();
					displayInActive();
				},
				error: function(data){
					$("#success").html(data.error);
					reload_table();
					closed();
					displayInActive();
				}
			});
		}
	},function(){
		alertify.error('Cancelado');
	});
}

function inactiveSubmitted()
{
	alertify.confirm('Are you sure? Do you want to delete all in-active user?',function(e)
	{
		if(e)
		{
			$.ajax({
				type: "POST",
				url: "{$base_url}serverside/delete/inactive.php",
				dataType: "JSON",
				cache: false,
				success: function(data)
				{
					if(data.response == 1)
					{
						alertify.success('Successfully Inactive Users Deleted!');
						$('#success').html(data.success);
						reload_table();
						closed();
						displayInActive();
					}else{
						alertify.error('Failed to Delete!');
						$('#success').html(data.error);
						reload_table();
						closed();
						displayInActive();
					}
				}
			});
		}
	},function(){
		alertify.error('Cancelado');
		reload_table();
		closed();
		displayInActive();
	});
}

function instantCreate(){
	$('#formUsers')[0].reset();
	$('.form-group').removeClass('has-error');
	$('#formUsers').formValidation('resetForm', true);
	$('#instant_form').modal('show');
	$('.modal-title').text('Instant Generate Trial Accounts');
}

$(document).ready(function($){
	$('#formUsers').formValidation({
        framework: 'bootstrap',
		icon: null,
		fields: 
		{
			add_users:
			{
				valid: true,
				message: 'The users count is not valid',
				validators: 
				{
                    notEmpty:
					{
						message: 'The users count is required and can\'t be empty'
					},
                    stringLength: 
					{
                        min: 1,
                        message: 'The users count must be more than 1'
                    },
                    regexp: 
					{
						regexp: /^[0-9\.]+$/,
						message: 'The users count can only consist of numeric number'
                    }
				}
			}
		}
	})
	.on('success.form.fv', function(e, data){
		e.preventDefault();	
		var $generate_form = $(e.target);
		var generate_loader = $('#generate_loader');
		$.ajax({
			type: "POST",
			url:"{$base_url}/serverside/users/generate_accounts.php",
			data: $generate_form.serialize(),
			cache: false,
			beforeSend: function() {
				generate_loader.html('Please! Wait... While Generating Trial Account');
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$('#success').html(data);
				$('#instant_form').modal('hide');
				$generate_form.formValidation('resetForm', true);
				reload_table();
				closed();
				displayInActive();
				alertify.error('Failed! to Generate Trial Accounts!');
			},
			success: function(data){
				$('#success').html(data);
				$('#instant_form').modal('hide');
				$generate_form.formValidation('resetForm', true);
				reload_table();
				closed();
				displayInActive();
				alertify.success('Successfully Generated Trial Accounts!');
			},
			complete: function(){
				generate_loader.html('');
			}
		});
	});
});

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

function suspendSubmitted()
{
	$(".ajs-header").html('{$siteTitle} - Alert Message');
	alertify.confirm('Are you sure? Do you want to suspend the checked users?',function(){
		$.ajax({
			type: "POST",
			url: "{$base_url}serverside/forms/suspendSubmit.php",
			data: $('#delflag_frm').serialize(),
			success: function(data) {
				$("#success").html(data);
				reload_table();
				$("#suspendSubmit").prop('disabled', true);
				$("#deleteSubmit").prop('disabled', true);
				$("#freezeSubmit").prop('disabled', true);
				$(".chk-box").prop('checked', false);
				$(".select-all").prop('checked', false);
				alertify.success('Successfully Suspended!...');
			},
			error: function(data){
				$("#success").html(data);
				reload_table();
				$("#suspendSubmit").prop('disabled', true);
				$("#deleteSubmit").prop('disabled', true);
				$("#freezeSubmit").prop('disabled', true);
				$(".chk-box").prop('checked', false);
				$(".select-all").prop('checked', false);
				alertify.error('Failed! to Suspend!...');
			}
		});
	},function(){
		alertify.error('Cancelado');
		$(".chk-box").prop('checked', false);
		$(".select-all").prop('checked', false);
		$("#suspendSubmit").prop('disabled', true);
		$("#deleteSubmit").prop('disabled', true);
		$("#freezeSubmit").prop('disabled', true);
	}).setting('labels',{literal}{'ok':'Aceptar', 'cancel': 'Cancelar'}{/literal});
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
				$("#suspendSubmit").prop('disabled', true);
				$("#deleteSubmit").prop('disabled', true);
				$("#freezeSubmit").prop('disabled', true);
				$(".chk-box").prop('checked', false);
				$(".select-all").prop('checked', false);
				alertify.error('Failed! to Delete!...');
			}
		});
	},function(){
		alertify.error('Cancelado');
		$(".chk-box").prop('checked', false);
		$(".select-all").prop('checked', false);
		$("#suspendSubmit").prop('disabled', true);
		$("#deleteSubmit").prop('disabled', true);
		$("#freezeSubmit").prop('disabled', true);
	}).setting('labels',{literal}{'ok':'Aceptar', 'cancel': 'Cancelar'}{/literal});
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
				$("#suspendSubmit").prop('disabled', true);
				$("#deleteSubmit").prop('disabled', true);
				$("#freezeSubmit").prop('disabled', true);
				$(".chk-box").prop('checked', false);
				$(".select-all").prop('checked', false);
				alertify.error('Failed! to Freezed!...');
			}
		});
	},function(){
		alertify.error('Cancelado');
		$(".chk-box").prop('checked', false);
		$(".select-all").prop('checked', false);
		$("#suspendSubmit").prop('disabled', true);
		$("#deleteSubmit").prop('disabled', true);
		$("#freezeSubmit").prop('disabled', true);
	}).setting('labels',{literal}{'ok':'Aceptar', 'cancel': 'Cancelar'}{/literal});
}

function suspendRecoveries() 
{
	$(".ajs-header").html('{$siteTitle} - Alert Message');
	alertify.confirm('Are you sure? Do you want to unsuspend the checked users?',function(){
		$.ajax({
			type: "POST",
			url: "{$base_url}serverside/forms/suspendRecovery.php",
			data: $('#frm').serialize(),
			success: function(data) {
				$("#success2").html(data);
				reload_table();
				$("#suspendRecovery").prop('disabled', true);
				$("#deleteSubmit2").prop('disabled', true);
				$(".chk-boxs").prop('checked', false);
				$(".select-alls").prop('checked', false);
				alertify.success('Successfully Unsuspended!...');
			},
			error: function(data){
				$("#success2").html(data);
				reload_table();
				$("#suspendRecovery").prop('disabled', true);
				$("#deleteSubmit2").prop('disabled', true);
				$(".chk-boxs").prop('checked', false);
				$(".select-alls").prop('checked', false);
				alertify.error('Failed to Unsuspend!...');
			}
		});
	},function(){
		alertify.error('Cancelado');
		$(".chk-boxs").prop('checked', false);
		$(".select-alls").prop('checked', false);
		$("#suspendRecovery").prop('disabled', true);
		$("#deleteSubmit2").prop('disabled', true);
	}).setting('labels',{literal}{'ok':'Aceptar', 'cancel': 'Cancelar'}{/literal});
}

function deleteSubmitted2() 
{
	$(".ajs-header").html('{$siteTitle} - Alert Message');
	alertify.confirm('Are you sure? Do you want to delete?',function(){
		$.ajax({
			type: "POST",
			url: "{$base_url}serverside/forms/deleteSubmit.php",
			data: $('#frm').serialize(),
			success: function(data) {
				$("#success").html(data);
				reload_table();
				$("#suspendRecovery").prop('disabled', true);
				$("#deleteSubmit2").prop('disabled', true);
				$(".chk-boxs").prop('checked', false);
				$(".select-alls").prop('checked', false);
				alertify.success('Successfully Deleted!...');
			},
			error: function(data){
				$("#success").html(data);
				reload_table();
				$("#suspendRecovery").prop('disabled', true);
				$("#deleteSubmit2").prop('disabled', true);
				$(".chk-boxs").prop('checked', false);
				$(".select-alls").prop('checked', false);
				alertify.error('Failed to Delete!...');
			}
		});
	},function(){
		alertify.error('Cancelado');
		$(".chk-boxs").prop('checked', false);
		$(".select-alls").prop('checked', false);
		$("#suspendRecovery").prop('disabled', true);
		$("#deleteSubmit2").prop('disabled', true);
	}).setting('labels',{literal}{'ok':'Aceptar', 'cancel': 'Cancelar'}{/literal});
}

$('document').ready(function()
{
    $('.select-all').click(function(event) {
        if(this.checked) {
            $('.chk-box').each(function() {
                this.checked = true;
				$("#suspendSubmit").prop('disabled', false);
				$("#deleteSubmit").prop('disabled', false);
				$("#freezeSubmit").prop('disabled', false);
            });
        }else{
            $('.chk-box').each(function() {
                this.checked = false;
            }); 
			if($(".select-all").prop('checked') == false){
				$("#suspendSubmit").prop('disabled', true);
				$("#deleteSubmit").prop('disabled', true);
				$("#freezeSubmit").prop('disabled', true);
			}
        }
    });
	
	if($(".select-all").prop('checked') == false){
		$("#suspendSubmit").prop('disabled', true);
		$("#deleteSubmit").prop('disabled', true);
		$("#freezeSubmit").prop('disabled', true);
	}
	
	$('body').delegate('.chk-box','click',function(event){
		if ($('.chk-box').is(':checked') == true){
			$("#suspendSubmit").prop('disabled', false);
			$("#deleteSubmit").prop('disabled', false);
			$("#freezeSubmit").prop('disabled', false);
		} else {
			$("#suspendSubmit").prop('disabled', true);
			$("#deleteSubmit").prop('disabled', true);
			$("#freezeSubmit").prop('disabled', true);
		}	
	});
	
    $('.select-alls').click(function(event) {
        if(this.checked) {
            $('.chk-boxs').each(function() {
                this.checked = true;
				$("#suspendRecovery").prop('disabled', false);
				$("#deleteSubmit2").prop('disabled', false);
            });
        }else{
            $('.chk-boxs').each(function() {
                this.checked = false;
            }); 
			if($(".select-alls").prop('checked') == false){
				$("#suspendRecovery").prop('disabled', true);
				$("#deleteSubmit2").prop('disabled', true);
			}			
        }
    });
	
	if($(".select-alls").prop('checked') == false){
		$("#suspendRecovery").prop('disabled', true);
		$("#deleteSubmit2").prop('disabled', true);
	}
	
	$('body').delegate('.chk-boxs','click',function(event){
		if ($('.chk-boxs').is(':checked') == true){
			$("#suspendRecovery").prop('disabled', false);
			$("#deleteSubmit2").prop('disabled', false);
		} else {
			$("#suspendRecovery").prop('disabled', true);
			$("#deleteSubmit2").prop('disabled', true);
		}	
	});
});
</script>
{include file='apps/liveclock.tpl'}
</body>
</html>