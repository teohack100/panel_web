<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '0');

require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json; charset=utf-8');

function pm_finance_webhook_log($row = array()) {
    if (!is_array($row)) {
        $row = array('message' => (string)$row);
    }
    $row['logged_at'] = gmdate('c');

    $logDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0775, true);
    }
    $logPath = $logDir . DIRECTORY_SEPARATOR . 'finance_webhook.log';
    if (!is_dir($logDir)) {
        $logPath = rtrim((string)sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR . 'programmit_finance_webhook.log';
    }

    @file_put_contents(
        $logPath,
        json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
        FILE_APPEND
    );
}

function pm_finance_webhook_response($ok, $message, $extra = array()) {
    $payload = array('ok' => (bool)$ok, 'message' => (string)$message);
    if (is_array($extra)) {
        foreach ($extra as $k => $v) {
            $payload[$k] = $v;
        }
    }
    pm_finance_webhook_log(array(
        'ok' => (bool)$ok,
        'message' => (string)$message,
        'remote_addr' => isset($_SERVER['HTTP_X_FORWARDED_FOR']) && trim((string)$_SERVER['HTTP_X_FORWARDED_FOR']) !== ''
            ? trim((string)$_SERVER['HTTP_X_FORWARDED_FOR'])
            : (isset($_SERVER['REMOTE_ADDR']) ? trim((string)$_SERVER['REMOTE_ADDR']) : ''),
        'request' => isset($GLOBALS['_pm_finance_webhook_request']) && is_array($GLOBALS['_pm_finance_webhook_request'])
            ? $GLOBALS['_pm_finance_webhook_request']
            : array(),
        'raw' => isset($GLOBALS['_pm_finance_webhook_raw']) ? (string)$GLOBALS['_pm_finance_webhook_raw'] : '',
        'response' => $payload
    ));
    echo json_encode($payload);
    exit;
}

function pm_finance_webhook_pick($data, $keys, $default = '') {
    if (!is_array($data) || !is_array($keys)) {
        return $default;
    }
    foreach ($keys as $key) {
        if (array_key_exists($key, $data)) {
            return $data[$key];
        }
    }
    return $default;
}

function pm_finance_webhook_try_json($raw) {
    if (!is_string($raw) || trim($raw) === '') {
        return array();
    }
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : array();
}

function pm_finance_webhook_try_pairs($raw) {
    if (!is_string($raw) || trim($raw) === '' || strpos($raw, '=') === false) {
        return array();
    }
    $parsed = array();
    parse_str($raw, $parsed);
    return is_array($parsed) ? $parsed : array();
}

function pm_finance_webhook_merge_payloads($existingRaw, $incomingRaw)
{
    $existing = pm_finance_webhook_try_json((string)$existingRaw);
    $incoming = pm_finance_webhook_try_json((string)$incomingRaw);

    if (empty($existing) || empty($incoming)) {
        return (string)$incomingRaw;
    }

    $merge = function ($a, $b) use (&$merge) {
        foreach ($b as $key => $value) {
            if (is_array($value) && isset($a[$key]) && is_array($a[$key])) {
                $a[$key] = $merge($a[$key], $value);
            } else {
                $a[$key] = $value;
            }
        }
        return $a;
    };

    return json_encode($merge($existing, $incoming), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function pm_finance_webhook_normalize_status($status) {
    $status = strtolower(trim((string)$status));
    if ($status === '') {
        return '';
    }
    if (is_numeric($status)) {
        $statusId = (int)$status;
        if ($statusId === 2) {
            return 'paid';
        }
        if ($statusId === 3) {
            return 'expired';
        }
        if ($statusId === 4) {
            return 'failed';
        }
        return 'pending';
    }

    $map = array(
        'paid' => 'paid',
        'pagado' => 'paid',
        'completado' => 'paid',
        'completed' => 'paid',
        'approved' => 'paid',
        'aprobado' => 'paid',
        'success' => 'paid',
        'successful' => 'paid',
        'successfull' => 'paid',
        'usado' => 'paid',
        'used' => 'paid',
        'acreditado' => 'paid',
        'procesado' => 'paid',
        'pending' => 'pending',
        'pendiente' => 'pending',
        'created' => 'pending',
        'generado' => 'pending',
        'nuevo' => 'pending',
        'expired' => 'expired',
        'expirado' => 'expired',
        'vencido' => 'expired',
        'caducado' => 'expired',
        'failed' => 'failed',
        'fallido' => 'failed',
        'rechazado' => 'failed',
        'cancelled' => 'cancelled',
        'canceled' => 'cancelled',
        'cancelado' => 'cancelled',
        'anulado' => 'cancelled'
    );

    return isset($map[$status]) ? $map[$status] : $status;
}

programmit_finance_ensure_tables($db);

$rawBody = file_get_contents('php://input');
$json = json_decode((string)$rawBody, true);
if (!is_array($json)) {
    $json = array();
}
if (empty($json)) {
    $json = pm_finance_webhook_try_pairs((string)$rawBody);
}
if (!is_array($json)) {
    $json = array();
}
if (!empty($_POST) && is_array($_POST)) {
    $json = array_merge($json, $_POST);
}

$contexts = array($json);
$nestedKeys = array('additionalData', 'additional_data', 'AdditionalData', 'data', 'Data', 'payload', 'Payload');
foreach ($nestedKeys as $nestedKey) {
    if (!isset($json[$nestedKey])) {
        continue;
    }
    $nestedValue = $json[$nestedKey];
    if (is_string($nestedValue)) {
        $nestedParsed = pm_finance_webhook_try_json($nestedValue);
        if (empty($nestedParsed)) {
            $nestedParsed = pm_finance_webhook_try_pairs($nestedValue);
        }
        if (!empty($nestedParsed)) {
            $contexts[] = $nestedParsed;
        }
    } elseif (is_array($nestedValue)) {
        $contexts[] = $nestedValue;
    }
}

$additionalData = array();
foreach ($contexts as $ctx) {
    if ($ctx === $json) {
        continue;
    }
    if (is_array($ctx)) {
        $additionalData = $ctx;
        break;
    }
}

$GLOBALS['_pm_finance_webhook_raw'] = (string)$rawBody;
$GLOBALS['_pm_finance_webhook_request'] = $json;

$referenceKeys = array('reference', 'recharge_ref', 'Referencia', 'referencia', 'ref', 'Ref');
$reference = '';
foreach ($contexts as $ctx) {
    $reference = trim((string)pm_finance_webhook_pick($ctx, $referenceKeys, ''));
    if ($reference !== '') {
        break;
    }
}
if ($reference === '') {
    $gloss = '';
    foreach ($contexts as $ctx) {
        $gloss = trim((string)pm_finance_webhook_pick($ctx, array('Gloss', 'gloss', 'detalle', 'Detalle', 'detail', 'Detail'), ''));
        if ($gloss !== '') {
            break;
        }
    }
    if (stripos($gloss, 'Recarga ') === 0) {
        $reference = trim(substr($gloss, 8));
    }
}

$status = '';
foreach ($contexts as $ctx) {
    $status = pm_finance_webhook_normalize_status(pm_finance_webhook_pick($ctx, array('status', 'Status', 'estado', 'Estado', 'transactionStatus', 'TransactionStatus'), ''));
    if ($status !== '') {
        break;
    }
}
$statusId = 0;
foreach ($contexts as $ctx) {
    $statusId = (int)pm_finance_webhook_pick($ctx, array('statusId', 'status_id', 'StatusId', 'estadoId', 'EstadoId'), 0);
    if ($statusId > 0) {
        break;
    }
}
if ($status === '' && $statusId > 0) {
    if ($statusId === 2) {
        $status = 'paid';
    } elseif ($statusId === 3) {
        $status = 'expired';
    } elseif ($statusId === 4) {
        $status = 'failed';
    } else {
        $status = 'pending';
    }
}

$providerTxn = '';
$providerTxnKeys = array(
    'transaction_id',
    'provider_txn_id',
    'id',
    'QRId',
    'qrId',
    'movimiento_id',
    'MovimientoId',
    'movimientoId',
    'VP',
    'vp'
);
foreach ($contexts as $ctx) {
    $providerTxn = trim((string)pm_finance_webhook_pick($ctx, $providerTxnKeys, ''));
    if ($providerTxn !== '') {
        break;
    }
}

$voucherId = '';
foreach ($contexts as $ctx) {
    $voucherId = trim((string)pm_finance_webhook_pick($ctx, array('VoucherId', 'voucherId', 'voucher_id', 'Bancarizacion', 'bancarizacion', 'voucher', 'Voucher'), ''));
    if ($voucherId !== '') {
        break;
    }
}
if ($status === '' && $providerTxn !== '' && $voucherId !== '' && $voucherId !== '0') {
    $status = 'paid';
}

$where = '';
if ($reference !== '') {
    $where = "recharge_ref='".$db->SanitizeForSQL($reference)."'";
} elseif ($providerTxn !== '') {
    $where = "provider_txn_id='".$db->SanitizeForSQL($providerTxn)."'";
} else {
    pm_finance_webhook_response(false, 'missing_reference');
}

$qry = $db->sql_query("SELECT id, user_id, method_id, status, provider_response
    FROM finance_recharges
    WHERE ".$where."
    LIMIT 1");
$recharge = $db->sql_fetchrow($qry);
if (!$recharge) {
    pm_finance_webhook_response(false, 'recharge_not_found');
}

$method = programmit_finance_get_method($db, (int)$recharge['method_id']);
if ($method) {
    $settings = isset($method['settings']) && is_array($method['settings']) ? $method['settings'] : array();
    $secret = isset($settings['webhook_secret']) ? trim((string)$settings['webhook_secret']) : '';
    if ($secret !== '') {
        $sentSignature = '';
        if (isset($_SERVER['HTTP_X_SIGNATURE'])) {
            $sentSignature = trim((string)$_SERVER['HTTP_X_SIGNATURE']);
        } elseif (isset($json['signature'])) {
            $sentSignature = trim((string)$json['signature']);
        }
        $localSignature = hash_hmac('sha256', (string)$rawBody, $secret);
        if ($sentSignature === '' || !hash_equals($localSignature, $sentSignature)) {
            pm_finance_webhook_response(false, 'invalid_signature');
        }
    }
}

$paidStates = array('paid', 'success', 'completed', 'approved');
if (!in_array($status, $paidStates, true)) {
    $mergedResponse = pm_finance_webhook_merge_payloads((string)$recharge['provider_response'], (string)$rawBody);
    $db->sql_query("UPDATE finance_recharges
        SET provider_txn_id='".$db->SanitizeForSQL($providerTxn)."',
            provider_response='".$db->SanitizeForSQL($mergedResponse)."',
            updated_at=NOW()
        WHERE id='".$db->SanitizeForSQL((int)$recharge['id'])."'
        LIMIT 1");
    pm_finance_webhook_response(true, 'status_recorded', array('status' => $status));
}

$res = programmit_finance_mark_recharge_paid(
    $db,
    (int)$recharge['id'],
    $providerTxn,
    'Recarga acreditada por webhook',
    0
);

if (empty($res['ok'])) {
    pm_finance_webhook_response(false, isset($res['error']) ? $res['error'] : 'mark_paid_failed');
}

$mergedResponse = pm_finance_webhook_merge_payloads((string)$recharge['provider_response'], (string)$rawBody);
$db->sql_query("UPDATE finance_recharges
    SET provider_response='".$db->SanitizeForSQL($mergedResponse)."',
        updated_at=NOW()
    WHERE id='".$db->SanitizeForSQL((int)$recharge['id'])."'
    LIMIT 1");

pm_finance_webhook_response(true, 'credited', array('recharge_id' => (int)$recharge['id']));
