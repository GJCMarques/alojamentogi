<?php
/**
 * A Casa do Gi - 404 Page Not Found
 */

// Set 404 HTTP response code
http_response_code(404);

require_once __DIR__ . '/includes/init.php';

$lang = \Core\Language::getInstance();
$isEnglish = $lang->isEnglish();
$base = basePath();

// Page meta
$pageTitle = $isEnglish ? 'Page Not Found' : 'Página Não Encontrada';
$pageDescription = $isEnglish
    ? 'The page you are looking for does not exist or has been moved.'
    : 'A página que procura não existe ou foi movida.';

include INCLUDES_PATH . '/header.php';
?>

<!-- 404 Hero Section -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-primary-700 via-primary to-primary-600">
    <!-- Decorative Background -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-20 left-10 w-72 h-72 bg-accent rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-secondary rounded-full blur-3xl animate-float" style="animation-delay: 1s;"></div>
    </div>

    <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
        <!-- 404 Number -->
        <div class="animate-on-scroll" data-animation="zoom-in">
            <h1 class="font-serif text-[180px] sm:text-[240px] md:text-[320px] font-black text-cream/20 leading-none select-none">
                404
            </h1>
        </div>

        <!-- Main Message -->
        <div class="mt-8 space-y-6 animate-on-scroll" data-delay="200">
            <h2 class="font-serif text-4xl sm:text-5xl md:text-6xl font-bold text-cream">
                <?= $isEnglish ? 'Page Not Found' : 'Página Não Encontrada' ?>
            </h2>
            <p class="text-cream-200 text-lg sm:text-xl max-w-2xl mx-auto leading-relaxed">
                <?= $isEnglish
                    ? 'The page you are looking for does not exist or has been moved. Please check the URL or navigate to one of the links below.'
                    : 'A página que procura não existe ou foi movida. Por favor, verifique o URL ou navegue para um dos links abaixo.' ?>
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="mt-12 flex flex-col sm:flex-row items-center justify-center gap-4 animate-on-scroll" data-delay="300">
            <a href="<?= $isEnglish ? $base . '/en/' : $base . '/' ?>"
               class="inline-flex items-center px-8 py-4 bg-accent text-primary font-semibold rounded-xl shadow-lg hover:bg-accent-400 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <?= $isEnglish ? 'Go Home' : 'Ir para Início' ?>
            </a>
            <button onclick="window.history.back()"
                    class="inline-flex items-center px-8 py-4 bg-cream/10 text-cream font-semibold rounded-xl border-2 border-cream/30 hover:bg-cream/20 hover:border-cream/50 hover:-translate-y-1 transition-all duration-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <?= $isEnglish ? 'Go Back' : 'Voltar' ?>
            </button>
        </div>

        <!-- Quick Links -->
        <div class="mt-20 animate-on-scroll" data-delay="400">
            <h3 class="font-serif text-2xl font-bold text-cream mb-8">
                <?= $isEnglish ? 'Quick Links' : 'Links Úteis' ?>
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-3xl mx-auto">
                <!-- Alojamento -->
                <a href="<?= $lang->url('alojamento') ?>"
                   class="group p-6 bg-cream/5 backdrop-blur-sm rounded-xl border border-cream/10 hover:bg-cream/10 hover:border-accent/50 transition-all duration-300">
                    <div class="w-12 h-12 mx-auto mb-4 bg-accent/20 rounded-full flex items-center justify-center group-hover:bg-accent/30 transition-colors">
                        <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <p class="text-cream font-semibold">
                        <?= $isEnglish ? 'Accommodation' : 'Alojamento' ?>
                    </p>
                </a>

                <!-- Loja -->
                <a href="<?= $lang->url('loja') ?>"
                   class="group p-6 bg-cream/5 backdrop-blur-sm rounded-xl border border-cream/10 hover:bg-cream/10 hover:border-accent/50 transition-all duration-300">
                    <div class="w-12 h-12 mx-auto mb-4 bg-accent/20 rounded-full flex items-center justify-center group-hover:bg-accent/30 transition-colors">
                        <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <p class="text-cream font-semibold">
                        <?= $isEnglish ? 'Shop' : 'Loja' ?>
                    </p>
                </a>

                <!-- Atividades -->
                <a href="<?= $lang->url('atividades') ?>"
                   class="group p-6 bg-cream/5 backdrop-blur-sm rounded-xl border border-cream/10 hover:bg-cream/10 hover:border-accent/50 transition-all duration-300">
                    <div class="w-12 h-12 mx-auto mb-4 bg-accent/20 rounded-full flex items-center justify-center group-hover:bg-accent/30 transition-colors">
                        <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <p class="text-cream font-semibold">
                        <?= $isEnglish ? 'Activities' : 'Atividades' ?>
                    </p>
                </a>

                <!-- Contactos -->
                <a href="<?= $lang->url('contactos') ?>"
                   class="group p-6 bg-cream/5 backdrop-blur-sm rounded-xl border border-cream/10 hover:bg-cream/10 hover:border-accent/50 transition-all duration-300">
                    <div class="w-12 h-12 mx-auto mb-4 bg-accent/20 rounded-full flex items-center justify-center group-hover:bg-accent/30 transition-colors">
                        <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-cream font-semibold">
                        <?= $isEnglish ? 'Contact' : 'Contactos' ?>
                    </p>
                </a>
            </div>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
