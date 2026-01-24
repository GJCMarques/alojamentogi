<?php
require_once __DIR__ . '/../includes/init.php';

/**
 * IfthenPay Payment Callback Handler
 *
 * This endpoint receives payment confirmations from IfthenPay
 * Supports: MBWay, Multibanco, and Credit Card payments
 */

// Log all incoming requests for debugging
$logFile = __DIR__ . '/../logs/payment-callbacks.log';
if (!file_exists(dirname($logFile))) {
    mkdir(dirname($logFile), 0755, true);
}

$logData = [
    'timestamp' => date('Y-m-d H:i:s'),
    'method' => $_SERVER['REQUEST_METHOD'],
    'get' => $_GET,
    'post' => $_POST,
    'raw_input' => file_get_contents('php://input'),
    'headers' => getallheaders()
];

file_put_contents($logFile, json_encode($logData, JSON_PRETTY_PRINT) . "\n\n", FILE_APPEND);

// Get callback data
$data = $_POST ?: json_decode(file_get_contents('php://input'), true);

if (empty($data)) {
    http_response_code(400);
    echo 'Invalid callback data';
    exit;
}

// Extract payment information based on IfthenPay format
// IfthenPay typically sends: order_id, amount, reference, status, payment_method
$orderId = $data['order_id'] ?? $data['Id'] ?? null;
$reference = $data['reference'] ?? $data['Referencia'] ?? null;
$amount = $data['amount'] ?? $data['Valor'] ?? null;
$paymentMethod = $data['payment_method'] ?? $data['method'] ?? null;
$status = $data['status'] ?? 'paid'; // IfthenPay only calls back on successful payments
$transactionId = $data['transaction_id'] ?? $data['idPedido'] ?? uniqid('txn_');

// Validate callback authenticity (IfthenPay key verification)
$callbackKey = $data['key'] ?? $data['chave'] ?? null;
$expectedKey = config('payment.ifthenpay.anti_phishing_key', '');

if (!empty($expectedKey) && $callbackKey !== $expectedKey) {
    http_response_code(403);
    error_log('Invalid IfthenPay callback key');
    echo 'Invalid authentication';
    exit;
}

if (!$orderId) {
    http_response_code(400);
    echo 'Missing order ID';
    exit;
}

try {
    $db = \Core\Database::getInstance()->getPdo();

    // Get order details
    $stmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        http_response_code(404);
        error_log("Order not found: $orderId");
        echo 'Order not found';
        exit;
    }

    // Verify amount matches (convert to cents for comparison)
    if ($amount && abs((float)$amount - (float)$order['total_amount']) > 0.01) {
        error_log("Amount mismatch for order $orderId: expected {$order['total_amount']}, got $amount");
        http_response_code(400);
        echo 'Amount mismatch';
        exit;
    }

    // Check if payment was already processed
    if ($order['payment_status'] === 'paid') {
        http_response_code(200);
        echo 'Payment already processed';
        exit;
    }

    // Update order status
    $stmt = $db->prepare("
        UPDATE orders
        SET payment_status = ?,
            transaction_id = ?,
            paid_at = NOW(),
            updated_at = NOW()
        WHERE id = ?
    ");

    $stmt->execute(['paid', $transactionId, $orderId]);

    // Update stock quantities
    $stmt = $db->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
    $stmt->execute([$orderId]);
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($orderItems as $item) {
        $stmt = $db->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
        $stmt->execute([$item['quantity'], $item['product_id']]);
    }

    // Send confirmation email to customer
    $customerEmail = $order['customer_email'];
    $customerName = $order['customer_name'];

    if ($customerEmail) {
        $subject = "Pagamento Confirmado - Encomenda #$orderId";
        $message = "
            <html>
            <body>
                <h2>Pagamento Confirmado!</h2>
                <p>Olá $customerName,</p>
                <p>O seu pagamento foi confirmado com sucesso.</p>
                <p><strong>Detalhes da encomenda:</strong></p>
                <ul>
                    <li>Número da encomenda: #$orderId</li>
                    <li>Valor: €" . number_format($order['total_amount'], 2) . "</li>
                    <li>Método de pagamento: " . ucfirst($order['payment_method']) . "</li>
                    <li>ID da transação: $transactionId</li>
                </ul>
                <p>A sua encomenda será processada e enviada em breve.</p>
                <p>Obrigado pela sua compra!</p>
                <p>--<br>A Casa do Gi</p>
            </body>
            </html>
        ";

        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: A Casa do Gi <noreply@acasadogi.pt>',
            'Reply-To: info@acasadogi.pt'
        ];

        mail($customerEmail, $subject, $message, implode("\r\n", $headers));
    }

    // Log successful payment
    error_log("Payment confirmed for order $orderId - Transaction: $transactionId");

    http_response_code(200);
    echo 'OK';

} catch (Exception $e) {
    error_log("Payment callback error: " . $e->getMessage());
    http_response_code(500);
    echo 'Internal error';
}
