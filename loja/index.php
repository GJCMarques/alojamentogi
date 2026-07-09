<?php

require_once dirname(__DIR__) . '/includes/init.php';

use Core\Language;
use Core\Database;

$lang = Language::getInstance();
$db = Database::getInstance();
$base = basePath();

// Loja em migração para serviço externo (shopk.it). Página informativa.
$shopExternalUrl = setting('shop_external_url', 'https://shopk.it/');

$pageHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = 'shop' AND is_active = 1");
$heroMedia = $pageHero ? $db->fetch("SELECT * FROM media WHERE entity_type = 'hero' AND entity_id = ? AND is_cover = 1", [$pageHero['id']]) : null;
$heroImage = $heroMedia['file_path'] ?? 'images/MogadouroNeve.webp';
$heroOverlay = $pageHero['hero_overlay_opacity'] ?? 0.45;

$heroUrl = $heroImage[0] === '/' ? basePath() . $heroImage : asset($heroImage);

$pageTitle = 'Loja';
$pageDescription = 'A loja online da Casa do Gi está em construção. Em breve poderá adquirir os nossos produtos regionais de Trás-os-Montes.';

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
            Produtos Regionais
        </span>
        <h1 class="font-cursive text-6xl md:text-7xl lg:text-8xl text-cream mb-6 drop-shadow-xl">
            A Nossa Loja
        </h1>
        <p class="text-xl md:text-2xl text-cream/90 max-w-2xl mx-auto font-light leading-relaxed">
            Sabores autênticos de Trás-os-Montes, em breve à distância de um clique.
        </p>
    </div>
</section>

<!-- Construction Notice -->
<section class="py-20 lg:py-28 bg-cream-50">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-block text-accent text-sm font-bold tracking-[0.2em] uppercase mb-4">Em Construção</span>
        <h2 class="text-3xl md:text-4xl text-primary mb-6">Loja Online em construção</h2>
        <p class="text-charcoal/75 text-lg leading-relaxed mb-6">
            Estamos a preparar a nossa loja online para lhe oferecer os melhores produtos regionais de
            Trás-os-Montes com toda a comodidade. Muito em breve poderá fazer as suas encomendas através
            da nossa nova plataforma.
        </p>
        <p class="text-charcoal/60">
            Para encomendas ou informações, <a href="<?= $base ?>/contactos/" class="text-secondary hover:underline">contacte-nos</a>.
        </p>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
