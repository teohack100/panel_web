<style>
:root {
    --pm-tenant-primary: {if isset($saas_ctx.primary_color) && $saas_ctx.primary_color ne ''}{$saas_ctx.primary_color}{else}#2fbde5{/if};
    --pm-tenant-accent: {if isset($saas_ctx.accent_color) && $saas_ctx.accent_color ne ''}{$saas_ctx.accent_color}{else}#95f100{/if};
    --pm-tenant-bg: {if isset($saas_ctx.background_color) && $saas_ctx.background_color ne ''}{$saas_ctx.background_color}{else}#132744{/if};
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

body .modal-backdrop {
    background-color: #0f1d33 !important;
    z-index: 2000 !important;
}

body .modal-backdrop.show {
    opacity: 0.62 !important;
}

body .modal {
    z-index: 2010 !important;
}

body .modal .modal-dialog {
    margin-top: 86px !important;
}

body .modal .modal-content {
    overflow: hidden !important;
    background-clip: padding-box !important;
    border: 1px solid #dbe0ec !important;
    box-shadow: 0 18px 42px rgba(13, 29, 51, 0.22) !important;
}

body .alertify .ajs-dimmer,
body .alertify .ajs-modal {
    z-index: 3000 !important;
}

body .alertify .ajs-dialog {
    z-index: 3010 !important;
}

body .alertify-notifier {
    z-index: 3020 !important;
}

body .swal2-container {
    z-index: 3030 !important;
}

@media (min-width: 1025px) {
    .left-sidenav {
        min-width: 263px !important;
        max-width: 263px !important;
    }

    .topbar .topbar-left {
        width: 263px !important;
    }

    .navbar-custom {
        margin-left: 263px !important;
    }
}

@media (max-width: 767.98px) {
    body .modal .modal-dialog {
        margin-top: 78px !important;
    }
}

</style>
{if isset($panel_favicon_url) && $panel_favicon_url ne ''}
<link rel="icon" type="image/png" href="{$panel_favicon_url}">
<link rel="shortcut icon" type="image/png" href="{$panel_favicon_url}">
{/if}
{if isset($saas_custom_css) && $saas_custom_css ne ''}
<style>{$saas_custom_css nofilter}</style>
{/if}
