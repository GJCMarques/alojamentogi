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
<section class="py-20 lg:py-28 bg-cream-50">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-block text-accent text-sm font-bold tracking-[0.2em] uppercase mb-4">Under Construction</span>
        <h2 class="text-3xl md:text-4xl text-primary mb-6">Online shop under construction</h2>
        <p class="text-charcoal/75 text-lg leading-relaxed mb-6">
            We are preparing our online shop to offer you the finest regional products from Trás-os-Montes
            with total convenience. Very soon you will be able to place your orders through our new platform.
        </p>
        <p class="text-charcoal/60">
            For orders or information, <a href="<?= $base ?>/en/contact/" class="text-secondary hover:underline">contact us</a>.
        </p>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
