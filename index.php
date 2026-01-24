<?php
/**
 * A Casa do Gi - Homepage (Portuguese)
 */

require_once __DIR__ . '/includes/init.php';

$lang = \Core\Language::getInstance();
$base = basePath();

// Get page content
$content = $lang->getPageContents('home');

// Page configuration
$pageTitle = 'Inicio';
$pageDescription = 'A Casa do Gi - Alojamento Local em Mogadouro. Simplicidade, acolhimento e muito amor no coracao de Tras-os-Montes.';
$bodyClass = 'homepage';

// Hero images (would come from media manager in production)
$heroImage = '/uploads/gallery/hero-main.jpg';

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section with Parallax -->
<section class="relative h-screen min-h-[600px] -mt-20 overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 parallax-bg"
         style="background-image: url('<?= asset('images/hero-placeholder.jpg') ?>');">
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-primary/70"></div>
    </div>

    <!-- Hero Content -->
    <div class="relative z-10 h-full flex items-center justify-center text-center px-4">
        <div class="max-w-4xl animate-slide-up">
            <!-- Decorative Element -->
            <div class="mb-8 animate-float">
                <svg class="w-16 h-16 mx-auto text-accent opacity-90" viewBox="0 0 100 100" fill="currentColor">
                    <path d="M50 5 L55 45 L95 50 L55 55 L50 95 L45 55 L5 50 L45 45 Z"/>
                </svg>
            </div>

            <!-- Title -->
            <h1 class="font-serif text-5xl md:text-6xl lg:text-7xl text-cream mb-6 drop-shadow-2xl">
                <?= e($content['hero_title'] ?? 'A Casa do Gi') ?>
            </h1>

            <!-- Subtitle -->
            <p class="text-xl md:text-2xl text-cream-100 mb-10 max-w-2xl mx-auto leading-relaxed">
                <?= e($content['hero_subtitle'] ?? 'Simplicidade, acolhimento e muito amor em Mogadouro') ?>
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="<?= $base ?>/alojamento/"
                   class="group inline-flex items-center px-8 py-4 bg-secondary text-cream font-semibold rounded-lg hover:bg-secondary-600 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105">
                    <span><?= e($content['hero_cta'] ?? 'Descobrir') ?></span>
                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
                <a href="<?= $base ?>/loja/"
                   class="inline-flex items-center px-8 py-4 bg-cream/10 backdrop-blur-sm text-cream font-semibold rounded-lg border-2 border-accent/50 hover:bg-cream/20 hover:border-accent transition-all duration-300">
                    Produtos Regionais
                </a>
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <a href="#about" class="block text-cream-100 hover:text-cream transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
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
                <span class="inline-block text-accent text-sm font-bold uppercase tracking-wider mb-4">
                    A Nossa Historia
                </span>
                <h2 class="font-serif text-3xl md:text-4xl lg:text-5xl text-primary mb-6 leading-tight">
                    <?= e($content['about_title'] ?? 'Uma Casa com Alma') ?>
                </h2>
                <div class="prose prose-lg text-charcoal leading-relaxed space-y-4">
                    <?= $content['about_text'] ?? '<p>Construida nos anos 80, altura em que os "artistas da construcao" e os "materiais" eram escassos por Terras de Mogadouro, este edificio foi mandado construir desde terras de Santa Cruz, por carta, e com os recursos de quem saiu da terra em busca de uma melhor oportunidade!</p><p>A Casa do Gi... e sinonimo de simplicidade, acolhimento, momentos de convivio marcantes, calor da familia, alegria, diversao, gargalhadas e muito amor!</p>' ?>
                </div>
                <div class="mt-8">
                    <a href="<?= $base ?>/sobre-nos/"
                       class="inline-flex items-center text-secondary font-medium hover:text-secondary-600 transition-colors">
                        <span>Saber mais sobre nos</span>
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
                        <div class="aspect-[4/5] rounded-sm overflow-hidden shadow-lg border border-accent/20">
                            <img src="<?= asset('images/about-1.jpg') ?>" alt="Interior da Casa do Gi"
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                                 loading="lazy">
                        </div>
                        <div class="aspect-square rounded-sm overflow-hidden shadow-lg border border-accent/20">
                            <img src="<?= asset('images/about-2.jpg') ?>" alt="Detalhes da casa"
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                                 loading="lazy">
                        </div>
                    </div>
                    <div class="pt-8 space-y-4">
                        <div class="aspect-square rounded-sm overflow-hidden shadow-lg border border-accent/20">
                            <img src="<?= asset('images/about-3.jpg') ?>" alt="Quarto da Casa do Gi"
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                                 loading="lazy">
                        </div>
                        <div class="aspect-[4/5] rounded-sm overflow-hidden shadow-lg border border-accent/20">
                            <img src="<?= asset('images/about-4.jpg') ?>" alt="Vista de Mogadouro"
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                                 loading="lazy">
                        </div>
                    </div>
                </div>
                <!-- Decorative Element -->
                <div class="absolute -bottom-6 -left-6 w-32 h-32 bg-accent/20 rounded-sm -z-10"></div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-20 bg-primary text-cream-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="font-serif text-3xl md:text-4xl text-cream mb-4">O Alojamento</h2>
            <p class="text-cream-200 max-w-2xl mx-auto">
                Uma casa de ferias de 100m2 com tudo o que precisa para uma estadia inesquecivel
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Feature 1 -->
            <div class="text-center group">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-secondary/20 flex items-center justify-center group-hover:bg-secondary transition-colors">
                    <svg class="w-8 h-8 text-accent group-hover:text-cream transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="font-serif text-xl text-cream mb-2">6 Hospedes</h3>
                <p class="text-cream-200 text-sm">Espaco para toda a familia ou grupo de amigos</p>
            </div>

            <!-- Feature 2 -->
            <div class="text-center group">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-secondary/20 flex items-center justify-center group-hover:bg-secondary transition-colors">
                    <svg class="w-8 h-8 text-accent group-hover:text-cream transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <h3 class="font-serif text-xl text-cream mb-2">3 Quartos</h3>
                <p class="text-cream-200 text-sm">Quartos confortaveis com camas de qualidade</p>
            </div>

            <!-- Feature 3 -->
            <div class="text-center group">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-secondary/20 flex items-center justify-center group-hover:bg-secondary transition-colors">
                    <svg class="w-8 h-8 text-accent group-hover:text-cream transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                    </svg>
                </div>
                <h3 class="font-serif text-xl text-cream mb-2">Wifi Gratuito</h3>
                <p class="text-cream-200 text-sm">Internet de alta velocidade em toda a casa</p>
            </div>

            <!-- Feature 4 -->
            <div class="text-center group">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-secondary/20 flex items-center justify-center group-hover:bg-secondary transition-colors">
                    <svg class="w-8 h-8 text-accent group-hover:text-cream transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
                <h3 class="font-serif text-xl text-cream mb-2">Piscina</h3>
                <p class="text-cream-200 text-sm">Acesso a piscina privada e partilhada</p>
            </div>
        </div>

        <div class="text-center mt-12">
            <a href="<?= $base ?>/alojamento/"
               class="inline-flex items-center px-8 py-4 bg-secondary text-cream font-semibold rounded-lg hover:bg-secondary-600 transition-all shadow-lg hover:shadow-xl hover:scale-105">
                Ver Detalhes do Alojamento
            </a>
        </div>
    </div>
</section>

<!-- Location / Mogadouro Section -->
<section class="py-20 lg:py-32 bg-cream-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
            <!-- Image -->
            <div class="relative">
                <div class="aspect-[4/3] rounded-lg overflow-hidden shadow-2xl border border-accent/20">
                    <img src="<?= asset('images/mogadouro.jpg') ?>" alt="Vista de Mogadouro"
                         class="w-full h-full object-cover"
                         loading="lazy">
                </div>
                <!-- Badge -->
                <div class="absolute -bottom-6 -right-6 bg-cream p-6 rounded-lg shadow-xl border border-accent/30">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-full bg-secondary/20 flex items-center justify-center">
                            <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-charcoal-600">Localizacao</p>
                            <p class="font-serif text-lg text-primary">Mogadouro</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div>
                <span class="inline-block text-accent text-sm font-bold uppercase tracking-wider mb-4">
                    Descubra a Regiao
                </span>
                <h2 class="font-serif text-3xl md:text-4xl lg:text-5xl text-primary mb-6 leading-tight">
                    Mogadouro, Tras-os-Montes
                </h2>
                <p class="text-charcoal text-lg leading-relaxed mb-6">
                    Uma encantadora vila situada no nordeste de Portugal, Mogadouro e um destino perfeito para quem busca historia, natureza e tranquilidade.
                </p>
                <ul class="space-y-4 mb-8">
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-secondary mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-charcoal">Parque Natural do Douro Internacional</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-secondary mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-charcoal">Miradouro Serpente do Medal</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-secondary mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-charcoal">Castelo de Mogadouro (seculo XIII)</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-secondary mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-charcoal">Gastronomia transmontana autentica</span>
                    </li>
                </ul>
                <a href="<?= $base ?>/atividades/"
                   class="inline-flex items-center text-secondary font-medium hover:text-secondary-600 transition-colors">
                    <span>Explorar atividades na regiao</span>
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
<section class="py-20 lg:py-32 bg-cream">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block text-accent text-sm font-bold uppercase tracking-wider mb-4">
                Loja Online
            </span>
            <h2 class="font-serif text-3xl md:text-4xl text-primary mb-4">Produtos Regionais</h2>
            <p class="text-charcoal max-w-2xl mx-auto">
                Descubra os sabores autenticos de Mogadouro e Tras-os-Montes. Produtos locais selecionados com carinho.
            </p>
        </div>

        <!-- Product Categories Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Category Card -->
            <?php
            $categories = [
                ['name' => 'Mel', 'desc' => 'Mel puro da regiao', 'icon' => 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707'],
                ['name' => 'Azeite', 'desc' => 'Azeite do vale do Sabor', 'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'],
                ['name' => 'Vinho', 'desc' => 'Vinhos da regiao do Douro', 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
            ];
            foreach ($categories as $cat):
            ?>
            <a href="<?= $base ?>/loja/" class="group block bg-cream-100 rounded-lg shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden hover:scale-105 border border-accent/20">
                <div class="p-8 text-center">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-secondary/10 flex items-center justify-center group-hover:bg-secondary transition-colors">
                        <svg class="w-10 h-10 text-secondary group-hover:text-cream transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?= $cat['icon'] ?>"/>
                        </svg>
                    </div>
                    <h3 class="font-serif text-xl text-primary mb-2"><?= e($cat['name']) ?></h3>
                    <p class="text-charcoal text-sm"><?= e($cat['desc']) ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-12">
            <a href="<?= $base ?>/loja/"
               class="inline-flex items-center px-8 py-4 bg-secondary text-cream font-semibold rounded-lg hover:bg-secondary-600 transition-all shadow-lg hover:shadow-xl hover:scale-105">
                Ver Todos os Produtos
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="relative py-20 lg:py-32 overflow-hidden">
    <!-- Background -->
    <div class="absolute inset-0 parallax-bg" style="background-image: url('<?= asset('images/cta-bg.jpg') ?>');">
        <div class="absolute inset-0 bg-primary/80"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-serif text-3xl md:text-4xl lg:text-5xl text-cream mb-6">
            Reserve a Sua Estadia
        </h2>
        <p class="text-xl text-cream-100 mb-10 max-w-2xl mx-auto">
            Venha descobrir a magia de Mogadouro e deixe-se envolver pelo calor da nossa casa.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <?php if ($guestreadyUrl = setting('guestready_url')): ?>
            <a href="<?= e($guestreadyUrl) ?>" target="_blank" rel="noopener"
               class="inline-flex items-center px-8 py-4 bg-secondary text-cream font-semibold rounded-lg hover:bg-secondary-600 transition-all shadow-lg hover:shadow-xl hover:scale-105 min-w-[200px] justify-center">
                Reservar no GuestReady
            </a>
            <?php endif; ?>
            <a href="<?= $base ?>/contactos/"
               class="inline-flex items-center px-8 py-4 bg-cream/10 backdrop-blur-sm text-cream font-semibold rounded-lg border-2 border-accent/50 hover:bg-cream/20 hover:border-accent transition-all min-w-[200px] justify-center">
                Contactar-nos
            </a>
        </div>
    </div>
</section>

<?php
// Page-specific scripts
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

    // Only apply parallax on desktop
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
