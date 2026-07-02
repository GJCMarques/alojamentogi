<?php

die("Cheguei aqui vivo 1");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/includes/init.php';

use Core\Database;

$lang = \Core\Language::getInstance();
$db = Database::getInstance();
$base = basePath();
$isEnglish = $lang->isEnglish();

$pageHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = 'home' AND is_active = 1");
$heroMedia = $pageHero ? $db->fetch("SELECT * FROM media WHERE entity_type = 'hero' AND entity_id = ? AND is_cover = 1", [$pageHero['id']]) : null;
$heroImage = $heroMedia['file_path'] ?? 'images/MogadouroAtividades.jpg';
$heroOverlay = $pageHero['hero_overlay_opacity'] ?? 0.30;
$heroUrl = $heroImage[0] === '/' ? basePath() . $heroImage : asset($heroImage);

$langId = $isEnglish ? 2 : 1;

$menuImages = [
    'accommodation' => 'images/MogadouroAlojamento.jpg',
    'activities' => 'images/MogadouroAtividades.jpg',
    'shop' => 'images/MogadouroContacto.jpg',
    'contact' => 'images/FotoGi.png'
];

$heroRows = $db->fetchAll(
    "SELECT ph.page_key, m.file_path
     FROM page_heroes ph
     INNER JOIN media m ON m.entity_type = 'hero' AND m.entity_id = ph.id AND m.is_cover = 1
     WHERE ph.page_key IN ('accommodation', 'alojamento', 'activities', 'shop', 'contact')
       AND ph.is_active = 1"
);
foreach ($heroRows as $row) {
    $key = $row['page_key'] === 'alojamento' ? 'accommodation' : $row['page_key'];
    if (!empty($row['file_path']) && isset($menuImages[$key])) {
        $menuImages[$key] = $row['file_path'];
    }
}

$pageTitle = 'Início';
$pageDescription = 'A Casa do Gi - Alojamento Local e Produtos Regionais em Mogadouro.';
$bodyClass = 'homepage-new';

include INCLUDES_PATH . '/header.php';

?>

<!-- HERO -->

<section class="relative h-screen w-full overflow-hidden" id="mountain-hero" style="background-image: url('<?= $heroUrl ?>'); background-size: cover; background-position: center; background-attachment: fixed;">
    <div class="absolute inset-0 bg-black" style="opacity: <?= $heroOverlay ?>"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-black/60"></div>
    <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-6 z-10">
        <div id="hero-content">
            <span class="hero-animate-up hero-delay-1 text-white/70 text-sm md:text-base font-bold tracking-[0.4em] uppercase mb-6 block">
                Tras-os-Montes, Portugal
            </span>
            <h1 class="hero-animate-up hero-delay-2 font-cursive text-6xl md:text-8xl lg:text-9xl text-cream mb-6 drop-shadow-2xl">
                A Casa do Gi
            </h1>
            <p class="hero-animate-up hero-delay-3 text-cream/80 text-lg md:text-xl font-light max-w-2xl mx-auto">
                <?= $content['home_hero_subtitle'] ?? 'Onde a tradição transmontana encontra o conforto moderno' ?>
            </p>
        </div>
    </div>
</section>

<!-- SPLIT HERO -->
<div class="relative h-screen w-full flex flex-col md:flex-row overflow-hidden" id="split-hero">
    <div class="split-hero-left split-panel split-left relative w-full md:w-1/2 h-1/2 md:h-full group overflow-hidden">
        <img src="<?= resolveContentImage(content('home_image_split_left', 'images/IgrejaMatriz.jpg')) ?>" alt="Igreja Matriz de Mogadouro" class="absolute inset-0 w-full h-full object-cover transition-transform duration-[2000ms] ease-out will-change-transform group-hover:scale-105">
        <div class="absolute inset-0 bg-primary/40 group-hover:bg-primary/10 transition-colors duration-700"></div>
        <div class="split-content-left split-content absolute inset-0 flex flex-col items-center justify-center text-center p-8 z-10">
            <span class="text-white/80 text-sm font-bold tracking-[0.5em] uppercase mb-10 opacity-0 translate-y-8 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-500 delay-100 block group-hover:tracking-[0.8em] group-hover:text-accent">
                <?= $content['home_split_left_label'] ?? 'Bem-vindo ao' ?>
            </span>
            <h2 class="font-cursive text-5xl md:text-7xl lg:text-8xl text-cream mb-8 drop-shadow-2xl transition-transform duration-500 group-hover:-translate-y-2 group-hover:text-white">
                <?= $content['home_split_left_title'] ?? 'Refúgio' ?>
            </h2>
            <div class="opacity-0 translate-y-8 transition-all duration-500 group-hover:opacity-100 group-hover:translate-y-0 delay-200">
                <a href="<?= $base ?>/alojamento/" class="inline-flex items-center justify-center px-10 py-4 backdrop-blur-md bg-white/10 border border-white/30 text-white font-medium tracking-widest uppercase text-xs rounded-full transition-all duration-300 hover:bg-white hover:text-primary shadow-xl hover:shadow-2xl hover:scale-105">
                    <?= $isEnglish ? 'View Accommodation' : 'Ver Alojamento' ?>
                </a>
            </div>
        </div>
    </div>
    <div class="split-center-logo absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-30 pointer-events-none hidden md:block">
        <div class="relative w-48 h-48 flex items-center justify-center">
            <div class="absolute w-full h-full animate-[spin_10s_linear_infinite] opacity-90">
               <svg viewBox="0 0 100 100" width="100%" height="100%">
                  <defs><path id="circle" d="M 50, 50 m -37, 0 a 37,37 0 1,1 74,0 a 37,37 0 1,1 -74,0"/></defs>
                  <text font-size="11" font-weight="bold" letter-spacing="2" fill="#FDFBF7" font-family="monospace">
                    <textPath xlink:href="#circle">A CASA DO GI &bull; MOGADOURO &bull;</textPath>
                  </text>
                </svg>
            </div>
            <div class="absolute w-24 h-24 bg-primary/20 backdrop-blur-sm rounded-full border border-cream/30 flex items-center justify-center shadow-2xl">
                <span class="font-cursive text-4xl text-cream pt-2">Gi</span>
            </div>
        </div>
    </div>
    <div class="split-hero-right split-panel split-right relative w-full md:w-1/2 h-1/2 md:h-full group overflow-hidden">
        <img src="<?= resolveContentImage(content('home_image_split_right', 'images/Castelo.jpg')) ?>" alt="Castelo de Mogadouro" class="absolute inset-0 w-full h-full object-cover transition-transform duration-[2000ms] ease-out will-change-transform group-hover:scale-105">
        <div class="absolute inset-0 bg-black/40 group-hover:bg-black/10 transition-colors duration-700"></div>
        <div class="split-content-right split-content absolute inset-0 flex flex-col items-center justify-center text-center p-8 z-10">
            <span class="text-white/80 text-sm font-bold tracking-[0.5em] uppercase mb-10 opacity-0 translate-y-8 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-500 delay-100 block group-hover:tracking-[0.8em] group-hover:text-accent">
                <?= $content['home_split_right_label'] ?? 'Descubra a' ?>
            </span>
            <h2 class="font-cursive text-5xl md:text-7xl lg:text-8xl text-cream mb-8 drop-shadow-2xl transition-transform duration-500 group-hover:-translate-y-2 group-hover:text-white">
                <?= $content['home_split_right_title'] ?? 'Tradição' ?>
            </h2>
            <div class="flex flex-col md:flex-row gap-4 opacity-0 translate-y-8 transition-all duration-500 group-hover:opacity-100 group-hover:translate-y-0 delay-200">
                <a href="<?= $base ?>/atividades/" class="inline-flex items-center justify-center px-10 py-4 backdrop-blur-md bg-white/10 border border-white/30 text-white font-medium tracking-widest uppercase text-xs rounded-full transition-all duration-300 hover:bg-white hover:text-primary shadow-xl hover:shadow-2xl hover:scale-105">
                    <?= $isEnglish ? 'Explore' : 'Explorar' ?>
                </a>
                <a href="<?= $base ?>/loja/" class="inline-flex items-center justify-center px-10 py-4 backdrop-blur-md bg-white/10 border border-white/30 text-white font-medium tracking-widest uppercase text-xs rounded-full transition-all duration-300 hover:bg-white hover:text-primary shadow-xl hover:shadow-2xl hover:scale-105">
                    <?= $isEnglish ? 'Shop' : 'Loja' ?>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ====== PAGE BODY ====== -->

<!-- BENTO GRID - 3 Experiences -->
<!-- EXPLORE MENU - DYNAMIC ACCORDION -->
<section class="py-20 bg-white min-h-[80vh] flex flex-col justify-center">
    <div class="max-w-[1800px] w-full mx-auto px-4 sm:px-6">

        <div class="text-center mb-16 animate-on-scroll">
            <h2 class="font-serif text-3xl md:text-4xl text-primary mb-4">
                <?= $content['home_explore_title'] ?? 'Explore o Nosso Mundo' ?>
            </h2>
            <div class="w-24 h-1 bg-accent mx-auto"></div>
        </div>

        <!-- Dynamic Flex Accordion -->
        <div class="flex flex-col md:flex-row h-[800px] gap-2 md:gap-4">

            <!-- CARD 1: ALOJAMENTO -->
            <a href="<?= $base ?>/alojamento/" class="animate-on-scroll relative flex-1 group hover:grow-[1.5] transition-[flex-grow] duration-700 ease-[cubic-bezier(0.25,1,0.5,1)] overflow-hidden rounded-3xl cursor-pointer">
                <div class="absolute inset-0 bg-cover bg-center transition-transform duration-1000 group-hover:scale-110"
                     style="background-image: url('<?= resolveContentImage($menuImages['accommodation']) ?>');"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent transition-opacity duration-500"></div>

                <div class="absolute bottom-0 left-0 w-full p-8 md:p-12 z-10 flex flex-col justify-end h-full">
                    <span class="text-accent text-xs font-bold tracking-[0.2em] uppercase mb-2 opacity-80"><?= $content['home_card1_label'] ?? 'Dormir' ?></span>
                    <h3 class="font-cursive text-5xl md:text-6xl text-white mb-2 transform origin-left transition-transform duration-500 group-hover:scale-105">
                        <?= $content['home_card1_title'] ?? 'Alojamento' ?>
                    </h3>
                    <div class="max-h-0 overflow-hidden group-hover:max-h-40 transition-all duration-700 ease-out opacity-0 group-hover:opacity-100">
                        <p class="text-white/90 text-lg font-light mt-4 max-w-md leading-relaxed">
                            <?= $content['home_card1_text'] ?? 'Sinta o conforto das nossas casas rústicas.' ?>
                        </p>
                        <span class="inline-flex items-center mt-6 text-white text-sm font-bold uppercase tracking-widest border-b border-accent pb-1">
                            <?= $content['home_card1_cta'] ?? 'Ver Casas' ?>
                        </span>
                    </div>
                </div>
            </a>

            <!-- CARD 2: ATIVIDADES -->
            <a href="<?= $base ?>/atividades/" class="animate-on-scroll delay-100 relative flex-1 group hover:grow-[1.5] transition-[flex-grow] duration-700 ease-[cubic-bezier(0.25,1,0.5,1)] overflow-hidden rounded-3xl cursor-pointer">
                <div class="absolute inset-0 bg-cover bg-center transition-transform duration-1000 group-hover:scale-110"
                     style="background-image: url('<?= resolveContentImage($menuImages['activities']) ?>');"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent transition-opacity duration-500"></div>

                <div class="absolute bottom-0 left-0 w-full p-8 md:p-12 z-10 flex flex-col justify-end h-full">
                    <span class="text-accent text-xs font-bold tracking-[0.2em] uppercase mb-2 opacity-80"><?= $content['home_card2_label'] ?? 'Experienciar' ?></span>
                    <h3 class="font-cursive text-5xl md:text-6xl text-white mb-2 transform origin-left transition-transform duration-500 group-hover:scale-105">
                        <?= $content['home_card2_title'] ?? 'Atividades' ?>
                    </h3>
                    <div class="max-h-0 overflow-hidden group-hover:max-h-40 transition-all duration-700 ease-out opacity-0 group-hover:opacity-100">
                        <p class="text-white/90 text-lg font-light mt-4 max-w-md leading-relaxed">
                            <?= $content['home_card2_text'] ?? 'Descubra a natureza e história de Mogadouro.' ?>
                        </p>
                        <span class="inline-flex items-center mt-6 text-white text-sm font-bold uppercase tracking-widest border-b border-accent pb-1">
                            <?= $content['home_card2_cta'] ?? 'Explorar' ?>
                        </span>
                    </div>
                </div>
            </a>

            <!-- CARD 3: LOJA -->
            <a href="<?= $base ?>/loja/" class="animate-on-scroll delay-200 relative flex-1 group hover:grow-[1.5] transition-[flex-grow] duration-700 ease-[cubic-bezier(0.25,1,0.5,1)] overflow-hidden rounded-3xl cursor-pointer">
                <div class="absolute inset-0 bg-cover bg-center transition-transform duration-1000 group-hover:scale-110"
                     style="background-image: url('<?= resolveContentImage($menuImages['shop']) ?>');"></div>
                 <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent transition-opacity duration-500"></div>

                <div class="absolute bottom-0 left-0 w-full p-8 md:p-12 z-10 flex flex-col justify-end h-full">
                    <span class="text-accent text-xs font-bold tracking-[0.2em] uppercase mb-2 opacity-80"><?= $content['home_card3_label'] ?? 'Saborear' ?></span>
                    <h3 class="font-cursive text-5xl md:text-6xl text-white mb-2 transform origin-left transition-transform duration-500 group-hover:scale-105">
                        <?= $content['home_card3_title'] ?? 'Loja Regional' ?>
                    </h3>
                    <div class="max-h-0 overflow-hidden group-hover:max-h-40 transition-all duration-700 ease-out opacity-0 group-hover:opacity-100">
                        <p class="text-white/90 text-lg font-light mt-4 max-w-md leading-relaxed">
                            <?= $content['home_card3_text'] ?? 'Sabores autênticos de Trás-os-Montes.' ?>
                        </p>
                        <span class="inline-flex items-center mt-6 text-white text-sm font-bold uppercase tracking-widest border-b border-accent pb-1">
                            <?= $content['home_card3_cta'] ?? 'Comprar' ?>
                        </span>
                    </div>
                </div>
            </a>

            <!-- CARD 4: SOBRE / CONTACTOS -->
            <a href="<?= $base ?>/contactos/" class="animate-on-scroll delay-300 relative flex-1 group hover:grow-[1.5] transition-[flex-grow] duration-700 ease-[cubic-bezier(0.25,1,0.5,1)] overflow-hidden rounded-3xl cursor-pointer">
                <div class="absolute inset-0 bg-cover bg-center transition-transform duration-1000 group-hover:scale-110"
                     style="background-image: url('<?= resolveContentImage($menuImages['contact']) ?>');"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent transition-opacity duration-500"></div>

                <div class="absolute bottom-0 left-0 w-full p-8 md:p-12 z-10 flex flex-col justify-end h-full">
                    <span class="text-accent text-xs font-bold tracking-[0.2em] uppercase mb-2 opacity-80"><?= $content['home_card4_label'] ?? 'Conectar' ?></span>
                    <h3 class="font-cursive text-5xl md:text-6xl text-white mb-2 transform origin-left transition-transform duration-500 group-hover:scale-105">
                        <?= $content['home_card4_title'] ?? 'Contactos' ?>
                    </h3>
                    <div class="max-h-0 overflow-hidden group-hover:max-h-40 transition-all duration-700 ease-out opacity-0 group-hover:opacity-100">
                        <p class="text-white/90 text-lg font-light mt-4 max-w-md leading-relaxed">
                            <?= $content['home_card4_text'] ?? 'Fale connosco e planeie a sua visita.' ?>
                        </p>
                        <span class="inline-flex items-center mt-6 text-white text-sm font-bold uppercase tracking-widest border-b border-accent pb-1">
                            <?= $content['home_card4_cta'] ?? 'Contactar' ?>
                        </span>
                    </div>
                </div>
            </a>

        </div>
    </div>
</section>

<!-- SECTION: ABOUT US (Storytelling) -->
<section class="relative py-24 md:py-32 bg-white overflow-hidden">
    <div class="max-w-[1400px] mx-auto px-6 md:px-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-16 lg:gap-24 items-center">

            <!-- Left: Text Content -->
            <div class="md:order-1 animate-on-scroll">
                <span class="text-accent text-xs font-bold tracking-[0.3em] uppercase block mb-6">
                    <?= $content['home_about_label'] ?? 'A Nossa História' ?>
                </span>
                <h2 class="font-serif text-4xl md:text-5xl lg:text-6xl text-primary mb-8 leading-tight">
                    <?= $content['home_about_title'] ?? 'Mais que uma casa,<br>um <span class="italic text-accent">legado</span>.' ?>
                </h2>
                <div class="space-y-6 text-charcoal/70 text-lg leading-relaxed mb-10">
                    <p>
                        <?= $content['home_about_text1'] ?? 'A Casa do Gi nasceu da vontade de preservar as raízes transmontanas. O que outrora foi uma casa de família, é hoje um refúgio para quem procura a autenticidade do campo.' ?>
                    </p>
                    <p>
                        <?= $content['home_about_text2'] ?? 'Aqui, o tempo abranda. Convidamo-lo a descobrir as tradições, os sabores e as gentes que fazem de Mogadouro um lugar único no mundo.' ?>
                    </p>
                </div>

                <div class="mt-12">
                     <a href="<?= $base ?>/sobre-nos/" class="inline-flex items-center gap-3 px-8 py-4 bg-primary text-white rounded-full transition-all duration-300 hover:bg-accent hover:scale-105 shadow-lg group">
                        <span class="text-xs font-bold tracking-[0.2em] uppercase"><?= $content['home_about_cta'] ?? 'Ler História Completa' ?></span>
                        <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center group-hover:bg-white/20 transition-colors">
                            <svg class="w-4 h-4 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Right: Image Composition -->
            <div class="relative md:order-2 animate-on-scroll" data-delay="200">
                <!-- Main Image -->
                <div class="relative z-10 rounded-[2.5rem] overflow-hidden shadow-2xl ring-1 ring-black/5">
                    <div class="absolute inset-0 bg-primary/10 mix-blend-multiply pointer-events-none"></div>
                    <img src="<?= resolveContentImage(content('home_image_about', 'images/MogadouroSobre.png')) ?>"
                         alt="A Casa do Gi"
                         class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-[1.5s] ease-[cubic-bezier(0.25,1,0.5,1)]">
                </div>

                <!-- Decorative Elements -->
                <div class="absolute -top-12 -right-12 w-48 h-48 bg-cream-100 rounded-full z-0 opacity-60 blur-3xl"></div>
                <div class="absolute -bottom-12 -left-12 w-72 h-72 bg-accent/10 rounded-full z-0 blur-3xl"></div>

                <!-- Floating Badge -->
                <div class="absolute -bottom-8 -left-8 z-20 bg-white p-6 shadow-[0_20px_50px_rgba(0,0,0,0.1)] rounded-2xl max-w-[200px] hidden md:block border border-gray-100">
                    <span class="block font-cursive text-4xl text-accent mb-0 leading-[0.8] relative top-1">100%</span>
                    <span class="text-[10px] text-primary/60 uppercase tracking-[0.2em] font-bold block mt-3 border-t border-gray-100 pt-3">
                        <?= $isEnglish ? 'Transmontano' : 'Transmontano' ?>
                    </span>
                </div>
            </div>

        </div>
    </div>
</section>

<style>
/* MARQUEE ANIMATION */
.marquee-wrapper {
    mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
    -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
}
.animate-marquee {
    animation: marquee 40s linear infinite;
    min-width: 100%;
}
@keyframes marquee {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

/* OUTLINE TEXT UTILITY */
.stroke-text {
    -webkit-text-stroke: 1px rgba(26, 26, 26, 0.2);
    color: transparent;
}

/* CUSTOM UNDERLINE */
.text-translucent-underline {
    position: relative;
    white-space: nowrap;
    z-index: 10;
}
.text-translucent-underline::after {
    content: '';
    position: absolute;
    bottom: 2px;
    left: 0;
    width: 100%;
    height: 12px;
    background-color: #C6A87C; /* Your Accent Color */
    opacity: 0.3;
    z-index: -1;
    transform: skewX(-12deg);
}
/* --- UTILITY ANIMATIONS --- */
.reveal-on-scroll {
    opacity: 0;
    transform: translateY(30px);
    transition: all 1s cubic-bezier(0.2, 0.8, 0.2, 1);
    will-change: opacity, transform;
}
.reveal-on-scroll.is-visible {
    opacity: 1;
    transform: translateY(0);
}

/* Stagger delays */
.delay-100 { transition-delay: 0.1s; }
.delay-200 { transition-delay: 0.2s; }
.delay-300 { transition-delay: 0.3s; }

/* HERO ANIMATIONS */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes scaleIn {
    from { transform: scale(1.1); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
.hero-animate-up {
    animation: fadeInUp 1.2s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
    opacity: 0;
}
.hero-delay-1 { animation-delay: 0.2s; }
.hero-delay-2 { animation-delay: 0.4s; }
.hero-delay-3 { animation-delay: 0.6s; }

/* SPLIT HERO SPECIFIC */
.split-panel {
    transition: transform 1.5s cubic-bezier(0.2, 0.8, 0.2, 1), opacity 1.5s ease;
    will-change: transform, opacity;
}
.split-left { transform: translateX(-100%); opacity: 0; }
.split-right { transform: translateX(100%); opacity: 0; }
.split-active .split-left { transform: translateX(0); opacity: 1; }
.split-active .split-right { transform: translateX(0); opacity: 1; }

.split-content {
    opacity: 0;
    transform: translateY(20px);
    transition: all 1s ease 0.5s;
}
.split-active .split-content { opacity: 1; transform: translateY(0); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // 1. SPLIT HERO INTERSECTION OBSERVER
    const splitHero = document.getElementById('split-hero');
    if (splitHero) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    splitHero.classList.add('split-active');
                    // Optional: Stop observing once activated
                    // observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 }); // Trigger when 15% visible
        observer.observe(splitHero);
    }

    // 2. REVEAL ON SCROLL (Generic)
    const revealElements = document.querySelectorAll('.animate-on-scroll');
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                entry.target.classList.add('reveal-on-scroll');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: "0px 0px -50px 0px" });

    revealElements.forEach(el => {
        el.classList.add('reveal-on-scroll'); // Initialize hidden state
        revealObserver.observe(el);
    });

    // 3. PARALLAX EFFECTS (Hero + General)
    const mountainHero = document.getElementById('mountain-hero');
    const heroContent = document.getElementById('hero-content');

    if (mountainHero && heroContent) {
        window.addEventListener('scroll', () => {
            const scrolled = window.scrollY;
            if (scrolled < window.innerHeight) {
                // Parallax text
                heroContent.style.transform = `translateY(${scrolled * 0.4}px)`;
                heroContent.style.opacity = 1 - (scrolled / 700);
                // Parallax background (subtle)
                mountainHero.style.backgroundPositionY = `${scrolled * 0.5}px`;
            }
        }, { passive: true });
    }
});
</script>

<?php include INCLUDES_PATH . '/footer.php'; ?>
