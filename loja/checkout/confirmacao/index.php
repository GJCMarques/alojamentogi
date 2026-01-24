<?php
require_once __DIR__ . '/../../../includes/init.php';

$orderId = $_GET['order_id'] ?? null;

if (!$orderId) {
    header('Location: ' . basePath() . '/loja/');
    exit;
}

$base = basePath();
$lang = \Core\Language::getInstance();

try {
    $db = \Core\Database::getInstance()->getPdo();

    // Get order details
    $stmt = $db->prepare("
        SELECT o.*,
               COUNT(oi.id) as item_count
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE o.id = ?
        GROUP BY o.id
    ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        throw new Exception('Encomenda não encontrada');
    }

    // Get order items
    $stmt = $db->prepare("
        SELECT oi.*, p.name
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$orderId]);
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: ' . basePath() . '/loja/');
    exit;
}

$pageTitle = 'Encomenda Confirmada - A Casa do Gi';

// Clear cart after successful order
if ($order['payment_status'] === 'paid') {
    unset($_SESSION['cart']);
    unset($_SESSION['checkout_order']);
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

    <main class="container mx-auto px-4 py-12 max-w-4xl">
        <!-- Success Message -->
        <div class="text-center mb-8">
            <?php if ($order['payment_status'] === 'paid'): ?>
                <div class="inline-block bg-green-100 rounded-full p-6 mb-4">
                    <i class="fas fa-check-circle text-green-500 text-6xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Pagamento Confirmado!</h1>
                <p class="text-gray-600 text-lg">Obrigado pela sua compra</p>
            <?php else: ?>
                <div class="inline-block bg-blue-100 rounded-full p-6 mb-4">
                    <i class="fas fa-clock text-blue-500 text-6xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Encomenda Registada</h1>
                <p class="text-gray-600 text-lg">A aguardar confirmação de pagamento</p>
            <?php endif; ?>
        </div>

        <!-- Order Summary Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold mb-1">Encomenda #<?= $orderId ?></h2>
                        <p class="text-blue-100">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold <?= $order['payment_status'] === 'paid' ? 'bg-green-500' : 'bg-yellow-500' ?>">
                            <?php
                            $statusLabels = [
                                'pending' => 'Pendente',
                                'paid' => 'Pago',
                                'processing' => 'Em Processamento',
                                'shipped' => 'Enviado',
                                'delivered' => 'Entregue',
                                'cancelled' => 'Cancelado'
                            ];
                            echo $statusLabels[$order['payment_status']] ?? 'Desconhecido';
                            ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <!-- Order Items -->
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Produtos</h3>
                <div class="space-y-3 mb-6">
                    <?php foreach ($orderItems as $item): ?>
                        <div class="flex justify-between items-center pb-3 border-b">
                            <div class="flex-1">
                                <p class="font-medium text-gray-800"><?= htmlspecialchars($item['name']) ?></p>
                                <p class="text-sm text-gray-500">
                                    Quantidade: <?= $item['quantity'] ?> × €<?= number_format($item['price'], 2) ?>
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-800">€<?= number_format($item['subtotal'], 2) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Totals -->
                <div class="space-y-2 mb-6 border-t pt-4">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span>€<?= number_format($order['subtotal'], 2) ?></span>
                    </div>
                    <?php if ($order['shipping_cost'] > 0): ?>
                        <div class="flex justify-between text-gray-600">
                            <span>Envio</span>
                            <span>€<?= number_format($order['shipping_cost'], 2) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="flex justify-between text-xl font-bold text-gray-800 border-t pt-2">
                        <span>Total</span>
                        <span class="text-green-600">€<?= number_format($order['total_amount'], 2) ?></span>
                    </div>
                </div>

                <!-- Customer & Shipping Info -->
                <div class="grid md:grid-cols-2 gap-6 border-t pt-6">
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-user text-blue-600 mr-2"></i>Dados do Cliente
                        </h4>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p><?= htmlspecialchars($order['customer_name']) ?></p>
                            <p><?= htmlspecialchars($order['customer_email']) ?></p>
                            <p><?= htmlspecialchars($order['customer_phone']) ?></p>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-shipping-fast text-blue-600 mr-2"></i>Endereço de Envio
                        </h4>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p><?= htmlspecialchars($order['shipping_address']) ?></p>
                            <p><?= htmlspecialchars($order['shipping_postal_code']) ?> <?= htmlspecialchars($order['shipping_city']) ?></p>
                            <?php if ($order['shipping_notes']): ?>
                                <p class="mt-2 text-xs italic"><?= htmlspecialchars($order['shipping_notes']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="border-t pt-6 mt-6">
                    <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-credit-card text-blue-600 mr-2"></i>Método de Pagamento
                    </h4>
                    <p class="text-gray-600">
                        <?php
                        $paymentLabels = [
                            'mbway' => 'MBWay',
                            'multibanco' => 'Multibanco',
                            'card' => 'Cartão de Crédito/Débito'
                        ];
                        echo $paymentLabels[$order['payment_method']] ?? ucfirst($order['payment_method']);
                        ?>
                    </p>
                    <?php if ($order['transaction_id']): ?>
                        <p class="text-sm text-gray-500 mt-1">ID Transação: <?= htmlspecialchars($order['transaction_id']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="bg-blue-50 border-l-4 border-blue-600 p-6 rounded-r-lg mb-6">
            <h3 class="font-semibold text-blue-900 mb-3 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>Próximos Passos
            </h3>
            <ul class="space-y-2 text-blue-800 text-sm">
                <?php if ($order['payment_status'] === 'paid'): ?>
                    <li><i class="fas fa-check text-green-600 mr-2"></i>A sua encomenda será processada em breve</li>
                    <li><i class="fas fa-box text-blue-600 mr-2"></i>Receberá um email quando a encomenda for enviada</li>
                    <li><i class="fas fa-truck text-blue-600 mr-2"></i>Tempo estimado de entrega: 3-5 dias úteis</li>
                    <li><i class="fas fa-envelope text-blue-600 mr-2"></i>Enviámos um email de confirmação para <?= htmlspecialchars($order['customer_email']) ?></li>
                <?php else: ?>
                    <li><i class="fas fa-clock text-yellow-600 mr-2"></i>A aguardar confirmação de pagamento</li>
                    <li><i class="fas fa-envelope text-blue-600 mr-2"></i>Receberá um email assim que o pagamento for confirmado</li>
                    <li><i class="fas fa-info-circle text-blue-600 mr-2"></i>A encomenda será processada após a confirmação do pagamento</li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?= $base ?>/" class="inline-flex items-center justify-center bg-blue-600 text-white py-3 px-8 rounded-lg hover:bg-blue-700 transition font-semibold">
                <i class="fas fa-home mr-2"></i>Voltar à Página Inicial
            </a>
            <a href="<?= $base ?>/loja/" class="inline-flex items-center justify-center bg-gray-200 text-gray-700 py-3 px-8 rounded-lg hover:bg-gray-300 transition font-semibold">
                <i class="fas fa-shopping-bag mr-2"></i>Continuar a Comprar
            </a>
        </div>

        <!-- Support Contact -->
        <div class="text-center mt-8 text-sm text-gray-500">
            <p>Precisa de ajuda? Contacte-nos:</p>
            <p class="mt-2">
                <i class="fas fa-envelope mr-2"></i>
                <a href="mailto:info@acasadogi.pt" class="text-blue-600 hover:underline">info@acasadogi.pt</a>
                <span class="mx-3">|</span>
                <i class="fas fa-phone mr-2"></i>
                <a href="tel:+351279340100" class="text-blue-600 hover:underline">+351 279 340 100</a>
            </p>
        </div>
    </main>

    <?php include __DIR__ . '/../../../includes/footer.php'; ?>
</body>
</html>
