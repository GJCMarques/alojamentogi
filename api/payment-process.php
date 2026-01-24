<?php
require_once __DIR__ . '/../includes/init.php';

/**
 * Payment Processing API
 * Handles card payment initialization and redirects to IfthenPay gateway
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . basePath() . '/loja/checkout/');
    exit;
}

$orderId = $_POST['order_id'] ?? null;
$paymentMethod = $_POST['payment_method'] ?? null;
$amount = $_POST['amount'] ?? null;

if (!$orderId || !$paymentMethod || !$amount) {
    $_SESSION['error'] = 'Dados de pagamento inválidos';
    header('Location: ' . basePath() . '/loja/checkout/');
    exit;
}

try {
    $db = \Core\Database::getInstance()->getPdo();

    // Verify order exists
    $stmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        throw new Exception('Encomenda não encontrada');
    }

    // Verify amount matches
    if (abs((float)$amount - (float)$order['total_amount']) > 0.01) {
        throw new Exception('Valor do pagamento não corresponde à encomenda');
    }

    if ($paymentMethod === 'card') {
        // IfthenPay Credit Card Payment Configuration
        $ifthenPayKey = config('payment.ifthenpay.card_key', '');

        if (empty($ifthenPayKey)) {
            throw new Exception('Configuração de pagamento inválida. Por favor contacte o suporte.');
        }

        // Build IfthenPay payment URL
        // Documentation: https://ifthenpay.com/documentacao/
        $callbackUrl = basePath() . '/api/payment-callback.php';
        $returnUrl = basePath() . '/loja/checkout/confirmacao/?order_id=' . $orderId;
        $cancelUrl = basePath() . '/loja/checkout/?error=payment_cancelled';

        // IfthenPay payment parameters
        $paymentParams = [
            'key' => $ifthenPayKey,
            'orderId' => $orderId,
            'amount' => number_format($amount, 2, '.', ''),
            'successUrl' => $returnUrl,
            'errorUrl' => $cancelUrl,
            'cancelUrl' => $cancelUrl,
            'callbackUrl' => $callbackUrl,
            'language' => 'PT'
        ];

        // Build payment URL
        $paymentUrl = 'https://ifthenpay.com/api/creditcard/init?' . http_build_query($paymentParams);

        // Log payment initialization
        error_log("Initializing card payment for order $orderId - Amount: €$amount");

        // Redirect to IfthenPay payment page
        header('Location: ' . $paymentUrl);
        exit;

    } else {
        throw new Exception('Método de pagamento não suportado');
    }

} catch (Exception $e) {
    error_log("Payment processing error: " . $e->getMessage());
    $_SESSION['error'] = $e->getMessage();
    header('Location: ' . basePath() . '/loja/checkout/pagamento/');
    exit;
}
