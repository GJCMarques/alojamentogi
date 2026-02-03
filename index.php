<?php
/**
 * A Casa do Gi - Homepage (New Design)
 */

require_once __DIR__ . '/includes/init.php';

use Core\Database;

$lang = \Core\Language::getInstance();
$db = Database::getInstance();
$base = basePath();
$content = $lang->getPageContents('home');

// Get hero image from database
$pageHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = 'home' AND is_active = 1");
$heroImage = $pageHero['hero_image'] ?? 'images/MogadouroAtividades.jpg';
$heroOverlay = $pageHero['hero_overlay_opacity'] ?? 0.30;

// Helper to get image URL
function getHeroImageUrl($imagePath, $default = '') {
    if (!$imagePath) return $default;
    if (strpos($imagePath, 'uploads/') === 0) {
        return basePath() . '/' . $imagePath;
    }
    return asset($imagePath);
}
$heroUrl = getHeroImageUrl($heroImage, asset('images/MogadouroAtividades.jpg'));

$pageTitle = 'Inicio';
$pageDescription = 'A Casa do Gi - Alojamento Local e Produtos Regionais em Mogadouro.';
$bodyClass = 'homepage-new';

include INCLUDES_PATH . '/header.php';
?>

<!-- MOUNTAIN HERO - Full Screen with Parallax -->
<section class="relative h-screen w-full overflow-hidden parallax-mountain" id="mountain-hero" style="background-image: url('<?= $heroUrl ?>');">
    <!-- Gradient Overlay with Dynamic Opacity -->
    <div class="absolute inset-0 bg-black" style="opacity: <?= $heroOverlay ?>"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-black/50"></div>

    <!-- Content -->
    <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-6 z-10">
        <div class="transform transition-all duration-1000" id="hero-content">
            <span class="text-white/70 text-sm md:text-base font-bold tracking-[0.4em] uppercase mb-6 block animate-fade-in-up">
                Trás-os-Montes, Portugal
            </span>
            <h1 class="font-cursive text-6xl md:text-8xl lg:text-9xl text-cream mb-6 drop-shadow-2xl animate-fade-in-up animation-delay-200">
                A Casa do Gi
            </h1>
            <p class="text-cream/80 text-lg md:text-xl font-light max-w-2xl mx-auto animate-fade-in-up animation-delay-400">
                Onde a tradição transmontana encontra o conforto moderno
            </p>
        </div>
    </div>
</section>

<!-- SPLIT HERO SECTION -->
<div class="relative h-screen w-full flex flex-col md:flex-row overflow-hidden" id="split-hero">

    <!-- LEFT SIDE: Accommodation -->
    <div class="split-hero-left relative w-full md:w-1/2 h-1/2 md:h-full group overflow-hidden">
        <!-- Background Image -->
        <img src="<?= asset('images/IgrejaMatriz.jpg') ?>"
             alt="Igreja Matriz de Mogadouro"
             class="absolute inset-0 w-full h-full object-cover transition-transform duration-[2000ms] ease-out will-change-transform group-hover:scale-105">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-primary/40 group-hover:bg-primary/10 transition-colors duration-700"></div>

        <!-- Content -->
        <div class="split-content-left absolute inset-0 flex flex-col items-center justify-center text-center p-8 z-10">
            <style>
                .hero-text-spacing {
                    letter-spacing: 0.5em !important;
                    transition: all 0.5s ease !important;
                }
                .group:hover .hero-text-spacing {
                    letter-spacing: 0.8em !important;
                    color: #C5A059 !important;
                }
            </style>
            <span class="hero-text-spacing text-white/80 text-sm font-bold uppercase mb-10 opacity-0 transform translate-y-8 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-500 delay-100 block">
                Bem-vindo ao
            </span>
            <h2 class="font-cursive text-5xl md:text-7xl lg:text-8xl text-cream mb-8 drop-shadow-2xl transform transition-transform duration-500 group-hover:-translate-y-2 group-hover:text-white">
                Refúgio
            </h2>
            <div class="opacity-0 transform translate-y-8 transition-all duration-500 group-hover:opacity-100 group-hover:translate-y-0 delay-200">
                <a href="<?= $base ?>/alojamento/"
                   class="inline-flex items-center justify-center px-10 py-4 backdrop-blur-md bg-white/10 border border-white/30 text-white font-medium tracking-widest uppercase text-xs rounded-full transition-all duration-300 hover:bg-white hover:text-primary hover:border-white shadow-xl hover:shadow-2xl hover:scale-105 cursor-pointer">
                    Ver Alojamento
                </a>
            </div>
        </div>
    </div>

    <!-- CENTER BRANDING (Absolute) -->
    <div class="split-center-logo absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-30 pointer-events-none hidden md:block">
        <!-- Rotating Text Ring -->
        <div class="relative w-48 h-48 flex items-center justify-center">
            <div class="absolute w-full h-full animate-[spin_10s_linear_infinite] opacity-90">
               <svg viewBox="0 0 100 100" width="100%" height="100%">
                  <defs>
                    <path id="circle"
                      d="
                        M 50, 50
                        m -37, 0
                        a 37,37 0 1,1 74,0
                        a 37,37 0 1,1 -74,0"/>
                  </defs>
                  <text font-size="11" font-weight="bold" letter-spacing="2" fill="#FDFBF7" font-family="monospace">
                    <textPath xlink:href="#circle">
                      A CASA DO GI • MOGADOURO •
                    </textPath>
                  </text>
                </svg>
            </div>
            <!-- Center Logo -->
            <div class="absolute w-24 h-24 bg-primary/20 backdrop-blur-sm rounded-full border border-cream/30 flex items-center justify-center shadow-2xl">
                <span class="font-cursive text-4xl text-cream pt-2">Gi</span>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: Region/Shop -->
    <div class="split-hero-right relative w-full md:w-1/2 h-1/2 md:h-full group overflow-hidden">
        <!-- Background Image -->
        <img src="<?= asset('images/Castelo.jpg') ?>"
             alt="Castelo de Mogadouro"
             class="absolute inset-0 w-full h-full object-cover transition-transform duration-[2000ms] ease-out will-change-transform group-hover:scale-105">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/40 group-hover:bg-black/10 transition-colors duration-700"></div>

        <!-- Content -->
        <div class="split-content-right absolute inset-0 flex flex-col items-center justify-center text-center p-8 z-10">
             <span class="hero-text-spacing text-white/80 text-sm font-bold uppercase mb-10 opacity-0 transform translate-y-8 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-500 delay-100 block">
                Descubra a
            </span>
            <h2 class="font-cursive text-5xl md:text-7xl lg:text-8xl text-cream mb-8 drop-shadow-2xl transform transition-transform duration-500 group-hover:-translate-y-2 group-hover:text-white">
                Tradição
            </h2>
            <div class="flex flex-col md:flex-row gap-4 opacity-0 transform translate-y-8 transition-all duration-500 group-hover:opacity-100 group-hover:translate-y-0 delay-200">
                <a href="<?= $base ?>/atividades/"
                   class="inline-flex items-center justify-center px-8 py-3 backdrop-blur-md bg-secondary/80 border border-transparent text-white font-medium tracking-widest uppercase text-xs rounded-full hover:bg-secondary hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-secondary/30 cursor-pointer">
                    Explorar
                </a>
                <a href="<?= $base ?>/loja/"
                   class="inline-flex items-center justify-center px-8 py-3 backdrop-blur-md bg-white/10 border border-white/30 text-white font-medium tracking-widest uppercase text-xs rounded-full hover:bg-white hover:text-primary hover:border-white transition-all duration-300 shadow-lg hover:shadow-2xl hover:scale-105 cursor-pointer">
                    Loja
                </a>
            </div>
        </div>
    </div>
</div>

<!-- VALUE PROPOSITION (Intro) -->
<section class="py-24 bg-white relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 left-0 w-64 h-64 bg-cream-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-secondary/5 rounded-full mix-blend-multiply filter blur-3xl opacity-30 translate-x-1/3 translate-y-1/3"></div>

    <div class="max-w-4xl mx-auto px-6 relative z-10 text-center">
        <span class="text-accent text-sm font-bold tracking-[0.2em] uppercase mb-6 block animate-on-scroll" data-animation="fade-up">Bem-vindo a Mogadouro</span>
        <h2 class="font-serif text-3xl md:text-5xl lg:text-6xl text-primary leading-tight mb-8 animate-on-scroll" data-animation="fade-up" data-delay="100">
            "Mais do que um lugar para dormir,<br>
            <span class="italic text-accent">um lugar para sentir.</span>"
        </h2>
        
        <div class="w-24 h-1 bg-secondary mx-auto my-12 rounded-full animate-on-scroll" data-animation="zoom-in" data-delay="200"></div>
        
        <p class="text-charcoal/80 text-lg md:text-xl font-light leading-relaxed max-w-2xl mx-auto animate-on-scroll" data-delay="300">
            No coração do Nordeste Transmontano, a Casa do Gi recupera a arte de bem receber. 
            Entre o conforto moderno e a autenticidade rústica, criámos o cenário perfeito para as suas memórias.
        </p>
    </div>
</section>

<!-- MAIN PILLARS (Grid Layout) -->
<section class="py-20 bg-cream-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 animate-on-scroll">
            <span class="text-accent text-sm font-bold tracking-[0.2em] uppercase mb-3 block">A Nossa Oferta</span>
            <h2 class="font-serif text-3xl md:text-4xl text-primary">Experiências Autênticas</h2>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Card 1: Alojamento -->
            <div class="group relative bg-white rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden animate-on-scroll" data-delay="0">
                <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110" style="background-image: url('<?= asset('images/MogadouroAlojamento.jpg') ?>');"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-primary/90 via-primary/20 to-transparent transition-opacity duration-300"></div>
                
                <div class="relative h-[450px] flex flex-col justify-end p-8 text-white">
                    <div class="transform transition-transform duration-500 translate-y-4 group-hover:translate-y-0">
                        <span class="text-accent text-xs font-bold tracking-widest uppercase mb-2 block">Descanso</span>
                        <h3 class="font-serif text-3xl mb-4">Alojamento</h3>
                        <p class="text-white/80 mb-6 opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100 line-clamp-2">
                            Quartos acolhedores onde a tradição se encontra com o conforto moderno.
                        </p>
                        <a href="<?= $base ?>/alojamento/" class="inline-flex items-center text-sm font-bold uppercase tracking-widest border-b border-accent pb-1 hover:text-accent transition-colors">
                            Ver Quartos
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card 2: Atividades -->
            <div class="group relative bg-white rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden animate-on-scroll" data-delay="100">
                <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110" style="background-image: url('<?= asset('images/MogadouroAtividades.jpg') ?>');"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-primary/90 via-primary/20 to-transparent transition-opacity duration-300"></div>
                
                <div class="relative h-[450px] flex flex-col justify-end p-8 text-white">
                     <div class="transform transition-transform duration-500 translate-y-4 group-hover:translate-y-0">
                        <span class="text-accent text-xs font-bold tracking-widest uppercase mb-2 block">Natureza</span>
                        <h3 class="font-serif text-3xl mb-4">Atividades</h3>
                        <p class="text-white/80 mb-6 opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100 line-clamp-2">
                            Explore o Douro Internacional, trilhos e paisagens de cortar a respiração.
                        </p>
                        <a href="<?= $base ?>/atividades/" class="inline-flex items-center text-sm font-bold uppercase tracking-widest border-b border-accent pb-1 hover:text-accent transition-colors">
                            Explorar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card 3: Loja -->
            <div class="group relative bg-white rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden animate-on-scroll" data-delay="200">
                 <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110" style="background-image: url('<?= asset('images/MogadouroContacto.jpg') ?>');"></div>
                 <div class="absolute inset-0 bg-gradient-to-t from-primary/90 via-primary/20 to-transparent transition-opacity duration-300"></div>
                
                <div class="relative h-[450px] flex flex-col justify-end p-8 text-white">
                     <div class="transform transition-transform duration-500 translate-y-4 group-hover:translate-y-0">
                        <span class="text-accent text-xs font-bold tracking-widest uppercase mb-2 block">Sabores</span>
                        <h3 class="font-serif text-3xl mb-4">Loja Regional</h3>
                        <p class="text-white/80 mb-6 opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100 line-clamp-2">
                             Leve um pedaço de Mogadouro consigo. Azeite, mel e produtos selecionados.
                        </p>
                        <a href="<?= $base ?>/loja/" class="inline-flex items-center text-sm font-bold uppercase tracking-widest border-b border-accent pb-1 hover:text-accent transition-colors">
                            Visitar Loja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURE: SHOP / REGIONAL PRODUCTS -->
<section class="py-24 bg-white relative overflow-hidden">
     <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            
            <!-- Image Composition -->
            <div class="relative animate-on-scroll" data-animation="fade-right">
                <div class="relative rounded-t-full rounded-b-3xl overflow-hidden shadow-2xl aspect-[4/5] z-10">
                     <img src="<?= asset('images/MogadouroNeve.jpeg') ?>" alt="Produtos Regionais" class="w-full h-full object-cover font-cursive text-9xl text-white/20">
                     <!-- Overlay Text -->
                     <div class="absolute inset-0 bg-black/20 flex items-center justify-center">
                        <span class="font-cursive text-7xl md:text-9xl text-white/30 select-none">Mogadouro</span>
                     </div>
                </div>
                <!-- Floating Badge -->
                <div class="absolute bottom-10 -right-8 md:-right-12 z-20 bg-white p-6 rounded-2xl shadow-xl max-w-[200px] animate-bounce-slow">
                     <div class="flex items-center gap-3 mb-2">
                         <div class="w-3 h-3 rounded-full bg-green-500"></div>
                         <span class="text-xs font-bold uppercase tracking-wider text-primary">Bio & Local</span>
                     </div>
                     <p class="text-charcoal/80 text-sm font-serif italic">"Ingredientes colhidos com amor e tradição."</p>
                </div>
                <!-- Decorative Circle -->
                <div class="absolute -top-10 -left-10 w-32 h-32 border border-accent/30 rounded-full z-0"></div>
            </div>

            <!-- Content -->
            <div class="space-y-8 animate-on-scroll" data-animation="fade-left">
                <div>
                     <span class="text-accent text-sm font-bold tracking-[0.2em] uppercase mb-4 block">Da Nossa Terra</span>
                     <h2 class="font-serif text-4xl md:text-5xl text-primary mb-6 leading-tight">
                        Sabores que contam <br><span class="italic text-secondary">histórias.</span>
                     </h2>
                     <p class="text-charcoal/70 text-lg leading-relaxed mb-6">
                         Selecionámos o melhor que a nossa região tem para oferecer. Desde o azeite virgem extra extraído de olivais centenários ao mel puro das nossas serras.
                     </p>
                     
                     <ul class="space-y-4 mb-8">
                         <li class="flex items-center gap-3 text-charcoal/80">
                             <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                             Azeite Transmontano DOP
                         </li>
                         <li class="flex items-center gap-3 text-charcoal/80">
                             <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                             Mel de Rosmaninho
                         </li>
                         <li class="flex items-center gap-3 text-charcoal/80">
                             <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                             Vinhos e Enchidos Artesanais
                         </li>
                     </ul>
                </div>

                <a href="<?= $base ?>/loja/" class="inline-flex items-center px-8 py-4 bg-primary text-white font-medium rounded-full hover:bg-primary-700 transition-all shadow-lg hover:shadow-xl hover:-translate-y-1 group">
                    <span>Visitar a Loja Online</span>
                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>

        </div>
     </div>
</section>

<!-- TESTIMONIALS (Clean) -->
<section class="py-24 bg-cream-50 relative">
    <div class="max-w-4xl mx-auto px-6 text-center animate-on-scroll" data-animation="fade-up">
        
        <div class="mb-10 flex justify-center">
            <div class="flex gap-1 text-accent">
                <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            </div>
        </div>

        <figure class="relative z-10">
            <svg class="w-16 h-16 mx-auto mb-6 text-accent/20" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21L14.017 18C14.017 16.8954 14.9124 16 16.017 16H19.017C19.5693 16 20.017 15.5523 20.017 15V9C20.017 8.44772 19.5693 8 19.017 8H15.017C14.4647 8 14.017 8.44772 14.017 9V11C14.017 11.5523 13.5693 12 13.017 12H12.017V5H22.017V15C22.017 18.3137 19.3307 21 16.017 21H14.017ZM5.01697 21L5.01697 18C5.01697 16.8954 5.9124 16 7.01697 16H10.017C10.5693 16 11.017 15.5523 11.017 15V9C11.017 8.44772 10.5693 8 10.017 8H6.01697C5.46468 8 5.01697 8.44772 5.01697 9V11C5.01697 11.5523 4.56925 12 4.01697 12H3.01697V5H13.017V15C13.017 18.3137 10.3307 21 7.01697 21H5.01697Z"/></svg>
            
            <blockquote class="font-serif text-2xl md:text-3xl text-primary italic leading-relaxed mb-8">
                "Um lugar onde o tempo pára e a alma respira. A Casa do Gi não é apenas um alojamento, é uma experiência de pura hospitalidade transmontana."
            </blockquote>
            
            <figcaption class="flex flex-col items-center">
                <span class="font-bold text-primary uppercase tracking-widest text-sm">A Casa do Gi</span>
                <span class="text-charcoal/50 text-xs mt-1">Desde 2024</span>
            </figcaption>
        </figure>
    </div>
</section>

<!-- CTA / NEWSLETTER -->
<section class="py-24 bg-primary text-cream relative overflow-hidden">
    <div class="absolute inset-0 bg-cover bg-center opacity-10" style="background-image: url('<?= asset('images/IgrejaMatriz.jpg') ?>'); mix-blend-mode: overlay;"></div>
    
    <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
        <h2 class="font-serif text-3xl md:text-5xl mb-8 leading-tight">Pronto para a sua estadia?</h2>
        <p class="text-lg text-cream/80 mb-10 max-w-2xl mx-auto">
            Reserve agora a sua escapadinha em Mogadouro e desfrute de momentos inesquecíveis.
        </p>
        
        <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
            <a href="<?= $base ?>/contactos/" class="min-w-[200px] px-8 py-4 bg-accent text-primary font-bold rounded-full hover:bg-white transition-all shadow-xl hover:-translate-y-1">
                Contactar Agora
            </a>
            <a href="<?= $base ?>/alojamento/" class="min-w-[200px] px-8 py-4 border border-white/30 text-white font-bold rounded-full hover:bg-white hover:text-primary hover:border-white transition-all">
                Ver Disponibilidade
            </a>
        </div>
    </div>
</section>

<style>
/* Custom animations */
@keyframes bounce-slow {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
.animate-bounce-slow {
    animation: bounce-slow 3s ease-in-out infinite;
}

/* Parallax Background - Simple CSS approach */
.parallax-mountain {
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
}

@media (max-width: 768px) {
    .parallax-mountain {
        background-attachment: scroll;
    }
}

/* Fade In Up Animation */
@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fade-in-up {
    animation: fade-in-up 1s ease-out forwards;
    opacity: 0;
}
.animation-delay-200 { animation-delay: 0.2s; }
.animation-delay-400 { animation-delay: 0.4s; }
.animation-delay-600 { animation-delay: 0.6s; }

/* Split Hero Scroll Animations */
.split-hero-left {
    opacity: 0;
    transform: translateX(-100px);
    transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
}

.split-hero-right {
    opacity: 0;
    transform: translateX(100px);
    transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
    transition-delay: 0.2s;
}

.split-center-logo {
    opacity: 0;
    transform: translate(-50%, -50%) scale(0.5);
    transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    transition-delay: 0.5s;
}

/* Active states when in view */
#split-hero.in-view .split-hero-left {
    opacity: 1;
    transform: translateX(0);
}

#split-hero.in-view .split-hero-right {
    opacity: 1;
    transform: translateX(0);
}

#split-hero.in-view .split-center-logo {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
}

/* Content fade in */
.split-content-left,
.split-content-right {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    transition-delay: 0.6s;
}

#split-hero.in-view .split-content-left,
#split-hero.in-view .split-content-right {
    opacity: 1;
    transform: translateY(0);
}

/* Mobile: Stack animations */
@media (max-width: 768px) {
    .split-hero-left {
        transform: translateY(-50px);
    }
    .split-hero-right {
        transform: translateY(50px);
        transition-delay: 0.3s;
    }
    #split-hero.in-view .split-hero-left,
    #split-hero.in-view .split-hero-right {
        transform: translateY(0);
    }
}
</style>

<script>
// Split Hero Scroll Animation
document.addEventListener('DOMContentLoaded', function() {
    const splitHero = document.getElementById('split-hero');

    if (splitHero) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -50px 0px'
        });

        observer.observe(splitHero);
    }
});
</script>

<?php include INCLUDES_PATH . '/footer.php'; ?>
