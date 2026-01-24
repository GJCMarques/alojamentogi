<?php
/**
 * A Casa do Gi - Accommodation Page (English)
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';

use Core\Database;
use Core\Language;

// Force English language
Language::getInstance()->setLanguage(LANG_EN);
$lang = Language::getInstance();
$db = Database::getInstance();
$base = basePath();

// Get accommodation data
$accommodation = $db->fetch("SELECT * FROM accommodation LIMIT 1");
$accTranslation = $db->fetch(
    "SELECT * FROM accommodation_translations WHERE accommodation_id = ? AND language_id = ?",
    [$accommodation['id'] ?? 1, $lang->getCurrentLangId()]
);

// Get amenities
$amenities = $db->fetchAll(
    "SELECT a.*, at.name
     FROM amenities a
     JOIN accommodation_amenities aa ON a.id = aa.amenity_id
     LEFT JOIN amenity_translations at ON a.id = at.amenity_id AND at.language_id = ?
     WHERE aa.accommodation_id = ? AND a.is_active = 1
     ORDER BY a.sort_order",
    [$lang->getCurrentLangId(), $accommodation['id'] ?? 1]
);

// Get gallery images
$gallery = $db->fetchAll(
    "SELECT * FROM accommodation_gallery WHERE accommodation_id = ? ORDER BY sort_order",
    [$accommodation['id'] ?? 1]
);

// Page configuration
$pageTitle = $accTranslation['name'] ?? 'Accommodation';
$pageDescription = $accTranslation['tagline'] ?? 'Local accommodation in Mogadouro - simplicity, warmth and love.';

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="relative py-20 lg:py-32 bg-granite-800 -mt-20 pt-32">
    <div class="absolute inset-0 parallax-bg opacity-30" style="background-image: url('<?= asset('images/accommodation-hero.jpg') ?>');"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-block text-olive-400 text-sm font-medium uppercase tracking-wider mb-4">
            Our Space
        </span>
        <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl text-cream-50 mb-6">
            <?= e($accTranslation['name'] ?? 'A Casa do Gi') ?>
        </h1>
        <p class="text-xl text-cream-200 max-w-2xl mx-auto">
            <?= e($accTranslation['tagline'] ?? 'Simplicity, warmth and love') ?>
        </p>
    </div>
</section>

<!-- Quick Stats -->
<section class="bg-cream-100 py-8 border-b border-cream-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <p class="text-3xl font-serif text-granite-800"><?= $accommodation['max_guests'] ?? 6 ?></p>
                <p class="text-sm text-granite-500">Guests</p>
            </div>
            <div>
                <p class="text-3xl font-serif text-granite-800"><?= $accommodation['bedrooms'] ?? 3 ?></p>
                <p class="text-sm text-granite-500">Bedrooms</p>
            </div>
            <div>
                <p class="text-3xl font-serif text-granite-800"><?= $accommodation['bathrooms'] ?? 2 ?></p>
                <p class="text-sm text-granite-500">Bathrooms</p>
            </div>
            <div>
                <p class="text-3xl font-serif text-granite-800">100m²</p>
                <p class="text-sm text-granite-500">Area</p>
            </div>
        </div>
    </div>
</section>

<!-- Description Section -->
<section class="py-16 lg:py-24 bg-cream-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-start">
            <!-- Text Content -->
            <div>
                <h2 class="font-serif text-3xl text-granite-800 mb-6">About the Space</h2>
                <div class="prose prose-lg text-granite-600 leading-relaxed">
                    <?= nl2br(e($accTranslation['description'] ?? 'A charming holiday home in the heart of Tras-os-Montes, perfect for families and groups of friends looking for tranquility and authentic Portuguese hospitality.')) ?>
                </div>
            </div>

            <!-- Amenities -->
            <div>
                <h2 class="font-serif text-3xl text-granite-800 mb-6">Amenities</h2>
                <div class="grid grid-cols-2 gap-4">
                    <?php foreach ($amenities as $amenity): ?>
                    <div class="flex items-center p-4 bg-white rounded shadow-sm">
                        <div class="w-10 h-10 bg-olive-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-olive-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-granite-700"><?= e($amenity['name']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-olive-700 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-serif text-3xl mb-6">Ready to Book?</h2>
        <p class="text-olive-100 mb-8 max-w-2xl mx-auto">
            Book your stay through our trusted partners and discover the magic of Mogadouro.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <?php if ($guestreadyUrl = setting('guestready_url')): ?>
            <a href="<?= e($guestreadyUrl) ?>" target="_blank" rel="noopener"
               class="inline-flex items-center px-8 py-4 bg-white text-olive-700 font-medium rounded hover:bg-cream-100 transition-colors">
                Book on GuestReady
            </a>
            <?php endif; ?>
            <a href="<?= $base ?>/en/contact/"
               class="inline-flex items-center px-8 py-4 bg-olive-600 text-white font-medium rounded hover:bg-olive-800 transition-colors border border-olive-500">
                Contact Us
            </a>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
