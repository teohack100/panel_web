<script>
var loading = $('#loading');
var convert_tbl;
var voucherTbl;
var credits_table;
var recoveryTbl;

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
			$("#email_2").html(data.email);
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

setInterval("displayAvatar()", 1000);

var progressbox     = $('.progress');
var progressbar     = $('.progress-bar');
var statustxt       = $('.percent');
var submitbutton    = $("#submitProfile");
var completed       = '0%';

function profile()
{
	$('.form-group').removeClass('has-error');
	$.ajax({
        url: "{$base_url}serverside/users/get-avatar.php",	
        dataType: "JSON",
		cache: false,
        success: function(value)
        {	
			$('#profile_frm')[0].reset();
			$('#profile_frm').trigger('reset');
			$('#profile_frm').formValidation('resetForm', true);
			$('#secret').val(value.secret);
			$('#full_name').val(value.name);
			$('#user_email').val(value.email);
			$('#profile_number').val(value.profile_number_3);
			$('#profile_address').val(value.profile_address_3);
			$('#profile_fb').val(value.profile_fb_3);
            $('#profileFrm').modal('show');
            $('.modal-title').text('Edit Profile Info: ' +value.username);
			$('#submitProfile').html('Update Profile: ' +value.username);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

$('document').ready(function()
{
	convert_tbl = $('#conversion').dataTable({
		responsive: true,
		"bProcessing": true,
		"bServerSide": true,
		"bStateSave": true,
		"ajax": {
			"url": "{$base_url}serverside/duration/logs/conversion.php",
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

	recovery_tbl = $('#recoveryTbl').dataTable({
		responsive: true,
		"bProcessing": true,
		"bServerSide": true,
		"bStateSave": true,
		"ajax": {
			"url": "{$base_url}serverside/ban/recovery-logs.php",
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
		dom: 'frtipB',
        buttons: ['copy', 'excel', 'pdf'],
		responsive: true,
        "bProcessing": true,
        "bServerSide": true,
        "bStateSave": true,
        "ajax": {
            "url": "{$base_url}serverside/voucher/logs/voucher_used.php",
            "type": "POST"
        },
		order: [[ 2, 'asc' ], [ 0, 'desc' ]],
		"iDisplayLength": 10,
		"aLengthMenu": [
				[10, 25, 50, 100, 99999999999999],
				[10, 25, 50, 100, "ALL"]
		],
		"sPaginationType": "full",
		language: {
			"sSearchPlaceholder": "Search..",
			"lengthMenu": "_MENU_",
			"search": "_INPUT_",
			"oPaginate":
			{
				"sFirst":'<i class="fa fa-angle-double-left" aria-hidden="true"></i>',
				"sLast": '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
				"sNext": '<i class="fa fa-angle-right" aria-hidden="true"></i>',
				"sPrevious": '<i class="fa fa-angle-left" aria-hidden="true"></i>'
			},
			"sInfo":'Showing _START_ to _END_ of _TOTAL_ entries',
			"sZeroRecords": "No matching records found"
		}
	});

{if $user_id_2 >0 && user_level_2 != 'normal'}
	credits_table = $('#creditsTbl').dataTable({
		dom: 'frtipB',
        buttons: ['copy', 'excel', 'pdf'],
		responsive: true,
        "bProcessing": true,
        "bServerSide": true,
        "bStateSave": true,
        "ajax": {
            "url": "{$base_url}index.php?p=credit_logs",
            "type": "POST"
        },
		"aoColumnDefs": [{
			'bSortable': true,
			'aTargets': [-1]
		}],
		order: [[ 0, 'desc' ], [ 0, 'asc' ]],
		"iDisplayLength": 10,
		"aLengthMenu": [
				[10, 25, 50, 100, 99999999999999],
				[10, 25, 50, 100, "ALL"]
		],
		"sPaginationType": "full",
		language: {
			"sSearchPlaceholder": "Search..",
			"lengthMenu": "_MENU_",
			"search": "_INPUT_",
			"oPaginate":
			{
				"sFirst":'<i class="fa fa-angle-double-left" aria-hidden="true"></i>',
				"sLast": '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
				"sNext": '<i class="fa fa-angle-right" aria-hidden="true"></i>',
				"sPrevious": '<i class="fa fa-angle-left" aria-hidden="true"></i>'
			},
			"sInfo":'Showing _START_ to _END_ of _TOTAL_ entries',
			"sZeroRecords": "No matching records found"
		}
	});

	selfused_tbl = $('#voucherTbl').dataTable({
		responsive: true,
        "bProcessing": true,
        "bServerSide": true,
        "bStateSave": true,
        "ajax": {
            "url": "{$base_url}serverside/voucher/logs/voucher_selfused.php",
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
{/if}
});

$(document).ready(function($){
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
			user_email:
			{				
				valid: true,
				message: 'The email address is not valid',
                validators: 
				{
					user_email: 
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
			profile_fb:
			{
				valid: true,
				message: 'The URL Address is not valid facebook profile link',
				validators: 
				{
					regexp:
					{
						regexp: '/\w.+facebook.co+[^/]+|\d21/',
						message: 'The value is not a valid URL Address'
					},
                    uri: 
					{
                        message: 'The value is not a valid URL Address'
                    }
                }
			},
			profile_address:
			{
				valid: true,
				message: 'The Address is not valid',
				validators: 
				{
					notEmpty: 
					{
						message: 'The Address is required and can\'t be empty'
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
	
	var $forms = $("#profile_frm");
	statustxt.empty();
	$forms.ajaxForm({
		type: "POST",
		url: "{$base_url}serverside/forms/edit_profile.php",
		data: $forms.serialize(),
		cache: false,
		contentType: false,
		processData: false,
		uploadProgress: function(event, position, total, percentComplete) {
			progressbar.width(percentComplete + '%')
			statustxt.html(percentComplete + '%');
			if(percentComplete>20)
			{
				statustxt.css('color','#E9FC04');
			}
			if(percentComplete>40)
			{
				statustxt.css('color','#FC4B04');
			}
			if(percentComplete>60)
			{
				statustxt.css('color','#1E04FC');
			}
			if(percentComplete>80)
			{
				statustxt.css('color','#4BFC04');
			}
		},
		complete: function()
		{
			submitbutton.removeAttr('disabled');
			statustxt.empty();
		},
		success: function(data)
		{
			$('#success').html(data);
			$forms.resetForm();
			$('#profileFrm').modal('hide');
			$( ".progress" ).css( "width", "0%" ).attr( "aria-valuenow", 0);
			closed();
		},
		error: function()
		{
			$('#success').html(data);
			$('#profileFrm').modal('hide');
			$forms.resetForm();
			closed();
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

function displayConv() {
	$.ajax({
		type: "POST",
		url: "{$base_url}serverside/duration/info.php",
		data: $('#convertForm').serialize(),
		success: function(data){
			$("#conv").html(data);
		}
	});
}
$(".conv").change( displayConv );
displayConv();

function convert()
{
	$('#convertForm')[0].reset();
	$('#success_convert').html('');
	$('#conversion_loader').html('');
	$('.form-group').removeClass('has-error');
	$('#convert_form').modal('show');
	$('.modal-title').text('Convertir duracion "Premium o VIP"');
	displayConv();
}

function conversion() 
{
	var conversion_loader = $('#conversion_loader');
	var $convertform = $('#convertForm');
	alertify.confirm('Deseas convertir tu duracion?',function(e)
	{
		if(e)
		{
			$.ajax({
				url: "{$base_url}serverside/duration/conversion.php",
				type: "POST",
				data: $convertform.serialize(),
				cache: false,
				beforeSend: function() {
					conversion_loader.html('Espera por favor... procesando datos...');
				},
				success: function(data) {
					$('#success_convert').html(data);
					conversion_loader.html('');
					displayConv();
					reload_table();
				},
				error: function(jqXHR, textStatus, errorThrown){
					$('#success_convert').html(data);
					displayConv();
					reload_table();
				}
			});
		}
	},function(){
		alertify.error('Cancelado');
		convert();
	}).setting('labels',{literal}{'ok':'Aceptar', 'cancel': 'Cancelar'}{/literal}).setHeader('{$siteTitle}'); ;
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
</script>
