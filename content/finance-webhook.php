<?php
chkSession();

$can_manage_finance = (
    (int)$user_id_2 === 1 ||
    $user_level_2 === 'superadmin' ||
    $user_level_2 === 'administrator' ||
    $user_level_2 === 'subadmin'
);

if (!$can_manage_finance) {
    header("Location: ".$db->base_url()."index.php?p=dashboard");
    exit;
}

programmit_finance_ensure_tables($db);
$masterHost = programmit_finance_get_master_host($db);
if (!programmit_finance_can_edit_from_current_host($db)) {
    header("Location: https://" . $masterHost . "/index.php?p=finance-webhook");
    exit;
}

$base = rtrim((string)$db->base_url(), '/');
$callback_api_url = $base . '/api/finance_webhook.php';
$callback_friendly_url = $base . '/webhook/veripagos';
$master_host = programmit_finance_get_master_host($db);
$master_webhook_url = 'https://' . $master_host . '/api/finance_webhook.php';

$rows = array();
$qry = $db->sql_query("SELECT id, recharge_ref, status, provider_txn_id, updated_at, provider_response
    FROM finance_recharges
    WHERE provider_response IS NOT NULL
    AND provider_response<>''
    ORDER BY id DESC
    LIMIT 120");

while ($row = $db->sql_fetchrow($qry)) {
    if (!$row) {
        continue;
    }
    $status = strtolower(trim((string)$row['status']));
    $badge = 'secondary';
    if ($status === 'pending') {
        $badge = 'warning';
    } elseif ($status === 'paid') {
        $badge = 'success';
    } elseif ($status === 'failed' || $status === 'expired' || $status === 'cancelled') {
        $badge = 'danger';
    }

    $raw = (string)$row['provider_response'];
    $raw = trim($raw);
    if (strlen($raw) > 280) {
        $raw = substr($raw, 0, 280) . '...';
    }

    $rows[] = array(
        'id' => (int)$row['id'],
        'recharge_ref' => (string)$row['recharge_ref'],
        'status' => strtoupper((string)$row['status']),
        'status_badge' => $badge,
        'provider_txn_id' => trim((string)$row['provider_txn_id']) !== '' ? (string)$row['provider_txn_id'] : '-',
        'updated_at' => date('Y-m-d H:i:s', strtotime((string)$row['updated_at'])),
        'provider_response' => $raw
    );
}

$smarty->assign('page', 'finance-webhook');
$smarty->assign('finance_callback_api_url', $callback_api_url);
$smarty->assign('finance_callback_friendly_url', $callback_friendly_url);
$smarty->assign('finance_callback_master_url', $master_webhook_url);
$smarty->assign('finance_callback_recommended_url', $callback_api_url);
$smarty->assign('finance_webhook_rows', $rows);
$smarty->display('finance-webhook.tpl');
?>
