<script>
var loading = $('#changepwd_modal #loading');
function displayAvatar()
{
	$.ajax({
        url: "{$base_url}serverside/users/get-avatar.php",	
        dataType: "JSON",
		cache: false,
        success: function(data)
        {
			$("#img").html(data.profile_image);
			$("#number_2").html(data.profile_number);
			$("#address_2").html(data.profile_address);
			$("#access_role_2").text(data.role_label || 'Cliente normal');
			$("#access_value_2").text(data.access_value || '');
			$("#fb_2").html(data.profile_fb);
			$("#credits").html(data.credits);
			$("#bandwidth_free").html(data.bandwidth_free);
			$("#bandwidth_ph").html(data.bandwidth_ph);
			$("#bandwidth_premium").html(data.bandwidth_premium);
			$("#bandwidth_private").html(data.bandwidth_private);
			$("#bandwidth_vip").html(data.bandwidth_vip);
			$("#premium_status").html(data.duration);
			
			$("#dur_day").html(data.duration_day);
			$("#dur_minute").html(data.duration_minute);
			$("#dur_hour").html(data.duration_hour);
			
			$("#dura_day").html(data.dur_day);
			$("#dura_minute").html(data.dur_minute);
			$("#dura_hour").html(data.dur_hour);
			
			$("#vip_status").html(data.vip_duration);
			$("#private_status").html(data.private_duration);
			$("#shadowsocks_status").html(data.shadowsocks_status);
			$("#self").html(data.selfreload);
			//$("#convBtn").html(data.convBtn);
	    }
    });
}
displayAvatar();


function profile()
{
	$('.form-group').removeClass('has-error'); // clear error class
	$('.help-block').empty(); // clear error string
	$.ajax({
        url: "{$base_url}serverside/users/get-avatar2.php",	
        dataType: "JSON",
		cache: false,
		success: function(value)
        {
			$('#profile_frm')[0].reset(); // reset form on modals
			$('#profile_frm').trigger('reset');
			$('#profile_frm').formValidation('resetForm', true);
			$('#profile_secret').val(value.profile_secret);
			$('#profile_access_role').text(value.role_label || 'Cliente normal');
			$('#profile_access_text').text(value.access_value || '');
			$('#full_name').val(value.name);
			$('#profile_number').val(value.profile_number_3 || '');
			$('#profile_address').val(value.profile_address_3 || '');
			$('#profile_fb').val(value.profile_fb_3 || '');
            $('.profile_frm').modal('show');
            $('.modal-title').text('Editar información del perfil: ' +value.username);
			$('#submitProfile').html('Actualizar perfil');
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('No se pudieron cargar los datos del perfil');
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
				message: 'El nombre no es valido',
				validators: 
				{
                    notEmpty: 
					{
                    message: 'El nombre es obligatorio y no puede estar vacio'
                    },
                    stringLength: 
					{
                        min: 8,
                        message: 'El nombre debe tener al menos 8 caracteres'
                    }
				}
			},
			profile_number:
			{
				validators: 
				{
                    notEmpty: 
					{
						message: 'El telefono es obligatorio y no puede estar vacio'
                    },
                    stringLength: 
					{
                        min: 7,
						max: 13,
                        message: 'El telefono debe tener entre 7 y 13 caracteres'
                    },
                    regexp: 
					{
						regexp: /^[0-9-+\.]+$/,
						message: 'El telefono solo puede contener numeros, punto, guion o signo mas'
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
						message: 'La imagen debe estar en formato .jpeg, .jpg o .png'
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
				message: 'La contrasena actual no es valida',
				validators: 
				{
                    notEmpty: 
					{
                    message: 'La contrasena actual es obligatoria y no puede estar vacia'
                    },
                    stringLength: 
					{
                        min: 8,
                        message: 'La contrasena actual debe tener al menos 8 caracteres'
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
				message: 'La nueva contrasena no es valida',
				validators: 
				{
					notEmpty: 
					{
                    message: 'La nueva contrasena es obligatoria y no puede estar vacia'
                    },
                    stringLength: 
					{
                        min: 8,
                        message: 'La nueva contrasena debe tener al menos 8 caracteres'
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
				message: 'La confirmacion de contrasena no es valida',
				validators: 
				{
                    identical: 
					{
                        field: 'new_user_pass',
                        message: 'The password and its confirm are not the same'
                    },
                    notEmpty: 
					{
                    message: 'La confirmacion de contrasena es obligatoria y no puede estar vacia'
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

});

$(document).on('submit', '#profile_frm', function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var $form = $(this);
	if ($form.data('submitting')) return false;
	$form.data('submitting', true);
	$form.ajaxSubmit({
		type: "POST",
		url: "{$base_url}serverside/forms/edit_profile.php",
		beforeSend: function() {
			loading.show();
		},
		complete: function() {
			$form.data('submitting', false);
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
				window.programmitApplyInlineFormResponse($form, result);
			}
			loading.hide();
			if (!result.ok) {
				return;
			}
			$form.resetForm();
			$('.profile_frm').modal('hide');
			displayAvatar();
		},
		error: function(jqXHR) {
			var response = jqXHR && jqXHR.responseText ? jqXHR.responseText : '';
			$('#success').html(response);
			var result = (typeof window.programmitHandleFormSaveResponse === 'function')
				? window.programmitHandleFormSaveResponse(response, {
					successTitle: 'Perfil actualizado',
					errorTitle: 'No se pudo actualizar el perfil'
				})
				: { ok: false, message: 'Ocurrio un error al actualizar el perfil.' };
			if (typeof window.programmitApplyInlineFormResponse === 'function') {
				window.programmitApplyInlineFormResponse($form, result);
			}
			loading.hide();
		}
	});
	return false;
});

$(document).on('submit', '#change_pwd', function(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	var $form = $(this);
	if ($form.data('submitting')) return false;
	$form.data('submitting', true);
	$form.ajaxSubmit({
		type: "POST",
		url: "{$base_url}serverside/forms/change-pwd.php",
		beforeSend: function() {
			loading.show();
		},
		complete: function() {
			$form.data('submitting', false);
		},
		success: function(data) {
			$('#success').html(data);
			loading.hide();
			$form.resetForm();
			$('.modal').modal('hide');
			displayAvatar();
		},
		error: function(jqXHR) {
			loading.hide();
			$('#success').html(jqXHR.responseText);
			$form.resetForm();
			$('.modal').modal('hide');
		}
	});
	return false;
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


	
function changepwd()
{
	$('.form-group').removeClass('has-error');
	$('.help-block').empty();
	$('#changepwd_modal').modal('show');
	$('.modal-title').text('Cambiar contrasena');
	var fv = $('#change_pwd').data('formValidation');
	if (fv) {
		$('#change_pwd').formValidation('resetForm', true);
	} else {
		$('#change_pwd')[0].reset();
	}
}	
</script>
