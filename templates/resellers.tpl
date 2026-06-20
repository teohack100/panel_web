<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="title" content="{$siteTitle} - My Account">
<meta name="description" content="Fast, Stable and Secure VPN Service">
<meta name="keywords" content="{$siteTitle}">
<meta name="author" content="Lenz Scott Kennedy">
<meta name="owner" content="Firenet Philippines">
<meta name="copyright" content="{$siteTitle}">
<title>{$siteTitle} - Authorized Resellers</title>
<link rel="apple-touch-icon" href="{$base_url}logo/favicon.ico">
<link rel="shortcut icon" href="{$base_url}logo/favicon.ico" type="image/x-icon">
<link rel="icon" href="{$base_url}logo/favicon.png">
<link rel="icon" sizes="32x32" href="{$base_url}logo/favicon-32x32.png">
<link rel="icon" sizes="57x57" href="{$base_url}logo/favicon-57x57.png">
<link rel="icon" sizes="72x72" href="{$base_url}logo/favicon-72x72.png">
<link rel="icon" sizes="76x76" href="{$base_url}logo/favicon-76x76.png">
<link rel="icon" sizes="114x114" href="{$base_url}logo/favicon-114x114.png">
<link rel="icon" sizes="120x120" href="{$base_url}logo/favicon-120x120.png">
<link rel="icon" sizes="144x144" href="{$base_url}logo/favicon-144x144.png">
<link rel="icon" sizes="152x152" href="{$base_url}logo/favicon-152x152.png">

<meta name="msapplication-TileColor" content="#000000">	
<meta name="msapplication-TileImage" content="{$base_url}logo/favicon-144x144.png">
<meta name="application-name" content="{$siteTitle}">
{include file='css/global_css.tpl'}
{include file='css/style_css.tpl'}
{include file='css/jqueryui_css.tpl'}
{include file='css/datatables_css.tpl'}
</head>
<body class="skin-purple-light layout-boxed sidebar-mini">
<!-- Site wrapper -->
{include file='apps/navigation.tpl'}
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>
				All Authorized Reseller
			</h1>
			<ol class="breadcrumb">
				<li><a href="{$base_url}"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active">Authorized Resellers</li>
			</ol>
		</section>
	
		<!-- Main content -->
		<section class="content">
			<!-- Default box -->
			<div class="box">
				<div class="box-body">
					<div class="row">
						<fieldset class="padding-20">
							<legend class="text-center bg-purple">
								<h4 class="text-white">
									<div class="panel-heading">
										<span class="capitalize">Reseller List</span>
									</div>
								</h4>
							</legend>
							<table id="bootstrap-table" class="table table-striped table-bordered display" cellspacing="0" width="100%">
								<thead>
									<tr>
										<th class="text-center"></th>
										<th class="text-center">Full Name</th>
										<th class="text-center">Username</th>
										<th class="text-center">Credits</th>
										<th class="text-center">Status</th>
										<th class="text-center">Facebook</th>
									</tr>
								</thead>
								<tbody>
									{foreach item=i from=$reseller}
										{$i}
									{/foreach}
								</tbody>
							</table>
						</fieldset>
					</div>
				</div>
		</section>
		<section class="content">
			<!-- Default box -->
			<div class="box">
				<div class="box-body">
					<div class="row">
						<fieldset class="padding-20">
							<legend class="text-center bg-purple">
								<h4 class="text-white">
									<div class="panel-heading">
										<span class="capitalize">Reseller List</span>
									</div>
								</h4>
							</legend>
							<table id="bootstrap-table" class="table table-striped table-bordered display" cellspacing="0" width="100%">
								<thead>
									<tr>
										<th class="text-center"></th>
										<th class="text-center">Full Name</th>
										<th class="text-center">Username</th>
										<th class="text-center">Credits</th>
										<th class="text-center">Status</th>
										<th class="text-center">Facebook</th>
									</tr>
								</thead>
								<tbody>
									{foreach item=i from=$reseller}
										{$i}
									{/foreach}
								</tbody>
							</table>
						</fieldset>
					</div>
				</div>
		</section>
		
</div>
{include file='js/global_js.tpl'}
{include file='js/jqueryui_js.tpl'}
{include file='js/datatables_js.tpl'}
<script>
function format(address, number, payment) {
    return	'<div class="slider">'+
				'<table cellpadding="5" cellspacing="5" border="0" style="padding-left:50px;">'+
						'<tr>'+
							'<td>Contact Address: </td>'+
							'<td>'+ address +'</td>'+
						'</tr>'+
						'<tr>'+
							'<td>Contact Number: </td>'+
							'<td>'+ number +'</td>'+
						'</tr>'+
				'</table>'+
			'</div>';			
}

var table;
$(document).ready(function() {
	table = $('table.display').DataTable({
	"aoColumnDefs": [{
	'bSortable': false,
	'aTargets': [0],
	"class": 'details-control',
	"orderable": false,
	"defaultContent": ''
	}],
	"iDisplayLength": 10,
	"aLengthMenu": [
		[10, 25, 50, 100, -1],
		[10, 25, 50, 100, "All"]
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
		
	$('table tbody').on('click', 'td.details-control', function () {
		var tr = $(this).closest('tr');
		var row = table.row(tr);
			
		if ( row.child.isShown() ) {
			$('div.slider',row.child()).slideUp( function () {
				row.child.hide();
				tr.removeClass('shown');
			});
		}else{
			row.child( format(tr.data('child-address'),
			tr.data('child-number')),'no-padding' ).show();
			tr.addClass('shown');
	 
			$('div.slider', row.child()).slideDown();
		}
	});
});
</script>
{include file='apps/liveclock.tpl'}
</body>
</html>