<?php
/**
 * A Casa do Gi - Privacy Policy
 */

require_once dirname(__DIR__) . '/includes/init.php';

use Core\Language;
use Core\Database;

$lang = Language::getInstance();
$db = Database::getInstance();
$base = basePath();

// Get hero image from database
$pageHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = 'privacy_policy' AND is_active = 1");
$heroMedia = $pageHero ? $db->fetch("SELECT * FROM media WHERE entity_type = 'hero' AND entity_id = ? AND is_cover = 1", [$pageHero['id']]) : null;
$heroImage = $heroMedia['file_path'] ?? 'images/MogadouroSobre.png'; // Fallback
$heroOverlay = $pageHero['hero_overlay_opacity'] ?? 0.40;

// Build hero URL
$heroUrl = $heroImage[0] === '/' ? basePath() . $heroImage : asset($heroImage);

$pageTitle = content('privacy_hero_title');
$pageDescription = content('privacy_hero_subtitle');

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="relative h-[60vh] min-h-[500px] flex items-center bg-primary overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat bg-fixed"
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

        <!-- WYSIWYG Content -->
        <div class="prose prose-lg prose-charcoal max-w-none animate-on-scroll" data-animation="fade-up" data-delay="100">
            <?= content('privacy_content') ?>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
