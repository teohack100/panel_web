<style>
  :root{
    --pm-brand-primary:{$panel_color_principal|default:'#4F96EE'};
    --pm-brand-bg-main:{$panel_fondo_principal|default:'#0B2A55'};
    --pm-brand-bg-secondary:{$panel_fondo_secundario|default:'#18467F'};
    --pm-brand-bg-tertiary:{$panel_fondo_terciario|default:'#2B68AB'};
    --pm-brand-text-primary:{$panel_texto_principal|default:'#FFFFFF'};
    --pm-brand-text-secondary:{$panel_texto_secundario|default:'#C7E0FF'};
  }
</style>
{literal}
<style>
  :root{
    --pm-blue-975:color-mix(in srgb, var(--pm-brand-bg-main) 78%, black 22%);
    --pm-blue-950:var(--pm-brand-bg-main);
    --pm-blue-925:color-mix(in srgb, var(--pm-brand-bg-secondary) 72%, var(--pm-brand-bg-main) 28%);
    --pm-blue-900:var(--pm-brand-bg-secondary);
    --pm-blue-850:color-mix(in srgb, var(--pm-brand-bg-tertiary) 72%, var(--pm-brand-bg-secondary) 28%);
    --pm-blue-800:color-mix(in srgb, var(--pm-brand-primary) 40%, var(--pm-brand-bg-secondary) 60%);
    --pm-blue-700:color-mix(in srgb, var(--pm-brand-primary) 68%, var(--pm-brand-bg-secondary) 32%);
    --pm-blue-650:color-mix(in srgb, var(--pm-brand-primary) 82%, white 18%);
    --pm-blue-600:var(--pm-brand-primary);
    --pm-blue-500:color-mix(in srgb, var(--pm-brand-primary) 68%, white 32%);
    --pm-blue-300:color-mix(in srgb, var(--pm-brand-text-primary) 18%, var(--pm-brand-primary) 82%);
    --pm-blue-200:color-mix(in srgb, var(--pm-brand-text-primary) 54%, var(--pm-brand-primary) 46%);
    --pm-topbar-bg:color-mix(in srgb, white 82%, var(--pm-brand-primary) 18%);
    --pm-topbar-border:color-mix(in srgb, white 56%, var(--pm-brand-primary) 44%);
    --pm-topbar-text:color-mix(in srgb, var(--pm-brand-bg-secondary) 78%, var(--pm-brand-primary) 22%);
  }
  .bg-success,
  .badge-success,
  .btn-success,
  .btn-gradient-success,
  .progress-bar-success{
    background:linear-gradient(180deg,var(--pm-blue-600) 0%, var(--pm-blue-700) 100%)!important;
    border-color:#4b8fe7!important;
    color:#ffffff!important;
  }
  .shadow-success{
    box-shadow:0 10px 24px rgba(31,95,182,.18)!important;
  }
  .text-success,
  .icon-success{
    color:var(--pm-blue-600)!important;
  }
  .border-success{
    border-color:#4b8fe7!important;
  }
  .badge-soft-success{
    background:rgba(49,106,187,.16)!important;
    color:#e8f2ff!important;
    border:1px solid rgba(91,148,222,.28)!important;
  }
  .btn-success:hover,
  .btn-gradient-success:hover{
    background:linear-gradient(180deg,var(--pm-blue-500) 0%, var(--pm-blue-650) 100%)!important;
    border-color:#71b1ff!important;
    color:#ffffff!important;
  }
  .btn-success:focus,
  .btn-gradient-success:focus{
    box-shadow:0 0 0 .2rem rgba(84,144,221,.22)!important;
  }
  .topbar{
    height:70px!important;
    min-height:70px!important;
    position:sticky!important;
    top:0!important;
    left:0!important;
    right:0!important;
    display:flex!important;
    align-items:center!important;
    background:linear-gradient(180deg,color-mix(in srgb, white 88%, var(--pm-brand-primary) 12%) 0%, color-mix(in srgb, white 82%, var(--pm-brand-primary) 18%) 52%, color-mix(in srgb, white 74%, var(--pm-brand-primary) 26%) 100%)!important;
    border-bottom:1px solid var(--pm-topbar-border)!important;
    box-shadow:0 10px 24px rgba(24,70,127,.08)!important;
    z-index:1100!important;
    overflow:visible!important;
  }
  .topbar:before{
    content:""!important;
    position:absolute!important;
    inset:0 0 auto 0!important;
    height:1px!important;
    background:rgba(255,255,255,.58)!important;
    pointer-events:none!important;
  }
  .topbar:after{
    content:""!important;
    position:absolute!important;
    left:0!important;
    right:0!important;
    bottom:0!important;
    height:1px!important;
    background:rgba(18,72,136,.14)!important;
    pointer-events:none!important;
  }
  .page-wrapper{
    padding-top:0!important;
  }
  .page-content{
    transition:margin-left .18s cubic-bezier(.22,1,.36,1)!important;
    will-change:margin-left!important;
  }
  .navbar-custom{
    height:70px!important;
    min-height:70px!important;
    position:static!important;
    overflow:visible!important;
    flex:1 1 auto!important;
    display:flex!important;
    align-items:center!important;
    justify-content:flex-end!important;
    padding:0 14px!important 0 10px!important;
    margin-left:0!important;
    background:transparent!important;
    box-shadow:none!important;
  }

  .topbar-left{
    height:70px!important;
    width:auto!important;
    display:flex!important;
    align-items:center!important;
    padding:0 12px!important;
    gap:8px!important;
    position:relative!important;
    z-index:10!important;
    flex:0 0 auto!important;
    float:none!important;
    background:transparent!important;
    box-shadow:none!important;
  }

  .topbar-left .logo{
    height:70px!important;
    display:flex!important;
    align-items:center!important;
    background:transparent!important;
  }
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
    border-radius:4px!important;
    transition:background .16s ease, transform .16s ease!important;
  }
  #menuBtnVisible:hover{
    background:rgba(44,114,193,.10)!important;
    transform:translateY(-1px)!important;
  }
  #menuBtnVisible .nav-icon{
    font-size:24px!important;
    line-height:1!important;
    color:var(--pm-topbar-text)!important;
  }

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
  .nav-user-credits-item{
    display:flex!important;
    align-items:center!important;
    gap:8px!important;
    color:var(--pm-topbar-text)!important;
    font-weight:400!important;
    font-size:14px!important;
  }
  .nav-user .nav-user-name{
    display:inline-flex!important;
    align-items:center!important;
    gap:6px!important;
    max-width:180px!important;
    color:var(--pm-topbar-text)!important;
    font-weight:400!important;
    font-size:15px!important;
    letter-spacing:0!important;
    white-space:nowrap!important;
    overflow:hidden!important;
    text-overflow:ellipsis!important;
    vertical-align:middle!important;
  }
  .nav-user .nav-user-name i{
    flex:0 0 auto!important;
    font-size:15px!important;
    color:#8ea4c1!important;
  }

  .finance-top-actions{
    position:absolute!important;
    left:50%!important;
    top:50%!important;
    transform:translate(-50%,-50%)!important;
    display:flex!important;
    align-items:center!important;
    gap:10px!important;
    justify-content:center!important;
    min-width:0!important;
    padding:0!important;
    z-index:3!important;
    pointer-events:auto!important;
  }
  .finance-top-balance{
    display:inline-flex!important;
    align-items:center!important;
    justify-content:center!important;
    gap:8px!important;
    border:1px solid rgba(24,73,132,.34)!important;
    border-radius:2px!important;
    background:linear-gradient(180deg,color-mix(in srgb, var(--pm-brand-primary) 36%, var(--pm-brand-bg-secondary) 64%) 0%, color-mix(in srgb, var(--pm-brand-bg-main) 76%, var(--pm-brand-primary) 24%) 100%)!important;
    color:#f4f8ff!important;
    font-weight:800!important;
    line-height:1!important;
    height:36px!important;
    padding:0 12px!important;
    box-shadow:0 10px 22px rgba(16,54,108,.20)!important, inset 0 1px 0 rgba(255,255,255,.08)!important;
    overflow:hidden!important;
    box-sizing:border-box!important;
  }
  .finance-top-add{
    display:inline-flex!important;
    align-items:center!important;
    justify-content:center!important;
    gap:8px!important;
    border:1px solid rgba(41,112,206,.34)!important;
    border-radius:2px!important;
    background:linear-gradient(180deg,color-mix(in srgb, var(--pm-brand-primary) 82%, white 18%) 0%, color-mix(in srgb, var(--pm-brand-primary) 74%, var(--pm-brand-bg-secondary) 26%) 100%)!important;
    color:#ffffff!important;
    font-weight:800!important;
    line-height:1!important;
    height:36px!important;
    padding:0 14px!important;
    text-decoration:none!important;
    box-shadow:0 12px 22px rgba(25,82,156,.22)!important, inset 0 1px 0 rgba(255,255,255,.16)!important;
    overflow:hidden!important;
    box-sizing:border-box!important;
  }
  .finance-top-add:hover{
    opacity:1!important;
    color:#ffffff!important;
    border-color:#9ec8ff!important;
    background:linear-gradient(180deg,color-mix(in srgb, var(--pm-brand-primary) 72%, white 28%) 0%, color-mix(in srgb, var(--pm-brand-primary) 82%, var(--pm-brand-bg-secondary) 18%) 100%)!important;
    box-shadow:0 14px 24px rgba(34,96,177,.24)!important, inset 0 1px 0 rgba(255,255,255,.2)!important;
  }
  .finance-top-add.is-muted{
    background:linear-gradient(180deg,var(--pm-blue-850) 0%, var(--pm-blue-900) 100%)!important;
    color:#eef6ff!important;
    border:1px solid #234c82!important;
  }
  .finance-top-add.is-muted:hover{
    opacity:1!important;
    color:#ffffff!important;
    background:linear-gradient(180deg,color-mix(in srgb, var(--pm-brand-primary) 22%, var(--pm-brand-bg-secondary) 78%) 0%, color-mix(in srgb, var(--pm-brand-primary) 16%, var(--pm-brand-bg-main) 84%) 100%)!important;
    border-color:#4f86c4!important;
  }
  .topbar-nav{
    flex:0 0 auto!important;
    margin-left:auto!important;
    display:flex!important;
    align-items:center!important;
    position:relative!important;
    z-index:5!important;
  }
  .nav-user img{
    box-shadow:0 8px 18px rgba(43,104,171,.18)!important;
  }

  body,
  .page-wrapper,
  .page-content{
    background:linear-gradient(180deg,#eef5ff 0%, #e7f0ff 44%, #edf4ff 100%)!important;
  }
  .page-content{
    min-height:calc(100vh - 70px)!important;
  }
  .page-title-box .page-title{
    color:#173f74!important;
    font-weight:700!important;
    letter-spacing:-.02em!important;
  }
  .breadcrumb-item a,
  .breadcrumb-item.active{
    color:#6d89ae!important;
  }
  .breadcrumb-item + .breadcrumb-item::before{
    color:#91add0!important;
  }

  .page-content .card{
    border:1px solid rgba(41,104,179,.14)!important;
    border-radius:14px!important;
    background:linear-gradient(180deg,rgba(255,255,255,.98) 0%, #f5f9ff 100%)!important;
    box-shadow:0 18px 38px rgba(18,59,118,.08)!important;
    overflow:hidden!important;
  }
  .page-content .card-header{
    border-bottom:1px solid rgba(43,110,189,.14)!important;
    background:linear-gradient(180deg,#f5faff 0%, #e8f1ff 100%)!important;
    color:#214876!important;
    font-weight:700!important;
  }
  .page-content .card-footer{
    border-top:1px solid rgba(43,110,189,.14)!important;
    background:linear-gradient(180deg,#f5faff 0%, #edf4ff 100%)!important;
  }
  .page-content .card-body{
    color:#35527a!important;
  }

  .page-content .table-responsive{
    border:1px solid rgba(43,110,189,.12)!important;
    border-radius:12px!important;
    background:linear-gradient(180deg,rgba(255,255,255,.96) 0%, #f7fbff 100%)!important;
    box-shadow:inset 0 1px 0 rgba(255,255,255,.72)!important;
  }
  .page-content .table{
    margin-bottom:0!important;
    color:#28486f!important;
    background:transparent!important;
  }
  .page-content .table thead th{
    border-top:0!important;
    border-bottom:1px solid rgba(43,110,189,.16)!important;
    background:linear-gradient(180deg,#f2f8ff 0%, #dceafb 100%)!important;
    color:#355a85!important;
    font-size:.76rem!important;
    font-weight:700!important;
    letter-spacing:.04em!important;
    text-transform:uppercase!important;
  }
  .page-content .table td,
  .page-content .table th{
    border-color:rgba(43,110,189,.12)!important;
  }
  .page-content .table-striped tbody tr:nth-of-type(odd){
    background:rgba(103,155,229,.05)!important;
  }
  .page-content .table-hover tbody tr:hover{
    background:rgba(92,153,232,.10)!important;
  }
  .page-content .table code{
    color:#184a86!important;
    background:rgba(101,159,236,.10)!important;
    border:1px solid rgba(80,135,208,.14)!important;
    border-radius:6px!important;
    padding:2px 6px!important;
  }

  .form-control,
  .custom-select{
    border:1px solid rgba(52,112,191,.18)!important;
    border-radius:10px!important;
    background:#f9fbff!important;
    color:#1f4069!important;
    box-shadow:none!important;
  }
  .form-control:focus,
  .custom-select:focus{
    border-color:var(--pm-blue-600)!important;
    background:#ffffff!important;
    box-shadow:0 0 0 .18rem rgba(98,157,235,.16)!important;
  }
  .input-group-text{
    border:1px solid rgba(52,112,191,.18)!important;
    background:linear-gradient(180deg,#eff6ff 0%, #dfebfb 100%)!important;
    color:#35639a!important;
  }

  .modal-content{
    border:1px solid rgba(38,95,168,.18)!important;
    border-radius:16px!important;
    background:linear-gradient(180deg,#fbfdff 0%, #eef5ff 100%)!important;
    box-shadow:0 24px 62px rgba(17,54,109,.24)!important;
  }
  .modal-header{
    border-bottom:1px solid rgba(43,110,189,.14)!important;
    background:linear-gradient(180deg,#f7fbff 0%, #e9f2ff 100%)!important;
  }
  .modal-title,
  .modal-title2{
    color:#183f73!important;
    font-weight:700!important;
  }
  .modal-footer{
    border-top:1px solid rgba(43,110,189,.14)!important;
    background:linear-gradient(180deg,#f7fbff 0%, #eef5ff 100%)!important;
  }
  .modal .close{
    color:#6b86a9!important;
    opacity:1!important;
    text-shadow:none!important;
  }

  .btn-primary{
    border-color:#4f93e9!important;
    background:linear-gradient(180deg,#63a5ff 0%, #3378d1 100%)!important;
    box-shadow:0 12px 24px rgba(39,102,181,.20)!important;
  }
  .btn-primary:hover{
    border-color:#84bbff!important;
    background:linear-gradient(180deg,#78b4ff 0%, #4389df 100%)!important;
  }
  .btn-outline-primary{
    border-color:#5c95dd!important;
    color:#2760a8!important;
    background:rgba(255,255,255,.72)!important;
  }
  .btn-outline-primary:hover{
    color:#ffffff!important;
    background:linear-gradient(180deg,#5ea6ff 0%, #3278d1 100%)!important;
    border-color:#5ea6ff!important;
  }
  .btn-secondary{
    border-color:#bfd5ef!important;
    color:#41658d!important;
    background:linear-gradient(180deg,#f7fbff 0%, #e5eefb 100%)!important;
  }
  .btn-secondary:hover{
    color:#24466f!important;
    border-color:#d0e2f8!important;
    background:linear-gradient(180deg,#ffffff 0%, #ecf4ff 100%)!important;
  }
  .badge-primary,
  .badge-info{
    background:linear-gradient(180deg,#5a9ef8 0%, #347ad4 100%)!important;
    color:#ffffff!important;
  }
  .badge-secondary{
    background:rgba(64,110,171,.12)!important;
    color:#315a88!important;
    border:1px solid rgba(76,125,190,.18)!important;
  }

  .dataTables_wrapper .dataTables_filter input,
  .dataTables_wrapper .dataTables_length select{
    border:1px solid rgba(52,112,191,.18)!important;
    border-radius:10px!important;
    background:#f9fbff!important;
    color:#1f4069!important;
    box-shadow:none!important;
  }
  .dataTables_wrapper .dataTables_paginate .paginate_button.current,
  .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover{
    border:1px solid #4f93e9!important;
    background:linear-gradient(180deg,#63a5ff 0%, #3378d1 100%)!important;
    color:#ffffff!important;
  }
  .dataTables_wrapper .dataTables_paginate .paginate_button:hover{
    border:1px solid #84bbff!important;
    background:linear-gradient(180deg,#78b4ff 0%, #4389df 100%)!important;
    color:#ffffff!important;
  }
  @media (max-width:1280px){
    .navbar-custom{padding-right:12px!important;padding-left:8px!important;}
    .finance-top-actions{gap:8px!important;}
    .finance-top-balance{
      height:34px!important;
      padding:0 10px!important;
      font-size:14px!important;
    }
    .finance-top-add{
      height:34px!important;
      padding:0 11px!important;
      font-size:12px!important;
      letter-spacing:.06em!important;
    }
  }
  @media (max-width:980px){
    .navbar-custom{padding-right:10px!important;padding-left:6px!important;}
    .finance-top-balance{
      display:inline-flex!important;
      height:32px!important;
      padding:0 9px!important;
      font-size:12px!important;
      gap:6px!important;
    }
    .finance-top-add{
      height:32px!important;
      padding:0 10px!important;
      border-radius:2px!important;
      font-size:11px!important;
    }
    .nav-user .nav-user-name{
      max-width:130px!important;
      font-size:13px!important;
    }
  }
  @media (max-width:760px){
    .finance-top-actions{gap:6px!important;}
    .finance-top-balance{
      height:30px!important;
      padding:0 8px!important;
      font-size:11px!important;
      gap:5px!important;
    }
    .finance-top-add{
      height:30px!important;
      padding:0 9px!important;
      border-radius:2px!important;
      font-size:10px!important;
      gap:6px!important;
    }
    .nav-user .nav-user-name{
      max-width:110px!important;
      font-size:12px!important;
    }
  }
  @media (max-width:560px){
    .finance-top-balance{
      height:28px!important;
      padding:0 7px!important;
      font-size:10px!important;
      gap:4px!important;
    }
    .finance-top-add{
      height:28px!important;
      padding:0 8px!important;
      border-radius:2px!important;
      font-size:10px!important;
      gap:5px!important;
    }
    .nav-user .nav-user-name{
      max-width:86px!important;
      font-size:11px!important;
    }
  }
  @media (min-width:680px){
    .page-wrapper{
      display:block!important;
      overflow-x:hidden!important;
    }
    .left-sidenav{
      position:fixed!important;
      top:70px!important;
      left:0!important;
      bottom:0!important;
      overflow:hidden!important;
      z-index:1000!important;
      width:252px!important;
      min-width:252px!important;
      max-width:252px!important;
      height:auto!important;
      max-height:none!important;
    }
    body.enlarge-menu .left-sidenav{
      position:fixed!important;
      top:70px!important;
      left:0!important;
      bottom:0!important;
      width:0!important;
      min-width:0!important;
      max-width:0!important;
      height:auto!important;
      max-height:none!important;
    }
    .page-content{
      width:auto!important;
      margin-left:252px!important;
      min-width:0!important;
    }
    body.enlarge-menu .page-content{
      width:auto!important;
      margin-left:0!important;
      min-width:0!important;
    }
  }
  @media (max-width:679px){
    .page-content{
      margin-left:0!important;
      width:100%!important;
    }
    body.enlarge-menu .page-content{
      margin-left:0!important;
      width:100%!important;
    }
  }
</style>
{/literal}

{literal}
<script>
  (function(){
    var key = 'pm_menu_expanded';
    function applyStoredMenuState(){
      var isExpanded = false;
      try {
        isExpanded = window.localStorage.getItem(key) === '1';
      } catch (e) {
        isExpanded = false;
      }

      document.body.setAttribute('data-keep-enlarged','true');

      if (window.innerWidth < 680) {
        document.body.classList.add('enlarge-menu');
        return;
      }

      if (isExpanded) {
        document.body.classList.remove('enlarge-menu');
      } else {
        document.body.classList.add('enlarge-menu');
      }
    }

    applyStoredMenuState();

    function reinforceState(times){
      applyStoredMenuState();
      if(times <= 0){
        return;
      }
      window.setTimeout(function(){
        reinforceState(times - 1);
      }, 60);
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', function(){
        reinforceState(8);
      });
    } else {
      reinforceState(8);
    }

    window.addEventListener('pageshow', function(){
      reinforceState(4);
    });
  })();
</script>
{/literal}

<div class="topbar">
  {assign var=is_finance_superadmin value=0}
  {assign var=finance_unlimited_label value='SUPERADMIN'}
  {if $panel_unlimited_credits_2 == 1}
    {assign var=is_finance_superadmin value=1}
    {if $user_id_2 == 1 && $user_level_2 != 'superadmin'}
      {assign var=finance_unlimited_label value='ROOT'}
    {/if}
  {/if}
  {assign var=programmit_logo value="`$base_url`logo/icon_panel.png"}
  {if isset($panel_logo_url) && $panel_logo_url != ''}
    {assign var=programmit_logo value=$panel_logo_url}
  {/if}
  {if isset($saas_ctx.logo_url) && $saas_ctx.logo_url != ''}
    {assign var=programmit_logo value=$saas_ctx.logo_url}
  {/if}
  <div class="topbar-left">
    <a href="{$base_url}" class="logo">
      <img src="{$programmit_logo}" alt="logo" class="logo-lg" style="height:36px;">
    </a>

    <!-- Visible junto al logo (sin caja) -->
    <button type="button" id="menuBtnVisible" aria-label="Menu">
      <i class="dripicons-menu nav-icon"></i>
    </button>
  </div>

  <nav class="navbar-custom">
    <div class="finance-top-actions">
      {if $is_finance_superadmin == 1}
      <span class="finance-top-balance"><i class="fas fa-shield-alt"></i> {$finance_unlimited_label} · &infin;</span>
      <a class="finance-top-add is-muted" href="{$base_url}index.php?p=finance-history"><i class="fas fa-clock-rotate-left"></i> RECARGAS</a>
      {else}
      <span class="finance-top-balance"><i class="fas fa-sack-dollar"></i> $US {if isset($wallet_balance_2)}{$wallet_balance_2}{else}0{/if}</span>
      <a class="finance-top-add" href="{$base_url}index.php?p=finance-add"><i class="fas fa-plus"></i> AGREGAR SALDO</a>
      {/if}
    </div>

    <ul class="list-unstyled topbar-nav float-right mb-0">
      <li class="dropdown">
        <a class="nav-link dropdown-toggle waves-effect waves-light nav-user"
           data-toggle="dropdown" href="#" role="button">
          {$avatar}
          <span class="ml-1 nav-user-name">{$full_name_2} <i class="mdi mdi-chevron-down"></i></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <div class="dropdown-item nav-user-credits-item"><i class="fas fa-coins text-muted mr-2"></i>
            {if $panel_unlimited_credits_2 == 1}
              <span class="credits-infinity-menu">&infin;</span>
            {else}
              {$credits_bal} Credito(s)
            {/if}
          </div>
          <a class="dropdown-item" href="{$base_url}index.php?p=my-profile"><i class="dripicons-user text-muted mr-2"></i> Perfil</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#panelTopbarLogoutModal">
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

<div class="modal fade" id="panelTopbarLogoutModal" tabindex="-1" role="dialog" aria-labelledby="panelTopbarLogoutLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title2" id="panelTopbarLogoutLabel">¿Listo para salir, {$full_name_2}?</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">Selecciona "Cerrar sesión" si quieres terminar tu sesión actual.</div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
        <a class="btn btn-primary" href="{$base_url}index.php?p=logout&target=panel">Cerrar sesión</a>
      </div>
    </div>
  </div>
</div>

{literal}
<script>
  (function(){
    var key='pm_menu_expanded';
    var v=document.getElementById('menuBtnVisible');
    var h=document.getElementById('menuBtnHidden');
    if(!v||!h) return;

    function saveMenuState(){
      try{
        if(window.innerWidth < 680){
          window.localStorage.setItem(key,'0');
          return;
        }
        window.localStorage.setItem(key, document.body.classList.contains('enlarge-menu') ? '0' : '1');
      }catch(e){}
    }

    v.addEventListener('click',function(e){
      e.preventDefault();
      e.stopPropagation();
      h.click();
      window.setTimeout(saveMenuState, 20);
    });

    window.addEventListener('resize', function(){
      if(window.innerWidth < 680){
        document.body.classList.add('enlarge-menu');
        saveMenuState();
      }
    });
  })();
</script>
{/literal}

{literal}
<script>
  (function(){
    function shieldPageContentClicks(){
      var page = document.querySelector('.page-content');
      if(!page){
        return;
      }

      function stopDesktopSidebarClose(ev){
        if(window.innerWidth < 680){
          return;
        }
        if(document.body.classList.contains('enlarge-menu')){
          return;
        }
        ev.stopPropagation();
      }

      page.addEventListener('mousedown', stopDesktopSidebarClose, false);
      page.addEventListener('touchstart', stopDesktopSidebarClose, false);
    }

    if(document.readyState === 'loading'){
      document.addEventListener('DOMContentLoaded', shieldPageContentClicks);
    } else {
      shieldPageContentClicks();
    }
  })();
</script>
{/literal}

{literal}
<script>
  (function(){
    function removeThemeOutsideClose(){
      if(typeof window.jQuery === 'undefined' || typeof jQuery._data !== 'function'){
        return;
      }

      var events = jQuery._data(document, 'events') || {};
      ['mousedown', 'touchstart'].forEach(function(type){
        var handlers = events[type] || [];
        handlers.slice().forEach(function(entry){
          var fn = entry && entry.handler;
          var src = fn ? String(fn) : '';
          if(
            src.indexOf("closest('.left-sidenav')") !== -1 &&
            src.indexOf(".button-menu-mobile, #menuBtnVisible, #menuBtnHidden") !== -1
          ){
            jQuery(document).off(type, fn);
          }
        });
      });
    }

    window.addEventListener('load', function(){
      removeThemeOutsideClose();
      window.setTimeout(removeThemeOutsideClose, 120);
      window.setTimeout(removeThemeOutsideClose, 400);
      window.setTimeout(removeThemeOutsideClose, 900);
    });
  })();
</script>
{/literal}
