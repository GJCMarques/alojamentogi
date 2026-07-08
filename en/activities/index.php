<?php

require_once dirname(dirname(__DIR__)) . '/includes/init.php';

use Core\Language;
use Core\Database;

Language::getInstance()->setLanguage(LANG_EN);

$lang = Language::getInstance();
$db = Database::getInstance();
$base = basePath();

// Old activity slugs -> redirect to the new informative page
if (!empty($_GET['slug'])) {
    header('Location: ' . $base . '/en/activities/', true, 301);
    exit;
}

$pageHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = 'activities' AND is_active = 1");
$heroMedia = $pageHero ? $db->fetch("SELECT * FROM media WHERE entity_type = 'hero' AND entity_id = ? AND is_cover = 1", [$pageHero['id']]) : null;
$heroImage = $heroMedia['file_path'] ?? 'images/MogadouroAtividades.webp';
$heroOverlay = $pageHero['hero_overlay_opacity'] ?? 0.45;
$heroUrl = $heroImage[0] === '/' ? basePath() . $heroImage : asset($heroImage);

$pageTitle = 'What to Do in Mogadouro';
$pageDescription = 'Discover what to visit and do in Mogadouro and Trás-os-Montes through the Mogadouro Town Council and Tourism Office.';

$officialLinks = [
    [
        'title' => 'Mogadouro Town Council',
        'desc'  => 'Official municipal information: what to visit, heritage, events and contacts.',
        'url'   => 'https://www.mogadouro.pt/',
        'tag'   => 'Official Site',
    ],
    [
        'title' => 'Mogadouro Tourism Office',
        'desc'  => 'Interactive Tourism Office — points of interest, trails and visitor support.',
        'url'   => 'https://www.mogadouro.pt/pages/17',
        'tag'   => 'Tourism',
    ],
];

$guideLinks = [
    ['title' => 'Mogadouro itinerary — Vagamundos', 'url' => 'https://www.vagamundos.pt/visitar-mogadouro-roteiro/'],
    ['title' => 'Attractions around Mogadouro — Komoot', 'url' => 'https://www.komoot.com/pt-pt/guide/900754/atracoes-em-torno-de-mogadouro'],
    ['title' => 'Mogadouro — Tripadvisor', 'url' => 'https://www.tripadvisor.pt/Attractions-g1458520-Activities-Mogadouro_Braganca_District_Northern_Portugal.html'],
];

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
            Discover Mogadouro
        </span>
        <h1 class="font-cursive text-6xl md:text-7xl lg:text-8xl text-cream mb-6 drop-shadow-xl">
            What to Do
        </h1>
        <p class="text-xl md:text-2xl text-cream/90 max-w-2xl mx-auto font-light leading-relaxed">
            From nature to gastronomy, history and culture in Trás-os-Montes.
        </p>
    </div>
</section>

<!-- Highlight: Since 2023 in Mogadouro -->
<section class="bg-accent/15 border-y border-accent/30">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex items-center justify-center gap-3 text-center">
        <svg class="w-6 h-6 text-accent flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
        </svg>
        <p class="text-base md:text-lg font-semibold text-accent-700">
            In Mogadouro since 2023 — welcoming our guests with the very best of Trás-os-Montes.
        </p>
    </div>
</section>

<!-- Official Resources -->
<section class="py-16 lg:py-24 bg-cream-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="inline-block text-accent text-sm font-medium tracking-[0.2em] uppercase mb-3">Official Information</span>
            <h2 class="font-serif text-3xl md:text-4xl text-primary mb-4">Plan your visit</h2>
            <p class="text-charcoal/70 max-w-2xl mx-auto">
                For up-to-date activities, points of interest and events, consult the official entities of Mogadouro.
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <?php foreach ($officialLinks as $link): ?>
            <a href="<?= e($link['url']) ?>" target="_blank" rel="noopener noreferrer"
               class="group bg-white rounded-2xl border border-cream-200 p-8 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                <span class="inline-block self-start text-[11px] font-bold uppercase tracking-widest text-secondary bg-secondary/10 px-3 py-1 rounded-full mb-4"><?= e($link['tag']) ?></span>
                <h3 class="font-serif text-2xl text-primary mb-3 group-hover:text-secondary transition-colors"><?= e($link['title']) ?></h3>
                <p class="text-charcoal/70 leading-relaxed mb-6 flex-1"><?= e($link['desc']) ?></p>
                <span class="inline-flex items-center gap-2 text-secondary font-medium">
                    Visit
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </span>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Reference guides -->
        <div class="mt-14">
            <h3 class="font-serif text-xl text-primary mb-6 text-center">Useful guides and itineraries</h3>
            <div class="flex flex-wrap justify-center gap-3">
                <?php foreach ($guideLinks as $g): ?>
                <a href="<?= e($g['url']) ?>" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 px-5 py-3 bg-white border border-cream-200 rounded-xl text-charcoal/80 hover:border-secondary hover:text-secondary hover:shadow-md transition-all">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 010 5.656l-3 3a4 4 0 01-5.656-5.656l1.5-1.5m6.828-.828a4 4 0 010-5.656l3-3a4 4 0 015.656 5.656l-1.5 1.5"/>
                    </svg>
                    <span class="text-sm font-medium"><?= e($g['title']) ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
