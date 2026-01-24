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

<!-- Hero Section -->
<section class="relative py-16 lg:py-24 bg-primary overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmZmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIj48cGF0aCBkPSJNMzYgMzRjMC0yLjIwOS0xLjc5MS00LTQtNHMtNCAxLjc5MS00IDQgMS43OTEgNCA0IDQgNC0xLjc5MSA0LTR6Ii8+PC9nPjwvZz48L3N2Zz4=')]"></div>
    </div>
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-primary/60 to-primary"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <span class="inline-block text-accent text-lg font-medium tracking-[0.2em] uppercase mb-4 animate-fade-in">
            Regional Products
        </span>
        <h1 class="font-cursive text-6xl md:text-7xl lg:text-8xl text-cream mb-6 drop-shadow-lg">
            <?= $currentCategory ? e($currentCategory->name) : 'Our Shop' ?>
        </h1>
        <p class="text-xl md:text-2xl text-cream/90 max-w-3xl mx-auto font-light leading-relaxed">
            <?php if ($currentCategory && $currentCategory->description): ?>
                <?= e($currentCategory->description) ?>
            <?php else: ?>
                Authentic flavors from Transmontana, straight from our land to your table.
            <?php endif; ?>
        </p>
    </div>
</section>

<!-- Shop Content -->
<section class="py-12 lg:py-16 bg-cream-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:flex lg:gap-8">

            <!-- Sidebar / Filters -->
            <aside class="lg:w-64 flex-shrink-0 mb-8 lg:mb-0">
                <!-- Mobile Filter Toggle -->
                <button id="filter-toggle" class="lg:hidden w-full flex items-center justify-between bg-cream px-4 py-3 rounded-lg shadow-md mb-4 border border-accent/20">
                    <span class="font-medium text-charcoal">Filters</span>
                    <svg class="w-5 h-5 text-charcoal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                </button>

                <div id="filter-sidebar" class="hidden lg:block bg-cream rounded-lg shadow-md p-6 border border-accent/20">
                    <!-- Search -->
                    <div class="mb-6">
                        <h3 class="font-medium text-primary mb-3">Search</h3>
                        <form action="" method="get" class="relative">
                            <input type="text"
                                   name="search"
                                   value="<?= e($search ?? '') ?>"
                                   placeholder="Search products..."
                                   class="w-full px-4 py-2 pr-10 border border-accent/30 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary bg-cream-50">
                            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-charcoal-500 hover:text-secondary transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                        </form>
                    </div>

                    <!-- Categories -->
                    <div>
                        <h3 class="font-medium text-primary mb-3">Categories</h3>
                        <nav class="space-y-2">
                            <a href="<?= $base ?>/en/shop/"
                               class="flex items-center justify-between py-1 text-sm <?= !$currentCategory ? 'text-secondary font-medium' : 'text-charcoal hover:text-secondary' ?> transition-colors">
                                <span>All Products</span>
                                <span class="text-charcoal-500">(<?= Product::countActive() ?>)</span>
                            </a>
                            <?php foreach ($categories as $category): ?>
                            <a href="<?= $base ?>/en/shop/?category=<?= e($category->slug) ?>"
                               class="flex items-center justify-between py-1 text-sm <?= $currentCategory && $currentCategory->id === $category->id ? 'text-secondary font-medium' : 'text-charcoal hover:text-secondary' ?> transition-colors">
                                <span><?= e($category->name) ?></span>
                                <span class="text-charcoal-500">(<?= $category->getProductCount() ?>)</span>
                            </a>
                            <?php endforeach; ?>
                        </nav>
                    </div>

                    <?php if ($search || $currentCategory): ?>
                    <!-- Clear Filters -->
                    <div class="mt-6 pt-6 border-t border-accent/20">
                        <a href="<?= $base ?>/en/shop/" class="inline-flex items-center text-sm text-secondary hover:text-secondary-600 font-medium transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Clear filters
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="flex-1">
                <!-- Results Header -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                    <p class="text-charcoal mb-2 sm:mb-0">
                        <?php if ($search): ?>
                            <?= $totalProducts ?> results for "<?= e($search) ?>"
                        <?php else: ?>
                            Showing <?= count($products) ?> of <?= $totalProducts ?> product(s)
                        <?php endif; ?>
                    </p>
                </div>

                <?php if (empty($products)): ?>
                <!-- No Products -->
                <div class="bg-cream rounded-lg shadow-md p-12 text-center border border-accent/20">
                    <svg class="w-16 h-16 text-charcoal-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <h3 class="font-serif text-xl text-primary mb-2">No products found</h3>
                    <p class="text-charcoal mb-6">
                        We're currently updating our store. Check back soon!
                    </p>
                    <a href="<?= $base ?>/en/shop/" class="inline-flex items-center px-6 py-3 bg-secondary text-cream font-semibold rounded-lg hover:bg-secondary-600 transition-all shadow-md hover:shadow-lg hover:scale-105">
                        View All Products
                    </a>
                </div>
                <?php else: ?>
                <!-- Products Grid -->
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($products as $product): ?>
                    <article class="product-card bg-cream rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all hover:scale-105 group border border-accent/20">
                        <!-- Product Image -->
                        <a href="<?= $base ?>/en/shop/product/?slug=<?= e($product->slug) ?>" class="block aspect-square relative overflow-hidden">
                            <?php if ($product->getPrimaryImage()): ?>
                            <img src="<?= e(UPLOADS_URL . '/products/' . $product->getPrimaryImage()) ?>"
                                 alt="<?= e($product->name) ?>"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-cream-200 to-cream-300 flex items-center justify-center">
                                <svg class="w-16 h-16 text-charcoal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                            </div>
                            <?php endif; ?>

                            <!-- Badges -->
                            <div class="absolute top-3 left-3 flex flex-col gap-2">
                                <?php if ($product->sale_price && $product->sale_price < $product->price): ?>
                                <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded shadow">
                                    SALE
                                </span>
                                <?php endif; ?>

                                <?php if ($product->is_featured): ?>
                                <span class="bg-secondary text-cream text-xs font-medium px-2 py-1 rounded shadow">
                                    Featured
                                </span>
                                <?php endif; ?>
                            </div>
                        </a>

                        <!-- Product Info -->
                        <div class="p-4">
                            <a href="<?= $base ?>/en/shop/product/?slug=<?= e($product->slug) ?>" class="block">
                                <h3 class="font-serif text-lg text-primary mb-1 hover:text-secondary transition-colors">
                                    <?= e($product->name) ?>
                                </h3>
                            </a>

                            <p class="text-charcoal text-sm mb-3 line-clamp-2">
                                <?= e($product->short_description) ?>
                            </p>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <?php if ($product->sale_price && $product->sale_price < $product->price): ?>
                                    <span class="text-lg font-bold text-accent">
                                        <?= formatPrice($product->sale_price) ?>
                                    </span>
                                    <span class="text-sm text-charcoal-500 line-through">
                                        <?= formatPrice($product->price) ?>
                                    </span>
                                    <?php else: ?>
                                    <span class="text-lg font-bold text-accent">
                                        <?= formatPrice($product->price) ?>
                                    </span>
                                    <?php endif; ?>
                                </div>

                                <button type="button"
                                        class="add-to-cart p-2 bg-secondary/10 text-secondary rounded-full hover:bg-secondary hover:text-cream transition-all shadow-sm hover:shadow-md hover:scale-110"
                                        data-product-id="<?= $product->id ?>"
                                        title="Add to cart">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <nav class="mt-12 flex justify-center">
                    <ul class="flex items-center gap-2">
                        <?php if ($page > 1): ?>
                        <li>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>"
                               class="inline-flex items-center px-3 py-2 text-sm text-charcoal bg-cream rounded-lg border border-accent/30 hover:bg-cream-100 transition-colors shadow-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Previous
                            </a>
                        </li>
                        <?php endif; ?>

                        <!-- Numbered pagination logic omitted for brevity, keeping simple Previous/Next -->
                        
                        <?php if ($page < $totalPages): ?>
                        <li>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>"
                               class="inline-flex items-center px-3 py-2 text-sm text-charcoal bg-cream rounded-lg border border-accent/30 hover:bg-cream-100 transition-colors shadow-sm">
                                Next
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php
$pageScripts = <<<JS
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile filter toggle
    const filterToggle = document.getElementById('filter-toggle');
    const filterSidebar = document.getElementById('filter-sidebar');

    if (filterToggle && filterSidebar) {
        filterToggle.addEventListener('click', function() {
            filterSidebar.classList.toggle('hidden');
        });
    }

    // Add to cart functionality
    const addToCartButtons = document.querySelectorAll('.add-to-cart');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const btn = this;

            btn.disabled = true;

            fetch('{$base}/api/cart.php', {
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
                    btn.classList.add('bg-secondary', 'text-cream');
                    setTimeout(() => {
                        btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>';
                        btn.classList.remove('bg-secondary', 'text-cream');
                    }, 2000);
                }
            })
            .finally(() => {
                btn.disabled = false;
            });
        });
    });
});
</script>
JS;
?>

<?php include INCLUDES_PATH . '/footer.php'; ?>
