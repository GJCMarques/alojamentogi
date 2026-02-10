<?php
/**
 * A Casa do Gi - 404 Page Not Found
 */

// Define flag for Language detection to know we shouldn't force default language
define('IS_404', true);
require_once __DIR__ . '/includes/init.php';

// Set 404 HTTP response code
http_response_code(404);

$lang = \Core\Language::getInstance();

// FORCE ENGLISH if the URL contains /en/ segment
// This fixes 404 pages not detecting language correctly because of base path issues
$reqUri = $_SERVER['REQUEST_URI'] ?? '';
if (strpos($reqUri, '/en/') !== false || substr($reqUri, -3) === '/en') {
    $lang->setLanguage('en');
}

$isEnglish = $lang->isEnglish();
$base = basePath();

// Define translations hardcoded in file (No Database)
$t = $isEnglish ? [
    'title' => 'Page Not Found',
    'description' => 'The page you are looking for does not exist or has been moved.',
    '404_text' => '404',
    'hero_title' => 'Page Not Found',
    'hero_message' => 'The page you are looking for does not exist or has been moved. Please check the URL or navigate to one of the links below.',
    'btn_back' => 'Go Back',
    'btn_home' => 'Go Home',
    'home_link' => $base . '/en/'
] : [
    'title' => 'Página Não Encontrada',
    'description' => 'A página que procura não existe ou foi movida.',
    '404_text' => '404',
    'hero_title' => 'Página Não Encontrada',
    'hero_message' => 'A página que procura não existe ou foi movida. Por favor, verifique o URL ou navegue para um dos links abaixo.',
    'btn_back' => 'Voltar',
    'btn_home' => 'Início',
    'home_link' => $base . '/'
];

// Page meta
$pageTitle = $t['title'];
$pageDescription = $t['description'];

include INCLUDES_PATH . '/header.php';
?>

<!-- 404 Hero Section -->
<section class="relative min-h-screen flex flex-col items-center justify-center overflow-hidden">
    <!-- Background Image with Overlay -->
    <div class="absolute inset-0 z-0">
        <img src="<?= $base ?>/assets/images/Castelo.jpg" alt="Background" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-[2px]"></div>
    </div>

    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center text-white -mt-20">
        <!-- 404 Number -->
        <div class="animate-on-scroll" data-animation="zoom-in">
            <h1 class="font-serif text-[120px] sm:text-[180px] md:text-[220px] font-black text-white/20 leading-none select-none tracking-tighter mix-blend-overlay">
                <?= $t['404_text'] ?>
            </h1>
        </div>

        <!-- Main Message -->
        <div class="relative z-20 space-y-6 animate-on-scroll" data-delay="200">
            <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-white tracking-wide drop-shadow-lg">
                <?= $t['hero_title'] ?>
            </h2>
            <p class="text-gray-100 text-lg sm:text-xl max-w-2xl mx-auto leading-relaxed font-light drop-shadow-md">
                <?= $t['hero_message'] ?>
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="mt-12 flex flex-col sm:flex-row items-center justify-center gap-6 animate-on-scroll" data-delay="300">
            <button onclick="window.history.back()"
                    class="group relative inline-flex items-center px-8 py-4 bg-white/10 text-white font-semibold rounded-full border border-white/20 hover:bg-white/20 hover:border-white/40 hover:-translate-y-1 transition-all duration-300 backdrop-blur-md overflow-hidden">
                <div class="absolute inset-0 w-0 bg-white/10 transition-all duration-[250ms] ease-out group-hover:w-full"></div>
                <svg class="w-5 h-5 mr-3 relative z-10 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span class="relative z-10"><?= $t['btn_back'] ?></span>
            </button>

            <a href="<?= $t['home_link'] ?>"
               class="group relative inline-flex items-center px-8 py-4 bg-accent text-white font-semibold rounded-full shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                <div class="absolute inset-0 w-full h-full bg-white/10 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                <svg class="w-5 h-5 mr-3 relative z-10 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="relative z-10"><?= $t['btn_home'] ?></span>
            </a>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
