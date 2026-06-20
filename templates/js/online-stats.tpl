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