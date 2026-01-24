<?php
/**
 * A Casa do Gi - About Us Page (English)
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';

use Core\Language;

// Force English language
Language::getInstance()->setLanguage(LANG_EN);
$lang = Language::getInstance();
$base = basePath();

// Get page content
$content = $lang->getPageContents('about');

// Page configuration
$pageTitle = 'About Us';
$pageDescription = 'Learn the story of Casa do Gi - built in the 80s, synonymous with simplicity, warmth and lots of love in Mogadouro.';

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="relative py-20 lg:py-32 bg-granite-800 -mt-20 pt-32">
    <div class="absolute inset-0 parallax-bg opacity-30" style="background-image: url('<?= asset('images/about-hero.jpg') ?>');"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-block text-olive-400 text-sm font-medium uppercase tracking-wider mb-4">
            Our Story
        </span>
        <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl text-cream-50 mb-6">
            About Us
        </h1>
        <p class="text-xl text-cream-200 max-w-2xl mx-auto">
            A house with soul, built with love and dedicated to creating unforgettable memories.
        </p>
    </div>
</section>

<!-- Story Section -->
<section class="py-16 lg:py-24 bg-cream-100">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="prose prose-lg text-granite-600 mx-auto">
            <h2 class="font-serif text-3xl text-granite-800">The History of Casa do Gi</h2>

            <p>Built in the 80s, at a time when "construction artists" and "materials" were scarce in the lands of Mogadouro, this building was commissioned from the lands of Santa Cruz, by letter, and with the resources of those who left the land in search of a better opportunity!</p>

            <p>A Casa do Gi... is synonymous with simplicity, warmth, memorable moments of fellowship, family warmth, joy, fun, laughter and lots of love!</p>

            <p>Today, we open the doors of our home to share this experience with you. Here, you will find not just a place to stay, but a space where you can disconnect from everyday life and reconnect with what really matters.</p>

            <h3 class="font-serif text-2xl text-granite-800 mt-12">Our Mission</h3>

            <p>We want every guest to feel at home. Our mission is to provide an authentic accommodation experience in Tras-os-Montes, where Portuguese tradition and hospitality blend with modern comfort.</p>

            <h3 class="font-serif text-2xl text-granite-800 mt-12">The Region</h3>

            <p>Mogadouro is a charming village located in northeastern Portugal, in the heart of Tras-os-Montes. It is a region rich in history, culture and stunning natural landscapes, from the Douro International Natural Park to the historic medieval villages.</p>
        </div>

        <div class="mt-12 text-center">
            <a href="<?= $base ?>/en/activities/" class="inline-flex items-center mt-6 text-olive-600 font-medium hover:text-olive-700">
                <span>Discover activities in the region</span>
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-olive-700 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-serif text-3xl mb-6">Come Visit Us</h2>
        <p class="text-olive-100 mb-8 max-w-2xl mx-auto">
            We would love to welcome you to our home. Book your stay and discover the magic of Mogadouro.
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
