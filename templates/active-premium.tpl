<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8" />
        <title>{$siteTitle} - Clientes Activos</title>
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
        <style>
            body.monitoring-radius-3 .page-content .card,
            body.monitoring-radius-3 .page-content .card-header,
            body.monitoring-radius-3 .page-content .card-footer,
            body.monitoring-radius-3 .page-content .card-body,
            body.monitoring-radius-3 .page-content .table-responsive,
            body.monitoring-radius-3 .page-content .btn,
            body.monitoring-radius-3 .page-content .form-control,
            body.monitoring-radius-3 .page-content .custom-select,
            body.monitoring-radius-3 .page-content .input-group-text,
            body.monitoring-radius-3 .page-content .dataTables_filter input,
            body.monitoring-radius-3 .page-content .dataTables_length select,
            body.monitoring-radius-3 .page-content .dataTables_paginate .paginate_button {
                border-radius: 3px !important;
            }

            body.monitoring-radius-3 .page-content .card,
            body.monitoring-radius-3 .page-content .table-responsive,
            body.monitoring-radius-3 .page-content .dataTables_wrapper,
            body.monitoring-radius-3 .page-content .dataTables_wrapper .row {
                overflow: hidden;
            }

            body.monitoring-radius-3.monitoring-premium-active .page-content .table-responsive {
                overflow-x: auto !important;
                overflow-y: hidden !important;
            }

            body.monitoring-radius-3.monitoring-premium-active #users-serverside {
                width: 100% !important;
            }

            body.monitoring-radius-3.monitoring-premium-active #users-serverside th,
            body.monitoring-radius-3.monitoring-premium-active #users-serverside td {
                white-space: nowrap !important;
                vertical-align: middle !important;
                padding: 0.62rem 0.72rem !important;
            }

            body.monitoring-radius-3.monitoring-premium-active #users-serverside .badge {
                font-size: 0.68rem !important;
                font-weight: 700 !important;
                line-height: 1.2 !important;
                padding: 0.3rem 0.46rem !important;
                border-radius: 3px !important;
            }

            body.monitoring-radius-3.monitoring-premium-active #users-serverside .live-countdown {
                font-size: 0.68rem !important;
                letter-spacing: 0 !important;
                white-space: nowrap !important;
            }

            body.monitoring-radius-3.monitoring-premium-active #users-serverside .btn-group.sidebar-social {
                display: inline-flex !important;
                flex-wrap: nowrap !important;
                gap: 0 !important;
            }

            body.monitoring-radius-3.monitoring-premium-active #users-serverside .btn-group.sidebar-social .btn {
                min-width: 54px !important;
                padding: 0.38rem 0.46rem !important;
            }

            body.monitoring-radius-3.monitoring-premium-active #users-serverside .btn-group.sidebar-social .btn span {
                display: block !important;
                font-size: 0.5rem !important;
                letter-spacing: 0.03em !important;
                line-height: 1.1 !important;
            }

            body.monitoring-radius-3.monitoring-premium-active #users-serverside_wrapper .dataTables_filter input {
                width: 170px !important;
                max-width: 170px !important;
            }

            body.monitoring-radius-3.monitoring-premium-active #users-serverside td.monitoring-control,
            body.monitoring-radius-3.monitoring-premium-active #users-serverside th.monitoring-control {
                width: 34px !important;
                min-width: 34px !important;
                max-width: 34px !important;
                text-align: center !important;
                padding-left: 0.35rem !important;
                padding-right: 0.35rem !important;
            }

            body.monitoring-radius-3.monitoring-premium-active #users-serverside td.monitoring-control {
                cursor: pointer !important;
            }

            body.monitoring-radius-3.monitoring-premium-active #users-serverside td.monitoring-control::before {
                content: '+' !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                width: 18px !important;
                height: 18px !important;
                border-radius: 50% !important;
                background: linear-gradient(180deg, #2da2ff 0%, #0d72d6 100%) !important;
                color: #ffffff !important;
                font-size: 13px !important;
                font-weight: 700 !important;
                line-height: 1 !important;
                box-shadow: 0 4px 10px rgba(18, 103, 192, 0.22) !important;
            }

            body.monitoring-radius-3.monitoring-premium-active #users-serverside tr.shown td.monitoring-control::before {
                content: '-' !important;
                background: linear-gradient(180deg, #ff6f6f 0%, #d93636 100%) !important;
                box-shadow: 0 4px 10px rgba(210, 62, 62, 0.18) !important;
            }

            body.monitoring-radius-3.monitoring-premium-active #users-serverside td.monitoring-select,
            body.monitoring-radius-3.monitoring-premium-active #users-serverside th.monitoring-select {
                width: 34px !important;
                min-width: 34px !important;
                max-width: 34px !important;
                text-align: center !important;
                padding-left: 0.35rem !important;
                padding-right: 0.35rem !important;
            }

            body.monitoring-radius-3.monitoring-premium-active #users-serverside tr.monitoring-child-row > td {
                padding: 0 !important;
                border-top: 0 !important;
                background: linear-gradient(180deg, rgba(231, 241, 255, 0.72) 0%, rgba(240, 246, 255, 0.96) 100%) !important;
            }

            body.monitoring-radius-3.monitoring-premium-active .monitoring-action-panel {
                display: flex !important;
                align-items: center !important;
                gap: 12px !important;
                padding: 0.72rem 0.9rem !important;
            }

            body.monitoring-radius-3.monitoring-premium-active .monitoring-action-label {
                color: #355a85 !important;
                font-size: 0.95rem !important;
                font-weight: 700 !important;
                min-width: 72px !important;
            }

            body.monitoring-radius-3.monitoring-premium-active .monitoring-action-buttons .btn-group.sidebar-social .btn {
                min-width: 54px !important;
            }
        </style>
</head>
<body class="monitoring-radius-3 monitoring-premium-active">
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
                                        <li class="breadcrumb-item active">Clientes activos</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Clientes activos</h4>
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
    
                                    <h4 class="mt-0 header-title">Clientes activos <span class="badge badge-success">activo</span></h4>
                                    <!--p class="text-muted mb-3">The DataTables API has a number of methods for attaching 
                                        child rows to a parent row in the DataTable. This can be used to show additional 
                                        information about a row, useful for cases where you wish to convey more information 
                                        about a row than there is space for in the host table.
                                    </p-->
                                    <div class="btn-group sidebar-social" role="group">
                                      <button id="freezeSubmit" type="button" class="btn btn-secondary" onclick="freezeSubmitted()"><a><i class="fas fa-user-lock" aria-hidden="true"></i></i><span>CONGELAR</span></a></button>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="users-serverside" class="table table-striped table-hover table-bordered" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th class="monitoring-control"></th>
                                                    <th class="monitoring-select"><input type="checkbox" class="select-all" /></th>
                                                    <th>Nombre</th>
                                                    <th>Estado</th>
                                                    <th>Duracion</th>
                                                    <th>Papel</th>
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
        
        
        {include file='apps/modals.tpl'}
        {include file='js/jqueryui_js.tpl'}
        {include file='js/formvalidation_js.tpl'}
        {include file='js/active-premium-client.tpl'}
        {include file='js/pass_toggle.tpl'}
</body>
</html>
