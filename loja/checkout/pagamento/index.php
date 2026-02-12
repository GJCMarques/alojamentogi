<?php

require_once dirname(dirname(dirname(__DIR__))) . '/includes/init.php';

use Core\Database;
use Core\Language;
use Core\Session;
use Core\Payment\IfthenPay;

$db = Database::getInstance();
$lang = Language::getInstance();
$base = basePath();
$isEnglish = $lang->isEnglish();

$pendingOrder = Session::get('pending_order');

if (!$pendingOrder || empty($pendingOrder['id'])) {
    Session::flash('warning', $isEnglish ? 'No pending order found.' : 'Nenhuma encomenda pendente encontrada.');
    redirect($base . ($isEnglish ? '/en/shop/' : '/loja/'));
}

$orderId = (int) $pendingOrder['id'];
$orderNumber = $pendingOrder['number'] ?? '';
$totalAmount = (float) $pendingOrder['total'];
$paymentMethod = $pendingOrder['payment_method'] ?? 'mbway';
$customerPhone = $pendingOrder['phone'] ?? '';
$customerEmail = $pendingOrder['email'] ?? '';

$order = $db->fetch("SELECT * FROM orders WHERE id = ?", [$orderId]);
if (!$order) {
    Session::flash('error', $isEnglish ? 'Order not found.' : 'Encomenda não encontrada.');
    redirect($base . ($isEnglish ? '/en/shop/' : '/loja/'));
}

$orderItems = $db->fetchAll(
    "SELECT oi.*, COALESCE(pt.name, oi.product_name) as display_name
     FROM order_items oi
     LEFT JOIN product_translations pt ON oi.product_id = pt.product_id AND pt.language_id = 1
     WHERE oi.order_id = ?",
    [$orderId]
);

$gateway = IfthenPay::getInstance();
$paymentData = null;
$paymentError = null;

try {
    switch ($paymentMethod) {
        case 'mbway':
            $result = $gateway->createMBWayPayment($orderId, $customerPhone, $totalAmount);
            if ($result['success']) {
                $paymentData = [
                    'type' => 'mbway',
                    'phone' => $customerPhone,
                    'request_id' => $result['request_id'],
                ];
            } else {
                $paymentError = $result['message'];
            }
            break;

        case 'multibanco':
            $result = $gateway->createMultibancoReference($orderId, $totalAmount);
            if ($result['success']) {
                $paymentData = [
                    'type' => 'multibanco',
                    'entity' => $result['entity'],
                    'reference' => $result['reference'],
                ];
            } else {
                $paymentError = $result['message'];
            }
            break;

        case 'card':
            $returnUrl = $base . '/loja/checkout/confirmacao/';
            $result = $gateway->createCardPayment($orderId, $totalAmount, $returnUrl);
            if ($result['success'] && !empty($result['payment_url'])) {

                redirect($result['payment_url']);
            } else {
                $paymentData = ['type' => 'card'];

                if (!$result['success']) {
                    $paymentError = $result['message'];
                }
            }
            break;

        default:
            $paymentError = $isEnglish ? 'Invalid payment method.' : 'Método de pagamento inválido.';
    }
} catch (\Exception $e) {
    logMessage("Payment page error: " . $e->getMessage(), 'error');
    $paymentError = $isEnglish ? 'An error occurred processing your payment.' : 'Ocorreu um erro ao processar o pagamento.';
}

$pageTitle = $isEnglish ? 'Payment' : 'Pagamento';
$pageDescription = $isEnglish ? 'Complete your payment' : 'Complete o seu pagamento';

include INCLUDES_PATH . '/header.php';
?>

<!-- Breadcrumb -->
<nav class="bg-cream-200 py-3 border-b border-cream-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <ol class="flex items-center text-sm text-charcoal/60">
            <li><a href="<?= $base ?>/" class="hover:text-secondary"><?= $isEnglish ? 'Home' : 'Início' ?></a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="<?= $base ?>/loja/" class="hover:text-secondary"><?= $isEnglish ? 'Shop' : 'Loja' ?></a></li>
            <li><span class="mx-2">/</span></li>
            <li class="text-primary font-medium"><?= $isEnglish ? 'Payment' : 'Pagamento' ?></li>
        </ol>
    </div>
</nav>

<section class="py-12 lg:py-16 bg-cream-50 min-h-[60vh]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <?php if ($paymentError): ?>
        <!-- Error State -->
        <div class="bg-white rounded-3xl shadow-sm p-10 text-center border border-cream-100">
            <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <h2 class="font-serif text-2xl text-primary mb-3"><?= $isEnglish ? 'Payment Error' : 'Erro no Pagamento' ?></h2>
            <p class="text-charcoal/60 mb-6"><?= e($paymentError) ?></p>
            <a href="<?= $base ?>/loja/checkout/" class="inline-flex items-center px-6 py-3 bg-secondary text-white rounded-full hover:bg-secondary-700 transition-all">
                <?= $isEnglish ? 'Try Again' : 'Tentar Novamente' ?>
            </a>
        </div>

        <?php else: ?>

        <!-- Success Header -->
        <div class="text-center mb-10">
            <div class="w-20 h-20 bg-secondary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="font-serif text-3xl md:text-4xl text-primary mb-2"><?= $isEnglish ? 'Order Placed!' : 'Encomenda Registada!' ?></h1>
            <p class="text-charcoal/60"><?= $isEnglish ? 'Order' : 'Encomenda' ?> <strong class="text-primary">#<?= e($orderNumber) ?></strong></p>
        </div>

        <div class="grid lg:grid-cols-5 gap-8">
            <!-- Payment Instructions (Left - 3 cols) -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-3xl shadow-sm border border-cream-100 overflow-hidden">

                    <?php if ($paymentMethod === 'mbway'): ?>
                    <!-- MBWay Payment -->
                    <div class="bg-primary/5 px-8 py-5 border-b border-cream-100">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="font-serif text-xl text-primary">MB WAY</h2>
                                <p class="text-sm text-charcoal/60"><?= $isEnglish ? 'Confirm on your phone' : 'Confirme no telemóvel' ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <div class="bg-cream-50 rounded-2xl p-6 mb-6 text-center">
                            <p class="text-sm text-charcoal/60 mb-2"><?= $isEnglish ? 'Payment request sent to' : 'Pedido de pagamento enviado para' ?></p>
                            <p class="text-2xl font-bold text-primary tracking-wider"><?= e($customerPhone) ?></p>
                            <p class="text-2xl font-bold text-accent mt-2"><?= formatPrice($totalAmount) ?></p>
                        </div>

                        <div class="flex items-center justify-center gap-3 mb-6" id="mbway-status">
                            <div class="animate-spin w-5 h-5 border-2 border-secondary border-t-transparent rounded-full"></div>
                            <span class="text-charcoal/60"><?= $isEnglish ? 'Waiting for confirmation...' : 'A aguardar confirmação...' ?></span>
                        </div>

                        <div class="space-y-3 text-sm text-charcoal/60">
                            <div class="flex items-start gap-3">
                                <svg class="w-4 h-4 mt-0.5 text-accent flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span><?= $isEnglish ? 'The request expires in 5 minutes' : 'O pedido expira em 5 minutos' ?></span>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="w-4 h-4 mt-0.5 text-accent flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span><?= $isEnglish ? 'You will receive a confirmation email after payment' : 'Receberá um email de confirmação após o pagamento' ?></span>
                            </div>
                        </div>
                    </div>

                    <?php elseif ($paymentMethod === 'multibanco'): ?>
                    <!-- Multibanco Payment -->
                    <div class="bg-primary/5 px-8 py-5 border-b border-cream-100">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="font-serif text-xl text-primary">Multibanco</h2>
                                <p class="text-sm text-charcoal/60"><?= $isEnglish ? 'Pay at ATM or home banking' : 'Pague no multibanco ou homebanking' ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <div class="bg-cream-50 rounded-2xl p-6 mb-6 space-y-4">
                            <div class="flex justify-between items-center pb-3 border-b border-cream-200">
                                <span class="text-charcoal/60 text-sm uppercase tracking-wider"><?= $isEnglish ? 'Entity' : 'Entidade' ?></span>
                                <span class="text-xl font-bold text-primary tracking-wider"><?= e($paymentData['entity']) ?></span>
                            </div>
                            <div class="flex justify-between items-center pb-3 border-b border-cream-200">
                                <span class="text-charcoal/60 text-sm uppercase tracking-wider"><?= $isEnglish ? 'Reference' : 'Referência' ?></span>
                                <span class="text-xl font-bold text-primary tracking-wider"><?= e($paymentData['reference']) ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-charcoal/60 text-sm uppercase tracking-wider"><?= $isEnglish ? 'Amount' : 'Montante' ?></span>
                                <span class="text-xl font-bold text-accent"><?= formatPrice($totalAmount) ?></span>
                            </div>
                        </div>

                        <div class="bg-accent/10 rounded-2xl p-4 mb-6 border border-accent/20">
                            <p class="text-sm text-charcoal/80">
                                <strong><?= $isEnglish ? 'Important:' : 'Importante:' ?></strong>
                                <?= $isEnglish
                                    ? 'Your order will only be processed after payment confirmation.'
                                    : 'A sua encomenda só será processada após a confirmação do pagamento.' ?>
                            </p>
                        </div>

                        <div class="space-y-3 text-sm text-charcoal/60">
                            <div class="flex items-start gap-3">
                                <svg class="w-4 h-4 mt-0.5 text-secondary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span><?= $isEnglish ? 'Pay at any ATM or via home banking' : 'Pague em qualquer multibanco ou homebanking' ?></span>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="w-4 h-4 mt-0.5 text-secondary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span><?= $isEnglish ? 'Reference valid for 3 days' : 'Referência válida por 3 dias' ?></span>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="w-4 h-4 mt-0.5 text-secondary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span><?= $isEnglish ? 'Confirmation email sent after payment' : 'Email de confirmação enviado após pagamento' ?></span>
                            </div>
                        </div>
                    </div>

                    <?php elseif ($paymentMethod === 'card'): ?>
                    <!-- Card Payment (fallback if no redirect) -->
                    <div class="bg-primary/5 px-8 py-5 border-b border-cream-100">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="font-serif text-xl text-primary"><?= $isEnglish ? 'Card Payment' : 'Pagamento por Cartão' ?></h2>
                                <p class="text-sm text-charcoal/60">Visa / Mastercard</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8 text-center">
                        <p class="text-charcoal/60 mb-6">
                            <?= $isEnglish
                                ? 'You will be redirected to the secure payment page.'
                                : 'Será redirecionado para a página de pagamento seguro.' ?>
                        </p>
                        <p class="text-2xl font-bold text-accent mb-6"><?= formatPrice($totalAmount) ?></p>

                        <div class="flex items-center justify-center gap-2 text-sm text-charcoal/40">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span><?= $isEnglish ? '100% secure and encrypted payment' : 'Pagamento 100% seguro e encriptado' ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Order Summary (Right - 2 cols) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl shadow-sm border border-cream-100 p-6 sticky top-32">
                    <h3 class="font-serif text-lg text-primary mb-4"><?= $isEnglish ? 'Order Summary' : 'Resumo' ?></h3>

                    <div class="space-y-3 mb-4">
                        <?php foreach ($orderItems as $item): ?>
                        <div class="flex justify-between text-sm">
                            <span class="text-charcoal/70 truncate mr-2"><?= e($item['display_name']) ?> <span class="text-charcoal/40">x<?= (int)$item['quantity'] ?></span></span>
                            <span class="font-medium text-primary whitespace-nowrap"><?= formatPrice(($item['total_price'] ?? $item['subtotal'] ?? 0)) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="border-t border-cream-100 pt-3 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-charcoal/60">Subtotal</span>
                            <span class="text-primary"><?= formatPrice($order['subtotal']) ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-charcoal/60"><?= $isEnglish ? 'Shipping' : 'Envio' ?></span>
                            <span class="text-primary">
                                <?php $shippingFee = (float)($order['shipping_fee'] ?? $order['shipping'] ?? 0); ?>
                                <?= $shippingFee > 0 ? formatPrice($shippingFee) : '<span class="text-secondary">' . ($isEnglish ? 'Free' : 'Grátis') . '</span>' ?>
                            </span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t border-cream-100 pt-2">
                            <span class="text-primary">Total</span>
                            <span class="text-primary"><?= formatPrice($totalAmount) ?></span>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="mt-6 pt-4 border-t border-cream-100">
                        <p class="text-xs text-charcoal/40 uppercase tracking-wider mb-2"><?= $isEnglish ? 'Delivery' : 'Envio para' ?></p>
                        <p class="text-sm text-charcoal/70"><?= e($order['customer_name']) ?></p>
                        <p class="text-sm text-charcoal/50"><?= e($order['shipping_address'] ?? '') ?></p>
                        <p class="text-sm text-charcoal/50"><?= e($order['shipping_postal_code'] ?? '') ?> <?= e($order['shipping_city'] ?? '') ?></p>
                    </div>

                    <div class="mt-6">
                        <a href="<?= $base ?>/" class="block text-center text-sm text-secondary hover:text-secondary-700 transition-colors">
                            <?= $isEnglish ? 'Back to Home' : 'Voltar ao Início' ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?php endif; ?>
    </div>
</section>

<?php if ($paymentMethod === 'mbway' && !$paymentError): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const orderId = <?= $orderId ?>;
    const base = '<?= $base ?>';
    const statusEl = document.getElementById('mbway-status');
    let attempts = 0;
    const maxAttempts = 60; // 5 minutes at 5s intervals

    const pollInterval = setInterval(function() {
        attempts++;

        fetch(base + '/api/check-payment-status.php?order_id=' + orderId, {
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'paid') {
                clearInterval(pollInterval);
                if (statusEl) {
                    statusEl.innerHTML = '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-green-600 font-medium"><?= $isEnglish ? "Payment confirmed!" : "Pagamento confirmado!" ?></span>';
                }
                setTimeout(() => {
                    window.location.href = base + '/loja/checkout/confirmacao/?order=' + orderId;
                }, 1500);
            } else if (data.status === 'failed' || data.status === 'expired') {
                clearInterval(pollInterval);
                if (statusEl) {
                    statusEl.innerHTML = '<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg><span class="text-red-600"><?= $isEnglish ? "Payment expired or cancelled." : "Pagamento expirado ou cancelado." ?></span>';
                }
            }
        })
        .catch(err => console.error('Poll error:', err));

        if (attempts >= maxAttempts) {
            clearInterval(pollInterval);
            if (statusEl) {
                statusEl.innerHTML = '<span class="text-charcoal/60"><?= $isEnglish ? "Timeout. Check your MB WAY app." : "Tempo esgotado. Verifique a app MB WAY." ?></span>';
            }
        }
    }, 5000);
});
</script>
<?php endif; ?>

<?php include INCLUDES_PATH . '/footer.php'; ?>
