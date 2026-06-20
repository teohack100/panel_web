<!DOCTYPE html>
<html lang="es">
<head>
        <meta charset="utf-8" />
        <title>{$siteTitle} - Tablero</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A premium admin dashboard template by Mannatthemes" name="description" />
        <meta content="Mannatthemes" name="author" />

        <!-- App favicon -->
        {assign var=pm_dashboard_favicon value="`$base_url`firenet/assets/images/v.png"}
        {if isset($panel_favicon_url) && $panel_favicon_url neq ''}
            {assign var=pm_dashboard_favicon value=$panel_favicon_url}
        {/if}
        <link rel="shortcut icon" href="{$pm_dashboard_favicon}">

        <link href="{$base_url}firenet/assets/plugins/jvectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet">

        <!-- App css -->
        <link href="{$base_url}firenet/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="{$base_url}firenet/assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="{$base_url}firenet/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
        <link href="{$base_url}firenet/assets/css/style.css" rel="stylesheet" type="text/css" />

        <!-- Sweet Alert -->
        <link href="{$base_url}firenet/assets/plugins/sweet-alert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
        <link href="{$base_url}firenet/assets/plugins/animate/animate.css" rel="stylesheet" type="text/css">
        
        <link rel="stylesheet" type="text/css" href="{$base_url}bootstrap/assets/alertifyjs/css/alertify.css">
	    <link rel="stylesheet" type="text/css" href="{$base_url}bootstrap/assets/alertifyjs/css/themes/bootstrap.rtl.css">
        
        {include file='css/custom_css.tpl'}
        {include file='css/formvalidation_css.tpl'}
        <style>
            .dashboard-kpi-row {
                margin-left: -6px;
                margin-right: -6px;
            }

            .dashboard-kpi-row > [class*="col-"] {
                padding-left: 6px;
                padding-right: 6px;
                -ms-flex: 0 0 25%;
                flex: 0 0 25%;
                max-width: 25%;
                width: 25%;
                margin-bottom: 12px;
            }

            .dashboard-kpi-row .programmit-kpi-card {
                height: 100%;
                margin-bottom: 0;
            }

            .dashboard-content-row {
                margin-left: -6px;
                margin-right: -6px;
            }

            .dashboard-content-row > [class*="col-"] {
                padding-left: 6px;
                padding-right: 6px;
            }

            .dashboard-content-row .card {
                border-radius: 3px !important;
                border: 1px solid #dbe4f2 !important;
                box-shadow: 0 8px 20px rgba(24, 70, 127, 0.08) !important;
                overflow: hidden;
            }

            .dashboard-news-card {
                margin-bottom: 12px;
            }

            .dashboard-news-card .card-body {
                padding: 1.1rem 1.15rem 1rem;
            }

            .dashboard-news-card .activity-scroll,
            .dashboard-news-card .slimScrollDiv {
                min-height: 0 !important;
                height: auto !important;
                overflow: visible !important;
            }

            .dashboard-news-card .activity {
                margin: 14px 14px 0 18px;
            }

            .dashboard-news-card .activity .item-info:last-child {
                margin-bottom: 0;
            }

            @media (max-width: 575.98px) {
                .dashboard-kpi-row > [class*="col-"] {
                    -ms-flex: 0 0 100%;
                    flex: 0 0 100%;
                    max-width: 100%;
                    width: 100%;
                }
            }
        </style>
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
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Navegación Principal</a></li>
                                        <li class="breadcrumb-item active">Tablero</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">Tablero</h4>
                            </div><!--end page-title-box-->
                        </div><!--end col-->
                    </div>
                    <div id="success"></div>
                    <!-- end page title end breadcrumb -->
                    {assign var=pm_kpi_card_style value="background: linear-gradient(180deg, var(--pm-blue-500, #74adff) 0%, var(--pm-blue-600, #4f96ee) 55%, var(--pm-blue-700, #2f6fcb) 100%) !important; border: 1px solid rgba(255, 255, 255, 0.12) !important; border-radius: 3px !important; box-shadow: 0 8px 20px rgba(24, 70, 127, 0.12) !important; overflow: hidden !important;"}
                    {assign var=pm_kpi_body_style value="position: relative; display: flex; flex-direction: column; justify-content: space-between; align-items: flex-start; gap: 0.45rem; background: transparent !important; color: #ffffff !important; padding: 0.9rem 1rem 0.82rem; min-height: 109px; height: 109px;"}
                    {assign var=pm_kpi_label_style value="display: block; width: auto; max-width: none; padding-right: 3.85rem; min-height: 2.2rem; margin: 0; color: #ffffff !important; font-weight: 400; font-size: clamp(0.84rem, 0.8rem + 0.14vw, 0.9rem); line-height: 1.2; letter-spacing: 0.01em; white-space: normal; word-break: keep-all; overflow-wrap: normal; hyphens: none;"}
                    {assign var=pm_kpi_value_style value="color: #ffffff !important; font-size: clamp(1.78rem, 1.68rem + 0.32vw, 1.94rem); font-weight: 500; line-height: 1; letter-spacing: 0.01em; margin-top: auto !important; margin-bottom: 0 !important;"}
                    {assign var=pm_kpi_icon_wrap_style value="float: none !important; position: absolute; top: 0.88rem; right: 0.88rem;"}
                    {assign var=pm_kpi_icon_style value="color: #ffffff !important; background: rgba(255, 255, 255, 0.12) !important; width: 48px; height: 48px; line-height: 48px; font-size: 1.45rem; display: block; text-align: center; border-radius: 52% 48% 23% 77% / 44% 68% 32% 56%; box-shadow: 0 3px 3px 0.25px rgba(48, 62, 103, 0.15);"}
                    {assign var=pm_panel_card_style value="border: 1px solid #dbe4f2 !important; border-radius: 3px !important; background: linear-gradient(180deg, rgba(255,255,255,.98) 0%, #f7fbff 100%) !important; box-shadow: 0 8px 20px rgba(24, 70, 127, 0.08) !important; overflow: hidden !important; margin-bottom: 12px;"}
                    {assign var=pm_panel_body_style value="padding: 1.1rem 1.15rem 1rem !important; color: #35527a !important;"}
                    
                    {if $panel_is_root_2 == 1 || $panel_is_superadmin_2 == 1}
                    <div class="row justify-content-center dashboard-kpi-row">
                        <div class="col-md-3">
                            <div class="card report-card programmit-kpi-card" style="{$pm_kpi_card_style}">
                                <div class="card-body" style="{$pm_kpi_body_style}">
                                    <div class="float-right" style="{$pm_kpi_icon_wrap_style}">
                                        <i class="dripicons-user-group report-main-icon" style="{$pm_kpi_icon_style}"></i>
                                    </div> 
                                    <span class="text-white" style="{$pm_kpi_label_style}">Total&nbsp;de<br>clientes</span>
                                    <h1 class="text-white my-3" style="{$pm_kpi_value_style}">{$clients2}</h1>
                                </div><!--end card-body--> 
                            </div><!--end card--> 
                        </div> <!--end col--> 
                        <div class="col-md-3">
                            <div class="card report-card programmit-kpi-card" style="{$pm_kpi_card_style}">
                                <div class="card-body" style="{$pm_kpi_body_style}">
                                    <div class="float-right" style="{$pm_kpi_icon_wrap_style}">
                                        <i class="dripicons-user-group report-main-icon" style="{$pm_kpi_icon_style}"></i>
                                    </div> 
                                    <span class="text-white" style="{$pm_kpi_label_style}">Clientes activos</span>
                                    <h1 class="my-3 text-white data-active" style="{$pm_kpi_value_style}">{$active_users2}</h1>
                                </div><!--end card-body--> 
                            </div><!--end card--> 
                        </div> <!--end col--> 
                        <div class="col-md-3">
                            <div class="card report-card programmit-kpi-card" style="{$pm_kpi_card_style}">
                                <div class="card-body" style="{$pm_kpi_body_style}">
                                    <div class="float-right" style="{$pm_kpi_icon_wrap_style}">
                                        <i class="dripicons-user-group report-main-icon" style="{$pm_kpi_icon_style}"></i>
                                    </div> 
                                    <span class="text-white" style="{$pm_kpi_label_style}">Clientes inactivos</span>
                                    <h1 class="text-white my-3 data-inactive" style="{$pm_kpi_value_style}">{$inactive_users2}</h1>
                                </div><!--end card-body--> 
                            </div><!--end card--> 
                        </div> <!--end col--> 
                        <div class="col-md-3">
                            <div class="card report-card programmit-kpi-card" style="{$pm_kpi_card_style}">
                                <div class="card-body" style="{$pm_kpi_body_style}">
                                    <div class="float-right" style="{$pm_kpi_icon_wrap_style}">
                                        <i class="dripicons-wallet report-main-icon" style="{$pm_kpi_icon_style}"></i>
                                    </div> 
                                    <span class="text-white" style="{$pm_kpi_label_style}">Creditos</span>
                                    <h1 class="text-white my-3" style="{$pm_kpi_value_style}">{$credits_bal}</h1>
                                </div><!--end card-body--> 
                            </div><!--end card--> 
                        </div> <!--end col-->                               
                    </div><!--end row-->
                    {/if}
                    
                    {if $panel_can_view_management_kpis_2 == 1 && $panel_is_root_2 != 1 && $panel_is_superadmin_2 != 1}
                    <div class="row justify-content-center dashboard-kpi-row">
                        <div class="col-md-3">
                            <div class="card report-card programmit-kpi-card" style="{$pm_kpi_card_style}">
                                <div class="card-body" style="{$pm_kpi_body_style}">
                                    <div class="float-right" style="{$pm_kpi_icon_wrap_style}">
                                        <i class="dripicons-user-group report-main-icon" style="{$pm_kpi_icon_style}"></i>
                                    </div> 
                                    <span class="text-white" style="{$pm_kpi_label_style}">Total&nbsp;de<br>clientes</span>
                                    <h1 class="text-white my-3" style="{$pm_kpi_value_style}">{$clients}</h1>
                                </div><!--end card-body--> 
                            </div><!--end card--> 
                        </div> <!--end col--> 
                        <div class="col-md-3">
                            <div class="card report-card programmit-kpi-card" style="{$pm_kpi_card_style}">
                                <div class="card-body" style="{$pm_kpi_body_style}">
                                    <div class="float-right" style="{$pm_kpi_icon_wrap_style}">
                                        <i class="dripicons-user-group report-main-icon" style="{$pm_kpi_icon_style}"></i>
                                    </div> 
                                    <span class="text-white" style="{$pm_kpi_label_style}">Clientes activos</span>
                                    <h1 class="my-3 text-white data-active" style="{$pm_kpi_value_style}">{$active_users}</h1>
                                </div><!--end card-body--> 
                            </div><!--end card--> 
                        </div> <!--end col--> 
                        <div class="col-md-3">
                            <div class="card report-card programmit-kpi-card" style="{$pm_kpi_card_style}">
                                <div class="card-body" style="{$pm_kpi_body_style}">
                                    <div class="float-right" style="{$pm_kpi_icon_wrap_style}">
                                        <i class="dripicons-user-group report-main-icon" style="{$pm_kpi_icon_style}"></i>
                                    </div> 
                                    <span class="text-white" style="{$pm_kpi_label_style}">Clientes inactivos</span>
                                    <h1 class="text-white my-3 data-inactive" style="{$pm_kpi_value_style}">{$inactive_users}</h1>
                                </div><!--end card-body--> 
                            </div><!--end card--> 
                        </div> <!--end col--> 
                        <div class="col-md-3">
                            <div class="card report-card programmit-kpi-card" style="{$pm_kpi_card_style}">
                                <div class="card-body" style="{$pm_kpi_body_style}">
                                    <div class="float-right" style="{$pm_kpi_icon_wrap_style}">
                                        <i class="dripicons-wallet report-main-icon" style="{$pm_kpi_icon_style}"></i>
                                    </div> 
                                    <span class="text-white" style="{$pm_kpi_label_style}">Creditos</span>
                                    <h1 class="text-white my-3" style="{$pm_kpi_value_style}">{$credits_bal}</h1>
                                </div><!--end card-body--> 
                            </div><!--end card--> 
                        </div> <!--end col-->                               
                    </div><!--end row-->
                    {/if}

                    {if $user_level_2 == 'normal' && $user_id_2 != 1}
                    <div class="row justify-content-center dashboard-kpi-row">
                        <div class="col-md-3">
                            <div class="card report-card programmit-kpi-card" style="{$pm_kpi_card_style}">
                                <div class="card-body" style="{$pm_kpi_body_style}">
                                    <div class="float-right" style="{$pm_kpi_icon_wrap_style}">
                                        <i class="dripicons-user-group report-main-icon" style="{$pm_kpi_icon_style}"></i>
                                    </div>
                                    <span class="text-white" style="{$pm_kpi_label_style}">Total&nbsp;de<br>clientes</span>
                                    <h1 class="text-white my-3" style="{$pm_kpi_value_style}">{$clients}</h1>
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div> <!--end col-->
                        <div class="col-md-3">
                            <div class="card report-card programmit-kpi-card" style="{$pm_kpi_card_style}">
                                <div class="card-body" style="{$pm_kpi_body_style}">
                                    <div class="float-right" style="{$pm_kpi_icon_wrap_style}">
                                        <i class="dripicons-user-group report-main-icon" style="{$pm_kpi_icon_style}"></i>
                                    </div>
                                    <span class="text-white" style="{$pm_kpi_label_style}">Clientes activos</span>
                                    <h1 class="my-3 text-white data-active" style="{$pm_kpi_value_style}">{$active_users}</h1>
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div> <!--end col-->
                        <div class="col-md-3">
                            <div class="card report-card programmit-kpi-card" style="{$pm_kpi_card_style}">
                                <div class="card-body" style="{$pm_kpi_body_style}">
                                    <div class="float-right" style="{$pm_kpi_icon_wrap_style}">
                                        <i class="dripicons-user-group report-main-icon" style="{$pm_kpi_icon_style}"></i>
                                    </div>
                                    <span class="text-white" style="{$pm_kpi_label_style}">Clientes inactivos</span>
                                    <h1 class="text-white my-3 data-inactive" style="{$pm_kpi_value_style}">{$inactive_users}</h1>
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div> <!--end col-->
                        <div class="col-md-3">
                            <div class="card report-card programmit-kpi-card" style="{$pm_kpi_card_style}">
                                <div class="card-body" style="{$pm_kpi_body_style}">
                                    <div class="float-right" style="{$pm_kpi_icon_wrap_style}">
                                        <i class="dripicons-wallet report-main-icon" style="{$pm_kpi_icon_style}"></i>
                                    </div>
                                    <span class="text-white" style="{$pm_kpi_label_style}">Creditos</span>
                                    <h1 class="text-white my-3" style="{$pm_kpi_value_style}">{$credits_bal}</h1>
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div> <!--end col-->
                    </div><!--end row-->
                    {/if}

                    <div class="row dashboard-content-row">
                        <div class="col-lg-12">
                            <div class="card dashboard-news-card" style="{$pm_panel_card_style}">                                       
                                <div class="card-body" style="{$pm_panel_body_style}"> 
                                    <h4 class="header-title mt-0 mb-3">Actualización & noticias</h4>
                                    <div class="slimscroll activity-scroll">
                                        <div class="activity">
                                            {foreach item=i from=$download} {$i} {/foreach}                                                                                                       
                                        </div><!--end activity-->
                                    </div><!--end activity-scroll-->
                                </div> <!--end card-body-->                                     
                            </div><!--end card-->
                        </div><!--end col-->
                    </div><!--end row-->
                    
                    <div class="row dashboard-content-row">
                        {if $panel_can_view_management_kpis_2 == 1}
                        <div class="col-lg-4">
                            <div class="card" style="{$pm_panel_card_style}">
                                <div class="card-body" style="{$pm_panel_body_style}">
                                    <h4 class="header-title">Estadísticas</h4>  
                                    <div id="clients" class="apex-charts mt-4 mb-4"></div>
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div><!--end col-->
                        {/if}
                        <div class="d-flex col-lg-{if $panel_can_view_management_kpis_2 == 1}8{else}12{/if}">
                            <div class="card" style="{$pm_panel_card_style}">
                                <div class="card-body" style="{$pm_panel_body_style}">
                                    <h4 class="mt-0 header-title">Preguntas más frecuentes</h4>
                                    <p class="text-muted">¿Tienes alguna pregunta? Obtenga respuestas aquí!

                                    </p>
                                    <div class="accordion" id="accordionExample-faq">
                                        <div class="card shadow-none border mb-1">
                                            <div class="card-header" id="headingOne">
                                            <h5 class="my-0">
                                                <button class="btn btn-link ml-4" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                    Que es VPN?
                                                </button>
                                            </h5>
                                            </div>
                                        
                                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample-faq">
                                            <div class="card-body">
                                              VPN significa red privada virtual, un túnel seguro entre dos o más dispositivos.
                                              Conectarse a una VPN le brinda una conexión encriptada a Internet. Esto le permite mantener la privacidad, la seguridad y acceder al contenido en línea que desee, sin importar dónde se encuentre.
                                            </div>
                                            </div>
                                        </div>
                                        <div class="card shadow-none border mb-1">
                                            <div class="card-header" id="headingTwo">
                                            <h5 class="my-0">
                                                <button class="btn btn-link collapsed ml-4 align-self-center" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                    Cómo te protege la VPN?
                                                </button>
                                            </h5>
                                            </div>
                                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample-faq">
                                            <div class="card-body">
                                               Una VPN redirige su tráfico de Internet, ocultando dónde está su computadora, teléfono u otro dispositivo cuando hace contacto con sitios web. También cifra la información que envía a través de Internet, lo que la hace ilegible para cualquiera que intercepte su tráfico. Eso incluye a su proveedor de servicios de Internet.
                                            </div>
                                            </div>
                                        </div>
                                        <div class="card shadow-none border mb-1">
                                            <div class="card-header" id="headingThree">
                                            <h5 class="my-0">
                                                <button class="btn btn-link collapsed ml-4" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                  ¿Onlycode VPN es gratis?
                                                </button>
                                            </h5>
                                            </div>
                                            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample-faq">
                                            <div class="card-body">
                                               No. Onlycode VPN es un servicio VPN premium, aunque hay una prueba gratuita de 15 horas disponible para ciertos servidores.
Además, todas las suscripciones vienen con una garantía de devolución de dinero de 30 días, lo que significa que puede probarnos durante un mes sin riesgo si cambia de opinión..

                                            </div>
                                            </div>
                                        </div>                                                
                                    </div><!--end accordion-->
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div><!--end col-->
                    </div><!--end row-->

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
        {include file='js/dashboard_statistics.tpl'}
        {include file='js/active-vip-client.tpl'}
        {include file='js/jqueryui_js.tpl'}
        {include file='js/formvalidation_js.tpl'}
        {include file='js/pass_toggle.tpl'}
</body>
</html>
