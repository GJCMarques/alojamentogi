<?php
/**
 * A Casa do Gi - Manual Order Page
 * When shop is in "manual" mode, customers submit their cart
 * and payment is handled manually by phone.
 */

require_once dirname(dirname(dirname(__DIR__))) . '/includes/init.php';

use Core\Cart;
use Core\CSRF;
use Core\Database;
use Core\Session;
use Core\Language;

$cart = Cart::getInstance();
$db = Database::getInstance();
$lang = Language::getInstance();
$base = basePath();
$isEnglish = $lang->isEnglish();

// If shop is not in manual mode, redirect to normal checkout
$shopMode = setting('shop_mode', 'active');
if ($shopMode !== 'manual') {
    redirect($base . '/loja/checkout/');
}

// Redirect if cart is empty
if ($cart->isEmpty()) {
    Session::flash('warning', $isEnglish ? 'Your cart is empty' : 'O seu carrinho esta vazio');
    redirect($base . ($isEnglish ? '/en/shop/' : '/loja/'));
}

$cartItems = $cart->getItems();
$cartSubtotal = $cart->getSubtotal();
$shippingCost = (float) setting('shipping_cost', 5);
$freeShippingThreshold = (float) setting('free_shipping_threshold', 50);
$shipping = $cartSubtotal >= $freeShippingThreshold ? 0 : $shippingCost;
$cartTotal = $cartSubtotal + $shipping;

$errors = [];
$formData = [
    'customer_name' => '',
    'customer_email' => '',
    'customer_phone' => '',
    'shipping_address' => '',
    'shipping_city' => '',
    'shipping_postal_code' => '',
    'notes' => '',
];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::check();

    $formData = [
        'customer_name' => sanitize(post('customer_name', '')),
        'customer_email' => sanitizeEmail(post('customer_email', '')),
        'customer_phone' => sanitize(post('customer_phone', '')),
        'shipping_address' => sanitize(post('shipping_address', '')),
        'shipping_city' => sanitize(post('shipping_city', '')),
        'shipping_postal_code' => sanitize(post('shipping_postal_code', '')),
        'notes' => sanitize(post('notes', '')),
    ];

    // Validation
    if (empty($formData['customer_name'])) $errors['customer_name'] = $isEnglish ? 'Name is required' : 'Nome e obrigatorio';
    if (!isValidEmail($formData['customer_email'])) $errors['customer_email'] = $isEnglish ? 'Valid email is required' : 'Email valido e obrigatorio';
    if (empty($formData['customer_phone'])) $errors['customer_phone'] = $isEnglish ? 'Phone is required' : 'Telefone e obrigatorio';

    if (empty($errors)) {
        // Build items snapshot
        $itemsSnapshot = array_map(function ($item) {
            return [
                'product_id' => $item['product']->id,
                'product_name' => $item['product']->name,
                'product_sku' => $item['product']->sku ?? '',
                'quantity' => (int) $item['quantity'],
                'unit_price' => (float) $item['product']->getCurrentPrice(),
                'subtotal' => (float) $item['subtotal'],
            ];
        }, $cartItems);

        try {
            $db->insert('manual_orders', [
                'customer_name' => $formData['customer_name'],
                'customer_email' => $formData['customer_email'],
                'customer_phone' => $formData['customer_phone'],
                'shipping_address' => $formData['shipping_address'] ?: null,
                'shipping_postal_code' => $formData['shipping_postal_code'] ?: null,
                'shipping_city' => $formData['shipping_city'] ?: null,
                'items_json' => json_encode($itemsSnapshot, JSON_UNESCAPED_UNICODE),
                'subtotal' => $cartSubtotal,
                'shipping_fee' => $shipping,
                'total' => $cartTotal,
                'status' => 'new',
                'notes' => $formData['notes'] ?: null,
                'ip_address' => getClientIp(),
                'user_agent' => substr(getUserAgent(), 0, 500),
            ]);

            // Clear cart
            $cart->clear();
            $success = true;

            // Send notification to admin
            try {
                $mailer = new \Core\Mailer();
                $adminEmail = setting('contact_email', '');
                if ($adminEmail) {
                    $itemsList = '';
                    foreach ($itemsSnapshot as $item) {
                        $itemsList .= "- {$item['product_name']} x{$item['quantity']} (" . number_format($item['subtotal'], 2) . " EUR)\n";
                    }

                    $body = "<h2>Novo Pedido Manual</h2>
                        <p><strong>Cliente:</strong> {$formData['customer_name']}</p>
                        <p><strong>Email:</strong> {$formData['customer_email']}</p>
                        <p><strong>Telefone:</strong> {$formData['customer_phone']}</p>
                        <p><strong>Total:</strong> " . number_format($cartTotal, 2) . " EUR</p>
                        <h3>Produtos:</h3><pre>{$itemsList}</pre>
                        " . ($formData['notes'] ? "<p><strong>Notas:</strong> {$formData['notes']}</p>" : '') . "
                        <p><em>Este pedido requer contacto telefonico para finalizar o pagamento.</em></p>";

                    $mailer->send($adminEmail, 'Novo Pedido Manual - A Casa do Gi', $body);
                }
            } catch (\Exception $e) {
                logMessage("Manual order email error: " . $e->getMessage(), 'error');
            }

        } catch (\Exception $e) {
            logMessage("Manual order error: " . $e->getMessage(), 'error');
            $errors['general'] = $isEnglish ? 'An error occurred. Please try again.' : 'Ocorreu um erro. Por favor tente novamente.';
        }
    }
}

$pageTitle = $isEnglish ? 'Order Request' : 'Pedido de Encomenda';
$pageDescription = $isEnglish ? 'Submit your order request' : 'Envie o seu pedido de encomenda';

include INCLUDES_PATH . '/header.php';
?>

<!-- Breadcrumb -->
<nav class="bg-cream-200 py-3 border-b border-cream-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <ol class="flex items-center text-sm text-charcoal/60">
            <li><a href="<?= $base ?>/" class="hover:text-secondary"><?= $isEnglish ? 'Home' : 'Inicio' ?></a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="<?= $base ?>/loja/" class="hover:text-secondary"><?= $isEnglish ? 'Shop' : 'Loja' ?></a></li>
            <li><span class="mx-2">/</span></li>
            <li class="text-primary font-medium"><?= $isEnglish ? 'Order Request' : 'Pedido' ?></li>
        </ol>
    </div>
</nav>

<section class="py-12 lg:py-16 bg-cream-50 min-h-[60vh]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <?php if ($success): ?>
        <!-- Success State -->
        <div class="bg-white rounded-3xl shadow-sm border border-cream-100 p-10 text-center">
            <div class="w-20 h-20 bg-secondary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="font-serif text-3xl text-primary mb-4"><?= $isEnglish ? 'Request Submitted!' : 'Pedido Enviado!' ?></h1>
            <p class="text-charcoal/60 max-w-lg mx-auto mb-4">
                <?= $isEnglish
                    ? 'Your order request has been sent. We will contact you by phone to finalize the payment and confirm the order.'
                    : 'O seu pedido de encomenda foi enviado. Iremos contacta-lo por telefone para finalizar o pagamento e confirmar a encomenda.' ?>
            </p>
            <p class="text-charcoal/60 max-w-lg mx-auto mb-8">
                <?= $isEnglish
                    ? 'Please keep your phone available. We will contact you within 24 hours.'
                    : 'Por favor mantenha o telefone disponivel. Iremos contacta-lo dentro de 24 horas.' ?>
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?= $base ?>/" class="inline-flex items-center justify-center px-8 py-3 bg-primary text-white font-medium rounded-full hover:bg-primary-700 transition-all">
                    <?= $isEnglish ? 'Back to Home' : 'Voltar ao Inicio' ?>
                </a>
                <a href="<?= $base ?>/loja/" class="inline-flex items-center justify-center px-8 py-3 bg-white text-primary font-medium rounded-full border border-cream-200 hover:bg-cream-50 transition-all">
                    <?= $isEnglish ? 'Continue Shopping' : 'Continuar a Comprar' ?>
                </a>
            </div>
        </div>

        <?php else: ?>

        <!-- Manual Order Info -->
        <div class="bg-accent/10 rounded-3xl border border-accent/20 p-6 mb-8">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 bg-accent/20 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-serif text-lg text-primary mb-2"><?= $isEnglish ? 'Manual Payment Mode' : 'Modo de Pagamento Manual' ?></h3>
                    <p class="text-sm text-charcoal/70">
                        <?= $isEnglish
                            ? 'Online payment is currently not available. Submit your order and we will contact you by phone to arrange payment.'
                            : 'O pagamento online nao esta disponivel de momento. Envie o seu pedido e iremos contacta-lo por telefone para finalizar o pagamento.' ?>
                    </p>
                </div>
            </div>
        </div>

        <h1 class="font-serif text-3xl text-primary mb-8"><?= $isEnglish ? 'Order Request' : 'Pedido de Encomenda' ?></h1>

        <?php if (!empty($errors['general'])): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm">
            <?= e($errors['general']) ?>
        </div>
        <?php endif; ?>

        <form method="POST" class="lg:grid lg:grid-cols-3 lg:gap-8">
            <?= CSRF::tokenField() ?>

            <div class="lg:col-span-2 space-y-6">
                <!-- Contact Info -->
                <div class="bg-white rounded-3xl shadow-sm border border-cream-100 p-6">
                    <h2 class="font-serif text-xl text-primary mb-6"><?= $isEnglish ? 'Contact Information' : 'Informacoes de Contacto' ?></h2>

                    <div class="grid md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-charcoal/70 mb-2"><?= $isEnglish ? 'Full Name' : 'Nome Completo' ?> *</label>
                            <input type="text" name="customer_name" value="<?= e($formData['customer_name']) ?>" required
                                   class="w-full px-4 py-3 border <?= isset($errors['customer_name']) ? 'border-red-400' : 'border-cream-200' ?> rounded-xl focus:ring-2 focus:ring-secondary/20 focus:border-secondary bg-cream-50 outline-none text-sm">
                            <?php if (isset($errors['customer_name'])): ?>
                            <p class="mt-1 text-xs text-red-500"><?= e($errors['customer_name']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-charcoal/70 mb-2">Email *</label>
                            <input type="email" name="customer_email" value="<?= e($formData['customer_email']) ?>" required
                                   class="w-full px-4 py-3 border <?= isset($errors['customer_email']) ? 'border-red-400' : 'border-cream-200' ?> rounded-xl focus:ring-2 focus:ring-secondary/20 focus:border-secondary bg-cream-50 outline-none text-sm">
                            <?php if (isset($errors['customer_email'])): ?>
                            <p class="mt-1 text-xs text-red-500"><?= e($errors['customer_email']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-charcoal/70 mb-2"><?= $isEnglish ? 'Phone' : 'Telefone' ?> *</label>
                            <input type="tel" name="customer_phone" value="<?= e($formData['customer_phone']) ?>" required placeholder="9XX XXX XXX"
                                   class="w-full px-4 py-3 border <?= isset($errors['customer_phone']) ? 'border-red-400' : 'border-cream-200' ?> rounded-xl focus:ring-2 focus:ring-secondary/20 focus:border-secondary bg-cream-50 outline-none text-sm">
                            <?php if (isset($errors['customer_phone'])): ?>
                            <p class="mt-1 text-xs text-red-500"><?= e($errors['customer_phone']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Shipping (optional for manual orders) -->
                <div class="bg-white rounded-3xl shadow-sm border border-cream-100 p-6">
                    <h2 class="font-serif text-xl text-primary mb-2"><?= $isEnglish ? 'Shipping Address' : 'Morada de Envio' ?></h2>
                    <p class="text-xs text-charcoal/40 mb-6"><?= $isEnglish ? '(Optional - can be provided later by phone)' : '(Opcional - pode ser fornecida por telefone)' ?></p>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-charcoal/70 mb-2"><?= $isEnglish ? 'Address' : 'Morada' ?></label>
                            <input type="text" name="shipping_address" value="<?= e($formData['shipping_address']) ?>"
                                   class="w-full px-4 py-3 border border-cream-200 rounded-xl focus:ring-2 focus:ring-secondary/20 focus:border-secondary bg-cream-50 outline-none text-sm">
                        </div>
                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-charcoal/70 mb-2"><?= $isEnglish ? 'City' : 'Cidade' ?></label>
                                <input type="text" name="shipping_city" value="<?= e($formData['shipping_city']) ?>"
                                       class="w-full px-4 py-3 border border-cream-200 rounded-xl focus:ring-2 focus:ring-secondary/20 focus:border-secondary bg-cream-50 outline-none text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-charcoal/70 mb-2"><?= $isEnglish ? 'Postal Code' : 'Codigo Postal' ?></label>
                                <input type="text" name="shipping_postal_code" value="<?= e($formData['shipping_postal_code']) ?>" placeholder="XXXX-XXX"
                                       class="w-full px-4 py-3 border border-cream-200 rounded-xl focus:ring-2 focus:ring-secondary/20 focus:border-secondary bg-cream-50 outline-none text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-charcoal/70 mb-2"><?= $isEnglish ? 'Notes' : 'Notas' ?></label>
                            <textarea name="notes" rows="3"
                                      class="w-full px-4 py-3 border border-cream-200 rounded-xl focus:ring-2 focus:ring-secondary/20 focus:border-secondary bg-cream-50 outline-none text-sm resize-none"><?= e($formData['notes']) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="lg:col-span-1 mt-6 lg:mt-0">
                <div class="bg-white rounded-3xl shadow-sm border border-cream-100 p-6 sticky top-32">
                    <h3 class="font-serif text-lg text-primary mb-4"><?= $isEnglish ? 'Your Cart' : 'Seu Carrinho' ?></h3>

                    <div class="space-y-3 mb-4">
                        <?php foreach ($cartItems as $item): ?>
                        <div class="flex justify-between text-sm">
                            <span class="text-charcoal/70 truncate mr-2"><?= e($item['product']->name) ?> <span class="text-charcoal/40">x<?= $item['quantity'] ?></span></span>
                            <span class="font-medium text-primary whitespace-nowrap"><?= formatPrice($item['subtotal']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="border-t border-cream-100 pt-3 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-charcoal/60">Subtotal</span>
                            <span><?= formatPrice($cartSubtotal) ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-charcoal/60"><?= $isEnglish ? 'Shipping' : 'Envio' ?></span>
                            <span><?= $shipping > 0 ? formatPrice($shipping) : '<span class="text-secondary">' . ($isEnglish ? 'Free' : 'Gratis') . '</span>' ?></span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t border-cream-100 pt-2">
                            <span class="text-primary">Total</span>
                            <span class="text-primary"><?= formatPrice($cartTotal) ?></span>
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-6 py-3.5 bg-secondary text-white font-medium rounded-full hover:bg-secondary-700 transition-all shadow-lg hover:shadow-xl">
                        <?= $isEnglish ? 'Submit Order Request' : 'Enviar Pedido' ?>
                    </button>

                    <p class="text-[11px] text-charcoal/40 text-center mt-3">
                        <?= $isEnglish
                            ? 'We will contact you by phone to finalize payment.'
                            : 'Iremos contacta-lo por telefone para finalizar o pagamento.' ?>
                    </p>
                </div>
            </div>
        </form>
        <?php endif; ?>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
