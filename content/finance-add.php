<?php
chkSession();

programmit_finance_ensure_tables($db);

$finance_error = '';
$finance_success = '';
$finance_is_superadmin = ((int)$user_id_2 === 1 || $user_level_2 === 'superadmin') ? 1 : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finance_submit'])) {
    $amountUsd = isset($_POST['amount_usd']) ? (float)$_POST['amount_usd'] : 0;
    $methodId = isset($_POST['method_id']) ? (int)$_POST['method_id'] : 0;
    $acceptTerms = isset($_POST['accept_terms']) ? 1 : 0;

    if ($acceptTerms !== 1) {
        $finance_error = 'Debes aceptar los terminos y condiciones para continuar.';
    } elseif ($amountUsd <= 0) {
        $finance_error = 'Monto invalido.';
    } elseif ($methodId <= 0) {
        $finance_error = 'Selecciona un metodo de pago.';
    } else {
        $created = programmit_finance_create_recharge($db, (int)$user_id_2, $methodId, $amountUsd);
        if (!empty($created['ok'])) {
            header("Location: ".$db->base_url()."index.php?p=finance-checkout&id=".$created['id']);
            exit;
        } else {
            $finance_error = isset($created['error']) ? (string)$created['error'] : 'No se pudo generar la recarga.';
            if (!empty($created['id'])) {
                $redirect = $db->base_url()."index.php?p=finance-checkout&id=".$created['id'];
                if ($finance_error !== '') {
                    $redirect .= "&notice=".rawurlencode($finance_error);
                }
                header("Location: ".$redirect);
                exit;
            }
        }
    }
}

$finance_methods = programmit_finance_list_methods($db, true);
$global_credit_price = programmit_finance_get_credit_price_usd($db);
$methods_render = array();
foreach ($finance_methods as $m) {
    $method_credit_price = programmit_finance_effective_credit_price($db, $m);
    $icon_url = '';
    if (isset($m['settings']) && is_array($m['settings']) && isset($m['settings']['icon_url'])) {
        $icon_url = trim((string)$m['settings']['icon_url']);
    }
    if ($icon_url !== '' && !preg_match('#^(?:https?:)?//#i', $icon_url) && stripos($icon_url, 'data:') !== 0) {
        $icon_url = rtrim((string)$db->base_url(), '/') . '/' . ltrim($icon_url, '/');
    }

    $methods_render[] = array(
        'id' => (int)$m['id'],
        'method_key' => isset($m['method_key']) ? (string)$m['method_key'] : '',
        'method_name' => (string)$m['method_name'],
        'provider_key' => (string)$m['provider_key'],
        'icon_url' => $icon_url,
        'min_amount' => (float)$m['min_amount'],
        'max_amount' => (float)$m['max_amount'],
        'fee_fixed' => (float)$m['fee_fixed'],
        'fee_percent' => (float)$m['fee_percent'],
        'rate_bob' => (float)$m['rate_bob'],
        'credit_price_usd' => (float)$method_credit_price,
        'is_active' => (int)$m['is_active']
    );
}

$pending_qry = $db->sql_query("SELECT COUNT(*) AS total
    FROM finance_recharges
    WHERE user_id='".$db->SanitizeForSQL((int)$user_id_2)."'
    AND status='pending'");
$pending_row = $db->sql_fetchrow($pending_qry);
$pending_count = $pending_row ? (int)$pending_row['total'] : 0;

$recent_amounts = array();
$recent_qry = $db->sql_query("SELECT amount_usd, COUNT(*) AS used_count, MAX(COALESCE(paid_at, created_at)) AS last_used
    FROM finance_recharges
    WHERE user_id='".$db->SanitizeForSQL((int)$user_id_2)."'
    AND status='paid'
    GROUP BY amount_usd
    ORDER BY COUNT(*) DESC, MAX(COALESCE(paid_at, created_at)) DESC
    LIMIT 3");
while ($recent_row = $db->sql_fetchrow($recent_qry)) {
    $recent_amount = isset($recent_row['amount_usd']) ? (float)$recent_row['amount_usd'] : 0;
    if ($recent_amount > 0) {
        $recent_amounts[] = number_format($recent_amount, 2, '.', '');
    }
}

$smarty->assign('finance_error', $finance_error);
$smarty->assign('finance_success', $finance_success);
$smarty->assign('finance_is_superadmin', $finance_is_superadmin);
$smarty->assign('finance_methods', $methods_render);
$smarty->assign('finance_pending_count', $pending_count);
$smarty->assign('finance_amount_suggestions', $recent_amounts);
$smarty->assign('finance_credit_price_usd', number_format((float)$global_credit_price, 4, '.', ''));
$smarty->assign('page', 'finance-add');
$smarty->display('finance-add.tpl');
?>
