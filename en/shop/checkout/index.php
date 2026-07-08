<?php

require_once dirname(dirname(dirname(__DIR__))) . '/includes/init.php';

use Core\Cart;
use Core\CSRF;
use Core\Validator;
use Core\Database;
use Core\Session;
use Core\Language;

$cart = Cart::getInstance();
$db = Database::getInstance();
$lang = Language::getInstance();
$lang->setLanguage(LANG_EN);
$base = basePath();
$isEnglish = $lang->isEnglish();

$shopMode = setting('shop_mode', 'active');

if ($shopMode === 'closed') {

    Session::flash('success', $isEnglish
        ? 'Our shop is temporarily closed. For more information, please contact us.'
        : 'A nossa loja esta temporariamente fechada. Para mais informacoes, contacte-nos.');
    $referer = $_SERVER['HTTP_REFERER'] ?? ($base . ($isEnglish ? '/en/shop/' : '/loja/'));
    redirect($referer);
}

if ($shopMode === 'manual') {

    redirect($base . '/en/shop/checkout/manual/');
}

if ($cart->isEmpty()) {
    Session::flash('warning', $isEnglish ? 'Your cart is empty' : 'O seu carrinho esta vazio');
    redirect($base . ($isEnglish ? '/en/shop/' : '/loja/'));
}

$cartErrors = $cart->validate();
if (!empty($cartErrors)) {
    foreach ($cartErrors as $error) {
        Session::flash('error', $error);
    }
    redirect($base . ($isEnglish ? '/en/shop/cart/' : '/loja/carrinho/'));
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
    'payment_method' => 'mbway'
];

if (isPost()) {
    CSRF::check();

    $rateLimiter = \Core\RateLimiter::getInstance();
    if (!$rateLimiter->check('checkout_submit', 5, 600)) {
        $errors['general'] = $isEnglish
            ? 'Too many checkout attempts. Please wait a few minutes and try again.'
            : 'Demasiadas tentativas. Por favor aguarde alguns minutos e tente novamente.';
    }

    $paymentNonce = post('payment_nonce', '');
    $sessionNonce = Session::get('checkout_nonce');
    if (!$paymentNonce || !$sessionNonce || !hash_equals($sessionNonce, $paymentNonce)) {
        $errors['general'] = $isEnglish
            ? 'Form expired. Please refresh the page and try again.'
            : 'Formulario expirado. Atualize a pagina e tente novamente.';
    }

    Session::remove('checkout_nonce');

    $formData = [
        'customer_name' => sanitize(post('customer_name', '')),
        'customer_email' => sanitizeEmail(post('customer_email', '')),
        'customer_phone' => sanitize(post('customer_phone', '')),
        'shipping_address' => sanitize(post('shipping_address', '')),
        'shipping_city' => sanitize(post('shipping_city', '')),
        'shipping_postal_code' => sanitize(post('shipping_postal_code', '')),
        'notes' => sanitize(post('notes', '')),
        'payment_method' => sanitize(post('payment_method', 'mbway'))
    ];

    $validator = new Validator();
    $validator->required($formData['customer_name'], 'customer_name', $isEnglish ? 'Name is required' : 'Nome e obrigatorio');
    $validator->email($formData['customer_email'], 'customer_email', $isEnglish ? 'Valid email is required' : 'Email valido e obrigatorio');
    $validator->required($formData['customer_phone'], 'customer_phone', $isEnglish ? 'Phone is required' : 'Telefone e obrigatorio');
    $validator->required($formData['shipping_address'], 'shipping_address', $isEnglish ? 'Address is required' : 'Morada e obrigatoria');
    $validator->required($formData['shipping_city'], 'shipping_city', $isEnglish ? 'City is required' : 'Cidade e obrigatoria');
    $validator->required($formData['shipping_postal_code'], 'shipping_postal_code', $isEnglish ? 'Postal code is required' : 'Codigo postal e obrigatorio');

    $validMethods = ['mbway', 'multibanco', 'card'];
    if (!in_array($formData['payment_method'], $validMethods)) {
        $errors['payment_method'] = $isEnglish ? 'Please select a valid payment method' : 'Selecione um metodo de pagamento valido';
    }

    if ($formData['payment_method'] === 'mbway') {
        $phone = preg_replace('/\D/', '', $formData['customer_phone']);
        if (strlen($phone) !== 9 || !preg_match('/^9[1236]\d{7}$/', $phone)) {
            $errors['customer_phone'] = $isEnglish ? 'Please enter a valid Portuguese mobile number for MBWay' : 'Introduza um numero de telemovel portugues valido para MBWay';
        }
    }

    $errors = array_merge($errors, $validator->getErrors());

    if (empty($errors)) {
        try {
            $db->beginTransaction();

            $orderNumber = generateOrderNumber();
            $orderId = $db->insert('orders', [
                'order_number' => $orderNumber,
                'customer_name' => $formData['customer_name'],
                'customer_email' => $formData['customer_email'],
                'customer_phone' => $formData['customer_phone'],
                'shipping_address' => $formData['shipping_address'],
                'shipping_city' => $formData['shipping_city'],
                'shipping_postal_code' => $formData['shipping_postal_code'],
                'notes' => $formData['notes'],
                'subtotal' => $cartSubtotal,
                'shipping' => $shipping,
                'total' => $cartTotal,
                'status' => ORDER_STATUS_PENDING,
                'payment_method' => $formData['payment_method'],
                'payment_status' => PAYMENT_STATUS_PENDING,
                'ip_address' => getClientIp(),
                'user_agent' => substr(getUserAgent(), 0, 500)
            ]);

            foreach ($cartItems as $item) {
                $db->insert('order_items', [
                    'order_id' => $orderId,
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'product_sku' => $item['product']->sku,
                    'quantity' => $item['quantity'],
                    'price' => $item['product']->getCurrentPrice(),
                    'subtotal' => $item['subtotal']
                ]);

                if ($item['product']->track_inventory) {
                    $db->query(
                        "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?",
                        [$item['quantity'], $item['product']->id]
                    );
                }
            }

            $db->commit();

            $cart->clear();

            Session::set('pending_order', [
                'id' => $orderId,
                'number' => $orderNumber,
                'total' => $cartTotal,
                'payment_method' => $formData['payment_method'],
                'phone' => $formData['customer_phone'],
                'email' => $formData['customer_email']
            ]);

            redirect($base . '/en/shop/checkout/payment/');

        } catch (\Exception $e) {
            $db->rollback();
            logMessage("Checkout error: " . $e->getMessage(), 'error');
            $errors['general'] = $isEnglish ? 'An error occurred. Please try again.' : 'Ocorreu um erro. Por favor tente novamente.';
        }
    }
}

$checkoutNonce = bin2hex(random_bytes(32));
Session::set('checkout_nonce', $checkoutNonce);

$checkoutHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = 'checkout' AND is_active = 1");
if (!$checkoutHero) {
    $checkoutHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = 'shop' AND is_active = 1");
}
$heroMedia = $checkoutHero ? $db->fetch("SELECT * FROM media WHERE entity_type = 'hero' AND entity_id = ? AND is_cover = 1", [$checkoutHero['id']]) : null;
$heroImage = $heroMedia['file_path'] ?? 'images/MogadouroNeve.webp';
$heroOverlay = $checkoutHero['hero_overlay_opacity'] ?? 0.40;
$heroUrl = $heroImage[0] === '/' ? basePath() . $heroImage : asset($heroImage);

$pageTitle = $isEnglish ? 'Checkout' : 'Finalizar Compra';
$pageDescription = $isEnglish ? 'Complete your order' : 'Complete a sua encomenda';
$headerLayer = 2;

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
                    <a href="<?= $base ?>/" class="hover:text-white transition-colors"><?= $isEnglish ? 'Home' : 'Início' ?></a>
                </li>
                <li>
                    <svg class="w-4 h-4 mx-2 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </li>
                <li>
                    <a href="<?= $base ?>/en/shop/" class="hover:text-white transition-colors"><?= $isEnglish ? 'Shop' : 'Loja' ?></a>
                </li>
                <li>
                    <svg class="w-4 h-4 mx-2 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </li>
                <li>
                    <a href="<?= $base ?>/en/shop/cart/" class="hover:text-white transition-colors"><?= $isEnglish ? 'Cart' : 'Carrinho' ?></a>
                </li>
                <li>
                    <svg class="w-4 h-4 mx-2 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </li>
                <li class="text-white font-medium"><?= $isEnglish ? 'Checkout' : 'Finalizar' ?></li>
            </ol>
        </nav>

        <!-- Product Count -->
        <p class="inline-block text-accent text-lg font-medium tracking-[0.15em] uppercase mb-4 animate-on-scroll" data-animation="fade-up">
            <?= count($cartItems) ?> <?= count($cartItems) === 1 ? ($isEnglish ? 'item' : 'produto') : ($isEnglish ? 'items' : 'produtos') ?>
        </p>

        <!-- Title -->
        <h1 class="font-cursive text-4xl md:text-5xl lg:text-6xl text-cream drop-shadow-xl animate-on-scroll" data-animation="fade-up" data-delay="100">
            <?= $isEnglish ? 'Checkout' : 'Finalizar Compra' ?>
        </h1>
    </div>
</section>

<!-- Checkout Section -->
<section class="py-12 lg:py-16 bg-cream-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <?php if (!empty($errors['general'])): ?>
        <div class="mb-6 p-4 bg-terracotta-50 border border-terracotta-200 text-terracotta-700 rounded">
            <?= e($errors['general']) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="" class="lg:grid lg:grid-cols-3 lg:gap-8">
            <?= CSRF::tokenField() ?>
            <input type="hidden" name="payment_nonce" value="<?= $checkoutNonce ?>">

            <!-- Left Column - Form -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Customer Info -->
                <div class="bg-white rounded-lg shadow-sm p-6 animate-on-scroll" data-animation="fade-up">
                    <h2 class="font-serif text-xl text-granite-800 mb-6">
                        <?= $isEnglish ? 'Contact Information' : 'Informacoes de Contacto' ?>
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="customer_name" class="block text-sm font-medium text-granite-700 mb-2">
                                <?= $isEnglish ? 'Full Name' : 'Nome Completo' ?> <span class="text-terracotta-500">*</span>
                            </label>
                            <input type="text" id="customer_name" name="customer_name"
                                   value="<?= e($formData['customer_name']) ?>" required
                                   class="w-full px-4 py-3 border <?= isset($errors['customer_name']) ? 'border-terracotta-500' : 'border-granite-200' ?> rounded focus:ring-2 focus:ring-olive-500 focus:border-olive-500 outline-none">
                            <?php if (isset($errors['customer_name'])): ?>
                            <p class="mt-1 text-sm text-terracotta-500"><?= e($errors['customer_name']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label for="customer_email" class="block text-sm font-medium text-granite-700 mb-2">
                                Email <span class="text-terracotta-500">*</span>
                            </label>
                            <input type="email" id="customer_email" name="customer_email"
                                   value="<?= e($formData['customer_email']) ?>" required
                                   class="w-full px-4 py-3 border <?= isset($errors['customer_email']) ? 'border-terracotta-500' : 'border-granite-200' ?> rounded focus:ring-2 focus:ring-olive-500 focus:border-olive-500 outline-none">
                            <?php if (isset($errors['customer_email'])): ?>
                            <p class="mt-1 text-sm text-terracotta-500"><?= e($errors['customer_email']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="md:col-span-2">
                            <label for="customer_phone" class="block text-sm font-medium text-granite-700 mb-2">
                                <?= $isEnglish ? 'Phone' : 'Telefone' ?> <span class="text-terracotta-500">*</span>
                            </label>
                            <input type="tel" id="customer_phone" name="customer_phone"
                                   value="<?= e($formData['customer_phone']) ?>" required
                                   placeholder="9XX XXX XXX"
                                   class="w-full px-4 py-3 border <?= isset($errors['customer_phone']) ? 'border-terracotta-500' : 'border-granite-200' ?> rounded focus:ring-2 focus:ring-olive-500 focus:border-olive-500 outline-none">
                            <?php if (isset($errors['customer_phone'])): ?>
                            <p class="mt-1 text-sm text-terracotta-500"><?= e($errors['customer_phone']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="bg-white rounded-lg shadow-sm p-6 animate-on-scroll" data-animation="fade-up" data-delay="100">
                    <h2 class="font-serif text-xl text-granite-800 mb-6">
                        <?= $isEnglish ? 'Shipping Address' : 'Morada de Envio' ?>
                    </h2>

                    <div class="space-y-6">
                        <div>
                            <label for="shipping_address" class="block text-sm font-medium text-granite-700 mb-2">
                                <?= $isEnglish ? 'Address' : 'Morada' ?> <span class="text-terracotta-500">*</span>
                            </label>
                            <input type="text" id="shipping_address" name="shipping_address"
                                   value="<?= e($formData['shipping_address']) ?>" required
                                   class="w-full px-4 py-3 border <?= isset($errors['shipping_address']) ? 'border-terracotta-500' : 'border-granite-200' ?> rounded focus:ring-2 focus:ring-olive-500 focus:border-olive-500 outline-none">
                            <?php if (isset($errors['shipping_address'])): ?>
                            <p class="mt-1 text-sm text-terracotta-500"><?= e($errors['shipping_address']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="shipping_city" class="block text-sm font-medium text-granite-700 mb-2">
                                    <?= $isEnglish ? 'City' : 'Cidade' ?> <span class="text-terracotta-500">*</span>
                                </label>
                                <input type="text" id="shipping_city" name="shipping_city"
                                       value="<?= e($formData['shipping_city']) ?>" required
                                       class="w-full px-4 py-3 border <?= isset($errors['shipping_city']) ? 'border-terracotta-500' : 'border-granite-200' ?> rounded focus:ring-2 focus:ring-olive-500 focus:border-olive-500 outline-none">
                                <?php if (isset($errors['shipping_city'])): ?>
                                <p class="mt-1 text-sm text-terracotta-500"><?= e($errors['shipping_city']) ?></p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label for="shipping_postal_code" class="block text-sm font-medium text-granite-700 mb-2">
                                    <?= $isEnglish ? 'Postal Code' : 'Codigo Postal' ?> <span class="text-terracotta-500">*</span>
                                </label>
                                <input type="text" id="shipping_postal_code" name="shipping_postal_code"
                                       value="<?= e($formData['shipping_postal_code']) ?>" required
                                       placeholder="XXXX-XXX"
                                       class="w-full px-4 py-3 border <?= isset($errors['shipping_postal_code']) ? 'border-terracotta-500' : 'border-granite-200' ?> rounded focus:ring-2 focus:ring-olive-500 focus:border-olive-500 outline-none">
                                <?php if (isset($errors['shipping_postal_code'])): ?>
                                <p class="mt-1 text-sm text-terracotta-500"><?= e($errors['shipping_postal_code']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-granite-700 mb-2">
                                <?= $isEnglish ? 'Order Notes (optional)' : 'Notas da Encomenda (opcional)' ?>
                            </label>
                            <textarea id="notes" name="notes" rows="3"
                                      class="w-full px-4 py-3 border border-granite-200 rounded focus:ring-2 focus:ring-olive-500 focus:border-olive-500 outline-none resize-none"><?= e($formData['notes']) ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white rounded-lg shadow-sm p-6 animate-on-scroll" data-animation="fade-up" data-delay="200">
                    <h2 class="font-serif text-xl text-granite-800 mb-6">
                        <?= $isEnglish ? 'Payment Method' : 'Metodo de Pagamento' ?>
                    </h2>

                    <div class="space-y-4">
                        <!-- MB WAY -->
                        <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer hover:bg-cream-50 transition-all duration-200 <?= $formData['payment_method'] === 'mbway' ? 'border-olive-500 bg-olive-50 shadow-sm' : 'border-granite-200' ?>">
                            <input type="radio" name="payment_method" value="mbway"
                                   <?= $formData['payment_method'] === 'mbway' ? 'checked' : '' ?>
                                   class="sr-only">
                            <div class="flex-shrink-0 w-14 h-14 bg-white rounded-xl border border-granite-100 flex items-center justify-center mr-4 p-1.5">
                                <img loading="lazy" decoding="async" src="<?= asset('images/MB_WAY.webp') ?>" alt="MB WAY" class="w-full h-full object-contain">
                            </div>
                            <div class="flex-1">
                                <span class="font-semibold text-granite-800 text-base">MB WAY</span>
                                <p class="text-sm text-granite-500 mt-0.5">
                                    <?= $isEnglish ? 'Pay instantly with your phone' : 'Pague instantaneamente com o telemovel' ?>
                                </p>
                            </div>
                            <div class="flex-shrink-0 w-6 h-6 rounded-full border-2 flex items-center justify-center ml-3 payment-indicator <?= $formData['payment_method'] === 'mbway' ? 'border-olive-500 bg-olive-500' : 'border-granite-300' ?>">
                                <?php if ($formData['payment_method'] === 'mbway'): ?>
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                <?php endif; ?>
                            </div>
                        </label>

                        <!-- Multibanco -->
                        <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer hover:bg-cream-50 transition-all duration-200 <?= $formData['payment_method'] === 'multibanco' ? 'border-olive-500 bg-olive-50 shadow-sm' : 'border-granite-200' ?>">
                            <input type="radio" name="payment_method" value="multibanco"
                                   <?= $formData['payment_method'] === 'multibanco' ? 'checked' : '' ?>
                                   class="sr-only">
                            <div class="flex-shrink-0 w-14 h-14 bg-white rounded-xl border border-granite-100 flex items-center justify-center mr-4 p-1.5">
                                <img loading="lazy" decoding="async" src="<?= asset('images/multibanco.webp') ?>" alt="Multibanco" class="w-full h-full object-contain">
                            </div>
                            <div class="flex-1">
                                <span class="font-semibold text-granite-800 text-base">Multibanco</span>
                                <p class="text-sm text-granite-500 mt-0.5">
                                    <?= $isEnglish ? 'Pay with ATM or home banking' : 'Pague no multibanco ou homebanking' ?>
                                </p>
                            </div>
                            <div class="flex-shrink-0 w-6 h-6 rounded-full border-2 flex items-center justify-center ml-3 payment-indicator <?= $formData['payment_method'] === 'multibanco' ? 'border-olive-500 bg-olive-500' : 'border-granite-300' ?>">
                                <?php if ($formData['payment_method'] === 'multibanco'): ?>
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                <?php endif; ?>
                            </div>
                        </label>

                        <!-- Credit/Debit Card -->
                        <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer hover:bg-cream-50 transition-all duration-200 <?= $formData['payment_method'] === 'card' ? 'border-olive-500 bg-olive-50 shadow-sm' : 'border-granite-200' ?>">
                            <input type="radio" name="payment_method" value="card"
                                   <?= $formData['payment_method'] === 'card' ? 'checked' : '' ?>
                                   class="sr-only">
                            <div class="flex-shrink-0 w-24 h-14 bg-white rounded-xl border border-granite-100 flex items-center justify-center mr-4 p-1">
                                <div class="flex items-center gap-3">
                                    <img loading="lazy" decoding="async" src="<?= asset('images/VISA.webp') ?>" alt="Visa" class="h-5 object-contain">
                                    <img loading="lazy" decoding="async" src="<?= asset('images/Mastercard.webp') ?>" alt="Mastercard" class="h-7 object-contain">
                                </div>
                            </div>
                            <div class="flex-1">
                                <span class="font-semibold text-granite-800 text-base"><?= $isEnglish ? 'Credit/Debit Card' : 'Cartao de Credito/Debito' ?></span>
                                <p class="text-sm text-granite-500 mt-0.5">
                                    <?= $isEnglish ? 'Secure payment with Visa or Mastercard' : 'Pagamento seguro com Visa ou Mastercard' ?>
                                </p>
                            </div>
                            <div class="flex-shrink-0 w-6 h-6 rounded-full border-2 flex items-center justify-center ml-3 payment-indicator <?= $formData['payment_method'] === 'card' ? 'border-olive-500 bg-olive-500' : 'border-granite-300' ?>">
                                <?php if ($formData['payment_method'] === 'card'): ?>
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                <?php endif; ?>
                            </div>
                        </label>
                    </div>

                    <?php if (isset($errors['payment_method'])): ?>
                    <p class="mt-2 text-sm text-terracotta-500"><?= e($errors['payment_method']) ?></p>
                    <?php endif; ?>
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
                        <?php foreach ($cartItems as $item): ?>
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-cream-100 rounded overflow-hidden flex-shrink-0">
                                <img loading="lazy" decoding="async" src="<?= e(basePath() . $item['product']->getPrimaryImage()) ?>"
                                     alt="<?= e($item['product']->name) ?>"
                                     class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-granite-800 truncate"><?= e($item['product']->name) ?></p>
                                <p class="text-sm text-granite-500">x<?= $item['quantity'] ?></p>
                            </div>
                            <p class="text-sm font-medium text-granite-800"><?= formatPrice($item['subtotal']) ?></p>
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
                                <?php if ($shipping === 0): ?>
                                <span class="text-olive-600"><?= $isEnglish ? 'Free' : 'Gratis' ?></span>
                                <?php else: ?>
                                <?= formatPrice($shipping) ?>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t border-granite-200 pt-3">
                            <span class="text-granite-800">Total</span>
                            <span class="text-granite-800"><?= formatPrice($cartTotal) ?></span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="w-full mt-6 py-4 bg-primary text-cream font-semibold text-lg rounded-xl hover:bg-primary/90 transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <?= $isEnglish ? 'Proceed to Payment' : 'Proceder ao Pagamento' ?>
                    </button>

                    <p class="text-xs text-granite-500 text-center mt-4">
                        <?= $isEnglish
                            ? 'By placing your order, you agree to our <a href="' . $base . '/termos-condicoes/" class="text-secondary hover:underline">terms and conditions</a> and <a href="' . $base . '/politica-privacidade/" class="text-secondary hover:underline">privacy policy</a>.'
                            : 'Ao efetuar a encomenda, concorda com os nossos <a href="' . $base . '/termos-condicoes/" class="text-secondary hover:underline">termos e condicoes</a> e <a href="' . $base . '/politica-privacidade/" class="text-secondary hover:underline">politica de privacidade</a>.' ?>
                    </p>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
// Update payment method styling on change
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('input[name="payment_method"]').forEach(r => {
            const label = r.closest('label');
            const indicator = label.querySelector('.payment-indicator');
            label.classList.remove('border-olive-500', 'bg-olive-50', 'shadow-sm');
            label.classList.add('border-granite-200');
            if (indicator) {
                indicator.classList.remove('border-olive-500', 'bg-olive-500');
                indicator.classList.add('border-granite-300');
                indicator.innerHTML = '';
            }
        });
        const label = this.closest('label');
        const indicator = label.querySelector('.payment-indicator');
        label.classList.remove('border-granite-200');
        label.classList.add('border-olive-500', 'bg-olive-50', 'shadow-sm');
        if (indicator) {
            indicator.classList.remove('border-granite-300');
            indicator.classList.add('border-olive-500', 'bg-olive-500');
            indicator.innerHTML = '<svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>';
        }
    });
});
</script>

<?php include INCLUDES_PATH . '/footer.php'; ?>
