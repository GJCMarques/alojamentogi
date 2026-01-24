<?php
/**
 * A Casa do Gi - About Us Page (Portuguese)
 */

require_once dirname(__DIR__) . '/includes/init.php';

use Core\Language;

$lang = Language::getInstance();
$base = basePath();

// Get page content
$content = $lang->getPageContents('about');

// Page configuration
$pageTitle = 'Sobre Nós';
$pageDescription = 'Conheça a história da Casa do Gi - construída nos anos 80, sinónimo de simplicidade, acolhimento e muito amor em Mogadouro.';

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="relative py-20 lg:py-32 bg-primary overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmZmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIj48cGF0aCBkPSJNMzYgMzRjMC0yLjIwOS0xLjc5MS00LTQtNHMtNCAxLjc5MS00IDQgMS43OTEgNCA0IDQgNC0xLjc5MSA0LTR6Ii8+PC9nPjwvZz48L3N2Zz4=')]"></div>
    </div>
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-primary/50 to-primary"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <span class="inline-block text-accent text-lg font-medium tracking-[0.2em] uppercase mb-4 animate-fade-in">
            A Nossa História
        </span>
        <h1 class="font-cursive text-6xl md:text-7xl lg:text-8xl text-cream mb-6 drop-shadow-lg">
            A Casa do Gi
        </h1>
        <p class="text-xl md:text-2xl text-cream/90 max-w-3xl mx-auto font-light leading-relaxed">
            Simplicidade, acolhimento, momentos de convívio marcantes, calor da família, alegria, diversão, gargalhadas e muito amor.
        </p>
    </div>
</section>

<!-- Story Section -->
<section class="py-16 lg:py-24 bg-cream-100 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
            <!-- Image -->
            <div class="relative group">
                <div class="aspect-[4/5] rounded-2xl overflow-hidden shadow-2xl border-4 border-white transform transition-transform duration-700 hover:scale-[1.02]">
                    <div class="w-full h-full bg-gradient-to-br from-accent/20 to-cream flex items-center justify-center">
                        <!-- Placeholder for historical image or just refined graphic -->
                        <img src="<?= asset('images/about-1.jpg') ?>" class="w-full h-full object-cover opacity-80 mix-blend-multiply" alt="Historia">
                    </div>
                </div>
                <!-- Decorative Badge -->
                <div class="absolute -bottom-6 -right-6 bg-secondary text-white p-8 rounded-2xl shadow-xl shadow-secondary/20 animate-bounce-slow">
                    <p class="text-4xl font-serif font-bold">1980</p>
                    <p class="text-sm text-white/80 uppercase tracking-widest mt-1">Ano de construção</p>
                </div>
            </div>

            <!-- Content -->
            <div class="space-y-8">
                <h2 class="font-serif text-4xl md:text-5xl text-primary leading-tight">
                    Uma Casa com <br><span class="text-secondary italic">História & Alma</span>
                </h2>
                <div class="prose prose-lg text-charcoal/80 space-y-6 font-light">
                    <p>
                        <strong class="text-primary font-serif">Construída nos anos 80</strong>, altura em que os "artistas da construção" e os "materiais" eram escassos por Terras de Mogadouro, este edifício foi mandado construir desde terras de Santa Cruz, por carta, e com os recursos de quem saiu da terra em busca de uma melhor oportunidade!
                    </p>
                    <p>
                        A Casa do Gi nasceu do sonho de uma família que, mesmo longe da sua terra natal, nunca esqueceu as suas raízes. Cada pedra, cada tijolo carrega consigo a história de sacrifício, esperança e amor pela terra transmontana.
                    </p>
                    <div class="border-l-4 border-accent pl-6 py-2 italic text-primary/70">
                        "Hoje, abrimos as portas desta casa especial para que possam experimentar o mesmo calor e acolhimento que sempre caracterizou este lugar."
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="py-16 lg:py-24 bg-cream-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="font-serif text-3xl md:text-4xl text-primary mb-4">O Que Nos Define</h2>
            <p class="text-charcoal max-w-2xl mx-auto">
                A Casa do Gi é mais do que um alojamento - é uma experiência autêntica da hospitalidade transmontana.
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Value 1 -->
            <div class="text-center">
                <div class="w-16 h-16 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <h3 class="font-serif text-xl text-primary mb-2">Acolhimento</h3>
                <p class="text-charcoal text-sm">
                    Recebemos cada hóspede como se fosse da família, com o calor característico das gentes transmontanas.
                </p>
            </div>

            <!-- Value 2 -->
            <div class="text-center">
                <div class="w-16 h-16 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
                <h3 class="font-serif text-xl text-primary mb-2">Simplicidade</h3>
                <p class="text-charcoal text-sm">
                    Valorizamos as coisas simples da vida - uma boa refeição, uma boa conversa, um pôr do sol.
                </p>
            </div>

            <!-- Value 3 -->
            <div class="text-center">
                <div class="w-16 h-16 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="font-serif text-xl text-primary mb-2">Família</h3>
                <p class="text-charcoal text-sm">
                    Esta casa foi construída por amor à família, e é esse espírito que queremos partilhar consigo.
                </p>
            </div>

            <!-- Value 4 -->
            <div class="text-center">
                <div class="w-16 h-16 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="font-serif text-xl text-primary mb-2">Autenticidade</h3>
                <p class="text-charcoal text-sm">
                    Proporcionamos uma experiência genuína, longe do turismo de massas, imersa na cultura local.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Mogadouro Section -->
<section class="py-16 lg:py-24 bg-cream-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
            <!-- Content -->
            <div class="order-2 lg:order-1">
                <span class="inline-block text-secondary text-sm font-medium uppercase tracking-wider mb-4">
                    A Nossa Terra
                </span>
                <h2 class="font-serif text-3xl md:text-4xl text-primary mb-6">
                    Mogadouro, Trás-os-Montes
                </h2>
                <div class="prose prose-lg text-charcoal space-y-4">
                    <p>
                        Mogadouro é uma vila encantadora situada no nordeste de Portugal, no coração de Trás-os-Montes. Com uma história que remonta a tempos medievais, esta terra oferece um património rico e paisagens deslumbrantes.
                    </p>
                    <p>
                        Entre as escarpas do rio Douro e as serras do Sabor, Mogadouro é um tesouro escondido para quem procura natureza, tranquilidade e autenticidade. O Parque Natural do Douro Internacional é apenas uma das muitas maravilhas que pode descobrir.
                    </p>
                    <p>
                        A gastronomia transmontana é outro dos grandes atrativos - a posta mirandesa, os enchidos, o mel, o azeite e os vinhos do Douro são experiências que não pode perder.
                    </p>
                </div>
                <a href="<?= $base ?>/atividades/" class="inline-flex items-center mt-6 text-secondary font-medium hover:text-secondary">
                    Descobrir o que fazer em Mogadouro
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            <!-- Image Grid -->
            <div class="order-1 lg:order-2">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <div class="aspect-square bg-gradient-to-br from-accent/20 to-cream rounded-lg shadow-lg"></div>
                        <div class="aspect-[4/3] bg-gradient-to-br from-accent/20 to-cream rounded-lg shadow-lg"></div>
                    </div>
                    <div class="pt-8 space-y-4">
                        <div class="aspect-[4/3] bg-gradient-to-br from-accent/20 to-cream rounded-lg shadow-lg"></div>
                        <div class="aspect-square bg-gradient-to-br from-charcoal/20 to-cream rounded-lg shadow-lg"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 lg:py-24 bg-secondary">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-serif text-3xl md:text-4xl text-cream mb-6">
            Venha Conhecer-nos
        </h2>
        <p class="text-xl text-accent/80 mb-10">
            Estamos ansiosos por recebê-lo e partilhar consigo a magia da Casa do Gi.
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

<?php include INCLUDES_PATH . '/footer.php'; ?>
