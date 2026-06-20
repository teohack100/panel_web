<script>
var save_method;
var loading = $('#loading');
var table;

function reload_table()
{
	$('#servers-serverside').DataTable().ajax.reload();
}

function server_add(){
    save_method = 'add';
    $('#server_frm')[0].reset();
	$('#server_frm').trigger('reset');
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();
	$('#server_frm').formValidation('resetForm', true);
    $('#modal_server').modal('show');
	$('.modal-title').text('Server List Form');
	$('#submitServer').html("Server Upload");
	$('#submitted').val("Server Upload");
	$('#server_parser').html('');
}

function delete_record() 
{
	$(".ajs-header").html('{$siteTitle} - Alert Message');
	alertify.confirm('Are you sure do you want to delete?',function(){
		$.ajax({
			type: "POST",
			url: "{$base_url}serverside/serverupload/server_delete.php",
			data: $('form').serialize(),
			success: function(data) {
				reload_table();
				$('#delete_records').prop('disabled', true);
				$('#success').html(data);
				$(".select-all").prop('checked', false);
				$(".chk-box").prop('checked', false);
				alertify.success('Successfully Deleted');
			},
			error: function(data) {	
				reload_table();
				$('#success').html(data);
				$(".select-all").prop('checked', false);
				$(".chk-box").prop('checked', false);
				alertify.error('Failed to Deleted');
			}
		});
	},function(){
		alertify.error('Declined');
		$("#delete_records").prop('disabled', true);
		$(".chk-box").prop('checked', false);
		$(".select-all").prop('checked', false);
	}).setting('labels',{literal}{'ok':'Accept', 'cancel': 'Decline'}{/literal});
}

function server_edit(id){
	save_method = 'update';
	$('#server_frm').trigger('reset');
	$('#server_frm').formValidation('resetForm', true);
	$('#submitted').val("Server Edit");
	$('.form-group').removeClass('has-error');
	$('.help-block').empty();
	$.ajax({
		url : "{$base_url}serverside/serverupload/server_edit.php",
		data: "server_id="+id,
		type: "GET",		
		dataType: "JSON",
		cache: false,
		success: function(data)
		{
			$('#server_frm').formValidation('resetForm', true);
			$('#server_id').val(data.server_id);
			$('#server_name').val(data.server_name);
			$('#server_ip').val(data.server_ip);
			$('#server_port').val(data.server_port);
			$('#server_parser').html(data.server_parser);
			$('#modal_server').modal('show'); // show bootstrap modal when complete loaded
			$('.modal-title').text('Edit Server List Form: ' +data.server_name); // Set title to Bootstrap modal title
			$('#submitServer').html("Server Update");
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert('Error get data from ajax');
		}
	});
}

$('document').ready(function()
{
	table = $('#servers-serverside').dataTable({
		dom: 'frtipB',
        buttons: ['copy', 'excel', 'pdf'],
		responsive: true,
        "bProcessing": true,
        "bServerSide": true,
        "bStateSave": false,
        "ajax": {
            "url": "{$base_url}serverside/serverupload/serverlist_serverside2.php",
            "type": "POST"
        },
		"aoColumnDefs": [{
			'bSortable': false,
			'aTargets': [0,-1]
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

	$('#server_frm').formValidation({
        framework: 'bootstrap',
		icon: null,
		fields: 
		{
			server_name:
			{
				valid: true,
				message: 'The server name is not valid',
				validators: 
				{
                    notEmpty: 
					{
						message: 'The server name is required and can\'t be empty'
                    },
                    stringLength: 
					{
                        min: 3,
                        message: 'The server name must be more than 3'
                    }
				}
			},
			server_port:
			{
				valid: true,
				message: 'The server port is not valid',
				validators: 
				{
                    notEmpty: 
					{
						message: 'The server port  is required and can\'t be empty'
                    }
				}
			},
			server_ip:
			{
				valid: true,
				message: 'The server ip address is not valid',
				validators: 
				{
                    notEmpty: 
					{
						message: 'The server ip address is required and can\'t be empty'
                    },
					ip: 
					{
						message: 'The value is not a valid server ip address'
					}
				}
			}
		}
    })
	
	.on('success.form.fv', function(e, data) {
		e.preventDefault();	

        var $form = $(e.target);
		$.ajax({
			type: "POST",
			url: "{$base_url}serverside/serverupload/server_upload.php",
			data: $form.serialize(),
			beforeSend: function() {
				loading.show();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$('#success').html(data);
				alertify.success('Failed! Submitted Record');
			},
			success: function(data){
				$('#success').html(data);
				$form.formValidation('resetForm', true);
				$('#modal_server').modal('hide');
				reload_table();
				alertify.success('Successfully Submitted Record');
			},
			complete: function(){
				loading.hide();
			}
		});
	});

    $('.select-all').click(function(event) {
        if(this.checked) {
            $('.chk-box').each(function() {
                this.checked = true;
				$("#delete_records").prop('disabled', false);
            });
        }else{
            $('.chk-box').each(function() {
                this.checked = false;
            });
			if($(".select-all").prop('checked') == false){
				$("#delete_records").prop('disabled', true);
			}
        }
    });
	
	if($(".select-all").prop('checked') == false){
		$("#delete_records").prop('disabled', true);
	}
	
	$('body').delegate('.chk-box','click',function(event){
		if ($('.chk-box').is(':checked') == true){
			$("#delete_records").prop('disabled', false);
			console.log('checked');
		} else {
			$("#delete_records").prop('disabled', true);
			console.log('unchecked');
		}
	});
	
	$('#server_ip').on('input', function (event){
		this.value = this.value.replace(/[^0-9-.]/g, '');
	});
	$('#server_port').on('input', function (event){
		this.value = this.value.replace(/[^0-9]/g, '');
	});
});
</script>

<script>
$(document).ready(function(){
	table = $('#onlineusers').dataTable({
        responsive: true,
		"iDisplayLength": -1,
		"aoColumnDefs": [{
			'bSortable': true,
			'aTargets': [0,-1]
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
			"infoFiltered": "",
			"sZeroRecords": "No users connected"
		},
		"dom": 'frtipB',
        "buttons": ['copy', 'excel', 'pdf'],
		"oTableTools": {
		"sSwfPath": "{$base_url}bootstrap/assets/datatables/extensions/TableTools/swf/copy_csv_xls_pdf.swf"
		}
	});
});
</script>