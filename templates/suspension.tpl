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
<title>{$siteTitle} - Suspension</title>
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
				Suspended / Unsuspended List
			</h1>
			<ol class="breadcrumb">
				<li><a href="{$base_url}"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active">Suspended</li>
			</ol>
		</section>
	
		<!-- Main content -->
		<section class="content">
			<!-- Default box -->
			<div class="box">
				<div class="box-body">
					<div class="col-md-12">
						<div class="padding-20">
							<div class="alert alert-info bg-purple">
								<p>
									<h4><i class="glyphicon glyphicon-attention"></i> WHEN TO REACTIVATE YOUR CLIENTS</h4><br>
									<ul class="list-group">
										<li>1ST OFFENSE - AFTER 3 DAYS OR 72 HOURS</li>
										<li>2ND OFFENSE - AFTER 7 DAYS OR 168 HOURS</li>
										<li>3RD OFFENSE - "SORRY- AUTOMATIC BANNED"</li>
									</ul><br>
									<h4> STRICT COMPLIANCE! </h4>
								</p>
							</div>
						</div>
						<fieldset class="padding-20">
							<legend class="text-center bg-purple">
								<h4 class="text-white">
									<div class="panel-heading">
										<i class="glyphicon glyphicon-user"></i> User's Suspended List
									</div>
								</h4>
							</legend>
							<table id="" class="table table-striped table-bordered display" cellspacing="0" width="100%">
								<thead>
									<tr>
										<th class="text-center">Username</th>
										<th class="text-center">Suspended by</th>
										<th class="text-center">Offense</th>
										<th class="text-center">Suspended Date</th>
									</tr>
								</thead>
								<tbody>
									{foreach item=i from=$suspended}
										{$i}
									{/foreach}
								</tbody>
							</table>
						</fieldset>

						<fieldset class="padding-20">
							<legend class="text-center bg-purple">
								<h4 class="text-white">
									<div class="panel-heading">
										<i class="glyphicon glyphicon-user"></i> User's Unsuspended List
									</div>
								</h4>
							</legend>
							<table id="" class="table table-striped table-bordered display" cellspacing="0" width="100%">
								<thead>
									<tr>
										<th class="text-center">Username</th>
										<th class="text-center">Unsuspended by</th>
										<th class="text-center">Offense</th>
										<th class="text-center">Suspended Date</th>
										<th class="text-center">Unuspended Date</th>
									</tr>
								</thead>
								<tbody>
									{foreach item=i from=$unsuspended}
										{$i}
									{/foreach}
								</tbody>
							</table>
						</fieldset>
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
		}
	});
});
</script>
{include file='apps/liveclock.tpl'}
</body>
</html>