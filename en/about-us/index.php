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
<section class="relative py-20 lg:py-32 bg-primary overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmZmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIj48cGF0aCBkPSJNMzYgMzRjMC0yLjIwOS0xLjc5MS00LTQtNHMtNCAxLjc5MS00IDQgMS43OTEgNCA0IDQgNC0xLjc5MSA0LTR6Ii8+PC9nPjwvZz48L3N2Zz4=')]"></div>
    </div>
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-primary/50 to-primary"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <span class="inline-block text-accent text-lg font-medium tracking-[0.2em] uppercase mb-4 animate-fade-in">
            Our Story
        </span>
        <h1 class="font-cursive text-6xl md:text-7xl lg:text-8xl text-cream mb-6 drop-shadow-lg">
            About Us
        </h1>
        <p class="text-xl md:text-2xl text-cream/90 max-w-3xl mx-auto font-light leading-relaxed">
            A house with soul, built with love and dedicated to creating unforgettable memories.
        </p>
    </div>
</section>

<!-- Story Section -->
<section class="py-16 lg:py-24 bg-cream-100 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
            <!-- Image -->
            <div class="relative group">
                <div class="aspect-[4/5] rounded-2xl overflow-hidden shadow-2xl border-4 border-white transform transition-transform duration-700 hover:scale-[1.02]">
                    <div class="w-full h-full bg-gradient-to-br from-accent/20 to-cream flex items-center justify-center">
                        <img src="<?= asset('images/about-1.jpg') ?>" class="w-full h-full object-cover opacity-80 mix-blend-multiply" alt="Story">
                    </div>
                </div>
                <!-- Decorative Badge -->
                <div class="absolute -bottom-6 -right-6 bg-secondary text-white p-8 rounded-2xl shadow-xl shadow-secondary/20 animate-bounce-slow">
                    <p class="text-4xl font-serif font-bold">1980</p>
                    <p class="text-sm text-white/80 uppercase tracking-widest mt-1">Founding Year</p>
                </div>
            </div>

            <!-- Content -->
            <div class="space-y-8">
                <h2 class="font-serif text-4xl md:text-5xl text-primary leading-tight">
                    A House with <br><span class="text-secondary italic">History & Soul</span>
                </h2>
                <div class="prose prose-lg text-charcoal/80 space-y-6 font-light">
                    <p>
                        <strong class="text-primary font-serif">Built in the 80s</strong>, at a time when "construction artists" and "materials" were scarce in the lands of Mogadouro, this building was commissioned from the lands of Santa Cruz, by letter, and with the resources of those who left the land in search of a better opportunity!
                    </p>
                    <p>
                        A Casa do Gi is synonymous with simplicity, warmth, memorable moments of fellowship, family warmth, joy, fun, laughter and lots of love!
                    </p>
                    <div class="border-l-4 border-accent pl-6 py-2 italic text-primary/70">
                        "Today, we open the doors of our home to share this experience with you."
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 lg:py-24 bg-secondary">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-serif text-3xl md:text-4xl text-cream mb-6">
            Come Visit Us
        </h2>
        <p class="text-xl text-accent/80 mb-10">
            We would love to welcome you to our home. Book your stay and discover the magic of Mogadouro.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="<?= $base ?>/en/accommodation/" class="inline-flex items-center px-8 py-4 bg-cream text-secondary font-medium rounded hover:bg-cream-100 transition-colors">
                View Accommodation
            </a>
            <a href="<?= $base ?>/en/contact/" class="inline-flex items-center px-8 py-4 bg-secondary text-cream font-medium rounded hover:bg-secondary-700 transition-colors border border-secondary">
                Contact Us
            </a>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
