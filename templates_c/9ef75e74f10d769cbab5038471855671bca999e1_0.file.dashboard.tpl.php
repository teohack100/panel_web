<?php
/* Smarty version 3.1.29, created on 2026-03-11 19:13:26
  from "C:\xampp\htdocs\panel_web\templates\dashboard.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_69b21336597a67_07107112',
  'file_dependency' => 
  array (
    '9ef75e74f10d769cbab5038471855671bca999e1' => 
    array (
      0 => 'C:\\xampp\\htdocs\\panel_web\\templates\\dashboard.tpl',
      1 => 1772758788,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:css/custom_css.tpl' => 1,
    'file:css/formvalidation_css.tpl' => 1,
    'file:apps/topnav.tpl' => 1,
    'file:apps/sidenavi.tpl' => 1,
    'file:apps/footer.tpl' => 1,
    'file:apps/modals.tpl' => 1,
    'file:js/dashboard_statistics.tpl' => 1,
    'file:js/active-vip-client.tpl' => 1,
    'file:js/jqueryui_js.tpl' => 1,
    'file:js/formvalidation_js.tpl' => 1,
    'file:js/pass_toggle.tpl' => 1,
  ),
),false)) {
function content_69b21336597a67_07107112 ($_smarty_tpl) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
        <meta charset="utf-8" />
        <title><?php echo $_smarty_tpl->tpl_vars['siteTitle']->value;?>
 - Tablero</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A premium admin dashboard template by Mannatthemes" name="description" />
        <meta content="Mannatthemes" name="author" />

        <!-- App favicon -->
        <?php $_smarty_tpl->tpl_vars['pm_dashboard_favicon'] = new Smarty_Variable(((string)$_smarty_tpl->tpl_vars['base_url']->value)."firenet/assets/images/v.png", null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'pm_dashboard_favicon', 0);?>
        <?php if (isset($_smarty_tpl->tpl_vars['panel_favicon_url']->value) && $_smarty_tpl->tpl_vars['panel_favicon_url']->value != '') {?>
            <?php $_smarty_tpl->tpl_vars['pm_dashboard_favicon'] = new Smarty_Variable($_smarty_tpl->tpl_vars['panel_favicon_url']->value, null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'pm_dashboard_favicon', 0);?>
        <?php }?>
        <link rel="shortcut icon" href="<?php echo $_smarty_tpl->tpl_vars['pm_dashboard_favicon']->value;?>
">

        <link href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/plugins/jvectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet">

        <!-- App css -->
        <link href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/css/style.css" rel="stylesheet" type="text/css" />

        <!-- Sweet Alert -->
        <link href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/plugins/sweet-alert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/plugins/animate/animate.css" rel="stylesheet" type="text/css">
        
        <link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
bootstrap/assets/alertifyjs/css/alertify.css">
	    <link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
bootstrap/assets/alertifyjs/css/themes/bootstrap.rtl.css">
        
        <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:css/custom_css.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:css/formvalidation_css.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        <style>
            .report-card .card-body {
                position: relative;
                padding: 0.95rem 1rem 0.85rem;
            }

            .report-card .card-body > .float-right {
                float: none !important;
                position: absolute;
                top: 0.75rem;
                right: 0.75rem;
            }

            .report-card .card-body > span.text-white {
                display: block;
                padding-right: 4.75rem;
                min-height: 2.25rem;
                line-height: 1.25;
                white-space: nowrap;
                word-break: normal;
                overflow-wrap: normal;
            }

            .report-card h1 {
                margin-top: 0.6rem !important;
                margin-bottom: 0 !important;
                line-height: 1;
            }

            .dashboard-kpi-row {
                margin-left: -6px;
                margin-right: -6px;
            }

            .dashboard-kpi-row > [class*="col-"] {
                padding-left: 6px;
                padding-right: 6px;
            }

        </style>
</head>
<body>
<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:apps/topnav.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<!-- Site wrapper -->
<div class="page-wrapper">
<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:apps/sidenavi.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

    <!-- Page Content-->
            <div class="page-content">

                <div class="container-fluid">
                    <!-- Page-Title -->
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="page-title-box">
                                <div class="float-right">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="javascript:void(0);"><?php echo $_smarty_tpl->tpl_vars['siteTitle']->value;?>
</a></li>
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
                    
                    <?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin') {?>
                    <div class="row justify-content-center dashboard-kpi-row">
                        <div class="col-md-3">
                            <div class="card report-card bg-success-gradient shadow-success">
                                <div class="card-body">
                                    <div class="float-right">
                                        <i class="dripicons-user-group report-main-icon"></i>
                                    </div> 
                                    <span class="text-white">Total&nbsp;de<br>clientes</span>
                                    <h1 class="text-white my-3"><?php echo $_smarty_tpl->tpl_vars['clients2']->value;?>
</h1>
                                </div><!--end card-body--> 
                            </div><!--end card--> 
                        </div> <!--end col--> 
                        <div class="col-md-3">
                            <div class="card report-card bg-success-gradient shadow-success">
                                <div class="card-body">
                                    <div class="float-right">
                                        <i class="dripicons-user-group report-main-icon"></i>
                                    </div> 
                                    <span class="text-white">Clientes<br>activos</span>
                                    <h1 class="my-3 text-white data-active"><?php echo $_smarty_tpl->tpl_vars['active_users2']->value;?>
</h1>
                                </div><!--end card-body--> 
                            </div><!--end card--> 
                        </div> <!--end col--> 
                        <div class="col-md-3">
                            <div class="card report-card bg-success-gradient shadow-success">
                                <div class="card-body">
                                    <div class="float-right">
                                        <i class="dripicons-user-group report-main-icon"></i>
                                    </div> 
                                    <span class="text-white">Clientes<br>inactivos</span>
                                    <h1 class="text-white my-3 data-inactive"><?php echo $_smarty_tpl->tpl_vars['inactive_users2']->value;?>
</h1>
                                </div><!--end card-body--> 
                            </div><!--end card--> 
                        </div> <!--end col--> 
                        <div class="col-md-3">
                            <div class="card report-card bg-success-gradient shadow-success">
                                <div class="card-body">
                                    <div class="float-right">
                                        <i class="dripicons-wallet report-main-icon"></i>
                                    </div> 
                                    <span class="text-white">Creditos<br>&nbsp;</span>
                                    <h1 class="text-white my-3"><?php echo $_smarty_tpl->tpl_vars['credits_bal']->value;?>
</h1>
                                </div><!--end card-body--> 
                            </div><!--end card--> 
                        </div> <!--end col-->                               
                    </div><!--end row-->
                    <?php }?>
                    
                    <?php if ($_smarty_tpl->tpl_vars['user_level_2']->value == 'subadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'administrator' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'reseller' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'subreseller') {?>
                    <div class="row justify-content-center dashboard-kpi-row">
                        <div class="col-md-3">
                            <div class="card report-card bg-success-gradient shadow-success">
                                <div class="card-body">
                                    <div class="float-right">
                                        <i class="dripicons-user-group report-main-icon"></i>
                                    </div> 
                                    <span class="text-white">Total&nbsp;de<br>clientes</span>
                                    <h1 class="text-white my-3"><?php echo $_smarty_tpl->tpl_vars['clients']->value;?>
</h1>
                                </div><!--end card-body--> 
                            </div><!--end card--> 
                        </div> <!--end col--> 
                        <div class="col-md-3">
                            <div class="card report-card bg-success-gradient shadow-success">
                                <div class="card-body">
                                    <div class="float-right">
                                        <i class="dripicons-user-group report-main-icon"></i>
                                    </div> 
                                    <span class="text-white">Clientes<br>activos</span>
                                    <h1 class="my-3 text-white data-active"><?php echo $_smarty_tpl->tpl_vars['active_users']->value;?>
</h1>
                                </div><!--end card-body--> 
                            </div><!--end card--> 
                        </div> <!--end col--> 
                        <div class="col-md-3">
                            <div class="card report-card bg-success-gradient shadow-success">
                                <div class="card-body">
                                    <div class="float-right">
                                        <i class="dripicons-user-group report-main-icon"></i>
                                    </div> 
                                    <span class="text-white">Clientes<br>inactivos</span>
                                    <h1 class="text-white my-3 data-inactive"><?php echo $_smarty_tpl->tpl_vars['inactive_users']->value;?>
</h1>
                                </div><!--end card-body--> 
                            </div><!--end card--> 
                        </div> <!--end col--> 
                        <div class="col-md-3">
                            <div class="card report-card bg-success-gradient shadow-success">
                                <div class="card-body">
                                    <div class="float-right">
                                        <i class="dripicons-wallet report-main-icon"></i>
                                    </div> 
                                    <span class="text-white">Creditos<br>&nbsp;</span>
                                    <h1 class="text-white my-3"><?php echo $_smarty_tpl->tpl_vars['credits_bal']->value;?>
</h1>
                                </div><!--end card-body--> 
                            </div><!--end card--> 
                        </div> <!--end col-->                               
                    </div><!--end row-->
                    <?php }?>

                    <?php if ($_smarty_tpl->tpl_vars['user_level_2']->value == 'normal' && $_smarty_tpl->tpl_vars['user_id_2']->value != 1) {?>
                    <div class="row justify-content-center dashboard-kpi-row">
                        <div class="col-md-3">
                            <div class="card report-card bg-success-gradient shadow-success">
                                <div class="card-body">
                                    <div class="float-right">
                                        <i class="dripicons-user-group report-main-icon"></i>
                                    </div>
                                    <span class="text-white">Total&nbsp;de<br>clientes</span>
                                    <h1 class="text-white my-3"><?php echo $_smarty_tpl->tpl_vars['clients']->value;?>
</h1>
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div> <!--end col-->
                        <div class="col-md-3">
                            <div class="card report-card bg-success-gradient shadow-success">
                                <div class="card-body">
                                    <div class="float-right">
                                        <i class="dripicons-user-group report-main-icon"></i>
                                    </div>
                                    <span class="text-white">Clientes<br>activos</span>
                                    <h1 class="my-3 text-white data-active"><?php echo $_smarty_tpl->tpl_vars['active_users']->value;?>
</h1>
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div> <!--end col-->
                        <div class="col-md-3">
                            <div class="card report-card bg-success-gradient shadow-success">
                                <div class="card-body">
                                    <div class="float-right">
                                        <i class="dripicons-user-group report-main-icon"></i>
                                    </div>
                                    <span class="text-white">Clientes<br>inactivos</span>
                                    <h1 class="text-white my-3 data-inactive"><?php echo $_smarty_tpl->tpl_vars['inactive_users']->value;?>
</h1>
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div> <!--end col-->
                        <div class="col-md-3">
                            <div class="card report-card bg-success-gradient shadow-success">
                                <div class="card-body">
                                    <div class="float-right">
                                        <i class="dripicons-wallet report-main-icon"></i>
                                    </div>
                                    <span class="text-white">Creditos<br>&nbsp;</span>
                                    <h1 class="text-white my-3"><?php echo $_smarty_tpl->tpl_vars['credits_bal']->value;?>
</h1>
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div> <!--end col-->
                    </div><!--end row-->
                    <?php }?>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">                                       
                                <div class="card-body"> 
                                    <h4 class="header-title mt-0 mb-3">Actualización & noticias</h4>
                                    <div class="slimscroll activity-scroll">
                                        <div class="activity">
                                            <?php
$_from = $_smarty_tpl->tpl_vars['download']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_i_0_saved_item = isset($_smarty_tpl->tpl_vars['i']) ? $_smarty_tpl->tpl_vars['i'] : false;
$_smarty_tpl->tpl_vars['i'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['i']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->_loop = true;
$__foreach_i_0_saved_local_item = $_smarty_tpl->tpl_vars['i'];
?> <?php echo $_smarty_tpl->tpl_vars['i']->value;?>
 <?php
$_smarty_tpl->tpl_vars['i'] = $__foreach_i_0_saved_local_item;
}
if ($__foreach_i_0_saved_item) {
$_smarty_tpl->tpl_vars['i'] = $__foreach_i_0_saved_item;
}
?>                                                                                                       
                                        </div><!--end activity-->
                                    </div><!--end activity-scroll-->
                                </div> <!--end card-body-->                                     
                            </div><!--end card-->
                        </div><!--end col-->
                    </div><!--end row-->
                    
                    <div class="row">
                        <?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'subadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'administrator' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'reseller' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'subreseller') {?>
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">Estadísticas</h4>  
                                    <div id="clients" class="apex-charts mt-4 mb-4"></div>
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div><!--end col-->
                        <?php }?>
                        <div class="d-flex col-lg-<?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'subadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'administrator' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'reseller' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'subreseller') {?>8<?php } else { ?>12<?php }?>">
                            <div class="card">
                                <div class="card-body">
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

                <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:apps/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

                <!--end footer-->
            </div>
            <!-- end page content -->
        </div>
        <!-- end page-wrapper -->
        <!-- jQuery  -->
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/js/jquery.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/js/bootstrap.bundle.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/js/metisMenu.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/js/waves.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/js/jquery.slimscroll.min.js"><?php echo '</script'; ?>
>

        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/plugins/moment/moment.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/plugins/apexcharts/apexcharts.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/pages/jquery.eco_dashboard.init.js"><?php echo '</script'; ?>
>

        <!-- Required datatable js -->
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/plugins/datatables/jquery.dataTables.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/plugins/datatables/dataTables.bootstrap4.min.js"><?php echo '</script'; ?>
>
        <!-- Buttons examples -->
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/plugins/datatables/dataTables.buttons.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/plugins/datatables/buttons.bootstrap4.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/plugins/datatables/jszip.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/plugins/datatables/pdfmake.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/plugins/datatables/vfs_fonts.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/plugins/datatables/buttons.html5.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/plugins/datatables/buttons.print.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/plugins/datatables/buttons.colVis.min.js"><?php echo '</script'; ?>
>
        <!-- Responsive examples -->
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/plugins/datatables/dataTables.responsive.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/plugins/datatables/responsive.bootstrap4.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/pages/jquery.datatable.init.js"><?php echo '</script'; ?>
>
        
        <!-- App js -->
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/js/jquery.core.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/js/app.js"><?php echo '</script'; ?>
>
        
        <!-- Sweet-Alert  -->
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/plugins/sweet-alert2/sweetalert2.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/pages/jquery.sweet-alert.init.js"><?php echo '</script'; ?>
>
        
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
bootstrap/assets/alertifyjs/alertify.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
bootstrap/dashboard/dist/js/app.js"><?php echo '</script'; ?>
>
	
	    <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:apps/modals.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:js/dashboard_statistics.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:js/active-vip-client.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:js/jqueryui_js.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:js/formvalidation_js.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:js/pass_toggle.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

</body>
</html>
<?php }
}
