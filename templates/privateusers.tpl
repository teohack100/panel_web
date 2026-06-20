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
<title>{$siteTitle} - Private Users</title>
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
<div class="lenz-wrapper-boxed">

{include file='apps/navigation.tpl'}

	<!-- Content Wrapper. Contains page content -->

		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>
				Private Users Connected
			</h1>
			<ol class="breadcrumb">
				<li><a href="{$base_url}"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active">Private Users</li>
			</ol>
		</section>
	
		<!-- Main content -->
		<section class="content">
			<!-- Default box -->
			<div class="box">
				<div class="box-body">
					<div class="row">
						<div class="col-md-12 alert alert-info bg-purple">
							<div class="page-header">
								<h1 class="page-title text-success text-center">
									<i class="glyphicon glyphicon-exclamation-sign"></i> ATTENTION! 
								</h1> 
									
								<p>
									<h4 class="text-warning text-center">FOR DUAL/MULTI LOGIN USER IN ONE ACCOUNT <BR> AUTOMATIC SUSPENSION FOR 3 DAYS WITHOUT PRIOR NOTICE <BR> FOR ACCOUNT ACTIVATION PLEASE CONTACT YOUR RESELLER. -TNX- </h4>
								</p>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
								<table class="table table-striped table-bordered display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<td colspan="{if $user_id_2 == 1}6{else}5{/if}" class="text-center">ALL Premium Servers</td>
										</tr>
										<tr>
											<td class="text-center">Username</td>
										{if $user_id_2 == 1}
											<td class="text-center">Real IP</td>
										{/if}
											<td class="text-center">Received</td>
											<td class="text-center">Sent</td>
											<td class="text-center">Since Connected</td>
											<td class="text-center">Update</td>
											<td class="text-center">Slot</td>
											<td class="text-center">Server</td>
										</tr>
									</thead>
									<tbody>
										{$stats}
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>


</div>
{include file='js/global_js.tpl'}
{include file='js/datatables_js.tpl'}
<script>
$(document).ready(function(){
	$('table.display').dataTable({
		"iDisplayLength": -1,
		"aLengthMenu": [
			[5, 10, 25, 50, -1],
			[5, 10, 25, 50, "All"]
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
		},
		"sDom": 'l<"dt-panelmenu clearfix"Tfr>t<"dt-panelfooter clearfix"ip>',
		"oTableTools": {
		"sSwfPath": "{$base_url}bootstrap/assets/datatables/extensions/TableTools/swf/copy_csv_xls_pdf.swf"
		}
	});
});
</script>
{include file='apps/liveclock.tpl'}
</body>
</html>