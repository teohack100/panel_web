	<!DOCTYPE html>
<script language="javascript">
           var message="This function is not allowed here.";
           function clickIE4(){
                 if (event.button==2){
                     alert(message);
                     return false;
                 }
           }
           function clickNS4(e){
                if (document.layers||document.getElementById&&!document.all){
                        if (e.which==2||e.which==3){
                                  alert(message);
                                  return false;
                        }
                }
           }
           if (document.layers){
                 document.captureEvents(Event.MOUSEDOWN);
                 document.onmousedown=clickNS4;
           }
           else if (document.all&&!document.getElementById){
                 document.onmousedown=clickIE4;
           }
           document.oncontextmenu=new Function("alert(message);return false;")
</script>



<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script type="text/javascript">
$(function() {
    $(this).bind("contextmenu", function(e) {
        e.preventDefault();
    });
}); 
</script>
<html class="no-js" lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="title" content="Ushatel">
<meta name="description" content="Ushatel page not found">
<meta name="keywords" content="Ushatel page not found">
<meta name="author" content="Allen Alvarez">
<meta name="owner" content="Ushatel">
<meta name="copyright" content="Allen Alvarez">
<title>Ushatel - Page Not Found</title>
<link rel="apple-touch-icon" href="https://www.ushatel.co/logo/favicon.ico">
<link rel="shortcut icon" href="https://www.ushatel.co/logo/favicon.ico" type="image/x-icon">
<link rel="icon" href="https://www.ushatel.co/logo/favicon.png">
<link rel="icon" sizes="57x57" href="https://www.ushatel.co/logo/favicon-32x32.png">
<link rel="icon" sizes="57x57" href="https://www.ushatel.co/logo/favicon-57x57.png">
<link rel="icon" sizes="72x72" href="https://www.ushatel.co/logo/favicon-72x72.png">
<link rel="icon" sizes="76x76" href="https://www.ushatel.co/logo/favicon-76x76.png">
<link rel="icon" sizes="114x114" href="https://www.ushatel.co/logo/favicon-114x114.png">
<link rel="icon" sizes="120x120" href="https://www.ushatel.co/logo/favicon-120x120.png">
<link rel="icon" sizes="144x144" href="https://www.ushatel.co/logo/favicon-144x144.png">
<link rel="icon" sizes="152x152" href="https://www.ushatel.co/logo/favicon-152x152.png">

<meta name="msapplication-TileColor" content="#FFFFFF">	
<meta name="msapplication-TileImage" content="https://www.ushatel.co/logo/favicon-144x144.png">
<meta name="application-name" content="Ushatel">
    <!-- Main -->
    <link rel="stylesheet" type="text/css" href="https://www.ushatel.co/bootstrap/main/dist/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="https://www.ushatel.co/bootstrap/main/assets/css/ie10-viewport-bug-workaround.css">
     <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="bootstrap/main/assets/assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="https://www.ushatel.co/bootstrap/main/assets/js/ie-emulation-modes-warning.js"></script>
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	
	<!-- Stylesheets -->
    <link rel="stylesheet" type="text/css" href="https://www.ushatel.co/bootstrap/dashboard/dist/css/FrontAdmin.css">
    <link rel="stylesheet" type="text/css" href="https://www.ushatel.co/bootstrap/dashboard/dist/css/skins/_all-skins.css">
	
	<!-- Fonts -->
    <link rel="stylesheet" type="text/css" href="https://www.ushatel.co/bootstrap/assets/fonts/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="https://www.ushatel.co/bootstrap/assets/fonts/web-icons/web-icons.css">
	<link rel="stylesheet" type="text/css" href="https://www.ushatel.co/bootstrap/assets/alertifyjs/css/alertify.css">
	<link rel="stylesheet" type="text/css" href="https://www.ushatel.co/bootstrap/assets/alertifyjs/css/themes/default.css">
    <!-- Global -->
	<link rel="stylesheet" type="text/css" href="https://www.ushatel.co/bootstrap/global/css/global.css" media="screen">

    

</head>
<body class="hold-transition skin-purple-light layout-boxed">
<!-- Content Header (Page header) -->
	<section class="content-header">
		<ol class="breadcrumb">
			<li><a href="https://www.ushatel.co/"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="active">500</li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">
		<div class="error-page">
			<div>
				<h1 class="headline text-red text-center"><i class="fa fa-spin fa-refresh text-red"></i> 500</h1>
			</div>
			<div class="text-center">
				<h3> Page Not Found!.</h3>
				<p>
					We could not find the page you were looking for.
					Meanwhile, you may <a href="https://www.ushatel.co/">return to dashboard</a>
				</p>
			</div>
		</div>
	</section>
	<!-- Core  -->
	<script src="https://www.ushatel.co/bootstrap/assets/jquery/jquery-3.1.1.js"></script>
	<script src="https://www.ushatel.co/bootstrap/dashboard/plugins/jQuery/jquery-2.2.3.min.js"></script>
	<script src="https://www.ushatel.co/bootstrap/main/dist/js/bootstrap.js"></script>
	<script src="https://www.ushatel.co/bootstrap/dashboard/plugins/slimScroll/jquery.slimscroll.js"></script>
	<script src="https://www.ushatel.co/bootstrap/dashboard/plugins/fastclick/fastclick.js"></script>
	<script src="https://www.ushatel.co/bootstrap/dashboard/dist/js/app.js"></script>
 
	<!-- Custom File Upload -->
    <script src="https://www.ushatel.co/bootstrap/assets/custom.fileupload/custom.fle_upload.js"></script>
	<script src="https://www.ushatel.co/bootstrap/assets/jqueryform/jquery.form.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script type="text/javascript" src="https://www.ushatel.co/bootstrap/main/assets/js/ie10-viewport-bug-workaround.js"></script>
	<script src="https://www.ushatel.co/bootstrap/assets/alertifyjs/alertify.js"></script>
</body>
</html>