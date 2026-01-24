<?php
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

if (!isset($_GET['order_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Order ID is required']);
    exit;
}

$orderId = (int)$_GET['order_id'];

try {
    $db = \Core\Database::getInstance()->getPdo();

    $stmt = $db->prepare("SELECT payment_status, payment_data FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        http_response_code(404);
        echo json_encode(['error' => 'Order not found']);
        exit;
    }

    echo json_encode([
        'status' => $order['payment_status'],
        'order_id' => $orderId
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
