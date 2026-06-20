<?php
define('DOC_ROOT_PATH', __DIR__ . '/');
require __DIR__ . '/includes/functions.php';

chkSession();

$isAdminUser = (
    (int)$user_id_2 === 1 ||
    $user_level_2 === 'superadmin' ||
    $user_level_2 === 'administrator' ||
    $user_level_2 === 'subadmin'
);

if (!$isAdminUser) {
    header("Location: " . $db->base_url() . "index.php?p=dashboard");
    exit;
}

programmit_finance_ensure_tables($db);
programmit_saas_ensure_tables($db);
$masterHost = programmit_saas_get_control_host($db);
if (!programmit_saas_can_manage_from_current_host($db)) {
    header("Location: https://" . $masterHost . "/admin.php");
    exit;
}

$currentHost = programmit_saas_current_host();
$isMasterHost = programmit_saas_can_manage_from_current_host($db) ? 1 : 0;
$masterAdminUrl = 'https://' . $masterHost . '/admin.php';
$can_manage_saas = programmit_saas_is_platform_admin($user_id_2, $user_level_2);

$adminSections = array(
    array(
        'title' => 'Configuracion general',
        'desc' => 'Define tasa USD/BOB, dominio admin principal y reglas globales.',
        'url' => $db->base_url() . 'index.php?p=finance-methods&tab=general',
        'icon' => 'ti-settings'
    ),
    array(
        'title' => 'API Providers',
        'desc' => 'Vista de proveedores y estado de integraciones disponibles.',
        'url' => $db->base_url() . 'index.php?p=finance-methods&tab=providers',
        'icon' => 'ti-plug'
    ),
    array(
        'title' => 'Metodos de pago',
        'desc' => 'Configura API, llaves, tasas, limites y activacion por metodo.',
        'url' => $db->base_url() . 'index.php?p=finance-methods&tab=methods',
        'icon' => 'ti-wallet'
    ),
    array(
        'title' => 'Monitor de recargas',
        'desc' => 'Revisa recargas globales, estados y acceso rapido a checkout.',
        'url' => $db->base_url() . 'index.php?p=finance-admin',
        'icon' => 'ti-reload'
    ),
    array(
        'title' => 'Webhook y callbacks',
        'desc' => 'URL oficial del webhook y ultimos eventos recibidos.',
        'url' => $db->base_url() . 'index.php?p=finance-webhook',
        'icon' => 'ti-link'
    ),
    array(
        'title' => 'SaaS Control Plane',
        'desc' => 'Define host de control central, auto-sync y estado de sincronizacion.',
        'url' => $db->base_url() . 'index.php?p=saas-control',
        'icon' => 'ti-shield'
    ),
    array(
        'title' => 'SaaS Planes',
        'desc' => 'Define planes de alquiler, limites y precio por credito.',
        'url' => $db->base_url() . 'index.php?p=saas-plans',
        'icon' => 'ti-medall-alt'
    ),
    array(
        'title' => 'SaaS Tenants',
        'desc' => 'Gestiona tenants, dominios white-label y branding.',
        'url' => $db->base_url() . 'index.php?p=saas-tenants',
        'icon' => 'ti-world'
    ),
    array(
        'title' => 'Actualización servidor',
        'desc' => 'Publica scripts y actualizaciones de instalación para VPS.',
        'url' => $db->base_url() . 'index.php?p=server-update',
        'icon' => 'ti-harddrives'
    ),
    array(
        'title' => 'Avisos globales',
        'desc' => 'Configura mensajes y avisos que se muestran en paneles.',
        'url' => $db->base_url() . 'index.php?p=notice-update',
        'icon' => 'ti-pencil-alt'
    )
);

if (!$can_manage_saas) {
    $adminSections = array_values(array_filter($adminSections, function($item) {
        $url = isset($item['url']) ? (string)$item['url'] : '';
        return (strpos($url, 'saas-') === false);
    }));
}

$smarty->assign('page', 'admin-hub');
$smarty->assign('admin_hub_current_host', $currentHost);
$smarty->assign('admin_hub_master_host', $masterHost);
$smarty->assign('admin_hub_is_master_host', $isMasterHost);
$smarty->assign('admin_hub_master_url', $masterAdminUrl);
$smarty->assign('admin_hub_can_manage_saas', $can_manage_saas ? 1 : 0);
$smarty->assign('admin_hub_sections', $adminSections);
$smarty->display('admin-hub.tpl');
