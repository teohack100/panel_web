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
<title>{$siteTitle} - Online Update</title>
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
				Editor de actualizaciones en línea
			</h1>
			<ol class="breadcrumb">
				<li><a href="{$base_url}"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active">Actualización en línea</li>
			</ol>
		</section>
	
		<!-- Main content -->
		<section class="content">
			<!-- Default box -->
			<div class="box">
				<div class="box-body">
					<div class="col-md-12">
						<form id="GUIFrm" name="GUIFrm" novalidate>
							<textarea id="guicode" class="form-control" name="guicode" rows="100%" required>{$editor}</textarea>
							<input type="hidden" id="submitted" name="submitted" value="GUI Update">
							<button type="button" class="btn btn-success btn-block" id="guiUpdate" name="guiUpdate" onclick="codeUpdate()">
								<i class="glyphicon glyphicon-edit"></i> Actualizar
							</button>
						</form>
						<div id="success"></div>
					</div>
				</div>
			</div>
		</section>
</div>
{include file='js/global_js.tpl'}
{include file='js/jqueryui_js.tpl'}
{include file='js/datatables_js.tpl'}
<script>
function displayVals() {
	$.ajax({
		type: "GET",
		url: "{$base_url}serverside/users/get_gui_update.php",
		success: function(data){
			$('textarea').val(data);
		}
	});
}
displayVals();

function codeUpdate()
{
	$(".ajs-header").html('{$siteTitle} - Alert Message');
	alertify.confirm('Are you sure? Do you want to update?',function(){
		$.ajax({
			url: "{$base_url}/gui_update.php",
			type: "POST",
			data: $('#GUIFrm').serialize(),
			cache: false,
			success: function(response)
			{
				alertify.success('Successfully Updated!...');
				$('#success').html(response);
				displayVals();
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alertify.error('Failed to Update!...');
				$('#success').html(response);
				displayVals();
			}
		});
	},function(){
		alertify.error('Declined');
		displayVals();
	}).setting('labels',{literal}{'ok':'Accept', 'cancel': 'Decline'}{/literal});	
}
</script>
{include file='apps/liveclock.tpl'}
</body>
</html>