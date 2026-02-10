<?php
/**
 * A Casa do Gi - Order Confirmation Page
 */

require_once dirname(dirname(dirname(__DIR__))) . '/includes/init.php';

use Core\Database;
use Core\Language;
use Core\Session;
use Core\Invoice;

$db = Database::getInstance();
$lang = Language::getInstance();
$base = basePath();
$isEnglish = $lang->isEnglish();

// Rate limit: prevent brute force enumeration of order IDs
$rateLimiter = \Core\RateLimiter::getInstance();
if (!$rateLimiter->check('confirmation_page', 15, 60)) {
    Session::flash('error', $isEnglish ? 'Too many requests. Please wait.' : 'Demasiados pedidos. Aguarde um momento.');
    redirect($base . ($isEnglish ? '/en/shop/' : '/loja/'));
}

// Get order from session first (secure), then fallback to URL with session validation
$pendingOrder = Session::get('pending_order') ?? Session::get('checkout_order');
$orderId = 0;

if ($pendingOrder) {
    $orderId = (int)($pendingOrder['id'] ?? $pendingOrder['order_id'] ?? 0);
}

// If order ID from URL, verify it belongs to the current session
if (!$orderId && (isset($_GET['order']) || isset($_GET['order_id']))) {
    $requestedId = (int)($_GET['order'] ?? $_GET['order_id'] ?? 0);
    // Only allow if the session knows about this order
    if ($pendingOrder && (int)($pendingOrder['id'] ?? $pendingOrder['order_id'] ?? 0) === $requestedId) {
        $orderId = $requestedId;
    } else {
        // Log suspicious access attempt
        logMessage("Confirmation page direct access attempt: order_id={$requestedId} from " . getClientIp(), 'warning');
        $rateLimiter->recordFailure('confirmation_brute_force');

        // Check for repeated failures - progressive blocking
        if ($rateLimiter->getFailureCount('confirmation_brute_force', 300) > 10) {
            http_response_code(403);
            exit;
        }

        Session::flash('error', $isEnglish ? 'Please complete the checkout process first.' : 'Por favor complete o processo de checkout primeiro.');
        redirect($base . ($isEnglish ? '/en/shop/' : '/loja/'));
    }
}

if (!$orderId) {
    redirect($base . ($isEnglish ? '/en/shop/' : '/loja/'));
}

// Get order
$order = $db->fetch("SELECT * FROM orders WHERE id = ?", [$orderId]);
if (!$order) {
    Session::flash('error', $isEnglish ? 'Order not found.' : 'Encomenda não encontrada.');
    redirect($base . ($isEnglish ? '/en/shop/' : '/loja/'));
}

// Get order items
$orderItems = $db->fetchAll(
    "SELECT oi.*, COALESCE(pt.name, oi.product_name) as display_name
     FROM order_items oi
     LEFT JOIN product_translations pt ON oi.product_id = pt.product_id AND pt.language_id = 1
     WHERE oi.order_id = ?",
    [$orderId]
);

// Get invoice if exists
$invoice = $db->fetch("SELECT * FROM invoices WHERE order_id = ?", [$orderId]);

// If payment is confirmed and no invoice yet, generate one
if ($order['payment_status'] === 'paid' && !$invoice) {
    try {
        $invoiceSystem = Invoice::getInstance();
        $invoice = $invoiceSystem->generate($orderId);
        if ($invoice) {
            $invoiceSystem->markAsPaid($invoice['id']);
            $invoiceSystem->sendEmail($invoice['id']);
        }
    } catch (\Exception $e) {
        logMessage("Confirmation page invoice error: " . $e->getMessage(), 'error');
    }
}

// Clear pending order from session
Session::delete('pending_order');
Session::delete('checkout_order');

$isPaid = $order['payment_status'] === 'paid';

$statusLabels = [
    'pending' => $isEnglish ? 'Pending' : 'Pendente',
    'paid' => $isEnglish ? 'Paid' : 'Pago',
    'processing' => $isEnglish ? 'Processing' : 'Em Processamento',
    'failed' => $isEnglish ? 'Failed' : 'Falhado',
    'refunded' => $isEnglish ? 'Refunded' : 'Reembolsado',
];

$paymentLabels = [
    'mbway' => 'MB WAY',
    'multibanco' => 'Multibanco',
    'card' => $isEnglish ? 'Card' : 'Cartao',
];

$pageTitle = $isEnglish ? 'Order Confirmation' : 'Confirmacao da Encomenda';
$pageDescription = $isEnglish ? 'Your order has been placed' : 'A sua encomenda foi registada';

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
            <li class="text-primary font-medium"><?= $isEnglish ? 'Confirmation' : 'Confirmação' ?></li>
        </ol>
    </div>
</nav>

<section class="py-12 lg:py-16 bg-cream-50 min-h-[60vh]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Status Header -->
        <div class="text-center mb-10">
            <?php if ($isPaid): ?>
            <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="font-serif text-3xl md:text-4xl text-primary mb-2"><?= $isEnglish ? 'Payment Confirmed!' : 'Pagamento Confirmado!' ?></h1>
            <p class="text-charcoal/60"><?= $isEnglish ? 'Thank you for your purchase' : 'Obrigado pela sua compra' ?></p>
            <?php else: ?>
            <div class="w-20 h-20 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="font-serif text-3xl md:text-4xl text-primary mb-2"><?= $isEnglish ? 'Order Registered' : 'Encomenda Registada' ?></h1>
            <p class="text-charcoal/60"><?= $isEnglish ? 'Awaiting payment confirmation' : 'A aguardar confirmação de pagamento' ?></p>
            <?php endif; ?>
        </div>

        <!-- Order Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-cream-100 overflow-hidden mb-8">
            <!-- Order Header -->
            <div class="bg-primary px-8 py-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div>
                        <h2 class="font-serif text-xl text-cream"><?= $isEnglish ? 'Order' : 'Encomenda' ?> #<?= e($order['order_number']) ?></h2>
                        <p class="text-cream/60 text-sm"><?= formatDateTime($order['created_at']) ?></p>
                    </div>
                    <span class="inline-flex px-4 py-1.5 text-xs font-bold uppercase tracking-wider rounded-full <?= $isPaid ? 'bg-green-500 text-white' : 'bg-accent text-white' ?>">
                        <?= $statusLabels[$order['payment_status']] ?? ucfirst($order['payment_status']) ?>
                    </span>
                </div>
            </div>

            <div class="p-8">
                <!-- Invoice Codes -->
                <?php if ($invoice): ?>
                <div class="bg-cream-50 rounded-2xl p-5 mb-8 border border-cream-200">
                    <div class="flex flex-col sm:flex-row justify-between gap-4">
                        <div>
                            <p class="text-xs text-charcoal/40 uppercase tracking-wider mb-1"><?= $isEnglish ? 'Invoice Barcode' : 'Código de Barras da Fatura' ?></p>
                            <p class="text-2xl font-bold text-primary font-mono tracking-widest">
                                <?= substr($invoice['barcode'], 0, 3) ?> <?= substr($invoice['barcode'], 3, 3) ?> <?= substr($invoice['barcode'], 6, 3) ?>
                            </p>
                        </div>
                        <div class="sm:text-right">
                            <p class="text-xs text-charcoal/40 uppercase tracking-wider mb-1">UUID</p>
                            <p class="text-xs text-charcoal/50 font-mono break-all"><?= e($invoice['invoice_uuid']) ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Order Items -->
                <h3 class="font-serif text-lg text-primary mb-4 pb-2 border-b border-cream-100"><?= $isEnglish ? 'Products' : 'Produtos' ?></h3>
                <div class="space-y-3 mb-6">
                    <?php foreach ($orderItems as $item): ?>
                    <div class="flex justify-between items-center py-2 border-b border-cream-50">
                        <div>
                            <p class="font-medium text-primary text-sm"><?= e($item['display_name']) ?></p>
                            <p class="text-xs text-charcoal/50"><?= $isEnglish ? 'Qty' : 'Qtd' ?>: <?= (int)$item['quantity'] ?> x <?= formatPrice($item['unit_price'] ?? $item['price'] ?? 0) ?></p>
                        </div>
                        <p class="font-medium text-primary text-sm"><?= formatPrice($item['total_price'] ?? $item['subtotal'] ?? 0) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Totals -->
                <div class="space-y-2 mb-8">
                    <div class="flex justify-between text-sm text-charcoal/60">
                        <span>Subtotal</span>
                        <span><?= formatPrice($order['subtotal']) ?></span>
                    </div>
                    <?php $shippingFee = (float)($order['shipping_fee'] ?? $order['shipping'] ?? 0); ?>
                    <div class="flex justify-between text-sm text-charcoal/60">
                        <span><?= $isEnglish ? 'Shipping' : 'Envio' ?></span>
                        <span><?= $shippingFee > 0 ? formatPrice($shippingFee) : '<span class="text-secondary">' . ($isEnglish ? 'Free' : 'Grátis') . '</span>' ?></span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-primary border-t border-cream-200 pt-2">
                        <span>Total</span>
                        <span><?= formatPrice($order['total']) ?></span>
                    </div>
                </div>

                <!-- Customer & Shipping Info -->
                <div class="grid md:grid-cols-2 gap-6 border-t border-cream-100 pt-6">
                    <div>
                        <h4 class="text-xs text-charcoal/40 uppercase tracking-wider mb-3"><?= $isEnglish ? 'Customer' : 'Cliente' ?></h4>
                        <p class="text-sm text-charcoal/70"><?= e($order['customer_name']) ?></p>
                        <p class="text-sm text-charcoal/50"><?= e($order['customer_email']) ?></p>
                        <p class="text-sm text-charcoal/50"><?= e($order['customer_phone']) ?></p>
                    </div>
                    <div>
                        <h4 class="text-xs text-charcoal/40 uppercase tracking-wider mb-3"><?= $isEnglish ? 'Shipping Address' : 'Morada de Envio' ?></h4>
                        <p class="text-sm text-charcoal/70"><?= e($order['shipping_address'] ?? '') ?></p>
                        <p class="text-sm text-charcoal/50"><?= e($order['shipping_postal_code'] ?? '') ?> <?= e($order['shipping_city'] ?? '') ?></p>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="border-t border-cream-100 pt-4 mt-4">
                    <p class="text-xs text-charcoal/40 uppercase tracking-wider mb-1"><?= $isEnglish ? 'Payment Method' : 'Método de Pagamento' ?></p>
                    <p class="text-sm text-charcoal/70"><?= $paymentLabels[$order['payment_method']] ?? ucfirst($order['payment_method']) ?></p>
                </div>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="bg-secondary/5 rounded-3xl border border-secondary/10 p-6 mb-8">
            <h3 class="font-serif text-lg text-primary mb-4"><?= $isEnglish ? 'Next Steps' : 'Próximos Passos' ?></h3>
            <ul class="space-y-3 text-sm text-charcoal/70">
                <?php if ($isPaid): ?>
                <li class="flex items-start gap-3">
                    <svg class="w-4 h-4 mt-0.5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <?= $isEnglish ? 'Your order will be processed soon' : 'A sua encomenda será processada em breve' ?>
                </li>
                <li class="flex items-start gap-3">
                    <svg class="w-4 h-4 mt-0.5 text-secondary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <?= $isEnglish ? 'You will receive an email when shipped' : 'Receberá um email quando a encomenda for enviada' ?>
                </li>
                <li class="flex items-start gap-3">
                    <svg class="w-4 h-4 mt-0.5 text-secondary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?= $isEnglish ? 'Estimated delivery: 3-5 business days' : 'Entrega estimada: 3-5 dias úteis' ?>
                </li>
                <?php if ($invoice): ?>
                <li class="flex items-start gap-3">
                    <svg class="w-4 h-4 mt-0.5 text-secondary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <?= $isEnglish ? 'Invoice sent to' : 'Fatura enviada para' ?> <strong><?= e($order['customer_email']) ?></strong>
                </li>
                <?php endif; ?>
                <?php else: ?>
                <li class="flex items-start gap-3">
                    <svg class="w-4 h-4 mt-0.5 text-accent flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?= $isEnglish ? 'Awaiting payment confirmation' : 'A aguardar confirmação de pagamento' ?>
                </li>
                <li class="flex items-start gap-3">
                    <svg class="w-4 h-4 mt-0.5 text-secondary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <?= $isEnglish ? 'Confirmation email will be sent after payment' : 'Email de confirmação será enviado após pagamento' ?>
                </li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?= $base ?>/" class="inline-flex items-center justify-center px-8 py-3 bg-primary text-white font-medium rounded-full hover:bg-primary-700 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                <?= $isEnglish ? 'Back to Home' : 'Voltar ao Início' ?>
            </a>
            <a href="<?= $base ?>/loja/" class="inline-flex items-center justify-center px-8 py-3 bg-white text-primary font-medium rounded-full border border-cream-200 hover:bg-cream-50 transition-all">
                <?= $isEnglish ? 'Continue Shopping' : 'Continuar a Comprar' ?>
            </a>
        </div>

        <!-- Support -->
        <div class="text-center mt-10 text-sm text-charcoal/40">
            <p><?= $isEnglish ? 'Need help?' : 'Precisa de ajuda?' ?></p>
            <p class="mt-1">
                <a href="mailto:<?= e(setting('contact_email', 'info@acasadogi.pt')) ?>" class="text-secondary hover:text-secondary-700 transition-colors">
                    <?= e(setting('contact_email', 'info@acasadogi.pt')) ?>
                </a>
                <?php $phone = setting('contact_phone', ''); if ($phone): ?>
                <span class="mx-2">|</span>
                <a href="tel:<?= e($phone) ?>" class="text-secondary hover:text-secondary-700 transition-colors"><?= e($phone) ?></a>
                <?php endif; ?>
            </p>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
