<?php
/**
 * A Casa do Gi - Shop Page (English)
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';

use Core\Language;
use Core\Cart;
use Models\Product;
use Models\ProductCategory;

// Force English language
Language::getInstance()->setLanguage(LANG_EN);
$lang = Language::getInstance();
$cart = Cart::getInstance();
$base = basePath();

// Get filter parameters
$categorySlug = isset($_GET['category']) ? sanitize($_GET['category']) : null;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : null;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

// Get current category if filtered
$currentCategory = null;
if ($categorySlug) {
    $currentCategory = ProductCategory::findBySlug($categorySlug);
}

// Get products
if ($search) {
    $products = Product::search($search, $perPage, $offset);
    $totalProducts = Product::searchCount($search);
} elseif ($currentCategory) {
    $products = Product::getAllActive($currentCategory->id, $perPage, $offset);
    $totalProducts = Product::countActive($currentCategory->id);
} else {
    $products = Product::getAllActive(null, $perPage, $offset);
    $totalProducts = Product::countActive();
}

$totalPages = ceil($totalProducts / $perPage);

// Get all categories
$categories = ProductCategory::getAllActive();

// Page configuration
$pageTitle = $currentCategory ? $currentCategory->name : 'Shop';
$pageDescription = 'Discover authentic regional products from Mogadouro and Tras-os-Montes. Olive oil, honey, cured meats and more.';

include INCLUDES_PATH . '/header.php';
?>

<!-- Page Header -->
<section class="bg-cream-200 py-12 border-b border-cream-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="font-serif text-3xl md:text-4xl text-granite-800 mb-4">
            <?= $currentCategory ? e($currentCategory->name) : 'Regional Products' ?>
        </h1>
        <p class="text-granite-600 max-w-2xl">
            Authentic flavors from Mogadouro and Tras-os-Montes, carefully selected for you.
        </p>
    </div>
</section>

<!-- Main Content -->
<section class="py-12 bg-cream-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-4 lg:gap-8">
            <!-- Sidebar -->
            <aside class="hidden lg:block">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-24">
                    <h3 class="font-semibold text-granite-800 mb-4">Categories</h3>
                    <nav class="space-y-2">
                        <a href="<?= $base ?>/en/shop/"
                           class="block py-2 px-3 rounded text-sm <?= !$currentCategory ? 'bg-olive-100 text-olive-700 font-medium' : 'text-granite-600 hover:bg-cream-100' ?>">
                            All Products
                        </a>
                        <?php foreach ($categories as $category): ?>
                        <a href="<?= $base ?>/en/shop/?category=<?= e($category->slug) ?>"
                           class="block py-2 px-3 rounded text-sm <?= $currentCategory && $currentCategory->id === $category->id ? 'bg-olive-100 text-olive-700 font-medium' : 'text-granite-600 hover:bg-cream-100' ?>">
                            <?= e($category->name) ?>
                        </a>
                        <?php endforeach; ?>
                    </nav>
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="lg:col-span-3">
                <?php if (empty($products)): ?>
                <div class="text-center py-16">
                    <svg class="w-16 h-16 mx-auto text-granite-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <h3 class="font-serif text-xl text-granite-700 mb-2">No products found</h3>
                    <p class="text-granite-500 mb-6">We're currently updating our store. Check back soon!</p>
                    <a href="<?= $base ?>/en/shop/" class="inline-flex items-center px-6 py-3 bg-olive-600 text-white font-medium rounded hover:bg-olive-700 transition-colors">
                        View All Products
                    </a>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($products as $product): ?>
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden group">
                        <a href="<?= $base ?>/en/shop/product/?slug=<?= e($product->slug) ?>" class="block aspect-square relative overflow-hidden">
                            <img src="<?= e(UPLOADS_URL . '/products/' . $product->getPrimaryImage()) ?>"
                                 alt="<?= e($product->name) ?>"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                 loading="lazy">
                            <?php if ($product->sale_price && $product->sale_price < $product->price): ?>
                            <span class="absolute top-3 left-3 bg-terracotta-500 text-white text-xs font-bold px-2 py-1 rounded">
                                SALE
                            </span>
                            <?php endif; ?>
                        </a>
                        <div class="p-4">
                            <a href="<?= $base ?>/en/shop/product/?slug=<?= e($product->slug) ?>" class="block">
                                <h3 class="font-medium text-granite-800 mb-2 group-hover:text-olive-600 transition-colors">
                                    <?= e($product->name) ?>
                                </h3>
                            </a>
                            <p class="text-sm text-granite-500 mb-3 line-clamp-2"><?= e($product->short_description) ?></p>
                            <div class="flex items-center justify-between">
                                <div>
                                    <?php if ($product->sale_price && $product->sale_price < $product->price): ?>
                                    <span class="text-granite-400 line-through text-sm"><?= formatPrice($product->price) ?></span>
                                    <span class="text-terracotta-600 font-bold ml-1"><?= formatPrice($product->sale_price) ?></span>
                                    <?php else: ?>
                                    <span class="text-granite-800 font-bold"><?= formatPrice($product->price) ?></span>
                                    <?php endif; ?>
                                </div>
                                <button type="button"
                                        class="add-to-cart p-2 bg-olive-100 text-olive-600 rounded hover:bg-olive-600 hover:text-white transition-colors"
                                        data-product-id="<?= $product->id ?>"
                                        title="Add to cart">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php
$pageScripts = <<<JS
<script>
    const addToCartButtons = document.querySelectorAll('.add-to-cart');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const btn = this;

            btn.disabled = true;

            fetch('<?= $base ?>/api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    action: 'add',
                    product_id: productId,
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.cart.total_quantity;
                        cartCount.classList.remove('hidden');
                    }
                    btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
                    setTimeout(() => {
                        btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>';
                    }, 2000);
                }
            })
            .finally(() => {
                btn.disabled = false;
            });
        });
    });
</script>
JS;
?>

<?php include INCLUDES_PATH . '/footer.php'; ?>
