
<script>
	alertify.defaults.glossary.title = '{$siteTitle}';
function closed(){
		setTimeout(function () { $('.closedBtn').trigger('click'); }, 10000);
	}
</script>
<script>

var loading = $('#loading');
var save_method;
var table;
var suspended_table;
var freeze_table;
var reloadduration_tbl;
var clientused_tbl;

function buildActivePremiumChildRow(actionsHtml) {
	return '<div class="monitoring-action-panel">' +
		'<div class="monitoring-action-label">Opciones</div>' +
		'<div class="monitoring-action-buttons">' + actionsHtml + '</div>' +
	'</div>';
}

function toggleActivePremiumRow(tr) {
	var dataTable = $('#users-serverside').DataTable();
	var row = dataTable.row(tr);
	if (!row.length) {
		return;
	}
	if (row.child.isShown()) {
		row.child.hide();
		tr.removeClass('shown');
	} else {
		var rowData = row.data();
		var actionsHtml = rowData && rowData[7] ? rowData[7] : '';
		row.child(buildActivePremiumChildRow(actionsHtml), 'monitoring-child-row').show();
		tr.addClass('shown');
	}
}

function programmitMonitoringTableLanguage() {
	return {
		"sSearchPlaceholder": "Buscar...",
		"lengthMenu": "_MENU_",
		"search": "_INPUT_",
		"oPaginate": {
			"sFirst": '<i class="fa fa-angle-double-left" aria-hidden="true"></i>',
			"sLast": '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
			"sNext": '<i class="fa fa-angle-right" aria-hidden="true"></i>',
			"sPrevious": '<i class="fa fa-angle-left" aria-hidden="true"></i>'
		},
		"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
		"sInfoEmpty": "Mostrando 0 a 0 de 0 registros",
		"sInfoFiltered": "",
		"sZeroRecords": "No se encontraron registros",
		"sEmptyTable": "No hay datos disponibles",
		"sLoadingRecords": "Cargando...",
		"sProcessing": "Procesando..."
	};
}

function uuidv4() {
  return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
    var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
    return v.toString(16);
  });
}

function v2rayrefresh(){
    $('#v2ray_id').val(uuidv4());	
}

function reload_table()
{
	if ($.fn.DataTable.isDataTable('#users-serverside')) {
		$('#users-serverside').DataTable().ajax.reload();
	}
	if ($('#freeze-logss').length && $.fn.DataTable.isDataTable('#freeze-logss')) {
		$('#freeze-logss').DataTable().ajax.reload();
	}
	if ($('#suspended-logs').length && $.fn.DataTable.isDataTable('#suspended-logs')) {
		$('#suspended-logs').DataTable().ajax.reload();
	}
	if ($('#download_serverside').length && $.fn.DataTable.isDataTable('#download_serverside')) {
		$('#download_serverside').DataTable().ajax.reload();
	}
	$('#v2ray_id').val(uuidv4());
}

function formatActivePremiumDuration(totalSeconds) {
	totalSeconds = parseInt(totalSeconds, 10);
	if (isNaN(totalSeconds) || totalSeconds < 0) {
		totalSeconds = 0;
	}
	var days = Math.floor(totalSeconds / 86400);
	var rem = totalSeconds % 86400;
	var hours = Math.floor(rem / 3600);
	rem = rem % 3600;
	var minutes = Math.floor(rem / 60);
	var seconds = rem % 60;
	return '<strong>' + days + 'd</strong> <strong>' + hours + 'h</strong> <strong>' + minutes + 'm</strong> <strong>' + seconds + 's</strong>';
}

var activePremiumCountdownReloadPending = false;

function scheduleActivePremiumCountdownReload() {
	if (activePremiumCountdownReloadPending) {
		return;
	}
	activePremiumCountdownReloadPending = true;
	setTimeout(function() {
		if ($.fn.DataTable.isDataTable('#users-serverside')) {
			$('#users-serverside').DataTable().ajax.reload(null, false);
		}
		activePremiumCountdownReloadPending = false;
	}, 1500);
}

function renderActivePremiumCountdowns(step) {
	var decrement = parseInt(step, 10);
	if (isNaN(decrement) || decrement < 0) {
		decrement = 0;
	}
	$('#users-serverside .live-countdown').each(function() {
		var $el = $(this);
		var current = parseInt($el.attr('data-seconds'), 10);
		var previous = current;
		if (isNaN(current) || current < 0) {
			current = 0;
		}
		if (decrement > 0 && current > 0) {
			current = Math.max(0, current - decrement);
			$el.attr('data-seconds', current);
			if (previous > 0 && current === 0) {
				scheduleActivePremiumCountdownReload();
			}
		}
		$el.html(formatActivePremiumDuration(current));
	});
}

setInterval(function() {
	renderActivePremiumCountdowns(1);
}, 1000);

function newexportaction(e, dt, button, config) {
    var self = this;
    var oldStart = dt.settings()[0]._iDisplayStart;
    dt.one('preXhr', function (e, s, data) {
        // Just this once, load all data from the server...
        data.start = 0;
        data.length = 2147483647;
        dt.one('preDraw', function (e, settings) {
            // Call the original action function
            if (button[0].className.indexOf('buttons-copy') >= 0) {
                $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-excel') >= 0) {
                $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
                    $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-csv') >= 0) {
                $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
                    $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
                $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
                    $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-print') >= 0) {
                $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
            }
            dt.one('preXhr', function (e, s, data) {
                // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                // Set the property to what it was before exporting.
                settings._iDisplayStart = oldStart;
                data.start = oldStart;
            });
            // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
            setTimeout(dt.ajax.reload, 0);
            // Prevent rendering of the full data to the DOM
            return false;
        });
    });
    // Requery the server with the new one-time export settings
    dt.ajax.reload();
};
//For Export Buttons available inside jquery-datatable "server side processing" - End

$('document').ready(function()
{
	table = $('#users-serverside').dataTable({
	    dom: 'frtipB',
        "buttons": [
            {
                "extend": 'copy',
                "text": '<i class="fas fa-copy" style="color: #fff;"></i>',
                "titleAttr": 'Copiar',
                "action": newexportaction
            },
            {
                "extend": 'excel',
                "text": '<i class="fas fa-file-excel" style="color: #fff;"></i>',
                "titleAttr": 'Excel',                               
                "action": newexportaction
            },
            {
                "extend": 'csv',
                "text": '<i class="fas fa-file-csv" style="color: #fff;"></i>',
                "titleAttr": 'CSV',                               
                "action": newexportaction
            },
            {
                "extend": 'pdf',
                "text": '<i class="fas fa-file-pdf" style="color: #fff;"></i>',
                "titleAttr": 'PDF',                               
                "action": newexportaction
            },
            {
                "extend": 'print',
                "text": '<i class="fas fa-print" style="color: #fff;"></i>',
                "titleAttr": 'Imprimir',
                "action": newexportaction
            }
        ],
		responsive: false,
		scrollX: false,
		autoWidth: false,
        "bProcessing": true,
        "deferRender": true,
        "bServerSide": true,
        "bStateSave": false,
        "ajax": {
            "url": "{$base_url}index.php?p=active-premium-serverside",
            "type": "POST"
        },
		"aoColumnDefs": [{
			'bSortable': false,
			'aTargets': [0,1,7]
		},{
			'className': 'monitoring-control',
			'orderable': false,
			'searchable': false,
			'width': '34px',
			'aTargets': [0]
		},{
			'className': 'monitoring-select',
			'orderable': false,
			'searchable': false,
			'width': '34px',
			'aTargets': [1]
		},{
			'width': '220px',
			'aTargets': [2]
		},{
			'width': '150px',
			'aTargets': [3]
		},{
			'width': '190px',
			'aTargets': [4]
		},{
			'width': '100px',
			'aTargets': [5]
		},{
			'width': '110px',
			'aTargets': [6]
		},{
			'visible': false,
			'searchable': false,
			'aTargets': [7]
		}],
		order: [[ 2, 'asc' ]],
		"iDisplayLength": 10,
		"aLengthMenu": [
				[10, 25, 50, 100, 99999999999999],
				[10, 25, 50, 100, "TODOS"]
		],
		"sPaginationType": "full",
		language: programmitMonitoringTableLanguage(),
		"drawCallback": function() {
			renderActivePremiumCountdowns(0);
		}
	});
	if (table && typeof table.buttons === 'function') {
		table.buttons().container()
	      .appendTo('#users-serverside_wrapper .col-md-6:eq(0)');
	}

	$('#users-serverside tbody').off('click.monitoringControl').on('click.monitoringControl', 'td.monitoring-control', function() {
		toggleActivePremiumRow($(this).closest('tr'));
	});

	$('#users-serverside tbody').off('click.monitoringCheckbox').on('click.monitoringCheckbox', 'td.monitoring-select input.chk-box', function(e) {
		e.stopPropagation();
		toggleActivePremiumRow($(this).closest('tr'));
	});
});

if ($('#freeze-logss').length) {
freeze_table = $('#freeze-logss').dataTable({
        dom: 'frtipB',
        buttons: ['copy', 'excel', 'pdf'],
		responsive: true,
        "bProcessing": true,
        "bServerSide": true,
        "bStateSave": true,
        "ajax": {
            "url": "{$base_url}index.php?p=freeze-serverside",
            "type": "POST"
        },
		"aoColumnDefs": [{
			'bSortable': false,
			'aTargets': [0,-1]
		}],
		"iDisplayLength": 10,
		"aLengthMenu": [
				[10, 25, 50, 100, 99999999999999],
				[10, 25, 50, 100, "TODOS"]
		],
		"sPaginationType": "full_numbers",
		language: programmitMonitoringTableLanguage()
	});
}
	
if ($('#suspended-logs').length) {
	suspended_table = $('#suspended-logs').dataTable({
		dom: 'frtipB',
        buttons: ['copy', 'excel', 'pdf'],
		responsive: true,
        "bProcessing": true,
        "bServerSide": true,
        "bStateSave": true,
        "ajax": {
            "url": "{$base_url}index.php?p=suspended-serverside",
            "type": "POST"
        },
		"aoColumnDefs": [{
			'bSortable': false,
			'aTargets': [0,-1]
		}],
		"iDisplayLength": 10,
		"aLengthMenu": [
				[10, 25, 50, 100, 99999999999999],
				[10, 25, 50, 100, "TODOS"]
		],
		"sPaginationType": "full_numbers",
		language: programmitMonitoringTableLanguage()
	});
}

function displayInActive() {
	if(!$('#inactive').length){
		return;
	}
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
				swal("{$siteTitle}", "No tienes acceso a este upline!", "warning");
			}
			if(data.response == 0){
				swal("{$siteTitle}", "Autorizacion fallida!", "danger");
			}
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            swal("{$siteTitle}", "Error al obtener datos por Ajax!", "info");
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
				swal("{$siteTitle}", "No tienes acceso a este upline!", "warning");
			}
			if(data.response == 0){
				swal("{$siteTitle}", "Autorizacion fallida!", "danger");
			}
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            swal("{$siteTitle}", "Error al obtener datos por Ajax!", "info");
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
		alertify.confirm('¿Deseas ajustar los creditos de este usuario?',function(e)
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
						swal("{$siteTitle}", "No tienes acceso a este upline!", "warning");
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
				swal("{$siteTitle}", "No tienes acceso a este upline!", "warning");
			}
			if(data.response == 0){
				swal("{$siteTitle}", "Autorizacion fallida!", "danger");
			}
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            swal("{$siteTitle}", "Error al obtener datos por Ajax!", "info");
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
						swal("{$siteTitle}", "Oh Oh! You dont have Authorization!", "warning");
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
	programmitResetRegisterValidation();
    $('#modal_form').modal('show'); 
	$('.modal-title').text('Agregar cliente'); 
	$('#hidden').addClass('hidden');
	$('#secret').prop('disabled', true);
	$('#resellers').prop('disabled', true);
	$('#submitRegister').html('Agregar usuario');
	$('#usname').removeClass('d-none');
	$('#role_acct').prop('disabled', false);
	$('#role_mgt').removeClass('d-none');
	$('#role_mgt2').addClass('d-none');
	$('#upline').addClass('d-none');
	$('#v2ray_id').val(uuidv4());
	window.programmitSyncRegisterShadowFields();
	if (typeof window.programmitPrepareRegisterAddUi === 'function') {
		window.programmitPrepareRegisterAddUi({
			title: 'Agregar cliente',
			submitText: 'Agregar usuario'
		});
	}
}

function edit_user(u,n)
{
	save_method = 'update';
	$('#register')[0].reset(); // reset form on modals
	$('.form-group').removeClass('has-error'); // clear error class
	$('.help-block').empty(); // clear error string
	programmitResetRegisterValidation();
	$('#hidden').removeClass('hidden');
	$('#secret').prop('disabled', false);
	$('#resellers').prop('disabled', false);
	$('#role_acct').prop('disabled', true);
	$('#client_type').prop('disabled', false);
	$('#usname').addClass('d-none');
	$('#role_mgt').addClass('d-none');
	$('#role_mgt2').removeClass('d-none');
	$('#role').prop('disabled', false);
	$('#upline').removeClass('d-none');

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
			$('#user_pass').val(data.user_pass);
			$('#register_user_pass2').val(data.user_pass);
			$('#v2ray_id').val(data.v2ray_id);
			$("option:selected",$('#client_type').val(data.client_type)).text();
			$("option:selected",$('#role').val(data.role)).text();
			$("option:selected",$('#is_active').val(data.is_active)).text();
            $('#modal_form').modal('show'); 
            $('.modal-title').text('Editar cuenta: ' + data.user_name);
			$('#submitRegister').html('Actualizar usuario: ' + data.user_name);
            
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
				var premiumstatus = 'Activo';
			}else{
				premiumstatus = 'Prueba';
			}

			if(data.status > 0){
				var status = 'Activo';
			}else{
				status = 'Inactivo';
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
			$('#v2rayid').html(data.v2ray_id);
            $('#view_modal').modal('show'); // show bootstrap modal when complete loaded
            $('.modal-title').text('Detalles de la cuenta: ' + data.username);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function unfreezed(u,n)
{
	alertify.confirm('¿Deseas descongelar este usuario?',function(e)
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
						alertify.success('Usuario descongelado correctamente');
					}else{
						alertify.error('No se pudo descongelar. Intentalo nuevamente.');
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

function unfreezedrole(u,n)
{
	alertify.confirm('¿Deseas descongelar y extender este usuario?',function(e)
	{
		if(e)
		{
			$.ajax({
				type: "POST",
				url: "{$base_url}serverside/freeze/unfreeze2.php",
				data: "uid="+u+"&ucode="+n,
				dataType: "JSON",
				cache: false,
				success: function(data) 
				{
					if(data.response == 1)
					{
						alertify.success('Usuario descongelado y extendido correctamente');
					}else{
						alertify.error('No se pudo descongelar y extender. Intentalo nuevamente.');
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
	alertify.confirm('¿Deseas eliminar todos los usuarios inactivos?',function(e)
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
						alertify.success('Usuarios inactivos eliminados correctamente');
						$('#success').html(data.success);
						reload_table();
						closed();
						displayInActive();
					}else{
						alertify.error('No se pudieron eliminar los usuarios');
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
	$('.modal-title').text('Generar cuentas de prueba');
}

function programmitResetRegisterValidation()
{
	var $form = $('#register');
	$form.find('.summary-errors').hide().find('ul').empty();
	if ($form.data('formValidation')) {
		$form.formValidation('resetForm', true);
	}
}

function programmitRegisterPasswordScore(value)
{
	var score = 0;
	value = String(value || '');
	if (value === '') {
		return null;
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

	return score;
}

function programmitUpdateRegisterPasswordMeter(value)
{
	var score = programmitRegisterPasswordScore(value);
	var $bar = $('#signuppwdMeter').find('.progress-bar');
	if (!$bar.length) {
		return;
	}

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
		default:
			$bar.html('Strong').css('width', '100%').removeClass().addClass('progress-bar progress-bar-success');
			break;
	}
}

function programmitValidateRegisterForm()
{
	var errors = [];
	var userName = $.trim($('#user_name').val() || '');
	var userPass = String($('#user_pass').val() || '');
	var v2rayId = $.trim($('#v2ray_id').val() || '');
	var registerMode = (typeof window.programmitGetRegisterMode === 'function')
		? window.programmitGetRegisterMode()
		: 'add';

	if (v2rayId === '') {
		errors.push('El UUID de V2Ray esta vacio.');
	}

	if (userName === '') {
		errors.push('El nombre de usuario esta vacio.');
	} else if (/[^_a-zA-Z0-9 -]/.test(userName)) {
		errors.push('Nombre de usuario invalido.');
	}

	if (!(typeof window.programmitRegisterUsesGeneralPassword === 'function' && window.programmitRegisterUsesGeneralPassword(registerMode))) {
		if (userPass === '') {
			errors.push('La contrasena esta vacia.');
		} else if (/[^_a-zA-Z0-9 !#$%&^~*.-]/.test(userPass)) {
			errors.push('Contrasena invalida.');
		} else if (userPass.length < 8) {
			errors.push('La contrasena debe tener al menos 8 caracteres.');
		}
	}

	return errors;
}

function programmitRenderRegisterErrors(errors)
{
	var $summary = $('#register').find('.summary-errors');
	var $list = $summary.find('ul');
	if (!$list.length) {
		$summary.html('<ul class="mb-0"></ul>');
		$list = $summary.find('ul');
	}

	$list.empty();
	if (!errors || !errors.length) {
		$summary.hide();
		return;
	}

	for (var i = 0; i < errors.length; i++) {
		$('<li/>').text(errors[i]).appendTo($list);
	}
	$summary.show();
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
			},
			prefix:
			{
				valid: true,
				message: 'The users count is not valid',
				validators: 
				{
                    notEmpty:
					{
						message: 'Prefix is required and can\'t be empty'
					},
                    stringLength: 
					{
					    min: 2,
                        max: 6,
                        message: 'Prefix must be more than 2 and less than 6'
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
			url:"{$base_url}serverside/users/generate_accounts.php",
			data: $generate_form.serialize(),
			cache: false,
			beforeSend: function() {
				generate_loader.html('Espera por favor... generando cuentas de prueba...');
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$('#success').html(data);
				$('#instant_form').modal('hide');
				$generate_form.formValidation('resetForm', true);
				reload_table();
				closed();
				displayInActive();
				alertify.error('No se pudieron generar las cuentas de prueba');
			},
			success: function(data){
				$('#success').html(data);
				$('#instant_form').modal('hide');
				$generate_form.formValidation('resetForm', true);
				reload_table();
				closed();
				displayInActive();
				alertify.success('Cuentas de prueba generadas correctamente');
			},
			complete: function(){
				generate_loader.html('');
			}
		});
	});
});

$('document').ready(function()
{
	var $form = $('#register');
	$('.summary-errors').hide();

	$(document)
		.off('input.programmitRegisterMeter', '#register #user_pass')
		.on('input.programmitRegisterMeter', '#register #user_pass', function() {
			window.programmitSyncRegisterShadowFields();
			programmitUpdateRegisterPasswordMeter(this.value);
		})
		.off('input.programmitRegisterUserName', '#register #user_name')
		.on('input.programmitRegisterUserName', '#register #user_name', function() {
			window.programmitSyncRegisterShadowFields();
			programmitRenderRegisterErrors([]);
		});

	$form.off('submit.programmitRegister').on('submit.programmitRegister', function(e) {
		e.preventDefault();

		var $currentForm = $(this);
		var $modal = $('#modal_form');
		var url = (save_method == 'add')
			? "{$base_url}serverside/forms/adduser.php"
			: "{$base_url}serverside/forms/edituser.php";
		var successTitle = (save_method == 'add') ? 'Cliente agregado' : 'Cliente actualizado';
		var renderBanner = function(response) {
			var bannerHtml = '';
			if (response && response.fragment) {
				bannerHtml = response.fragment;
			} else if (response && response.message) {
				var bannerType = response.ok ? 'success' : 'danger';
				bannerHtml = "<div class='alert alert-" + bannerType + "'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button><strong>" + window.programmitEscapeHtml(response.message) + "</strong></div>";
			}
			$('#success').html(bannerHtml);
		};

		window.programmitSyncRegisterShadowFields();
		programmitUpdateRegisterPasswordMeter($('#user_pass').val());

		var errors = programmitValidateRegisterForm();
		if (errors.length) {
			programmitRenderRegisterErrors(errors);
			renderBanner({ ok: false, message: errors[0] });
			if (typeof window.swal === 'function') {
				window.swal({
					type: 'error',
					title: 'No se pudo guardar',
					text: errors[0]
				});
			}
			return false;
		}

		programmitRenderRegisterErrors([]);
		window.programmitApplyInlineFormResponse($currentForm, { ok: true, message: '' });
		$.ajax({
			type: "POST",
			url: url,
			data: $currentForm.serialize(),
			beforeSend: function() {
				loading.show();
			},
			error: function(jqXHR) {
				var responseHtml = (jqXHR && jqXHR.responseText) ? jqXHR.responseText : "<div class='alert alert-danger'><strong>No se pudo procesar la solicitud.</strong></div>";
				var response = programmitHandleFormSaveResponse(responseHtml, {
					successTitle: successTitle,
					errorTitle: 'No se pudo guardar'
				});
				renderBanner(response);
				window.programmitApplyInlineFormResponse($currentForm, response);
			},
			success: function(data){
				var response = programmitHandleFormSaveResponse(data, {
					successTitle: successTitle,
					errorTitle: 'No se pudo guardar'
				});
				renderBanner(response);
				window.programmitApplyInlineFormResponse($currentForm, response);
				if (response.ok) {
					$modal.off('hidden.bs.modal.programmitRegisterReset').one('hidden.bs.modal.programmitRegisterReset', function() {
						if ($currentForm.length && $currentForm[0]) {
							$currentForm[0].reset();
						}
						programmitResetRegisterValidation();
						programmitUpdateRegisterPasswordMeter('');
						window.programmitSyncRegisterShadowFields();
					});
					$modal.modal('hide');
					reload_table();
				}
			},
			complete: function(){
				loading.hide();
			}
		});

		return false;
	});
});

function suspendSubmitted()
{
	$(".ajs-header").html('{$siteTitle} - Mensaje de alerta');
	alertify.confirm('¿Deseas suspender los usuarios seleccionados?',function(){
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
				alertify.success('Usuarios suspendidos correctamente');
			},
			error: function(data){
				$("#success").html(data);
				reload_table();
				$("#suspendSubmit").prop('disabled', true);
				$("#deleteSubmit").prop('disabled', true);
				$("#freezeSubmit").prop('disabled', true);
				$(".chk-box").prop('checked', false);
				$(".select-all").prop('checked', false);
				alertify.error('No se pudieron suspender los usuarios');
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
	$(".ajs-header").html('{$siteTitle} - Mensaje de alerta');
	alertify.confirm('¿Deseas eliminar los usuarios seleccionados?',function(){
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
			},
			error: function(data){
				$("#success").html(data);
				reload_table();
				$("#suspendSubmit").prop('disabled', true);
				$("#deleteSubmit").prop('disabled', true);
				$("#freezeSubmit").prop('disabled', true);
				$(".chk-box").prop('checked', false);
				$(".select-all").prop('checked', false);
				alertify.error('No se pudieron eliminar los usuarios');
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
	$(".ajs-header").html('{$siteTitle} - Mensaje de alerta');
	alertify.confirm('¿Deseas congelar los usuarios seleccionados?',function(){
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
				alertify.success('Usuarios congelados correctamente');
			},
			error: function(data){
				$("#success").html(data);
				reload_table();
				$("#suspendSubmit").prop('disabled', true);
				$("#deleteSubmit").prop('disabled', true);
				$("#freezeSubmit").prop('disabled', true);
				$(".chk-box").prop('checked', false);
				$(".select-all").prop('checked', false);
				alertify.error('No se pudieron congelar los usuarios');
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
	$(".ajs-header").html('{$siteTitle} - Mensaje de alerta');
	alertify.confirm('¿Deseas reactivar los usuarios seleccionados?',function(){
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
				alertify.success('Usuarios reactivados correctamente');
			},
			error: function(data){
				$("#success2").html(data);
				reload_table();
				$("#suspendRecovery").prop('disabled', true);
				$("#deleteSubmit2").prop('disabled', true);
				$(".chk-boxs").prop('checked', false);
				$(".select-alls").prop('checked', false);
				alertify.error('No se pudieron reactivar los usuarios');
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
	$(".ajs-header").html('{$siteTitle} - Mensaje de alerta');
	alertify.confirm('¿Deseas eliminar los usuarios seleccionados?',function(){
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
			},
			error: function(data){
				$("#success").html(data);
				reload_table();
				$("#suspendRecovery").prop('disabled', true);
				$("#deleteSubmit2").prop('disabled', true);
				$(".chk-boxs").prop('checked', false);
				$(".select-alls").prop('checked', false);
				alertify.error('No se pudieron eliminar los usuarios');
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
	$('.modal-title').text('Convertir duracion');
	displayConv();
}

function conversion() 
{
	var conversion_loader = $('#conversion_loader');
	var $convertform = $('#convertForm');
	alertify.confirm('Deseas continuar con la conversion?',function(e)
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

function selfreload()
{
	var c;
	$.ajax({
        url: "{$base_url}serverside/users/get-avatar.php",
		data: "self",
        dataType: "JSON",
		cache: false,
        success: function(data)
        {
			$('#formSelf')[0].reset();
			$('.form-group').removeClass('has-error');
			$('#vouchers_loader').html('');
			$('#formSelf').formValidation('resetForm', true);
			$('#selfreload_form').modal('show');
			$('.modal-title').text(data.self+' | Usuario: {$user_name_2}');
	    }
    });
}

////////////////self

$(document).ready(function($){
	$('#formSelf').formValidation
	({
		framework: 'bootstrap',
		excluded: ':disabled',
		icon: null,
		fields: 
		{
			qty:
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
		var vouchers_loader = $('#vouchers_loader');
		var $form = $(e.target);
		alertify.confirm('Deseas recargar un voucher a tu cuenta?',function(e)
		{
			if(e)
			{
				$.ajax({
					url: "{$base_url}serverside/duration/selfreload.php",
					type: "POST",
					data: $form.serialize(),
					cache: false,
					beforeSend: function() {
						vouchers_loader.html('Espera por favor... procesando datos...');
					},
					complete: function(){
						vouchers_loader.html('');
					},
					success: function(data) {
						$('#success').html(data);
						$('#selfreload_form').modal('hide');
						$form.formValidation('resetForm', true);
						reload_table();
						closed();
						displayAvatar();
					},
					error: function(jqXHR, textStatus, errorThrown){
						$('#success').html(data);
						reload_table();
						closed();
						displayAvatar();
					}
				});
			}
		},function(){
			alertify.error('Cancelado');
			closed();
			displayAvatar();
		});
	});
});
////////

function displayConv2() {
	$.ajax({
		type: "POST",
		url: "{$base_url}serverside/duration/info2.php",
		data: $('#formDuration').serialize(),
		success: function(data){
			$("#conv2").html(data);
		}
	});
}
$(".conv2").change( displayConv2 );
displayConv2();
</script>
