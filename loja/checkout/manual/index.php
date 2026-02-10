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
    if (empty($formData['shipping_address'])) $errors['shipping_address'] = $isEnglish ? 'Address is required' : 'Morada e obrigatoria';
    if (empty($formData['shipping_city'])) $errors['shipping_city'] = $isEnglish ? 'City is required' : 'Cidade e obrigatoria';
    if (empty($formData['shipping_postal_code'])) $errors['shipping_postal_code'] = $isEnglish ? 'Postal code is required' : 'Codigo postal e obrigatorio';

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

// Get hero image from database (checkout hero, fallback to shop)
$checkoutHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = 'checkout' AND is_active = 1");
if (!$checkoutHero) {
    $checkoutHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = 'shop' AND is_active = 1");
}
$heroMedia = $checkoutHero ? $db->fetch("SELECT * FROM media WHERE entity_type = 'hero' AND entity_id = ? AND is_cover = 1", [$checkoutHero['id']]) : null;
$heroImage = $heroMedia['file_path'] ?? 'images/MogadouroNeve.jpeg';
$heroOverlay = $checkoutHero['hero_overlay_opacity'] ?? 0.40;
$heroUrl = $heroImage[0] === '/' ? basePath() . $heroImage : asset($heroImage);

$pageTitle = $isEnglish ? 'Order Request' : 'Pedido de Encomenda';
$pageDescription = $isEnglish ? 'Submit your order request' : 'Envie o seu pedido de encomenda';

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Banner with Breadcrumbs -->
<section class="relative h-[45vh] min-h-[400px] flex items-end pb-16 bg-primary overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat bg-fixed"
             style="background-image: url('<?= $heroUrl ?>');">
        </div>
        <div class="absolute inset-0 bg-black" style="opacity: <?= $heroOverlay ?>"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-black/40"></div>
    </div>

    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
        <!-- Breadcrumbs -->
        <nav class="mb-4 animate-on-scroll" data-animation="fade-up" aria-label="Breadcrumb">
            <ol class="flex items-center text-sm text-white/90 flex-wrap">
                <li>
                    <a href="<?= $base ?>/" class="hover:text-white transition-colors"><?= $isEnglish ? 'Home' : 'Inicio' ?></a>
                </li>
                <li>
                    <svg class="w-4 h-4 mx-2 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </li>
                <li>
                    <a href="<?= $base ?>/loja/" class="hover:text-white transition-colors"><?= $isEnglish ? 'Shop' : 'Loja' ?></a>
                </li>
                <li>
                    <svg class="w-4 h-4 mx-2 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </li>
                <li>
                    <a href="<?= $base ?>/loja/carrinho/" class="hover:text-white transition-colors"><?= $isEnglish ? 'Cart' : 'Carrinho' ?></a>
                </li>
                <li>
                    <svg class="w-4 h-4 mx-2 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </li>
                <li class="text-white font-medium"><?= $isEnglish ? 'Order Request' : 'Pedido' ?></li>
            </ol>
        </nav>

        <!-- Product Count -->
        <?php if (!$success): ?>
        <p class="inline-block text-accent text-lg font-medium tracking-[0.15em] uppercase mb-4 animate-on-scroll" data-animation="fade-up">
            <?= count($cartItems) ?> <?= count($cartItems) === 1 ? ($isEnglish ? 'item' : 'produto') : ($isEnglish ? 'items' : 'produtos') ?>
        </p>
        <?php endif; ?>

        <!-- Title -->
        <h1 class="font-cursive text-4xl md:text-5xl lg:text-6xl text-cream drop-shadow-xl animate-on-scroll" data-animation="fade-up" data-delay="100">
            <?= $isEnglish ? 'Order Request' : 'Pedido de Encomenda' ?>
        </h1>
    </div>
</section>

<?php if ($success): ?>
<!-- Success State -->
<section class="py-12 lg:py-16 bg-cream-100 min-h-[60vh]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm border border-cream-100 p-10 lg:p-16 text-center animate-on-scroll" data-animation="fade-up">
            <div class="w-20 h-20 bg-secondary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h2 class="font-serif text-3xl text-primary mb-4"><?= $isEnglish ? 'Request Submitted!' : 'Pedido Enviado!' ?></h2>
            <p class="text-charcoal/60 max-w-lg mx-auto mb-4 leading-relaxed">
                <?= $isEnglish
                    ? 'Your order request has been sent. We will contact you by phone or email to finalize the payment and confirm the order.'
                    : 'O seu pedido de encomenda foi enviado. Iremos contacta-lo por telefone ou email para finalizar o pagamento e confirmar a encomenda.' ?>
            </p>
            <p class="text-charcoal/60 max-w-lg mx-auto mb-8 leading-relaxed">
                <?= $isEnglish
                    ? 'Please keep your phone available. We will contact you within 24 hours.'
                    : 'Por favor mantenha o telefone disponivel. Iremos contacta-lo dentro de 24 horas.' ?>
            </p>

            <div class="bg-accent/10 rounded-2xl p-5 mb-8 border border-accent/20 max-w-md mx-auto animate-on-scroll" data-animation="fade-up" data-delay="100">
                <div class="flex items-center justify-center gap-3">
                    <svg class="w-5 h-5 text-accent flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <span class="text-sm text-charcoal/70 font-medium">
                        <?= $isEnglish ? 'We will contact you by phone or email' : 'Iremos contacta-lo por telefone ou email' ?>
                    </span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center animate-on-scroll" data-animation="fade-up" data-delay="200">
                <a href="<?= $base ?>/" class="inline-flex items-center justify-center px-8 py-4 bg-primary text-cream font-semibold rounded-xl hover:bg-primary/90 transition-all duration-200 shadow-md hover:shadow-lg">
                    <?= $isEnglish ? 'Back to Home' : 'Voltar ao Inicio' ?>
                </a>
                <a href="<?= $base ?>/loja/" class="inline-flex items-center justify-center px-8 py-4 bg-white text-primary font-semibold rounded-xl border-2 border-cream-200 hover:bg-cream-50 transition-all duration-200">
                    <?= $isEnglish ? 'Continue Shopping' : 'Continuar a Comprar' ?>
                </a>
            </div>
        </div>
    </div>
</section>

<?php else: ?>
<!-- Manual Order Form -->
<section class="py-12 lg:py-16 bg-cream-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <?php if (!empty($errors['general'])): ?>
        <div class="mb-6 p-4 bg-terracotta-50 border border-terracotta-200 text-terracotta-700 rounded-xl text-sm">
            <?= e($errors['general']) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="" class="lg:grid lg:grid-cols-3 lg:gap-8">
            <?= CSRF::tokenField() ?>

            <!-- Left Column - Form -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Contact Info -->
                <div class="bg-white rounded-lg shadow-sm p-6 animate-on-scroll" data-animation="fade-up">
                    <h2 class="font-serif text-xl text-granite-800 mb-6">
                        <?= $isEnglish ? 'Contact Information' : 'Informacoes de Contacto' ?>
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="customer_name" class="block text-sm font-medium text-granite-700 mb-2">
                                <?= $isEnglish ? 'Full Name' : 'Nome Completo' ?> <span class="text-terracotta-500">*</span>
                            </label>
                            <input type="text" id="customer_name" name="customer_name" value="<?= e($formData['customer_name']) ?>" required
                                   class="w-full px-4 py-3 border <?= isset($errors['customer_name']) ? 'border-terracotta-400' : 'border-granite-200' ?> rounded focus:ring-2 focus:ring-olive-500 focus:border-olive-500 outline-none">
                            <?php if (isset($errors['customer_name'])): ?>
                            <p class="mt-1 text-sm text-terracotta-500"><?= e($errors['customer_name']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label for="customer_email" class="block text-sm font-medium text-granite-700 mb-2">
                                Email <span class="text-terracotta-500">*</span>
                            </label>
                            <input type="email" id="customer_email" name="customer_email" value="<?= e($formData['customer_email']) ?>" required
                                   class="w-full px-4 py-3 border <?= isset($errors['customer_email']) ? 'border-terracotta-400' : 'border-granite-200' ?> rounded focus:ring-2 focus:ring-olive-500 focus:border-olive-500 outline-none">
                            <?php if (isset($errors['customer_email'])): ?>
                            <p class="mt-1 text-sm text-terracotta-500"><?= e($errors['customer_email']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="md:col-span-2">
                            <label for="customer_phone" class="block text-sm font-medium text-granite-700 mb-2">
                                <?= $isEnglish ? 'Phone' : 'Telefone' ?> <span class="text-terracotta-500">*</span>
                            </label>
                            <input type="tel" id="customer_phone" name="customer_phone" value="<?= e($formData['customer_phone']) ?>" required placeholder="9XX XXX XXX"
                                   class="w-full px-4 py-3 border <?= isset($errors['customer_phone']) ? 'border-terracotta-400' : 'border-granite-200' ?> rounded focus:ring-2 focus:ring-olive-500 focus:border-olive-500 outline-none">
                            <?php if (isset($errors['customer_phone'])): ?>
                            <p class="mt-1 text-sm text-terracotta-500"><?= e($errors['customer_phone']) ?></p>
                            <?php endif; ?>
                            <p class="mt-1 text-xs text-granite-400">
                                <?= $isEnglish ? 'We will contact you by phone or email to arrange payment' : 'Iremos contacta-lo por telefone ou email para combinar o pagamento' ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="bg-white rounded-lg shadow-sm p-6 animate-on-scroll" data-animation="fade-up" data-delay="100">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="font-serif text-xl text-granite-800">
                            <?= $isEnglish ? 'Shipping Address' : 'Morada de Envio' ?>
                        </h2>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label for="shipping_address" class="block text-sm font-medium text-granite-700 mb-2">
                                <?= $isEnglish ? 'Address' : 'Morada' ?> <span class="text-terracotta-500">*</span>
                            </label>
                            <input type="text" id="shipping_address" name="shipping_address" value="<?= e($formData['shipping_address']) ?>" required
                                   class="w-full px-4 py-3 border <?= isset($errors['shipping_address']) ? 'border-terracotta-400' : 'border-granite-200' ?> rounded focus:ring-2 focus:ring-olive-500 focus:border-olive-500 outline-none">
                            <?php if (isset($errors['shipping_address'])): ?>
                            <p class="mt-1 text-sm text-terracotta-500"><?= e($errors['shipping_address']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="shipping_city" class="block text-sm font-medium text-granite-700 mb-2">
                                    <?= $isEnglish ? 'City' : 'Cidade' ?> <span class="text-terracotta-500">*</span>
                                </label>
                                <input type="text" id="shipping_city" name="shipping_city" value="<?= e($formData['shipping_city']) ?>" required
                                       class="w-full px-4 py-3 border <?= isset($errors['shipping_city']) ? 'border-terracotta-400' : 'border-granite-200' ?> rounded focus:ring-2 focus:ring-olive-500 focus:border-olive-500 outline-none">
                                <?php if (isset($errors['shipping_city'])): ?>
                                <p class="mt-1 text-sm text-terracotta-500"><?= e($errors['shipping_city']) ?></p>
                                <?php endif; ?>
                            </div>
                            <div>
                                <label for="shipping_postal_code" class="block text-sm font-medium text-granite-700 mb-2">
                                    <?= $isEnglish ? 'Postal Code' : 'Codigo Postal' ?> <span class="text-terracotta-500">*</span>
                                </label>
                                <input type="text" id="shipping_postal_code" name="shipping_postal_code" value="<?= e($formData['shipping_postal_code']) ?>" required placeholder="XXXX-XXX"
                                       class="w-full px-4 py-3 border <?= isset($errors['shipping_postal_code']) ? 'border-terracotta-400' : 'border-granite-200' ?> rounded focus:ring-2 focus:ring-olive-500 focus:border-olive-500 outline-none">
                                <?php if (isset($errors['shipping_postal_code'])): ?>
                                <p class="mt-1 text-sm text-terracotta-500"><?= e($errors['shipping_postal_code']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div>
                            <label for="notes" class="block text-sm font-medium text-granite-700 mb-2">
                                <?= $isEnglish ? 'Notes' : 'Notas' ?>
                            </label>
                            <textarea id="notes" name="notes" rows="3"
                                      class="w-full px-4 py-3 border border-granite-200 rounded focus:ring-2 focus:ring-olive-500 focus:border-olive-500 outline-none resize-none"><?= e($formData['notes']) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Order Summary -->
            <div class="lg:col-span-1 mt-8 lg:mt-0 animate-on-scroll" data-animation="fade-up" data-delay="100">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-24">
                    <h2 class="font-serif text-xl text-granite-800 mb-6">
                        <?= $isEnglish ? 'Order Summary' : 'Resumo da Encomenda' ?>
                    </h2>

                    <!-- Items -->
                    <div class="space-y-4 mb-6">
                        <?php foreach ($cartItems as $item):
                            $productImage = $item['product']->getPrimaryImage();
                        ?>
                        <div class="flex items-center gap-3 text-sm">
                            <div class="w-14 h-14 rounded-lg overflow-hidden flex-shrink-0 bg-cream-100 border border-granite-100">
                                <?php if ($productImage): ?>
                                <img src="<?= e(basePath() . $productImage) ?>" alt="<?= e($item['product']->name) ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-granite-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-granite-700 truncate"><?= e($item['product']->name) ?></p>
                                <p class="text-granite-400 text-xs">x<?= $item['quantity'] ?></p>
                            </div>
                            <span class="font-medium text-granite-800 whitespace-nowrap"><?= formatPrice($item['subtotal']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Totals -->
                    <div class="border-t border-granite-200 pt-4 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-granite-600">Subtotal</span>
                            <span class="text-granite-800"><?= formatPrice($cartSubtotal) ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-granite-600"><?= $isEnglish ? 'Shipping' : 'Envio' ?></span>
                            <span class="text-granite-800">
                                <?= $shipping > 0 ? formatPrice($shipping) : '<span class="text-secondary font-medium">' . ($isEnglish ? 'Free' : 'Gratis') . '</span>' ?>
                            </span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t border-granite-200 pt-3">
                            <span class="text-granite-800">Total</span>
                            <span class="text-granite-800"><?= formatPrice($cartTotal) ?></span>
                        </div>
                    </div>

                    <!-- Payment Methods Accepted -->
                    <div class="mt-6 pt-6 border-t border-granite-200">
                        <p class="text-xs text-granite-500 uppercase tracking-wider font-medium mb-3 text-center">
                            <?= $isEnglish ? 'Accepted Payment Methods' : 'Metodos de Pagamento Aceites' ?>
                        </p>
                        <div class="flex items-center justify-center gap-3 flex-wrap">
                            <img src="<?= asset('images/MB_WAY.jpg') ?>" alt="MB WAY" class="h-8 rounded shadow-sm">
                            <img src="<?= asset('images/multibanco.png') ?>" alt="Multibanco" class="h-8 rounded shadow-sm">
                            <img src="<?= asset('images/VISA.png') ?>" alt="Visa" class="h-8 rounded shadow-sm">
                            <img src="<?= asset('images/Mastercard.png') ?>" alt="Mastercard" class="h-8 rounded shadow-sm">
                        </div>
                    </div>

                    <!-- How it works -->
                    <div class="mt-6 pt-6 border-t border-granite-200">
                        <p class="text-xs text-granite-500 uppercase tracking-wider font-medium mb-3">
                            <?= $isEnglish ? 'How it works' : 'Como funciona' ?>
                        </p>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <div class="w-6 h-6 bg-secondary/10 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <span class="text-xs font-bold text-secondary">1</span>
                                </div>
                                <span class="text-xs text-granite-500 leading-relaxed">
                                    <?= $isEnglish ? 'Submit your order request' : 'Envie o seu pedido' ?>
                                </span>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-6 h-6 bg-secondary/10 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <span class="text-xs font-bold text-secondary">2</span>
                                </div>
                                <span class="text-xs text-granite-500 leading-relaxed">
                                    <?= $isEnglish ? 'We contact you by phone or email to confirm and arrange payment' : 'Contactamos por telefone ou email para confirmar e combinar pagamento' ?>
                                </span>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-6 h-6 bg-secondary/10 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <span class="text-xs font-bold text-secondary">3</span>
                                </div>
                                <span class="text-xs text-granite-500 leading-relaxed">
                                    <?= $isEnglish ? 'We ship your order' : 'Enviamos a sua encomenda' ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="w-full mt-6 py-4 bg-primary text-cream font-semibold text-lg rounded-xl hover:bg-primary/90 transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <?= $isEnglish ? 'Submit Order Request' : 'Enviar Pedido' ?>
                    </button>

                    <p class="text-xs text-granite-500 text-center mt-4">
                        <?= $isEnglish
                            ? 'We will contact you by phone or email to finalize payment.'
                            : 'Iremos contacta-lo por telefone ou email para finalizar o pagamento.' ?>
                    </p>
                    <p class="text-xs text-granite-500 text-center mt-2">
                        <?= $isEnglish
                            ? 'By submitting, you agree to our <a href="' . $base . '/termos-condicoes/" class="text-secondary hover:underline">terms and conditions</a> and <a href="' . $base . '/politica-privacidade/" class="text-secondary hover:underline">privacy policy</a>.'
                            : 'Ao enviar, concorda com os nossos <a href="' . $base . '/termos-condicoes/" class="text-secondary hover:underline">termos e condicoes</a> e <a href="' . $base . '/politica-privacidade/" class="text-secondary hover:underline">politica de privacidade</a>.' ?>
                    </p>
                </div>
            </div>
        </form>
    </div>
</section>
<?php endif; ?>

<?php include INCLUDES_PATH . '/footer.php'; ?>
