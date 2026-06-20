<?php
require_once __DIR__ . '/vpn_api_common.php';

programmit_vpn_ensure_tables($db);

$data = pm_vpn_api_input();
$providedKey = trim((string)pm_vpn_api_request_value($data, array('key', 'app_key'), ''));
if ($providedKey === '') {
    $providedKey = pm_vpn_api_header('X-App-Key');
}

$requiredKey = trim((string)programmit_vpn_get_setting($db, 'vpn_public_app_endpoint_key', 'vpn-app-config'));
if ($requiredKey !== '' && !hash_equals($requiredKey, $providedKey)) {
    pm_vpn_api_fail(403, 'invalid_endpoint_key');
}

$catalog = programmit_vpn_public_catalog($db);
$methodFilter = programmit_vpn_normalize_key((string)pm_vpn_api_request_value($data, array('method', 'method_key'), ''));
if ($methodFilter !== '') {
    $filtered = array();
    foreach ((array)$catalog['methods'] as $methodRow) {
        $rowKey = programmit_vpn_normalize_key(isset($methodRow['method_key']) ? $methodRow['method_key'] : '');
        if ($rowKey === $methodFilter) {
            $filtered[] = $methodRow;
        }
    }
    $catalog['methods'] = $filtered;
}

$payload = array(
    'ok' => true,
    'generated_at' => isset($catalog['generated_at']) ? $catalog['generated_at'] : date('Y-m-d H:i:s'),
    'host' => isset($catalog['host']) ? $catalog['host'] : '',
    'methods' => isset($catalog['methods']) ? $catalog['methods'] : array()
);

$jsonFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
if ((int)pm_vpn_api_request_value($data, array('pretty'), 1) === 1) {
    $jsonFlags |= JSON_PRETTY_PRINT;
}

if (function_exists('http_response_code')) {
    http_response_code(200);
}
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
echo json_encode($payload, $jsonFlags);
exit;
