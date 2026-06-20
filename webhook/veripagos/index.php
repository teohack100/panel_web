<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));

if ($method === 'GET') {
    echo json_encode(array(
        'ok' => true,
        'message' => 'veripagos_callback_ready',
        'target' => '/api/finance_webhook.php'
    ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(array(
        'ok' => false,
        'message' => 'method_not_allowed'
    ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

require_once __DIR__ . '/../../api/finance_webhook.php';
