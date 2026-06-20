<script>
var table;

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

function reload_table()
{
	$('#accountrecovery').DataTable().ajax.reload();
}

function recover() 
{
	$(".ajs-header").html('{$siteTitle} - Mensaje de alerta');
	alertify.confirm('¿Deseas recuperar este usuario?',function(){
		$.ajax({
			type: "POST",
			url: "{$base_url}serverside/ban/accountrecovery.php",
			data: $('#recovery_frm').serialize(),
			success: function(data) {
				$("#success").html ( data );
				$('#accountrecovery').DataTable().ajax.reload();
				$(".select-all").prop('checked', false);
				$(".chk-box").prop('checked', false);
				$("#recover").prop('disabled', true);
				alertify.success('Usuario recuperado correctamente');
			},
			error: function(data) {	
				$("#success").html ( data );
				$(".select-all").prop('checked', false);
				$(".chk-box").prop('checked', false);
				$("#recover").prop('disabled', true);
				alertify.error('No se pudo recuperar el usuario');
			}
		});
	},function(){
		alertify.error('Cancelado');
		$("#recover").prop('disabled', true);
		$(".chk-box").prop('checked', false);
		$(".select-all").prop('checked', false);
	}).setting('labels',{literal}{'ok':'Aceptar', 'cancel': 'Cancelar'}{/literal});
}

$('document').ready(function()
{
    $('.select-all').click(function(event) {  //on click
        if(this.checked) { // check select status
            $('.chk-box').each(function() { //loop through each checkbox
                this.checked = true;  //select all checkboxes with class ".chk-box"    
				$("#recover").prop('disabled', false);
            });
        }else{
            $('.chk-box').each(function() { //loop through each checkbox
                this.checked = false; //deselect all checkboxes with class ".chk-box"
            }); 
			if($(".select-all").prop('checked') == false){
				$("#recover").prop('disabled', true);
			}			
        }
    });
	
	if($(".select-all").prop('checked') == false){
		$("#recover").prop('disabled', true);
	}
	
	$('body').delegate('.chk-box','click',function(event){	
		if ($('.chk-box').is(':checked') == true){		
			$("#recover").prop('disabled', false);
			console.log('checked');
		} else {
			$("#recover").prop('disabled', true);		
			console.log('unchecked');
		}	
	});

	table = $('#accountrecovery').dataTable({
		dom: 'frtipB',
        buttons: ['copy', 'excel', 'pdf'],
		responsive: true,
        "bProcessing": true,
        "bServerSide": true,
        "bStateSave": true,
        "ajax": {
            "url": "{$base_url}index.php?p=deleted-serverside",
            "type": "POST"
        },
		"aoColumnDefs": [{
			'bSortable': true,
			'aTargets': [0]
		}],
		order: [[ 0, 'desc' ], [ 0, 'asc' ]],
		"iDisplayLength": 10,
		"aLengthMenu": [
				[10, 25, 50, 100, 99999999999999],
				[10, 25, 50, 100, "TODOS"]
		],
		"sPaginationType": "full",
		language: programmitMonitoringTableLanguage()
	});
});
</script>
