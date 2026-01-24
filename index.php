<?php
/**
 * A Casa do Gi - Homepage
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

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section with Parallax -->
<section class="relative h-screen min-h-[700px] flex items-center justify-center overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 parallax-bg transform scale-105"
         style="background-image: url('<?= asset('images/MogadouroNeve.jpeg') ?>');">
        <!-- Overlay Gradient -->
        <div class="absolute inset-0 bg-primary/30 mix-blend-multiply"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-primary/20 via-transparent to-cream"></div>
    </div>

    <!-- Floating Decorative Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <!-- Floating leaf 1 -->
        <div class="absolute top-1/4 left-[10%] w-16 h-16 opacity-20 animate-float-slow">
            <svg viewBox="0 0 24 24" fill="currentColor" class="text-accent w-full h-full">
                <path d="M17,8C8,10 5.9,16.17 3.82,21.34L5.71,22L6.66,19.7C7.14,19.87 7.64,20 8,20C19,20 22,3 22,3C21,5 14,5.25 9,6.25C4,7.25 2,11.5 2,13.5C2,15.5 3.75,17.25 3.75,17.25C7,8 17,8 17,8Z"/>
            </svg>
        </div>
        <!-- Floating circle 2 -->
        <div class="absolute top-1/3 right-[15%] w-24 h-24 border-2 border-cream/10 rounded-full animate-float-medium"></div>
        <!-- Floating dot 3 -->
        <div class="absolute bottom-1/3 left-[20%] w-4 h-4 bg-accent/30 rounded-full animate-float-fast"></div>
        <!-- Floating ring 4 -->
        <div class="absolute top-1/2 right-[8%] w-12 h-12 border border-cream/15 rounded-full animate-float-slow" style="animation-delay: -2s;"></div>
        <!-- Floating diamond 5 -->
        <div class="absolute bottom-1/4 right-[25%] w-8 h-8 bg-cream/5 rotate-45 animate-float-medium" style="animation-delay: -1s;"></div>
    </div>

    <!-- Hero Content -->
    <div class="relative z-10 text-center px-4 max-w-5xl mx-auto">
        <!-- Animated Entrance -->
        <div class="animate-slide-up space-y-8">
            <span class="inline-block text-accent text-lg md:text-xl font-medium tracking-[0.2em] uppercase mb-4 animate-fade-in drop-shadow-md">
                Bem-vindo a
            </span>
            
            <h1 class="font-cursive text-7xl md:text-8xl lg:text-9xl text-cream drop-shadow-2xl leading-none mb-6">
                <?= e($content['hero_title'] ?? 'A Casa do Gi') ?>
            </h1>

            <p class="font-serif text-xl md:text-2xl text-cream/95 max-w-2xl mx-auto italic font-light leading-relaxed drop-shadow-md">
                "<?= e($content['hero_subtitle'] ?? 'Simplicidade, acolhimento e muito amor em Mogadouro') ?>"
            </p>

            <!-- Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-6 mt-12">
                <a href="<?= $base ?>/alojamento/"
                   class="group relative overflow-hidden inline-flex items-center px-10 py-4 bg-secondary text-cream rounded-full transition-all duration-300 hover:scale-105 hover:shadow-xl hover:bg-secondary-600">
                    <span class="relative font-bold tracking-widest uppercase text-sm">Descobrir a Casa</span>
                    <svg class="relative w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
                
                <?php if (isShopEnabled()): ?>
                <a href="<?= $base ?>/loja/"
                   class="group inline-flex items-center px-10 py-4 bg-white/10 backdrop-blur-sm text-cream border border-cream/30 rounded-full transition-all duration-300 hover:bg-white/20 hover:border-cream/50 hover:shadow-lg">
                    <span class="font-bold tracking-widest uppercase text-sm">Produtos Regionais</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 animate-bounce">
        <a href="#about" class="text-cream/80 hover:text-accent transition-colors p-2">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
            </svg>
        </a>
    </div>
</section>

<!-- Introduction / About Section -->
<section id="about" class="py-24 relative overflow-hidden">
    <!-- Decorative Pattern -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-accent/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-secondary/5 rounded-full blur-3xl translate-y-1/3 -translate-x-1/4"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <!-- Images Grid - Parallax Effect -->
            <div class="relative order-2 lg:order-1 group animate-on-scroll">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-4 translate-y-12 transition-transform duration-700 hover:translate-y-10">
                        <div class="aspect-[3/4] rounded-2xl overflow-hidden shadow-xl transform transition-transform hover:scale-[1.02] duration-500 border border-black/5">
                            <img src="<?= asset('images/about-1.jpg') ?>" class="w-full h-full object-cover" alt="Interior">
                        </div>
                    </div>
                    <div class="space-y-4 -translate-y-12 transition-transform duration-700 hover:-translate-y-10">
                        <div class="aspect-[3/4] rounded-2xl overflow-hidden shadow-xl transform transition-transform hover:scale-[1.02] duration-500 border-4 border-white">
                            <img src="<?= asset('images/about-2.jpg') ?>" class="w-full h-full object-cover" alt="Detalhe">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="order-1 lg:order-2 space-y-8 animate-on-scroll" data-delay="200">
                <div class="space-y-2">
                    <span class="font-cursive text-4xl text-accent block mb-2">Nossa Historia</span>
                    <h2 class="font-serif text-4xl lg:text-5xl text-primary leading-tight">
                        Uma casa com alma e <br>
                        <span class="italic text-secondary">muita tradicao</span>
                    </h2>
                </div>

                <div class="prose prose-lg text-charcoal/80">
                    <p><?= $content['about_text'] ?? 'Construida nos anos 80, esta casa e o resultado do sonho e do esforco de quem partiu em busca de novas oportunidades, mas nunca esqueceu as suas raizes.' ?></p>
                    <p class="font-serif text-xl text-primary/80 italic border-l-4 border-accent pl-6 py-4 my-8 bg-cream-100/50 rounded-r-lg">
                        "A Casa do Gi e sinonimo de simplicidade, acolhimento, momentos de convivio marcantes e muito amor!"
                    </p>
                    <p>Aqui, cada canto conta uma historia e cada detalhe foi pensado para que se sinta nao num alojamento, mas em sua propria casa.</p>
                </div>

                <a href="<?= $base ?>/sobre-nos/" class="inline-flex items-center text-primary font-bold hover:text-accent transition-colors group uppercase tracking-widest text-sm">
                    <span class="border-b-2 border-primary/30 group-hover:border-accent transition-colors pb-1">Conheca a nossa historia</span>
                    <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Amenities Section -->
<section class="py-24 bg-primary text-cream relative overflow-hidden">
    <!-- Background Texture -->
    <div class="absolute inset-0 opacity-5" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-16 space-y-4 animate-on-scroll">
            <span class="font-cursive text-4xl text-accent">Comodidades</span>
            <h2 class="font-serif text-4xl text-cream">Tudo o que precisa para relaxar</h2>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 animate-on-scroll" data-delay="200">
            <!-- Feature 1 -->
            <div class="p-8 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 hover:border-accent/50 transition-all duration-300 group text-center hover:-translate-y-1">
                <div class="w-16 h-16 mx-auto mb-6 bg-secondary/20 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300 text-accent">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="font-serif text-xl mb-2 text-cream">6 Hospedes</h3>
                <p class="text-cream/60 text-sm">Ideal para familias e grupos</p>
            </div>

            <!-- Feature 2 -->
            <div class="p-8 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 hover:border-accent/50 transition-all duration-300 group text-center hover:-translate-y-1">
                <div class="w-16 h-16 mx-auto mb-6 bg-secondary/20 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300 text-accent">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <h3 class="font-serif text-xl mb-2 text-cream">3 Quartos</h3>
                <p class="text-cream/60 text-sm">Conforto maximo garantido</p>
            </div>

            <!-- Feature 3 -->
            <div class="p-8 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 hover:border-accent/50 transition-all duration-300 group text-center hover:-translate-y-1">
                <div class="w-16 h-16 mx-auto mb-6 bg-secondary/20 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300 text-accent">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                    </svg>
                </div>
                <h3 class="font-serif text-xl mb-2 text-cream">Wi-Fi & TV</h3>
                <p class="text-cream/60 text-sm">Sempre conectado</p>
            </div>

            <!-- Feature 4 -->
            <div class="p-8 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 hover:border-accent/50 transition-all duration-300 group text-center hover:-translate-y-1">
                <div class="w-16 h-16 mx-auto mb-6 bg-secondary/20 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300 text-accent">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
                <h3 class="font-serif text-xl mb-2 text-cream">Exterior</h3>
                <p class="text-cream/60 text-sm">Piscina e Churrasqueira</p>
            </div>
        </div>
    </div>
</section>

<!-- Location Section -->
<section class="py-24 relative overflow-hidden bg-cream text-center md:text-left">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div class="space-y-6 animate-on-scroll">
                <span class="font-cursive text-4xl text-secondary">A Regiao</span>
                <h2 class="font-serif text-4xl md:text-5xl text-primary leading-tight">Mogadouro,<br>Terra de Encantos</h2>
                <p class="text-lg text-charcoal/70 leading-relaxed font-light">
                    Localizada no Nordeste Transmontano, Mogadouro oferece paisagens deslumbrantes, historia riquissima e uma gastronomia de comer e chorar por mais.
                </p>
                
                <div class="space-y-4 pt-4">
                    <div class="flex items-center justify-center md:justify-start space-x-4 group">
                        <span class="w-12 h-1 bg-accent/30 rounded-full group-hover:bg-accent group-hover:w-16 transition-all duration-300"></span>
                        <span class="text-primary font-medium group-hover:text-accent transition-colors">Parque Natural do Douro Internacional</span>
                    </div>
                    <div class="flex items-center justify-center md:justify-start space-x-4 group">
                        <span class="w-12 h-1 bg-accent/30 rounded-full group-hover:bg-accent group-hover:w-16 transition-all duration-300"></span>
                        <span class="text-primary font-medium group-hover:text-accent transition-colors">Castelo de Mogadouro</span>
                    </div>
                    <div class="flex items-center justify-center md:justify-start space-x-4 group">
                        <span class="w-12 h-1 bg-accent/30 rounded-full group-hover:bg-accent group-hover:w-16 transition-all duration-300"></span>
                        <span class="text-primary font-medium group-hover:text-accent transition-colors">Lagos do Sabor</span>
                    </div>
                </div>

                <div class="pt-8">
                    <a href="<?= $base ?>/atividades/" class="inline-flex items-center px-8 py-3 border border-primary text-primary font-bold rounded-lg hover:bg-primary hover:text-white transition-all duration-300 uppercase tracking-wider text-sm">
                        Explorar a Regiao
                    </a>
                </div>
            </div>

            <!-- Image Composition -->
            <div class="relative animate-on-scroll" data-delay="200">
                <div class="aspect-video rounded-2xl overflow-hidden shadow-2xl skew-y-2 hover:skew-y-0 transition-transform duration-700 cursor-pointer">
                    <img src="<?= asset('images/mogadouro.jpg') ?>" class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-1000" alt="Mogadouro">
                    <!-- Badge -->
                    <div class="absolute bottom-6 left-6 bg-cream/95 backdrop-blur-md p-4 rounded-xl shadow-lg border-l-4 border-accent">
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <div>
                                <p class="text-xs text-charcoal/50 uppercase font-bold tracking-wider">Localizacao</p>
                                <p class="text-primary font-serif font-bold">Mogadouro</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA / Booking Section -->
<section class="py-24 bg-secondary relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 30px 30px;"></div>
    
    <div class="max-w-4xl mx-auto px-4 text-center relative z-10 transition-all duration-300 animate-on-scroll">
        <h2 class="font-cursive text-5xl md:text-6xl text-cream mb-8 drop-shadow-sm">Reserve a sua Estadia</h2>
        <p class="text-xl text-cream/90 mb-12 max-w-2xl mx-auto font-light">
            Pronto para dias inesqueciveis em Tras-os-Montes? Escolha a sua plataforma preferida e garanta ja a sua reserva.
        </p>
        
        <div class="flex flex-col md:flex-row items-center justify-center gap-6">
            <?php if ($bookingUrl = setting('booking_url')): ?>
            <a href="<?= e($bookingUrl) ?>" target="_blank" 
               class="w-full md:w-auto flex items-center justify-center px-8 py-5 bg-[#003580] text-white font-bold rounded-xl shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group min-w-[200px]">
                <!-- Booking Logo -->
                <div class="mr-3">
                   <svg class="h-6 w-auto" viewbox="0 0 24 24" fill="none">
                        <path d="M4 3C2.89543 3 2 3.89543 2 5V19C2 20.1046 2.89543 21 4 21H13V15H11V13H13V3H4Z" fill="white"/>
                        <path d="M19 8C19.5523 8 20 8.44772 20 9V11C20 11.5523 19.5523 12 19 12H15V8H19Z" fill="white"/>
                        <path d="M19 14C19.5523 14 20 14.4477 20 15V19C20 19.5523 19.5523 20 19 20H15V14H19Z" fill="white"/>
                   </svg> 
                </div>
                Booking.com
            </a>
            <?php endif; ?>
            
            <?php if ($airbnbUrl = setting('airbnb_url')): ?>
            <a href="<?= e($airbnbUrl) ?>" target="_blank" 
               class="w-full md:w-auto flex items-center justify-center px-8 py-5 bg-[#FF385C] text-white font-bold rounded-xl shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group min-w-[200px]">
                <!-- Airbnb Logo -->
                <svg class="w-6 h-6 mr-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M22.519,4.427C21.949,3.879,21.166,3.606,20.252,3.606c-0.494,0-0.965,0.082-1.401,0.245 c-0.638,0.24-1.258,0.704-1.849,1.383c-1.302,1.496-2.924,4.421-4.996,8.995c-2.071-4.573-3.694-7.498-4.996-8.995 C6.42,4.555,5.801,4.09,5.163,3.851C4.727,3.688,4.256,3.606,3.762,3.606c-0.914,0-1.697,0.273-2.267,0.821 C0.804,5.15,0.463,6.29,0.463,7.96c0,1.935,0.49,4.259,1.455,6.905c1.474,4.043,4.646,7.575,8.933,9.947l1.155,0.64l1.155-0.64 c4.287-2.372,7.459-5.904,8.933-9.947c0.965-2.646,1.455-4.97,1.455-6.905C23.547,6.29,23.206,5.15,22.519,4.427L22.519,4.427z"/>
                </svg>
                Airbnb
            </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
// Initialize Parallax and Floating Animations
$pageScripts = <<<'JS'
<style>
/* Floating Animation Keyframes */
@keyframes float-slow {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
}
@keyframes float-medium {
    0%, 100% { transform: translateY(0) scale(1); }
    50% { transform: translateY(-15px) scale(1.05); }
}
@keyframes float-fast {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
.animate-float-slow {
    animation: float-slow 8s ease-in-out infinite;
}
.animate-float-medium {
    animation: float-medium 6s ease-in-out infinite;
}
.animate-float-fast {
    animation: float-fast 4s ease-in-out infinite;
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .animate-float-slow,
    .animate-float-medium,
    .animate-float-fast {
        animation: none;
    }
}
</style>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const parallaxBg = document.querySelector('.parallax-bg');
        if (parallaxBg && window.innerWidth > 768) {
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                parallaxBg.style.transform = `scale(1.05) translateY(${scrolled * 0.5}px)`;
            });
        }
    });
</script>
JS;

include INCLUDES_PATH . '/footer.php';
?>
