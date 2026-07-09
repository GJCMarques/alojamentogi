<?php

$lang = \Core\Language::getInstance();
$currentLang = $lang->getCurrentLang();
$isEnglish = $lang->isEnglish();
$base = basePath();

$pageTitle = $pageTitle ?? 'A Casa do Gi';
$pageDescription = $pageDescription ?? ($isEnglish
    ? 'Local accommodation in Mogadouro, Portugal. Simplicity, warmth and love.'
    : 'Alojamento local em Mogadouro, Portugal. Simplicidade, acolhimento e muito amor.');
$bodyClass = $bodyClass ?? '';
$hideNav = $hideNav ?? false;

$navItems = $isEnglish ? [
    ['url' => $base . '/en/', 'label' => 'Home'],
    ['url' => $base . '/en/accommodation/', 'label' => 'Accommodation'],
    ['url' => $base . '/en/activities/', 'label' => 'Activities'],
    ['url' => $base . '/en/about-us/', 'label' => 'About Us'],
    ['url' => $base . '/en/contact/', 'label' => 'Contact'],
    ['url' => $base . '/en/shop/', 'label' => 'Shop'],
] : [
    ['url' => $base . '/', 'label' => 'Início'],
    ['url' => $base . '/alojamento/', 'label' => 'Alojamento'],
    ['url' => $base . '/atividades/', 'label' => 'Atividades'],
    ['url' => $base . '/sobre-nos/', 'label' => 'Sobre Nós'],
    ['url' => $base . '/contactos/', 'label' => 'Contactos'],
    ['url' => $base . '/loja/', 'label' => 'Loja'],
];

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>" class="scroll-smooth overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($pageDescription) ?>">

    <title><?= e($pageTitle) ?> | A Casa do Gi</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= asset('images/CGsimbUpNB.ico') ?>">
    <link rel="shortcut icon" href="<?= asset('images/CGsimbUpNB.ico') ?>">

    <!-- Open Graph / Social Meta -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e($pageTitle) ?> | A Casa do Gi">
    <meta property="og:description" content="<?= e($pageDescription) ?>">
    <meta property="og:url" content="<?= e(rtrim($base, '/') . ($_SERVER['REQUEST_URI'] ?? '/')) ?>">
    <meta property="og:site_name" content="A Casa do Gi">
    <meta property="og:image" content="<?= isset($ogImage) ? e($ogImage) : asset('images/MogadouroNeve.webp') ?>">
    <meta property="og:locale" content="<?= $isEnglish ? 'en_GB' : 'pt_PT' ?>">
    <?php if ($isEnglish): ?>
    <meta property="og:locale:alternate" content="pt_PT">
    <?php else: ?>
    <meta property="og:locale:alternate" content="en_GB">
    <?php endif; ?>

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($pageTitle) ?> | A Casa do Gi">
    <meta name="twitter:description" content="<?= e($pageDescription) ?>">
    <meta name="twitter:image" content="<?= isset($ogImage) ? e($ogImage) : asset('images/MogadouroNeve.webp') ?>">

    <!-- Progressive enhancement: só esconder conteúdo animado se o JS estiver disponível -->
    <script>document.documentElement.classList.add('js');</script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Great+Vibes&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (compilado localmente) -->
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>?v=<?= @filemtime(ROOT_PATH . '/assets/css/app.css') ?>">

    <!-- Custom Styles -->
    <style>
        /* Base styles */
        body {
            font-family: 'Poppins', system-ui, sans-serif;
            background-color: #FDFBF7;
            color: #2D3748;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', system-ui, sans-serif;
        }

        .font-cursive {
            font-family: 'Great Vibes', cursive;
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Smooth parallax */
        .parallax-bg {
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        @media (max-width: 768px) {
            .parallax-bg {
                background-attachment: scroll;
            }
        }

        /* Header smooth transitions */
        #main-header {
            transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: background-color, height, box-shadow;
        }

        #main-header.scrolled {
            background-color: rgba(30, 56, 66, 0.98); /* Primary-600 */
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
        }

        /* Logo smooth scaling with CSS transform */
        .logo-text {
            transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: left center;
            will-change: transform;
        }

        #main-header.scrolled .logo-text {
            transform: scale(0.8);
        }

        /* Header inner height transition */
        .header-inner {
            transition: height 1s cubic-bezier(0.4, 0, 0.2, 1), padding 1s cubic-bezier(0.4, 0, 0.2, 1);
            height: 8.5rem;
            will-change: height;
        }

        #main-header.scrolled .header-inner {
            height: 6rem;
        }

        /* Navigation transitions */
        .nav-link {
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 50%;
            width: 0;
            height: 2px;
            background-color: #C5A059;
            transition: width 0.3s ease, left 0.3s ease;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
            left: 0;
        }

        /* Mobile menu animation */
        .mobile-menu {
            /* Handled by Tailwind classes */
        }

        .mobile-menu.open {
            opacity: 1;
            visibility: visible;
        }

        /* Mobile menu backdrop */
        .mobile-backdrop {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0.2s ease;
        }

        .mobile-backdrop.open {
            opacity: 1;
            visibility: visible;
        }

        /* Cart badge */
        .cart-badge {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* Scroll animations — só escondem quando há JS (.js), para o conteúdo/imagens
           nunca ficarem invisíveis se o observer não correr no primeiro carregamento. */
        .js .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .animate-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .js .animate-on-scroll[data-animation="fade-left"] {
            transform: translateX(-30px);
        }

        .animate-on-scroll[data-animation="fade-left"].visible {
            transform: translateX(0);
        }

        .js .animate-on-scroll[data-animation="fade-right"] {
            transform: translateX(30px);
        }

        .animate-on-scroll[data-animation="fade-right"].visible {
            transform: translateX(0);
        }

        .js .animate-on-scroll[data-animation="zoom-in"] {
            transform: scale(0.9);
        }

        .animate-on-scroll[data-animation="zoom-in"].visible {
            transform: scale(1);
        }

        /* Stagger delays */
        .animate-on-scroll[data-delay="100"] { transition-delay: 0.1s; }
        .animate-on-scroll[data-delay="200"] { transition-delay: 0.2s; }
        .animate-on-scroll[data-delay="300"] { transition-delay: 0.3s; }
        .animate-on-scroll[data-delay="400"] { transition-delay: 0.4s; }
        .animate-on-scroll[data-delay="500"] { transition-delay: 0.5s; }

        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            .animate-on-scroll,
            .logo-text,
            .header-inner,
            #main-header,
            .mobile-menu,
            .mobile-backdrop {
                transition: none !important;
                animation: none !important;
            }
            .animate-on-scroll {
                opacity: 1;
                transform: none;
            }
        }
    </style>

    <!-- CSRF Token for AJAX -->
    <?= \Core\CSRF::tokenMeta() ?>
</head>
<body class="bg-cream text-charcoal antialiased <?= e($bodyClass) ?>">
    <?php if (!$hideNav): ?>
    <!-- Header Navigation -->
    <header id="main-header" class="fixed top-0 left-0 right-0 z-[100] bg-transparent text-cream">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="header-inner flex items-center justify-between">
                <!-- Logo / Brand -->
                <a href="<?= $isEnglish ? $base . '/en/' : $base . '/' ?>" class="flex items-center group">
                    <!-- Text Logo -->
                    <span class="logo-text font-cursive text-5xl text-cream group-hover:text-accent transition-colors duration-300 drop-shadow-md pb-2 relative top-1">
                        A Casa do Gi
                    </span>
                </a>

                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center space-x-6">
                    <?php foreach ($navItems as $item):

                        $homeUrlPT = $base . '/';
                        $homeUrlEN = $base . '/en/';
                        $itemUrlTrimmed = rtrim($item['url'], '/');
                        $currentPathTrimmed = rtrim($currentPath, '/');

                        if ($item['url'] === $homeUrlPT || $item['url'] === $homeUrlEN) {
                            $isActive = ($currentPath === $item['url']) ||
                                       ($currentPathTrimmed === $itemUrlTrimmed);
                        } else {
                            $isActive = ($currentPath === $item['url']) ||
                                       ($currentPathTrimmed === $itemUrlTrimmed) ||
                                       (strpos($currentPath, $itemUrlTrimmed . '/') === 0);
                        }
                    ?>
                    <a href="<?= $item['url'] ?>"
                       class="nav-link text-[13px] font-sans font-medium text-cream hover:text-accent transition-colors tracking-wide uppercase <?= $isActive ? 'active text-accent' : '' ?>">
                        <?= e($item['label']) ?>
                    </a>
                    <?php endforeach; ?>

                    <!-- Separator -->
                    <div class="h-6 w-px bg-cream/30"></div>

                    <!-- Language Switcher -->
                    <div class="flex items-center ml-2">
                        <?php $switchLang = $isEnglish ? LANG_PT : LANG_EN; ?>
                        <a href="<?= $lang->getSwitchUrl($switchLang) ?>"
                           class="flex items-center gap-2 px-3 py-1.5 rounded-full border border-cream/50 hover:bg-white/10 hover:border-accent transition-all duration-300 group"
                           title="<?= $isEnglish ? 'Mudar para Português' : 'Switch to English' ?>">
                            <div class="w-5 h-5 rounded-full overflow-hidden relative flex-shrink-0">
                                <?php if ($isEnglish): ?>
                                    <!-- Portuguese Flag (SVG) -->
                                    <svg viewBox="0 0 640 480" class="w-full h-full object-cover" aria-hidden="true">
                                        <path fill="#214524" d="M0 0h220v480H0z"/>
                                        <path fill="#cf1020" d="M220 0h420v480H220z"/>
                                        <path fill="#ffc400" d="M220 240m-60 0a60 60 0 1 0 120 0 60 60 0 1 0-120 0"/>
                                    </svg>
                                <?php else: ?>
                                    <!-- UK Flag (SVG) -->
                                    <svg viewBox="0 0 640 480" class="w-full h-full object-cover" aria-hidden="true">
                                        <path fill="#012169" d="M0 0h640v480H0z"/>
                                        <path fill="#FFF" d="M75 0l244 181L562 0h78v62L400 241l240 178v61h-80L320 301 81 480H0v-60l239-178L0 64V0h75z"/>
                                        <path fill="#C8102E" d="M424 281l216 159v40L369 281h55zm-184 20l6 35L54 480H0l240-179zM640 0v3L391 191l2-44L590 0h50zM0 0l239 176h-60L0 42V0z"/>
                                        <path fill="#FFF" d="M241 0v480h160V0H241zM0 160v160h640V160H0z"/>
                                        <path fill="#C8102E" d="M0 193v96h640v-96H0zM273 0v480h96V0h-96z"/>
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <span class="text-xs font-medium text-cream uppercase tracking-wide group-hover:text-accent transition-colors">
                                <?= $isEnglish ? 'PT' : 'EN' ?>
                            </span>
                        </a>
                    </div>

                    <!-- Cart Icon (if shop enabled) -->
                    <?php if (isShopEnabled()): ?>
                    <a href="<?= $lang->url($isEnglish ? 'shop/cart' : 'loja/carrinho') ?>"
                       class="relative p-2 text-cream hover:text-accent transition-colors ml-4"
                       title="<?= $isEnglish ? 'Shopping Cart' : 'Carrinho' ?>">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span id="cart-count" class="cart-count cart-badge absolute -top-1 -right-1 w-5 h-5 bg-accent text-primary text-xs font-bold rounded-full flex items-center justify-center hidden">0</span>
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Button -->
                <!-- Mobile Menu Button - Unified Toggle -->
                <button type="button" id="mobile-menu-btn" class="lg:hidden p-4 -mr-4 text-cream hover:text-accent transition-colors cursor-pointer relative z-[10000] group" aria-label="Menu">
                   <div class="relative w-8 h-6 flex flex-col justify-between items-center transition-all duration-300">
                        <!-- Top Line -->
                        <span class="w-8 h-[2px] bg-current rounded-full transform transition-all duration-300 origin-center group-[.open]:translate-y-[11px] group-[.open]:rotate-45"></span>
                        <!-- Middle Line -->
                        <span class="w-8 h-[2px] bg-current rounded-full transform transition-all duration-300 group-[.open]:opacity-0"></span>
                        <!-- Bottom Line -->
                        <span class="w-8 h-[2px] bg-current rounded-full transform transition-all duration-300 origin-center group-[.open]:-translate-y-[11px] group-[.open]:-rotate-45"></span>
                   </div>
                </button>
            </div>
        </nav>

            <!-- Background with Gradient and Blur -->

    </header>

    <!-- Mobile Menu - Moved Outside Header for Perfect Stacking -->
    <div id="mobile-menu" class="mobile-menu fixed inset-0 w-full h-full lg:hidden z-[9999] opacity-0 invisible transition-all duration-300 ease-in-out">

        <!-- Background with Gradient and Blur -->
        <!-- Background with Original Glass Gradient (Lighter Corners) -->
        <div class="absolute inset-0 bg-gradient-to-br from-primary-800/98 via-primary-700/95 to-primary-800/98 backdrop-blur-xl"></div>

        <!-- Solid Overlay for Scroll (Lighter Corners) -->
        <div id="mobile-menu-bg-solid" class="absolute inset-0 bg-gradient-to-br from-primary-800 via-primary-700 to-primary-800 transition-opacity duration-500 opacity-0"></div>

        <!-- Decorative Watermark (Optional) -->
        <div class="absolute inset-0 flex items-center justify-center overflow-hidden pointer-events-none opacity-[0.03]">
                 <span class="font-cursive text-[40vh] text-cream leading-none select-none">Gi</span>
        </div>

        <!-- Menu Content -->
        <div class="relative z-10 flex flex-col justify-center items-center h-full w-full px-6">
            <nav class="flex flex-col items-center space-y-6 md:space-y-8">
                <?php
                $delay = 0;
                foreach ($navItems as $item):

                    $homeUrlPT = $base . '/'; $homeUrlEN = $base . '/en/';
                    $isActive = ($item['url'] === $homeUrlPT || $item['url'] === $homeUrlEN)
                            ? ($currentPath === $item['url'])
                            : (strpos($currentPath, rtrim($item['url'], '/')) === 0);
                ?>
                <div class="mobile-item overflow-hidden px-4 w-full flex justify-center">
                    <a href="<?= $item['url'] ?>"
                       class="mobile-nav-link block w-full text-center text-4xl md:text-5xl font-serif font-light tracking-wide py-1 px-2 <?= $isActive ? 'text-accent italic' : 'text-cream' ?> hover:text-accent hover:italic transition-all duration-700 transform translate-y-8 opacity-0"
                       style="transition-delay: <?= $delay ?>ms">
                        <?= e($item['label']) ?>
                    </a>
                </div>
                <?php
                $delay += 75;
                endforeach;
                ?>

                <div class="h-px w-48 bg-gradient-to-r from-transparent via-accent/50 to-transparent my-8 transform scale-x-0 transition-transform duration-500 delay-300" id="mobile-separator"></div>

                <!-- Footer Actions (Lang, Cart) -->
                <div class="flex items-center gap-8 mobile-footer transform translate-y-4 opacity-0 transition-all duration-500 delay-500">
                     <!-- Language Switcher Mobile -->
                    <a href="<?= $lang->getSwitchUrl($switchLang) ?>"
                       class="flex items-center gap-3 text-cream/80 hover:text-accent transition-colors group">
                       <div class="w-8 h-8 rounded-full overflow-hidden border border-cream/20 group-hover:border-accent transition-colors shadow-sm">
                             <?php if ($isEnglish): ?>
                                <svg viewBox="0 0 640 480" class="w-full h-full object-cover">
                                    <path fill="#214524" d="M0 0h220v480H0z"/>
                                    <path fill="#cf1020" d="M220 0h420v480H220z"/>
                                    <path fill="#ffc400" d="M220 240m-60 0a60 60 0 1 0 120 0 60 60 0 1 0-120 0"/>
                                </svg>
                            <?php else: ?>
                                <svg viewBox="0 0 640 480" class="w-full h-full object-cover">
                                    <path fill="#012169" d="M0 0h640v480H0z"/>
                                    <path fill="#FFF" d="M75 0l244 181L562 0h78v62L400 241l240 178v61h-80L320 301 81 480H0v-60l239-178L0 64V0h75z"/>
                                    <path fill="#C8102E" d="M424 281l216 159v40L369 281h55zm-184 20l6 35L54 480H0l240-179zM640 0v3L391 191l2-44L590 0h50zM0 0l239 176h-60L0 42V0z"/>
                                    <path fill="#FFF" d="M241 0v480h160V0H241zM0 160v160h640V160H0z"/>
                                    <path fill="#C8102E" d="M0 193v96h640v-96H0zM273 0v480h96V0h-96z"/>
                                </svg>
                            <?php endif; ?>
                        </div>
                         <span class="text-sm font-medium uppercase tracking-[0.2em]"><?= $isEnglish ? 'PT' : 'EN' ?></span>
                    </a>

                    <!-- Cart Mobile -->
                    <?php if (isShopEnabled()): ?>
                    <div class="w-px h-4 bg-cream/20"></div>
                    <a href="<?= $lang->url($isEnglish ? 'shop/cart' : 'loja/carrinho') ?>"
                       class="relative flex items-center gap-3 text-cream/80 hover:text-accent transition-colors">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                         </svg>
                         <span id="mobile-cart-count" class="absolute -top-2 -right-2 w-5 h-5 bg-accent text-primary text-[10px] font-bold rounded-full flex items-center justify-center hidden shadow-sm">0</span>
                    </a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </div>

    <!-- Mobile Menu Transparency Fix -->
    <style>
        #main-header.menu-open {
            background-color: transparent !important;
            backdrop-filter: none !important;
            box-shadow: none !important;
            z-index: 10000 !important;
            transition: none !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
    </style>

    <!-- Header Scroll Script -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const header = document.getElementById('main-header');
        const isHomepage = document.body.classList.contains('homepage-new');
        const splitHero = document.getElementById('split-hero');
        const mobileHeader = document.getElementById('mobile-menu-header');

        // Scroll Effect with debouncing using requestAnimationFrame
        let ticking = false;
        const headerLayer = <?= isset($headerLayer) ? (int)$headerLayer : 1 ?>;
        let lastScrollY = 0;

        function handleScroll() {
            // Adjust mobile menu background opacity
            const mobileBgSolid = document.getElementById('mobile-menu-bg-solid');
            if (mobileBgSolid) {
                // If Layer 2, keep mobile menu solid (bg visible) when open (or always)
                // Otherwise normal scroll check
                if (window.scrollY > 50 || headerLayer === 2) {
                     mobileBgSolid.classList.remove('opacity-0');
                     mobileBgSolid.classList.add('opacity-100');
                } else {
                     mobileBgSolid.classList.add('opacity-0');
                     mobileBgSolid.classList.remove('opacity-100');
                }
            }

            // For homepage, keep transparent through both heroes
            if (isHomepage && splitHero) {
                const splitHeroBottom = splitHero.offsetTop + splitHero.offsetHeight;
                if (lastScrollY > splitHeroBottom - 100) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            } else {
                // Normal behavior for other pages - Transparency on Top
                if (lastScrollY > 50) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            }

            // Adjust mobile menu close button position to match header
            if (mobileHeader) {
                if (window.scrollY > 50) {
                     mobileHeader.classList.remove('h-[8.5rem]', 'md:h-32');
                     mobileHeader.classList.add('h-[6rem]');
                } else {
                     mobileHeader.classList.add('h-[8.5rem]', 'md:h-32');
                     mobileHeader.classList.remove('h-[6rem]');
                }
            }

            ticking = false;
        }

        window.addEventListener('scroll', () => {
            lastScrollY = window.scrollY;
            if (!ticking) {
                requestAnimationFrame(handleScroll);
                ticking = true;
            }
        }, { passive: true });

        // Initial check
        handleScroll();

        // Mobile Menu Logic with backdrop
        const mobileBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');
        const mobileSeparator = document.getElementById('mobile-separator');
        const mobileFooter = document.querySelector('.mobile-footer');

        function openMenu() {
            header.classList.add('menu-open'); // Priority: layout/z-index first
            mobileMenu.classList.add('open');
            mobileBtn.classList.add('open');
            document.body.style.overflow = 'hidden';
            document.documentElement.style.overflow = 'hidden'; // Ensure lock on HTML too

            // Trigger Animation Stagger
            // Increased timeout for smoother entrance
            setTimeout(() => {
                mobileNavLinks.forEach(link => {
                    link.classList.remove('translate-y-8', 'opacity-0');
                });
                if(mobileSeparator) mobileSeparator.classList.remove('scale-x-0');
                if(mobileFooter) {
                    mobileFooter.classList.remove('translate-y-4', 'opacity-0');
                }
            }, 100);
        }

        function closeMenu() {
            // Reset Animations first
            mobileNavLinks.forEach(link => {
                link.classList.add('translate-y-8', 'opacity-0');
            });
            if(mobileSeparator) mobileSeparator.classList.add('scale-x-0');
             if(mobileFooter) {
                mobileFooter.classList.add('translate-y-4', 'opacity-0');
            }

            mobileMenu.classList.remove('open');
            mobileBtn.classList.remove('open'); // Revert hamburger
            header.classList.remove('menu-open'); // Restore header styles
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
        }

        function toggleMenu() {
            if (mobileMenu.classList.contains('open')) {
                closeMenu();
            } else {
                openMenu();
            }
        }

        if (mobileBtn) mobileBtn.addEventListener('click', toggleMenu);
        // mobile-menu-close listener removed as element is gone

        // Close menu when clicking nav links
        mobileNavLinks.forEach(link => {
            link.addEventListener('click', () => {
                setTimeout(closeMenu, 100);
            });
        });

        // Close menu on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && mobileMenu.classList.contains('open')) {
                closeMenu();
            }
        });

        // Scroll Animations with Intersection Observer
        const animatedElements = document.querySelectorAll('.animate-on-scroll');

        if (animatedElements.length > 0) {
            const observerOptions = {
                root: null,
                rootMargin: '0px 0px -50px 0px',
                threshold: 0.1
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        // Optional: unobserve after animation
                        // observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            animatedElements.forEach(el => observer.observe(el));
        }
    });
    </script>

    <!-- Back to Top Button -->
    <button id="backToTop" class="fixed bottom-8 right-8 bg-secondary text-cream p-3 rounded-full shadow-lg opacity-0 invisible transition-all duration-300 z-40 hover:bg-secondary-600 hover:-translate-y-1 transform scale-90 hover:scale-100" aria-label="Voltar ao topo">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
        </svg>
    </button>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const backToTop = document.getElementById('backToTop');
        let backToTopTicking = false;

        function handleBackToTop() {
            if (window.scrollY > 300) {
                backToTop.classList.remove('opacity-0', 'invisible', 'scale-90');
                backToTop.classList.add('opacity-100', 'visible', 'scale-100');
            } else {
                backToTop.classList.add('opacity-0', 'invisible', 'scale-90');
                backToTop.classList.remove('opacity-100', 'visible', 'scale-100');
            }
            backToTopTicking = false;
        }

        window.addEventListener('scroll', () => {
            if (!backToTopTicking) {
                requestAnimationFrame(handleBackToTop);
                backToTopTicking = true;
            }
        }, { passive: true });

        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
    </script>

    <!-- Spacer for content to sit below header is NOT needed if header is transparent over hero.
         But if other pages don't have a hero, they might need padding.
         We can handle this by adding padding-top to body or main in specific pages if needed.
         For Index, we want it transparent over hero.
         For others, we might want a solid background.
         Let's handle this in the pages themselves or via a body class.
    -->
    <?php endif; ?>

    <!-- Flash Messages -->
    <?php if (\Core\Session::hasFlash()): ?>
    <div class="fixed top-24 right-4 z-50 space-y-2" id="flash-messages">
        <?php foreach (\Core\Session::getFlash() as $type => $messages): ?>
            <?php foreach ($messages as $message): ?>
            <div class="flash-message px-4 py-3 rounded-lg shadow-lg max-w-sm
                <?php
                switch ($type) {
                    case 'success': echo 'bg-secondary text-cream'; break;
                    case 'error': echo 'bg-red-500 text-white'; break;
                    case 'warning': echo 'bg-accent text-primary'; break;
                    default: echo 'bg-primary text-cream';
                }
                ?>">
                <div class="flex items-center justify-between">
                    <p class="text-sm"><?= e($message) ?></p>
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white/70 hover:text-white">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main>
