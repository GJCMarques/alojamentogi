<?php

require_once dirname(__DIR__) . '/includes/init.php';

use Core\Language;
use Core\Database;

$lang = Language::getInstance();
$db = Database::getInstance();
$base = basePath();

$content = $lang->getPageContents('about');

$pageHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = 'about' AND is_active = 1");
$heroMedia = $pageHero ? $db->fetch("SELECT * FROM media WHERE entity_type = 'hero' AND entity_id = ? AND is_cover = 1", [$pageHero['id']]) : null;
$heroImage = $heroMedia['file_path'] ?? 'images/MogadouroSobre.png';
$heroOverlay = $pageHero['hero_overlay_opacity'] ?? 0.40;

$heroUrl = $heroImage[0] === '/' ? basePath() . $heroImage : asset($heroImage);

$pageTitle = 'Sobre Nós';
$pageDescription = 'Conheça a história da Casa do Gi - construída nos anos 80, sinónimo de simplicidade, acolhimento e muito amor em Mogadouro.';

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="relative h-screen md:h-[75vh] min-h-[600px] flex items-center bg-primary overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat bg-fixed"
             style="background-image: url('<?= $heroUrl ?>');">
        </div>
        <div class="absolute inset-0 bg-black" style="opacity: <?= $heroOverlay ?>"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <span class="inline-block text-accent text-lg font-medium tracking-[0.2em] uppercase mb-4 animate-on-scroll" data-animation="fade-up">
            <?= content('about_hero_tagline') ?>
        </span>
        <h1 class="font-cursive text-6xl md:text-7xl lg:text-8xl text-cream mb-6 drop-shadow-lg animate-on-scroll" data-animation="fade-up" data-delay="200">
            <?= content('about_hero_title') ?>
        </h1>
        <p class="text-xl md:text-2xl text-cream/90 max-w-3xl mx-auto font-light leading-relaxed animate-on-scroll" data-animation="fade-up" data-delay="400">
            <?= content('about_hero_subtitle') ?>
        </p>
    </div>
</section>

<!-- Intro Section -->
<section class="py-20 bg-white relative overflow-hidden">
    <!-- Decorative elements -->
    <div class="absolute top-0 left-0 w-64 h-64 bg-cream-100 rounded-br-full opacity-50 -translate-x-1/2 -translate-y-1/2"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div class="animate-on-scroll" data-animation="fade-right">
                <span class="text-accent text-sm font-bold tracking-widest uppercase mb-4 block"><?= $content['about_origin_label'] ?? 'A Nossa Origem' ?></span>
                <h2 class="font-serif text-4xl lg:text-5xl text-primary mb-6 leading-tight">
                    <?= $content['about_origin_title'] ?? 'Uma casa construída com <span class="italic text-secondary">amor</span> e <span class="italic text-secondary">saudade</span>.' ?>
                </h2>
                <div class="prose prose-lg text-charcoal-600 font-light space-y-4">
                    <p>
                        <?= $content['about_origin_text1'] ?? 'Erguida nos anos 80, a <strong>Casa do Gi</strong> conta a história intemporal de quem partiu para longe mas nunca esqueceu as suas raízes. Construída tijolo a tijolo, representa o sonho concretizado de regressar a casa.' ?>
                    </p>
                    <p>
                        <?= $content['about_origin_text2'] ?? 'O que começou como um projeto de vida familiar transformou-se num refúgio para quem procura a paz do interior. Aqui, o tempo abranda e os dias são medidos pela luz do sol e pelas conversas à beira da lareira.' ?>
                    </p>
                </div>

                <div class="mt-8 flex items-center gap-4">
                    <div class="h-px w-16 bg-accent"></div>
                    <span class="font-cursive text-2xl text-primary-600"><?= $content['about_origin_signature'] ?? 'Família Gi' ?></span>
                </div>
            </div>

            <div class="relative animate-on-scroll" data-animation="fade-left">
                <!-- Image Composition -->
                <div class="relative z-10 rounded-2xl overflow-hidden shadow-2xl transform rotate-2 hover:rotate-0 transition-transform duration-700">
                    <img src="<?= resolveContentImage(content('about_image_intro', 'images/FotoGi.png')) ?>" alt="A Casa do Gi Antigamente" class="w-full h-auto object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-primary/60 to-transparent"></div>
                    <div class="absolute bottom-6 left-6 text-white text-sm font-medium tracking-wide">
                        <?= $content['about_origin_caption'] ?? '1980 • O Início' ?>
                    </div>
                </div>
                <!-- Decorative Border -->
                <div class="absolute inset-0 border-2 border-accent rounded-2xl transform -rotate-2 scale-105 z-0 opacity-40"></div>
            </div>
        </div>
    </div>
</section>

<!-- Essence Section - Sophisticated Grid -->
<section class="py-24 bg-cream relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-12 gap-12 lg:gap-16 items-start">

            <!-- Sticky Title Area -->
            <div class="lg:col-span-4 lg:sticky lg:top-32 animate-on-scroll" data-animation="fade-right">
                <span class="text-accent text-sm font-bold tracking-widest uppercase mb-4 block"><?= $content['about_values_label'] ?? 'Valores' ?></span>
                <h2 class="font-serif text-4xl lg:text-5xl text-primary mb-6 leading-tight">
                    <?= $content['about_values_title'] ?? 'A arte de bem receber,<br>à moda antiga.' ?>
                </h2>
                <div class="w-16 h-1 bg-secondary mb-8"></div>
                <p class="text-charcoal-600 font-light text-lg leading-relaxed">
                    <?= $content['about_values_intro'] ?? 'Não somos um hotel. Somos uma casa de família que decidiu abrir as portas ao mundo. Aqui, a hospitalidade não é um serviço, é a nossa natureza.' ?>
                </p>
            </div>

            <!-- Cards Grid -->
            <div class="lg:col-span-8 grid md:grid-cols-2 gap-6">
                <!-- Card 1: Acolhimento -->
                <div class="bg-white p-10 rounded-2xl border-l-4 border-accent shadow-lg hover:shadow-xl transition-all duration-500 group animate-on-scroll" data-delay="100">
                    <div class="mb-6 text-primary group-hover:text-accent transition-colors duration-300">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <h3 class="font-serif text-2xl text-primary mb-3 group-hover:translate-x-1 transition-transform duration-300"><?= $content['about_value1_title'] ?? 'Acolhimento Genuíno' ?></h3>
                    <p class="text-charcoal-500 font-light leading-relaxed">
                        <?= $content['about_value1_text'] ?? 'Recebemos cada hóspede como um velho amigo. Sem formalismos rígidos, com o calor de um abraço e a sinceridade de um sorriso transmontano.' ?>
                    </p>
                </div>

                <!-- Card 2: Silêncio -->
                <div class="bg-white p-10 rounded-2xl border-l-4 border-secondary shadow-lg hover:shadow-xl transition-all duration-500 group animate-on-scroll md:mt-12" data-delay="200">
                    <div class="mb-6 text-secondary group-hover:text-primary transition-colors duration-300">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    </div>
                    <h3 class="font-serif text-2xl text-primary mb-3 group-hover:translate-x-1 transition-transform duration-300"><?= $content['about_value2_title'] ?? 'Paz Absoluta' ?></h3>
                    <p class="text-charcoal-500 font-light leading-relaxed">
                        <?= $content['about_value2_text'] ?? 'O luxo do silêncio. Longe da confusão, onde o único ruído é o vento nas árvores e o cantar dos pássaros. O refúgio perfeito para recarregar energias.' ?>
                    </p>
                </div>

                <!-- Card 3: Partilha -->
                <div class="bg-white p-10 rounded-2xl border-l-4 border-primary shadow-lg hover:shadow-xl transition-all duration-500 group animate-on-scroll" data-delay="300">
                    <div class="mb-6 text-primary group-hover:text-accent transition-colors duration-300">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="font-serif text-2xl text-primary mb-3 group-hover:translate-x-1 transition-transform duration-300"><?= $content['about_value3_title'] ?? 'Espírito de Partilha' ?></h3>
                    <p class="text-charcoal-500 font-light leading-relaxed">
                        <?= $content['about_value3_text'] ?? 'Acreditamos que as melhores memórias são construídas à mesa. Partilhamos histórias, sabores e experiências que ficam para sempre.' ?>
                    </p>
                </div>

               <!-- Card 4: Detalhe -->
                <div class="bg-white p-10 rounded-2xl border-l-4 border-cream-400 shadow-lg hover:shadow-xl transition-all duration-500 group animate-on-scroll md:mt-12" data-delay="400">
                    <div class="mb-6 text-accent group-hover:text-secondary transition-colors duration-300">
                         <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    </div>
                    <h3 class="font-serif text-2xl text-primary mb-3 group-hover:translate-x-1 transition-transform duration-300"><?= $content['about_value4_title'] ?? 'Atenção ao Detalhe' ?></h3>
                    <p class="text-charcoal-500 font-light leading-relaxed">
                        <?= $content['about_value4_text'] ?? 'Nada é deixado ao acaso. Da decoração cuidada aos pequenos detalhes, tudo é pensado para o seu conforto e bem-estar.' ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Region Section - Full Bleed Immersive -->
<section class="relative h-screen min-h-[700px] flex items-center overflow-hidden">
    <!-- Parallax Background -->
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-black/40 z-10"></div> <!-- Overlay -->
        <img src="<?= resolveContentImage(content('about_image_region', 'images/Castelo.jpg')) ?>" class="w-full h-full object-cover attachment-fixed transform scale-110" style="object-position: center;" alt="Castelo de Mogadouro">
    </div>

    <div class="relative z-20 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex flex-col justify-center">
        <div class="max-w-xl ml-auto text-right text-white">
            <span class="inline-block py-1 px-3 border border-white/30 rounded-full text-xs font-bold tracking-[0.2em] uppercase mb-6 backdrop-blur-sm animate-on-scroll" data-animation="fade-left">
                <?= $content['about_region_label'] ?? 'O Nosso Berço' ?>
            </span>
            <h2 class="font-serif text-6xl md:text-7xl lg:text-8xl mb-6 shadow-black drop-shadow-2xl animate-on-scroll" data-animation="fade-left" data-delay="100">
                Mogadouro
            </h2>
            <div class="w-24 h-1 bg-accent ml-auto mb-8 animate-on-scroll" data-animation="fade-left" data-delay="200"></div>
            <p class="text-xl md:text-2xl font-light leading-relaxed text-white/90 mb-10 drop-shadow-lg animate-on-scroll" data-animation="fade-left" data-delay="300">
                <?= $content['about_region_text'] ?? 'Onde o tempo pára e a alma respira. Uma terra de horizontes infinitos, guardiã de tradições milenares e de uma beleza natural bruta e intocada.' ?>
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-end animate-on-scroll" data-animation="fade-left" data-delay="400">
                 <a href="<?= $base ?>/contactos/" class="group inline-flex items-center justify-center px-8 py-4 bg-white text-primary font-bold rounded-full shadow-lg hover:shadow-xl hover:-translate-y-1 hover:bg-cream transition-all duration-300">
                    <span><?= $content['about_region_cta1'] ?? 'Planear Visita' ?></span>
                    <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                <a href="<?= $base ?>/atividades/" class="inline-flex items-center justify-center px-8 py-4 border border-white text-white font-medium rounded-full hover:bg-white/10 hover:-translate-y-1 transition-all duration-300">
                    <?= $content['about_region_cta2'] ?? 'O que fazer' ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Bottom gradient for smooth transition if needed -->
    <div class="absolute bottom-0 left-0 w-full h-32 bg-gradient-to-t from-black/50 to-transparent z-10 pointer-events-none"></div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
