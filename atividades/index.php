<?php
/**
 * A Casa do Gi - Activities / Tourist Guide Page (Portuguese)
 */

require_once dirname(__DIR__) . '/includes/init.php';

use Core\Language;

$lang = Language::getInstance();
$base = basePath();

// Get page content
$content = $lang->getPageContents('activities');

// Page configuration
$pageTitle = 'O Que Fazer em Mogadouro';
$pageDescription = 'Descubra as melhores atividades e atrações turísticas em Mogadouro e Trás-os-Montes. Natureza, gastronomia, história e cultura.';

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="relative py-20 lg:py-32 bg-primary">
    <div class="absolute inset-0 opacity-20">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmZmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIj48cGF0aCBkPSJNMzYgMzRjMC0yLjIwOS0xLjc5MS00LTQtNHMtNCAxLjc5MS00IDQgMS43OTEgNCA0IDQgNC0xLjc5MSA0LTR6Ii8+PC9nPjwvZz48L3N2Zz4=')]"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-block text-accent text-sm font-medium uppercase tracking-wider mb-4">
            Descubra Mogadouro
        </span>
        <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl text-cream mb-6">
            O Que Fazer
        </h1>
        <p class="text-xl text-charcoal max-w-3xl mx-auto">
            De paisagens deslumbrantes a sabores únicos, Mogadouro e o nordeste transmontano têm muito para oferecer.
        </p>
    </div>
</section>

<!-- Category Filter -->
<section class="py-8 bg-cream border-b border-cream-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-center gap-4">
            <button class="activity-filter active px-6 py-2 rounded-full text-sm font-medium transition-colors bg-secondary text-cream" data-filter="all">
                Todas
            </button>
            <button class="activity-filter px-6 py-2 rounded-full text-sm font-medium transition-colors bg-cream-100 text-charcoal hover:bg-accent/10" data-filter="natureza">
                Natureza
            </button>
            <button class="activity-filter px-6 py-2 rounded-full text-sm font-medium transition-colors bg-cream-100 text-charcoal hover:bg-accent/10" data-filter="cultura">
                Cultura & História
            </button>
            <button class="activity-filter px-6 py-2 rounded-full text-sm font-medium transition-colors bg-cream-100 text-charcoal hover:bg-accent/10" data-filter="gastronomia">
                Gastronomia
            </button>
            <button class="activity-filter px-6 py-2 rounded-full text-sm font-medium transition-colors bg-cream-100 text-charcoal hover:bg-accent/10" data-filter="aventura">
                Aventura
            </button>
        </div>
    </div>
</section>

<!-- Activities Grid -->
<section class="py-16 lg:py-24 bg-cream-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8" id="activities-grid">

            <!-- Activity 1: Parque Natural do Douro Internacional -->
            <article class="activity-card bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow" data-category="natureza">
                <div class="aspect-[4/3] relative overflow-hidden">
                    <div class="w-full h-full bg-gradient-to-br from-accent/20 to-accent/40 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="absolute top-4 left-4 bg-secondary text-cream text-xs font-medium px-3 py-1 rounded-full">
                        Natureza
                    </span>
                </div>
                <div class="p-6">
                    <h3 class="font-serif text-xl text-primary mb-2">Parque Natural do Douro Internacional</h3>
                    <p class="text-charcoal text-sm mb-4">
                        Paisagens de cortar a respiração ao longo das arribas do rio Douro. Observe aves de rapina como o grifo e a águia-real nos seus habitats naturais.
                    </p>
                    <div class="flex items-center text-sm text-charcoal/70">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        20 km de Mogadouro
                    </div>
                </div>
            </article>

            <!-- Activity 2: Castelo de Mogadouro -->
            <article class="activity-card bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow" data-category="cultura">
                <div class="aspect-[4/3] relative overflow-hidden">
                    <div class="w-full h-full bg-gradient-to-br from-charcoal/30 to-charcoal/50 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <span class="absolute top-4 left-4 bg-accent text-cream text-xs font-medium px-3 py-1 rounded-full">
                        Cultura & História
                    </span>
                </div>
                <div class="p-6">
                    <h3 class="font-serif text-xl text-primary mb-2">Castelo de Mogadouro</h3>
                    <p class="text-charcoal text-sm mb-4">
                        Torre de menagem do antigo castelo medieval, símbolo da vila. Desfrute de vistas panorâmicas e descubra a história templária da região.
                    </p>
                    <div class="flex items-center text-sm text-charcoal/70">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Centro de Mogadouro
                    </div>
                </div>
            </article>

            <!-- Activity 3: Gastronomia Transmontana -->
            <article class="activity-card bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow" data-category="gastronomia">
                <div class="aspect-[4/3] relative overflow-hidden">
                    <div class="w-full h-full bg-gradient-to-br from-accent/20 to-accent/40 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="absolute top-4 left-4 bg-accent text-cream text-xs font-medium px-3 py-1 rounded-full">
                        Gastronomia
                    </span>
                </div>
                <div class="p-6">
                    <h3 class="font-serif text-xl text-primary mb-2">Gastronomia Transmontana</h3>
                    <p class="text-charcoal text-sm mb-4">
                        Prove a famosa posta mirandesa, os enchidos artesanais, o azeite DOP de Trás-os-Montes e os vinhos do Douro em restaurantes locais.
                    </p>
                    <div class="flex items-center text-sm text-charcoal/70">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Experiência imperdível
                    </div>
                </div>
            </article>

            <!-- Activity 4: Trilhos Pedestres -->
            <article class="activity-card bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow" data-category="aventura">
                <div class="aspect-[4/3] relative overflow-hidden">
                    <div class="w-full h-full bg-gradient-to-br from-olive-300 to-olive-500 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <span class="absolute top-4 left-4 bg-secondary text-white text-xs font-medium px-3 py-1 rounded-full">
                        Aventura
                    </span>
                </div>
                <div class="p-6">
                    <h3 class="font-serif text-xl text-primary mb-2">Trilhos Pedestres</h3>
                    <p class="text-charcoal text-sm mb-4">
                        Explore os percursos pedestres da região, desde caminhadas suaves até trilhos mais desafiantes pelas serras e vales transmontanos.
                    </p>
                    <div class="flex items-center text-sm text-charcoal/70">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        Vários percursos
                    </div>
                </div>
            </article>

            <!-- Activity 5: Convento de São Francisco -->
            <article class="activity-card bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow" data-category="cultura">
                <div class="aspect-[4/3] relative overflow-hidden">
                    <div class="w-full h-full bg-gradient-to-br from-wood-200 to-wood-400 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                        </svg>
                    </div>
                    <span class="absolute top-4 left-4 bg-terracotta-500 text-white text-xs font-medium px-3 py-1 rounded-full">
                        Cultura & História
                    </span>
                </div>
                <div class="p-6">
                    <h3 class="font-serif text-xl text-primary mb-2">Convento de São Francisco</h3>
                    <p class="text-charcoal text-sm mb-4">
                        Antigo convento franciscano do século XIII, hoje transformado em espaço cultural. Admire a arquitetura gótica e os azulejos históricos.
                    </p>
                    <div class="flex items-center text-sm text-charcoal/70">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Centro de Mogadouro
                    </div>
                </div>
            </article>

            <!-- Activity 6: Vinhas do Douro -->
            <article class="activity-card bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow" data-category="gastronomia">
                <div class="aspect-[4/3] relative overflow-hidden">
                    <div class="w-full h-full bg-gradient-to-br from-olive-200 to-wood-400 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <span class="absolute top-4 left-4 bg-wood-500 text-white text-xs font-medium px-3 py-1 rounded-full">
                        Gastronomia
                    </span>
                </div>
                <div class="p-6">
                    <h3 class="font-serif text-xl text-primary mb-2">Enoturismo no Douro</h3>
                    <p class="text-charcoal text-sm mb-4">
                        Visite quintas vinícolas na região do Douro Superior. Prove os vinhos locais e descubra os segredos da vindima transmontana.
                    </p>
                    <div class="flex items-center text-sm text-charcoal/70">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        30 km de Mogadouro
                    </div>
                </div>
            </article>

            <!-- Activity 7: Observação de Aves -->
            <article class="activity-card bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow" data-category="natureza">
                <div class="aspect-[4/3] relative overflow-hidden">
                    <div class="w-full h-full bg-gradient-to-br from-cream-300 to-olive-300 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <span class="absolute top-4 left-4 bg-secondary text-white text-xs font-medium px-3 py-1 rounded-full">
                        Natureza
                    </span>
                </div>
                <div class="p-6">
                    <h3 class="font-serif text-xl text-primary mb-2">Observação de Aves</h3>
                    <p class="text-charcoal text-sm mb-4">
                        A região é um paraíso para birdwatchers. Observe grifos, águias-reais, cegonhas-negras e muitas outras espécies nos seus habitats naturais.
                    </p>
                    <div class="flex items-center text-sm text-charcoal/70">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Primavera e Verão
                    </div>
                </div>
            </article>

            <!-- Activity 8: Passeios de Barco -->
            <article class="activity-card bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow" data-category="aventura">
                <div class="aspect-[4/3] relative overflow-hidden">
                    <div class="w-full h-full bg-gradient-to-br from-granite-300 to-olive-400 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                        </svg>
                    </div>
                    <span class="absolute top-4 left-4 bg-secondary text-white text-xs font-medium px-3 py-1 rounded-full">
                        Aventura
                    </span>
                </div>
                <div class="p-6">
                    <h3 class="font-serif text-xl text-primary mb-2">Passeios de Barco no Douro</h3>
                    <p class="text-charcoal text-sm mb-4">
                        Navegue pelo rio Douro e descubra as impressionantes arribas e a fauna única desta região classificada pela UNESCO.
                    </p>
                    <div class="flex items-center text-sm text-charcoal/70">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Abril a Outubro
                    </div>
                </div>
            </article>

            <!-- Activity 9: Aldeias Históricas -->
            <article class="activity-card bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow" data-category="cultura">
                <div class="aspect-[4/3] relative overflow-hidden">
                    <div class="w-full h-full bg-gradient-to-br from-terracotta-200 to-granite-400 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <span class="absolute top-4 left-4 bg-terracotta-500 text-white text-xs font-medium px-3 py-1 rounded-full">
                        Cultura & História
                    </span>
                </div>
                <div class="p-6">
                    <h3 class="font-serif text-xl text-primary mb-2">Aldeias Históricas</h3>
                    <p class="text-charcoal text-sm mb-4">
                        Visite as aldeias tradicionais do concelho: Bemposta, Bruçó, Castro Vicente. Descubra a arquitetura rural transmontana e o artesanato local.
                    </p>
                    <div class="flex items-center text-sm text-charcoal/70">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Concelho de Mogadouro
                    </div>
                </div>
            </article>

        </div>
    </div>
</section>

<!-- Seasonal Activities -->
<section class="py-16 lg:py-24 bg-cream-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block text-secondary text-sm font-medium uppercase tracking-wider mb-4">
                Ao Longo do Ano
            </span>
            <h2 class="font-serif text-3xl md:text-4xl text-primary mb-4">Eventos e Festividades</h2>
            <p class="text-charcoal max-w-2xl mx-auto">
                Mogadouro vive de tradições. Ao longo do ano, várias festas e eventos animam a região.
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Spring -->
            <div class="bg-cream-100 rounded-lg p-6 border border-cream-300">
                <div class="w-12 h-12 bg-accent/10 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
                <h3 class="font-serif text-lg text-primary mb-2">Primavera</h3>
                <ul class="text-charcoal text-sm space-y-2">
                    <li>• Festa das Amendoeiras em Flor</li>
                    <li>• Páscoa Tradicional</li>
                    <li>• Romarias locais</li>
                </ul>
            </div>

            <!-- Summer -->
            <div class="bg-cream-100 rounded-lg p-6 border border-cream-300">
                <div class="w-12 h-12 bg-accent/10 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-terracotta-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <h3 class="font-serif text-lg text-primary mb-2">Verão</h3>
                <ul class="text-charcoal text-sm space-y-2">
                    <li>• Festas de Nossa Senhora do Caminho</li>
                    <li>• Festival Medieval</li>
                    <li>• Noites de Verão</li>
                </ul>
            </div>

            <!-- Autumn -->
            <div class="bg-cream-100 rounded-lg p-6 border border-cream-300">
                <div class="w-12 h-12 bg-accent/10 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h3 class="font-serif text-lg text-primary mb-2">Outono</h3>
                <ul class="text-charcoal text-sm space-y-2">
                    <li>• Vindimas no Douro</li>
                    <li>• Feira da Castanha</li>
                    <li>• Matança do Porco</li>
                </ul>
            </div>

            <!-- Winter -->
            <div class="bg-cream-100 rounded-lg p-6 border border-cream-300">
                <div class="w-12 h-12 bg-charcoal/10 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-charcoal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                    </svg>
                </div>
                <h3 class="font-serif text-lg text-primary mb-2">Inverno</h3>
                <ul class="text-charcoal text-sm space-y-2">
                    <li>• Natal Tradicional</li>
                    <li>• Dia de Reis</li>
                    <li>• Carnaval Transmontano</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Practical Info -->
<section class="py-16 lg:py-24 bg-cream-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-start">
            <!-- Getting Around -->
            <div>
                <h2 class="font-serif text-2xl md:text-3xl text-primary mb-6">Como Chegar e Circular</h2>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-accent/10 rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                            <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-primary mb-1">De Carro</h3>
                            <p class="text-charcoal text-sm">A forma mais prática de explorar a região. Mogadouro está a cerca de 2h30 do Porto via A4/IP4.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-accent/10 rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                            <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-primary mb-1">Transportes Públicos</h3>
                            <p class="text-charcoal text-sm">Existem autocarros que ligam Mogadouro a Bragança e outras localidades, mas com horários limitados.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-accent/10 rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                            <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-primary mb-1">Dica Local</h3>
                            <p class="text-charcoal text-sm">Para explorar as arribas do Douro e as aldeias mais remotas, recomendamos veículo próprio ou aluguer.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Useful Links -->
            <div>
                <h2 class="font-serif text-2xl md:text-3xl text-primary mb-6">Ligações Úteis</h2>
                <div class="bg-white rounded-lg p-6 shadow-md space-y-4">
                    <a href="https://www.cm-mogadouro.pt/" target="_blank" rel="noopener" class="flex items-center justify-between p-3 bg-cream-50 rounded hover:bg-cream-100 transition-colors">
                        <span class="text-primary font-medium">Câmara Municipal de Mogadouro</span>
                        <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                    <a href="https://natural.pt/protected-areas/parque-natural-do-douro-internacional" target="_blank" rel="noopener" class="flex items-center justify-between p-3 bg-cream-50 rounded hover:bg-cream-100 transition-colors">
                        <span class="text-primary font-medium">Parque Natural do Douro Internacional</span>
                        <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                    <a href="https://www.visitportugal.com/pt-pt/destinos/porto-e-norte/tras-os-montes" target="_blank" rel="noopener" class="flex items-center justify-between p-3 bg-cream-50 rounded hover:bg-cream-100 transition-colors">
                        <span class="text-primary font-medium">Visit Portugal - Trás-os-Montes</span>
                        <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 lg:py-24 bg-secondary">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-serif text-3xl md:text-4xl text-cream mb-6">
            Pronto para Explorar?
        </h2>
        <p class="text-xl text-accent/80 mb-10">
            Reserve a sua estadia na Casa do Gi e descubra o melhor de Mogadouro.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="<?= $base ?>/alojamento/" class="inline-flex items-center px-8 py-4 bg-cream text-secondary font-medium rounded hover:bg-cream-100 transition-colors">
                Ver Alojamento
            </a>
            <a href="<?= $base ?>/contactos/" class="inline-flex items-center px-8 py-4 bg-secondary text-cream font-medium rounded hover:bg-secondary-700 transition-colors border border-secondary">
                Contactar-nos
            </a>
        </div>
    </div>
</section>

<!-- Filter Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.activity-filter');
    const activityCards = document.querySelectorAll('.activity-card');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;

            // Update active button
            filterButtons.forEach(btn => {
                btn.classList.remove('active', 'bg-secondary', 'text-cream');
                btn.classList.add('bg-cream-100', 'text-charcoal');
            });
            this.classList.add('active', 'bg-secondary', 'text-cream');
            this.classList.remove('bg-cream-100', 'text-charcoal');

            // Filter cards
            activityCards.forEach(card => {
                if (filter === 'all' || card.dataset.category === filter) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeIn 0.3s ease-in-out';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<?php include INCLUDES_PATH . '/footer.php'; ?>
