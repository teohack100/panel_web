<?php
/* Smarty version 3.1.29, created on 2026-03-11 19:13:43
  from "C:\xampp\htdocs\panel_web\templates\apps\topnav.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_69b21347c2aa76_99203185',
  'file_dependency' => 
  array (
    '1e8ff512d650b275f0ddb753231ccd084c115c1c' => 
    array (
      0 => 'C:\\xampp\\htdocs\\panel_web\\templates\\apps\\topnav.tpl',
      1 => 1772811900,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b21347c2aa76_99203185 ($_smarty_tpl) {
?>

<style>
  .topbar{height:70px!important;}
  .navbar-custom{height:70px!important;min-height:70px!important;}

  .topbar-left{
    height:70px!important;
    display:flex!important;
    align-items:center!important;
    padding:0 12px!important;
    gap:8px!important;
    position:relative!important;
    z-index:10!important;
  }

  .topbar-left .logo{height:70px!important;display:flex!important;align-items:center!important;}
  .topbar-left .logo img{height:44px!important;width:auto!important;display:block!important;}

  /* Botón visible: mismo estilo del theme (sin caja) */
  #menuBtnVisible{
    height:70px!important;
    display:flex!important;
    align-items:center!important;
    justify-content:center!important;
    padding:0 8px!important;
    margin:0!important;
    border:0!important;
    background:transparent!important;
    box-shadow:none!important;
    cursor:pointer!important;
  }
  #menuBtnVisible .nav-icon{font-size:24px!important;line-height:1!important;}

  /* Oculto pero mantiene el JS original del theme */
  #menuBtnHiddenWrap{
    position:absolute!important;
    left:-9999px!important;
    top:-9999px!important;
    width:1px!important;
    height:1px!important;
    overflow:hidden!important;
  }

  .credits-infinity-menu{
    display:inline-block!important;
    font-size:1.35rem!important;
    font-weight:700!important;
    line-height:1!important;
    vertical-align:middle!important;
  }

  .finance-top-actions{
    position:absolute!important;
    left:50%!important;
    top:50%!important;
    transform:translate(-50%,-50%)!important;
    display:flex!important;
    align-items:center!important;
    gap:10px!important;
    z-index:5!important;
  }
  .finance-top-balance{
    display:inline-flex!important;
    align-items:center!important;
    gap:8px!important;
    border:1px solid rgba(77,122,187,.6)!important;
    border-radius:999px!important;
    background:#132744!important;
    color:#ecf4ff!important;
    font-weight:800!important;
    line-height:1!important;
    padding:8px 12px!important;
  }
  .finance-top-add{
    display:inline-flex!important;
    align-items:center!important;
    gap:8px!important;
    border-radius:10px!important;
    background:#95f100!important;
    color:#0d1b2c!important;
    font-weight:900!important;
    line-height:1!important;
    padding:10px 14px!important;
    text-decoration:none!important;
  }
  .finance-top-add:hover{opacity:.92!important}
  @media (max-width:1280px){
    .finance-top-actions{
      left:50%!important;
      gap:8px!important;
    }
    .finance-top-balance{
      padding:7px 10px!important;
      font-size:14px!important;
    }
    .finance-top-add{
      padding:8px 11px!important;
      font-size:12px!important;
      letter-spacing:.06em!important;
    }
    .topbar-nav .nav-user-name{
      display:none!important;
    }
  }
  @media (max-width:980px){
    .finance-top-balance{
      display:inline-flex!important;
      padding:6px 9px!important;
      font-size:12px!important;
      gap:6px!important;
    }
    .finance-top-add{
      padding:8px 10px!important;
      border-radius:9px!important;
      font-size:11px!important;
    }
  }
  @media (max-width:760px){
    .finance-top-actions{
      display:none!important;
    }
  }
</style>



<?php echo '<script'; ?>
>
  (function(){
    document.body.classList.add('enlarge-menu');
    document.body.setAttribute('data-keep-enlarged','true');
  })();
<?php echo '</script'; ?>
>


<div class="topbar">
  <?php $_smarty_tpl->tpl_vars['programmit_logo'] = new Smarty_Variable(((string)$_smarty_tpl->tpl_vars['base_url']->value)."logo/icon_panel.png", null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'programmit_logo', 0);?>
  <?php if (isset($_smarty_tpl->tpl_vars['panel_logo_url']->value) && $_smarty_tpl->tpl_vars['panel_logo_url']->value != '') {?>
    <?php $_smarty_tpl->tpl_vars['programmit_logo'] = new Smarty_Variable($_smarty_tpl->tpl_vars['panel_logo_url']->value, null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'programmit_logo', 0);?>
  <?php }?>
  <?php if (isset($_smarty_tpl->tpl_vars['saas_ctx']->value['logo_url']) && $_smarty_tpl->tpl_vars['saas_ctx']->value['logo_url'] != '') {?>
    <?php $_smarty_tpl->tpl_vars['programmit_logo'] = new Smarty_Variable($_smarty_tpl->tpl_vars['saas_ctx']->value['logo_url'], null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'programmit_logo', 0);?>
  <?php }?>
  <div class="topbar-left">
    <a href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
" class="logo">
      <img src="<?php echo $_smarty_tpl->tpl_vars['programmit_logo']->value;?>
" alt="logo" class="logo-lg" style="height:36px;">
    </a>

    <!-- Visible junto al logo (sin caja) -->
    <button type="button" id="menuBtnVisible" aria-label="Menu">
      <i class="dripicons-menu nav-icon"></i>
    </button>
  </div>

  <nav class="navbar-custom">
    <div class="finance-top-actions">
      <span class="finance-top-balance"><i class="fas fa-sack-dollar"></i> $US <?php if (isset($_smarty_tpl->tpl_vars['wallet_balance_2']->value)) {
echo $_smarty_tpl->tpl_vars['wallet_balance_2']->value;
} else { ?>0<?php }?></span>
      <a class="finance-top-add" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=finance-add"><i class="fas fa-plus"></i> AGREGAR SALDO</a>
    </div>

    <ul class="list-unstyled topbar-nav float-right mb-0">
      <li class="dropdown">
        <a class="nav-link dropdown-toggle waves-effect waves-light nav-user"
           data-toggle="dropdown" href="#" role="button">
          <?php echo $_smarty_tpl->tpl_vars['avatar']->value;?>

          <span class="ml-1 nav-user-name hidden-sm"><?php echo $_smarty_tpl->tpl_vars['full_name_2']->value;?>
 <i class="mdi mdi-chevron-down"></i></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <?php if (!$_smarty_tpl->tpl_vars['control_is_host']->value) {?>
          <div class="dropdown-item"><i class="fas fa-coins text-muted mr-2"></i>
            <?php if ($_smarty_tpl->tpl_vars['user_id_2']->value == 1 || $_smarty_tpl->tpl_vars['user_level_2']->value == 'superadmin') {?>
              <span class="credits-infinity-menu">&infin;</span>
            <?php } else { ?>
              <?php echo $_smarty_tpl->tpl_vars['credits_bal']->value;?>
 Credito(s)
            <?php }?>
          </div>
          <?php }?>
          <a class="dropdown-item" href="<?php echo $_smarty_tpl->tpl_vars['base_url']->value;?>
index.php?p=my-profile"><i class="dripicons-user text-muted mr-2"></i> Perfil</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#logoutModal">
            <i class="dripicons-exit text-muted mr-2"></i> Cerrar sesión
          </a>
        </div>
      </li>
    </ul>

    <!-- Botón original oculto (para que el toggle funcione igual) -->
    <div id="menuBtnHiddenWrap">
      <button type="button" id="menuBtnHidden" class="button-menu-mobile nav-link waves-effect waves-light">
        <i class="dripicons-menu nav-icon"></i>
      </button>
    </div>
  </nav>
</div>


<?php echo '<script'; ?>
>
  (function(){
    var v=document.getElementById('menuBtnVisible');
    var h=document.getElementById('menuBtnHidden');
    if(!v||!h) return;
    v.addEventListener('click',function(e){
      e.preventDefault();
      e.stopPropagation();
      h.click();
    });
  })();
<?php echo '</script'; ?>
>

<?php }
}
