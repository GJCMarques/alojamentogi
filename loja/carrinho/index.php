<?php
/**
 * A Casa do Gi - Shopping Cart Page (Portuguese)
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';

use Core\Cart;
use Core\CSRF;
use Core\Database;

$base = basePath();
$db = Database::getInstance();

$cart = Cart::getInstance();
$cartItems = $cart->getItems();
$cartErrors = $cart->validate();

// Remove invalid items messages (except empty cart)
$cartErrors = array_filter($cartErrors, fn($e) => $e !== 'O carrinho está vazio.');

// Get hero image from database (cart page hero, fallback to shop)
$pageHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = 'cart' AND is_active = 1");
if (!$pageHero) {
    $pageHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = 'shop' AND is_active = 1");
}
$heroMedia = $pageHero ? $db->fetch("SELECT * FROM media WHERE entity_type = 'hero' AND entity_id = ? AND is_cover = 1", [$pageHero['id']]) : null;
$heroImage = $heroMedia['file_path'] ?? 'images/MogadouroNeve.jpeg';
$heroOverlay = $pageHero['hero_overlay_opacity'] ?? 0.40;
$heroUrl = $heroImage[0] === '/' ? basePath() . $heroImage : asset($heroImage);

// Page configuration
$pageTitle = 'Carrinho de Compras';
$pageDescription = 'Reveja os produtos no seu carrinho de compras da Casa do Gi.';

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Banner with Breadcrumbs -->
<section class="relative h-[45vh] min-h-[400px] bg-gray-900 overflow-hidden">
    <!-- Hero Image -->
    <div class="absolute inset-0">
        <img src="<?= e($heroUrl) ?>"
             alt="Carrinho de Compras"
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-b from-black/<?= (int)($heroOverlay * 100) ?> via-black/<?= (int)($heroOverlay * 100) ?> to-black/70"></div>
    </div>

    <!-- Content -->
    <div class="relative h-full flex flex-col justify-end max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <!-- Breadcrumbs -->
        <nav class="mb-4 animate-on-scroll" data-animation="fade-up">
            <ol class="flex items-center space-x-2 text-sm text-white/90">
                <li>
                    <a href="<?= $base ?>/" class="hover:text-white transition-colors">Início</a>
                </li>
                <li>
                    <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </li>
                <li>
                    <a href="<?= $base ?>/loja/" class="hover:text-white transition-colors">Loja</a>
                </li>
                <li>
                    <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </li>
                <li class="text-white font-medium">Carrinho</li>
            </ol>
        </nav>

        <!-- Count -->
        <?php if (!$cart->isEmpty()): ?>
        <p class="inline-block text-accent text-lg font-medium tracking-[0.15em] uppercase mb-4 animate-on-scroll" data-animation="fade-up">
            <?= count($cartItems) ?> <?= count($cartItems) === 1 ? 'produto' : 'produtos' ?> no carrinho
        </p>
        <?php endif; ?>

        <!-- Title -->
        <h1 class="font-cursive text-4xl md:text-5xl lg:text-6xl text-cream drop-shadow-xl animate-on-scroll" data-animation="fade-up" data-delay="100">Carrinho de Compras</h1>
    </div>
</section>

<!-- Cart Section -->
<section class="py-12 lg:py-16 bg-cream-100 min-h-[60vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (!empty($cartErrors)): ?>
        <div class="bg-terracotta-50 border-l-4 border-terracotta-500 text-terracotta-800 px-6 py-4 rounded-r-lg mb-6 shadow-sm">
            <div class="flex items-start">
                <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <ul class="list-disc list-inside space-y-1">
                        <?php foreach ($cartErrors as $error): ?>
                        <li><?= e($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($cart->isEmpty()): ?>
        <!-- Empty Cart State -->
        <div class="bg-white rounded-2xl shadow-md p-12 lg:p-16 text-center animate-on-scroll" data-animation="fade-up">
            <div class="max-w-md mx-auto">
                <svg class="w-32 h-32 text-granite-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h2 class="font-serif text-3xl text-granite-800 mb-3">O seu carrinho está vazio</h2>
                <p class="text-granite-600 text-lg mb-8">Ainda não adicionou nenhum produto ao carrinho.</p>
                <a href="<?= $base ?>/loja/"
                   class="inline-flex items-center justify-center px-8 py-4 bg-olive-600 text-white font-medium rounded-lg hover:bg-olive-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Continuar a Comprar
                </a>
            </div>
        </div>

        <?php else: ?>
        <div class="lg:grid lg:grid-cols-3 lg:gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 animate-on-scroll" data-animation="fade-up">
                <div class="bg-white rounded-2xl shadow-md overflow-hidden" id="cart-items">
                    <!-- Table Header (Desktop) -->
                    <div class="hidden md:grid md:grid-cols-12 gap-6 px-6 py-4 bg-granite-50 border-b border-granite-200 text-sm font-semibold text-granite-700 uppercase tracking-wide">
                        <div class="col-span-5">Produto</div>
                        <div class="col-span-2 text-center">Preço</div>
                        <div class="col-span-2 text-center">Quantidade</div>
                        <div class="col-span-2 text-right">Subtotal</div>
                        <div class="col-span-1"></div>
                    </div>

                    <!-- Cart Items -->
                    <?php foreach ($cartItems as $item): ?>
                    <?php $product = $item['product']; ?>
                    <div class="cart-item border-b border-granite-100 last:border-b-0 p-6 hover:bg-cream-50 transition-colors duration-200" data-product-id="<?= $product->id ?>">
                        <div class="md:grid md:grid-cols-12 md:gap-6 md:items-center">
                            <!-- Product Info -->
                            <div class="col-span-5 flex items-center mb-4 md:mb-0">
                                <a href="<?= $base ?>/loja/produto/?slug=<?= e($product->slug) ?>"
                                   class="w-24 h-24 md:w-28 md:h-28 flex-shrink-0 bg-cream-100 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200 group">
                                    <?php if ($product->getPrimaryImage()): ?>
                                    <img src="<?= e(basePath() . $product->getPrimaryImage()) ?>"
                                         alt="<?= e($product->name) ?>"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                    <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-10 h-10 text-cream-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <?php endif; ?>
                                </a>
                                <div class="ml-5 flex-1 min-w-0">
                                    <a href="<?= $base ?>/loja/produto/?slug=<?= e($product->slug) ?>"
                                       class="font-medium text-granite-900 hover:text-olive-600 transition-colors text-lg leading-snug block line-clamp-2">
                                        <?= e($product->name) ?>
                                    </a>
                                    <?php if (!$product->isInStock()): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-terracotta-100 text-terracotta-800 mt-2">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Esgotado
                                    </span>
                                    <?php endif; ?>
                                    <!-- Mobile: Price -->
                                    <div class="md:hidden mt-2">
                                        <span class="text-olive-600 font-semibold text-lg"><?= formatPrice($product->getCurrentPrice()) ?></span>
                                        <?php if ($product->isOnSale()): ?>
                                        <span class="ml-2 text-sm text-granite-400 line-through"><?= formatPrice($product->price) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Price (Desktop) -->
                            <div class="hidden md:block col-span-2 text-center">
                                <span class="text-granite-800 font-semibold text-lg"><?= formatPrice($product->getCurrentPrice()) ?></span>
                                <?php if ($product->isOnSale()): ?>
                                <span class="block text-sm text-granite-400 line-through mt-1"><?= formatPrice($product->price) ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Quantity -->
                            <div class="col-span-2 flex items-center justify-between md:justify-center mb-4 md:mb-0">
                                <span class="md:hidden text-granite-600 font-medium">Quantidade:</span>
                                <div class="inline-flex items-center border-2 border-granite-200 rounded-lg bg-white shadow-sm hover:border-olive-400 transition-colors duration-200">
                                    <button type="button"
                                            class="qty-btn flex-shrink-0 w-10 h-10 flex items-center justify-center text-granite-600 hover:bg-olive-50 hover:text-olive-700 transition-colors duration-200 focus:outline-none"
                                            data-action="decrease"
                                            data-product-id="<?= $product->id ?>"
                                            aria-label="Diminuir quantidade">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/>
                                        </svg>
                                    </button>
                                    <input type="number"
                                           value="<?= $item['quantity'] ?>"
                                           min="1"
                                           max="<?= $product->track_inventory ? $product->stock_quantity : 99 ?>"
                                           class="item-qty w-12 text-center border-x-2 border-granite-200 h-10 focus:outline-none text-base font-medium text-granite-800 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                           data-product-id="<?= $product->id ?>"
                                           aria-label="Quantidade">
                                    <button type="button"
                                            class="qty-btn flex-shrink-0 w-10 h-10 flex items-center justify-center text-granite-600 hover:bg-olive-50 hover:text-olive-700 transition-colors duration-200 focus:outline-none"
                                            data-action="increase"
                                            data-product-id="<?= $product->id ?>"
                                            aria-label="Aumentar quantidade">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Subtotal & Remove -->
                            <!-- Subtotal & Remove -->
                            <div class="col-span-3 flex items-center justify-between md:grid md:grid-cols-3 md:gap-6">
                                <span class="item-subtotal text-granite-900 font-bold text-xl md:col-span-2 md:text-right"><?= formatPrice($item['subtotal']) ?></span>
                                <div class="md:col-span-1 md:justify-self-end">
                                    <button type="button"
                                            class="remove-item p-2 text-granite-400 hover:text-terracotta-600 hover:bg-terracotta-50 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-terracotta-400"
                                            data-product-id="<?= $product->id ?>"
                                            title="Remover produto"
                                            aria-label="Remover produto">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <!-- Cart Actions -->
                    <div class="px-6 py-5 bg-granite-50 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <a href="<?= $base ?>/loja/"
                           class="inline-flex items-center text-olive-600 hover:text-olive-700 font-medium transition-colors duration-200 group">
                            <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Continuar a Comprar
                        </a>
                        <button type="button"
                                id="clear-cart"
                                class="inline-flex items-center text-granite-600 hover:text-terracotta-600 font-medium text-sm transition-colors duration-200 group">
                            <svg class="w-4 h-4 mr-1.5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Limpar Carrinho
                        </button>
                    </div>
                </div>
            </div>

            <!-- Cart Summary Sidebar -->
            <div class="lg:col-span-1 mt-8 lg:mt-0 animate-on-scroll" data-animation="fade-up" data-delay="100">
                <div class="bg-white rounded-2xl shadow-md p-6 lg:sticky lg:top-24" id="cart-summary">
                    <h2 class="font-serif text-2xl text-granite-900 mb-6 pb-3 border-b border-granite-200">Resumo do Pedido</h2>

                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-granite-600">Subtotal</span>
                            <span class="text-granite-900 font-semibold text-lg" id="cart-subtotal"><?= formatPrice($cart->getSubtotal()) ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-granite-600">Portes de envio</span>
                            <?php if ($cart->getShippingCost() > 0): ?>
                            <span class="text-granite-900 font-semibold" id="cart-shipping"><?= formatPrice($cart->getShippingCost()) ?></span>
                            <?php else: ?>
                            <span class="text-olive-600 font-semibold" id="cart-shipping">Grátis</span>
                            <?php endif; ?>
                        </div>

                        <?php
                        $freeShippingThreshold = (float)setting('free_shipping_threshold', 50);
                        $amountToFreeShipping = $freeShippingThreshold - $cart->getSubtotal();
                        if ($amountToFreeShipping > 0):
                        ?>
                        <div class="pt-3 pb-2 px-4 bg-olive-50 rounded-lg border border-olive-200">
                            <p class="text-sm text-olive-800 font-medium mb-2">
                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                                    <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
                                </svg>
                                Faltam <strong><?= formatPrice($amountToFreeShipping) ?></strong> para portes grátis!
                            </p>
                            <div class="w-full bg-olive-200 rounded-full h-2.5">
                                <div class="bg-olive-600 h-2.5 rounded-full transition-all duration-300" style="width: <?= min(100, ($cart->getSubtotal() / $freeShippingThreshold) * 100) ?>%"></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="border-t-2 border-granite-200 pt-4 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-granite-800 text-lg">Total</span>
                            <span class="font-bold text-olive-600 text-2xl" id="cart-total"><?= formatPrice($cart->getTotal()) ?></span>
                        </div>
                        <p class="text-xs text-granite-500 mt-1 text-right">IVA incluído</p>
                    </div>

                    <a href="<?= $base ?>/loja/checkout/"
                       class="w-full inline-flex items-center justify-center px-6 py-4 bg-primary text-cream font-semibold text-lg rounded-xl hover:bg-primary/90 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 <?= empty($cartErrors) ? '' : 'opacity-50 pointer-events-none' ?>">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Finalizar Compra
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>

                    <!-- Payment Methods -->
                    <div class="mt-6 pt-6 border-t border-granite-200">
                        <p class="text-sm text-granite-600 font-medium mb-3">Métodos de pagamento aceites:</p>
                        <div class="flex items-center justify-center gap-3 flex-wrap">
                            <!-- MB WAY -->
                            <div class="bg-white border border-granite-200 rounded-lg px-2 py-1.5 shadow-sm flex items-center gap-2">
                                <div class="w-8 h-6 flex items-center justify-center flex-shrink-0">
                                    <img src="<?= asset('images/MB_WAY.jpg') ?>" alt="MB WAY" class="max-w-full max-h-full object-contain">
                                </div>
                            </div>
                            <!-- Multibanco -->
                            <div class="bg-white border border-granite-200 rounded-lg px-2 py-1.5 shadow-sm flex items-center gap-2">
                                <div class="w-8 h-6 flex items-center justify-center flex-shrink-0">
                                    <img src="<?= asset('images/multibanco.png') ?>" alt="Multibanco" class="max-w-full max-h-full object-contain">
                                </div>
                            </div>
                            <!-- Visa -->
                            <div class="bg-white border border-granite-200 rounded-lg px-2 py-1.5 shadow-sm flex items-center gap-2">
                                <div class="w-8 h-6 flex items-center justify-center flex-shrink-0">
                                    <img src="<?= asset('images/VISA.png') ?>" alt="Visa" class="max-w-full max-h-full object-contain">
                                </div>
                            </div>
                            <!-- Mastercard -->
                            <div class="bg-white border border-granite-200 rounded-lg px-2 py-1.5 shadow-sm flex items-center gap-2">
                                <div class="w-8 h-6 flex items-center justify-center flex-shrink-0">
                                    <img src="<?= asset('images/Mastercard.png') ?>" alt="Mastercard" class="max-w-full max-h-full object-contain">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Cart Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '<?= CSRF::getToken() ?>';

    // Update cart display
    function updateCartDisplay(cartData) {
        // Update header cart count
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            if (cartData.total_quantity > 0) {
                cartCount.textContent = cartData.total_quantity;
                cartCount.classList.remove('hidden');
            } else {
                cartCount.classList.add('hidden');
            }
        }

        // Update summary
        document.getElementById('cart-subtotal').textContent = formatPrice(cartData.subtotal);
        document.getElementById('cart-total').textContent = formatPrice(cartData.total);

        const shippingEl = document.getElementById('cart-shipping');
        if (cartData.shipping > 0) {
            shippingEl.textContent = formatPrice(cartData.shipping);
            shippingEl.className = 'text-granite-900 font-semibold';
        } else {
            shippingEl.textContent = 'Grátis';
            shippingEl.className = 'text-olive-600 font-semibold';
        }

        // Reload if cart becomes empty
        if (cartData.items.length === 0) {
            location.reload();
        }
    }

    function formatPrice(amount) {
        return new Intl.NumberFormat('pt-PT', {
            style: 'currency',
            currency: 'EUR'
        }).format(amount);
    }

    // Quantity buttons
    document.querySelectorAll('.qty-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const action = this.dataset.action;
            const input = document.querySelector(`.item-qty[data-product-id="${productId}"]`);
            let qty = parseInt(input.value) || 1;
            const max = parseInt(input.max) || 99;

            if (action === 'increase' && qty < max) {
                qty++;
            } else if (action === 'decrease' && qty > 1) {
                qty--;
            }

            input.value = qty;
            updateQuantity(productId, qty);
        });
    });

    // Manual quantity input
    document.querySelectorAll('.item-qty').forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.dataset.productId;
            let qty = parseInt(this.value) || 1;
            const max = parseInt(this.max) || 99;

            if (qty < 1) qty = 1;
            if (qty > max) qty = max;

            this.value = qty;
            updateQuantity(productId, qty);
        });
    });

    function updateQuantity(productId, quantity) {
        fetch('<?= $base ?>/api/cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify({
                action: 'update',
                product_id: productId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update item subtotal
                const item = data.cart.items.find(i => i.id == productId);
                if (item) {
                    const subtotalEl = document.querySelector(`.cart-item[data-product-id="${productId}"] .item-subtotal`);
                    if (subtotalEl) {
                        subtotalEl.textContent = formatPrice(item.subtotal);
                    }
                }
                updateCartDisplay(data.cart);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Remove item
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', async function() {
            const productId = this.dataset.productId;
            const productName = document.querySelector(`.cart-item[data-product-id="${productId}"] a.font-medium`)?.textContent?.trim() || 'este produto';

            const confirmed = await GiModal.confirm(
                `Tem a certeza que deseja remover "${productName}" do carrinho?`,
                'Remover Produto',
                { type: 'danger', confirmText: 'Sim, remover', cancelText: 'Cancelar' }
            );

            if (!confirmed) return;

            fetch('<?= $base ?>/api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken
                },
                body: JSON.stringify({
                    action: 'remove',
                    product_id: productId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove item from DOM
                    const itemEl = document.querySelector(`.cart-item[data-product-id="${productId}"]`);
                    if (itemEl) {
                        itemEl.style.opacity = '0';
                        setTimeout(() => {
                            itemEl.remove();
                            updateCartDisplay(data.cart);
                        }, 300);
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    // Clear cart
    const clearCartBtn = document.getElementById('clear-cart');
    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', async function() {
            const confirmed = await GiModal.confirm(
                'Todos os produtos serão removidos do seu carrinho. Esta ação não pode ser desfeita.',
                'Limpar Carrinho',
                { type: 'danger', confirmText: 'Sim, limpar tudo', cancelText: 'Cancelar' }
            );

            if (!confirmed) return;

            fetch('<?= $base ?>/api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken
                },
                body: JSON.stringify({
                    action: 'clear'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }
});
</script>

<style>
.cart-item {
    transition: opacity 0.3s ease;
}
</style>

<?php include INCLUDES_PATH . '/footer.php'; ?>
