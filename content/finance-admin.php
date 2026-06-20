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
    header("Location: https://" . $masterHost . "/index.php?p=finance-admin");
    exit;
}

$status_filter = isset($_GET['status']) ? strtolower(trim((string)$_GET['status'])) : 'all';
$allowed_status = array('all', 'pending', 'paid', 'failed', 'expired', 'cancelled');
if (!in_array($status_filter, $allowed_status, true)) {
    $status_filter = 'all';
}

$where = "1=1";
if ($status_filter !== 'all') {
    $where .= " AND r.status='".$db->SanitizeForSQL($status_filter)."'";
}

$stats_qry = $db->sql_query("SELECT
    COUNT(*) AS total,
    SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) AS pending_total,
    SUM(CASE WHEN status='paid' THEN 1 ELSE 0 END) AS paid_total,
    SUM(CASE WHEN status IN ('failed','expired','cancelled') THEN 1 ELSE 0 END) AS failed_total,
    COALESCE(SUM(total_usd),0) AS usd_total,
    COALESCE(SUM(total_bob),0) AS bob_total
    FROM finance_recharges");
$stats_row = $db->sql_fetchrow($stats_qry);

$stats = array(
    'total' => $stats_row ? (int)$stats_row['total'] : 0,
    'pending_total' => $stats_row ? (int)$stats_row['pending_total'] : 0,
    'paid_total' => $stats_row ? (int)$stats_row['paid_total'] : 0,
    'failed_total' => $stats_row ? (int)$stats_row['failed_total'] : 0,
    'usd_total' => $stats_row ? number_format((float)$stats_row['usd_total'], 2) : '0.00',
    'bob_total' => $stats_row ? number_format((float)$stats_row['bob_total'], 2) : '0.00'
);

$rows = array();
$qry = $db->sql_query("SELECT
    r.id, r.recharge_ref, r.user_id, r.method_name, r.total_usd, r.total_bob,
    r.credits_to_add, r.status, r.created_at, r.updated_at, r.provider_txn_id,
    u.user_name
    FROM finance_recharges r
    LEFT JOIN users u ON u.user_id=r.user_id
    WHERE ".$where."
    ORDER BY r.id DESC
    LIMIT 250");

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

    $rows[] = array(
        'id' => (int)$row['id'],
        'recharge_ref' => (string)$row['recharge_ref'],
        'user_id' => (int)$row['user_id'],
        'user_name' => trim((string)$row['user_name']) !== '' ? (string)$row['user_name'] : ('UID '.$row['user_id']),
        'method_name' => (string)$row['method_name'],
        'total_usd' => number_format((float)$row['total_usd'], 2),
        'total_bob' => number_format((float)$row['total_bob'], 2),
        'credits_to_add' => (int)$row['credits_to_add'],
        'status' => strtoupper((string)$row['status']),
        'status_badge' => $badge,
        'provider_txn_id' => (string)$row['provider_txn_id'],
        'created_at' => date('Y-m-d H:i:s', strtotime((string)$row['created_at'])),
        'updated_at' => date('Y-m-d H:i:s', strtotime((string)$row['updated_at']))
    );
}

$smarty->assign('page', 'finance-admin');
$smarty->assign('finance_admin_stats', $stats);
$smarty->assign('finance_admin_rows', $rows);
$smarty->assign('finance_admin_status', $status_filter);
$smarty->display('finance-admin.tpl');
?>
