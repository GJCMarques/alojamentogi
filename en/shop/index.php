<?php

require_once dirname(dirname(__DIR__)) . '/includes/init.php';

use Core\Language;
use Core\Database;

Language::getInstance()->setLanguage(LANG_EN);
$lang = Language::getInstance();
$db = Database::getInstance();
$base = basePath();

// Shop migrating to an external service (shopk.it). Informative page.
$shopExternalUrl = setting('shop_external_url', 'https://shopk.it/');

$pageHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = 'shop' AND is_active = 1");
$heroMedia = $pageHero ? $db->fetch("SELECT * FROM media WHERE entity_type = 'hero' AND entity_id = ? AND is_cover = 1", [$pageHero['id']]) : null;
$heroImage = $heroMedia['file_path'] ?? 'images/MogadouroNeve.webp';
$heroOverlay = $pageHero['hero_overlay_opacity'] ?? 0.45;

$heroUrl = $heroImage[0] === '/' ? basePath() . $heroImage : asset($heroImage);

$pageTitle = 'Shop';
$pageDescription = 'The Casa do Gi online shop is under construction. Soon you will be able to buy our regional products from Trás-os-Montes.';

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="relative h-[70vh] min-h-[520px] flex items-center bg-primary overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat"
             style="background-image: url('<?= $heroUrl ?>');"></div>
        <div class="absolute inset-0 bg-black" style="opacity: <?= $heroOverlay ?>"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-black/40"></div>
    </div>

    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <span class="inline-block text-accent text-lg font-medium tracking-[0.2em] uppercase mb-4">
            Regional Products
        </span>
        <h1 class="font-cursive text-6xl md:text-7xl lg:text-8xl text-cream mb-6 drop-shadow-xl">
            Our Shop
        </h1>
        <p class="text-xl md:text-2xl text-cream/90 max-w-2xl mx-auto font-light leading-relaxed">
            Authentic flavours from Trás-os-Montes, soon just a click away.
        </p>
    </div>
</section>

<!-- Construction Notice -->
<section class="py-20 lg:py-28 bg-cream-50 min-h-[50vh]">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="bg-white rounded-3xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-cream-200 p-10 md:p-14">
            <div class="w-20 h-20 mx-auto mb-8 bg-accent/10 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                </svg>
            </div>

            <span class="inline-block text-accent text-sm font-bold tracking-[0.2em] uppercase mb-4">Under Construction</span>
            <h2 class="font-serif text-3xl md:text-4xl text-primary mb-5">Online shop under construction</h2>
            <p class="text-charcoal/70 text-lg leading-relaxed mb-8">
                We are preparing our online shop to offer you the finest regional products from Trás-os-Montes
                with total convenience. Very soon you will be able to place your orders through our new platform.
            </p>

            <a href="<?= e($shopExternalUrl) ?>" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-secondary text-white font-medium rounded-xl hover:bg-secondary-700 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Visit the new shop
            </a>

            <p class="mt-8 text-sm text-charcoal/50">
                For orders or information, <a href="<?= $base ?>/en/contact/" class="text-secondary hover:underline">contact us</a>.
            </p>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
