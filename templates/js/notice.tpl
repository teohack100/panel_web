<script>

var download_tbl;
var download_method;
var loading = $('#loading');
var progressbox     = $('.progress');
var progressbar     = $('.progress-bar');
var statustxt       = $('.percent').addClass('d-none');
var submitbutton    = $("#submitdownload");
var completed       = '0%';
$("#delete_download").prop('disabled', true);

function CKupdate(){
	for ( instance in CKEDITOR.instances ){
		CKEDITOR.instances[instance].updateElement();
	}
}

var editor = $('textarea').ckeditor({
	toolbar: 'Full',
	enterMode : CKEDITOR.ENTER_BR,
	shiftEnterMode: CKEDITOR.ENTER_P
});

editor.on( 'change', function( evt ) {
	console.log( 'Total bytes: ' + evt.editor.getData().length );
});


// add
function download_add(){
    download_method = 'add';
    $('#dlform')[0].reset();
    $('.form-group').removeClass('has-error');
	$('#dlform').formValidation('resetForm', true);
    $('#download_form').modal('show');
	$('.dl_frm').val('Download Upload');
	$('.modal-title').text('Add Notice');
	statustxt.empty();
}

// edit
function download_edit(id){
	download_method = 'update';
	$('#dlform').trigger('reset');
	$('#dlform').formValidation('resetForm', true);
	$('.form-group').removeClass('has-error');
	$.ajax({
		url : "{$base_url}serverside/download/download_edit.php",
		data: "id="+id,
		type: "GET",
		dataType: "JSON",
		cache: false,
		success: function(datas)
		{
			$('#download_id').val(datas.id);
			$('#download_category').val(datas.download_category);
			$("#download_title").val(datas.download_title);
			$('#download_msg').val(datas.download_msg);
			$("option:selected",$('#download_network').val(datas.download_network)).text();
			$("option:selected",$('#download_device').val(datas.download_device)).text();
			$('#download_form').modal('show'); // show bootstrap modal when complete loaded
			$('.dl-title').text('Edit Download Form: ' +datas.download_title); // Set title to Bootstrap modal title
			$('.dl_frm').val('Download Update');
			statustxt.empty();
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert('Error get data from ajax');
		}
	});
}

// view
function download_view(id){
	$.ajax({
		url : "{$base_url}serverside/download/download_edit.php",
		data: "id="+id,
		type: "GET",
		dataType: "JSON",
		cache: false,
		success: function(data)
		{
			$('#output_msg').html(data.download_msg);
			$('#output_file').html(data.download_url);
			$('#download_view').modal('show');
			$('.modal-title').text('Notice Details: ' +data.download_title);
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert('Error get data from ajax');
		}
	});	
}

// delete
function delete_download() 
{
	$(".ajs-header").html('{$siteTitle} - Alert Message');
	alertify.confirm('Are you sure do you want to delete?',function(){
		$.ajax({
			type: "POST",
			url: "{$base_url}serverside/download/download_delete.php",
			data: $('#dl_frm').serialize(),
			success: function(data)
			{
				reload_table();
				$('#delete_download').prop('disabled', true);
				$('.success').html(data);
				$(".select-alls").prop('checked', false);
				$(".chk-boxs").prop('checked', false);
				alertify.success('Successfully Deleted');
			},
			error: function(data)
			{
				reload_table();
				$('.success').html(data);
				$(".select-alls").prop('checked', false);
				$(".chk-boxs").prop('checked', false);
				alertify.error('Failed to Deleted');
			}
		});
	},function(){
		alertify.error('Declined');
		$("#delete_download").prop('disabled', true);
		$(".chk-boxs").prop('checked', false);
		$(".select-alls").prop('checked', false);
	}).setting('labels',{literal}{'ok':'Accept', 'cancel': 'Decline'}{/literal});
}

$('#dlform').formValidation
({
	framework: 'bootstrap',
	icon: null,
	fields: 
	{
		download_title:
		{
			valid: true,
			message: 'The download title is not valid',
			validators:
			{
				notEmpty:
				{
					message: 'The download title is required and can\'t be empty'
				},
				stringLength:
				{
					min: 3,
					message: 'The download title must be more than 3'
				}
			}
		},
		download_file:
		{
			validators: 
			{
				file:
				{
					maxSize: 20 * 2024 * 2024,
					message: 'The file must be in .APK, .EXE, .MSI, .ZIP, .RAR format and must not exceed 10MB in size'
				}
			}
		},
		download_msg:
		{
			valid: true,
			message: 'The download message is not valid',
			validators:
			{
				notEmpty:
				{
					message: 'The download message is required and can\'t be empty'
				},
				stringLength:
				{
					min: 3,
					message: 'The download message must be more than 3'
				},
				callback:
				{
					message: 'The download message is must be less than 99999 characters long',
					callback: function(value, validator, $field)
					{
						if (value === '')
						{
							return true;
						}
						var div  = $('<div/>').html(value).get(0),
						text = div.textContent || div.innerText;
						return text.length <= 99999;
					}
				}
			}
		}
	}
})
.on('success.form.fv', function(e, data) {
	statustxt.empty();
	var $forms = $(e.target);
	$forms.ajaxForm({
		type: "POST",
		url: "{$base_url}serverside/download/download_upload.php",
		data: $forms.serialize(),
		cache: false,
		contentType: false,
		beforeSend: function()
		{
			loading.show();
			$("#submitdownload").prop('disabled', true);
		},
		uploadProgress: function(event, position, total, percentComplete) {
			statustxt.html(percentComplete + '').removeClass('d-none');
			if(percentComplete>5)
			{
				statustxt.css('color','#E9FC04');
				progressbar.html('5% Uploaded').css('width', '5%').removeClass().addClass('progress-bar  progress-bar-striped progress-bar-animated bg-danger');
			}
			if(percentComplete>10)
			{
				statustxt.css('color','#E9FC04');
				progressbar.html('10% Uploaded').css('width', '10%').removeClass().addClass('progress-bar  progress-bar-striped progress-bar-animated bg-danger');
			}
			if(percentComplete>15)
			{
				statustxt.css('color','#E9FC04');
				progressbar.html('15% Uploaded').css('width', '15%').removeClass().addClass('progress-bar  progress-bar-striped progress-bar-animated bg-danger');
			}
			if(percentComplete>20)
			{
				statustxt.css('color','#E9FC04');
				progressbar.html('20% Uploaded').css('width', '20%').removeClass().addClass('progress-bar  progress-bar-striped progress-bar-animated bg-danger');
			}
			if(percentComplete>25)
			{
				statustxt.css('color','#E9FC04');
				progressbar.html('25% Uploaded').css('width', '25%').removeClass().addClass('progress-bar  progress-bar-striped progress-bar-animated bg-danger');
			}
			if(percentComplete>30)
			{
				statustxt.css('color','#FC4B04');
				progressbar.html('30% Uploaded').css('width', '30%').removeClass().addClass('progress-bar  progress-bar-striped progress-bar-animated bg-warning');
			}
			if(percentComplete>35)
			{
				statustxt.css('color','#FC4B04');
				progressbar.html('35% Uploaded').css('width', '35%').removeClass().addClass('progress-bar  progress-bar-striped progress-bar-animated bg-warning');
			}
			if(percentComplete>40)
			{
				statustxt.css('color','#FC4B04');
				progressbar.html('40% Uploaded').css('width', '40%').removeClass().addClass('progress-bar  progress-bar-striped progress-bar-animated bg-warning');
			}
			if(percentComplete>45)
			{
				statustxt.css('color','#FC4B04');
				progressbar.html('45% Uploaded').css('width', '45%').removeClass().addClass('progress-bar  progress-bar-striped progress-bar-animated bg-warning');
			}
			if(percentComplete>50)
			{
				statustxt.css('color','#FC4B04');
				progressbar.html('50% Uploaded').css('width', '50%').removeClass().addClass('progress-bar  progress-bar-striped progress-bar-animated bg-warning');
			}
			if(percentComplete>55)
			{
				statustxt.css('color','#1E04FC');
				progressbar.html('55% Uploaded').css('width', '55%').removeClass().addClass('progress-bar  progress-bar-striped progress-bar-animated bg-secondary');
			}
			if(percentComplete>60)
			{
				statustxt.css('color','#1E04FC');
				progressbar.html('60% Uploaded').css('width', '60%').removeClass().addClass('progress-bar  progress-bar-striped progress-bar-animated bg-secondary');
			}
			if(percentComplete>65)
			{
				statustxt.css('color','#1E04FC');
				progressbar.html('65% Uploaded').css('width', '65%').removeClass().addClass('progress-bar  progress-bar-striped progress-bar-animated bg-secondary');
			}
			if(percentComplete>70)
			{
				statustxt.css('color','#1E04FC');
				progressbar.html('70% Uploaded').css('width', '70%').removeClass().addClass('progress-bar  progress-bar-striped progress-bar-animated bg-secondary');
			}
			if(percentComplete>75)
			{
				statustxt.css('color','#1E04FC');
				progressbar.html('75% Uploaded').css('width', '75%').removeClass().addClass('progress-bar  progress-bar-striped progress-bar-animated bg-secondary');
			}
			if(percentComplete>80)
			{
				statustxt.css('color','#4BFC04');
				progressbar.html('80% Uploaded').css('width', '80%').removeClass().addClass('progress-bar  progress-bar-striped progress-bar-animated bg-success');
			}
			if(percentComplete>85)
			{
				statustxt.css('color','#4BFC04');
				progressbar.html('85% Uploaded').css('width', '85%').removeClass().addClass('progress-bar  progress-bar-striped progress-bar-animated bg-success');
			}
			if(percentComplete>90)
			{
				statustxt.css('color','#4BFC04');
				progressbar.html('90% Uploaded').css('width', '90%').removeClass().addClass('progress-bar  progress-bar-striped progress-bar-animated bg-success');
			}
			if(percentComplete>95)
			{
				statustxt.css('color','#4BFC04');
				progressbar.html('95% Uploaded').css('width', '95%').removeClass().addClass('progress-bar  progress-bar-striped progress-bar-animated bg-success');
			}
			if(percentComplete>99)
			{
				statustxt.css('color','#4BFC04');
				progressbar.html('100% Uploaded').css('width', '100%').removeClass().addClass('progress-bar  progress-bar-striped progress-bar-animated bg-success');
			}
		},
		complete: function()
		{
			loading.hide();
			$("#submitdownload").prop('disabled', false);
			progressbar.html('').css('width', '0%').removeClass();
			statustxt.empty().addClass('d-none');;
		},
		success: function(data)
		{
			$('.success').html(data);
			$forms.formValidation('resetForm', true);
			reload_table();
			$('#download_form').modal('hide');
			alertify.success('Successfully Submitted Record');
		},
		error: function()
		{
			$('.success').html(data);
			$('#download_form').modal('hide');
			$forms.formValidation('resetForm', true);
			alertify.success('Failed! Submitted Record');
		}
	});
});

$.fn.modal.Constructor.prototype.enforceFocus = function () {
	modal_this = this
	$(document).on('focusin.modal', function (e) {
		if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length
		&&
		!$(e.target.parentNode).hasClass('cke_dialog_ui_input_select') && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_text')) {
			modal_this.$element.focus()
		}
	})
};

$('document').ready(function()
{
    $('.select-alls').click(function(event) {
        if(this.checked) {
            $('.chk-boxs').each(function() {
                this.checked = true;
				$("#delete_download").prop('disabled', false);
            });
        }else{
            $('.chk-boxs').each(function() {
                this.checked = false;
            }); 
			if($(".select-alls").prop('checked') == false){
				$("#delete_download").prop('disabled', true);
			}
        }
    });

	if($(".select-alls").prop('checked') == false){
		$("#delete_download").prop('disabled', true);
	}

	$('body').delegate('.chk-boxs','click',function(event){
		if ($('.chk-boxs').is(':checked') == true){
			$("#delete_download").prop('disabled', false);
		} else {
			$("#delete_download").prop('disabled', true);
		}
	});

	download_tbl = $('#download_serverside').dataTable({
		dom: 'frtipB',
        buttons: ['copy', 'excel', 'pdf'],
		responsive: true,
        "bProcessing": true,
        "bServerSide": true,
        "bStateSave": true,
        "ajax": {
            "url": "{$base_url}serverside/download/download_serverside.php",
            "type": "POST"
        },
		"aoColumnDefs": [{
			'bSortable': true,
			'aTargets': [0,-1]
		}],
		order: [[ 0, 'desc' ], [ 0, 'asc' ]],
		"iDisplayLength": 10,
		"aLengthMenu": [
				[10, 25, 50, 100, -1],
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
});
</script>