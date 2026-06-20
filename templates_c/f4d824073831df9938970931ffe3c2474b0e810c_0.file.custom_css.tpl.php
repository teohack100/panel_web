<?php
/* Smarty version 3.1.29, created on 2026-03-11 19:13:43
  from "C:\xampp\htdocs\panel_web\templates\css\custom_css.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_69b21347bf75a2_17563419',
  'file_dependency' => 
  array (
    'f4d824073831df9938970931ffe3c2474b0e810c' => 
    array (
      0 => 'C:\\xampp\\htdocs\\panel_web\\templates\\css\\custom_css.tpl',
      1 => 1771963366,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b21347bf75a2_17563419 ($_smarty_tpl) {
?>
<style>
:root {
    --pm-tenant-primary: <?php if (isset($_smarty_tpl->tpl_vars['saas_ctx']->value['primary_color']) && $_smarty_tpl->tpl_vars['saas_ctx']->value['primary_color'] != '') {
echo $_smarty_tpl->tpl_vars['saas_ctx']->value['primary_color'];
} else { ?>#2fbde5<?php }?>;
    --pm-tenant-accent: <?php if (isset($_smarty_tpl->tpl_vars['saas_ctx']->value['accent_color']) && $_smarty_tpl->tpl_vars['saas_ctx']->value['accent_color'] != '') {
echo $_smarty_tpl->tpl_vars['saas_ctx']->value['accent_color'];
} else { ?>#95f100<?php }?>;
    --pm-tenant-bg: <?php if (isset($_smarty_tpl->tpl_vars['saas_ctx']->value['background_color']) && $_smarty_tpl->tpl_vars['saas_ctx']->value['background_color'] != '') {
echo $_smarty_tpl->tpl_vars['saas_ctx']->value['background_color'];
} else { ?>#132744<?php }?>;
}

.main-menu-inner .menu-body .nav-link:active, .main-menu-inner .menu-body .nav-link.active {
    color: var(--pm-tenant-primary) !important;
}

.main-menu-inner .menu-body .nav-item .nav-link.active i, .main-menu-inner .menu-body .nav-item .nav-link.active {
    color: var(--pm-tenant-primary) !important;
}

.main-menu-inner .menu-body .nav-link:hover, .main-menu-inner .menu-body .nav-link:focus, .main-menu-inner .menu-body .nav-link:hover i, .main-menu-inner .menu-body .nav-link:focus i {
    color: var(--pm-tenant-primary) !important;
}

.dataTables_processing {
    display: none !important;
}

.dataTables_wrapper .dataTables_processing {
    display: none !important;
}

.navbar-custom .nav-link {
    line-height: 70px !important;
    max-height: 70px !important;
    padding-top: 0 !important;
    padding-bottom: 0 !important;
}

.met-profile .met-profile-main .met-profile-main-pic {
    width: 170px !important;
    height: 170px !important;
    max-width: 170px !important;
    max-height: 170px !important;
    overflow: hidden !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    line-height: 0 !important;
}

.met-profile .met-profile-main .met-profile-main-pic img {
    width: 100% !important;
    height: 100% !important;
    display: block;
    object-fit: cover;
    object-position: center center !important;
    margin: 0 !important;
    padding: 0 !important;
    border: 0 !important;
}

#img {
    display: block;
    width: 170px !important;
    height: 170px !important;
    max-width: 170px !important;
    max-height: 170px !important;
    overflow: hidden !important;
    border-radius: 50% !important;
    line-height: 0 !important;
}

#img img {
    width: 100% !important;
    height: 100% !important;
    display: block;
    object-fit: cover;
    object-position: center center !important;
    margin: 0 !important;
    padding: 0 !important;
    border: 0 !important;
}

.nav-user img {
    width: 40px !important;
    height: 40px !important;
    object-fit: cover;
    border-radius: 50%;
    display: block !important;
    margin: 0 !important;
    padding: 0 !important;
}

.nav-user {
    display: flex !important;
    align-items: center !important;
    height: 70px !important;
    line-height: 70px !important;
    gap: 8px;
    padding-top: 0 !important;
    padding-bottom: 0 !important;
}

.navbar-custom .topbar-nav {
    display: flex !important;
    align-items: center !important;
    height: 70px !important;
}

.navbar-custom .topbar-nav > li {
    float: none !important;
}

.navbar-custom .topbar-nav .nav-link {
    display: flex !important;
    align-items: center !important;
    height: 70px !important;
}
</style>
<?php if (isset($_smarty_tpl->tpl_vars['saas_custom_css']->value) && $_smarty_tpl->tpl_vars['saas_custom_css']->value != '') {?>
<style><?php echo $_smarty_tpl->tpl_vars['saas_custom_css']->value;?>
</style>
<?php }
}
}
