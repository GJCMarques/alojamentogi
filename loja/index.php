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
<section class="py-20 lg:py-28 bg-cream-50 min-h-[50vh]">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="bg-white rounded-3xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-cream-200 p-10 md:p-14">
            <div class="w-20 h-20 mx-auto mb-8 bg-accent/10 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                </svg>
            </div>

            <span class="inline-block text-accent text-sm font-bold tracking-[0.2em] uppercase mb-4">Em Construção</span>
            <h2 class="font-serif text-3xl md:text-4xl text-primary mb-5">Loja Online em construção</h2>
            <p class="text-charcoal/70 text-lg leading-relaxed mb-8">
                Estamos a preparar a nossa loja online para lhe oferecer os melhores produtos regionais de
                Trás-os-Montes com toda a comodidade. Muito em breve poderá fazer as suas encomendas através
                da nossa nova plataforma.
            </p>

            <a href="<?= e($shopExternalUrl) ?>" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-secondary text-white font-medium rounded-xl hover:bg-secondary-700 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Visitar a nova loja
            </a>

            <p class="mt-8 text-sm text-charcoal/50">
                Para encomendas ou informações, <a href="<?= $base ?>/contactos/" class="text-secondary hover:underline">contacte-nos</a>.
            </p>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
