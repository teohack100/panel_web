<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8" />
        <title>{$siteTitle} - Server Status</title>
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
        {if $server_status_embed_admin == 1}
        <style>
        {literal}
            html, body.programmit-admin-embed { min-height: 0 !important; height: auto !important; background: #f4f7fb !important; }
            body.programmit-admin-embed { margin: 0; overflow-x: hidden; overflow-y: hidden; }
            .programmit-admin-embed-wrapper { min-height: 0 !important; height: auto !important; background: transparent !important; }
            .programmit-admin-embed-content { margin-left: 0 !important; padding: 0 !important; min-height: 0 !important; height: auto !important; }
            .programmit-admin-embed-content .container-fluid { padding: 14px !important; }
            .programmit-admin-embed-content .page-title-box { display: none !important; }
            .programmit-admin-embed-content .card { box-shadow: none !important; }
        {/literal}
        </style>
        {/if}
</head>
<body{if $server_status_embed_admin == 1} class="programmit-admin-embed"{/if}>
{if $server_status_embed_admin != 1}{include file='apps/topnav.tpl'}{/if}
<!-- Site wrapper -->
<div class="{if $server_status_embed_admin == 1}programmit-admin-embed-wrapper{else}page-wrapper{/if}">
{if $server_status_embed_admin != 1}{include file='apps/sidenavi.tpl'}{/if}
    <!-- Page Content-->
            <div class="{if $server_status_embed_admin == 1}page-content programmit-admin-embed-content{else}page-content{/if}">

                <div class="container-fluid">
                    <!-- Page-Title -->
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="page-title-box">
                                <div class="float-right">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">{$siteTitle}</a></li>
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Server Management</a></li>
                                        <li class="breadcrumb-item active">Server Status</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Server Status</h4>
                            </div><!--end page-title-box-->
                        </div><!--end col-->
                    </div>
                    <!-- end page title end breadcrumb -->
                    
                    <div class="row">
                        <div class="col-12">
                            <div id="success"></div>
                            <div class="card">
                                <form method="post" id="frm" name="frm">
                                <div class="card-body">
    
                                    <h4 class="mt-0 header-title">Server Status</h4>
                                    <!--p class="text-muted mb-3">The DataTables API has a number of methods for attaching 
                                        child rows to a parent row in the DataTable. This can be used to show additional 
                                        information about a row, useful for cases where you wish to convey more information 
                                        about a row than there is space for in the host table.
                                    </p-->
                                    <div class="table-responsive">
                                        <table id="servers-serverside" class="table table-striped table-hover table-bordered dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
    												<th>Server Name</th>
    												<th>Bandwith</th>
    												<th>Status</th>
                                                </tr>
                                            </thead>
                                        <tbody>
                                            {foreach item=i from=$server}
												{$i}
											{/foreach}
                                        </tbody>
                                        </table>  
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div> <!-- end col -->
                    </div> <!-- end row -->

                </div><!-- container -->

                {if $server_status_embed_admin != 1}{include file='apps/footer.tpl'}{/if}
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
        
        
        {include file='js/server-status.tpl'}
        {include file='apps/modals.tpl'}
        {include file='js/jqueryui_js.tpl'}
        {include file='js/formvalidation_js.tpl'}
        {include file='js/active-premium-client.tpl'}
        {include file='js/ckeditor_js.tpl'}
        {include file='js/pass_toggle.tpl'}
        {if $server_status_embed_admin == 1}
        {literal}
        <script>
        (function () {
            if (window.parent === window) { return; }
            var timer = null;
            function postHeight() {
                var doc = document.documentElement;
                var body = document.body;
                var height = Math.max(
                    body ? body.scrollHeight : 0,
                    body ? body.offsetHeight : 0,
                    doc ? doc.scrollHeight : 0,
                    doc ? doc.offsetHeight : 0
                );
                window.parent.postMessage({ type: 'programmit_admin_embed_height', height: height }, '*');
            }
            function queueHeight() {
                if (timer) { window.clearTimeout(timer); }
                timer = window.setTimeout(postHeight, 60);
            }
            window.addEventListener('load', function () {
                queueHeight();
                window.setTimeout(postHeight, 180);
                window.setTimeout(postHeight, 360);
            });
            window.addEventListener('resize', queueHeight);
            if (window.MutationObserver && document.body) {
                new MutationObserver(queueHeight).observe(document.body, {
                    childList: true,
                    subtree: true,
                    attributes: true,
                    characterData: true
                });
            }
        })();
        </script>
        {/literal}
        {/if}
</body>
</html>
