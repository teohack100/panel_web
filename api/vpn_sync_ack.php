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
    programmit_vpn_sync_log($db, 0, 'ack', 'error', 0, 0, 0, $requestIp, array('reason' => 'missing_credentials'));
    pm_vpn_api_fail(401, 'missing_credentials');
}

$server = programmit_vpn_server_authenticate($db, $serverKey, $token);
if (!$server) {
    $knownServer = programmit_vpn_server_find_by_key($db, $serverKey);
    $knownServerId = ($knownServer && isset($knownServer['id'])) ? (int)$knownServer['id'] : 0;
    programmit_vpn_sync_log($db, $knownServerId, 'ack', 'error', 0, 0, 0, $requestIp, array('reason' => 'auth_failed', 'server_key' => $serverKey));
    pm_vpn_api_fail(401, 'auth_failed');
}

$serverId = (int)$server['id'];
$runtime = pm_vpn_api_request_value($data, array('runtime', 'agent_runtime'), array());
if (!is_array($runtime)) {
    $runtime = array();
}
$storedCursor = isset($server['last_sync_cursor']) ? (int)$server['last_sync_cursor'] : 0;
$cursorFrom = (int)pm_vpn_api_request_value($data, array('cursor_from'), 0);
$ackCursor = (int)pm_vpn_api_request_value($data, array('cursor', 'ack_cursor', 'last_event_id'), 0);

if ($ackCursor <= 0) {
    programmit_vpn_sync_log($db, $serverId, 'ack', 'error', $storedCursor, $ackCursor, 0, $requestIp, array('reason' => 'invalid_cursor'));
    pm_vpn_api_fail(422, 'invalid_cursor');
}

if ($cursorFrom > 0 && $storedCursor !== $cursorFrom) {
    programmit_vpn_sync_log($db, $serverId, 'ack', 'error', $storedCursor, $ackCursor, 0, $requestIp, array(
        'reason' => 'cursor_conflict',
        'cursor_from' => $cursorFrom
    ));
    pm_vpn_api_fail(409, 'cursor_conflict', array(
        'expected_cursor_from' => $storedCursor,
        'received_cursor_from' => $cursorFrom
    ));
}

$ok = programmit_vpn_mark_server_sync($db, $serverId, $ackCursor, true);
if (!$ok) {
    programmit_vpn_sync_log($db, $serverId, 'ack', 'error', $storedCursor, $ackCursor, 0, $requestIp, array('reason' => 'update_failed'));
    pm_vpn_api_fail(500, 'ack_failed');
}

if (!empty($runtime)) {
    programmit_vpn_server_update_runtime($db, $serverId, $runtime, $requestIp);
}

programmit_vpn_sync_log($db, $serverId, 'ack', 'ok', $storedCursor, $ackCursor, 0, $requestIp, array(
    'cursor_from' => $cursorFrom,
    'runtime' => !empty($runtime) ? programmit_vpn_agent_runtime_normalize($runtime, $requestIp) : array()
));

pm_vpn_api_json(200, array(
    'ok' => true,
    'message' => 'ack_recorded',
    'server' => array(
        'server_id' => $serverId,
        'server_key' => (string)$server['server_key'],
        'server_name' => (string)$server['server_name']
    ),
    'cursor_from' => $storedCursor,
    'ack_cursor' => $ackCursor,
    'ack_at' => date('Y-m-d H:i:s')
));
