<?php
/**
 * IfthenPay Payment Callback Handler
 * Receives payment confirmations from IfthenPay (MBWay, Multibanco, Card)
 *
 * Security: Anti-phishing key verification, amount validation, replay protection
 */

require_once __DIR__ . '/../includes/init.php';

use Core\Payment\IfthenPay;
use Core\RateLimiter;

// Rate limit: max 30 callback requests per minute per IP
$rateLimiter = RateLimiter::getInstance();
if (!$rateLimiter->check('payment_callback', 30, 60)) {
    logMessage("Rate limit exceeded on payment callback from " . getClientIp(), 'warning');
    http_response_code(429);
    header('Retry-After: 60');
    exit('Too many requests');
}

// Log all incoming requests for audit trail
$logDir = ROOT_PATH . '/logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

$logData = [
    'timestamp' => date('Y-m-d H:i:s'),
    'method' => $_SERVER['REQUEST_METHOD'],
    'ip' => getClientIp(),
    'get' => $_GET,
    'post' => $_POST,
    'raw_input' => file_get_contents('php://input'),
];

file_put_contents(
    $logDir . '/payment-callbacks.log',
    json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n",
    FILE_APPEND | LOCK_EX
);

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    exit('Method not allowed');
}

// Get callback data from POST body or GET params (IfthenPay uses both)
$data = $_POST;
if (empty($data)) {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true) ?? [];
}
if (empty($data)) {
    $data = $_GET;
}

if (empty($data)) {
    http_response_code(400);
    exit('No callback data');
}

// Process callback through IfthenPay class
try {
    $gateway = IfthenPay::getInstance();
    $result = $gateway->handleCallback($data);

    if ($result['success']) {
        http_response_code(200);
        echo 'OK';
    } else {
        http_response_code(400);
        echo $result['message'];
    }
} catch (\Exception $e) {
    logMessage("Payment callback exception: " . $e->getMessage(), 'error');
    http_response_code(500);
    echo 'Internal error';
}
