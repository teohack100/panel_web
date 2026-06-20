<?php
chkSession();

if (!function_exists('programmit_finance_checkout_status_meta')) {
    function programmit_finance_checkout_status_meta($status) {
        $status = strtolower(trim((string)$status));
        $badge = 'secondary';
        $label = 'EN REVISION';

        if ($status === 'pending') {
            $badge = 'warning';
            $label = 'PENDIENTE';
        } elseif ($status === 'paid') {
            $badge = 'success';
            $label = 'PAGO COMPLETADO';
        } elseif ($status === 'failed') {
            $badge = 'danger';
            $label = 'PAGO FALLIDO';
        } elseif ($status === 'expired') {
            $badge = 'danger';
            $label = 'EXPIRADO';
        } elseif ($status === 'cancelled') {
            $badge = 'secondary';
            $label = 'CANCELADO';
        }

        return array(
            'code' => strtoupper($status),
            'label' => $label,
            'badge' => $badge
        );
    }
}

if (!function_exists('programmit_finance_checkout_countdown_text')) {
    function programmit_finance_checkout_countdown_text($expires_at_unix) {
        $expires_at_unix = (int)$expires_at_unix;
        if ($expires_at_unix <= 0) {
            return '00:00:00';
        }
        $remaining = $expires_at_unix - time();
        if ($remaining <= 0) {
            return '00:00:00';
        }
        $hours = floor($remaining / 3600);
        $minutes = floor(($remaining % 3600) / 60);
        $seconds = $remaining % 60;
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}

programmit_finance_ensure_tables($db);

$recharge_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($recharge_id <= 0) {
    header("Location: ".$db->base_url()."index.php?p=finance-history");
    exit;
}

$finance_notice = '';
$notice_get = isset($_GET['notice']) ? trim((string)$_GET['notice']) : '';
if ($notice_get !== '') {
    $finance_notice = strip_tags($notice_get);
}
$is_superadmin = ((int)$user_id_2 === 1 || $user_level_2 === 'superadmin') ? 1 : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_paid']) && $is_superadmin === 1) {
    $paid = programmit_finance_mark_recharge_paid($db, $recharge_id, 'MANUAL_ADMIN', 'Recarga aprobada manualmente', (int)$user_id_2);
    if (!empty($paid['ok'])) {
        $finance_notice = 'Recarga acreditada correctamente.';
    } else {
        $finance_notice = isset($paid['error']) ? (string)$paid['error'] : 'No se pudo acreditar la recarga.';
    }
}

$recharge = programmit_finance_get_recharge($db, $recharge_id, (int)$user_id_2);
if (!$recharge) {
    header("Location: ".$db->base_url()."index.php?p=finance-history");
    exit;
}

$status = strtolower(trim((string)$recharge['status']));
if ($status === 'pending' && !empty($recharge['expires_at']) && $recharge['expires_at'] !== '0000-00-00 00:00:00') {
    if (strtotime((string)$recharge['expires_at']) < time()) {
        $db->sql_query("UPDATE finance_recharges
            SET status='expired', updated_at=NOW()
            WHERE id='".$db->SanitizeForSQL($recharge_id)."'
            AND status='pending'
            LIMIT 1");
        $recharge = programmit_finance_get_recharge($db, $recharge_id, (int)$user_id_2);
    }
}

$status = strtolower(trim((string)$recharge['status']));
$status_meta = programmit_finance_checkout_status_meta($status);
$method_key = strtolower(trim((string)($recharge['method_key'] ?? '')));
$method_name = trim((string)($recharge['method_name'] ?? ''));
$expires_at_unix = (!empty($recharge['expires_at']) && $recharge['expires_at'] !== '0000-00-00 00:00:00')
    ? (int)strtotime((string)$recharge['expires_at'])
    : 0;

$qr_image_url = trim((string)$recharge['qr_image_url']);
$qr_payload = trim((string)$recharge['qr_payload']);

if ($qr_image_url !== '') {
    $normalized = programmit_finance_normalize_qr_image($qr_image_url);
    if ($normalized !== '') {
        $qr_image_url = $normalized;
    }
}
if ($qr_image_url === '' && $qr_payload !== '') {
    $fromPayload = programmit_finance_normalize_qr_image($qr_payload);
    if ($fromPayload !== '') {
        $qr_image_url = $fromPayload;
    }
}

$is_qr_checkout = in_array($method_key, array('qr_bolivia_auto', 'veripagos'), true) || $qr_image_url !== '';
$is_manual_checkout = ($method_key === 'manual_transfer');
$hero_title = $is_manual_checkout ? 'Transferencia manual' : 'Pago con QR Bolivia';
$hero_subtitle = $is_manual_checkout
    ? 'Realiza la transferencia y envía el comprobante para acreditar tu saldo.'
    : 'Escanea el QR, confirma el pago y el panel acreditará tu saldo automáticamente.';
$pending_label = $is_manual_checkout ? 'Pendiente de validación' : 'Esperando pago';
$pending_note = $is_manual_checkout
    ? 'Envía el comprobante y tu saldo se acreditará una vez validado el pago.'
    : 'Paga antes de que expire y el saldo se acreditará automáticamente.';
$expired_title = $is_manual_checkout ? 'Solicitud expirada' : 'QR expirado';
$show_pending_countdown = ($status_meta['code'] === 'PENDING' && $is_qr_checkout);
$show_manual_instructions = ($status_meta['code'] === 'PENDING' && !$is_qr_checkout);

$recharge_view = array(
    'id' => (int)$recharge['id'],
    'recharge_ref' => (string)$recharge['recharge_ref'],
    'method_key' => $method_key,
    'method_name' => $method_name,
    'amount_usd' => number_format((float)$recharge['amount_usd'], 2),
    'fee_usd' => number_format((float)$recharge['fee_usd'], 2),
    'total_usd' => number_format((float)$recharge['total_usd'], 2),
    'total_bob' => number_format((float)$recharge['total_bob'], 2),
    'credits_to_add' => (int)$recharge['credits_to_add'],
    'status' => $status_meta['label'],
    'status_code' => $status_meta['code'],
    'status_badge' => $status_meta['badge'],
    'created_at' => date('Y-m-d H:i:s', strtotime((string)$recharge['created_at'])),
    'expires_at' => (!empty($recharge['expires_at']) && $recharge['expires_at'] !== '0000-00-00 00:00:00') ? date('Y-m-d H:i:s', strtotime((string)$recharge['expires_at'])) : '-',
    'expires_at_unix' => $expires_at_unix,
    'countdown_text' => programmit_finance_checkout_countdown_text($expires_at_unix),
    'paid_at' => (!empty($recharge['paid_at']) && $recharge['paid_at'] !== '0000-00-00 00:00:00') ? date('Y-m-d H:i:s', strtotime((string)$recharge['paid_at'])) : '-',
    'qr_image_url' => $qr_image_url,
    'qr_payload' => $qr_payload,
    'provider_response' => (string)$recharge['provider_response'],
    'is_qr_checkout' => $is_qr_checkout ? 1 : 0,
    'is_manual_checkout' => $is_manual_checkout ? 1 : 0,
    'show_pending_countdown' => $show_pending_countdown ? 1 : 0,
    'show_manual_instructions' => $show_manual_instructions ? 1 : 0,
    'hero_title' => $hero_title,
    'hero_subtitle' => $hero_subtitle,
    'pending_label' => $pending_label,
    'pending_note' => $pending_note,
    'expired_title' => $expired_title
);

if (isset($_GET['ajax']) && (string)$_GET['ajax'] === '1') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
        'ok' => true,
        'id' => $recharge_view['id'],
        'status' => $recharge_view['status'],
        'status_code' => $recharge_view['status_code'],
        'status_badge' => $recharge_view['status_badge'],
        'expires_at_unix' => $recharge_view['expires_at_unix'],
        'countdown_text' => $recharge_view['countdown_text'],
        'paid_at' => $recharge_view['paid_at'],
        'credits_to_add' => $recharge_view['credits_to_add']
    ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$smarty->assign('finance_notice', $finance_notice);
$smarty->assign('finance_is_superadmin', $is_superadmin);
$smarty->assign('finance_recharge', $recharge_view);
$smarty->assign('page', 'finance-checkout');
$smarty->display('finance-checkout.tpl');
?>
