<?php
/**
 * A Casa do Gi - Activities / Tourist Guide Page
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
<section class="relative h-[75vh] min-h-[600px] flex items-center bg-primary overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat bg-fixed"
             style="background-image: url('<?= asset('images/MogadouroAtividades.jpg') ?>');">
        </div>
        <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/30 to-black/60"></div>
    </div>

    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <span class="inline-block text-accent text-lg font-medium tracking-[0.2em] uppercase mb-4 animate-on-scroll" data-animation="fade-up">
            Descubra Mogadouro
        </span>
        
        <h1 class="font-cursive text-6xl md:text-7xl lg:text-8xl text-cream mb-6 drop-shadow-xl animate-on-scroll" data-animation="fade-up" data-delay="100">
            O Que Fazer
        </h1>

        <p class="text-lg md:text-xl text-cream/90 max-w-2xl mx-auto font-light leading-relaxed animate-on-scroll" data-animation="fade-up" data-delay="200">
            De paisagens deslumbrantes a sabores únicos, o nordeste transmontano tem muito para oferecer.
        </p>
    </div>
</section>

<!-- Category Filter -->
<div class="sticky top-[80px] z-40 py-6 bg-white/90 backdrop-blur-md border-b border-cream-100 shadow-sm transition-all duration-300" id="filter-bar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-center gap-3 md:gap-4" id="filters-container">
            <button class="activity-filter active group relative px-6 py-2.5 rounded-full text-sm font-bold tracking-wide uppercase transition-all duration-300 bg-secondary text-white shadow-lg shadow-secondary/20 hover:scale-105" data-filter="all">
                <span>Todas</span>
            </button>
            
            <button class="activity-filter group relative px-6 py-2.5 rounded-full text-sm font-bold tracking-wide uppercase transition-all duration-300 bg-cream-50 text-charcoal/60 hover:bg-white hover:text-accent border border-cream-200 hover:border-accent hover:shadow-md hover:scale-105" data-filter="natureza">
                <span>Natureza</span>
            </button>
            
            <button class="activity-filter group relative px-6 py-2.5 rounded-full text-sm font-bold tracking-wide uppercase transition-all duration-300 bg-cream-50 text-charcoal/60 hover:bg-white hover:text-accent border border-cream-200 hover:border-accent hover:shadow-md hover:scale-105" data-filter="cultura">
                <span>Cultura</span>
            </button>
            
            <button class="activity-filter group relative px-6 py-2.5 rounded-full text-sm font-bold tracking-wide uppercase transition-all duration-300 bg-cream-50 text-charcoal/60 hover:bg-white hover:text-accent border border-cream-200 hover:border-accent hover:shadow-md hover:scale-105" data-filter="gastronomia">
                <span>Gastronomia</span>
            </button>
            
            <button class="activity-filter group relative px-6 py-2.5 rounded-full text-sm font-bold tracking-wide uppercase transition-all duration-300 bg-cream-50 text-charcoal/60 hover:bg-white hover:text-accent border border-cream-200 hover:border-accent hover:shadow-md hover:scale-105" data-filter="aventura">
                <span>Aventura</span>
            </button>
        </div>
    </div>
</div>

<!-- Activities Grid -->
<section class="py-20 bg-cream-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8" id="activities-grid">

            <!-- Activity 1: Parque Natural do Douro Internacional -->
            <article class="activity-card group bg-white rounded-3xl overflow-hidden shadow-[0_10px_30px_rgba(0,0,0,0.05)] hover:shadow-xl transition-all duration-500 hover:-translate-y-2" data-category="natureza">
                <div class="aspect-[4/3] relative overflow-hidden">
                    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110" style="background-image: url('<?= asset('images/douro_internacional.jpg') ?>'); background-color: #5D7A4F;">
                       <!-- Fallback if image missing -->
                       <div class="w-full h-full bg-gradient-to-br from-secondary/80 to-primary/80 flex items-center justify-center opacity-90">
                           <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                       </div>
                    </div>
                    <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-colors"></div>
                    <span class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-secondary text-xs font-bold tracking-widest uppercase px-4 py-2 rounded-full shadow-sm">
                        Natureza
                    </span>
                </div>
                <div class="p-8">
                    <h3 class="font-serif text-2xl text-primary mb-3 group-hover:text-secondary transition-colors">Parque Natural Douro</h3>
                    <p class="text-charcoal/70 text-sm leading-relaxed mb-6 font-light">
                        Paisagens de cortar a respiração ao longo das arribas do Douro. Observe aves de rapina nos seus habitats naturais.
                    </p>
                    <div class="flex items-center text-xs font-medium text-accent uppercase tracking-wider">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        20 km de Mogadouro
                    </div>
                </div>
            </article>

            <!-- Activity 2: Castelo de Mogadouro -->
            <article class="activity-card group bg-white rounded-3xl overflow-hidden shadow-[0_10px_30px_rgba(0,0,0,0.05)] hover:shadow-xl transition-all duration-500 hover:-translate-y-2" data-category="cultura">
                <div class="aspect-[4/3] relative overflow-hidden">
                    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110" style="background-color: #C5A059;">
                         <div class="w-full h-full bg-gradient-to-br from-accent/80 to-primary/80 flex items-center justify-center opacity-90">
                           <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                       </div>
                    </div>
                    <span class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-secondary text-xs font-bold tracking-widest uppercase px-4 py-2 rounded-full shadow-sm">
                        Cultura & História
                    </span>
                </div>
                <div class="p-8">
                    <h3 class="font-serif text-2xl text-primary mb-3 group-hover:text-secondary transition-colors">Castelo de Mogadouro</h3>
                    <p class="text-charcoal/70 text-sm leading-relaxed mb-6 font-light">
                        Torre de menagem medieval, símbolo da vila. Desfrute de vistas panorâmicas e descubra a história templária.
                    </p>
                    <div class="flex items-center text-xs font-medium text-accent uppercase tracking-wider">
                         <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Centro de Mogadouro
                    </div>
                </div>
            </article>

            <!-- Activity 3: Gastronomia -->
            <article class="activity-card group bg-white rounded-3xl overflow-hidden shadow-[0_10px_30px_rgba(0,0,0,0.05)] hover:shadow-xl transition-all duration-500 hover:-translate-y-2" data-category="gastronomia">
                <div class="aspect-[4/3] relative overflow-hidden">
                    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110" style="background-color: #8B4513;">
                        <div class="w-full h-full bg-gradient-to-br from-orange-900/80 to-primary/80 flex items-center justify-center opacity-90">
                           <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                       </div>
                    </div>
                    <span class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-secondary text-xs font-bold tracking-widest uppercase px-4 py-2 rounded-full shadow-sm">
                        Gastronomia
                    </span>
                </div>
                <div class="p-8">
                    <h3 class="font-serif text-2xl text-primary mb-3 group-hover:text-secondary transition-colors">Sabores da Região</h3>
                    <p class="text-charcoal/70 text-sm leading-relaxed mb-6 font-light">
                        Prove a famosa posta mirandesa, os enchidos artesanais e o azeite único em restaurantes locais de excelência.
                    </p>
                    <div class="flex items-center text-xs font-medium text-accent uppercase tracking-wider">
                         <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Experiência imperdível
                    </div>
                </div>
            </article>

            <!-- Activity 4: Trilhos -->
            <article class="activity-card group bg-white rounded-3xl overflow-hidden shadow-[0_10px_30px_rgba(0,0,0,0.05)] hover:shadow-xl transition-all duration-500 hover:-translate-y-2" data-category="aventura">
                <div class="aspect-[4/3] relative overflow-hidden">
                     <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110" style="background-color: #2F4F4F;">
                        <div class="w-full h-full bg-gradient-to-br from-green-900/80 to-primary/80 flex items-center justify-center opacity-90">
                           <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                       </div>
                    </div>
                    <span class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-secondary text-xs font-bold tracking-widest uppercase px-4 py-2 rounded-full shadow-sm">
                        Aventura
                    </span>
                </div>
                <div class="p-8">
                    <h3 class="font-serif text-2xl text-primary mb-3 group-hover:text-secondary transition-colors">Trilhos Pedestres</h3>
                    <p class="text-charcoal/70 text-sm leading-relaxed mb-6 font-light">
                        Caminhadas suaves ou trilhos desafiantes pelas serras e vales. Explore a natureza ao seu próprio ritmo.
                    </p>
                    <div class="flex items-center text-xs font-medium text-accent uppercase tracking-wider">
                         <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        Vários percursos
                    </div>
                </div>
            </article>

            <!-- Activity 5: Convento -->
            <article class="activity-card group bg-white rounded-3xl overflow-hidden shadow-[0_10px_30px_rgba(0,0,0,0.05)] hover:shadow-xl transition-all duration-500 hover:-translate-y-2" data-category="cultura">
                <div class="aspect-[4/3] relative overflow-hidden">
                     <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110" style="background-color: #8B0000;">
                        <div class="w-full h-full bg-gradient-to-br from-red-900/80 to-primary/80 flex items-center justify-center opacity-90">
                           <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                       </div>
                    </div>
                    <span class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-secondary text-xs font-bold tracking-widest uppercase px-4 py-2 rounded-full shadow-sm">
                        Cultura
                    </span>
                </div>
                <div class="p-8">
                    <h3 class="font-serif text-2xl text-primary mb-3 group-hover:text-secondary transition-colors">Convento de S. Francisco</h3>
                    <p class="text-charcoal/70 text-sm leading-relaxed mb-6 font-light">
                        Antigo convento do séc. XIII transformado em espaço cultural. Admire a arquitetura gótica e os azulejos.
                    </p>
                    <div class="flex items-center text-xs font-medium text-accent uppercase tracking-wider">
                         <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Centro de Mogadouro
                    </div>
                </div>
            </article>

            <!-- Activity 6: Enoturismo -->
            <article class="activity-card group bg-white rounded-3xl overflow-hidden shadow-[0_10px_30px_rgba(0,0,0,0.05)] hover:shadow-xl transition-all duration-500 hover:-translate-y-2" data-category="gastronomia">
                <div class="aspect-[4/3] relative overflow-hidden">
                     <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110" style="background-color: #556B2F;">
                        <div class="w-full h-full bg-gradient-to-br from-lime-900/80 to-primary/80 flex items-center justify-center opacity-90">
                           <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                       </div>
                    </div>
                    <span class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-secondary text-xs font-bold tracking-widest uppercase px-4 py-2 rounded-full shadow-sm">
                        Enoturismo
                    </span>
                </div>
                <div class="p-8">
                    <h3 class="font-serif text-2xl text-primary mb-3 group-hover:text-secondary transition-colors">Vinhos do Douro</h3>
                    <p class="text-charcoal/70 text-sm leading-relaxed mb-6 font-light">
                        Visite quintas vinícolas e descubra os segredos da vindima transmontana e do Vinho do Porto.
                    </p>
                    <div class="flex items-center text-xs font-medium text-accent uppercase tracking-wider">
                         <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        30 km de Mogadouro
                    </div>
                </div>
            </article>

        </div>
    </div>
</section>

<!-- Seasonal Calendar -->
<section class="py-24 bg-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16 animate-on-scroll">
            <span class="text-accent text-sm font-medium tracking-[0.2em] uppercase mb-3 inline-block">Ao Longo do Ano</span>
            <h2 class="font-serif text-3xl md:text-5xl text-primary mb-4">Eventos e Festividades</h2>
            <p class="text-charcoal/70 max-w-2xl mx-auto leading-relaxed">
                Mogadouro vive de tradições. Descubra os eventos que marcam as estações.
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Spring -->
            <div class="bg-cream-50 rounded-2xl p-8 border border-cream-100 hover:border-accent/30 transition-all duration-300 hover:shadow-lg group">
                <div class="w-14 h-14 bg-white rounded-xl shadow-sm flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                </div>
                <h3 class="font-serif text-xl text-primary mb-4">Primavera</h3>
                <ul class="text-charcoal/70 text-sm space-y-3">
                    <li class="flex items-center"><div class="w-1.5 h-1.5 bg-accent rounded-full mr-2"></div>Amendoeiras em Flor</li>
                    <li class="flex items-center"><div class="w-1.5 h-1.5 bg-accent rounded-full mr-2"></div>Páscoa Tradicional</li>
                    <li class="flex items-center"><div class="w-1.5 h-1.5 bg-accent rounded-full mr-2"></div>Romarias locais</li>
                </ul>
            </div>

            <!-- Summer -->
            <div class="bg-cream-50 rounded-2xl p-8 border border-cream-100 hover:border-accent/30 transition-all duration-300 hover:shadow-lg group">
                 <div class="w-14 h-14 bg-white rounded-xl shadow-sm flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <h3 class="font-serif text-xl text-primary mb-4">Verão</h3>
                <ul class="text-charcoal/70 text-sm space-y-3">
                    <li class="flex items-center"><div class="w-1.5 h-1.5 bg-accent rounded-full mr-2"></div>Festas N. Sra. Caminho</li>
                    <li class="flex items-center"><div class="w-1.5 h-1.5 bg-accent rounded-full mr-2"></div>Festival Medieval</li>
                    <li class="flex items-center"><div class="w-1.5 h-1.5 bg-accent rounded-full mr-2"></div>Noites de Verão</li>
                </ul>
            </div>

            <!-- Autumn -->
            <div class="bg-cream-50 rounded-2xl p-8 border border-cream-100 hover:border-accent/30 transition-all duration-300 hover:shadow-lg group">
                 <div class="w-14 h-14 bg-white rounded-xl shadow-sm flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h3 class="font-serif text-xl text-primary mb-4">Outono</h3>
                <ul class="text-charcoal/70 text-sm space-y-3">
                    <li class="flex items-center"><div class="w-1.5 h-1.5 bg-accent rounded-full mr-2"></div>Vindimas no Douro</li>
                    <li class="flex items-center"><div class="w-1.5 h-1.5 bg-accent rounded-full mr-2"></div>Feira da Castanha</li>
                    <li class="flex items-center"><div class="w-1.5 h-1.5 bg-accent rounded-full mr-2"></div>Matança do Porco</li>
                </ul>
            </div>

            <!-- Winter -->
            <div class="bg-cream-50 rounded-2xl p-8 border border-cream-100 hover:border-accent/30 transition-all duration-300 hover:shadow-lg group">
                 <div class="w-14 h-14 bg-white rounded-xl shadow-sm flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                </div>
                <h3 class="font-serif text-xl text-primary mb-4">Inverno</h3>
                <ul class="text-charcoal/70 text-sm space-y-3">
                    <li class="flex items-center"><div class="w-1.5 h-1.5 bg-accent rounded-full mr-2"></div>Natal Tradicional</li>
                    <li class="flex items-center"><div class="w-1.5 h-1.5 bg-accent rounded-full mr-2"></div>Dia de Reis</li>
                    <li class="flex items-center"><div class="w-1.5 h-1.5 bg-accent rounded-full mr-2"></div>Carnaval Transmontano</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Useful Info & Links -->
<section class="py-24 bg-cream-50 overflow-hidden">
     <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            
            <!-- Getting Around -->
            <div class="animate-on-scroll" data-animation="fade-right">
                <span class="text-accent text-sm font-medium tracking-[0.2em] uppercase mb-3 block">Info Prática</span>
                <h2 class="font-serif text-3xl md:text-4xl text-primary mb-8">Como Chegar e Circular</h2>
                
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="w-12 h-12 rounded-full bg-white shadow-md flex items-center justify-center flex-shrink-0 text-secondary border border-cream-100">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        </div>
                        <div>
                            <h4 class="font-serif text-lg text-primary mb-2">De Carro</h4>
                            <p class="text-charcoal/70 text-sm leading-relaxed">A forma mais prática de explorar. Mogadouro está a cerca de 2h30 do Porto via A4/IP4.</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-4">
                        <div class="w-12 h-12 rounded-full bg-white shadow-md flex items-center justify-center flex-shrink-0 text-secondary border border-cream-100">
                              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-serif text-lg text-primary mb-2">Transportes Públicos</h4>
                            <p class="text-charcoal/70 text-sm leading-relaxed">Existem autocarros da Rede Expressos que ligam Mogadouro ao Porto e a Bragança diariamente.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Links -->
            <div class="bg-white rounded-3xl p-8 md:p-12 shadow-[0_20px_40px_rgba(0,0,0,0.05)] border border-cream-100 animate-on-scroll" data-animation="fade-left">
                <h3 class="font-serif text-2xl text-primary mb-8">Ligações Úteis</h3>
                <div class="space-y-4">
                    <a href="https://www.cm-mogadouro.pt/" target="_blank" class="flex items-center justify-between p-4 bg-cream-50 rounded-xl hover:bg-cream-100 transition-colors group border border-transparent hover:border-cream-200">
                        <span class="font-medium text-primary group-hover:text-secondary transition-colors">Câmara Municipal</span>
                        <svg class="w-5 h-5 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="https://natural.pt/protected-areas/parque-natural-do-douro-internacional" target="_blank" class="flex items-center justify-between p-4 bg-cream-50 rounded-xl hover:bg-cream-100 transition-colors group border border-transparent hover:border-cream-200">
                        <span class="font-medium text-primary group-hover:text-secondary transition-colors">Parque Natural do Douro</span>
                        <svg class="w-5 h-5 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                     <a href="https://www.visitportugal.com/pt-pt/destinos/porto-e-norte/tras-os-montes" target="_blank" class="flex items-center justify-between p-4 bg-cream-50 rounded-xl hover:bg-cream-100 transition-colors group border border-transparent hover:border-cream-200">
                        <span class="font-medium text-primary group-hover:text-secondary transition-colors">Visit Portugal</span>
                        <svg class="w-5 h-5 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
            
        </div>
     </div>
</section>



<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.activity-filter');
    const activityCards = document.querySelectorAll('.activity-card');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;

            // Update active button state
            filterButtons.forEach(btn => {
                btn.classList.remove('active', 'bg-secondary', 'text-white', 'shadow-lg', 'shadow-secondary/20');
                btn.classList.add('bg-cream-50', 'text-charcoal/60');
            });
            this.classList.remove('bg-cream-50', 'text-charcoal/60');
            this.classList.add('active', 'bg-secondary', 'text-white', 'shadow-lg', 'shadow-secondary/20');

            // Filter cards with animation
            activityCards.forEach(card => {
                if (filter === 'all' || card.dataset.category === filter) {
                    card.style.display = 'block';
                    // Restart animation
                    card.style.animation = 'none';
                    card.offsetHeight; /* trigger reflow */
                    card.style.animation = 'fadeInUp 0.5s ease forwards';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>

<style>
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<?php include INCLUDES_PATH . '/footer.php'; ?>
