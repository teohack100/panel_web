<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8" />
        <title>{$siteTitle} - Super Administrador</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A premium admin dashboard template by Mannatthemes" name="description" />
        <meta content="Mannatthemes" name="author" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="{$base_url}firenet/assets/images/v.png">


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
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Gestión de roles</a></li>
                                        <li class="breadcrumb-item active">Super Administrador</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Lista de superadministradores</h4>
                            </div><!--end page-title-box-->
                        </div><!--end col-->
                    </div>
                    <!-- end page title end breadcrumb -->
                    
                    <div class="row">
                        <div class="col-12">
                            <div id="success"></div>
                            <div class="card">
                                <form id="delflag_frm" name="delflag_frm">
		                        <input type="hidden" id="submitted" name="submitted" value="Suspend | Delete Submitted">
                                <div class="card-body">
    
                                    <h4 class="mt-0 header-title">Lista de superadministradores</h4>
                                    <!--p class="text-muted mb-3">The DataTables API has a number of methods for attaching 
                                        child rows to a parent row in the DataTable. This can be used to show additional 
                                        information about a row, useful for cases where you wish to convey more information 
                                        about a row than there is space for in the host table.
                                    </p-->
                                    <div class="btn-group sidebar-social" role="group">
                                        <button id="demoteSubmit" name="demoteSubmit" type="button" class="btn btn-primary" onclick="demoteSubmitted()"><a><i class="fas fa-level-down-alt" aria-hidden="true"></i><span>DEGRADAR</span></a></button>
                                        <button id="deleteSubmit" name="deleteSubmit" type="button" class="btn btn-danger" onclick="deleteSubmitted()"><a><i class="fas fa-user-times" aria-hidden="true"></i><span>ELIMINAR</span></a></button>
                                        <button id="freezeSubmit" type="button" class="btn btn-secondary" onclick="freezeSubmitted()"><a><i class="fas fa-user-lock" aria-hidden="true"></i></i><span>CONGELAR</span></a></button>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="users-serverside" class="table table-striped table-hover table-bordered dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th><input type="checkbox" class="select-all" /></th>
        											<th>Nombre</th>
        											<th>Estado</th>
        											<th>Creditos</th>
        											<th>Upline</th>
        											<th>Opciones</th>
                                                </tr>
                                            </thead>
                                        <tbody>
                                        </tbody>
                                        </table>  
                                    </div>
                                </div>
                                </form>
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
        
        
        {include file='apps/modals-role.tpl'}
        {include file='js/jqueryui_js.tpl'}
        {include file='js/formvalidation_js.tpl'}
        {include file='js/super-administrator.tpl'}
        {include file='js/pass_toggle.tpl'}
</body>
</html>