<?php

require_once dirname(dirname(__DIR__)) . '/includes/init.php';

use Core\Language;
use Core\Cart;
use Core\Database;
use Models\Product;
use Models\ProductCategory;

Language::getInstance()->setLanguage(LANG_EN);

$lang = Language::getInstance();
$cart = Cart::getInstance();
$db = Database::getInstance();
$base = basePath();

$pageHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = 'shop' AND is_active = 1");
$heroMedia = $pageHero ? $db->fetch("SELECT * FROM media WHERE entity_type = 'hero' AND entity_id = ? AND is_cover = 1", [$pageHero['id']]) : null;
$heroImage = $heroMedia['file_path'] ?? 'images/MogadouroNeve.jpeg';
$heroOverlay = $pageHero['hero_overlay_opacity'] ?? 0.40;

$heroUrl = $heroImage[0] === '/' ? basePath() . $heroImage : asset($heroImage);

$products = Product::getAllActive();
$totalProducts = count($products);

$categories = ProductCategory::getAllActive();

$pageTitle = 'Shop';
$pageDescription = 'Discover the best regional products from Trás-os-Montes. Olive oil, honey, sausages, almonds and much more from Casa do Gi.';

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="relative h-screen md:h-[75vh] min-h-[600px] flex items-center bg-primary overflow-hidden">
    <div class="absolute inset-0">
         <div class="absolute inset-0 bg-cover bg-center bg-no-repeat bg-fixed"
             style="background-image: url('<?= $heroUrl ?>');">
        </div>
        <div class="absolute inset-0 bg-black" style="opacity: <?= $heroOverlay ?>"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-black/40"></div>
    </div>

    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <span class="inline-block text-accent text-lg font-medium tracking-[0.2em] uppercase mb-4 animate-on-scroll" data-animation="fade-up">
            Regional Products
        </span>

        <h1 class="font-cursive text-6xl md:text-7xl lg:text-8xl text-cream mb-6 drop-shadow-xl animate-on-scroll" data-animation="fade-up" data-delay="100">
            Our Shop
        </h1>

        <p class="text-xl md:text-2xl text-cream/90 max-w-2xl mx-auto font-light leading-relaxed animate-on-scroll" data-animation="fade-up" data-delay="200">
            Authentic flavors from Trás-os-Montes, carefully selected for your table.
        </p>
    </div>
</section>

<!-- Shop Content -->
<section class="py-20 bg-cream-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:flex lg:gap-12">

            <!-- Sidebar / Filters -->
            <aside class="lg:w-80 flex-shrink-0 mb-12 lg:mb-0 animate-on-scroll" data-animation="fade-up">
                <!-- Mobile Filter Toggle -->
                <button id="filter-toggle" class="lg:hidden w-full flex items-center justify-between bg-white px-6 py-4 rounded-full shadow-md mb-6 border border-cream-200 text-primary font-medium hover:bg-cream-50 transition-colors">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filter Products
                    </span>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="filter-sidebar" class="hidden lg:block bg-white rounded-3xl shadow-[0_10px_40px_rgba(0,0,0,0.05)] border border-cream-200 p-8 sticky top-32 transition-all duration-300">
                    <!-- Search -->
                    <div class="mb-10">
                        <span class="text-xs font-bold uppercase tracking-[0.2em] text-accent mb-4 block">Search</span>
                        <div class="relative group">
                            <input type="text"
                                   id="shop-search"
                                   placeholder="Search products..."
                                   class="w-full pl-5 pr-12 py-4 bg-cream-50 border border-cream-200 rounded-xl focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all outline-none text-sm placeholder:text-charcoal/40 font-medium text-primary">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center text-charcoal/40">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <button id="clear-search" class="absolute right-12 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center text-charcoal/30 hover:text-charcoal hover:bg-cream-100 rounded-lg hidden transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div>
                        <span class="text-xs font-bold uppercase tracking-[0.2em] text-accent mb-4 block">Categories</span>
                        <ul class="space-y-2">
                            <li>
                                <button type="button"
                                   class="shop-filter w-full flex items-center justify-between p-3 rounded-xl text-sm transition-all duration-300 group active bg-primary text-white shadow-md"
                                   data-filter="all">
                                    <span class="font-medium">All Categories</span>
                                    <span class="text-xs py-0.5 px-2 rounded-md bg-white/20 text-white transition-colors">
                                        <?= Product::countActive() ?>
                                    </span>
                                </button>
                            </li>
                            <?php foreach ($categories as $category): ?>
                            <li>
                                <button type="button"
                                    class="shop-filter w-full flex items-center justify-between p-3 rounded-xl text-sm transition-all duration-300 group text-charcoal hover:bg-cream-50 hover:pl-4 border border-transparent hover:border-cream-100"
                                    data-filter="<?= e($category->slug) ?>">
                                    <span class="font-medium"><?= e($category->name) ?></span>
                                    <span class="text-xs py-0.5 px-2 rounded-md bg-cream-100 text-charcoal/60 group-hover:bg-cream-200 transition-colors">
                                        <?= $category->getProductCount() ?>
                                    </span>
                                </button>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Clear Filters Message (Hidden by default) -->
                    <div id="active-filters-msg" class="hidden mt-8 pt-6 border-t border-cream-100 text-center">
                        <button onclick="resetFilters()" class="group inline-flex items-center gap-2 text-xs uppercase tracking-widest text-charcoal/50 hover:text-secondary font-bold transition-colors">
                            <span class="group-hover:-translate-x-1 transition-transform duration-300">←</span>
                            Clear filters
                        </button>
                    </div>

                    <!-- Need Help? -->
                    <div class="mt-12 p-6 bg-cream-50 rounded-2xl border border-cream-100 text-center">
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm text-secondary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <h4 class="font-serif text-primary mb-1">Need Help?</h4>
                        <p class="text-xs text-charcoal/60 mb-3">We are available to answer any questions.</p>
                        <a href="<?= $base ?>/en/contact/" class="text-xs font-bold uppercase tracking-widest text-secondary hover:text-secondary-700">Contact</a>
                    </div>
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="flex-1">
                <!-- Results Header -->
                <div class="mb-8 flex items-center justify-between animate-on-scroll" data-animation="fade-up">
                    <p class="text-charcoal/60 text-sm font-light">
                        Showing <strong id="visible-count" class="text-primary font-medium"><?= count($products) ?></strong> of <?= $totalProducts ?> products
                    </p>
                </div>

                <!-- Products Grid -->
                <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <?php $pidx = 0; foreach ($products as $product): ?>
                    <article class="product-card group bg-white rounded-3xl overflow-hidden shadow-[0_10px_30px_rgba(0,0,0,0.03)] hover:shadow-[0_20px_40px_rgba(0,0,0,0.08)] transition-all duration-500 hover:-translate-y-2 border border-cream-100 animate-on-scroll"
                             data-animation="fade-up"
                             data-delay="<?= min($pidx * 50, 300) ?>"
                             data-category="<?= e($product->category_slug ?? '') ?>"
                             data-title="<?= strtolower(e($product->name)) ?>"
                             data-description="<?= strtolower(e($product->short_description ?? '')) ?>">

                        <!-- Image Container -->
                        <a href="<?= $base ?>/en/shop/product/?slug=<?= e($product->slug) ?>" class="block aspect-[4/3] relative overflow-hidden bg-cream-50">
                            <?php if ($product->getPrimaryImage()): ?>
                            <img src="<?= e(basePath() . $product->getPrimaryImage()) ?>"
                                 alt="<?= e($product->name) ?>"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-in-out">
                            <?php else: ?>
                             <!-- Fallback Image -->
                            <div class="w-full h-full bg-cream-100 flex items-center justify-center group-hover:bg-cream-200 transition-colors">
                                <svg class="w-16 h-16 text-charcoal/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <?php endif; ?>

                            <!-- Overlay Gradient -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                            <!-- Badges -->
                            <div class="absolute top-4 left-4 flex flex-col gap-2">
                                <?php if ($product->isOnSale()): ?>
                                <span class="bg-red-500 text-white text-[10px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-full shadow-sm">
                                    -<?= $product->getDiscountPercentage() ?>%
                                </span>
                                <?php endif; ?>

                                <?php if ($product->is_featured): ?>
                                <span class="bg-accent text-white text-[10px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-full shadow-sm">
                                    Featured
                                </span>
                                <?php endif; ?>
                            </div>

                             <?php if (!$product->isInStock()): ?>
                                <div class="absolute inset-0 bg-white/60 backdrop-blur-[2px] flex items-center justify-center z-10">
                                    <span class="bg-primary text-white text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-full shadow-lg">Out of Stock</span>
                                </div>
                            <?php endif; ?>
                        </a>

                        <!-- Content -->
                        <div class="p-6 relative">
                            <!-- Category Tag -->
                             <?php if ($product->category_name): ?>
                                <span class="text-xs font-medium text-accent uppercase tracking-wider block mb-2"><?= e($product->category_name) ?></span>
                            <?php endif; ?>

                            <a href="<?= $base ?>/en/shop/product/?slug=<?= e($product->slug) ?>" class="block mb-3">
                                <h3 class="font-serif text-xl text-primary group-hover:text-secondary transition-colors line-clamp-2 min-h-[3.5rem]">
                                    <?= e($product->name) ?>
                                </h3>
                            </a>

                            <div class="flex items-end justify-between mt-4 border-t border-cream-100 pt-4">
                                <div class="flex flex-col">
                                    <?php if ($product->isOnSale()): ?>
                                    <span class="text-sm text-charcoal/40 line-through mb-1">
                                        <?= formatPrice($product->price) ?>
                                    </span>
                                    <?php endif; ?>
                                    <span class="font-serif text-xl font-medium text-primary">
                                        <?= formatPrice($product->getCurrentPrice()) ?>
                                    </span>
                                </div>

                                <?php if ($product->isInStock()): ?>
                                <button type="button"
                                        class="add-to-cart w-10 h-10 rounded-full bg-cream-100 text-primary flex items-center justify-center hover:bg-secondary hover:text-white transition-all duration-300 shadow-sm hover:shadow-lg hover:scale-110 group/btn"
                                        data-product-id="<?= $product->id ?>"
                                        title="Add to cart">
                                    <svg class="w-5 h-5 transition-transform group-hover/btn:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                    <?php $pidx++; endforeach; ?>
                </div>

                <!-- No Results Message (Hidden by default) -->
                <div id="no-results" class="hidden bg-white rounded-3xl shadow-sm p-16 text-center border border-cream-100 animate-on-scroll">
                    <div class="w-20 h-20 bg-cream-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-charcoal/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <h3 class="font-serif text-2xl text-primary mb-3">No products found</h3>
                    <p class="text-charcoal/60 mb-8 max-w-md mx-auto font-light">
                        We couldn't find any products matching your search criteria.
                    </p>
                    <button onclick="resetFilters()" class="inline-flex items-center px-8 py-3 bg-primary text-white font-medium rounded-full hover:bg-primary-700 transition-all shadow-lg hover:shadow-xl hover:-translate-y-1">
                        View all products
                    </button>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="pt-20 pb-20 mb-0 bg-white border-t border-cream-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-12">
            <div class="text-center group animate-on-scroll" data-animation="fade-up">
                <div class="w-16 h-16 bg-cream-50 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 border border-cream-100 text-secondary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                </div>
                <h3 class="font-serif text-xl text-primary mb-2">100% Regional Products</h3>
                <p class="text-charcoal/60 text-sm leading-relaxed font-light">Selected directly from small local producers of Trás-os-Montes.</p>
            </div>

            <div class="text-center group animate-on-scroll" data-animation="fade-up" data-delay="100">
                <div class="w-16 h-16 bg-cream-50 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 border border-cream-100 text-secondary">
                   <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="font-serif text-xl text-primary mb-2">Quality Guaranteed</h3>
                <p class="text-charcoal/60 text-sm leading-relaxed font-light">All products are tasted and approved by the Casa do Gi team.</p>
            </div>

            <div class="text-center group animate-on-scroll" data-animation="fade-up" data-delay="200">
                <div class="w-16 h-16 bg-cream-50 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 border border-cream-100 text-secondary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                </div>
                <h3 class="font-serif text-xl text-primary mb-2">Careful Shipping</h3>
                <p class="text-charcoal/60 text-sm leading-relaxed font-light">Sustainable packaging that preserves freshness and quality.</p>
            </div>

            <div class="text-center group animate-on-scroll" data-animation="fade-up" data-delay="300">
                <div class="w-16 h-16 bg-cream-50 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 border border-cream-100 text-secondary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                <h3 class="font-serif text-xl text-primary mb-2">Fair Trade</h3>
                <p class="text-charcoal/60 text-sm leading-relaxed font-light">By shopping in our store, you directly support producers and the local economy.</p>
            </div>
        </div>
    </div>
</section>

<!-- Shop Scripts -->
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

    // Filtering Logic
    const filterButtons = document.querySelectorAll('.shop-filter');
    const productCards = document.querySelectorAll('.product-card');
    const searchInput = document.getElementById('shop-search');
    const clearSearch = document.getElementById('clear-search');
    const visibleCount = document.getElementById('visible-count');
    const noResults = document.getElementById('no-results');
    const grid = document.getElementById('products-grid');
    const activeFiltersMsg = document.getElementById('active-filters-msg');

    let currentFilter = 'all';
    let searchTerm = '';

    function filterProducts() {
        let count = 0;

        productCards.forEach(card => {
            const category = card.dataset.category;
            const title = card.dataset.title || '';
            const description = card.dataset.description || '';

            const matchesFilter = currentFilter === 'all' || category === currentFilter;
            const matchesSearch = !searchTerm ||
                                title.includes(searchTerm) ||
                                description.includes(searchTerm);

            if (matchesFilter && matchesSearch) {
                card.style.display = 'block';
                // Trigger reflow/animation if needed, or just let CSS handle it
                card.style.animation = 'fadeInUp 0.4s ease forwards';
                count++;
            } else {
                card.style.display = 'none';
            }
        });

        visibleCount.textContent = count;

        if (count === 0) {
            noResults.classList.remove('hidden');
            grid.classList.add('hidden');
        } else {
            noResults.classList.add('hidden');
            grid.classList.remove('hidden');
        }

        // Show "Clear Filters" if filtering is active
        if (currentFilter !== 'all' || searchTerm !== '') {
            activeFiltersMsg.classList.remove('hidden');
        } else {
            activeFiltersMsg.classList.add('hidden');
        }
    }

    // Filter Buttons Click
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            currentFilter = this.dataset.filter;

            // Update active state
            filterButtons.forEach(btn => {
                btn.classList.remove('active', 'bg-primary', 'text-white', 'shadow-md');
                btn.classList.add('text-charcoal', 'hover:bg-cream-50', 'hover:pl-4', 'border', 'border-transparent', 'hover:border-cream-100');

                // Reset pills styling
                const pill = btn.querySelector('span:last-child');
                pill.classList.remove('bg-white/20', 'text-white');
                pill.classList.add('bg-cream-100', 'text-charcoal/60');
            });

            // Activate current
            this.classList.remove('text-charcoal', 'hover:bg-cream-50', 'hover:pl-4', 'border', 'border-transparent', 'hover:border-cream-100');
            this.classList.add('active', 'bg-primary', 'text-white', 'shadow-md');

            const pill = this.querySelector('span:last-child');
            pill.classList.remove('bg-cream-100', 'text-charcoal/60');
            pill.classList.add('bg-white/20', 'text-white');

            filterProducts();
        });
    });

    // Search Input
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            searchTerm = this.value.toLowerCase().trim();
            if (clearSearch) {
                clearSearch.classList.toggle('hidden', !searchTerm);
            }
            filterProducts();
        });
    }

    if (clearSearch) {
        clearSearch.addEventListener('click', function() {
            searchInput.value = '';
            searchTerm = '';
            this.classList.add('hidden');
            filterProducts();
        });
    }

    // Global Reset
    window.resetFilters = function() {
        currentFilter = 'all';
        searchTerm = '';
        if (searchInput) searchInput.value = '';
        if (clearSearch) clearSearch.classList.add('hidden');

        // Reset buttons visual state
        filterButtons.forEach(btn => {
            btn.classList.remove('active', 'bg-primary', 'text-white', 'shadow-md');
            btn.classList.add('text-charcoal', 'hover:bg-cream-50', 'hover:pl-4', 'border', 'border-transparent', 'hover:border-cream-100');

            const pill = btn.querySelector('span:last-child');
            pill.classList.remove('bg-white/20', 'text-white');
            pill.classList.add('bg-cream-100', 'text-charcoal/60');
        });

        // Activate "All" (assuming first button)
        if (filterButtons.length > 0) {
            const first = filterButtons[0];
            first.classList.remove('text-charcoal', 'hover:bg-cream-50', 'hover:pl-4', 'border', 'border-transparent', 'hover:border-cream-100');
            first.classList.add('active', 'bg-primary', 'text-white', 'shadow-md');
            const pill = first.querySelector('span:last-child');
            pill.classList.remove('bg-cream-100', 'text-charcoal/60');
            pill.classList.add('bg-white/20', 'text-white');
        }

        filterProducts();
    };

    // Add to cart functionality
    const addToCartButtons = document.querySelectorAll('.add-to-cart');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const btn = this;
            const originalContent = btn.innerHTML;

            // Disable button during request
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            fetch('<?= $base ?>/api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?= \Core\CSRF::getToken() ?>'
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
                    // Update cart count in header
                    if (window.updateCartCount) {
                        window.updateCartCount(data.cart.total_quantity);
                    } else {
                        // Fallback
                        const cartCount = document.querySelector('.cart-count');
                        if (cartCount) {
                            cartCount.textContent = data.cart.total_quantity;
                            cartCount.classList.remove('hidden');
                            cartCount.classList.add('animate-bounce');
                            setTimeout(() => cartCount.classList.remove('animate-bounce'), 1000);
                        }
                    }

                    // Visual feedback
                    btn.innerHTML = '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
                    btn.classList.remove('bg-cream-100', 'text-primary');
                    btn.classList.add('bg-green-600', 'text-white');

                    setTimeout(() => {
                        btn.innerHTML = originalContent;
                        btn.classList.remove('bg-green-600', 'text-white');
                        btn.classList.add('bg-cream-100', 'text-primary');
                        btn.disabled = false;
                        btn.classList.remove('opacity-75', 'cursor-not-allowed');
                    }, 2000);
                } else {
                    GiModal.alert(data.message || 'Error adding to cart', 'Error', { type: 'error' });
                     btn.disabled = false;
                     btn.classList.remove('opacity-75', 'cursor-not-allowed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                GiModal.alert('Error adding to cart. Please try again.', 'Error', { type: 'error' });
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            });
        });
    });
});
</script>

<style>
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<?php include INCLUDES_PATH . '/footer.php'; ?>
