<?php
/**
 * A Casa do Gi - Shopping Cart Page (Portuguese)
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';

use Core\Cart;
use Core\CSRF;

$base = basePath();

$cart = Cart::getInstance();
$cartItems = $cart->getItems();
$cartErrors = $cart->validate();

// Remove invalid items messages (except empty cart)
$cartErrors = array_filter($cartErrors, fn($e) => $e !== 'O carrinho está vazio.');

// Page configuration
$pageTitle = 'Carrinho de Compras';
$pageDescription = 'Reveja os produtos no seu carrinho de compras da Casa do Gi.';

include INCLUDES_PATH . '/header.php';
?>

<!-- Breadcrumb -->
<nav class="bg-cream-200 py-3 border-b border-cream-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <ol class="flex items-center text-sm text-granite-500">
            <li><a href="<?= $base ?>/" class="hover:text-olive-600">Início</a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="<?= $base ?>/loja/" class="hover:text-olive-600">Loja</a></li>
            <li><span class="mx-2">/</span></li>
            <li class="text-granite-700 font-medium">Carrinho</li>
        </ol>
    </div>
</nav>

<!-- Cart Section -->
<section class="py-12 lg:py-16 bg-cream-100 min-h-[60vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="font-serif text-3xl md:text-4xl text-granite-800 mb-8">Carrinho de Compras</h1>

        <?php if (!empty($cartErrors)): ?>
        <div class="bg-terracotta-50 border border-terracotta-200 text-terracotta-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                <?php foreach ($cartErrors as $error): ?>
                <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if ($cart->isEmpty()): ?>
        <!-- Empty Cart -->
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <svg class="w-24 h-24 text-granite-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <h2 class="font-serif text-2xl text-granite-700 mb-4">O seu carrinho está vazio</h2>
            <p class="text-granite-500 mb-8">Ainda não adicionou nenhum produto ao carrinho.</p>
            <a href="<?= $base ?>/loja/" class="inline-flex items-center px-6 py-3 bg-olive-600 text-white font-medium rounded hover:bg-olive-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Continuar a Comprar
            </a>
        </div>

        <?php else: ?>
        <div class="lg:grid lg:grid-cols-3 lg:gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden" id="cart-items">
                    <!-- Table Header (Desktop) -->
                    <div class="hidden md:grid md:grid-cols-12 gap-4 px-6 py-4 bg-cream-50 border-b border-cream-200 text-sm font-medium text-granite-600">
                        <div class="col-span-6">Produto</div>
                        <div class="col-span-2 text-center">Preço</div>
                        <div class="col-span-2 text-center">Quantidade</div>
                        <div class="col-span-2 text-right">Subtotal</div>
                    </div>

                    <!-- Cart Items -->
                    <?php foreach ($cartItems as $item): ?>
                    <?php $product = $item['product']; ?>
                    <div class="cart-item border-b border-cream-200 p-4 md:p-6" data-product-id="<?= $product->id ?>">
                        <div class="md:grid md:grid-cols-12 md:gap-4 md:items-center">
                            <!-- Product Info -->
                            <div class="col-span-6 flex items-center mb-4 md:mb-0">
                                <a href="<?= $base ?>/loja/produto/?slug=<?= e($product->slug) ?>" class="w-20 h-20 flex-shrink-0 bg-cream-100 rounded overflow-hidden">
                                    <?php if ($product->getPrimaryImage()): ?>
                                    <img src="<?= e(UPLOADS_URL . '/products/' . $product->getPrimaryImage()) ?>"
                                         alt="<?= e($product->name) ?>"
                                         class="w-full h-full object-cover">
                                    <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-cream-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <?php endif; ?>
                                </a>
                                <div class="ml-4 flex-1">
                                    <a href="<?= $base ?>/loja/produto/?slug=<?= e($product->slug) ?>" class="font-medium text-granite-800 hover:text-olive-600">
                                        <?= e($product->name) ?>
                                    </a>
                                    <?php if (!$product->isInStock()): ?>
                                    <span class="block text-sm text-terracotta-600 mt-1">Esgotado</span>
                                    <?php endif; ?>
                                    <!-- Mobile: Price -->
                                    <div class="md:hidden mt-1 text-olive-600 font-medium">
                                        <?= formatPrice($product->getCurrentPrice()) ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Price (Desktop) -->
                            <div class="hidden md:block col-span-2 text-center">
                                <span class="text-granite-700 font-medium"><?= formatPrice($product->getCurrentPrice()) ?></span>
                                <?php if ($product->isOnSale()): ?>
                                <span class="block text-sm text-granite-400 line-through"><?= formatPrice($product->price) ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Quantity -->
                            <div class="col-span-2 flex items-center justify-between md:justify-center mb-4 md:mb-0">
                                <span class="md:hidden text-granite-600">Quantidade:</span>
                                <div class="flex items-center border border-cream-300 rounded">
                                    <button type="button"
                                            class="qty-btn px-2 py-1 text-granite-600 hover:bg-cream-100"
                                            data-action="decrease"
                                            data-product-id="<?= $product->id ?>">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                        </svg>
                                    </button>
                                    <input type="number"
                                           value="<?= $item['quantity'] ?>"
                                           min="1"
                                           max="<?= $product->track_inventory ? $product->stock_quantity : 99 ?>"
                                           class="item-qty w-12 text-center border-x border-cream-300 py-1 focus:outline-none text-sm"
                                           data-product-id="<?= $product->id ?>">
                                    <button type="button"
                                            class="qty-btn px-2 py-1 text-granite-600 hover:bg-cream-100"
                                            data-action="increase"
                                            data-product-id="<?= $product->id ?>">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Subtotal & Remove -->
                            <div class="col-span-2 flex items-center justify-between md:justify-end">
                                <span class="item-subtotal text-granite-800 font-bold"><?= formatPrice($item['subtotal']) ?></span>
                                <button type="button"
                                        class="remove-item ml-4 text-granite-400 hover:text-terracotta-600"
                                        data-product-id="<?= $product->id ?>"
                                        title="Remover">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <!-- Cart Actions -->
                    <div class="px-6 py-4 bg-cream-50 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <a href="<?= $base ?>/loja/" class="inline-flex items-center text-olive-600 hover:text-olive-700">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Continuar a Comprar
                        </a>
                        <button type="button" id="clear-cart" class="text-granite-500 hover:text-terracotta-600 text-sm">
                            Limpar Carrinho
                        </button>
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="lg:col-span-1 mt-8 lg:mt-0">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-24" id="cart-summary">
                    <h2 class="font-serif text-xl text-granite-800 mb-6">Resumo</h2>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-granite-600">Subtotal</span>
                            <span class="text-granite-800 font-medium" id="cart-subtotal"><?= formatPrice($cart->getSubtotal()) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-granite-600">Portes de envio</span>
                            <?php if ($cart->getShippingCost() > 0): ?>
                            <span class="text-granite-800" id="cart-shipping"><?= formatPrice($cart->getShippingCost()) ?></span>
                            <?php else: ?>
                            <span class="text-olive-600 font-medium" id="cart-shipping">Grátis</span>
                            <?php endif; ?>
                        </div>
                        <?php
                        $freeShippingThreshold = (float)setting('free_shipping_threshold', 50);
                        $amountToFreeShipping = $freeShippingThreshold - $cart->getSubtotal();
                        if ($amountToFreeShipping > 0):
                        ?>
                        <div class="pt-2">
                            <p class="text-sm text-olive-600">
                                Faltam <strong><?= formatPrice($amountToFreeShipping) ?></strong> para portes grátis!
                            </p>
                            <div class="w-full bg-cream-200 rounded-full h-2 mt-2">
                                <div class="bg-olive-500 h-2 rounded-full" style="width: <?= min(100, ($cart->getSubtotal() / $freeShippingThreshold) * 100) ?>%"></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="border-t border-cream-200 mt-4 pt-4">
                        <div class="flex justify-between text-lg">
                            <span class="font-medium text-granite-800">Total</span>
                            <span class="font-bold text-olive-600" id="cart-total"><?= formatPrice($cart->getTotal()) ?></span>
                        </div>
                        <p class="text-xs text-granite-500 mt-1">IVA incluído</p>
                    </div>

                    <a href="<?= $base ?>/loja/checkout/"
                       class="mt-6 w-full inline-flex items-center justify-center px-6 py-4 bg-olive-600 text-white font-medium rounded hover:bg-olive-700 transition-colors <?= empty($cartErrors) ? '' : 'opacity-50 pointer-events-none' ?>">
                        Finalizar Compra
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>

                    <!-- Payment Methods -->
                    <div class="mt-6 pt-6 border-t border-cream-200">
                        <p class="text-sm text-granite-500 mb-3">Métodos de pagamento aceites:</p>
                        <div class="flex items-center gap-3">
                            <div class="bg-cream-100 rounded px-2 py-1">
                                <span class="text-xs font-medium text-granite-600">MBWay</span>
                            </div>
                            <div class="bg-cream-100 rounded px-2 py-1">
                                <span class="text-xs font-medium text-granite-600">Multibanco</span>
                            </div>
                            <div class="bg-cream-100 rounded px-2 py-1">
                                <span class="text-xs font-medium text-granite-600">Cartão</span>
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
            shippingEl.className = 'text-granite-800';
        } else {
            shippingEl.textContent = 'Grátis';
            shippingEl.className = 'text-olive-600 font-medium';
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
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;

            if (!confirm('Tem a certeza que deseja remover este produto?')) {
                return;
            }

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
        clearCartBtn.addEventListener('click', function() {
            if (!confirm('Tem a certeza que deseja limpar o carrinho?')) {
                return;
            }

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
