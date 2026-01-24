<?php
/**
 * A Casa do Gi - Single Product Page (Portuguese)
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';

use Core\Language;
use Core\Cart;
use Core\CSRF;
use Models\Product;

$lang = Language::getInstance();
$cart = Cart::getInstance();
$base = basePath();

// Get product slug
$slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : null;

if (!$slug) {
    redirect($base . '/loja/');
}

// Get product
$product = Product::findBySlug($slug);

if (!$product) {
    http_response_code(404);
    $pageTitle = 'Produto não encontrado';
    include INCLUDES_PATH . '/header.php';
    ?>
    <section class="py-20 bg-cream-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <svg class="w-24 h-24 text-granite-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h1 class="font-serif text-3xl text-granite-800 mb-4">Produto não encontrado</h1>
            <p class="text-granite-600 mb-8">O produto que procura não existe ou foi removido.</p>
            <a href="<?= $base ?>/loja/" class="inline-flex items-center px-6 py-3 bg-olive-600 text-white font-medium rounded hover:bg-olive-700 transition-colors">
                Voltar à Loja
            </a>
        </div>
    </section>
    <?php
    include INCLUDES_PATH . '/footer.php';
    exit;
}

// Get related products (same category)
$relatedProducts = Product::getAllActive($product->category_id, 4);
$relatedProducts = array_filter($relatedProducts, fn($p) => $p->id !== $product->id);
$relatedProducts = array_slice($relatedProducts, 0, 4);

// Page configuration
$pageTitle = $product->name;
$pageDescription = $product->short_description ?? substr(strip_tags($product->description ?? ''), 0, 160);

include INCLUDES_PATH . '/header.php';
?>

<!-- Breadcrumb -->
<nav class="bg-cream-200 py-3 border-b border-cream-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <ol class="flex items-center text-sm text-granite-500">
            <li><a href="<?= $base ?>/" class="hover:text-olive-600">Início</a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="<?= $base ?>/loja/" class="hover:text-olive-600">Loja</a></li>
            <?php if ($product->category): ?>
            <li><span class="mx-2">/</span></li>
            <li><a href="<?= $base ?>/loja/?categoria=<?= e($product->category->slug) ?>" class="hover:text-olive-600"><?= e($product->category->name) ?></a></li>
            <?php endif; ?>
            <li><span class="mx-2">/</span></li>
            <li class="text-granite-700 font-medium truncate"><?= e($product->name) ?></li>
        </ol>
    </div>
</nav>

<!-- Product Section -->
<section class="py-12 lg:py-16 bg-cream-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-2 lg:gap-12">

            <!-- Product Images -->
            <div class="mb-8 lg:mb-0">
                <!-- Main Image -->
                <div class="aspect-square bg-white rounded-lg overflow-hidden shadow-md mb-4" id="main-image-container">
                    <?php if ($product->getPrimaryImage()): ?>
                    <img src="<?= e(UPLOADS_URL . '/products/' . $product->getPrimaryImage()) ?>"
                         alt="<?= e($product->name) ?>"
                         class="w-full h-full object-contain"
                         id="main-image">
                    <?php else: ?>
                    <div class="w-full h-full bg-gradient-to-br from-cream-200 to-cream-300 flex items-center justify-center">
                        <svg class="w-24 h-24 text-cream-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <?php endif; ?>

                    <!-- Sale Badge -->
                    <?php if ($product->isOnSale()): ?>
                    <div class="absolute top-4 left-4 bg-terracotta-500 text-white text-sm font-bold px-3 py-1 rounded">
                        -<?= $product->getDiscountPercentage() ?>%
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Thumbnail Gallery -->
                <?php if (count($product->images) > 1): ?>
                <div class="grid grid-cols-5 gap-2">
                    <?php foreach ($product->images as $index => $image): ?>
                    <button type="button"
                            class="aspect-square bg-white rounded overflow-hidden border-2 transition-colors thumbnail-btn <?= $index === 0 ? 'border-olive-600' : 'border-transparent hover:border-olive-300' ?>"
                            data-image="<?= e(UPLOADS_URL . '/products/' . $image['image_path']) ?>">
                        <img src="<?= e(UPLOADS_URL . '/products/' . $image['image_path']) ?>"
                             alt="<?= e($product->name) ?> - Imagem <?= $index + 1 ?>"
                             class="w-full h-full object-cover">
                    </button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Product Info -->
            <div>
                <?php if ($product->category): ?>
                <a href="<?= $base ?>/loja/?categoria=<?= e($product->category->slug) ?>"
                   class="inline-block text-olive-600 text-sm font-medium uppercase tracking-wider mb-2 hover:text-olive-700">
                    <?= e($product->category->name) ?>
                </a>
                <?php endif; ?>

                <h1 class="font-serif text-3xl md:text-4xl text-granite-800 mb-4">
                    <?= e($product->name) ?>
                </h1>

                <?php if ($product->short_description): ?>
                <p class="text-lg text-granite-600 mb-6">
                    <?= e($product->short_description) ?>
                </p>
                <?php endif; ?>

                <!-- Price -->
                <div class="flex items-baseline gap-4 mb-6">
                    <span class="text-3xl font-bold text-olive-600">
                        <?= formatPrice($product->getCurrentPrice()) ?>
                    </span>
                    <?php if ($product->isOnSale()): ?>
                    <span class="text-xl text-granite-400 line-through">
                        <?= formatPrice($product->price) ?>
                    </span>
                    <span class="bg-terracotta-100 text-terracotta-700 text-sm font-medium px-2 py-1 rounded">
                        Poupa <?= formatPrice($product->price - $product->sale_price) ?>
                    </span>
                    <?php endif; ?>
                </div>

                <!-- Stock Status -->
                <div class="mb-6">
                    <?php if ($product->isInStock()): ?>
                    <span class="inline-flex items-center text-sm text-olive-600">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Em stock
                        <?php if ($product->track_inventory && $product->stock_quantity <= 5): ?>
                        <span class="text-terracotta-600 ml-2">(Apenas <?= $product->stock_quantity ?> disponíveis)</span>
                        <?php endif; ?>
                    </span>
                    <?php else: ?>
                    <span class="inline-flex items-center text-sm text-granite-500">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        Esgotado
                    </span>
                    <?php endif; ?>
                </div>

                <!-- Add to Cart Form -->
                <?php if ($product->isInStock()): ?>
                <form id="add-to-cart-form" class="mb-8">
                    <input type="hidden" name="product_id" value="<?= $product->id ?>">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">

                    <div class="flex items-center gap-4 mb-4">
                        <label for="quantity" class="text-granite-700 font-medium">Quantidade:</label>
                        <div class="flex items-center border border-cream-300 rounded">
                            <button type="button"
                                    class="qty-btn px-3 py-2 text-granite-600 hover:bg-cream-100"
                                    data-action="decrease">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </button>
                            <input type="number"
                                   name="quantity"
                                   id="quantity"
                                   value="1"
                                   min="1"
                                   max="<?= $product->track_inventory ? $product->stock_quantity : 99 ?>"
                                   class="w-16 text-center border-x border-cream-300 py-2 focus:outline-none">
                            <button type="button"
                                    class="qty-btn px-3 py-2 text-granite-600 hover:bg-cream-100"
                                    data-action="increase">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full sm:w-auto px-8 py-4 bg-olive-600 text-white font-medium rounded hover:bg-olive-700 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Adicionar ao Carrinho
                    </button>
                </form>
                <?php else: ?>
                <div class="bg-cream-200 rounded-lg p-6 mb-8">
                    <p class="text-granite-600">
                        Este produto está temporariamente esgotado. Por favor, contacte-nos para saber quando estará disponível.
                    </p>
                    <a href="<?= $base ?>/contactos/" class="inline-flex items-center mt-4 text-olive-600 font-medium hover:text-olive-700">
                        Contactar-nos
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                <?php endif; ?>

                <!-- Product Details -->
                <div class="border-t border-cream-300 pt-8">
                    <div class="mb-6">
                        <span class="text-granite-500 text-sm">SKU: <?= e($product->sku) ?></span>
                    </div>

                    <!-- Features -->
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="flex items-center text-sm text-granite-600">
                            <svg class="w-5 h-5 text-olive-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Pagamento seguro
                        </div>
                        <div class="flex items-center text-sm text-granite-600">
                            <svg class="w-5 h-5 text-olive-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                            </svg>
                            Envio para Portugal
                        </div>
                        <div class="flex items-center text-sm text-granite-600">
                            <svg class="w-5 h-5 text-olive-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                            </svg>
                            Embalagem cuidada
                        </div>
                        <div class="flex items-center text-sm text-granite-600">
                            <svg class="w-5 h-5 text-olive-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            Apoio ao cliente
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description -->
        <?php if ($product->description): ?>
        <div class="mt-12 lg:mt-16 border-t border-cream-300 pt-12">
            <h2 class="font-serif text-2xl text-granite-800 mb-6">Descrição do Produto</h2>
            <div class="prose prose-lg text-granite-600 max-w-none">
                <?= nl2br(e($product->description)) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Related Products -->
<?php if (!empty($relatedProducts)): ?>
<section class="py-12 lg:py-16 bg-cream-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="font-serif text-2xl md:text-3xl text-granite-800 mb-8">Produtos Relacionados</h2>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($relatedProducts as $related): ?>
            <article class="product-card bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition-shadow group">
                <a href="<?= $base ?>/loja/produto/?slug=<?= e($related->slug) ?>" class="block aspect-square relative overflow-hidden">
                    <?php if ($related->getPrimaryImage()): ?>
                    <img src="<?= e(UPLOADS_URL . '/products/' . $related->getPrimaryImage()) ?>"
                         alt="<?= e($related->name) ?>"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <?php else: ?>
                    <div class="w-full h-full bg-gradient-to-br from-cream-200 to-cream-300 flex items-center justify-center">
                        <svg class="w-12 h-12 text-cream-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <?php endif; ?>

                    <?php if ($related->isOnSale()): ?>
                    <span class="absolute top-2 left-2 bg-terracotta-500 text-white text-xs font-bold px-2 py-1 rounded">
                        -<?= $related->getDiscountPercentage() ?>%
                    </span>
                    <?php endif; ?>
                </a>
                <div class="p-4">
                    <a href="<?= $base ?>/loja/produto/?slug=<?= e($related->slug) ?>" class="block">
                        <h3 class="font-serif text-lg text-granite-800 hover:text-olive-600 transition-colors">
                            <?= e($related->name) ?>
                        </h3>
                    </a>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="font-bold text-olive-600"><?= formatPrice($related->getCurrentPrice()) ?></span>
                        <?php if ($related->isOnSale()): ?>
                        <span class="text-sm text-granite-400 line-through"><?= formatPrice($related->price) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image gallery
    const mainImage = document.getElementById('main-image');
    const thumbnails = document.querySelectorAll('.thumbnail-btn');

    thumbnails.forEach(btn => {
        btn.addEventListener('click', function() {
            const newSrc = this.dataset.image;
            if (mainImage) {
                mainImage.src = newSrc;
            }

            thumbnails.forEach(t => {
                t.classList.remove('border-olive-600');
                t.classList.add('border-transparent');
            });
            this.classList.remove('border-transparent');
            this.classList.add('border-olive-600');
        });
    });

    // Quantity buttons
    const quantityInput = document.getElementById('quantity');
    const qtyButtons = document.querySelectorAll('.qty-btn');

    if (quantityInput) {
        const maxQty = parseInt(quantityInput.max) || 99;

        qtyButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const action = this.dataset.action;
                let currentQty = parseInt(quantityInput.value) || 1;

                if (action === 'increase' && currentQty < maxQty) {
                    quantityInput.value = currentQty + 1;
                } else if (action === 'decrease' && currentQty > 1) {
                    quantityInput.value = currentQty - 1;
                }
            });
        });
    }

    // Add to cart form
    const form = document.getElementById('add-to-cart-form');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const productId = this.querySelector('[name="product_id"]').value;
            const quantity = parseInt(this.querySelector('[name="quantity"]').value) || 1;
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> A adicionar...';

            fetch('<?= $base ?>/api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?= CSRF::getToken() ?>'
                },
                body: JSON.stringify({
                    action: 'add',
                    product_id: productId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count in header
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.cart.total_quantity;
                        cartCount.classList.remove('hidden');
                    }

                    // Success feedback
                    submitBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Adicionado!';
                    submitBtn.classList.remove('bg-olive-600', 'hover:bg-olive-700');
                    submitBtn.classList.add('bg-olive-700');

                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.classList.add('bg-olive-600', 'hover:bg-olive-700');
                        submitBtn.classList.remove('bg-olive-700');
                        submitBtn.disabled = false;
                    }, 2000);
                } else {
                    alert(data.message || 'Erro ao adicionar ao carrinho');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erro ao adicionar ao carrinho');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});
</script>

<?php include INCLUDES_PATH . '/footer.php'; ?>
