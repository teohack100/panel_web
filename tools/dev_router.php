<?php
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$docRoot = realpath(dirname(__DIR__));

if ($docRoot !== false && is_string($requestPath) && $requestPath !== '/') {
    $candidate = realpath($docRoot . DIRECTORY_SEPARATOR . ltrim($requestPath, '/'));
    if ($candidate !== false && strpos($candidate, $docRoot) === 0 && is_file($candidate)) {
        return false;
    }
}

require $docRoot . DIRECTORY_SEPARATOR . 'index.php';
