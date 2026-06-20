{literal}
<style>
  .left-sidenav{
    background:
      radial-gradient(circle at top left, rgba(109, 176, 255, .14) 0%, rgba(109, 176, 255, 0) 26%),
      linear-gradient(180deg, color-mix(in srgb, var(--pm-brand-bg-secondary) 82%, var(--pm-brand-bg-main) 18%) 0%, var(--pm-brand-bg-main) 100%)!important;
    border-right:1px solid color-mix(in srgb, var(--pm-brand-primary) 26%, transparent)!important;
    box-shadow:0 18px 40px rgba(8,20,40,.16)!important;
    overflow:hidden!important;
  }
  .pm-sidebar-shell{
    height:100%;
    display:flex;
    flex-direction:column;
    padding:14px 12px 18px;
    opacity:1;
    transform:translateX(0);
    transition:opacity .16s ease-out, transform .16s ease-out;
  }
  body.enlarge-menu .pm-sidebar-shell{
    opacity:0;
    transform:translateX(-12px);
    pointer-events:none;
  }
  .pm-sidebar-scroll{
    flex:1 1 auto;
    overflow-y:auto;
    overflow-x:hidden;
    padding-right:4px;
    scrollbar-width:thin;
    scrollbar-color:rgba(143,190,255,.34) transparent;
  }
  .pm-sidebar-scroll::-webkit-scrollbar{ width:6px; }
  .pm-sidebar-scroll::-webkit-scrollbar-thumb{
    background:rgba(143,190,255,.28);
    border-radius:4px;
  }
  .pm-sidebar-scroll::-webkit-scrollbar-track{ background:transparent; }

  .pm-nav-category{
    border-bottom:1px solid rgba(138, 183, 240, .12);
    padding:6px 0;
  }
  .pm-nav-category:first-child{
    padding-top:0;
  }
  .pm-nav-toggle{
    width:100%;
    display:flex;
    align-items:center;
    gap:12px;
    padding:10px 10px;
    border:0;
    border-radius:4px;
    background:transparent;
    color:var(--pm-brand-text-primary);
    text-align:left;
    cursor:pointer;
    transition:background .16s ease, box-shadow .16s ease, transform .16s ease;
  }
  .pm-nav-toggle:hover,
  .pm-nav-toggle:focus{
    background:rgba(91, 146, 223, .10);
    box-shadow:inset 0 0 0 1px rgba(134, 183, 252, .10);
    outline:none;
  }
  .pm-nav-category.is-open > .pm-nav-toggle{
    background:linear-gradient(180deg, rgba(88, 147, 228, .16) 0%, rgba(33, 71, 127, .18) 100%);
    box-shadow:inset 0 1px 0 rgba(255,255,255,.04), inset 0 0 0 1px rgba(122, 177, 245, .10);
  }
  .pm-nav-icon{
    flex:0 0 36px;
    width:36px;
    height:36px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    border-radius:3px;
    background:linear-gradient(180deg, rgba(52, 111, 185, .94) 0%, rgba(17, 50, 94, .98) 100%);
    border:1px solid rgba(120, 173, 244, .16);
    box-shadow:inset 0 1px 0 rgba(255,255,255,.04);
  }
  .pm-nav-icon i{
    font-size:18px;
    color:color-mix(in srgb, var(--pm-brand-primary) 72%, white 28%);
  }
  .pm-nav-copy{
    flex:1 1 auto;
    min-width:0;
  }
  .pm-nav-title{
    display:block;
    color:#edf5ff;
    font-family:'Poppins',sans-serif;
    font-size:12px;
    font-weight:700;
    letter-spacing:.04em;
    line-height:1.2;
  }
  .pm-nav-chevron{
    flex:0 0 auto;
    color:color-mix(in srgb, var(--pm-brand-primary) 66%, white 34%);
    font-size:13px;
    transition:transform .14s ease-out, color .14s ease-out;
  }
  .pm-nav-category.is-open > .pm-nav-toggle .pm-nav-chevron{
    transform:rotate(180deg);
    color:#e9f3ff;
  }
  .pm-nav-body{
    display:none;
    padding:6px 0 6px 58px;
  }
  .pm-nav-category.is-open > .pm-nav-body{
    display:block;
  }
  .pm-nav-list{
    list-style:none;
    margin:0;
    padding:0;
  }
  .pm-nav-list li + li{
    margin-top:4px;
  }
  .pm-nav-link,
  .pm-nav-button{
    width:100%;
    display:flex;
    align-items:center;
    gap:10px;
    padding:10px 12px;
    border:0;
    border-radius:4px;
    background:transparent;
    color:color-mix(in srgb, var(--pm-brand-text-secondary) 76%, white 24%);
    text-decoration:none!important;
    font-size:14px;
    font-weight:600;
    line-height:1.25;
    text-align:left;
    transition:background .16s ease, color .16s ease, transform .16s ease, box-shadow .16s ease;
    cursor:pointer;
  }
  .pm-nav-link i,
  .pm-nav-button i{
    flex:0 0 16px;
    width:16px;
    text-align:center;
    color:color-mix(in srgb, var(--pm-brand-primary) 68%, white 32%);
    font-size:15px;
  }
  .pm-nav-link:hover,
  .pm-nav-button:hover,
  .pm-nav-link:focus,
  .pm-nav-button:focus{
    background:rgba(81, 144, 226, .10);
    color:#ffffff;
    outline:none;
  }
  .pm-nav-link.active{
    background:linear-gradient(180deg, rgba(67, 132, 220, .18) 0%, rgba(19, 52, 98, .24) 100%);
    color:#ffffff;
    box-shadow:inset 3px 0 0 var(--pm-brand-primary);
  }
  .pm-nav-link.active i{
    color:#6fd0ff;
  }

  @media (max-width: 820px){
    .pm-nav-title{ font-size:11px; }
    .pm-nav-link,
    .pm-nav-button{ font-size:13px; }
  }
</style>
{/literal}

<div class="left-sidenav">
    <div class="pm-sidebar-shell">
        <div class="pm-sidebar-scroll">
            <div class="pm-nav-category" data-category="main">
                <button class="pm-nav-toggle" type="button">
                    <span class="pm-nav-icon"><i class="fas fa-home"></i></span>
                    <span class="pm-nav-copy"><span class="pm-nav-title">NAVEGACION PRINCIPAL</span></span>
                    <span class="pm-nav-chevron"><i class="fas fa-chevron-down"></i></span>
                </button>
                <div class="pm-nav-body">
                    <ul class="pm-nav-list">
                        <li><a class="pm-nav-link" data-page="dashboard" href="{$base_url}index.php?p=dashboard"><i class="ti-pie-chart"></i>Tablero</a></li>
                        <li><a class="pm-nav-link" data-page="my-profile" href="{$base_url}index.php?p=my-profile"><i class="ti-id-badge"></i>Mi Perfil</a></li>
                    </ul>
                </div>
            </div>

            {if $panel_can_manage_clients_2 == 1 || $panel_restricted_2}
            <div class="pm-nav-category" data-category="clients">
                <button class="pm-nav-toggle" type="button">
                    <span class="pm-nav-icon"><i class="fas fa-user-plus"></i></span>
                    <span class="pm-nav-copy"><span class="pm-nav-title">GESTION DE CLIENTES</span></span>
                    <span class="pm-nav-chevron"><i class="fas fa-chevron-down"></i></span>
                </button>
                <div class="pm-nav-body">
                    <ul class="pm-nav-list">
                        <li><button class="pm-nav-button" type="button" data-pm-action="add-user"><i class="ti-user"></i>Agregar cliente</button></li>
                        <li><a class="pm-nav-link" data-page="bulk-premium" href="{$base_url}index.php?p=bulk-premium"><i class="ti-bolt"></i>Generar prueba</a></li>
                    </ul>
                </div>
            </div>
            {/if}

            {if $panel_can_view_roles_2 == 1 || $panel_restricted_2 }
            <div class="pm-nav-category" data-category="roles">
                <button class="pm-nav-toggle" type="button">
                    <span class="pm-nav-icon"><i class="fas fa-user-shield"></i></span>
                    <span class="pm-nav-copy"><span class="pm-nav-title">GESTION DE ROLES</span></span>
                    <span class="pm-nav-chevron"><i class="fas fa-chevron-down"></i></span>
                </button>
                <div class="pm-nav-body">
                    <ul class="pm-nav-list">
                        {if $panel_can_view_superadministrator_2 == 1 || $panel_restricted_2}
                        <li><a class="pm-nav-link" data-page="super-administrator" href="{$base_url}index.php?p=super-administrator"><i class="ti-crown"></i>Super Administrador</a></li>
                        {/if}
                        {if $panel_can_view_administrator_2 == 1 || $panel_restricted_2}
                        <li><a class="pm-nav-link" data-page="administrator" href="{$base_url}index.php?p=administrator"><i class="ti-user"></i>Administrador</a></li>
                        {/if}
                        {if $panel_can_view_subadministrator_2 == 1 || $panel_restricted_2}
                        <li><a class="pm-nav-link" data-page="sub-administrator" href="{$base_url}index.php?p=sub-administrator"><i class="ti-user"></i>Sub Administrador</a></li>
                        {/if}
                        {if $panel_can_view_reseller_2 == 1 || $panel_restricted_2}
                        <li><a class="pm-nav-link" data-page="reseller" href="{$base_url}index.php?p=reseller"><i class="ti-user"></i>Reseller</a></li>
                        {/if}
                        {if $panel_can_view_subreseller_2 == 1 || $panel_restricted_2}
                        <li><a class="pm-nav-link" data-page="sub-reseller" href="{$base_url}index.php?p=sub-reseller"><i class="ti-user"></i>Sub Reseller</a></li>
                        {/if}
                    </ul>
                </div>
            </div>
            {/if}

            {if $panel_can_manage_clients_2 == 1 || $panel_restricted_2}
            <div class="pm-nav-category" data-category="monitoring">
                <button class="pm-nav-toggle" type="button">
                    <span class="pm-nav-icon"><i class="fas fa-binoculars"></i></span>
                    <span class="pm-nav-copy"><span class="pm-nav-title">MONITOREO DE CLIENTES</span></span>
                    <span class="pm-nav-chevron"><i class="fas fa-chevron-down"></i></span>
                </button>
                <div class="pm-nav-body">
                    <ul class="pm-nav-list">
                        <li><a class="pm-nav-link" data-page="active-premium" href="{$base_url}index.php?p=active-premium"><i class="ti-user"></i>Clientes activos</a></li>
                        <li><a class="pm-nav-link" data-page="inactive-premium" href="{$base_url}index.php?p=inactive-premium"><i class="ti-user"></i>Clientes inactivos</a></li>
                        <li><a class="pm-nav-link" data-page="freezed-client" href="{$base_url}index.php?p=freezed-client"><i class="ti-lock"></i>Clientes congelados</a></li>
                        <li><a class="pm-nav-link" data-page="deleted-client" href="{$base_url}index.php?p=deleted-client"><i class="ti-na"></i>Clientes eliminados</a></li>
                    </ul>
                </div>
            </div>
            {/if}

            <div class="pm-nav-category" data-category="finance">
                <button class="pm-nav-toggle" type="button">
                    <span class="pm-nav-icon"><i class="fas fa-wallet"></i></span>
                    <span class="pm-nav-copy"><span class="pm-nav-title">FINANZAS</span></span>
                    <span class="pm-nav-chevron"><i class="fas fa-chevron-down"></i></span>
                </button>
                <div class="pm-nav-body">
                    <ul class="pm-nav-list">
                        <li><a class="pm-nav-link" data-page="finance-add" href="{$base_url}index.php?p=finance-add"><i class="ti-wallet"></i>Agregar saldo</a></li>
                        <li><a class="pm-nav-link" data-page="finance-history" href="{$base_url}index.php?p=finance-history"><i class="ti-back-left"></i>Historial de recargas</a></li>
                        {if $panel_can_manage_finance_admin_2 == 1 || $panel_restricted_2}
                        <li><a class="pm-nav-link" data-page="finance-admin" href="{$base_url}index.php?p=finance-admin"><i class="ti-money"></i>Retiros</a></li>
                        {/if}
                    </ul>
                </div>
            </div>

            {if $panel_can_manage_server_2 == 1 || $panel_restricted_2}
            <div class="pm-nav-category" data-category="server">
                <button class="pm-nav-toggle" type="button">
                    <span class="pm-nav-icon"><i class="fas fa-server"></i></span>
                    <span class="pm-nav-copy"><span class="pm-nav-title">GESTION DE SERVIDOR</span></span>
                    <span class="pm-nav-chevron"><i class="fas fa-chevron-down"></i></span>
                </button>
                <div class="pm-nav-body">
                    <ul class="pm-nav-list">
                        <li><a class="pm-nav-link" data-page="server-status" href="{$base_url}index.php?p=server-status"><i class="ti-server"></i>El estado del servidor</a></li>
                        <li><a class="pm-nav-link" data-page="vpn-control" href="{$base_url}index.php?p=vpn-control"><i class="ti-layout-grid2"></i>VPN Multi-VPS</a></li>
                        {if $panel_can_manage_server_sessions_2 == 1 || $panel_restricted_2}
                        <li><a class="pm-nav-link" data-page="online-users-tcp" href="{$base_url}index.php?p=online-users-tcp"><i class="ti-layers-alt"></i>Usuarios en línea (TCP)</a></li>
                        <li><a class="pm-nav-link" data-page="online-users-udp" href="{$base_url}index.php?p=online-users-udp"><i class="ti-layers-alt"></i>Usuarios en línea (UDP)</a></li>
                        {/if}
                    </ul>
                </div>
            </div>
            {/if}

            {if $panel_can_view_history_2 == 1 || $panel_restricted_2}
            <div class="pm-nav-category" data-category="history">
                <button class="pm-nav-toggle" type="button">
                    <span class="pm-nav-icon"><i class="fas fa-history"></i></span>
                    <span class="pm-nav-copy"><span class="pm-nav-title">HISTORIAL</span></span>
                    <span class="pm-nav-chevron"><i class="fas fa-chevron-down"></i></span>
                </button>
                <div class="pm-nav-body">
                    <ul class="pm-nav-list">
                        <li><a class="pm-nav-link" data-page="credit-logs" href="{$base_url}index.php?p=credit-logs"><i class="ti-book"></i>Registros de crédito</a></li>
                        <li><a class="pm-nav-link" data-page="voucher-logs" href="{$base_url}index.php?p=voucher-logs"><i class="ti-agenda"></i>Registros de cupones</a></li>
                    </ul>
                </div>
            </div>
            {/if}

            {if $panel_can_manage_panels_2 == 1 || $panel_restricted_2}
            <div class="pm-nav-category" data-category="panels">
                <button class="pm-nav-toggle" type="button">
                    <span class="pm-nav-icon"><i class="fas fa-cogs"></i></span>
                    <span class="pm-nav-copy"><span class="pm-nav-title">GESTION DE PANELES</span></span>
                    <span class="pm-nav-chevron"><i class="fas fa-chevron-down"></i></span>
                </button>
                <div class="pm-nav-body">
                    <ul class="pm-nav-list">
                        <li><a class="pm-nav-link" data-page="server-update" href="{$base_url}index.php?p=server-update"><i class="ti-harddrives"></i>Actualización del servidor</a></li>
                        <li><a class="pm-nav-link" data-page="notice-update" href="{$base_url}index.php?p=notice-update"><i class="ti-pencil-alt"></i>Actualización de aviso</a></li>
                        <li><a class="pm-nav-link" data-page="updater-v1" href="{$base_url}index.php?p=updater-v1"><i class="ti-marker-alt"></i>Updater #1</a></li>
                        <li><a class="pm-nav-link" data-page="updater-v2" href="{$base_url}index.php?p=updater-v2"><i class="ti-marker-alt"></i>Updater #2</a></li>
                        <li><a class="pm-nav-link" data-page="updater-v3" href="{$base_url}index.php?p=updater-v3"><i class="ti-marker-alt"></i>Updater #3</a></li>
                        <li><a class="pm-nav-link" data-page="updater-v4" href="{$base_url}index.php?p=updater-v4"><i class="ti-marker-alt"></i>Updater #4</a></li>
                    </ul>
                </div>
            </div>
            {/if}
        </div>
    </div>
</div>

{if $panel_restricted_2}
<script>window.PM_LOCK_URL = '{$base_url}index.php?p=access-lock';</script>
{/if}

{literal}
<script>
(function(){
    function currentPage(){
        try{
            return new URL(window.location.href).searchParams.get('p') || 'dashboard';
        }catch(e){
            var m = String(window.location.href).match(/[?&]p=([^&#]+)/i);
            return m ? decodeURIComponent(m[1]) : 'dashboard';
        }
    }

    function lockRestrictedItems(){
        if(!window.PM_LOCK_URL){
            return;
        }
        var allowPages = {
            dashboard:true,
            'my-profile':true,
            'finance-add':true,
            'finance-history':true,
            'finance-checkout':true,
            'finance-methods':true,
            'finance-admin':true,
            'finance-webhook':true,
            logout:true,
            'access-lock':true
        };

        document.querySelectorAll('.left-sidenav .pm-nav-link, .left-sidenav .pm-nav-button').forEach(function(node){
            var href = (node.getAttribute('href') || '').trim();
            var page = node.getAttribute('data-page') || '';
            var isButton = node.classList.contains('pm-nav-button');
            if(isButton || href.toLowerCase().indexOf('javascript:') === 0){
                node.addEventListener('click', function(ev){
                    ev.preventDefault();
                    window.location.href = window.PM_LOCK_URL;
                });
                return;
            }
            if(page && !allowPages[page]){
                node.setAttribute('href', window.PM_LOCK_URL);
            }
        });
    }

    function openAddUserModal(){
        var modal = document.getElementById('modal_form');
        if(!modal){
            return;
        }
        var form = modal.querySelector('#register');
        if(form){
            form.reset();
        }
        var title = modal.querySelector('.modal-title');
        if(title){
            title.textContent = 'Agregar cliente';
        }
        var submitText = modal.querySelector('#butext');
        if(submitText){
            submitText.textContent = 'Crear cliente';
        }
        var roleAcct = modal.querySelector('#role_acct');
        if(roleAcct && roleAcct.options.length){
            roleAcct.selectedIndex = 0;
        }
        var role = modal.querySelector('#role');
        if(role && role.options.length){
            role.selectedIndex = 0;
        }
        var clientType = modal.querySelector('#client_type');
        if(clientType && clientType.options.length){
            clientType.selectedIndex = 0;
        }
        if(typeof window.v2rayrefresh === 'function'){
            window.v2rayrefresh();
        }
        if(typeof window.jQuery !== 'undefined' && typeof window.jQuery(modal).modal === 'function'){
            window.jQuery(modal).modal('show');
        }
    }

    function closeAll(except){
        document.querySelectorAll('.pm-nav-category').forEach(function(cat){
            if(cat !== except){
                cat.classList.remove('is-open');
            }
        });
    }

    function categoryForPage(page){
        var map = {
            dashboard:'main',
            'my-profile':'main',
            'bulk-premium':'clients',
            'super-administrator':'roles',
            administrator:'roles',
            'sub-administrator':'roles',
            reseller:'roles',
            'sub-reseller':'roles',
            'active-premium':'monitoring',
            'inactive-premium':'monitoring',
            'freezed-client':'monitoring',
            'deleted-client':'monitoring',
            'finance-add':'finance',
            'finance-history':'finance',
            'finance-admin':'finance',
            'finance-checkout':'finance',
            'finance-methods':'finance',
            'finance-webhook':'finance',
            'server-status':'server',
            'vpn-control':'server',
            'online-users-tcp':'server',
            'online-users-udp':'server',
            'credit-logs':'history',
            'voucher-logs':'history',
            'server-update':'panels',
            'notice-update':'panels',
            'updater-v1':'panels',
            'updater-v2':'panels',
            'updater-v3':'panels',
            'updater-v4':'panels'
        };
        return map[page] || 'main';
    }

    function syncMenuState(){
        var page = currentPage();
        var activeLink = document.querySelector('.pm-nav-link[data-page="' + page + '"]');
        if(activeLink){
            activeLink.classList.add('active');
        }
        var targetCategory = categoryForPage(page);
        var category = document.querySelector('.pm-nav-category[data-category="' + targetCategory + '"]');
        if(!category){
            category = document.querySelector('.pm-nav-category');
        }
        if(category){
            category.classList.add('is-open');
        }
    }

    function bindCategoryToggles(){
        document.querySelectorAll('.pm-nav-toggle').forEach(function(toggle){
            toggle.addEventListener('click', function(ev){
                ev.preventDefault();
                var category = toggle.closest('.pm-nav-category');
                if(!category){
                    return;
                }
                var isOpen = category.classList.contains('is-open');
                if(isOpen){
                    category.classList.remove('is-open');
                    return;
                }
                closeAll(category);
                category.classList.add('is-open');
            });
        });
    }

    function bindActions(){
        document.querySelectorAll('[data-pm-action="add-user"]').forEach(function(btn){
            btn.addEventListener('click', function(ev){
                if(window.PM_LOCK_URL){
                    return;
                }
                ev.preventDefault();
                openAddUserModal();
            });
        });
    }

    function init(){
        bindCategoryToggles();
        bindActions();
        syncMenuState();
        lockRestrictedItems();
    }

    if(document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', init);
    }else{
        init();
    }
})();
</script>
{/literal}
