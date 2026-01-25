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
<section class="relative h-[75vh] min-h-[600px] flex items-center bg-primary overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat bg-fixed" 
             style="background-image: url('<?= asset('images/MogadouroSobre.png') ?>');">
        </div>
        <div class="absolute inset-0 bg-black/40"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <span class="inline-block text-accent text-lg font-medium tracking-[0.2em] uppercase mb-4 animate-on-scroll" data-animation="fade-up">
            Our Story
        </span>
        <h1 class="font-cursive text-6xl md:text-7xl lg:text-8xl text-cream mb-6 drop-shadow-lg animate-on-scroll" data-animation="fade-up" data-delay="200">
            About Us
        </h1>
        <p class="text-xl md:text-2xl text-cream/90 max-w-3xl mx-auto font-light leading-relaxed animate-on-scroll" data-animation="fade-up" data-delay="400">
            Simplicity, warmth, memorable moments of togetherness, family warmth, joy, fun, laughter, and lots of love!
        </p>
    </div>
</section>

<!-- Story Section -->
<section class="py-16 lg:py-24 bg-cream-100 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
            <!-- Image -->
            <div class="relative group animate-on-scroll" data-animation="fade-right">
                <div class="aspect-[4/5] rounded-2xl overflow-hidden shadow-2xl border-4 border-white transform transition-transform duration-700 hover:scale-[1.02]">
                    <div class="w-full h-full bg-gradient-to-br from-accent/20 to-cream flex items-center justify-center">
                        <img src="<?= asset('images/FotoGi.png') ?>" class="w-full h-full object-cover opacity-90 transition-opacity duration-700 group-hover:opacity-100" alt="Mogadouro">
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="space-y-8 animate-on-scroll" data-animation="fade-left" data-delay="200">
                <h2 class="font-serif text-4xl md:text-5xl text-primary leading-tight">
                    A House with <br><span class="text-secondary italic">History & Soul</span>
                </h2>
                <div class="prose prose-lg text-charcoal/80 space-y-6 font-light">
                    <p>
                        <strong class="text-primary font-serif">Built in the 80s</strong>, a time when "construction artists" and "materials" were scarce in the lands of Mogadouro, this building was commissioned from the lands of Santa Cruz, by letter, and with the resources of those who left the land in search of a better opportunity!
                    </p>
                    <p>
                        A Casa do Gi... is synonymous with simplicity, warmth, memorable moments of togetherness, family warmth, joy, fun, laughter, and lots of love!
                    </p>
                    <div class="border-l-4 border-accent pl-6 py-2 italic text-primary/70">
                        "Today, we open the doors of our home to share this experience with you."
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<?php include INCLUDES_PATH . '/footer.php'; ?>
