<?php

require_once __DIR__ . '/../includes/init.php';

use Core\Payment\IfthenPay;
use Core\RateLimiter;

header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

$rateLimiter = RateLimiter::getInstance();
if (!$rateLimiter->check('check_payment_status', 20, 60)) {
    http_response_code(429);
    echo json_encode(['error' => 'Too many requests']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$orderId = isset($_GET['order_id']) ? (int) $_GET['order_id'] : 0;

if ($orderId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid order ID']);
    exit;
}

$pendingOrder = $_SESSION['pending_order'] ?? $_SESSION['checkout_order'] ?? null;
if (!$pendingOrder || ((int)($pendingOrder['id'] ?? 0) !== $orderId && (int)($pendingOrder['order_id'] ?? 0) !== $orderId)) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $gateway = IfthenPay::getInstance();
    $result = $gateway->checkPaymentStatus($orderId);

    echo json_encode([
        'status' => $result['status'],
        'message' => $result['message'],
        'order_id' => $orderId,
    ]);
} catch (\Exception $e) {
    logMessage("Check payment status error: " . $e->getMessage(), 'error');
    http_response_code(500);
    echo json_encode(['error' => 'Internal error']);
}
