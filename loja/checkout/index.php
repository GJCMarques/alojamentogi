<?php
/**
 * A Casa do Gi - Checkout Page
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';

use Core\Cart;
use Core\CSRF;
use Core\Validator;
use Core\Database;
use Core\Session;
use Core\Language;

$cart = Cart::getInstance();
$db = Database::getInstance();
$lang = Language::getInstance();
$base = basePath();
$isEnglish = $lang->isEnglish();

// Redirect if cart is empty
if ($cart->isEmpty()) {
    Session::flash('warning', $isEnglish ? 'Your cart is empty' : 'O seu carrinho esta vazio');
    redirect($base . ($isEnglish ? '/en/shop/' : '/loja/'));
}

// Validate cart (check stock, etc.)
$cartErrors = $cart->validate();
if (!empty($cartErrors)) {
    foreach ($cartErrors as $error) {
        Session::flash('error', $error);
    }
    redirect($base . ($isEnglish ? '/en/shop/cart/' : '/loja/carrinho/'));
}

// Get cart data
$cartItems = $cart->getItems();
$cartSubtotal = $cart->getSubtotal();
$shippingCost = (float) setting('shipping_cost', 5);
$freeShippingThreshold = (float) setting('free_shipping_threshold', 50);
$shipping = $cartSubtotal >= $freeShippingThreshold ? 0 : $shippingCost;
$cartTotal = $cartSubtotal + $shipping;

// Form data
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

// Handle form submission
if (isPost()) {
    CSRF::check();

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

    // Validation
    $validator = new Validator();
    $validator->required($formData['customer_name'], 'customer_name', $isEnglish ? 'Name is required' : 'Nome e obrigatorio');
    $validator->email($formData['customer_email'], 'customer_email', $isEnglish ? 'Valid email is required' : 'Email valido e obrigatorio');
    $validator->required($formData['customer_phone'], 'customer_phone', $isEnglish ? 'Phone is required' : 'Telefone e obrigatorio');
    $validator->required($formData['shipping_address'], 'shipping_address', $isEnglish ? 'Address is required' : 'Morada e obrigatoria');
    $validator->required($formData['shipping_city'], 'shipping_city', $isEnglish ? 'City is required' : 'Cidade e obrigatoria');
    $validator->required($formData['shipping_postal_code'], 'shipping_postal_code', $isEnglish ? 'Postal code is required' : 'Codigo postal e obrigatorio');

    // Validate payment method
    $validMethods = ['mbway', 'multibanco', 'card'];
    if (!in_array($formData['payment_method'], $validMethods)) {
        $errors['payment_method'] = $isEnglish ? 'Please select a valid payment method' : 'Selecione um metodo de pagamento valido';
    }

    // Validate phone for MBWay
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

            // Create order
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

            // Add order items
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

                // Update stock
                if ($item['product']->track_inventory) {
                    $db->query(
                        "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?",
                        [$item['quantity'], $item['product']->id]
                    );
                }
            }

            $db->commit();

            // Clear cart
            $cart->clear();

            // Store order info in session for payment page
            Session::set('pending_order', [
                'id' => $orderId,
                'number' => $orderNumber,
                'total' => $cartTotal,
                'payment_method' => $formData['payment_method'],
                'phone' => $formData['customer_phone'],
                'email' => $formData['customer_email']
            ]);

            // Redirect to payment page
            redirect($base . '/loja/checkout/pagamento/');

        } catch (\Exception $e) {
            $db->rollback();
            logMessage("Checkout error: " . $e->getMessage(), 'error');
            $errors['general'] = $isEnglish ? 'An error occurred. Please try again.' : 'Ocorreu um erro. Por favor tente novamente.';
        }
    }
}

// Page configuration
$pageTitle = $isEnglish ? 'Checkout' : 'Finalizar Compra';
$pageDescription = $isEnglish ? 'Complete your order' : 'Complete a sua encomenda';

include INCLUDES_PATH . '/header.php';
?>

<!-- Breadcrumb -->
<nav class="bg-cream-200 py-3 border-b border-cream-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <ol class="flex items-center text-sm text-granite-500">
            <li><a href="<?= $base ?>/" class="hover:text-olive-600"><?= $isEnglish ? 'Home' : 'Inicio' ?></a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="<?= $base ?>/loja/" class="hover:text-olive-600"><?= $isEnglish ? 'Shop' : 'Loja' ?></a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="<?= $base ?>/loja/carrinho/" class="hover:text-olive-600"><?= $isEnglish ? 'Cart' : 'Carrinho' ?></a></li>
            <li><span class="mx-2">/</span></li>
            <li class="text-granite-700 font-medium"><?= $isEnglish ? 'Checkout' : 'Finalizar' ?></li>
        </ol>
    </div>
</nav>

<!-- Checkout Section -->
<section class="py-12 lg:py-16 bg-cream-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="font-serif text-3xl md:text-4xl text-granite-800 mb-8">
            <?= $isEnglish ? 'Checkout' : 'Finalizar Compra' ?>
        </h1>

        <?php if (!empty($errors['general'])): ?>
        <div class="mb-6 p-4 bg-terracotta-50 border border-terracotta-200 text-terracotta-700 rounded">
            <?= e($errors['general']) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="" class="lg:grid lg:grid-cols-3 lg:gap-8">
            <?= CSRF::tokenField() ?>

            <!-- Left Column - Form -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Customer Info -->
                <div class="bg-white rounded-lg shadow-sm p-6">
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
                <div class="bg-white rounded-lg shadow-sm p-6">
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
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="font-serif text-xl text-granite-800 mb-6">
                        <?= $isEnglish ? 'Payment Method' : 'Metodo de Pagamento' ?>
                    </h2>

                    <div class="space-y-4">
                        <label class="flex items-center p-4 border rounded cursor-pointer hover:bg-cream-50 transition-colors <?= $formData['payment_method'] === 'mbway' ? 'border-olive-500 bg-olive-50' : 'border-granite-200' ?>">
                            <input type="radio" name="payment_method" value="mbway"
                                   <?= $formData['payment_method'] === 'mbway' ? 'checked' : '' ?>
                                   class="w-4 h-4 text-olive-600 focus:ring-olive-500">
                            <div class="ml-4 flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-granite-800">MB WAY</span>
                                    <img src="<?= asset('images/mbway-logo.png') ?>" alt="MB WAY" class="h-6" onerror="this.style.display='none'">
                                </div>
                                <p class="text-sm text-granite-500 mt-1">
                                    <?= $isEnglish ? 'Pay instantly with your phone' : 'Pague instantaneamente com o telemovel' ?>
                                </p>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border rounded cursor-pointer hover:bg-cream-50 transition-colors <?= $formData['payment_method'] === 'multibanco' ? 'border-olive-500 bg-olive-50' : 'border-granite-200' ?>">
                            <input type="radio" name="payment_method" value="multibanco"
                                   <?= $formData['payment_method'] === 'multibanco' ? 'checked' : '' ?>
                                   class="w-4 h-4 text-olive-600 focus:ring-olive-500">
                            <div class="ml-4 flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-granite-800">Multibanco</span>
                                    <img src="<?= asset('images/multibanco-logo.png') ?>" alt="Multibanco" class="h-6" onerror="this.style.display='none'">
                                </div>
                                <p class="text-sm text-granite-500 mt-1">
                                    <?= $isEnglish ? 'Pay with ATM or home banking' : 'Pague no multibanco ou homebanking' ?>
                                </p>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border rounded cursor-pointer hover:bg-cream-50 transition-colors <?= $formData['payment_method'] === 'card' ? 'border-olive-500 bg-olive-50' : 'border-granite-200' ?>">
                            <input type="radio" name="payment_method" value="card"
                                   <?= $formData['payment_method'] === 'card' ? 'checked' : '' ?>
                                   class="w-4 h-4 text-olive-600 focus:ring-olive-500">
                            <div class="ml-4 flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-granite-800"><?= $isEnglish ? 'Credit/Debit Card' : 'Cartao de Credito/Debito' ?></span>
                                    <div class="flex space-x-2">
                                        <img src="<?= asset('images/visa-logo.png') ?>" alt="Visa" class="h-6" onerror="this.style.display='none'">
                                        <img src="<?= asset('images/mastercard-logo.png') ?>" alt="Mastercard" class="h-6" onerror="this.style.display='none'">
                                    </div>
                                </div>
                                <p class="text-sm text-granite-500 mt-1">
                                    <?= $isEnglish ? 'Secure payment with Visa or Mastercard' : 'Pagamento seguro com Visa ou Mastercard' ?>
                                </p>
                            </div>
                        </label>
                    </div>

                    <?php if (isset($errors['payment_method'])): ?>
                    <p class="mt-2 text-sm text-terracotta-500"><?= e($errors['payment_method']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column - Order Summary -->
            <div class="lg:col-span-1 mt-8 lg:mt-0">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-24">
                    <h2 class="font-serif text-xl text-granite-800 mb-6">
                        <?= $isEnglish ? 'Order Summary' : 'Resumo da Encomenda' ?>
                    </h2>

                    <!-- Items -->
                    <div class="space-y-4 mb-6">
                        <?php foreach ($cartItems as $item): ?>
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-cream-100 rounded overflow-hidden flex-shrink-0">
                                <img src="<?= e(UPLOADS_URL . '/products/' . $item['product']->getPrimaryImage()) ?>"
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
                            class="w-full mt-6 py-4 bg-olive-600 text-white font-medium rounded hover:bg-olive-700 transition-colors">
                        <?= $isEnglish ? 'Proceed to Payment' : 'Proceder ao Pagamento' ?>
                    </button>

                    <p class="text-xs text-granite-500 text-center mt-4">
                        <?= $isEnglish
                            ? 'By placing your order, you agree to our terms and conditions.'
                            : 'Ao efetuar a encomenda, concorda com os nossos termos e condicoes.' ?>
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
            r.closest('label').classList.remove('border-olive-500', 'bg-olive-50');
            r.closest('label').classList.add('border-granite-200');
        });
        this.closest('label').classList.remove('border-granite-200');
        this.closest('label').classList.add('border-olive-500', 'bg-olive-50');
    });
});
</script>

<?php include INCLUDES_PATH . '/footer.php'; ?>
