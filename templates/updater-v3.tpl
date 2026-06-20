<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8" />
        <title>{$siteTitle} - Actualizador #3</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A premium admin dashboard template by Mannatthemes" name="description" />
        <meta content="Mannatthemes" name="author" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="{$base_url}firenet/assets/images/v.png">

        <link href="{$base_url}firenet/assets/plugins/jvectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet">

        <!-- App css -->
        <link href="{$base_url}firenet/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="{$base_url}firenet/assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="{$base_url}firenet/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
        <link href="{$base_url}firenet/assets/css/style.css" rel="stylesheet" type="text/css" />

        <!-- DataTables -->
        <link href="{$base_url}firenet/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
        <link href="{$base_url}firenet/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
        
        <!-- Responsive datatable examples -->
        <link href="{$base_url}firenet/assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" /> 
        
        <!-- Sweet Alert -->
        <link href="{$base_url}firenet/assets/plugins/sweet-alert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
        <link href="{$base_url}firenet/assets/plugins/animate/animate.css" rel="stylesheet" type="text/css">
        
        <link rel="stylesheet" type="text/css" href="{$base_url}bootstrap/assets/alertifyjs/css/alertify.css">
	    <link rel="stylesheet" type="text/css" href="{$base_url}bootstrap/assets/alertifyjs/css/themes/bootstrap.rtl.css">
        
        {include file='css/custom_css.tpl'}
        {include file='css/extra_button.tpl'}
        {include file='css/formvalidation_css.tpl'}
</head>
<body>
{include file='apps/topnav.tpl'}
<!-- Site wrapper -->
<div class="page-wrapper">
{include file='apps/sidenavi.tpl'}
    <!-- Page Content-->
            <div class="page-content">

                <div class="container-fluid">
                    <!-- Page-Title -->
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="page-title-box">
                                <div class="float-right">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">{$siteTitle}</a></li>
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Actualizador</a></li>
                                        <li class="breadcrumb-item active">Actualizador #3</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Actualizador #3</h4>
                            </div><!--end page-title-box-->
                        </div><!--end col-->
                    </div>
                    <!-- end page title end breadcrumb -->
                    
                    <div class="row">
                        <div class="col-12">
                            <div id="success"></div>
                            <div class="card">
                                <div class="col-md-12">
            						<form id="GUIFrm" name="GUIFrm" novalidate>
            							<h6>Link: <a class="text-success" href="{$base_url}update-v3" target="_blank">{$base_url}update-v3</a></h6>
            							<hr>
            							<textarea id="guicode" class="form-control" name="guicode" rows="100%" required>{$editor}</textarea>
            							<input type="hidden" id="submitted" name="submitted" value="GUI Update">
            							<hr>
            							<button type="button" class="btn btn-success btn-block mb-4" id="guiUpdate" name="guiUpdate" onclick="codeUpdate()">
            								<i class="glyphicon glyphicon-edit"></i> Actualizar
            							</button>
            						</form>
            					</div>
                            </div>
                        </div> <!-- end col -->
                    </div> <!-- end row -->

                </div><!-- container -->

                {include file='apps/footer.tpl'}
                <!--end footer-->
            </div>
            <!-- end page content -->
        </div>
        <!-- end page-wrapper -->
<!-- jQuery  -->
        <script src="{$base_url}firenet/assets/js/jquery.min.js"></script>
        <script src="{$base_url}firenet/assets/js/bootstrap.bundle.min.js"></script>
        <script src="{$base_url}firenet/assets/js/metisMenu.min.js"></script>
        <script src="{$base_url}firenet/assets/js/waves.min.js"></script>
        <script src="{$base_url}firenet/assets/js/jquery.slimscroll.min.js"></script>

        <script src="{$base_url}firenet/assets/plugins/moment/moment.js"></script>
        <script src="{$base_url}firenet/assets/plugins/apexcharts/apexcharts.min.js"></script>
        <script src="{$base_url}firenet/assets/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
        <script src="{$base_url}firenet/assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
        <script src="{$base_url}firenet/assets/pages/jquery.eco_dashboard.init.js"></script>

        <!-- Required datatable js -->
        <script src="{$base_url}firenet/assets/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="{$base_url}firenet/assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>
        <!-- Buttons examples -->
        <script src="{$base_url}firenet/assets/plugins/datatables/dataTables.buttons.min.js"></script>
        <script src="{$base_url}firenet/assets/plugins/datatables/buttons.bootstrap4.min.js"></script>
        <script src="{$base_url}firenet/assets/plugins/datatables/jszip.min.js"></script>
        <script src="{$base_url}firenet/assets/plugins/datatables/pdfmake.min.js"></script>
        <script src="{$base_url}firenet/assets/plugins/datatables/vfs_fonts.js"></script>
        <script src="{$base_url}firenet/assets/plugins/datatables/buttons.html5.min.js"></script>
        <script src="{$base_url}firenet/assets/plugins/datatables/buttons.print.min.js"></script>
        <script src="{$base_url}firenet/assets/plugins/datatables/buttons.colVis.min.js"></script>
        <!-- Responsive examples -->
        <script src="{$base_url}firenet/assets/plugins/datatables/dataTables.responsive.min.js"></script>
        <script src="{$base_url}firenet/assets/plugins/datatables/responsive.bootstrap4.min.js"></script>
        <script src="{$base_url}firenet/assets/pages/jquery.datatable.init.js"></script>
        
        <!-- App js -->
        <script src="{$base_url}firenet/assets/js/jquery.core.js"></script>
        <script src="{$base_url}firenet/assets/js/app.js"></script>
        
        <!-- Sweet-Alert  -->
        <script src="{$base_url}firenet/assets/plugins/sweet-alert2/sweetalert2.min.js"></script>
        <script src="{$base_url}firenet/assets/pages/jquery.sweet-alert.init.js"></script>
        
        <script src="{$base_url}bootstrap/assets/alertifyjs/alertify.js"></script>
        <script src="{$base_url}bootstrap/dashboard/dist/js/app.js"></script>
        
        
        {include file='apps/modals.tpl'}
        {include file='js/jqueryui_js.tpl'}
        {include file='js/formvalidation_js.tpl'}
        {include file='js/active-premium-client.tpl'}
        {include file='js/pass_toggle.tpl'}
        {include file='js/update_js3.tpl'}
</body>
</html>