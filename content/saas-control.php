<?php
chkSession();

if (!programmit_saas_is_platform_admin($user_id_2, $user_level_2)) {
    header("Location: ".$db->base_url()."index.php?p=dashboard");
    exit;
}

programmit_saas_ensure_tables($db);
$controlHost = programmit_saas_get_control_host($db);
if (!programmit_saas_can_manage_from_current_host($db)) {
    header("Location: https://" . $controlHost . "/index.php?p=saas-control");
    exit;
}

$saas_control_error = '';
$saas_control_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_saas_control'])) {
    $newControlHost = programmit_saas_normalize_host(isset($_POST['saas_control_host']) ? $_POST['saas_control_host'] : '');
    $defaultPanelHost = programmit_saas_normalize_host(isset($_POST['saas_default_panel_host']) ? $_POST['saas_default_panel_host'] : '');
    $autoSync = isset($_POST['saas_auto_sync_enabled']) ? '1' : '0';
    $allowLocal = isset($_POST['saas_allow_local_control']) ? '1' : '0';

    if (!programmit_saas_valid_hostname($newControlHost)) {
        $saas_control_error = 'Dominio control invalido.';
    } elseif (!programmit_saas_valid_hostname($defaultPanelHost)) {
        $saas_control_error = 'Dominio panel por defecto invalido.';
    } else {
        programmit_saas_set_setting($db, 'saas_control_host', $newControlHost);
        programmit_saas_set_setting($db, 'saas_default_panel_host', $defaultPanelHost);
        programmit_saas_set_setting($db, 'saas_auto_sync_enabled', $autoSync);
        programmit_saas_set_setting($db, 'saas_allow_local_control', $allowLocal);
        if (function_exists('programmit_finance_set_setting')) {
            programmit_finance_set_setting($db, 'finance_master_host', $newControlHost);
        }
        $saas_control_success = 'Configuracion central guardada.';
        $controlHost = $newControlHost;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_saas_sync'])) {
    $summary = programmit_saas_sync_runtime($db, 'manual');
    $saas_control_success = 'Sincronizacion manual ejecutada. Usuarios: '.(int)$summary['users_tagged'].' | Recargas: '.(int)$summary['recharges_tagged'];
}

$currentSettings = array(
    'saas_control_host' => programmit_saas_get_control_host($db),
    'saas_default_panel_host' => programmit_saas_get_setting($db, 'saas_default_panel_host', 'panel.programmit.com'),
    'saas_auto_sync_enabled' => (int)programmit_saas_get_setting($db, 'saas_auto_sync_enabled', '1'),
    'saas_allow_local_control' => (int)programmit_saas_get_setting($db, 'saas_allow_local_control', '1'),
    'saas_last_sync_at' => programmit_saas_get_setting($db, 'saas_last_sync_at', '')
);

$syncLogs = programmit_saas_list_sync_logs($db, 80);

$smarty->assign('page', 'saas-control');
$smarty->assign('saas_control_error', $saas_control_error);
$smarty->assign('saas_control_success', $saas_control_success);
$smarty->assign('saas_control_settings', $currentSettings);
$smarty->assign('saas_sync_logs', $syncLogs);
$smarty->display('saas-control.tpl');
?>
