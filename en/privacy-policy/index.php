<?php

require_once dirname(dirname(__DIR__)) . '/includes/init.php';

use Core\Language;
use Core\Database;

Language::getInstance()->setLanguage('en');
$lang = Language::getInstance();
$db = Database::getInstance();
$base = basePath();

$pageHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = 'privacy_policy' AND is_active = 1");
$heroMedia = $pageHero ? $db->fetch("SELECT * FROM media WHERE entity_type = 'hero' AND entity_id = ? AND is_cover = 1", [$pageHero['id']]) : null;
$heroImage = $heroMedia['file_path'] ?? 'images/MogadouroSobre.png';
$heroOverlay = $pageHero['hero_overlay_opacity'] ?? 0.40;

$heroUrl = $heroImage[0] === '/' ? basePath() . $heroImage : asset($heroImage);

$sections = $db->fetchAll(
    "SELECT s.*, st.title, st.content
     FROM legal_sections s
     LEFT JOIN legal_section_translations st ON s.id = st.section_id AND st.language_id = ?
     WHERE s.page = 'privacy' AND s.is_active = 1
     ORDER BY s.sort_order ASC",
    [$lang->getCurrentLangId()]
);

$pageTitle = content('privacy_hero_title');
$pageDescription = content('privacy_hero_subtitle');

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="relative h-screen md:h-[60vh] min-h-[500px] flex items-center bg-black overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat bg-fixed parallax-bg"
             style="background-image: url('<?= $heroUrl ?>');">
        </div>
        <div class="absolute inset-0 bg-black" style="opacity: <?= $heroOverlay ?>"></div>
    </div>

    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <span class="inline-block text-accent text-lg font-medium tracking-[0.2em] uppercase mb-4 animate-on-scroll" data-animation="fade-up">
            <?= content('privacy_hero_tagline') ?>
        </span>
        <h1 class="font-cursive text-5xl md:text-6xl lg:text-7xl text-cream mb-6 drop-shadow-lg animate-on-scroll" data-animation="fade-up" data-delay="100">
            <?= content('privacy_hero_title') ?>
        </h1>
        <p class="text-xl md:text-2xl text-cream/90 max-w-3xl mx-auto font-light leading-relaxed animate-on-scroll" data-animation="fade-up" data-delay="200">
            <?= content('privacy_hero_subtitle') ?>
        </p>
    </div>
</section>

<!-- Main Content -->
<section class="py-16 lg:py-24 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Last Update Date -->
        <div class="mb-12 text-center animate-on-scroll" data-animation="fade-up">
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-cream-100 rounded-full text-secondary text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <?= content('privacy_date') ?>
            </span>
        </div>

        <!-- Structured Sections -->
        <div class="space-y-16">
            <?php foreach ($sections as $index => $section): ?>
            <div class="animate-on-scroll" data-animation="fade-up" data-delay="<?= ($index % 3) * 100 ?>">
                <h2 class="text-3xl font-serif text-primary mb-3 relative inline-block">
                    <?= e($section['title']) ?>
                    <span class="absolute -bottom-2 left-0 w-1/2 h-1 bg-accent rounded-full"></span>
                </h2>
                <div class="prose prose-lg prose-charcoal max-w-none mt-8">
                    <?= $section['content'] ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
