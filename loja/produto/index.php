<?php
/**
 * A Casa do Gi - Single Product Page (Portuguese)
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';

use Core\Language;
use Core\Cart;
use Core\CSRF;
use Core\Database;
use Models\Product;

$lang = Language::getInstance();
$cart = Cart::getInstance();
$db = Database::getInstance();
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

// Get hero image from database (product detail page hero, fallback to shop)
$pageHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = 'product_detail' AND is_active = 1");
if (!$pageHero) {
    $pageHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = 'shop' AND is_active = 1");
}
$heroMedia = $pageHero ? $db->fetch("SELECT * FROM media WHERE entity_type = 'hero' AND entity_id = ? AND is_cover = 1", [$pageHero['id']]) : null;
$heroImage = $heroMedia['file_path'] ?? 'images/MogadouroNeve.jpeg';
$heroOverlay = $pageHero['hero_overlay_opacity'] ?? 0.40;
$heroUrl = $heroImage[0] === '/' ? basePath() . $heroImage : asset($heroImage);

// Get related products (same category)
$relatedProducts = Product::getAllActive($product->category_id, 4);
$relatedProducts = array_filter($relatedProducts, fn($p) => $p->id !== $product->id);
$relatedProducts = array_slice($relatedProducts, 0, 4);

// Page configuration
$pageTitle = $product->name;
$pageDescription = $product->short_description ?? substr(strip_tags($product->description ?? ''), 0, 160);

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section with Breadcrumbs -->
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
                    <a href="<?= $base ?>/" class="hover:text-white transition-colors">Início</a>
                </li>
                <li>
                    <svg class="w-4 h-4 mx-2 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </li>
                <li>
                    <a href="<?= $base ?>/loja/" class="hover:text-white transition-colors">Loja</a>
                </li>
                <?php if ($product->category): ?>
                <li>
                    <svg class="w-4 h-4 mx-2 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </li>
                <li>
                    <a href="<?= $base ?>/loja/?categoria=<?= e($product->category->slug) ?>" class="hover:text-white transition-colors">
                        <?= e($product->category->name) ?>
                    </a>
                </li>
                <?php endif; ?>
                <li>
                    <svg class="w-4 h-4 mx-2 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </li>
                <li class="text-white font-medium">
                    <?= e($product->name) ?>
                </li>
            </ol>
        </nav>

        <!-- Category Tag -->
        <?php if ($product->category): ?>
        <a href="<?= $base ?>/loja/?categoria=<?= e($product->category->slug) ?>"
           class="inline-block text-accent text-sm font-medium tracking-[0.15em] uppercase mb-4 hover:text-accent/80 bg-white/10 px-4 py-1 rounded-full backdrop-blur-sm animate-on-scroll" data-animation="fade-up" data-delay="100">
            <?= e($product->category->name) ?>
        </a>
        <?php endif; ?>

        <!-- Product Title -->
        <h1 class="font-cursive text-4xl md:text-5xl lg:text-6xl text-cream drop-shadow-xl animate-on-scroll" data-animation="fade-up" data-delay="200">
            <?= e($product->name) ?>
        </h1>
    </div>
</section>

<!-- Product Section -->
<section class="py-12 lg:py-16 bg-cream-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-2 lg:gap-12">

            <!-- Product Images -->
            <div class="mb-8 lg:mb-0 animate-on-scroll" data-animation="fade-up">
                <!-- Main Image with Navigation -->
                <div class="relative aspect-square bg-white rounded-2xl overflow-hidden shadow-lg mb-4 group" id="main-image-container">
                    <?php if ($product->getPrimaryImage()): ?>
                    <img src="<?= e(basePath() . $product->getPrimaryImage()) ?>"
                         alt="<?= e($product->name) ?>"
                         class="w-full h-full object-cover cursor-pointer"
                         id="main-image"
                         data-index="0">
                    <?php else: ?>
                    <div class="w-full h-full bg-gradient-to-br from-cream-200 to-cream-300 flex items-center justify-center">
                        <svg class="w-24 h-24 text-cream-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <?php endif; ?>

                    <!-- Sale Badge -->
                    <?php if ($product->isOnSale()): ?>
                    <div class="absolute top-4 right-4 bg-terracotta text-white text-sm font-bold px-4 py-2 rounded-full shadow-lg">
                        -<?= $product->getDiscountPercentage() ?>%
                    </div>
                    <?php endif; ?>

                    <!-- Navigation Arrows (only show if multiple images) -->
                    <?php if (count($product->images) > 1): ?>
                    <button type="button"
                            class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/90 hover:bg-white text-charcoal rounded-full shadow-lg flex items-center justify-center transition-all opacity-0 group-hover:opacity-100"
                            id="prev-image">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button type="button"
                            class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/90 hover:bg-white text-charcoal rounded-full shadow-lg flex items-center justify-center transition-all opacity-0 group-hover:opacity-100"
                            id="next-image">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <?php endif; ?>
                </div>

                <!-- Thumbnail Gallery -->
                <?php if (count($product->images) > 1): ?>
                <div class="grid grid-cols-5 gap-2">
                    <?php foreach ($product->images as $index => $image): ?>
                    <button type="button"
                            class="aspect-square bg-white rounded-lg overflow-hidden border-2 transition-all thumbnail-btn <?= $index === 0 ? 'border-primary ring-2 ring-primary/20' : 'border-cream-200 hover:border-secondary' ?>"
                            data-image="<?= e(basePath() . $image['image_path']) ?>"
                            data-index="<?= $index ?>">
                        <img src="<?= e(basePath() . $image['image_path']) ?>"
                             alt="<?= e($product->name) ?> - Imagem <?= $index + 1 ?>"
                             class="w-full h-full object-cover">
                    </button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Product Info -->
            <div class="animate-on-scroll" data-animation="fade-up" data-delay="100">
                <!-- Category Link -->
                <?php if ($product->category): ?>
                <a href="<?= $base ?>/loja/?categoria=<?= e($product->category->slug) ?>"
                   class="inline-block text-secondary text-sm font-semibold uppercase tracking-wider mb-3 hover:text-primary transition-colors">
                    <?= e($product->category->name) ?>
                </a>
                <?php endif; ?>

                <!-- Product Name -->
                <h2 class="font-serif text-3xl md:text-4xl text-primary mb-4">
                    <?= e($product->name) ?>
                </h2>

                <!-- Short Description -->
                <?php if ($product->short_description): ?>
                <p class="text-lg text-charcoal/80 mb-6 leading-relaxed">
                    <?= e($product->short_description) ?>
                </p>
                <?php endif; ?>

                <!-- Price -->
                <div class="flex items-baseline gap-4 mb-6 pb-6 border-b border-cream-200">
                    <span class="text-4xl font-bold text-primary">
                        <?= formatPrice($product->getCurrentPrice()) ?>
                    </span>
                    <?php if ($product->isOnSale()): ?>
                    <span class="text-xl text-charcoal/40 line-through">
                        <?= formatPrice($product->price) ?>
                    </span>
                    <span class="bg-terracotta/10 text-terracotta text-sm font-semibold px-3 py-1.5 rounded-full">
                        Poupa <?= formatPrice($product->price - $product->sale_price) ?>
                    </span>
                    <?php endif; ?>
                </div>

                <!-- Stock Status -->
                <div class="mb-6">
                    <?php if ($product->isInStock()): ?>
                    <div class="flex items-center text-sm">
                        <div class="flex items-center justify-center w-6 h-6 bg-secondary/10 rounded-full mr-2">
                            <svg class="w-4 h-4 text-secondary" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="font-medium text-secondary">Em stock</span>
                        <?php if ($product->track_inventory && $product->stock_quantity <= 5): ?>
                        <span class="text-terracotta ml-3 font-medium">(Apenas <?= $product->stock_quantity ?> disponíveis)</span>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <div class="flex items-center text-sm">
                        <div class="flex items-center justify-center w-6 h-6 bg-charcoal/10 rounded-full mr-2">
                            <svg class="w-4 h-4 text-charcoal/60" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="font-medium text-charcoal/60">Esgotado</span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Add to Cart Form -->
                <?php if ($product->isInStock()): ?>
                <form id="add-to-cart-form" class="mb-8">
                    <input type="hidden" name="product_id" value="<?= $product->id ?>">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">

                    <div class="flex items-center gap-4 mb-6">
                        <label for="quantity" class="text-charcoal font-semibold text-sm">Quantidade:</label>
                        <div class="flex items-center border-2 border-cream-200 rounded-xl overflow-hidden bg-white">
                            <button type="button"
                                    class="qty-btn px-4 py-3 text-charcoal hover:bg-cream-50 transition-colors"
                                    data-action="decrease">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </button>
                            <input type="number"
                                   name="quantity"
                                   id="quantity"
                                   value="1"
                                   min="1"
                                   max="<?= $product->track_inventory ? $product->stock_quantity : 99 ?>"
                                   class="w-16 text-center border-x-2 border-cream-200 py-3 focus:outline-none font-semibold text-primary">
                            <button type="button"
                                    class="qty-btn px-4 py-3 text-charcoal hover:bg-cream-50 transition-colors"
                                    data-action="increase">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full px-8 py-5 bg-primary text-white font-semibold text-lg rounded-xl hover:bg-primary/90 transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-3 group">
                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Adicionar ao Carrinho
                    </button>
                </form>
                <?php else: ?>
                <div class="bg-cream-100 rounded-xl p-6 mb-8 border border-cream-200">
                    <p class="text-charcoal/80 mb-4">
                        Este produto está temporariamente esgotado. Por favor, contacte-nos para saber quando estará disponível.
                    </p>
                    <a href="<?= $base ?>/contactos/" class="inline-flex items-center text-secondary font-semibold hover:text-primary transition-colors">
                        Contactar-nos
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                <?php endif; ?>

                <!-- Product Features -->
                <div class="bg-white rounded-xl p-6 border border-cream-200">
                    <div class="mb-4">
                        <span class="text-charcoal/60 text-sm font-medium">SKU: <?= e($product->sku) ?></span>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="flex items-start text-sm text-charcoal">
                            <div class="flex-shrink-0 w-10 h-10 bg-secondary/10 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-semibold text-primary">Pagamento seguro</div>
                                <div class="text-xs text-charcoal/60 mt-0.5">Transações protegidas</div>
                            </div>
                        </div>
                        <div class="flex items-start text-sm text-charcoal">
                            <div class="flex-shrink-0 w-10 h-10 bg-secondary/10 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-semibold text-primary">Envio para Portugal</div>
                                <div class="text-xs text-charcoal/60 mt-0.5">Entrega rápida e segura</div>
                            </div>
                        </div>
                        <div class="flex items-start text-sm text-charcoal">
                            <div class="flex-shrink-0 w-10 h-10 bg-secondary/10 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-semibold text-primary">Embalagem cuidada</div>
                                <div class="text-xs text-charcoal/60 mt-0.5">Produtos bem protegidos</div>
                            </div>
                        </div>
                        <div class="flex items-start text-sm text-charcoal">
                            <div class="flex-shrink-0 w-10 h-10 bg-secondary/10 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-semibold text-primary">Apoio ao cliente</div>
                                <div class="text-xs text-charcoal/60 mt-0.5">Estamos aqui para ajudar</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description -->
        <?php if ($product->description): ?>
        <div class="mt-16 bg-white rounded-2xl p-8 lg:p-12 shadow-sm border border-cream-200 animate-on-scroll" data-animation="fade-up">
            <h2 class="font-serif text-3xl text-primary mb-6 pb-4 border-b border-cream-200">Descrição do Produto</h2>
            <div class="prose prose-lg text-charcoal/80 max-w-none leading-relaxed">
                <?= nl2br(e($product->description)) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Related Products -->
<?php if (!empty($relatedProducts)): ?>
<section class="py-16 lg:py-20 bg-cream-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 animate-on-scroll" data-animation="fade-up">
            <span class="inline-block text-accent text-sm font-medium tracking-[0.2em] uppercase mb-3">
                Descubra Mais
            </span>
            <h2 class="font-serif text-3xl md:text-4xl text-primary">Produtos Relacionados</h2>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php $ridx = 0; foreach ($relatedProducts as $related): ?>
            <article class="product-card bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 group border border-cream-200 animate-on-scroll" data-animation="fade-up" data-delay="<?= $ridx * 100 ?>">
                <a href="<?= $base ?>/loja/produto/?slug=<?= e($related->slug) ?>" class="block aspect-square relative overflow-hidden">
                    <?php if ($related->getPrimaryImage()): ?>
                    <img src="<?= e(basePath() . $related->getPrimaryImage()) ?>"
                         alt="<?= e($related->name) ?>"
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <?php else: ?>
                    <div class="w-full h-full bg-gradient-to-br from-cream-200 to-cream-300 flex items-center justify-center">
                        <svg class="w-16 h-16 text-cream-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <?php endif; ?>

                    <?php if ($related->isOnSale()): ?>
                    <span class="absolute top-3 right-3 bg-terracotta text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
                        -<?= $related->getDiscountPercentage() ?>%
                    </span>
                    <?php endif; ?>
                </a>
                <div class="p-5">
                    <a href="<?= $base ?>/loja/produto/?slug=<?= e($related->slug) ?>" class="block">
                        <h3 class="font-serif text-lg text-primary group-hover:text-secondary transition-colors line-clamp-2 min-h-[3.5rem]">
                            <?= e($related->name) ?>
                        </h3>
                    </a>
                    <div class="flex items-center gap-2 mt-3">
                        <span class="font-bold text-xl text-primary"><?= formatPrice($related->getCurrentPrice()) ?></span>
                        <?php if ($related->isOnSale()): ?>
                        <span class="text-sm text-charcoal/40 line-through"><?= formatPrice($related->price) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
            <?php $ridx++; endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Lightbox Modal -->
<div id="lightbox" class="fixed inset-0 bg-black/95 z-50 hidden flex items-center justify-center p-4">
    <button type="button"
            class="absolute top-6 right-6 w-12 h-12 bg-white/10 hover:bg-white/20 text-white rounded-full flex items-center justify-center transition-colors z-10"
            id="close-lightbox">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>

    <button type="button"
            class="absolute left-6 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/10 hover:bg-white/20 text-white rounded-full flex items-center justify-center transition-colors"
            id="lightbox-prev">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>

    <img src="" alt="<?= e($product->name) ?>" class="max-w-full max-h-full object-contain" id="lightbox-image">

    <button type="button"
            class="absolute right-6 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/10 hover:bg-white/20 text-white rounded-full flex items-center justify-center transition-colors"
            id="lightbox-next">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </button>
</div>

<!-- Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image gallery data
    const images = [
        <?php foreach ($product->images as $index => $image): ?>
        {
            src: '<?= e(basePath() . $image['image_path']) ?>',
            alt: '<?= e($product->name) ?> - Imagem <?= $index + 1 ?>'
        }<?= $index < count($product->images) - 1 ? ',' : '' ?>
        <?php endforeach; ?>
    ];

    let currentImageIndex = 0;

    // Main image and thumbnails
    const mainImage = document.getElementById('main-image');
    const thumbnails = document.querySelectorAll('.thumbnail-btn');

    // Update active thumbnail
    function updateActiveThumbnail(index) {
        thumbnails.forEach((thumb, i) => {
            if (i === index) {
                thumb.classList.remove('border-cream-200');
                thumb.classList.add('border-primary', 'ring-2', 'ring-primary/20');
            } else {
                thumb.classList.add('border-cream-200');
                thumb.classList.remove('border-primary', 'ring-2', 'ring-primary/20');
            }
        });
    }

    // Change main image
    function changeImage(index) {
        if (images[index] && mainImage) {
            currentImageIndex = index;
            mainImage.src = images[index].src;
            mainImage.dataset.index = index;
            updateActiveThumbnail(index);
        }
    }

    // Thumbnail clicks
    thumbnails.forEach((btn, index) => {
        btn.addEventListener('click', function() {
            changeImage(index);
        });
    });

    // Previous/Next buttons
    const prevBtn = document.getElementById('prev-image');
    const nextBtn = document.getElementById('next-image');

    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            const newIndex = currentImageIndex > 0 ? currentImageIndex - 1 : images.length - 1;
            changeImage(newIndex);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            const newIndex = currentImageIndex < images.length - 1 ? currentImageIndex + 1 : 0;
            changeImage(newIndex);
        });
    }

    // Lightbox functionality
    const lightbox = document.getElementById('lightbox');
    const lightboxImage = document.getElementById('lightbox-image');
    const closeLightbox = document.getElementById('close-lightbox');
    const lightboxPrev = document.getElementById('lightbox-prev');
    const lightboxNext = document.getElementById('lightbox-next');

    function openLightbox(index) {
        if (images[index]) {
            lightbox.classList.remove('hidden');
            lightboxImage.src = images[index].src;
            lightboxImage.alt = images[index].alt;
            currentImageIndex = index;
            document.body.style.overflow = 'hidden';
        }
    }

    function closeLightboxModal() {
        lightbox.classList.add('hidden');
        document.body.style.overflow = '';
    }

    function showLightboxImage(index) {
        if (images[index]) {
            currentImageIndex = index;
            lightboxImage.src = images[index].src;
            lightboxImage.alt = images[index].alt;
        }
    }

    // Main image click opens lightbox
    if (mainImage) {
        mainImage.addEventListener('click', function() {
            const index = parseInt(this.dataset.index) || 0;
            openLightbox(index);
        });
    }

    // Lightbox controls
    if (closeLightbox) {
        closeLightbox.addEventListener('click', closeLightboxModal);
    }

    if (lightboxPrev) {
        lightboxPrev.addEventListener('click', function() {
            const newIndex = currentImageIndex > 0 ? currentImageIndex - 1 : images.length - 1;
            showLightboxImage(newIndex);
        });
    }

    if (lightboxNext) {
        lightboxNext.addEventListener('click', function() {
            const newIndex = currentImageIndex < images.length - 1 ? currentImageIndex + 1 : 0;
            showLightboxImage(newIndex);
        });
    }

    // Click outside image to close
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) {
            closeLightboxModal();
        }
    });

    // Escape key to close
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !lightbox.classList.contains('hidden')) {
            closeLightboxModal();
        }
        if (!lightbox.classList.contains('hidden')) {
            if (e.key === 'ArrowLeft') {
                const newIndex = currentImageIndex > 0 ? currentImageIndex - 1 : images.length - 1;
                showLightboxImage(newIndex);
            } else if (e.key === 'ArrowRight') {
                const newIndex = currentImageIndex < images.length - 1 ? currentImageIndex + 1 : 0;
                showLightboxImage(newIndex);
            }
        }
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
                    submitBtn.classList.remove('bg-primary', 'hover:bg-primary/90');
                    submitBtn.classList.add('bg-secondary');

                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.classList.add('bg-primary', 'hover:bg-primary/90');
                        submitBtn.classList.remove('bg-secondary');
                        submitBtn.disabled = false;
                    }, 2000);
                } else {
                    GiModal.alert(data.message || 'Erro ao adicionar ao carrinho', 'Erro', { type: 'error' });
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                GiModal.alert('Erro ao adicionar ao carrinho. Por favor tente novamente.', 'Erro', { type: 'error' });
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});
</script>

<?php include INCLUDES_PATH . '/footer.php'; ?>
