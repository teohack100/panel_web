<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="title" content="{$siteTitle}">
<meta name="description" content="{$siteTitle} password recovery">
<meta name="keywords" content="{$siteTitle} password recovery">
<meta name="author" content="Jhoe Angeleye">
<meta name="owner" content="{$siteTitle}">
<meta name="copyright" content="Jhoe Angeleye">
<title>Password Recovery</title>
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
{include file='css/formvalidation_css.tpl'}
</head>
<body class="hold-transition skin-main sidebar-mini">
	<div class="login-box">
		<div class="login-logo">
		</div>
		<div class="login-box-body">
			{if $smarty.get.code != ""}
				{if $error_code == 0}
					<form id="change_pwd" method="post">
						<div class="form-group has-feedback">
							<div class="input-group">
								<input id="user_pass" type="password" class="form-control" name="user_pass" value="{$user_pass}" placeholder="Password" required>
								<a class="input-group-addon" href="javascript:;" onclick="toggle_password('user_pass');" id="showhide"><i class="glyphicon glyphicon-eye-open"></i></a>
							</div>
							<div class="progress password-meter" id="signuppwdMeter">
								<div class="progress-bar"></div>
							</div>
						</div>

						<div class="form-group has-feedback">
							<div class="input-group">
								<input id="user_pass2" type="password" class="form-control" name="user_pass2" value="{$user_pass2}" placeholder="Re-Type Password" required>
								<a class="input-group-addon" href="javascript:;" onclick="new_password('user_pass2');" id="newshowhide"><i class="glyphicon glyphicon-eye-open"></i></a>
							</div>
							<div class="progress password-meter" id="chkpwdMeter">
								<div class="progress-bar"></div>
							</div>
						</div>
						<input type="hidden" class="form-control" id='resetcodes' name='resetcodes' value="{$smarty.get.code}">
						<button type="submit" class="btn btn-info btn-block btn-lg" id="reset" name="reset"><i class="glyphicon glyphicon-lock"></i> Change Password</button>
						<div id="success"></div>
					</form>
					<p>Have account already? Please go to <a href="/login">Sign In</a></p>
				{else}
							<ul style="list-style: none;" class="alert alert-success alert-dismissible">
								<button type="button" class="close" aria-label="Close" data-dismiss="alert">
									<span aria-hidden="true">×</span>
								</button>
								<li>Invalid Code</li>
							</ul>
							<h4 class="text-center">
								<a href="{$base_url}">Click Here! Return to home page!</a>
							</h4>
				{/if}
	
			{else}

						<form name="recoveryForm" id="recoveryForm" method="post" accept-charset="UTF-8">
							<input type="hidden" class="form-control" id='menu' name='menu' value="user_email" required>
							<div class="form-group has-feedback">
								<input class="form-control" id='given' name='given' autocomplete='off' required placeholder="Email Address.">
								<label class="control-label" for="given"><i class="glyphicon glyphicon-envelope form-control-feedback"></i></label>
							</div>
							
							<div class="form-group clearfix">
								<span id="loading" class='text-left'></span> 
								<a class="pull-right" href="/register">Still no account?</a>
							</div>
							<button type="submit" class="btn btn-info btn-block btn-lg" id="submitRecovery" name="submitRecovery">
								<i class="glyphicon glyphicon-lock"></i> Recovery
							</button>
						</form>
						<div id="success"></div>
						<p>Have account already? Please go to <a href="/login">Sign In</a></p>

			{/if}
		</div>
	</div>

{include file='js/global_js.tpl'}
{include file='js/formvalidation_js.tpl'}
<script>
{if $smarty.get.code != ""}
{if $error_code == 0}
$('document').ready(function()
{
	$('#change_pwd').formValidation
		({
        framework: 'bootstrap',
		icon: null,
		fields: 
		{
			user_pass:
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
			
			user_pass2:
			{				
				valid: true,
				message: 'The Re-Type Password is not valid',
				validators: 
				{
                    identical: 
					{
                        field: 'user_pass',
                        message: 'The password and its confirm are not the same'
                    },
                    notEmpty: 
					{
                    message: 'The Re-Type Password is required and can\'t be empty'
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
		// The password passes the callback validator
		if (data.field === 'user_pass' && data.validator === 'callback') 
		{
                // Get the score
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
			
            // The password passes the callback validator
		if (data.field === 'user_pass2' && data.validator === 'callback')
		{
                // Get the score
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
	});
	
	var loading = $('#loading');
	var recoveryForm  = $("#change_pwd");
	$(recoveryForm).ajaxForm({
		type: "POST",
		url: "{$base_url}serverside/forms/recovery.php",
		data: recoveryForm.serialize(),
		beforeSend: function() {
			loading.show();
		},
		complete: function(response) {
			recoveryForm.resetForm();
		},
		success: function(data) {	
			$('#success').html(data);
			loading.hide();
			recoveryForm.resetForm();
		},
		error: function(jqXHR, textStatus, errorThrown) {
			loading.hide();		
			$('#success').html(data);
			recoveryForm.resetForm();
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
{/if}
{else}
$(document).ready(function()
{
	var loading = $('#loading');
	var recoveryForm  = $("#recoveryForm");
	$(recoveryForm).ajaxForm({
		type: "POST",
		url: "{$base_url}serverside/forms/recovery.php",
		data: recoveryForm.serialize(),
		beforeSend: function() {
			loading.show();
		},
		complete: function(response) {
			recoveryForm.resetForm();
		},
		success: function(data) {	
			$('#success').html(data);
			loading.hide();
			recoveryForm.resetForm();
		},
		error: function(jqXHR, textStatus, errorThrown) {
			loading.hide();		
			$('#success').html(data);
			recoveryForm.resetForm();
		}
	});
});	

function recoveryacc()
{
	$('#recoveryForm')[0].reset();
	$('.form-group').removeClass('has-error');
	$('.help-block').empty();
	$('#recoveryForm').formValidation('resetForm', true);
}
{/if}
</script>
</body>
</html>