<!-- Change Password Start-->
<script>
{literal}
$(document).ready(function($){
	$('#change_pwd').formValidation
		({
        framework: 'bootstrap',
		icon: null,
		/*{
			valid: 'glyphicon glyphicon-ok',
			invalid: 'glyphicon glyphicon-remove',
			validating: 'glyphicon glyphicon-refresh'
		},*/
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
            // The password passes the callback validator
            if (data.field === 'old_user_pass' && data.validator === 'callback') {
                // Get the score
                var score = data.result.score,
                    $bar  = $('#oldpwdMeter').find('.progress-bar');

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
            if (data.field === 'new_user_pass' && data.validator === 'callback') {
                // Get the score
                var score = data.result.score,
                    $bar  = $('#newpwdMeter').find('.progress-bar');

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
            if (data.field === 'new_user_pass2' && data.validator === 'callback') {
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
});	
{/literal}
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
</script>	
<!-- Change Password End -->
