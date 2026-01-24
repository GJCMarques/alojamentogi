<?php
/**
 * A Casa do Gi - Shop Main Page (Portuguese)
 */

require_once dirname(__DIR__) . '/includes/init.php';

use Core\Language;
use Core\Cart;
use Models\Product;
use Models\ProductCategory;

$lang = Language::getInstance();
$cart = Cart::getInstance();
$base = basePath();

// Get filter parameters
$categorySlug = isset($_GET['categoria']) ? sanitize($_GET['categoria']) : null;
$search = isset($_GET['pesquisa']) ? sanitize($_GET['pesquisa']) : null;
$page = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

// Get current category if filtered
$currentCategory = null;
if ($categorySlug) {
    $currentCategory = ProductCategory::findBySlug($categorySlug);
}

// Get products
if ($search) {
    $products = Product::search($search, 50);
    $totalProducts = count($products);
} else {
    $categoryId = $currentCategory ? $currentCategory->id : null;
    $products = Product::getAllActive($categoryId, $perPage, $offset);
    $totalProducts = Product::countActive($categoryId);
}

$totalPages = ceil($totalProducts / $perPage);

// Get all categories for filter
$categories = ProductCategory::getAllActive();

// Page configuration
$pageTitle = $currentCategory ? $currentCategory->name . ' - Loja' : 'Loja';
$pageDescription = 'Descubra os melhores produtos regionais de Trás-os-Montes. Azeite, mel, enchidos, amêndoas e muito mais da Casa do Gi.';

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
            Produtos Regionais
        </span>
        <h1 class="font-cursive text-6xl md:text-7xl lg:text-8xl text-cream mb-6 drop-shadow-lg">
            <?= $currentCategory ? e($currentCategory->name) : 'A Nossa Loja' ?>
        </h1>
        <p class="text-xl md:text-2xl text-cream/90 max-w-3xl mx-auto font-light leading-relaxed">
            <?php if ($currentCategory && $currentCategory->description): ?>
                <?= e($currentCategory->description) ?>
            <?php else: ?>
                Sabores autenticos de Tras-os-Montes, diretamente da nossa terra para a sua mesa.
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
                    <span class="font-medium text-charcoal">Filtros</span>
                    <svg class="w-5 h-5 text-charcoal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                </button>

                <div id="filter-sidebar" class="hidden lg:block bg-cream rounded-lg shadow-md p-6 border border-accent/20">
                    <!-- Search -->
                    <div class="mb-6">
                        <h3 class="font-medium text-primary mb-3">Pesquisar</h3>
                        <form action="" method="get" class="relative">
                            <input type="text"
                                   name="pesquisa"
                                   value="<?= e($search ?? '') ?>"
                                   placeholder="Procurar produtos..."
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
                        <h3 class="font-medium text-primary mb-3">Categorias</h3>
                        <ul class="space-y-2">
                            <li>
                                <a href="<?= $base ?>/loja/"
                                   class="flex items-center justify-between py-1 text-sm <?= !$currentCategory ? 'text-secondary font-medium' : 'text-charcoal hover:text-secondary' ?> transition-colors">
                                    <span>Todas as categorias</span>
                                    <span class="text-charcoal-500">(<?= Product::countActive() ?>)</span>
                                </a>
                            </li>
                            <?php foreach ($categories as $category): ?>
                            <li>
                                <a href="<?= $base ?>/loja/?categoria=<?= e($category->slug) ?>"
                                   class="flex items-center justify-between py-1 text-sm <?= ($currentCategory && $currentCategory->id === $category->id) ? 'text-secondary font-medium' : 'text-charcoal hover:text-secondary' ?> transition-colors">
                                    <span><?= e($category->name) ?></span>
                                    <span class="text-charcoal-500">(<?= $category->getProductCount() ?>)</span>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <?php if ($search || $currentCategory): ?>
                    <!-- Clear Filters -->
                    <div class="mt-6 pt-6 border-t border-accent/20">
                        <a href="<?= $base ?>/loja/" class="inline-flex items-center text-sm text-secondary hover:text-secondary-600 font-medium transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Limpar filtros
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
                            <?= $totalProducts ?> resultado(s) para "<?= e($search) ?>"
                        <?php else: ?>
                            A mostrar <?= count($products) ?> de <?= $totalProducts ?> produto(s)
                        <?php endif; ?>
                    </p>
                </div>

                <?php if (empty($products)): ?>
                <!-- No Products -->
                <div class="bg-cream rounded-lg shadow-md p-12 text-center border border-accent/20">
                    <svg class="w-16 h-16 text-charcoal-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <h3 class="font-serif text-xl text-primary mb-2">Nenhum produto encontrado</h3>
                    <p class="text-charcoal mb-6">
                        <?php if ($search): ?>
                            Tente uma pesquisa diferente ou explore as nossas categorias.
                        <?php else: ?>
                            Brevemente teremos produtos disponiveis nesta categoria.
                        <?php endif; ?>
                    </p>
                    <a href="<?= $base ?>/loja/" class="inline-flex items-center px-6 py-3 bg-secondary text-cream font-semibold rounded-lg hover:bg-secondary-600 transition-all shadow-md hover:shadow-lg hover:scale-105">
                        Ver todos os produtos
                    </a>
                </div>

                <?php else: ?>
                <!-- Products Grid -->
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($products as $product): ?>
                    <article class="product-card bg-cream rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all hover:scale-105 group border border-accent/20">
                        <!-- Product Image -->
                        <a href="<?= $base ?>/loja/produto/?slug=<?= e($product->slug) ?>" class="block aspect-square relative overflow-hidden">
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
                                <?php if ($product->isOnSale()): ?>
                                <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded shadow">
                                    -<?= $product->getDiscountPercentage() ?>%
                                </span>
                                <?php endif; ?>

                                <?php if ($product->is_featured): ?>
                                <span class="bg-secondary text-cream text-xs font-medium px-2 py-1 rounded shadow">
                                    Destaque
                                </span>
                                <?php endif; ?>

                                <?php if (!$product->isInStock()): ?>
                                <span class="bg-primary text-cream text-xs font-medium px-2 py-1 rounded shadow">
                                    Esgotado
                                </span>
                                <?php endif; ?>
                            </div>
                        </a>

                        <!-- Product Info -->
                        <div class="p-4">
                            <a href="<?= $base ?>/loja/produto/?slug=<?= e($product->slug) ?>" class="block">
                                <h3 class="font-serif text-lg text-primary mb-1 hover:text-secondary transition-colors">
                                    <?= e($product->name) ?>
                                </h3>
                            </a>

                            <?php if ($product->short_description): ?>
                            <p class="text-charcoal text-sm mb-3 line-clamp-2">
                                <?= e($product->short_description) ?>
                            </p>
                            <?php endif; ?>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-lg font-bold text-accent">
                                        <?= formatPrice($product->getCurrentPrice()) ?>
                                    </span>
                                    <?php if ($product->isOnSale()): ?>
                                    <span class="text-sm text-charcoal-500 line-through">
                                        <?= formatPrice($product->price) ?>
                                    </span>
                                    <?php endif; ?>
                                </div>

                                <?php if ($product->isInStock()): ?>
                                <button type="button"
                                        class="add-to-cart p-2 bg-secondary/10 text-secondary rounded-full hover:bg-secondary hover:text-cream transition-all shadow-sm hover:shadow-md hover:scale-110"
                                        data-product-id="<?= $product->id ?>"
                                        title="Adicionar ao carrinho">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </button>
                                <?php endif; ?>
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
                            <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => $page - 1])) ?>"
                               class="inline-flex items-center px-3 py-2 text-sm text-charcoal bg-cream rounded-lg border border-accent/30 hover:bg-cream-100 transition-colors shadow-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Anterior
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php
                        $start = max(1, $page - 2);
                        $end = min($totalPages, $page + 2);

                        if ($start > 1): ?>
                        <li>
                            <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => 1])) ?>"
                               class="inline-flex items-center px-3 py-2 text-sm text-charcoal bg-cream rounded-lg border border-accent/30 hover:bg-cream-100 transition-colors shadow-sm">
                                1
                            </a>
                        </li>
                        <?php if ($start > 2): ?>
                        <li><span class="px-2 text-charcoal-400">...</span></li>
                        <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $start; $i <= $end; $i++): ?>
                        <li>
                            <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => $i])) ?>"
                               class="inline-flex items-center px-3 py-2 text-sm rounded-lg border transition-all shadow-sm <?= $i === $page ? 'bg-secondary text-cream border-secondary font-semibold' : 'text-charcoal bg-cream border-accent/30 hover:bg-cream-100' ?>">
                                <?= $i ?>
                            </a>
                        </li>
                        <?php endfor; ?>

                        <?php if ($end < $totalPages): ?>
                        <?php if ($end < $totalPages - 1): ?>
                        <li><span class="px-2 text-charcoal-400">...</span></li>
                        <?php endif; ?>
                        <li>
                            <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => $totalPages])) ?>"
                               class="inline-flex items-center px-3 py-2 text-sm text-charcoal bg-cream rounded-lg border border-accent/30 hover:bg-cream-100 transition-colors shadow-sm">
                                <?= $totalPages ?>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if ($page < $totalPages): ?>
                        <li>
                            <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => $page + 1])) ?>"
                               class="inline-flex items-center px-3 py-2 text-sm text-charcoal bg-cream rounded-lg border border-accent/30 hover:bg-cream-100 transition-colors shadow-sm">
                                Seguinte
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

<!-- Features Section -->
<section class="py-12 bg-cream border-t border-accent/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="flex items-start">
                <div class="w-12 h-12 bg-secondary/10 rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                    <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-primary mb-1">Produtos Regionais</h3>
                    <p class="text-charcoal text-sm">Selecionados diretamente de produtores locais de Tras-os-Montes.</p>
                </div>
            </div>

            <div class="flex items-start">
                <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-primary mb-1">Pagamento Seguro</h3>
                    <p class="text-charcoal text-sm">MBWay, Multibanco e cartao de credito/debito.</p>
                </div>
            </div>

            <div class="flex items-start">
                <div class="w-12 h-12 bg-secondary/10 rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                    <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-primary mb-1">Envio Cuidado</h3>
                    <p class="text-charcoal text-sm">Embalagem especial para preservar a qualidade dos produtos.</p>
                </div>
            </div>

            <div class="flex items-start">
                <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-primary mb-1">Apoio ao Produtor</h3>
                    <p class="text-charcoal text-sm">Ao comprar, apoia diretamente os produtores da regiao.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Cart Script -->
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

            // Disable button during request
            btn.disabled = true;

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
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.cart.total_quantity;
                        cartCount.classList.remove('hidden');
                    }

                    // Visual feedback
                    btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
                    btn.classList.add('bg-secondary', 'text-cream');

                    setTimeout(() => {
                        btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>';
                        btn.classList.remove('bg-secondary', 'text-cream');
                    }, 2000);
                } else {
                    alert(data.message || 'Erro ao adicionar ao carrinho');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erro ao adicionar ao carrinho');
            })
            .finally(() => {
                btn.disabled = false;
            });
        });
    });
});
</script>

<?php include INCLUDES_PATH . '/footer.php'; ?>
