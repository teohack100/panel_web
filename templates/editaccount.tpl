<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="title" content="{$siteTitle}">
<meta name="description" content="{$siteTitle} edit account">
<meta name="keywords" content="{$siteTitle} edit account">
<meta name="author" content="Jhoe Angeleye">
<meta name="owner" content="{$siteTitle}">
<meta name="copyright" content="Jhoe Angeleye">
<title>Edit Account</title>
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
</head>
<body class="hold-transition skin-main sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">

{include file='apps/navigation.tpl'}
{include file='apps/sidebar.tpl'}

	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>
				All Client
			</h1>
			<ol class="breadcrumb">
				<li><a href="{$base_url}"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active">client</li>
			</ol>
		</section>

		<!-- Main content -->
		<section class="content">
			<!-- Default box -->
			<div class="box">
				<div class="box-body">
					<div class="col-md-6">
						<fieldset class="padding-10">
							<div id="success"></div>
							<legend class="text-center text-white">
								<h4 class="text-white"> Information </h4>
							</legend>
							<div class="widget widget-shadow text-center">
								<div class="widget-header">
									<div class="widget-header-content">
										<a class="avatar avatar-lg" href="javascript:void(0)">
											<span id="img">{$profile_image}</span>
										</a>
										<div class="profile-user">{$username_2}</div>
										<div class="profile-job"><p class="text-red">{$user_level_2}</p></div>
									</div>
								</div>
								<div class="widget-footer">
									<p class="text-left">
										<i class="fa fa-envelope-o"></i> 
										<abbr title="Email Address">E</abbr>: <span id="email_2">{$email_2}</span>
									</p>
									<p class="text-left">
										<i class="fa fa-phone"></i> 
										<abbr title="Phone Number">P</abbr>: <span id="number_2">{$number_2}</span>
									</p>
									<p class="text-left">
										<i class="fa fa-home"></i> 
										<abbr title="Home Address">A</abbr>: <span id="address_2">{$address_2}</span>
									</p>
									<div class="btn-group btn-group-justified" role="group">
										<div class="btn-group" role="group">
											<button type="button" class="btn btn-info" onclick="profile()">
												Update Info
											</button>
										</div>
										<div class="btn-group" role="group">
											<button type="button" class="btn btn-primary" onclick="changepwd()">
												Change Password
											</button>
										</div>
									</div>
								</div>
							</div>
						</fieldset>
					</div>

					<div class="col-md-6">
						<fieldset class="padding-10">
							<legend class="text-center">
								<h4 class="text-white"> Others </h4>
							</legend>
							<table class="table table-striped table-bordered" cellspacing="0" width="100%">	
								<tr>
									<td><i class="fa fa-fw fa-user"></i> Client:</td>
									<td>{$chk_active} <label class="label label-success">Live</label></td>
								</tr>
								<tr>
									<td><i class="fa fa-fw fa-user"></i> Client:</td>
									<td>{$chk_suspend} <label class="label label-warning">Suspended</label></td>
								</tr>
								<tr>
									<td><i class="fa fa-fw fa-user"></i> Client:</td>
									<td>{$chk_banned} <label class="label label-danger">Banned</label></td>
								</tr>
								<tr>
									<td><i class="glyphicon glyphicon-barcode"></i> Voucher:</td>
									<td>{$chk_notused} <label class="label label-success">Not Used<label></td>
								</tr>
								<tr>
									<td><i class="glyphicon glyphicon-barcode"></i> Voucher:</td>
									<td>{$chk_used} <label class="label label-danger">Used</label></td>
								</tr>
							</table>
						</fieldset>
					</div>
				</div>

				<div class="modal fade" id="changepwd_modal" tabindex="-1" role="dialog" aria-labelledby="changepwd_modal" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h3 class="modal-title"></h3>
							</div>
							<div class="modal-body">
								<form id="change_pwd" name="change_pwd">
									<input type="hidden" id="submitted" name="submitted" value="Change Password">
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<div class="input-group">
													<input type="password" class="form-control" id="old_user_pass" name="old_user_pass"
													autocomplete="off" ondrop="return false;" onpaste="return false;" placeholder="Old Password" required>
													<a class="input-group-addon" href="javascript:void(0);" onclick="toggle_password('old_user_pass');" id="showhide"><i class="glyphicon glyphicon-eye-open"></i></a>
												</div>
												<div class="progress password-meter" id="oldpwdMeter">
													<div class="progress-bar"></div>
												</div>
											</div>

											<div class="form-group">
												<div class="input-group">
													<input type="password" class="form-control" id="new_user_pass" name="new_user_pass"
													autocomplete="off" ondrop="return false;" onpaste="return false;" placeholder="New Password" required>
													<a class="input-group-addon" href="javascript:;" onclick="new_password('new_user_pass');" id="newshowhide"><i class="glyphicon glyphicon-eye-open"></i></a>
												</div>
												<div class="progress password-meter" id="newpwdMeter">
													<div class="progress-bar"></div>
												</div>
											</div>

											<div class="form-group">
												<div class="input-group">
													<input type="password" class="form-control" id="new_user_pass2" name="new_user_pass2"
													autocomplete="off" ondrop="return false;" onpaste="return false;" placeholder="Verify Password" required>
													<a class="input-group-addon" href="javascript:;" onclick="new_password2('new_user_pass2');" id="newshowhide2"><i class="glyphicon glyphicon-eye-open"></i></a>
												</div>
												<div class="progress password-meter" id="chkpwdMeter">
													<div class="progress-bar"></div>
												</div>
											</div>
										</div>
									</div>
									<div class="control-group form-group">
										<div class="modal-footer">
											<button type="submit" id="submitChangePWD" name="submitChangePWD" class="btn btn-success">Change Password</button>
											<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
											<span align="left" id="loading"></span>
										</div>
									</div>
								</form>
							</div><!-- /.modal-body -->
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
				<!-- End Bootstrap modal -->

				<div class="modal fade profile_frm" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="modal_form" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h3 class="modal-title"></h3>
							</div>
							<div class="modal-body">
								<form id="profile_frm" name="profile_frm" accept-charset="UTF-8" enctype="multipart/form-data"
								method="POST">
									<input type="hidden" id="submitted" name="submitted" value="Edit Profile">
									<div class="summary-errors alert alert-danger alert-dismissible">
										<p>Errors list below: </p>
										<ul></ul>
									</div>
									<div class="row">
										<div class="col-md-12">
											<div class="form-group input-group">
												<label class="input-group-addon control-label" for="full_name">
												<i class="glyphicon glyphicon-user"></i>
												</label>
												<input class="form-control" id="full_name" type="text" name="full_name" value="" required> 	
											</div>
											<div class="form-group input-group">
												<label class="input-group-addon control-label" for="email">
												<i class="glyphicon glyphicon-envelope"></i>
												</label>
												<input class="form-control" type="email" id="email" name="email" value="" required>
											</div>

											<div class="form-group form-material floating">
												<input type="file" class="custom-file-upload" id="images" name="images[]" data-btn-text="Select a Image" >
												<label class="floating-label" for="images"></label>
											</div>

											<div class="form-group input-group">
												<label class="input-group-addon control-label" for="profile_number">
												<i class="glyphicon glyphicon-phone"></i>
												</label>
												<input id="profile_number" type="text" class="form-control"
												autocomplete="off" onkeypress="return IsNumeric(event);"
												ondrop="return false;" onpaste="return false;"
												maxlength="13" name="profile_number" value="" required>
											</div>

											<div class="form-group>
												<label class="control-label" for="profile_address">
												<i class="glyphicon glyphicon-road"></i> Address:
												</label>
												<textarea id="profile_address" class="form-control"
												name="profile_address" rows="6" wrap="hard" required></textarea>
											</div>
										</div>	
									</div>
									<input type="hidden" id="secret" name="secret">

									<div class="control-group form-group">
										<div class="modal-footer">
											<button type="submit" id="submitProfile" name="submitProfile" class="btn btn-success">Edit Profile</button>
											<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
											<span align="left" id="loading"></span>
										</div>
									</div>
								</form>
							</div><!-- /.modal-body -->
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
				<!-- End Bootstrap modal -->
			</div>
		</section>
	</div>
{include file='apps/footer.tpl'}
</div>
{include file='js/global_js.tpl'}
{include file='js/jqueryui_js.tpl'}
{include file='js/formvalidation_js.tpl'}
{include file='ajax/changepass.tpl'}
<script>
var loading = $('#loading');
function displayAvatar() {
	$.ajax({
		type: "GET",
		url: "{$base_url}serverside/users/get-avatar.php",
		dataType: "JSON",
		success: function(data){
			$("#img").html(data.profile_image);
			$("#number_2").html(data.profile_number);
			$("#address_2").html(data.profile_address);
			$("#email_2").html(data.email);
		}
	});
}
displayAvatar();
function profile()
{
	$('.form-group').removeClass('has-error'); // clear error class
	$('.help-block').empty(); // clear error string
	$.ajax({
        url: "{$base_url}serverside/users/get-avatar.php",	
        dataType: "JSON",
		cache: false,
        success: function(value)
        {
			$('#profile_frm')[0].reset(); // reset form on modals
			$('#profile_frm').trigger('reset');
			$('#profile_frm').formValidation('resetForm', true);
			$('#secret').val(value.secret);
			$('#full_name').val(value.name);
			$('#email').val(value.email);
			$('#profile_number').val(value.profile_number_3 || '');
			$('#profile_address').val(value.profile_address_3 || '');
			$('#profile_fb').val(value.profile_fb_3 || '');
            $('.profile_frm').modal('show');
            $('.modal-title').text('Edit Profile Info: ' +value.username);
			$('#submitProfile').html('Update Profile: ' +value.username);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

var specialKeys = new Array();
specialKeys.push(8,46); //Backspace
function IsNumeric(e)
{
	var keyCode = e.which ? e.which : e.keyCode;
	console.log( keyCode );
	var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeys.indexOf(keyCode) != -1);
	return ret;
}
	
$('document').ready(function(){
	$('.summary-errors').hide();
	$('#profile_frm').formValidation({
        framework: 'bootstrap',
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
			email:
			{
				valid: true,
				message: 'The email address is not valid',
                validators: 
				{
					email: 
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
			profile_number:
			{
				validators: 
				{
                    notEmpty: 
					{
						message: 'The Phone Number is required and can\'t be empty'
                    },
                    stringLength: 
					{
                        min: 7,
						max: 13,
                        message: 'The Phone Number Password must be more than 7 and less than 13'
                    },
                    regexp: 
					{
						regexp: /^[0-9-+\.]+$/,
						message: 'The Phone Number can only consist of numeric number and plus sign'
                    }
				}
			},
			'images[]':
			{				
				validators: 
				{
					file:
					{
						extension: 'jpeg,jpg,png',
						type: 'image/jpeg,image/png',
						maxSize: 100 * 1024 * 1024,
						message: 'The file must be in .jpeg, .jpg, .png format and must not exceed 2MB in size'
					}
                }
			}
        }
    })
	.on('success.form.fv', function(e, data) {
		$('.summary-errors').html(data);
	})
	.on('err.field.fv', function(e, data) {
		$('.summary-errors').show();
		
		var messages = data.fv.getMessages(data.element);
		
		$('.summary-errors').find('li[data-field="' + data.field +
		'"]').remove();

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
		
		if ($('#profile_frm').data('formValidation').isValid()) 
		{
            $('.summary-errors').hide();
		}
	});
	
	$('#change_pwd').formValidation
	({
        framework: 'bootstrap',
		icon: null,
		fields: 
		{
			old_user_pass:
			{
				valid: true,
				message: 'The Old Password is not valid',
				validators: 
				{
                    notEmpty: 
					{
                    message: 'The Old Password is required and can\'t be empty'
                    },
                    stringLength: 
					{
                        min: 8,
                        message: 'The Old Password must be more than 8'
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

							// Check the password strength
							score += ((value.length >= 8) ? 1 : -1);

							// The password contains uppercase character
							if (/[A-Z]/.test(value)) {
								score += 1;
							}

							// The password contains uppercase character
							if (/[a-z]/.test(value)) {
								score += 1;
							}

							// The password contains number
							if (/[0-9]/.test(value)) {
								score += 1;
							}

							// The password contains special characters
							if (/[!#$%&^~*_]/.test(value)) {
								score += 1;
							}

							return {
								valid: true,
								score: score    // We will get the score later
							};
						}
                    }
                }
			},
			new_user_pass:
			{
				valid: true,
				message: 'The New Password is not valid',
				validators: 
				{
					notEmpty: 
					{
                    message: 'The New Password is required and can\'t be empty'
                    },
                    stringLength: 
					{
                        min: 8,
                        message: 'The New Password must be more than 8'
                    },
                    identical: 
					{
                        field: 'new_user_pass2',
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

							// Check the password strength
							score += ((value.length >= 8) ? 1 : -1);

							// The password contains uppercase character
							if (/[A-Z]/.test(value)) {
								score += 1;
							}

							// The password contains uppercase character
							if (/[a-z]/.test(value)) {
								score += 1;
							}

							// The password contains number
							if (/[0-9]/.test(value)) {
								score += 1;
							}

							// The password contains special characters
							if (/[!#$%&^~*_]/.test(value)) {
								score += 1;
							}

							return {
								valid: true,
								score: score    // We will get the score later
							};
						}
					}
                }
			},

			new_user_pass2:
			{
				valid: true,
				message: 'The New Password is not valid',
				validators: 
				{
                    identical: 
					{
                        field: 'new_user_pass',
                        message: 'The password and its confirm are not the same'
                    },
                    notEmpty: 
					{
                    message: 'The New Password is required and can\'t be empty'
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

							// Check the password strength
							score += ((value.length >= 8) ? 1 : -1);

							// The password contains uppercase character
							if (/[A-Z]/.test(value)) {
								score += 1;
							}

							// The password contains uppercase character
							if (/[a-z]/.test(value)) {
								score += 1;
							}

							// The password contains number
							if (/[0-9]/.test(value)) {
								score += 1;
							}

							// The password contains special characters
							if (/[!#$%&^~*_]/.test(value)) {
								score += 1;
							}

							return {
								valid: true,
								score: score    // We will get the score later
							};
						}
					}
                }
			}
        }
    })
	.on('success.validator.fv', function(e, data) {
		if (data.field === 'old_user_pass' && data.validator === 'callback')
		{
			var score = data.result.score,
			$bar  = $('#oldpwdMeter').find('.progress-bar');
			switch (true)
			{
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

		if (data.field === 'new_user_pass' && data.validator === 'callback')
		{
			var score = data.result.score,
			$bar  = $('#newpwdMeter').find('.progress-bar');
			switch (true)
			{
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

		if (data.field === 'new_user_pass2' && data.validator === 'callback')
		{
			var score = data.result.score,
			$bar  = $('#chkpwdMeter').find('.progress-bar');
			switch (true)
			{
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
    });	

	var myform  = $("#profile_frm");
	$(myform).ajaxForm({
		type: "POST",
		url: "{$base_url}serverside/forms/edit_profile.php",
		data: myform.serialize(),
		beforeSend: function() {
			loading.show();
		},
		complete: function(response) {
			myform.resetForm();
		},
		success: function(response) {
			$('#success').html(response);
			var result = (typeof window.programmitHandleFormSaveResponse === 'function')
				? window.programmitHandleFormSaveResponse(response, {
					successTitle: 'Perfil actualizado',
					errorTitle: 'No se pudo actualizar el perfil'
				})
				: { ok: true, message: '' };
			if (typeof window.programmitApplyInlineFormResponse === 'function') {
				window.programmitApplyInlineFormResponse($(myform), result);
			}
			loading.hide();
			if (!result.ok) {
				return;
			}
			myform.resetForm();
			$('.profile_frm').modal('hide');
			displayAvatar();
		},
		error: function(jqXHR, textStatus, errorThrown) {
			var response = jqXHR && jqXHR.responseText ? jqXHR.responseText : '';
			$('#success').html(response);
			var result = (typeof window.programmitHandleFormSaveResponse === 'function')
				? window.programmitHandleFormSaveResponse(response, {
					successTitle: 'Perfil actualizado',
					errorTitle: 'No se pudo actualizar el perfil'
				})
				: { ok: false, message: 'Ocurrio un error al actualizar el perfil.' };
			if (typeof window.programmitApplyInlineFormResponse === 'function') {
				window.programmitApplyInlineFormResponse($(myform), result);
			}
			loading.hide();
		}
	});


	var change_pwd  = $("#change_pwd");
	$(change_pwd).ajaxForm({
		type: "POST",
		url: "{$base_url}serverside/forms/change-pwd.php",
		data: change_pwd.serialize(),
		beforeSend: function() {
			loading.show();
		},
		complete: function(response) {
			change_pwd.resetForm();
		},
		success: function(data) {
			$('#success').html(data);
			loading.hide();
			change_pwd.resetForm();
			$('.modal').modal('hide');
			displayAvatar();
		},
		error: function(jqXHR, textStatus, errorThrown) {
			loading.hide();		
			$('#success').html(data);
			change_pwd.resetForm();
			$('.modal').modal('hide');
		}
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

function new_password2(target){
    var d = document;
    var tag = d.getElementById(target);
    var tag2 = d.getElementById("newshowhide2");

    if (tag2.innerHTML == '<i class="glyphicon glyphicon-eye-open"></i>')
	{
        tag.setAttribute('type', 'text');   
        tag2.innerHTML = '<i class="glyphicon glyphicon-eye-close"></i>';

    } else {
        tag.setAttribute('type', 'password');   
        tag2.innerHTML = '<i class="glyphicon glyphicon-eye-open"></i>';
    }
}
	
function changepwd()
{
	$('.form-group').removeClass('has-error');
	$('.help-block').empty();
	$('#changepwd_modal').modal('show');
	$('.modal-title').text('Changing Password');
	$('#change_pwd').formValidation('resetForm', true);
}	
</script>
{include file='apps/liveclock.tpl'}
</body>
</html>
