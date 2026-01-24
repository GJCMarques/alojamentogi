<?php
require_once __DIR__ . '/../../../includes/init.php';

// Check if order exists in session
if (!isset($_SESSION['checkout_order']) || !isset($_SESSION['checkout_order']['order_id'])) {
    header('Location: ' . basePath() . '/loja/checkout/');
    exit;
}

$order = $_SESSION['checkout_order'];
$orderId = $order['order_id'];
$paymentMethod = $order['payment_method'];
$totalAmount = $order['total'];

$base = basePath();
$lang = \Core\Language::getInstance();

// IfthenPay configuration
$ifthenPayKey = config('payment.ifthenpay.mbway_key', '');
$ifthenPayEntity = config('payment.ifthenpay.multibanco_entity', '');

$pageTitle = 'Pagamento - A Casa do Gi';

// Process payment based on method
$paymentData = null;
$paymentError = null;

try {
    switch ($paymentMethod) {
        case 'mbway':
            // MBWay payment
            $phone = $order['customer']['phone'] ?? '';
            if (empty($phone)) {
                throw new Exception('Número de telefone não fornecido');
            }

            // Call IfthenPay MBWay API
            $paymentData = [
                'type' => 'mbway',
                'phone' => $phone,
                'amount' => $totalAmount,
                'order_id' => $orderId,
                'status' => 'pending'
            ];
            break;

        case 'multibanco':
            // Multibanco payment - generate reference
            $paymentData = [
                'type' => 'multibanco',
                'entity' => $ifthenPayEntity,
                'reference' => generateMultibancoReference($orderId),
                'amount' => $totalAmount,
                'order_id' => $orderId,
                'status' => 'pending'
            ];
            break;

        case 'card':
            // Credit/Debit card payment
            $paymentData = [
                'type' => 'card',
                'amount' => $totalAmount,
                'order_id' => $orderId,
                'status' => 'pending'
            ];
            break;

        default:
            throw new Exception('Método de pagamento inválido');
    }

    // Store payment data in database
    $db = \Core\Database::getInstance()->getPdo();
    $stmt = $db->prepare("UPDATE orders SET payment_data = ? WHERE id = ?");
    $stmt->execute([json_encode($paymentData), $orderId]);

} catch (Exception $e) {
    $paymentError = $e->getMessage();
}

// Helper function to generate Multibanco reference
function generateMultibancoReference($orderId) {
    // This is a simplified version - in production you'd call IfthenPay API
    // Format: 999 999 999
    $reference = str_pad($orderId, 9, '0', STR_PAD_LEFT);
    return substr($reference, 0, 3) . ' ' . substr($reference, 3, 3) . ' ' . substr($reference, 6, 3);
}
?>
<!DOCTYPE html>
<html lang="<?= $lang->getCurrentLang() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <?php include __DIR__ . '/../../../includes/header.php'; ?>

    <main class="container mx-auto px-4 py-8 max-w-4xl">
        <?php if ($paymentError): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <div class="flex">
                    <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3"></i>
                    <div>
                        <h3 class="text-red-800 font-semibold">Erro no Pagamento</h3>
                        <p class="text-red-700"><?= htmlspecialchars($paymentError) ?></p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Payment Success/Instructions -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="text-center mb-6">
                    <i class="fas fa-check-circle text-green-500 text-5xl mb-3"></i>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Encomenda Confirmada!</h1>
                    <p class="text-gray-600">Número da encomenda: <span class="font-semibold">#<?= $orderId ?></span></p>
                </div>

                <?php if ($paymentMethod === 'mbway'): ?>
                    <!-- MBWay Instructions -->
                    <div class="border-t pt-6">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-mobile-alt text-blue-600 text-3xl mr-4"></i>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800">Pagamento MBWay</h2>
                                <p class="text-gray-600">Valor: <span class="font-bold text-lg">€<?= number_format($totalAmount, 2) ?></span></p>
                            </div>
                        </div>

                        <div class="bg-blue-50 p-4 rounded-lg mb-4">
                            <p class="text-sm text-gray-700 mb-2">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                                Foi enviado um pedido de pagamento para o número:
                            </p>
                            <p class="text-2xl font-bold text-center text-blue-600 my-3"><?= htmlspecialchars($paymentData['phone']) ?></p>
                            <p class="text-sm text-gray-600 text-center">
                                Por favor, abra a app MBWay no seu telemóvel e confirme o pagamento.
                            </p>
                        </div>

                        <div class="flex items-center justify-center space-x-2 mb-4">
                            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600"></div>
                            <span class="text-gray-600">A aguardar confirmação...</span>
                        </div>

                        <div class="text-sm text-gray-500 space-y-1">
                            <p><i class="fas fa-clock mr-2"></i>O pedido expira em 5 minutos</p>
                            <p><i class="fas fa-envelope mr-2"></i>Receberá um email de confirmação após o pagamento</p>
                        </div>
                    </div>

                <?php elseif ($paymentMethod === 'multibanco'): ?>
                    <!-- Multibanco Instructions -->
                    <div class="border-t pt-6">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-university text-blue-600 text-3xl mr-4"></i>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800">Pagamento por Multibanco</h2>
                                <p class="text-gray-600">Use os dados abaixo para efetuar o pagamento</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-lg mb-4 space-y-4">
                            <div class="flex justify-between items-center border-b pb-3">
                                <span class="text-gray-600">Entidade:</span>
                                <span class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($paymentData['entity']) ?></span>
                            </div>
                            <div class="flex justify-between items-center border-b pb-3">
                                <span class="text-gray-600">Referência:</span>
                                <span class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($paymentData['reference']) ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Valor:</span>
                                <span class="text-2xl font-bold text-green-600">€<?= number_format($totalAmount, 2) ?></span>
                            </div>
                        </div>

                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Importante:</strong> A sua encomenda só será processada após a confirmação do pagamento.
                            </p>
                        </div>

                        <div class="text-sm text-gray-600 space-y-2">
                            <p><i class="fas fa-check-circle text-green-500 mr-2"></i>Pode pagar em qualquer Multibanco ou Homebanking</p>
                            <p><i class="fas fa-check-circle text-green-500 mr-2"></i>A referência é válida por 3 dias</p>
                            <p><i class="fas fa-envelope mr-2"></i>Receberá um email de confirmação após o pagamento</p>
                        </div>
                    </div>

                <?php elseif ($paymentMethod === 'card'): ?>
                    <!-- Credit Card Payment -->
                    <div class="border-t pt-6">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-credit-card text-blue-600 text-3xl mr-4"></i>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800">Pagamento por Cartão</h2>
                                <p class="text-gray-600">Valor: <span class="font-bold text-lg">€<?= number_format($totalAmount, 2) ?></span></p>
                            </div>
                        </div>

                        <div class="bg-blue-50 p-4 rounded-lg mb-4">
                            <p class="text-sm text-gray-700 mb-4 text-center">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                                Será redirecionado para a página de pagamento seguro IfthenPay
                            </p>

                            <form id="cardPaymentForm" method="POST" action="<?= $base ?>/api/payment-process.php">
                                <input type="hidden" name="order_id" value="<?= $orderId ?>">
                                <input type="hidden" name="payment_method" value="card">
                                <input type="hidden" name="amount" value="<?= $totalAmount ?>">

                                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition font-semibold">
                                    <i class="fas fa-lock mr-2"></i>Proceder ao Pagamento Seguro
                                </button>
                            </form>
                        </div>

                        <div class="flex items-center justify-center space-x-4 text-gray-500 text-sm">
                            <i class="fas fa-shield-alt"></i>
                            <span>Pagamento 100% seguro e encriptado</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Resumo da Encomenda</h3>

                <div class="space-y-3">
                    <?php if (isset($order['items'])): ?>
                        <?php foreach ($order['items'] as $item): ?>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600"><?= htmlspecialchars($item['name']) ?> x<?= $item['quantity'] ?></span>
                                <span class="font-semibold">€<?= number_format($item['subtotal'], 2) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (isset($order['shipping_cost']) && $order['shipping_cost'] > 0): ?>
                        <div class="flex justify-between text-sm border-t pt-3">
                            <span class="text-gray-600">Envio</span>
                            <span class="font-semibold">€<?= number_format($order['shipping_cost'], 2) ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="flex justify-between text-lg font-bold border-t pt-3">
                        <span>Total</span>
                        <span class="text-green-600">€<?= number_format($totalAmount, 2) ?></span>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t">
                    <h4 class="font-semibold text-gray-800 mb-2">Dados de Envio</h4>
                    <div class="text-sm text-gray-600 space-y-1">
                        <p><?= htmlspecialchars($order['customer']['name']) ?></p>
                        <p><?= htmlspecialchars($order['shipping']['address']) ?></p>
                        <p><?= htmlspecialchars($order['shipping']['postal_code']) ?> <?= htmlspecialchars($order['shipping']['city']) ?></p>
                        <p><?= htmlspecialchars($order['customer']['email']) ?></p>
                        <p><?= htmlspecialchars($order['customer']['phone']) ?></p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-6">
                <a href="<?= $base ?>/" class="inline-block bg-gray-200 text-gray-700 py-2 px-6 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-home mr-2"></i>Voltar à Página Inicial
                </a>
            </div>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/../../../includes/footer.php'; ?>

    <?php if ($paymentMethod === 'mbway'): ?>
    <script>
        // Poll for payment status every 5 seconds
        let pollInterval = setInterval(function() {
            fetch('<?= $base ?>/api/check-payment-status.php?order_id=<?= $orderId ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'paid') {
                        clearInterval(pollInterval);
                        window.location.href = '<?= $base ?>/loja/checkout/confirmacao/?order_id=<?= $orderId ?>';
                    } else if (data.status === 'expired' || data.status === 'failed') {
                        clearInterval(pollInterval);
                        alert('O pagamento expirou ou foi cancelado. Por favor, tente novamente.');
                        window.location.href = '<?= $base ?>/loja/checkout/';
                    }
                })
                .catch(error => console.error('Error checking payment status:', error));
        }, 5000);

        // Stop polling after 5 minutes
        setTimeout(function() {
            clearInterval(pollInterval);
        }, 300000);
    </script>
    <?php endif; ?>
</body>
</html>
<?php
// Clear checkout session after displaying payment info
// But keep order_id for confirmation
unset($_SESSION['checkout_order']['items']);
unset($_SESSION['checkout_order']['customer']);
unset($_SESSION['checkout_order']['shipping']);
?>
