{literal}
<style>
  .topbar{height:50px!important;}
  .navbar-custom{height:50px!important;min-height:50px!important;}

  .topbar-left{
    height:50px!important;
    display:flex!important;
    align-items:center!important;
    padding:0 12px!important;
    gap:8px!important;
    position:relative!important;
    z-index:10!important;
  }

  .topbar-left .logo{height:50px!important;display:flex!important;align-items:center!important;}
  .topbar-left .logo img{height:36px!important;width:auto!important;display:block!important;}

  /* Botón visible: mismo estilo del theme (sin caja) */
  #menuBtnVisible{
    height:50px!important;
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
</style>
{/literal}

<div class="topbar">
  <div class="topbar-left">
    <a href="{$base_url}" class="logo">
      <img src="{$base_url}firenet/assets/images/vicath2.png" alt="logo" class="logo-lg" style="height:36px;">
    </a>

    <!-- Visible junto al logo (sin caja) -->
    <button type="button" id="menuBtnVisible" aria-label="Menu">
      <i class="dripicons-menu nav-icon"></i>
    </button>
  </div>

  <nav class="navbar-custom">
    <ul class="list-unstyled topbar-nav float-right mb-0">
      <li class="dropdown">
        <a class="nav-link dropdown-toggle waves-effect waves-light nav-user"
           data-toggle="dropdown" href="#" role="button">
          {$avatar}
          <span class="ml-1 nav-user-name hidden-sm">{$full_name_2} <i class="mdi mdi-chevron-down"></i></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <div class="dropdown-item"><i class="fas fa-coins text-muted mr-2"></i> {$credits_bal} Credito(s)</div>
          <a class="dropdown-item" href="my-profile"><i class="dripicons-user text-muted mr-2"></i> Perfil</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="logout" data-toggle="modal" data-target="#logoutModal">
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

{literal}
<script>
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
</script>
{/literal}
