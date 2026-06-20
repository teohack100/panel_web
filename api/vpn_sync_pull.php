<?php
require_once __DIR__ . '/vpn_api_common.php';

programmit_vpn_ensure_tables($db);

$data = pm_vpn_api_input();
$serverKey = trim((string)pm_vpn_api_request_value($data, array('server_key', 'server', 'node'), ''));
if ($serverKey === '') {
    $serverKey = pm_vpn_api_header('X-Server-Key');
}

$token = trim((string)pm_vpn_api_request_value($data, array('sync_token', 'token'), ''));
if ($token === '') {
    $token = pm_vpn_api_header('X-Sync-Token');
}
if ($token === '') {
    $token = pm_vpn_api_bearer_token();
}

$requestIp = method_exists($db, 'get_client_ip') ? (string)$db->get_client_ip() : '';

if ($serverKey === '' || $token === '') {
    programmit_vpn_sync_log($db, 0, 'pull', 'error', 0, 0, 0, $requestIp, array('reason' => 'missing_credentials'));
    pm_vpn_api_fail(401, 'missing_credentials');
}

$server = programmit_vpn_server_authenticate($db, $serverKey, $token);
if (!$server) {
    $knownServer = programmit_vpn_server_find_by_key($db, $serverKey);
    $knownServerId = ($knownServer && isset($knownServer['id'])) ? (int)$knownServer['id'] : 0;
    programmit_vpn_sync_log($db, $knownServerId, 'pull', 'error', 0, 0, 0, $requestIp, array('reason' => 'auth_failed', 'server_key' => $serverKey));
    pm_vpn_api_fail(401, 'auth_failed');
}

$serverId = (int)$server['id'];
$runtime = pm_vpn_api_request_value($data, array('runtime', 'agent_runtime'), array());
if (!is_array($runtime)) {
    $runtime = array();
}
$serverStatus = strtolower(trim((string)(isset($server['status']) ? $server['status'] : 'active')));
if ($serverStatus === 'disabled') {
    programmit_vpn_sync_log($db, $serverId, 'pull', 'error', 0, 0, 0, $requestIp, array('reason' => 'server_disabled'));
    pm_vpn_api_fail(403, 'server_disabled');
}

$requestedCursor = (int)pm_vpn_api_request_value($data, array('cursor', 'since_id', 'last_event_id'), 0);
$storedCursor = isset($server['last_sync_cursor']) ? (int)$server['last_sync_cursor'] : 0;
if ($requestedCursor <= 0) {
    $cursor = $storedCursor;
} elseif ($storedCursor > 0 && $requestedCursor > $storedCursor) {
    $cursor = $storedCursor;
} else {
    $cursor = $requestedCursor;
}

$limit = (int)pm_vpn_api_request_value($data, array('limit'), 100);
if ($limit <= 0) {
    $limit = 100;
}
if ($limit > 500) {
    $limit = 500;
}

$reconcile = programmit_vpn_reconcile_users($db, false);
$methodKeys = programmit_vpn_server_method_keys($db, $serverId);
$syncBatch = programmit_vpn_server_events_since($db, $serverId, $cursor, $limit);
$events = isset($syncBatch['events']) && is_array($syncBatch['events']) ? $syncBatch['events'] : array();
$nextCursor = isset($syncBatch['next_cursor']) ? (int)$syncBatch['next_cursor'] : $cursor;

programmit_vpn_touch_server($db, $serverId);
if (!empty($runtime)) {
    programmit_vpn_server_update_runtime($db, $serverId, $runtime, $requestIp);
}
$logStatus = empty($methodKeys) ? 'warn' : 'ok';
programmit_vpn_sync_log($db, $serverId, 'pull', $logStatus, $cursor, $nextCursor, count($events), $requestIp, array(
    'requested_cursor' => $requestedCursor,
    'method_keys' => $methodKeys,
    'reconcile' => $reconcile,
    'runtime' => !empty($runtime) ? programmit_vpn_agent_runtime_normalize($runtime, $requestIp) : array()
));

pm_vpn_api_json(200, array(
    'ok' => true,
    'server' => array(
        'server_id' => $serverId,
        'server_key' => (string)$server['server_key'],
        'server_name' => (string)$server['server_name']
    ),
    'cursor_from' => $cursor,
    'next_cursor' => $nextCursor,
    'events_count' => count($events),
    'method_keys' => $methodKeys,
    'events' => $events,
    'generated_at' => date('Y-m-d H:i:s'),
    'reconcile' => $reconcile
));
