<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="title" content="{$siteTitle}">
<meta name="description" content="{$siteTitle} download page">
<meta name="keywords" content="{$siteTitle} download page">
<meta name="author" content="Jhoe Angeleye">
<meta name="owner" content="{$siteTitle}">
<meta name="copyright" content="Jhoe Angeleye">
<title>Download Page</title>
<link rel="apple-touch-icon" href="{$base_url}logo/favicon.ico">
<link rel="shortcut icon" href="{$base_url}logo/favicon.ico" type="image/x-icon">
<link rel="icon" href="{$base_url}logo/favicon.png">
<link rel="icon" sizes="57x57" href="{$base_url}logo/favicon-32x32.png">
<link rel="icon" sizes="57x57" href="{$base_url}logo/favicon-57x57.png">
<link rel="icon" sizes="72x72" href="{$base_url}logo/favicon-72x72.png">
<link rel="icon" sizes="76x76" href="{$base_url}logo/favicon-76x76.png">
<link rel="icon" sizes="114x114" href="{$base_url}logo/favicon-114x114.png">
<link rel="icon" sizes="120x120" href="{$base_url}logo/favicon-120x120.png">
<link rel="icon" sizes="144x144" href="{$base_url}logo/favicon-144x144.png">
<link rel="icon" sizes="152x152" href="{$base_url}logo/favicon-152x152.png">

<meta name="msapplication-TileColor" content="#FFFFFF">	
<meta name="msapplication-TileImage" content="{$base_url}logo/favicon-144x144.png">
<meta name="application-name" content="{$siteTitle}">
{include file='css/global_css.tpl'}
{include file='css/style_css.tpl'}
</head>
<body class="hold-transition skin-main sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">

{include file='apps/navigation.tpl'}
{if $user_id_2 >= 0 || $user_level_2 == 'superadmin' || $user_level_2 == 'reseller'}
	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>
				Download & Tutorials
			</h1>
			<ol class="breadcrumb">
				<li><a href="{$base_url}"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active">download</li>
			</ol>
		</section>
	
		<!-- Main content -->
		<section class="content">
			<!-- Default box -->
			<div class="box">
				<div class="box-body">
					{foreach item=i from=$download} {$i} {/foreach}
				</div>
			</div>
		</section>
	</div>
{include file='apps/footer.tpl'}
{/if}
</div>

{include file='js/global_js.tpl'}
{include file='apps/liveclock.tpl'}
</body>
</html>