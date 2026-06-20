<!-- Login -->
<script>
$(document).ready(function($){	
	$('.login-errors').hide();
	$('form').formValidation
		({
        framework: 'bootstrap',
		excluded: ':disabled',
		icon: null,
		fields: 
		{		
			user_name:
			{				
				valid: true,
				message: 'The Username is not valid',
				validators: 
				{
                    notEmpty: 
					{
                    message: 'The Username is required and can\'t be empty'
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
            if (data.field === 'user_pass' && data.validator === 'callback') {
                // Get the score
                var score = data.result.score,
                    $bar  = $('#signinpwdMeter').find('.progress-bar');

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
          $('.login-errors').html(data);
        })

        .on('err.field.fv', function(e, data) {
          // data.fv     --> The FormValidation instance
          // data.field  --> The field name
          // data.element --> The field element
          $('.login-errors').show();

          // Get the messages of field
          var messages = data.fv.getMessages(data.element);

          // Remove the field messages if they're already available
          $('.login-errors').find('li[data-field="' + data.field +
            '"]').remove();

          // Loop over the messages
          for (var i in messages) {
            // Create new 'li' element to show the message
            $('<li/>')
              .attr('data-field', data.field)
              .wrapInner(
                $('<a/>')
                .attr('href', 'javascript: void(0);')
                // .addClass('alert alert-danger alert-dismissible')
                .html(messages[i])
                .on('click', function(e) {
                  // Focus on the invalid field
                  data.element.focus();
                })
              ).appendTo('.login-errors > ul');
          }

          // Hide the default message
          // $field.data('fv.messages') returns the default element containing the messages
          data.element
            .data('fv.messages')
            .find('.help-block[data-fv-for="' + data.field + '"]')
            .hide();
        })

        .on('success.field.fv', function(e, data) {
          // Remove the field messages
          $('.login-errors > ul').find('li[data-field="' + data.field +
            '"]').remove();
          if ($('form').data('formValidation').isValid()) {
            $('.login-errors').hide();
          }
        });
/*
	$('form').keypress(function(e){
		if(e.keyCode=='13') //Keycode for "Return"
		$('#submitLogin').click();
	});	
*/	
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
</script>
<!-- Login End -->
