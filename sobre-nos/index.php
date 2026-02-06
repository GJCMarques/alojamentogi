<?php
/**
 * A Casa do Gi - About Us Page (Portuguese)
 */

require_once dirname(__DIR__) . '/includes/init.php';

use Core\Language;
use Core\Database;

$lang = Language::getInstance();
$db = Database::getInstance();
$base = basePath();

// Get page content
$content = $lang->getPageContents('about');

// Get hero image from database
$pageHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = 'about' AND is_active = 1");
$heroMedia = $pageHero ? $db->fetch("SELECT * FROM media WHERE entity_type = 'hero' AND entity_id = ? AND is_cover = 1", [$pageHero['id']]) : null;
$heroImage = $heroMedia['file_path'] ?? 'images/MogadouroSobre.png';
$heroOverlay = $pageHero['hero_overlay_opacity'] ?? 0.40;

// Build hero URL (file_path from media already has leading slash)
$heroUrl = $heroImage[0] === '/' ? basePath() . $heroImage : asset($heroImage);

// Page configuration
$pageTitle = 'Sobre Nós';
$pageDescription = 'Conheça a história da Casa do Gi - construída nos anos 80, sinónimo de simplicidade, acolhimento e muito amor em Mogadouro.';

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="relative h-[75vh] min-h-[600px] flex items-center bg-primary overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat bg-fixed"
             style="background-image: url('<?= $heroUrl ?>');">
        </div>
        <div class="absolute inset-0 bg-black" style="opacity: <?= $heroOverlay ?>"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <span class="inline-block text-accent text-lg font-medium tracking-[0.2em] uppercase mb-4 animate-on-scroll" data-animation="fade-up">
            A Nossa História
        </span>
        <h1 class="font-cursive text-6xl md:text-7xl lg:text-8xl text-cream mb-6 drop-shadow-lg animate-on-scroll" data-animation="fade-up" data-delay="200">
            A Casa do Gi
        </h1>
        <p class="text-xl md:text-2xl text-cream/90 max-w-3xl mx-auto font-light leading-relaxed animate-on-scroll" data-animation="fade-up" data-delay="400">
            Simplicidade, acolhimento, momentos de convívio marcantes, calor da família, alegria, diversão, gargalhadas e muito amor!
        </p>
    </div>
</section>



<!-- The Story - Asymmetrical Layout -->
<section class="py-20 lg:py-32 bg-cream-50 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center gap-16 lg:gap-24">
            
            <!-- Visual Side (Left) -->
            <div class="w-full lg:w-1/2 relative animate-on-scroll" data-animation="fade-right">
                <!-- Main Image Frame -->
                <div class="relative z-10 w-full aspect-[4/5] max-w-md mx-auto lg:mr-auto lg:ml-0">
                     <div class="absolute inset-0 bg-primary/5 transform translate-x-4 translate-y-4 rounded-sm"></div>
                     <div class="absolute inset-0 border border-primary/10 transform -translate-x-4 -translate-y-4 rounded-sm"></div>
                     <div class="relative h-full w-full bg-white p-4 shadow-2xl transform transition-transform duration-700 hover:rotate-1">
                        <div class="h-full w-full overflow-hidden grayscale hover:grayscale-0 transition-all duration-1000">
                            <img src="<?= asset('images/FotoGi.png') ?>" alt="A Casa do Gi Antigamente" class="w-full h-full object-cover">
                        </div>
                     </div>
                </div>
                
                <!-- Decorative textual element behind -->
                <span class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-[120px] lg:text-[180px] font-cursive text-primary/5 whitespace-nowrap z-0 pointer-events-none select-none">
                    1980
                </span>
            </div>

            <!-- Narrative Side (Right) -->
            <div class="w-full lg:w-1/2 space-y-10 animate-on-scroll" data-animation="fade-left">
                <div class="space-y-4">
                    <h3 class="font-serif text-4xl lg:text-5xl text-primary">
                        As Raízes
                    </h3>
                    <div class="w-20 h-1 bg-accent"></div>
                </div>

                <div class="prose prose-lg text-charcoal/80 font-light space-y-6">
                    <p class="first-letter:text-5xl first-letter:font-serif first-letter:text-accent first-letter:mr-3 first-letter:float-left">
                        Erguida nos anos 80, esta casa conta a história de quem partiu para longe mas nunca esqueceu as suas origens. Foi construída "por carta", tijolo a tijolo, com a saudade e o sonho de um dia regressar a casa.
                    </p>
                    <p>
                        Numa altura em que os materiais escasseavam mas a determinação abundava, a Casa do Gi tornou-se um marco de perseverança em Mogadouro. 
                    </p>
                    <p class="font-medium text-primary">
                        Hoje, mantém a mesma alma: acolhedora, familiar e portas abertas para quem vem por bem.
                    </p>
                </div>

                <!-- Signature / Personal Touch -->
                <div class="pt-6 flex items-center gap-4 opacity-80">
                    <div class="h-px bg-charcoal/20 flex-1"></div>
                    <span class="font-cursive text-2xl text-secondary">A Família Gi</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- The Pillars - Dark Mode Section -->
<section class="py-24 bg-primary text-cream relative overflow-hidden">
    <!-- Background Noise/Texture -->
    <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid md:grid-cols-3 gap-12 lg:gap-16">
            
            <!-- Pillar 1 -->
            <div class="group cursor-default animate-on-scroll" data-delay="100">
                <div class="mb-6 inline-flex items-center justify-center w-16 h-16 rounded-full border border-accent/30 text-accent group-hover:bg-accent group-hover:text-primary transition-all duration-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h4 class="font-serif text-2xl mb-4 text-white group-hover:text-accent transition-colors">Acolhimento</h4>
                <p class="text-cream/70 leading-relaxed font-light border-l border-accent/20 pl-4 group-hover:border-accent transition-colors duration-500">
                    Mais do que hóspedes, recebemos amigos. A hospitalidade transmontana não é uma obrigação, é a nossa forma de estar na vida.
                </p>
            </div>

            <!-- Pillar 2 -->
            <div class="group cursor-default animate-on-scroll" data-delay="200">
                <div class="mb-6 inline-flex items-center justify-center w-16 h-16 rounded-full border border-accent/30 text-accent group-hover:bg-accent group-hover:text-primary transition-all duration-500">
                   <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h4 class="font-serif text-2xl mb-4 text-white group-hover:text-accent transition-colors">Simplicidade</h4>
                <p class="text-cream/70 leading-relaxed font-light border-l border-accent/20 pl-4 group-hover:border-accent transition-colors duration-500">
                    O luxo está nos detalhes simples: no silêncio da noite, no sabor do pão fresco, na conversa sem pressas.
                </p>
            </div>

            <!-- Pillar 3 -->
            <div class="group cursor-default animate-on-scroll" data-delay="300">
                <div class="mb-6 inline-flex items-center justify-center w-16 h-16 rounded-full border border-accent/30 text-accent group-hover:bg-accent group-hover:text-primary transition-all duration-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                </div>
                <h4 class="font-serif text-2xl mb-4 text-white group-hover:text-accent transition-colors">Família</h4>
                <p class="text-cream/70 leading-relaxed font-light border-l border-accent/20 pl-4 group-hover:border-accent transition-colors duration-500">
                    Esta casa foi feita por uma família, para famílias. Um espaço seguro e amplo onde crianças e adultos criam laços.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- Location Section - Magazine Style -->
<section class="pt-20 lg:pt-32 pb-0 bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative">
            <!-- Background Shape -->
            <div class="absolute -top-20 -right-20 w-[500px] h-[500px] bg-cream-100 rounded-full opacity-50 blur-3xl z-0"></div>

            <div class="flex flex-col lg:flex-row items-center">
                <!-- Text Box (Overlapping) -->
                <div class="w-full lg:w-5/12 relative z-20 mb-10 lg:mb-0 lg:-mr-16 order-2 lg:order-1 animate-on-scroll" data-animation="fade-right">
                    <div class="bg-white p-8 md:p-12 shadow-[0_20px_60px_rgba(0,0,0,0.08)] rounded-xl border border-cream-200">
                        <span class="text-secondary text-xs font-bold tracking-[0.2em] uppercase mb-4 block">O Destino</span>
                        <h2 class="font-serif text-4xl text-primary mb-6">Mogadouro,<br>Trás-os-Montes</h2>
                        <p class="text-charcoal/70 mb-8 leading-relaxed font-light">
                            Um território de horizontes vastos, onde o Douro Internacional esculpe a paisagem e a amendoeira em flor pinta a primavera. Aqui, respira-se o ar puro do interior e vive-se ao ritmo da natureza.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="<?= $base ?>/atividades/" class="inline-flex items-center justify-center px-6 py-3 bg-secondary text-white font-medium rounded hover:bg-secondary-700 transition-colors">
                                Explorar Região
                            </a>
                            <a href="<?= $base ?>/contactos/" class="inline-flex items-center justify-center px-6 py-3 border border-charcoal/20 text-primary font-medium rounded hover:bg-charcoal/5 transition-colors">
                                Como Chegar
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Image (Large) -->
                <div class="w-full lg:w-8/12 h-[400px] lg:h-[600px] relative z-10 order-1 lg:order-2 rounded-2xl overflow-hidden shadow-lg animate-on-scroll" data-animation="fade-left">
                     <!-- Using generic scenic placeholder or specific image if exists -->
                     <img src="<?= asset('images/Castelo.jpg') ?>" class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-[2s]" alt="Mogadouro Paisagem">
                     <div class="absolute inset-0 bg-gradient-to-l from-transparent to-black/20"></div>
                </div>
            </div>
        </div>
    </div>
</section>



<?php include INCLUDES_PATH . '/footer.php'; ?>
