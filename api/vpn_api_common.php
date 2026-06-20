<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '0');

require_once __DIR__ . '/../includes/functions.php';

if (!function_exists('pm_vpn_api_json')) {
    function pm_vpn_api_json($statusCode, $payload) {
        if (function_exists('http_response_code')) {
            http_response_code((int)$statusCode);
        }
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

if (!function_exists('pm_vpn_api_fail')) {
    function pm_vpn_api_fail($statusCode, $message, $extra = array()) {
        $payload = array(
            'ok' => false,
            'message' => (string)$message
        );
        if (is_array($extra)) {
            foreach ($extra as $key => $value) {
                $payload[$key] = $value;
            }
        }
        pm_vpn_api_json($statusCode, $payload);
    }
}

if (!function_exists('pm_vpn_api_input')) {
    function pm_vpn_api_input() {
        static $data = null;
        if ($data !== null) {
            return $data;
        }
        $data = array();
        if (is_array($_GET)) {
            $data = array_merge($data, $_GET);
        }
        if (is_array($_POST)) {
            $data = array_merge($data, $_POST);
        }
        $rawBody = file_get_contents('php://input');
        if (is_string($rawBody) && trim($rawBody) !== '') {
            $json = json_decode($rawBody, true);
            if (is_array($json)) {
                $data = array_merge($data, $json);
            }
        }
        return $data;
    }
}

if (!function_exists('pm_vpn_api_header')) {
    function pm_vpn_api_header($key) {
        $serverKey = 'HTTP_' . strtoupper(str_replace('-', '_', (string)$key));
        if (isset($_SERVER[$serverKey])) {
            return trim((string)$_SERVER[$serverKey]);
        }
        return '';
    }
}

if (!function_exists('pm_vpn_api_request_value')) {
    function pm_vpn_api_request_value($data, $keys, $default = '') {
        foreach ((array)$keys as $key) {
            if (isset($data[$key])) {
                return $data[$key];
            }
        }
        return $default;
    }
}

if (!function_exists('pm_vpn_api_bearer_token')) {
    function pm_vpn_api_bearer_token() {
        $header = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $header = trim((string)$_SERVER['HTTP_AUTHORIZATION']);
        } elseif (isset($_SERVER['Authorization'])) {
            $header = trim((string)$_SERVER['Authorization']);
        }
        if ($header !== '' && stripos($header, 'Bearer ') === 0) {
            return trim(substr($header, 7));
        }
        return '';
    }
}
