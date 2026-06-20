
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
						max: 128,
                        message: 'The Username must be more than 3 and less than 128 characters long'
                    },
                    remote: 
					{
                        url: '{$base_url}serverside/validation/username_validation.php',
                        message: 'The Username is not available',
						data: {
							type: 'user_name'
						},
						type: 'POST'
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
					},
                    remote: 
					{
                        url: '{$base_url}serverside/validation/email_validation.php',
                        message: 'The Email is not available',
						data: {
							type: 'user_email'
						},
						type: 'POST'
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
            if (data.field === 'user_pass2' && data.validator === 'callback') {
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
	})
		
	.on('success.form.fv', function(e, data) {
          // Reset the message element when the form is valid
          $('.summary-errors').html(data);
	})

	.on('err.field.fv', function(e, data) {
          // data.fv     --> The FormValidation instance
          // data.field  --> The field name
          // data.element --> The field element
          $('.summary-errors').show();

          // Get the messages of field
          var messages = data.fv.getMessages(data.element);

          // Remove the field messages if they're already available
          $('.summary-errors').find('li[data-field="' + data.field +
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
              ).appendTo('.summary-errors > ul');
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
          $('.summary-errors > ul').find('li[data-field="' + data.field +
            '"]').remove();
          if ($('#register').data('formValidation').isValid()) {
            $('.summary-errors').hide();
          }
	})		

	.on('success.form.fv', function(e, data) {
		e.preventDefault();		
        var $form = $(e.target);
		$.ajax({
			type: "POST",
			url: "{$base_url}/serverside/forms/addclient.php",
			data: $form.serialize(),
			beforeSend: function() {
				loading.show();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$('#success').html(data);
				$form.formValidation('resetForm', true);
				$('#modal_form').modal('hide');
				reload_table();
			},
			success: function(data){
				$('#success').html(data);
				$form.formValidation('resetForm', true);
				$('#modal_form').modal('hide');
				reload_table();
			},
			complete: function(){
				loading.hide();
			}
		});
	})
	
	// This event will be triggered when the field doesn't pass given validator
	.on('err.validator.fv', function(e, data) {
            // We need to remove has-warning class
            // when the field doesn't pass any validator
            if (data.field === 'user_name') {
                data.element
                    .closest('.form-group')
                    .removeClass('has-warning');
            }
			
            if (data.field === 'user_email') {
                data.element
                    .closest('.form-group')
                    .removeClass('has-warning');
            }
	})

	// This event will be triggered when the field passes given validator
	.on('success.validator.fv', function(e, data) {
		e.preventDefault();
		var $form = $(e.target),
		bv    = $form.data('formValidation');
		if (data.field === 'user_name'
		&& data.validator === 'remote'
		&& (data.result.available === false || data.result.available === 'false'))
		{
			data.element
				.closest('.form-group')
				.removeClass('has-success')
				.addClass('has-warning')
				.find('small[data-fv-validator="remote"][data-fv-for="user_name"]')
				.show();
		}
		if (data.field === 'user_email'
		&& data.validator === 'remote'
		&& (data.result.available === false || data.result.available === 'false'))
		{
			data.element 
				.closest('.form-group')
				.removeClass('has-success')
				.addClass('has-warning')
				.find('small[data-fv-validator="remote"][data-fv-for="user_email"]')
				.show();
		}			
	});	

