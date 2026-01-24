<?php
/**
 * A Casa do Gi - Activities Page (English)
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';

use Core\Language;

// Force English language
Language::getInstance()->setLanguage(LANG_EN);
$lang = Language::getInstance();
$base = basePath();

// Get page content
$content = $lang->getPageContents('activities');

// Page configuration
$pageTitle = 'Things To Do in Mogadouro';
$pageDescription = 'Discover the best activities and tourist attractions in Mogadouro and Tras-os-Montes. Nature, gastronomy, history and culture.';

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="relative py-20 lg:py-32 bg-granite-800 -mt-20 pt-32">
    <div class="absolute inset-0 parallax-bg opacity-30" style="background-image: url('<?= asset('images/activities-hero.jpg') ?>');"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-block text-olive-400 text-sm font-medium uppercase tracking-wider mb-4">
            Discover Mogadouro
        </span>
        <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl text-cream-50 mb-6">
            Things To Do
        </h1>
        <p class="text-xl text-cream-200 max-w-2xl mx-auto">
            Explore everything the region has to offer - from stunning nature to rich history and delicious gastronomy.
        </p>
    </div>
</section>

<!-- Activities Grid -->
<section class="py-16 lg:py-24 bg-cream-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Nature -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="aspect-video bg-olive-100 flex items-center justify-center">
                    <svg class="w-16 h-16 text-olive-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
                <div class="p-6">
                    <h3 class="font-serif text-xl text-granite-800 mb-3">Nature & Trails</h3>
                    <p class="text-granite-600 mb-4">Explore the Douro International Natural Park, hiking trails and breathtaking viewpoints.</p>
                    <ul class="text-sm text-granite-500 space-y-2">
                        <li>Medal Serpent Viewpoint</li>
                        <li>Douro International Natural Park</li>
                        <li>Walking and hiking trails</li>
                    </ul>
                </div>
            </div>

            <!-- Culture -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="aspect-video bg-terracotta-100 flex items-center justify-center">
                    <svg class="w-16 h-16 text-terracotta-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="p-6">
                    <h3 class="font-serif text-xl text-granite-800 mb-3">History & Culture</h3>
                    <p class="text-granite-600 mb-4">Discover centuries of history through castles, churches and traditional villages.</p>
                    <ul class="text-sm text-granite-500 space-y-2">
                        <li>Mogadouro Castle (13th century)</li>
                        <li>Igreja Matriz</li>
                        <li>Traditional villages</li>
                    </ul>
                </div>
            </div>

            <!-- Gastronomy -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="aspect-video bg-wood-100 flex items-center justify-center">
                    <svg class="w-16 h-16 text-wood-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <div class="p-6">
                    <h3 class="font-serif text-xl text-granite-800 mb-3">Gastronomy</h3>
                    <p class="text-granite-600 mb-4">Taste the authentic flavors of Transmontana cuisine - cured meats, olive oil, honey and regional wines.</p>
                    <ul class="text-sm text-granite-500 space-y-2">
                        <li>Traditional restaurants</li>
                        <li>Local markets</li>
                        <li>Wine tastings</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-olive-700 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-serif text-3xl mb-6">Plan Your Visit</h2>
        <p class="text-olive-100 mb-8 max-w-2xl mx-auto">
            Stay at A Casa do Gi and explore everything Mogadouro has to offer.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="<?= $base ?>/en/accommodation/" class="inline-flex items-center px-8 py-4 bg-white text-olive-700 font-medium rounded hover:bg-cream-100 transition-colors">
                View Accommodation
            </a>
            <a href="<?= $base ?>/en/contact/" class="inline-flex items-center px-8 py-4 bg-olive-600 text-white font-medium rounded hover:bg-olive-800 transition-colors border border-olive-500">
                Contact Us
            </a>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
