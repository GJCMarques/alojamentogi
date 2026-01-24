<?php
/**
 * A Casa do Gi - Header Template
 *
 * Variables available:
 * - $pageTitle: Page title (required)
 * - $pageDescription: Meta description (optional)
 * - $bodyClass: Additional body classes (optional)
 * - $hideNav: Hide navigation (optional)
 */

$lang = \Core\Language::getInstance();
$currentLang = $lang->getCurrentLang();
$isEnglish = $lang->isEnglish();
$base = basePath();

// Default values
$pageTitle = $pageTitle ?? 'A Casa do Gi';
$pageDescription = $pageDescription ?? ($isEnglish
    ? 'Local accommodation in Mogadouro, Portugal. Simplicity, warmth and love.'
    : 'Alojamento local em Mogadouro, Portugal. Simplicidade, acolhimento e muito amor.');
$bodyClass = $bodyClass ?? '';
$hideNav = $hideNav ?? false;

// Navigation items
$navItems = $isEnglish ? [
    ['url' => $base . '/en/', 'label' => 'Home'],
    ['url' => $base . '/en/accommodation/', 'label' => 'Accommodation'],
    ['url' => $base . '/en/shop/', 'label' => 'Shop'],
    ['url' => $base . '/en/activities/', 'label' => 'Activities'],
    ['url' => $base . '/en/about-us/', 'label' => 'About Us'],
    ['url' => $base . '/en/contact/', 'label' => 'Contact'],
] : [
    ['url' => $base . '/', 'label' => 'Inicio'],
    ['url' => $base . '/alojamento/', 'label' => 'Alojamento'],
    ['url' => $base . '/loja/', 'label' => 'Loja'],
    ['url' => $base . '/atividades/', 'label' => 'Atividades'],
    ['url' => $base . '/sobre-nos/', 'label' => 'Sobre Nos'],
    ['url' => $base . '/contactos/', 'label' => 'Contactos'],
];

// Get current path for active state
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($pageDescription) ?>">

    <title><?= e($pageTitle) ?> | A Casa do Gi</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= asset('images/CGsimbUpNB.ico') ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Great+Vibes&family=Merriweather:wght@300;400;700;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (via CDN for development) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Sophisticated Nature Palette
                        // Primary - Deep Slate Petrol (Navbar, Footer, Headings)
                        'primary': {
                            50: '#e8edef',
                            100: '#d1dbdf',
                            200: '#a3b7bf',
                            300: '#75939f',
                            400: '#476f7f',
                            500: '#264653',
                            600: '#1e3842',
                            700: '#172a32',
                            800: '#0f1c21',
                            900: '#080e11',
                            DEFAULT: '#264653',
                        },
                        // Secondary - Olive Sage (CTA Buttons, Icons)
                        'secondary': {
                            50: '#f2f4f0',
                            100: '#e5e9e1',
                            200: '#cbd3c3',
                            300: '#b1bda5',
                            400: '#97a787',
                            500: '#768A68',
                            600: '#5e6e53',
                            700: '#47533e',
                            800: '#2f372a',
                            900: '#181c15',
                            DEFAULT: '#768A68',
                        },
                        // Accent - Gold Ocre (Prices, Stars, Borders)
                        'accent': {
                            50: '#faf6ed',
                            100: '#f5eddb',
                            200: '#ebdbb7',
                            300: '#e1c993',
                            400: '#d7b76f',
                            500: '#C5A059',
                            600: '#9e8047',
                            700: '#766035',
                            800: '#4f4024',
                            900: '#272012',
                            DEFAULT: '#C5A059',
                        },
                        // Background - Warm Cream
                        'cream': {
                            50: '#FDFBF7',
                            100: '#faf5eb',
                            200: '#f5ebd7',
                            300: '#f0e1c3',
                            400: '#ebd7af',
                            DEFAULT: '#FDFBF7',
                        },
                        // Text - Charcoal (Replace pure black)
                        'charcoal': {
                            50: '#f7f8f8',
                            100: '#ebedef',
                            200: '#d4d8dc',
                            300: '#b8bfc5',
                            400: '#9aa3ab',
                            500: '#7b8792',
                            600: '#5f6a74',
                            700: '#4a5259',
                            800: '#2D3748',
                            900: '#1a2028',
                            DEFAULT: '#2D3748',
                        }
                    },
                    fontFamily: {
                        'serif': ['Merriweather', 'Georgia', 'serif'],
                        'sans': ['Poppins', 'system-ui', 'sans-serif'],
                        'display': ['Great Vibes', 'cursive'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 1s ease-in-out',
                        'slide-up': 'slideUp 0.8s ease-out',
                        'float': 'float 3s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(40px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        }
                    }
                }
            }
        }
    </script>

    <!-- Custom Styles -->
    <style>
        /* Base styles */
        body {
            font-family: 'Poppins', system-ui, sans-serif;
            background-color: #FDFBF7;
            color: #2D3748;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Merriweather', Georgia, serif;
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

        /* Navigation transitions */
        .nav-link {
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #C5A059; /* Accent - Gold Ocre */
            transition: width 0.3s ease;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }

        /* Mobile menu animation */
        .mobile-menu {
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }

        .mobile-menu.open {
            transform: translateX(0);
        }

        /* Cart badge */
        .cart-badge {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
    </style>

    <!-- CSRF Token for AJAX -->
    <?= \Core\CSRF::tokenMeta() ?>
</head>
<body class="bg-cream text-charcoal antialiased <?= e($bodyClass) ?>">
    <?php if (!$hideNav): ?>
    <!-- Header Navigation -->
    <header id="main-header" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-transparent text-cream">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-24 transition-all duration-300">
                <!-- Logo / Brand -->
                <a href="<?= $isEnglish ? $base . '/en/' : $base . '/' ?>" class="flex items-center group">
                    <!-- Text Logo -->
                    <span class="font-cursive text-5xl text-cream group-hover:text-accent transition-colors duration-300 drop-shadow-md pb-2 relative top-1">
                        A Casa do Gi
                    </span>
                </a>

                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center space-x-8">
                    <?php foreach ($navItems as $item):
                        // Fix active detection logic
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
                       class="nav-link text-sm font-sans font-medium text-cream hover:text-accent transition-colors tracking-widest uppercase <?= $isActive ? 'active text-accent' : '' ?>">
                        <?= e($item['label']) ?>
                    </a>
                    <?php endforeach; ?>

                    <!-- Separator -->
                    <div class="h-6 w-px bg-cream/30"></div>

                    <!-- Language Switcher -->
                    <div class="flex items-center ml-2">
                        <?php $switchLang = $isEnglish ? LANG_PT : LANG_EN; ?>
                        <a href="<?= $lang->getSwitchUrl($switchLang) ?>"
                           class="flex items-center justify-center w-8 h-8 rounded-full border border-cream/50 hover:bg-white/10 hover:border-accent transition-all duration-300 group"
                           title="<?= $isEnglish ? 'Mudar para Portugues' : 'Switch to English' ?>">
                            <div class="w-6 h-6 rounded-full overflow-hidden relative">
                                <?php if ($isEnglish): ?>
                                    <!-- Portuguese Flag (SVG) -->
                                    <svg viewBox="0 0 640 480" class="w-full h-full object-cover">
                                        <path fill="#214524" d="M0 0h220v480H0z"/>
                                        <path fill="#cf1020" d="M220 0h420v480H220z"/>
                                        <path fill="#ffc400" d="M220 240m-60 0a60 60 0 1 0 120 0 60 60 0 1 0-120 0"/>
                                    </svg>
                                <?php else: ?>
                                    <!-- UK Flag (SVG) -->
                                    <svg viewBox="0 0 640 480" class="w-full h-full object-cover">
                                        <path fill="#012169" d="M0 0h640v480H0z"/>
                                        <path fill="#FFF" d="M75 0l244 181L562 0h78v62L400 241l240 178v61h-80L320 301 81 480H0v-60l239-178L0 64V0h75z"/>
                                        <path fill="#C8102E" d="M424 281l216 159v40L369 281h55zm-184 20l6 35L54 480H0l240-179zM640 0v3L391 191l2-44L590 0h50zM0 0l239 176h-60L0 42V0z"/>
                                        <path fill="#FFF" d="M241 0v480h160V0H241zM0 160v160h640V160H0z"/>
                                        <path fill="#C8102E" d="M0 193v96h640v-96H0zM273 0v480h96V0h-96z"/>
                                    </svg>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>

                    <!-- Cart Icon (if shop enabled) -->
                    <?php if (isShopEnabled()): ?>
                    <a href="<?= $lang->url('loja/carrinho') ?>"
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
                <button type="button" id="mobile-menu-btn" class="lg:hidden p-2 text-cream hover:text-accent transition-colors" aria-label="Menu">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </nav>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="mobile-menu fixed inset-y-0 right-0 w-full max-w-sm bg-primary shadow-2xl lg:hidden border-l border-accent/20 z-50 transform translate-x-full transition-transform duration-300">
            <div class="flex flex-col h-full">
                <div class="flex items-center justify-between p-6 border-b border-accent/20 bg-primary-700">
                    <span class="font-cursive text-3xl text-cream">Menu</span>
                    <button type="button" id="mobile-menu-close" class="p-2 text-cream hover:text-accent transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <nav class="flex-1 px-6 py-8 overflow-y-auto bg-primary">
                    <?php foreach ($navItems as $item):
                        // Active logic
                        $homeUrlPT = $base . '/'; $homeUrlEN = $base . '/en/';
                        $isActive = ($item['url'] === $homeUrlPT || $item['url'] === $homeUrlEN)
                             ? ($currentPath === $item['url'])
                             : (strpos($currentPath, rtrim($item['url'], '/')) === 0);
                    ?>
                    <a href="<?= $item['url'] ?>"
                       class="block py-4 text-xl border-b border-white/5 <?= $isActive ? 'text-accent font-semibold' : 'text-cream/90' ?> hover:text-accent hover:pl-2 transition-all">
                        <?= e($item['label']) ?>
                    </a>
                    <?php endforeach; ?>

                    <div class="mt-8 pt-8 border-t border-accent/20">
                        <a href="<?= $lang->getSwitchUrl($switchLang) ?>"
                           class="flex items-center space-x-3 py-3 text-cream/90 hover:text-accent">
                            <div class="w-8 h-8 rounded-full overflow-hidden border border-cream/20">
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
                            <span class="text-lg"><?= $isEnglish ? 'Portugues' : 'English' ?></span>
                        </a>
                    </div>
                </nav>
            </div>
        </div>
    </header>

    <!-- Header Scroll Script -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const header = document.getElementById('main-header');
        const headerInner = header.querySelector('div'); // The inner flex container
        const logoText = header.querySelector('.font-cursive');

        // Scroll Effect
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                // Scrolled state
                header.classList.remove('bg-transparent', 'text-cream');
                header.classList.add('bg-primary/95', 'text-cream', 'shadow-md', 'backdrop-blur-sm');

                // Shrink height
                headerInner.classList.remove('h-24');
                headerInner.classList.add('h-20');

                // Adjust logo size
                logoText.classList.remove('text-5xl');
                logoText.classList.add('text-4xl');
            } else {
                // Top state
                header.classList.add('bg-transparent', 'text-cream');
                header.classList.remove('bg-primary/95', 'shadow-md', 'backdrop-blur-sm');

                // Restore height
                headerInner.classList.remove('h-20');
                headerInner.classList.add('h-24');

                // Restore logo size
                logoText.classList.remove('text-4xl');
                logoText.classList.add('text-5xl');
            }
        });

        // Mobile Menu Logic
        const mobileBtn = document.getElementById('mobile-menu-btn');
        const mobileClose = document.getElementById('mobile-menu-close');
        const mobileMenu = document.getElementById('mobile-menu');

        function toggleMenu() {
            const isOpen = !mobileMenu.classList.contains('translate-x-full');
            if (isOpen) {
                mobileMenu.classList.add('translate-x-full');
            } else {
                mobileMenu.classList.remove('translate-x-full');
            }
        }

        if (mobileBtn) mobileBtn.addEventListener('click', toggleMenu);
        if (mobileClose) mobileClose.addEventListener('click', toggleMenu);
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

        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                backToTop.classList.remove('opacity-0', 'invisible', 'scale-90');
                backToTop.classList.add('opacity-100', 'visible', 'scale-100');
            } else {
                backToTop.classList.add('opacity-0', 'invisible', 'scale-90');
                backToTop.classList.remove('opacity-100', 'visible', 'scale-100');
            }
        });

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
