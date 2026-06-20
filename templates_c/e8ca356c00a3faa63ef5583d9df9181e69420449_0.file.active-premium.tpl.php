<?php
/* Smarty version 3.1.29, created on 2026-03-11 19:13:43
  from "C:\xampp\htdocs\panel_web\templates\active-premium.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_69b21347bddce2_99908726',
  'file_dependency' => 
  array (
    'e8ca356c00a3faa63ef5583d9df9181e69420449' => 
    array (
      0 => 'C:\\xampp\\htdocs\\panel_web\\templates\\active-premium.tpl',
      1 => 1770344268,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:css/custom_css.tpl' => 1,
    'file:css/extra_button.tpl' => 1,
    'file:css/formvalidation_css.tpl' => 1,
    'file:apps/topnav.tpl' => 1,
    'file:apps/sidenavi.tpl' => 1,
    'file:apps/footer.tpl' => 1,
    'file:apps/modals.tpl' => 1,
    'file:js/jqueryui_js.tpl' => 1,
    'file:js/formvalidation_js.tpl' => 1,
    'file:js/active-premium-client.tpl' => 1,
    'file:js/pass_toggle.tpl' => 1,
  ),
),false)) {
function content_69b21347bddce2_99908726 ($_smarty_tpl) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8" />
        <title><?php echo $_smarty_tpl->tpl_vars['siteTitle']->value;?>
 - Clientes Activos</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A premium admin dashboard template by Mannatthemes" name="description" />
        <meta content="Mannatthemes" name="author" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/images/v.png">


        <!-- App css -->
        <link href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/css/style.css" rel="stylesheet" type="text/css" />

        <!-- DataTables -->
        <link href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
        
        <!-- Responsive datatable examples -->
        <link href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
firenet/assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" /> 
        
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

        <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:css/extra_button.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:css/formvalidation_css.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

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
                                        <table id="users-serverside" class="table table-striped table-hover table-bordered dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th><input type="checkbox" class="select-all" /></th>
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

        <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:js/jqueryui_js.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:js/formvalidation_js.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:js/active-premium-client.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:js/pass_toggle.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

</body>
</html><?php }
}
