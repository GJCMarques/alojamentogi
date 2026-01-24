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
         style="background-image: url('<?= asset('images/hero.jpg') ?>');">
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-primary/40 mix-blend-multiply"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-primary/90 via-primary/40 to-transparent"></div>
    </div>

    <!-- Hero Content -->
    <div class="relative z-10 h-full flex items-center justify-center text-center px-4 pt-20">
        <div class="max-w-4xl animate-slide-up space-y-4">
            <!-- Decorative Element -->
            <span class="inline-block text-accent text-lg font-medium tracking-[0.2em] uppercase mb-2 drop-shadow-md">
                Welcome to
            </span>

            <!-- Title -->
            <h1 class="font-cursive text-6xl md:text-7xl lg:text-9xl text-cream drop-shadow-2xl leading-none">
                <?= e($content['hero_title'] ?? 'A Casa do Gi') ?>
            </h1>

            <!-- Subtitle -->
            <p class="font-serif text-xl md:text-2xl text-cream/90 max-w-2xl mx-auto italic font-light leading-relaxed mt-6 drop-shadow-md">
                "<?= e($content['hero_subtitle'] ?? 'Simplicity, warmth and love in Mogadouro') ?>"
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-6 mt-10">
                <a href="<?= $base ?>/en/accommodation/"
                   class="group relative inline-flex items-center px-8 py-4 bg-secondary text-cream font-serif text-lg tracking-wide hover:bg-secondary-600 transition-all duration-500 shadow-lg hover:shadow-secondary/30 overflow-hidden rounded-sm">
                    <span class="relative z-10"><?= e($content['hero_cta'] ?? 'Discover') ?></span>
                    <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                </a>
                <a href="<?= $base ?>/en/shop/"
                   class="group inline-flex items-center px-8 py-4 bg-transparent border border-cream/50 text-cream font-serif text-lg tracking-wide hover:bg-cream hover:text-primary transition-all duration-300 backdrop-blur-sm rounded-sm">
                    <span>Regional Products</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce-slow">
        <a href="#about" class="block text-cream/50 hover:text-cream transition-colors p-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
            </svg>
        </a>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-24 lg:py-32 bg-cream-100 relative overflow-hidden">
    <!-- Background Patterns -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-accent/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-secondary/5 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid lg:grid-cols-2 gap-16 lg:gap-24 items-center">
            <!-- Text Content -->
            <div class="order-2 lg:order-1 space-y-8">
                <div class="space-y-4">
                    <span class="text-secondary font-medium tracking-[0.2em] uppercase text-sm">
                        Our Story
                    </span>
                    <h2 class="font-serif text-4xl md:text-5xl lg:text-6xl text-primary leading-tight">
                        <?= e($content['about_title'] ?? 'A House with Soul') ?>
                    </h2>
                </div>
                
                <div class="prose prose-lg text-charcoal/80 font-light leading-relaxed">
                    <?= $content['about_text'] ?? '<p>Built in the 80s, when "construction artists" and "materials" were scarce in the lands of Mogadouro, this building was commissioned from the lands of Santa Cruz, by letter, with the resources of those who left the land in search of a better opportunity!</p><p>A Casa do Gi... is synonymous with simplicity, warmth, memorable moments of fellowship, family warmth, joy, fun, laughter and lots of love!</p>' ?>
                </div>

                <div class="pt-4">
                    <a href="<?= $base ?>/en/about-us/"
                       class="inline-flex items-center text-accent hover:text-accent-600 font-medium transition-colors group">
                        <span class="border-b border-accent/30 group-hover:border-accent pb-1">Learn more about us</span>
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Image Composition -->
            <div class="order-1 lg:order-2 relative">
                <div class="relative z-10 transform hover:scale-[1.02] transition-transform duration-700">
                    <div class="aspect-[4/5] rounded-lg overflow-hidden shadow-2xl">
                        <img src="<?= asset('images/about-1.jpg') ?>" alt="Casa do Gi Interior"
                             class="w-full h-full object-cover"
                             loading="lazy">
                    </div>
                </div>
                <!-- Decorative Frame -->
                <div class="absolute top-8 -right-8 w-full h-full border-2 border-accent/20 rounded-lg -z-0 hidden lg:block"></div>
                <div class="absolute -bottom-12 -left-12 -z-10">
                    <svg class="w-32 h-32 text-secondary/10" viewBox="0 0 100 100" fill="currentColor">
                        <circle cx="50" cy="50" r="40"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-24 bg-primary text-cream relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20 space-y-4">
            <h2 class="font-cursive text-5xl md:text-6xl text-cream drop-shadow-md">The Accommodation</h2>
            <p class="font-serif text-xl text-cream/80 max-w-2xl mx-auto italic font-light">
                A 100m2 holiday home with everything you need for an unforgettable stay
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-12">
            <!-- Feature 1 -->
            <div class="text-center group">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-white/5 border border-white/10 flex items-center justify-center group-hover:scale-110 group-hover:bg-accent/20 transition-all duration-300">
                    <svg class="w-8 h-8 text-cream/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="font-serif text-xl text-cream mb-2">6 Guests</h3>
                <p class="text-cream/60 text-sm font-light">Space for the whole family or group of friends</p>
            </div>

            <!-- Feature 2 -->
            <div class="text-center group">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-white/5 border border-white/10 flex items-center justify-center group-hover:scale-110 group-hover:bg-accent/20 transition-all duration-300">
                    <svg class="w-8 h-8 text-cream/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <h3 class="font-serif text-xl text-cream mb-2">3 Bedrooms</h3>
                <p class="text-cream/60 text-sm font-light">Comfortable bedrooms with quality beds</p>
            </div>

            <!-- Feature 3 -->
            <div class="text-center group">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-white/5 border border-white/10 flex items-center justify-center group-hover:scale-110 group-hover:bg-accent/20 transition-all duration-300">
                    <svg class="w-8 h-8 text-cream/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                    </svg>
                </div>
                <h3 class="font-serif text-xl text-cream mb-2">Free Wifi</h3>
                <p class="text-cream/60 text-sm font-light">High-speed internet throughout the house</p>
            </div>

            <!-- Feature 4 -->
            <div class="text-center group">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-white/5 border border-white/10 flex items-center justify-center group-hover:scale-110 group-hover:bg-accent/20 transition-all duration-300">
                    <svg class="w-8 h-8 text-cream/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
                <h3 class="font-serif text-xl text-cream mb-2">Pool</h3>
                <p class="text-cream/60 text-sm font-light">Access to private and shared pool</p>
            </div>
        </div>

        <div class="text-center mt-20">
            <a href="<?= $base ?>/en/accommodation/"
               class="inline-flex items-center px-10 py-4 bg-secondary text-cream font-medium rounded-sm hover:bg-secondary-600 transition-all shadow-lg hover:shadow-xl hover:-translate-y-1">
                View Accommodation Details
            </a>
        </div>
    </div>
</section>

<!-- Location / Mogadouro Section -->
<section class="py-24 lg:py-32 bg-cream-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 lg:gap-24 items-center">
            <!-- Image -->
            <div class="relative group">
                <div class="aspect-[4/3] rounded-lg overflow-hidden shadow-2xl">
                    <img src="<?= asset('images/mogadouro.jpg') ?>" alt="View of Mogadouro"
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-[2s]"
                         loading="lazy">
                </div>
                <!-- Badge -->
                <div class="absolute -bottom-8 -right-8 bg-cream p-8 rounded-lg shadow-xl animate-fade-in-up">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 rounded-full bg-accent/20 flex items-center justify-center">
                            <svg class="w-7 h-7 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-charcoal/60 uppercase tracking-widest">Location</p>
                            <p class="font-serif text-2xl text-primary">Mogadouro</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="space-y-8">
                <div>
                    <span class="text-secondary font-medium tracking-[0.2em] uppercase text-sm">
                        Discover the Region
                    </span>
                    <h2 class="font-serif text-4xl md:text-5xl text-primary mt-4 mb-6">
                        Mogadouro, Tras-os-Montes
                    </h2>
                    <p class="text-charcoal/80 text-lg leading-relaxed font-light">
                        A charming village located in northeastern Portugal, Mogadouro is a perfect destination for those seeking history, nature and tranquility.
                    </p>
                </div>

                <ul class="space-y-4">
                    <li class="flex items-center p-4 bg-white rounded-lg shadow-sm border border-accent/10 hover:border-accent/30 transition-colors">
                        <span class="w-2 h-2 rounded-full bg-secondary mr-4"></span>
                        <span class="text-charcoal-600">Douro International Natural Park</span>
                    </li>
                    <li class="flex items-center p-4 bg-white rounded-lg shadow-sm border border-accent/10 hover:border-accent/30 transition-colors">
                        <span class="w-2 h-2 rounded-full bg-secondary mr-4"></span>
                        <span class="text-charcoal-600">Medal Serpent Viewpoint</span>
                    </li>
                    <li class="flex items-center p-4 bg-white rounded-lg shadow-sm border border-accent/10 hover:border-accent/30 transition-colors">
                        <span class="w-2 h-2 rounded-full bg-secondary mr-4"></span>
                        <span class="text-charcoal-600">Mogadouro Castle (13th century)</span>
                    </li>
                    <li class="flex items-center p-4 bg-white rounded-lg shadow-sm border border-accent/10 hover:border-accent/30 transition-colors">
                        <span class="w-2 h-2 rounded-full bg-secondary mr-4"></span>
                        <span class="text-charcoal-600">Authentic Transmontana gastronomy</span>
                    </li>
                </ul>

                <div class="pt-4">
                    <a href="<?= $base ?>/en/activities/"
                       class="inline-flex items-center px-8 py-3 bg-white border border-secondary text-secondary font-medium rounded hover:bg-secondary hover:text-white transition-all shadow-md">
                        <span>Explore activities</span>
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Products Preview Section -->
<?php if (isShopEnabled()): ?>
<section class="py-24 bg-cream-100 relative overflow-hidden">
    <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-accent/30 to-transparent"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 space-y-4">
            <span class="text-secondary font-medium tracking-[0.2em] uppercase text-sm">
                Online Shop
            </span>
            <h2 class="font-cursive text-5xl text-primary">Regional Products</h2>
            <p class="text-charcoal/70 max-w-2xl mx-auto font-light">
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
            <a href="<?= $base ?>/en/shop/" class="group block bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-500 hover:-translate-y-2 border border-accent/10">
                <div class="p-10 text-center">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-cream-200/50 flex items-center justify-center group-hover:bg-accent/20 transition-colors duration-500">
                        <svg class="w-10 h-10 text-primary group-hover:text-accent transition-colors duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?= $cat['icon'] ?>"/>
                        </svg>
                    </div>
                    <h3 class="font-serif text-2xl text-primary mb-2 group-hover:text-secondary transition-colors"><?= e($cat['name']) ?></h3>
                    <p class="text-charcoal/60 text-sm"><?= e($cat['desc']) ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-12">
            <a href="<?= $base ?>/en/shop/"
               class="inline-flex items-center px-8 py-3 border-b-2 border-accent text-primary font-medium hover:text-accent transition-colors">
                View All Products
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="relative py-32 overflow-hidden">
    <!-- Background -->
    <div class="absolute inset-0 parallax-bg" style="background-image: url('<?= asset('images/cta-bg.jpg') ?>');">
        <!-- Overlay Gradient -->
        <div class="absolute inset-0 bg-primary/80 mix-blend-multiply"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-primary/90 to-transparent"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <h2 class="font-cursive text-6xl text-cream mb-6 drop-shadow-md">
                Book Your Stay
            </h2>
            <p class="text-xl text-cream/90 mb-10 leading-relaxed font-light">
                Come discover the magic of Mogadouro and let yourself be enveloped by the warmth of our home. We are waiting for you.
            </p>
            <div class="flex flex-col sm:flex-row items-center gap-6">
                <a href="<?= $base ?>/en/accommodation/"
                   class="inline-flex items-center px-8 py-4 bg-accent text-primary font-bold rounded-sm hover:bg-accent-300 transition-all shadow-lg hover:shadow-accent/50 w-full sm:w-auto justify-center">
                    Check Availability
                </a>
                <a href="<?= $base ?>/en/contact/"
                   class="inline-flex items-center px-8 py-4 border-2 border-cream/30 text-cream font-medium rounded-sm hover:bg-cream/10 transition-colors w-full sm:w-auto justify-center">
                    Contact Us
                </a>
            </div>
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
