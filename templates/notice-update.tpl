<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8" />
        <title>{$siteTitle} - Actualización de aviso</title>
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
        
        {include file='css/jqueryui_css.tpl'}
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
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Gestión de paneles</a></li>
                                        <li class="breadcrumb-item active">Actualización de aviso</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Actualización de aviso</h4>
                            </div><!--end page-title-box-->
                        </div><!--end col-->
                    </div>
                    <!-- end page title end breadcrumb -->
                    
                    <div class="row">
                        <div class="col-12">
                            <div id="success" class="success"></div>
                            <div class="card">
                                
                                <div class="card-body">
    
                                    <h4 class="mt-0 header-title">Actualización de aviso</h4>
                                    <!--p class="text-muted mb-3">The DataTables API has a number of methods for attaching 
                                        child rows to a parent row in the DataTable. This can be used to show additional 
                                        information about a row, useful for cases where you wish to convey more information 
                                        about a row than there is space for in the host table.
                                    </p-->
                                    <div class="btn-group sidebar-social" role="group">
                                      <button onclick="download_add()" id="download_add" name="deleteSubmit" type="button" class="btn btn-success"><a><i class="fas fa-plus" aria-hidden="true"></i><span>CREAR</span></a></button>
                                      <button onClick="delete_download();" id="delete_download" type="button" class="btn btn-danger"><a><i class="fas fa-times" aria-hidden="true"></i></i><span>ELIMINAR</span></a></button>
                                    </div>
                                    <form method="post" id="dl_frm" name="dl_frm">
		                            <input type="hidden" id="submitted" name="submitted" value="Download Delete">
                                    <div class="table-responsive">
                                        <table id="download_serverside" class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th><input type="checkbox" class="select-allss" /></th>
											<th>Titulo</th>
											<th>Fecha</th>
											<th>Visibilidad</th>
											<th>Categoria</th>
											<th>Plataforma</th>
											<th>Opciones</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
                                    </div>
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

        {include file='apps/modals-notice.tpl'}
        {include file='apps/modals.tpl'}

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
        
        {include file='js/pass_toggle.tpl'}
        {include file='js/active-premium-client.tpl'}
     
        <!-- Custom File Upload -->
        <script src="{$base_url}bootstrap/assets/custom.fileupload/custom.fle_upload.js"></script>
    	<script src="{$base_url}bootstrap/assets/jqueryform/jquery.form.js"></script>
	
        {include file='js/formvalidation_js.tpl'}
        {include file='js/jqueryui_js.tpl'}
        
        {include file='js/ckeditor_js.tpl'}
        {include file='js/notice.tpl'}
        
</body>
</html>