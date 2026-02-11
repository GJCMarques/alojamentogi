<?php
/**
 * A Casa do Gi - About Us Page (English)
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';

use Core\Language;
use Core\Database;

Language::getInstance()->setLanguage(LANG_EN);

$lang = Language::getInstance();
$db = Database::getInstance();
$base = basePath();

// Get page content
$content = $lang->getPageContents('about');

// Get hero image from database
$pageHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = 'about' AND is_active = 1");
$heroMedia = $pageHero ? $db->fetch("SELECT * FROM media WHERE entity_type = 'hero' AND entity_id = ? AND is_cover = 1", [$pageHero['id']]) : null;
$heroImage = $heroMedia['file_path'] ?? 'images/MogadouroSobre.png';
$heroOverlay = $pageHero['hero_overlay_opacity'] ?? 0.40;

// Build hero URL (file_path from media already has leading slash)
$heroUrl = $heroImage[0] === '/' ? basePath() . $heroImage : asset($heroImage);

// Page configuration
$pageTitle = 'About Us';
$pageDescription = 'Discover the story of Casa do Gi - built in the 80s, a synonym for simplicity, warmth and love in Mogadouro.';

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="relative h-screen md:h-[75vh] min-h-[600px] flex items-center bg-primary overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat bg-fixed"
             style="background-image: url('<?= $heroUrl ?>');">
        </div>
        <div class="absolute inset-0 bg-black" style="opacity: <?= $heroOverlay ?>"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <span class="inline-block text-accent text-lg font-medium tracking-[0.2em] uppercase mb-4 animate-on-scroll" data-animation="fade-up">
            <?= $content['about_hero_label'] ?? 'Our Story' ?>
        </span>
        <h1 class="font-cursive text-6xl md:text-7xl lg:text-8xl text-cream mb-6 drop-shadow-lg animate-on-scroll" data-animation="fade-up" data-delay="200">
            A Casa do Gi
        </h1>
        <p class="text-xl md:text-2xl text-cream/90 max-w-3xl mx-auto font-light leading-relaxed animate-on-scroll" data-animation="fade-up" data-delay="400">
            <?= $content['about_hero_subtitle'] ?? 'Simplicity, warmth, memorable moments together, family warmth, joy, fun, laughter and lots of love!' ?>
        </p>
    </div>
</section>



<!-- Intro Section -->
<section class="py-20 bg-white relative overflow-hidden">
    <!-- Decorative elements -->
    <div class="absolute top-0 left-0 w-64 h-64 bg-cream-100 rounded-br-full opacity-50 -translate-x-1/2 -translate-y-1/2"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div class="animate-on-scroll" data-animation="fade-right">
                <span class="text-accent text-sm font-bold tracking-widest uppercase mb-4 block"><?= $content['about_origin_label'] ?? 'Our Origins' ?></span>
                <h2 class="font-serif text-4xl lg:text-5xl text-primary mb-6 leading-tight">
                    <?= $content['about_origin_title'] ?? 'A house built with <span class="italic text-secondary">love</span> and <span class="italic text-secondary">longing</span>.' ?>
                </h2>
                <div class="prose prose-lg text-charcoal-600 font-light space-y-4">
                    <p>
                        <?= $content['about_origin_text1'] ?? 'Built in the 80s, <strong>Casa do Gi</strong> tells the timeless story of those who left for distant lands but never forgot their roots. Constructed brick by brick, it represents the fulfilled dream of returning home.' ?>
                    </p>
                    <p>
                        <?= $content['about_origin_text2'] ?? 'What began as a family life project transformed into a refuge for those seeking the peace of the countryside. Here, time slows down and days are measured by sunlight and conversations by the fireplace.' ?>
                    </p>
                </div>

                <div class="mt-8 flex items-center gap-4">
                    <div class="h-px w-16 bg-accent"></div>
                    <span class="font-cursive text-2xl text-primary-600"><?= $content['about_origin_signature'] ?? 'Gi Family' ?></span>
                </div>
            </div>

// Data
$langId = 2;

// ... (in body)

            <div class="relative animate-on-scroll" data-animation="fade-left">
                <!-- Image Composition -->
                <div class="relative z-10 rounded-2xl overflow-hidden shadow-2xl transform rotate-2 hover:rotate-0 transition-transform duration-700">
                    <img src="<?= resolveContentImage(content('about_image_intro', 'images/FotoGi.png')) ?>" alt="A Casa do Gi in the Past" class="w-full h-auto object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-primary/60 to-transparent"></div>
                    <div class="absolute bottom-6 left-6 text-white text-sm font-medium tracking-wide">
                        <?= $content['about_origin_caption'] ?? '1980 • The Beginning' ?>
                    </div>
                </div>
                <!-- Decorative Border -->
                <div class="absolute inset-0 border-2 border-accent rounded-2xl transform -rotate-2 scale-105 z-0 opacity-40"></div>
            </div>

// ... (lower down)

<!-- Region Section - Full Bleed Immersive -->
<section class="relative h-screen min-h-[700px] flex items-center overflow-hidden">
    <!-- Parallax Background -->
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-black/40 z-10"></div> <!-- Overlay -->
        <img src="<?= resolveContentImage(content('about_image_region', 'images/Castelo.jpg')) ?>" class="w-full h-full object-cover attachment-fixed transform scale-110" style="object-position: center;" alt="Mogadouro Castle">
    </div>

    <div class="relative z-20 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex flex-col justify-center">
        <div class="max-w-xl ml-auto text-right text-white">
            <span class="inline-block py-1 px-3 border border-white/30 rounded-full text-xs font-bold tracking-[0.2em] uppercase mb-6 backdrop-blur-sm animate-on-scroll" data-animation="fade-left">
                <?= $content['about_region_label'] ?? 'Our Home' ?>
            </span>
            <h2 class="font-serif text-6xl md:text-7xl lg:text-8xl mb-6 shadow-black drop-shadow-2xl animate-on-scroll" data-animation="fade-left" data-delay="100">
                Mogadouro
            </h2>
            <div class="w-24 h-1 bg-accent ml-auto mb-8 animate-on-scroll" data-animation="fade-left" data-delay="200"></div>
            <p class="text-xl md:text-2xl font-light leading-relaxed text-white/90 mb-10 drop-shadow-lg animate-on-scroll" data-animation="fade-left" data-delay="300">
                <?= $content['about_region_text'] ?? 'Where time stops and the soul breathes. A land of infinite horizons, guardian of ancient traditions and raw, untouched natural beauty.' ?>
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-end animate-on-scroll" data-animation="fade-left" data-delay="400">
                 <a href="<?= $base ?>/en/contact/" class="group inline-flex items-center justify-center px-8 py-4 bg-white text-primary font-bold rounded-full shadow-lg hover:shadow-xl hover:-translate-y-1 hover:bg-cream transition-all duration-300">
                    <span><?= $content['about_region_cta1'] ?? 'Plan Visit' ?></span>
                    <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                <a href="<?= $base ?>/en/activities/" class="inline-flex items-center justify-center px-8 py-4 border border-white text-white font-medium rounded-full hover:bg-white/10 hover:-translate-y-1 transition-all duration-300">
                    <?= $content['about_region_cta2'] ?? 'Things to do' ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Bottom gradient for smooth transition if needed -->
    <div class="absolute bottom-0 left-0 w-full h-32 bg-gradient-to-t from-black/50 to-transparent z-10 pointer-events-none"></div>
</section>





<?php include INCLUDES_PATH . '/footer.php'; ?>
