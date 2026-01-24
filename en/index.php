<?php
/**
 * A Casa do Gi - Homepage (English)
 */

require_once dirname(__DIR__) . '/includes/init.php';

// Force English language
\Core\Language::getInstance()->setLanguage(LANG_EN);
$lang = \Core\Language::getInstance();
$base = basePath();

// Get page content
$content = $lang->getPageContents('home');

// Page configuration
$pageTitle = 'Home';
$pageDescription = 'A Casa do Gi - Local Accommodation in Mogadouro. Simplicity, warmth and love in the heart of Tras-os-Montes.';
$bodyClass = 'homepage';

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section with Parallax -->
<section class="relative h-screen min-h-[600px] -mt-20 overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 parallax-bg"
         style="background-image: url('<?= asset('images/hero-placeholder.jpg') ?>');">
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-b from-granite-900/30 via-granite-900/40 to-granite-900/70"></div>
    </div>

    <!-- Hero Content -->
    <div class="relative z-10 h-full flex items-center justify-center text-center px-4">
        <div class="max-w-4xl">
            <!-- Decorative Element -->
            <div class="mb-8">
                <svg class="w-16 h-16 mx-auto text-cream-200 opacity-80" viewBox="0 0 100 100" fill="currentColor">
                    <path d="M50 5 L55 45 L95 50 L55 55 L50 95 L45 55 L5 50 L45 45 Z"/>
                </svg>
            </div>

            <!-- Title -->
            <h1 class="font-serif text-5xl md:text-6xl lg:text-7xl text-cream-50 mb-6 drop-shadow-lg">
                <?= e($content['hero_title'] ?? 'A Casa do Gi') ?>
            </h1>

            <!-- Subtitle -->
            <p class="text-xl md:text-2xl text-cream-200 mb-10 max-w-2xl mx-auto leading-relaxed">
                <?= e($content['hero_subtitle'] ?? 'Simplicity, warmth and love in Mogadouro') ?>
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="<?= $base ?>/en/accommodation/"
                   class="group inline-flex items-center px-8 py-4 bg-terracotta-500 text-white font-medium rounded-sm hover:bg-terracotta-600 transition-all duration-300 shadow-lg hover:shadow-xl">
                    <span><?= e($content['hero_cta'] ?? 'Discover') ?></span>
                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
                <a href="<?= $base ?>/en/shop/"
                   class="inline-flex items-center px-8 py-4 bg-white/10 backdrop-blur-sm text-cream-50 font-medium rounded-sm border border-cream-200/30 hover:bg-white/20 transition-all duration-300">
                    Regional Products
                </a>
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <a href="#about" class="block text-cream-200 hover:text-white transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
            </svg>
        </a>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-20 lg:py-32 bg-cream-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
            <!-- Text Content -->
            <div class="order-2 lg:order-1">
                <span class="inline-block text-olive-600 text-sm font-medium uppercase tracking-wider mb-4">
                    Our Story
                </span>
                <h2 class="font-serif text-3xl md:text-4xl lg:text-5xl text-granite-800 mb-6 leading-tight">
                    <?= e($content['about_title'] ?? 'A House with Soul') ?>
                </h2>
                <div class="prose prose-lg text-granite-600 leading-relaxed space-y-4">
                    <?= $content['about_text'] ?? '<p>Built in the 80s, when "construction artists" and "materials" were scarce in the lands of Mogadouro, this building was commissioned from the lands of Santa Cruz, by letter, with the resources of those who left the land in search of a better opportunity!</p><p>A Casa do Gi... is synonymous with simplicity, warmth, memorable moments of fellowship, family warmth, joy, fun, laughter and lots of love!</p>' ?>
                </div>
                <div class="mt-8">
                    <a href="<?= $base ?>/en/about-us/"
                       class="inline-flex items-center text-olive-600 font-medium hover:text-olive-700 transition-colors">
                        <span>Learn more about us</span>
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Image Grid -->
            <div class="order-1 lg:order-2 relative">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <div class="aspect-[4/5] rounded-sm overflow-hidden shadow-lg">
                            <img src="<?= asset('images/about-1.jpg') ?>" alt="Casa do Gi Interior"
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                                 loading="lazy">
                        </div>
                        <div class="aspect-square rounded-sm overflow-hidden shadow-lg">
                            <img src="<?= asset('images/about-2.jpg') ?>" alt="House details"
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                                 loading="lazy">
                        </div>
                    </div>
                    <div class="pt-8 space-y-4">
                        <div class="aspect-square rounded-sm overflow-hidden shadow-lg">
                            <img src="<?= asset('images/about-3.jpg') ?>" alt="Bedroom at Casa do Gi"
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                                 loading="lazy">
                        </div>
                        <div class="aspect-[4/5] rounded-sm overflow-hidden shadow-lg">
                            <img src="<?= asset('images/about-4.jpg') ?>" alt="View of Mogadouro"
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                                 loading="lazy">
                        </div>
                    </div>
                </div>
                <!-- Decorative Element -->
                <div class="absolute -bottom-6 -left-6 w-32 h-32 bg-olive-100 rounded-sm -z-10"></div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-20 bg-granite-800 text-cream-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="font-serif text-3xl md:text-4xl text-cream-50 mb-4">The Accommodation</h2>
            <p class="text-granite-300 max-w-2xl mx-auto">
                A 100m2 holiday home with everything you need for an unforgettable stay
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Feature 1 -->
            <div class="text-center group">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-olive-600/20 flex items-center justify-center group-hover:bg-olive-600 transition-colors">
                    <svg class="w-8 h-8 text-olive-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="font-serif text-xl text-cream-50 mb-2">6 Guests</h3>
                <p class="text-granite-400 text-sm">Space for the whole family or group of friends</p>
            </div>

            <!-- Feature 2 -->
            <div class="text-center group">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-olive-600/20 flex items-center justify-center group-hover:bg-olive-600 transition-colors">
                    <svg class="w-8 h-8 text-olive-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <h3 class="font-serif text-xl text-cream-50 mb-2">3 Bedrooms</h3>
                <p class="text-granite-400 text-sm">Comfortable bedrooms with quality beds</p>
            </div>

            <!-- Feature 3 -->
            <div class="text-center group">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-olive-600/20 flex items-center justify-center group-hover:bg-olive-600 transition-colors">
                    <svg class="w-8 h-8 text-olive-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                    </svg>
                </div>
                <h3 class="font-serif text-xl text-cream-50 mb-2">Free Wifi</h3>
                <p class="text-granite-400 text-sm">High-speed internet throughout the house</p>
            </div>

            <!-- Feature 4 -->
            <div class="text-center group">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-olive-600/20 flex items-center justify-center group-hover:bg-olive-600 transition-colors">
                    <svg class="w-8 h-8 text-olive-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
                <h3 class="font-serif text-xl text-cream-50 mb-2">Pool</h3>
                <p class="text-granite-400 text-sm">Access to private and shared pool</p>
            </div>
        </div>

        <div class="text-center mt-12">
            <a href="<?= $base ?>/en/accommodation/"
               class="inline-flex items-center px-8 py-4 bg-olive-600 text-white font-medium rounded-sm hover:bg-olive-700 transition-colors">
                View Accommodation Details
            </a>
        </div>
    </div>
</section>

<!-- Location / Mogadouro Section -->
<section class="py-20 lg:py-32 bg-cream-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
            <!-- Image -->
            <div class="relative">
                <div class="aspect-[4/3] rounded-sm overflow-hidden shadow-2xl">
                    <img src="<?= asset('images/mogadouro.jpg') ?>" alt="View of Mogadouro"
                         class="w-full h-full object-cover"
                         loading="lazy">
                </div>
                <!-- Badge -->
                <div class="absolute -bottom-6 -right-6 bg-white p-6 rounded-sm shadow-xl">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-full bg-olive-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-olive-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-granite-500">Location</p>
                            <p class="font-serif text-lg text-granite-800">Mogadouro</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div>
                <span class="inline-block text-olive-600 text-sm font-medium uppercase tracking-wider mb-4">
                    Discover the Region
                </span>
                <h2 class="font-serif text-3xl md:text-4xl lg:text-5xl text-granite-800 mb-6 leading-tight">
                    Mogadouro, Tras-os-Montes
                </h2>
                <p class="text-granite-600 text-lg leading-relaxed mb-6">
                    A charming village located in northeastern Portugal, Mogadouro is a perfect destination for those seeking history, nature and tranquility.
                </p>
                <ul class="space-y-4 mb-8">
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-olive-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-granite-600">Douro International Natural Park</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-olive-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-granite-600">Medal Serpent Viewpoint</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-olive-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-granite-600">Mogadouro Castle (13th century)</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-olive-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-granite-600">Authentic Transmontana gastronomy</span>
                    </li>
                </ul>
                <a href="<?= $base ?>/en/activities/"
                   class="inline-flex items-center text-olive-600 font-medium hover:text-olive-700 transition-colors">
                    <span>Explore activities in the region</span>
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Products Preview Section -->
<?php if (isShopEnabled()): ?>
<section class="py-20 lg:py-32 bg-cream-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block text-olive-600 text-sm font-medium uppercase tracking-wider mb-4">
                Online Shop
            </span>
            <h2 class="font-serif text-3xl md:text-4xl text-granite-800 mb-4">Regional Products</h2>
            <p class="text-granite-600 max-w-2xl mx-auto">
                Discover the authentic flavors of Mogadouro and Tras-os-Montes. Carefully selected local products.
            </p>
        </div>

        <!-- Product Categories Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php
            $categories = [
                ['name' => 'Honey', 'desc' => 'Pure honey from the region', 'icon' => 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707'],
                ['name' => 'Olive Oil', 'desc' => 'Oil from the Sabor valley', 'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'],
                ['name' => 'Wine', 'desc' => 'Wines from the Douro region', 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
            ];
            foreach ($categories as $cat):
            ?>
            <a href="<?= $base ?>/en/shop/" class="group block bg-white rounded-sm shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">
                <div class="p-8 text-center">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-olive-100 flex items-center justify-center group-hover:bg-olive-600 transition-colors">
                        <svg class="w-10 h-10 text-olive-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?= $cat['icon'] ?>"/>
                        </svg>
                    </div>
                    <h3 class="font-serif text-xl text-granite-800 mb-2"><?= e($cat['name']) ?></h3>
                    <p class="text-granite-500 text-sm"><?= e($cat['desc']) ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-12">
            <a href="<?= $base ?>/en/shop/"
               class="inline-flex items-center px-8 py-4 bg-terracotta-500 text-white font-medium rounded-sm hover:bg-terracotta-600 transition-colors">
                View All Products
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="relative py-20 lg:py-32 overflow-hidden">
    <!-- Background -->
    <div class="absolute inset-0 parallax-bg" style="background-image: url('<?= asset('images/cta-bg.jpg') ?>');">
        <div class="absolute inset-0 bg-granite-900/80"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-serif text-3xl md:text-4xl lg:text-5xl text-cream-50 mb-6">
            Book Your Stay
        </h2>
        <p class="text-xl text-cream-200 mb-10 max-w-2xl mx-auto">
            Come discover the magic of Mogadouro and let yourself be enveloped by the warmth of our home.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <?php if ($guestreadyUrl = setting('guestready_url')): ?>
            <a href="<?= e($guestreadyUrl) ?>" target="_blank" rel="noopener"
               class="inline-flex items-center px-8 py-4 bg-olive-600 text-white font-medium rounded-sm hover:bg-olive-700 transition-colors min-w-[200px] justify-center">
                Book on GuestReady
            </a>
            <?php endif; ?>
            <a href="<?= $base ?>/en/contact/"
               class="inline-flex items-center px-8 py-4 bg-white/10 backdrop-blur-sm text-cream-50 font-medium rounded-sm border border-cream-200/30 hover:bg-white/20 transition-colors min-w-[200px] justify-center">
                Contact Us
            </a>
        </div>
    </div>
</section>

<?php
$pageScripts = <<<'JS'
<script>
    // Parallax effect on scroll
    const parallaxElements = document.querySelectorAll('.parallax-bg');

    const handleParallax = () => {
        const scrolled = window.pageYOffset;
        parallaxElements.forEach(el => {
            const speed = 0.5;
            el.style.backgroundPositionY = `${scrolled * speed}px`;
        });
    };

    if (window.innerWidth > 768) {
        window.addEventListener('scroll', handleParallax);
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
</script>
JS;

include INCLUDES_PATH . '/footer.php';
?>
