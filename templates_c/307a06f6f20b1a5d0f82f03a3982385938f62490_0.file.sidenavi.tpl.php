<?php
/* Smarty version 3.1.29, created on 2026-03-11 19:13:43
  from "C:\xampp\htdocs\panel_web\templates\apps\sidenavi.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_69b21347c612c9_05184833',
  'file_dependency' => 
  array (
    '307a06f6f20b1a5d0f82f03a3982385938f62490' => 
    array (
      0 => 'C:\\xampp\\htdocs\\panel_web\\templates\\apps\\sidenavi.tpl',
      1 => 1773183933,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b21347c612c9_05184833 ($_smarty_tpl) {
?>
<!-- Left Sidenav -->
             <div class="left-sidenav">
                <div class="main-icon-menu">
                    <nav class="nav">
                        <a href="#MetricaAnalytic" class="nav-link leftmenu-sm-item bg-success shadow-success" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Main Navigation">
                            <svg class="nav-svg" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                            viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
                                <g>
                                    <path d="M184,448h48c4.4,0,8-3.6,8-8V72c0-4.4-3.6-8-8-8h-48c-4.4,0-8,3.6-8,8v368C176,444.4,179.6,448,184,448z"/>
                                    <path class="svg-primary" d="M88,448H136c4.4,0,8-3.6,8-8V296c0-4.4-3.6-8-8-8H88c-4.4,0-8,3.6-8,8V440C80,444.4,83.6,448,88,448z"/>
                                    <path class="svg-primary" d="M280.1,448h47.8c4.5,0,8.1-3.6,8.1-8.1V232.1c0-4.5-3.6-8.1-8.1-8.1h-47.8c-4.5,0-8.1,3.6-8.1,8.1v207.8
                                        C272,444.4,275.6,448,280.1,448z"/>
                                    <path d="M368,136.1v303.8c0,4.5,3.6,8.1,8.1,8.1h47.8c4.5,0,8.1-3.6,8.1-8.1V136.1c0-4.5-3.6-8.1-8.1-8.1h-47.8
                                        C371.6,128,368,131.6,368,136.1z"/>
                                </g>
                            </svg>
                        </a><!--end MetricaAnalytic-->
                        <?php if ($_smarty_tpl->tpl_vars['user_id_2']->value > 0) {?>
                        <?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'subadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'administrator' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'reseller' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'subreseller' || $_smarty_tpl->tpl_vars['panel_restricted_2']->value) {?>
                        <a href="#MetricaEcommerce" class="nav-link leftmenu-sm-item bg-success shadow-success" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Client Management">
                            <i class="fas fa-user text-white"></i>
                        </a> <!--end MetricaEcommerce-->   
                        <?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'subadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'administrator' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'reseller' || $_smarty_tpl->tpl_vars['panel_restricted_2']->value) {?>
                        <a href="#MetricaProject" class="nav-link leftmenu-sm-item bg-success shadow-success" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Role Management">
                            <i class="fas fa-user-shield text-white"></i>
                        </a><!--end MetricaProject-->
                        <?php }?>
                        <a href="#MetricaCRM" class="nav-link leftmenu-sm-item bg-success shadow-success" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Client Monitoring">
                            <i class="fas fa-user-tie text-white"></i>
                        </a><!--end MetricaCRM-->

                        <a href="#MetricaFinance" class="nav-link leftmenu-sm-item bg-success shadow-success" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Finanzas">
                            <i class="fas fa-wallet text-white"></i>
                        </a><!--end MetricaFinance-->

                        <a href="#MetricaOthers" class="nav-link leftmenu-sm-item bg-success shadow-success" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Server Management">
                            <i class="fas fa-server text-white"></i>
                        </a><!--end MetricaOthers-->

                        <a href="#MetricaAuthentication" class="nav-link leftmenu-sm-item bg-success shadow-success" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="History">
                            <i class="fas fa-history text-white"></i>
                        </a> <!--end MetricaAuthentication--> 

                        <!--a href="#MetricaPages" class="nav-link leftmenu-sm-item bg-light shadow-light" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="History">
                            <svg class="nav-svg" version="1.1" id="Layer_4" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
                                <g>
                                    <path d="M462.5,352.3c-1.9-5.5-5.6-11.5-11.4-18.3c-10.2-12-30.8-29.3-54.8-47.2c-2.6-2-6.4-0.8-7.5,2.3l-4.7,13.4
                                        c-0.7,2,0,4.3,1.7,5.5c15.9,11.6,35.9,27.9,41.8,35.9c2,2.8-0.5,6.6-3.9,5.8c-10-2.3-29-7.3-44.2-12.8c-8.6-3.1-17.7-6.7-27.2-10.6
                                        c16-20.8,24.7-46.3,24.7-72.6c0-32.8-13.2-63.6-37.1-86.4c-22.9-21.9-53.8-34.1-85.7-33.7c-25.7,0.3-50.1,8.4-70.7,23.5
                                        c-18.3,13.4-32.2,31.3-40.6,52c-8.3-6-16.1-11.9-23.2-17.6c-13.7-10.9-28.4-22-38.7-34.7c-2.2-2.8,0.9-6.7,4.4-5.9
                                        c11.3,2.6,35.4,10.9,56.4,18.9c1.5,0.6,3.2,0.3,4.5-0.8l11.1-10.1c2.4-2.1,1.7-6-1.3-7.2C121,137.4,89.2,128,73.2,128
                                        c-11.5,0-19.3,3.5-23.3,10.4c-7.6,13.3,7.1,35.2,45.1,66.8c34.1,28.5,82.6,61.8,136.5,92c87.5,49.1,171.1,81,208,81
                                        c11.2,0,18.7-3.1,22.1-9.1C464.4,364.4,464.7,358.7,462.5,352.3z"/>
                                    <path  class="svg-primary" d="M312,354c-29.1-12.8-59.3-26-92.6-44.8c-30.1-16.9-59.4-36.5-84.4-53.6c-1-0.7-2.2-1.1-3.4-1.1c-0.9,0-1.9,0.2-2.8,0.7
                                        c-2,1-3.3,3-3.3,5.2c0,1.2-0.1,2.4-0.1,3.5c0,32.1,12.6,62.3,35.5,84.9c22.9,22.7,53.4,35.2,85.8,35.2c23.6,0,46.5-6.7,66.2-19.5
                                        c1.9-1.2,2.9-3.3,2.7-5.5C315.5,356.8,314.1,354.9,312,354z"/>
                                </g>
                            </svg>                           
                        </a--><!--end MetricaPages-->

                        <!--a href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/pages/pages-calendar.html" class="nav-link bg-info shadow-info" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Calendar">                            
                            <svg class="nav-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path class="svg-primary" d="M368.005 272h-96v96h96v-96zm-32-208v32h-160V64h-48v32h-24.01c-22.002 0-40 17.998-40 40v272c0 22.002 17.998 40 40 40h304.01c22.002 0 40-17.998 40-40V136c0-22.002-17.998-40-40-40h-24V64h-48zm72 344h-304.01V196h304.01v212z"/>
                            </svg>                            
                        </a--> <!--end MetricaCalendar--> 
                        <?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin' || $_smarty_tpl->tpl_vars['panel_restricted_2']->value) {?>
                        <a href="#MetricaCrypto" class="nav-link leftmenu-sm-item bg-success shadow-success" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Panel Management">
                            <!--svg class="nav-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path class="svg-primary" d="M410.5 279.2c-5-11.5-12.7-21.6-28.1-30.1-8.2-4.5-16.1-7.8-25.4-10 5.4-2.5 10-5.4 16.3-11 7.5-6.6 13.1-15.7 15.6-23.3 2.6-7.5 4.1-18 3.5-28.2-1.1-16.8-4.4-33.1-13.2-44.8-8.8-11.7-21.2-20.7-37.6-27-12.6-4.8-25.5-7.8-45.5-8.9V32h-40v64h-32V32h-41v64H96v48h27.9c8.7 0 14.6.8 17.6 2.3 3.1 1.5 5.3 3.5 6.5 6 1.3 2.5 1.9 8.4 1.9 17.5V343c0 9-.6 14.8-1.9 17.4-1.3 2.6-2 4.9-5.1 6.3-3.1 1.4-3.2 1.3-11.8 1.3h-26.4L96 416h87v64h41v-64h32v64h40v-64.4c26-1.3 44.5-4.7 59.4-10.3 19.3-7.2 34.1-17.7 44.7-31.5 10.6-13.8 14.9-34.9 15.8-51.2.7-14.5-.9-33.2-5.4-43.4zM224 150h32v74h-32v-74zm0 212v-90h32v90h-32zm72-208.1c6 2.5 9.9 7.5 13.8 12.7 4.3 5.7 6.5 13.3 6.5 21.4 0 7.8-2.9 14.5-7.5 20.5-3.8 4.9-6.8 8.3-12.8 11.1v-65.7zm28.8 186.7c-7.8 6.9-12.3 10.1-22.1 13.8-2 .8-4.7 1.4-6.7 1.9v-82.8c5 .8 7.6 1.8 11.3 3.4 7.8 3.3 15.2 6.9 19.8 13.2 4.6 6.3 8 15.6 8 24.7 0 10.9-2.8 19.2-10.3 25.8z"/>
                            </svg-->
                            <i class="fas fa-cogs text-white"></i>
                        </a><!--end MetricaCrypto-->
                        <?php }?>
                        <?php }?>
                        <?php }?>
                    </nav><!--end nav-->
                </div><!--end main-icon-menu-->

                <div class="main-menu-inner">
                    <div class="menu-body slimscroll">
                        <div id="MetricaAnalytic" class="main-icon-menu-pane">
                            <div class="title-box">
                                <h6 class="menu-title">NAVEGACIÓN PRINCIPAL</h6>       
                            </div>
                            <ul class="nav">
                                <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=dashboard"><i class="ti-pie-chart"></i>Tablero</a></li>
                                <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=my-profile"><i class="ti-id-badge"></i>Mi Perfil</a></li>
                                <?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'subadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'administrator' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'reseller' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'subreseller' || $_smarty_tpl->tpl_vars['panel_restricted_2']->value) {?>
                                <?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin' || $_smarty_tpl->tpl_vars['credits_2']->value != 0) {?>
                                <li class="nav-item"><a class="nav-link" href="javascript:void(0);" onclick="selfreload()"><i class="ti-calendar"></i>Auto recarga</a></li>
                                <?php }?>
                                <?php if ($_smarty_tpl->tpl_vars['duration_2']->value > 0 || $_smarty_tpl->tpl_vars['vip_duration_2']->value > 0 || $_smarty_tpl->tpl_vars['private_duration_2']->value > 0) {?>
                                <!--li class="nav-item"><a class="nav-link" href="javascript:void(0);" onclick="convert()"><i class="ti-calendar"></i>Convert Duration</a></li-->
                                <?php }?>
                                <?php }?>
                            </ul>
                        </div><!-- end Analytic -->
                        <div id="MetricaCrypto" class="main-icon-menu-pane">
                            <div class="title-box">
                                <h6 class="menu-title">Gestión de paneles</h6>
                            </div>
                            <ul class="nav">
                                <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=server-update"><i class="ti-harddrives"></i>Actualización del servidor</a></li>
                                <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=notice-update"><i class="ti-pencil-alt"></i>Actualización de aviso</a></li>
                                <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=updater-v1"><i class="ti-marker-alt"></i>Updater #1</a></li>
                                <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=updater-v2"><i class="ti-marker-alt"></i>Updater #2</a></li>  
                                <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=updater-v3"><i class="ti-marker-alt"></i>Updater #3</a></li>
                                <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=updater-v4"><i class="ti-marker-alt"></i>Updater #4</a></li>
                            </ul>
                        </div><!-- end Crypto -->
                        <div id="MetricaProject" class="main-icon-menu-pane">
                            <div class="title-box">
                                <h6 class="menu-title">Gestión de roles</h6>        
                            </div>
                            <ul class="nav">
                                <?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'subadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'administrator' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'reseller' || $_smarty_tpl->tpl_vars['panel_restricted_2']->value) {?>
                                    <?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['panel_restricted_2']->value) {?>
                                        <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=super-administrator"><i class="ti-user"></i>Super Administrador</a></li>
                                    <?php }?>
                                    <?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin' || $_smarty_tpl->tpl_vars['panel_restricted_2']->value) {?>
                                        <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=administrator"><i class="ti-user"></i>Administrador</a></li>
                                    <?php }?>
                                    <?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'administrator' || $_smarty_tpl->tpl_vars['panel_restricted_2']->value) {?>
                                        <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=sub-administrator"><i class="ti-user"></i>Sub Administrador</a></li>
                                    <?php }?>
                                    <?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'subadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'administrator' || $_smarty_tpl->tpl_vars['panel_restricted_2']->value) {?>
                                        <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=reseller"><i class="ti-user"></i>Reseller</a></li>
                                    <?php }?>
                                    <?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'subadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'reseller' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'administrator' || $_smarty_tpl->tpl_vars['panel_restricted_2']->value) {?>
                                        <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=sub-reseller"><i class="ti-user"></i>Sub Reseller</a></li>
                                    <?php }?>
                                <?php }?>
                            </ul>
                        </div><!-- end  Project-->
                        <div id="MetricaEcommerce" class="main-icon-menu-pane">
                            <div class="title-box">
                                <h6 class="menu-title">Gestión de clientes</h6>           
                            </div>
                            <ul class="nav">
                                <li class="nav-item"><a class="nav-link" href="javascript:void(0);" onclick="add_user()"><i class="ti-user"></i>Agregar cliente</a></li>
                                <li class="nav-item"><a class="nav-link" href="javascript:void(0);" onclick="instantCreate()"><i class="ti-user"></i>Generar prueba</a></li>
                            </ul>
                        </div><!-- end Ecommerce -->
                        <div id="MetricaCRM" class="main-icon-menu-pane">
                            <div class="title-box">
                                <h6 class="menu-title">Monitoreo de clientes</h6>          
                            </div>
                            <ul class="nav" id="main_menu_side_nav">
                                <!--li class="nav-item">
                                    <a class="nav-link" href="#"><i class="ti-user"></i><span class="w-100">Active Clients</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                                    <ul class="nav-second-level" aria-expanded="false">
                                        <li><a href="active-premium">Premium Clients</a></li>
                                        <li><a href="active-vip">VIP Client</a></li>  
                                        <li><a href="active-private">Private Client</a></li>  
                                    </ul>            
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#"><i class="ti-user"></i><span class="w-100">Inactive Clients</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                                    <ul class="nav-second-level" aria-expanded="false">
                                        <li><a href="inactive-premium">Premium Clients</a></li>
                                        <li><a href="inactive-vip">VIP Client</a></li>  
                                        <li><a href="inactive-private">Private Client</a></li>  
                                    </ul>            
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#"><i class="ti-user"></i><span class="w-100">Trial Clients</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                                    <ul class="nav-second-level" aria-expanded="false">
                                        <li><a href="bulk-premium">Premium Clients</a></li>
                                        <li><a href="bulk-vip">VIP Clients</a></li>  
                                        <li><a href="bulk-private">Private Clients</a></li>  
                                    </ul>            
                                </li-->
                                <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=active-premium"><i class="ti-user"></i>Clientes activos</a></li>
                                <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=inactive-premium"><i class="ti-user"></i>Clientes inactivos</a></li>
                                <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=bulk-premium"><i class="ti-user"></i>Clientes de prueba</a></li>
                                <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=freezed-client"><i class="ti-lock"></i>Clientes congelados</a></li>
                                <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=deleted-client"><i class="ti-na"></i>Clientes eliminados</a></li>
                            </ul>
                        </div><!-- end CRM -->
                        <div id="MetricaFinance" class="main-icon-menu-pane">
                            <div class="title-box">
                                <h6 class="menu-title">Finanzas</h6>
                            </div>
                            <ul class="nav" id="main_menu_side_nav">
                                <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=finance-add"><i class="ti-wallet"></i>Agregar saldo</a></li>
                                <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=finance-history"><i class="ti-back-left"></i>Historial de recargas</a></li>
                                <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=finance-admin"><i class="ti-money"></i>Retiros</a></li>
                            </ul>
                        </div><!-- end Finance -->
                        <div id="MetricaOthers" class="main-icon-menu-pane">
                            <div class="title-box">
                                <h6 class="menu-title">Gestión del servidor</h6>      
                            </div>
                            <ul class="nav metismenu" id="main_menu_side_nav">
                                <!--li class="nav-item">
                                    <a class="nav-link" href="#"><i class="dripicons-mail"></i><span class="w-100">Email</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                                    <ul class="nav-second-level" aria-expanded="false">
                                        <li><a href="../others/email-inbox.html">Inbox</a></li>
                                        <li><a href="../others/email-read.html">Read Email</a></li>            
                                    </ul>            
                                </li--><!--end nav-item-->
                                <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=server-status"><i class="ti-server"></i>El estado del servidor</a></li>
                                <?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'administrator' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'subadmin' || $_smarty_tpl->tpl_vars['panel_restricted_2']->value) {?>
                                <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=vpn-control"><i class="ti-layout-grid2"></i>VPN Multi-VPS</a></li>
                                <?php }?>
                                <?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin' || $_smarty_tpl->tpl_vars['user_level_2']->value == 'administrator' || $_smarty_tpl->tpl_vars['panel_restricted_2']->value) {?>
                                <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=online-users-tcp"><i class="ti-layers-alt"></i>Usuarios en línea (TCP)</a></li>
                                <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=online-users-udp"><i class="ti-layers-alt"></i>Usuarios en línea (UDP)</a></li>
                                <?php }?>
                            </ul><!--end nav-->
                        </div><!-- end Others -->

                        <!--div id="MetricaPages" class="main-icon-menu-pane">
                            <div class="title-box">
                                <h6 class="menu-title">Pages</h6>        
                            </div>
                            <ul class="nav">
                                <li class="nav-item"><a class="nav-link" href="../pages/pages-profile.html"><i class="dripicons-user"></i>Profile</a></li>
                                <li class="nav-item"><a class="nav-link" href="../pages/pages-chat.html"><i class="dripicons-conversation"></i>Chat</a></li>
                                <li class="nav-item"><a class="nav-link" href="../pages/pages-contact-list.html"><i class="dripicons-user-id"></i>Contact List</a></li>
                                <li class="nav-item"><a class="nav-link" href="../pages/pages-tour.html"><i class="dripicons-rocket"></i>Tour</a></li>
                                <li class="nav-item"><a class="nav-link" href="../pages/pages-timeline.html"><i class="dripicons-clock"></i>Timeline</a></li>
                                <li class="nav-item"><a class="nav-link" href="../pages/pages-invoice.html"><i class="dripicons-document"></i>Invoice</a></li>
                                <li class="nav-item"><a class="nav-link" href="../pages/pages-treeview.html"><i class="dripicons-network-3"></i>Treeview</a></li>
                                <li class="nav-item"><a class="nav-link" href="../pages/pages-starter.html"><i class="dripicons-clipboard"></i>Starter Page</a></li>
                                <li class="nav-item"><a class="nav-link" href="../pages/pages-pricing.html"><i class="dripicons-article"></i>Pricing</a></li>
                                <li class="nav-item"><a class="nav-link" href="../pages/pages-blogs.html"><i class="dripicons-blog"></i>Blogs</a></li>
                                <li class="nav-item"><a class="nav-link" href="../pages/pages-faq.html"><i class="dripicons-question"></i>FAQs</a></li>
                                <li class="nav-item"><a class="nav-link" href="../pages/pages-gallery.html"><i class="dripicons-photo-group"></i>Gallery</a></li>
                            </ul>
                        </div--><!-- end Pages -->
                        <div id="MetricaAuthentication" class="main-icon-menu-pane">
                            <div class="title-box">
                                <h6 class="menu-title">Historial</h6>     
                            </div>
                            <ul class="nav">
                                <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=credit-logs"><i class="ti-book"></i>Registros de crédito</a></li>
                                <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=voucher-logs"><i class="ti-agenda"></i>Registros de cupones</a></li>
                            </ul>
                        </div><!-- end Authentication-->
                    </div><!--end menu-body-->
                </div><!-- end main-menu-inner-->
            </div>
    <!-- end left-sidenav-->

<?php if ($_smarty_tpl->tpl_vars['panel_restricted_2']->value) {
echo '<script'; ?>
>window.PM_LOCK_URL = '<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=access-lock';<?php echo '</script'; ?>
>

<?php echo '<script'; ?>
>
(function(){
    var lockUrl = window.PM_LOCK_URL || '';
    if(!lockUrl){ return; }

    var allowPages = {
        'dashboard': true,
        'my-profile': true,
        'finance-add': true,
        'finance-history': true,
        'finance-checkout': true,
        'finance-methods': true,
        'finance-admin': true,
        'finance-webhook': true,
        'logout': true,
        'access-lock': true
    };

    function getPageFromHref(href){
        try{
            var url = new URL(href, window.location.origin);
            return (url.searchParams.get('p') || '').toLowerCase();
        }catch(e){
            var m = String(href).match(/[?&]p=([^&#]+)/i);
            return m ? decodeURIComponent(m[1]).toLowerCase() : '';
        }
    }

    function lockAnchor(a){
        if(!a){ return; }
        a.setAttribute('title', 'Acceso no disponible');
        a.setAttribute('href', lockUrl);
        a.onclick = function(ev){
            ev.preventDefault();
            window.location.href = lockUrl;
        };
    }

    var anchors = document.querySelectorAll('.left-sidenav a.nav-link');
    for(var i=0; i<anchors.length; i++){
        var a = anchors[i];
        var href = (a.getAttribute('href') || '').trim();
        var hasOnclick = a.hasAttribute('onclick');

        if(href.toLowerCase().indexOf('javascript:') === 0 || hasOnclick){
            lockAnchor(a);
            continue;
        }

        if(href.toLowerCase().indexOf('index.php?p=') !== -1){
            var p = getPageFromHref(href);
            if(!allowPages[p]){
                lockAnchor(a);
            }
        }
    }
})();
<?php echo '</script'; ?>
>

<?php }
}
}
