<?php
/**
 * A Casa do Gi - Homepage
 */

require_once __DIR__ . '/includes/init.php';

use Core\Database;

$lang = \Core\Language::getInstance();
$db = Database::getInstance();
$base = basePath();
$isEnglish = $lang->isEnglish();
$content = $lang->getPageContents('home');

// Hero image
$pageHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = 'home' AND is_active = 1");
$heroMedia = $pageHero ? $db->fetch("SELECT * FROM media WHERE entity_type = 'hero' AND entity_id = ? AND is_cover = 1", [$pageHero['id']]) : null;
$heroImage = $heroMedia['file_path'] ?? 'images/MogadouroAtividades.jpg';
$heroOverlay = $pageHero['hero_overlay_opacity'] ?? 0.30;
$heroUrl = $heroImage[0] === '/' ? basePath() . $heroImage : asset($heroImage);

// Data
$langId = $isEnglish ? 2 : 1;

$featuredProducts = $db->fetchAll("
    SELECT p.*, pt.name, pt.short_description,
           (SELECT m.file_path FROM product_images pi JOIN media m ON m.id = pi.media_id WHERE pi.product_id = p.id AND pi.is_primary = 1 LIMIT 1) as image_path
    FROM products p
    JOIN product_translations pt ON pt.product_id = p.id AND pt.language_id = ?
    WHERE p.is_active = 1 AND p.is_featured = 1
    ORDER BY p.sort_order ASC, p.created_at DESC
    LIMIT 4
", [$langId]);

$featuredActivities = $db->fetchAll("
    SELECT a.*, at.title, at.short_description,
           (SELECT m.file_path FROM media m WHERE m.entity_type = 'activity' AND m.entity_id = a.id AND m.is_cover = 1 LIMIT 1) as cover_image
    FROM activities a
    JOIN activity_translations at ON at.activity_id = a.id AND at.language_id = ?
    WHERE a.is_active = 1 AND a.is_featured = 1
    ORDER BY a.sort_order ASC
    LIMIT 4
", [$langId]);

$pageTitle = 'Inicio';
$pageDescription = 'A Casa do Gi - Alojamento Local e Produtos Regionais em Mogadouro.';
$bodyClass = 'homepage-new';

include INCLUDES_PATH . '/header.php';

$categoryLabels = [
    'nature' => $isEnglish ? 'Nature' : 'Natureza',
    'culture' => $isEnglish ? 'Culture' : 'Cultura',
    'gastronomy' => $isEnglish ? 'Gastronomy' : 'Gastronomia',
    'adventure' => $isEnglish ? 'Adventure' : 'Aventura',
    'wellness' => $isEnglish ? 'Wellness' : 'Bem-estar',
    'events' => $isEnglish ? 'Events' : 'Eventos',
];
?>

<!-- HERO -->
<section class="relative h-screen w-full overflow-hidden" id="mountain-hero" style="background-image: url('<?= $heroUrl ?>'); background-size: cover; background-position: center; background-attachment: fixed;">
    <div class="absolute inset-0 bg-black" style="opacity: <?= $heroOverlay ?>"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-black/60"></div>
    <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-6 z-10">
        <div id="hero-content">
            <span class="text-white/70 text-sm md:text-base font-bold tracking-[0.4em] uppercase mb-6 block animate-fade-in-up">
                Tras-os-Montes, Portugal
            </span>
            <h1 class="font-cursive text-6xl md:text-8xl lg:text-9xl text-cream mb-6 drop-shadow-2xl animate-fade-in-up animation-delay-200">
                A Casa do Gi
            </h1>
            <p class="text-cream/80 text-lg md:text-xl font-light max-w-2xl mx-auto animate-fade-in-up animation-delay-400">
                <?= $isEnglish ? 'Where Transmontana tradition meets modern comfort' : 'Onde a tradicao transmontana encontra o conforto moderno' ?>
            </p>
        </div>
    </div>
</section>

<!-- SPLIT HERO -->
<div class="relative h-screen w-full flex flex-col md:flex-row overflow-hidden" id="split-hero">
    <div class="split-hero-left relative w-full md:w-1/2 h-1/2 md:h-full group overflow-hidden">
        <img src="<?= asset('images/IgrejaMatriz.jpg') ?>" alt="Igreja Matriz de Mogadouro" class="absolute inset-0 w-full h-full object-cover transition-transform duration-[2000ms] ease-out will-change-transform group-hover:scale-105">
        <div class="absolute inset-0 bg-primary/40 group-hover:bg-primary/10 transition-colors duration-700"></div>
        <div class="split-content-left absolute inset-0 flex flex-col items-center justify-center text-center p-8 z-10">
            <span class="text-white/80 text-sm font-bold tracking-[0.5em] uppercase mb-10 opacity-0 translate-y-8 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-500 delay-100 block group-hover:tracking-[0.8em] group-hover:text-accent">
                <?= $isEnglish ? 'Welcome to the' : 'Bem-vindo ao' ?>
            </span>
            <h2 class="font-cursive text-5xl md:text-7xl lg:text-8xl text-cream mb-8 drop-shadow-2xl transition-transform duration-500 group-hover:-translate-y-2 group-hover:text-white">
                <?= $isEnglish ? 'Refuge' : 'Refugio' ?>
            </h2>
            <div class="opacity-0 translate-y-8 transition-all duration-500 group-hover:opacity-100 group-hover:translate-y-0 delay-200">
                <a href="<?= $base ?>/alojamento/" class="inline-flex items-center justify-center px-10 py-4 backdrop-blur-md bg-white/10 border border-white/30 text-white font-medium tracking-widest uppercase text-xs rounded-full transition-all duration-300 hover:bg-white hover:text-primary shadow-xl hover:shadow-2xl hover:scale-105">
                    <?= $isEnglish ? 'View Accommodation' : 'Ver Alojamento' ?>
                </a>
            </div>
        </div>
    </div>
    <div class="split-center-logo absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-30 pointer-events-none hidden md:block">
        <div class="relative w-48 h-48 flex items-center justify-center">
            <div class="absolute w-full h-full animate-[spin_10s_linear_infinite] opacity-90">
               <svg viewBox="0 0 100 100" width="100%" height="100%">
                  <defs><path id="circle" d="M 50, 50 m -37, 0 a 37,37 0 1,1 74,0 a 37,37 0 1,1 -74,0"/></defs>
                  <text font-size="11" font-weight="bold" letter-spacing="2" fill="#FDFBF7" font-family="monospace">
                    <textPath xlink:href="#circle">A CASA DO GI &bull; MOGADOURO &bull;</textPath>
                  </text>
                </svg>
            </div>
            <div class="absolute w-24 h-24 bg-primary/20 backdrop-blur-sm rounded-full border border-cream/30 flex items-center justify-center shadow-2xl">
                <span class="font-cursive text-4xl text-cream pt-2">Gi</span>
            </div>
        </div>
    </div>
    <div class="split-hero-right relative w-full md:w-1/2 h-1/2 md:h-full group overflow-hidden">
        <img src="<?= asset('images/Castelo.jpg') ?>" alt="Castelo de Mogadouro" class="absolute inset-0 w-full h-full object-cover transition-transform duration-[2000ms] ease-out will-change-transform group-hover:scale-105">
        <div class="absolute inset-0 bg-black/40 group-hover:bg-black/10 transition-colors duration-700"></div>
        <div class="split-content-right absolute inset-0 flex flex-col items-center justify-center text-center p-8 z-10">
            <span class="text-white/80 text-sm font-bold tracking-[0.5em] uppercase mb-10 opacity-0 translate-y-8 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-500 delay-100 block group-hover:tracking-[0.8em] group-hover:text-accent">
                <?= $isEnglish ? 'Discover the' : 'Descubra a' ?>
            </span>
            <h2 class="font-cursive text-5xl md:text-7xl lg:text-8xl text-cream mb-8 drop-shadow-2xl transition-transform duration-500 group-hover:-translate-y-2 group-hover:text-white">
                <?= $isEnglish ? 'Tradition' : 'Tradicao' ?>
            </h2>
            <div class="flex flex-col md:flex-row gap-4 opacity-0 translate-y-8 transition-all duration-500 group-hover:opacity-100 group-hover:translate-y-0 delay-200">
                <a href="<?= $base ?>/atividades/" class="inline-flex items-center justify-center px-8 py-3 bg-secondary/80 text-white font-medium tracking-widest uppercase text-xs rounded-full hover:bg-secondary hover:scale-105 transition-all duration-300 shadow-lg">
                    <?= $isEnglish ? 'Explore' : 'Explorar' ?>
                </a>
                <a href="<?= $base ?>/loja/" class="inline-flex items-center justify-center px-8 py-3 bg-white/10 border border-white/30 text-white font-medium tracking-widest uppercase text-xs rounded-full hover:bg-white hover:text-primary transition-all duration-300 shadow-lg">
                    <?= $isEnglish ? 'Shop' : 'Loja' ?>
                </a>
            </div>
        </div>
    </div>
</div>


<!-- ====== PAGE BODY ====== -->

<!-- BENTO GRID - 3 Experiences -->
<section class="bg-primary">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
        <div class="text-center mb-14 animate-on-scroll">
            <span class="text-accent/80 text-xs font-bold tracking-[0.3em] uppercase block mb-4"><?= $isEnglish ? 'Discover' : 'Descubra' ?></span>
            <h2 class="font-serif text-3xl md:text-5xl text-cream leading-snug">
                <?= $isEnglish
                    ? 'More than a place to sleep,<br><span class="italic text-accent">a place to feel.</span>'
                    : 'Mais do que um lugar para dormir,<br><span class="italic text-accent">um lugar para sentir.</span>' ?>
            </h2>
        </div>

        <!-- Bento: 1 large left + 2 stacked right -->
        <div class="grid md:grid-cols-5 gap-4 md:gap-5">
            <!-- LARGE CARD: Alojamento -->
            <a href="<?= $base ?>/alojamento/" class="group relative md:col-span-3 rounded-2xl overflow-hidden min-h-[400px] md:min-h-[550px] animate-on-scroll" data-delay="0">
                <img src="<?= asset('images/MogadouroAlojamento.jpg') ?>" alt="Alojamento" class="absolute inset-0 w-full h-full object-cover transition-transform duration-[1500ms] ease-out group-hover:scale-105" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-8 md:p-10 z-10">
                    <span class="text-accent text-xs font-bold tracking-[0.2em] uppercase block mb-3"><?= $isEnglish ? 'Rest' : 'Descanso' ?></span>
                    <h3 class="font-serif text-3xl md:text-4xl text-white mb-3"><?= $isEnglish ? 'Accommodation' : 'Alojamento' ?></h3>
                    <p class="text-white/60 text-sm max-w-md leading-relaxed mb-5 hidden md:block">
                        <?= $isEnglish ? 'Cozy rooms where tradition meets modern comfort. Wake up to the serenity of Tras-os-Montes.' : 'Quartos acolhedores onde a tradicao encontra o conforto. Acorde com a serenidade de Tras-os-Montes.' ?>
                    </p>
                    <span class="inline-flex items-center gap-2 text-white text-sm font-semibold group-hover:text-accent transition-colors">
                        <?= $isEnglish ? 'View Rooms' : 'Ver Quartos' ?>
                        <svg class="w-4 h-4 group-hover:translate-x-1.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </div>
            </a>

            <!-- 2 STACKED CARDS -->
            <div class="md:col-span-2 grid gap-4 md:gap-5">
                <!-- Atividades -->
                <a href="<?= $base ?>/atividades/" class="group relative rounded-2xl overflow-hidden min-h-[200px] md:min-h-0 animate-on-scroll" data-delay="100">
                    <img src="<?= asset('images/MogadouroAtividades.jpg') ?>" alt="Atividades" class="absolute inset-0 w-full h-full object-cover transition-transform duration-[1500ms] ease-out group-hover:scale-105" loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-6 md:p-8 z-10">
                        <span class="text-accent text-xs font-bold tracking-[0.2em] uppercase block mb-2"><?= $isEnglish ? 'Nature' : 'Natureza' ?></span>
                        <h3 class="font-serif text-2xl text-white mb-2"><?= $isEnglish ? 'Activities' : 'Atividades' ?></h3>
                        <span class="inline-flex items-center gap-2 text-white/80 text-sm font-medium group-hover:text-accent transition-colors">
                            <?= $isEnglish ? 'Explore' : 'Explorar' ?>
                            <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </span>
                    </div>
                </a>
                <!-- Loja -->
                <a href="<?= $base ?>/loja/" class="group relative rounded-2xl overflow-hidden min-h-[200px] md:min-h-0 animate-on-scroll" data-delay="200">
                    <img src="<?= asset('images/MogadouroContacto.jpg') ?>" alt="Loja Regional" class="absolute inset-0 w-full h-full object-cover transition-transform duration-[1500ms] ease-out group-hover:scale-105" loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-6 md:p-8 z-10">
                        <span class="text-accent text-xs font-bold tracking-[0.2em] uppercase block mb-2"><?= $isEnglish ? 'Flavours' : 'Sabores' ?></span>
                        <h3 class="font-serif text-2xl text-white mb-2"><?= $isEnglish ? 'Regional Shop' : 'Loja Regional' ?></h3>
                        <span class="inline-flex items-center gap-2 text-white/80 text-sm font-medium group-hover:text-accent transition-colors">
                            <?= $isEnglish ? 'Visit Shop' : 'Visitar Loja' ?>
                            <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>


<!-- STATS STRIP -->
<section class="bg-cream border-y border-accent/10">
    <div class="max-w-6xl mx-auto px-4 py-14">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-4 text-center">
            <div class="animate-on-scroll" data-delay="0">
                <div class="font-cursive text-4xl md:text-5xl text-primary mb-1">700+</div>
                <span class="text-charcoal/50 text-xs uppercase tracking-widest"><?= $isEnglish ? 'Years of History' : 'Anos de Historia' ?></span>
            </div>
            <div class="animate-on-scroll" data-delay="100">
                <div class="font-cursive text-4xl md:text-5xl text-primary mb-1">85km</div>
                <span class="text-charcoal/50 text-xs uppercase tracking-widest"><?= $isEnglish ? 'Douro Cliffs' : 'Arribas do Douro' ?></span>
            </div>
            <div class="animate-on-scroll" data-delay="200">
                <div class="font-cursive text-4xl md:text-5xl text-primary mb-1">DOP</div>
                <span class="text-charcoal/50 text-xs uppercase tracking-widest"><?= $isEnglish ? 'Certified Olive Oil' : 'Azeite Certificado' ?></span>
            </div>
            <div class="animate-on-scroll" data-delay="300">
                <div class="font-cursive text-4xl md:text-5xl text-primary mb-1">5<span class="text-accent">&#9733;</span></div>
                <span class="text-charcoal/50 text-xs uppercase tracking-widest"><?= $isEnglish ? 'Guest Reviews' : 'Avaliacoes' ?></span>
            </div>
        </div>
    </div>
</section>


<!-- REGION: Full-bleed image + floating text card -->
<section class="relative overflow-hidden">
    <div class="grid lg:grid-cols-2 min-h-[650px]">
        <!-- Image half -->
        <div class="relative min-h-[350px] lg:min-h-0 animate-on-scroll" data-animation="fade-right">
            <img src="<?= asset('images/MogadouroNeve.jpeg') ?>" alt="Mogadouro" class="absolute inset-0 w-full h-full object-cover" loading="lazy">
            <div class="absolute inset-0 bg-primary/20 lg:bg-transparent"></div>
        </div>
        <!-- Content half with accent background -->
        <div class="relative bg-primary flex items-center">
            <!-- Decorative oversized text -->
            <div class="absolute top-6 right-8 font-cursive text-[120px] md:text-[180px] text-white/[0.03] leading-none select-none pointer-events-none hidden lg:block">Gi</div>
            <div class="relative z-10 px-8 md:px-16 py-16 animate-on-scroll" data-animation="fade-left">
                <span class="text-accent text-xs font-bold tracking-[0.3em] uppercase block mb-5"><?= $isEnglish ? 'The Region' : 'A Regiao' ?></span>
                <h2 class="font-serif text-3xl md:text-4xl text-cream leading-snug mb-6">
                    <?= $isEnglish ? 'Mogadouro, land of memories.' : 'Mogadouro, terra de memorias.' ?>
                </h2>
                <p class="text-cream/60 leading-relaxed mb-6">
                    <?= $isEnglish
                        ? 'Between the International Douro and the Transmontana plateau, discover a region where nature, history and gastronomy intertwine in a unique way.'
                        : 'Entre o Douro Internacional e o planalto transmontano, descubra uma regiao onde a natureza, a historia e a gastronomia se entrecruzam de forma unica.' ?>
                </p>
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center gap-3 text-cream/80 text-sm">
                        <svg class="w-4 h-4 text-accent flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <?= $isEnglish ? 'Transmontano DOP Olive Oil' : 'Azeite Transmontano DOP' ?>
                    </li>
                    <li class="flex items-center gap-3 text-cream/80 text-sm">
                        <svg class="w-4 h-4 text-accent flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <?= $isEnglish ? 'Rosemary Honey & Artisanal Sausages' : 'Mel de Rosmaninho & Enchidos Artesanais' ?>
                    </li>
                    <li class="flex items-center gap-3 text-cream/80 text-sm">
                        <svg class="w-4 h-4 text-accent flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <?= $isEnglish ? 'International Douro Natural Park' : 'Parque Natural do Douro Internacional' ?>
                    </li>
                </ul>
                <a href="<?= $base ?>/sobre-nos/" class="inline-flex items-center gap-2 px-6 py-3 bg-accent text-primary text-sm font-bold rounded-full hover:bg-white transition-all group">
                    <span><?= $isEnglish ? 'Know More' : 'Saber Mais' ?></span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>


<!-- PRODUCTS -->
<?php if (!empty($featuredProducts)): ?>
<section class="py-20 md:py-28 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-12">
            <div class="animate-on-scroll">
                <span class="text-accent text-xs font-bold tracking-[0.3em] uppercase block mb-3"><?= $isEnglish ? 'From Our Land' : 'Da Nossa Terra' ?></span>
                <h2 class="font-serif text-3xl md:text-4xl text-primary"><?= $isEnglish ? 'Regional Products' : 'Produtos Regionais' ?></h2>
            </div>
            <a href="<?= $base ?>/loja/" class="hidden sm:inline-flex items-center gap-2 text-primary text-sm font-semibold hover:text-accent transition-colors animate-on-scroll">
                <span><?= $isEnglish ? 'View All' : 'Ver Todos' ?></span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 md:gap-7">
            <?php foreach ($featuredProducts as $i => $product):
                $imgPath = $product['image_path'] ? $base . $product['image_path'] : asset('images/MogadouroContacto.jpg');
                $productUrl = $base . '/loja/produto/' . e($product['slug']) . '/';
                $hasDiscount = $product['sale_price'] && $product['sale_price'] < $product['price'];
            ?>
            <a href="<?= $productUrl ?>" class="group animate-on-scroll" data-delay="<?= $i * 100 ?>">
                <div class="relative aspect-[3/4] rounded-2xl overflow-hidden mb-4 bg-cream-50">
                    <img src="<?= $imgPath ?>" alt="<?= e($product['name']) ?>"
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
                    <?php if ($hasDiscount): ?>
                    <span class="absolute top-3 left-3 px-2.5 py-1 bg-terracotta text-white text-[10px] font-bold rounded-full">
                        -<?= round((1 - $product['sale_price'] / $product['price']) * 100) ?>%
                    </span>
                    <?php endif; ?>
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors duration-300 rounded-2xl"></div>
                </div>
                <h3 class="font-serif text-base text-primary group-hover:text-accent transition-colors mb-1 line-clamp-1"><?= e($product['name']) ?></h3>
                <div>
                    <?php if ($hasDiscount): ?>
                        <span class="text-charcoal/40 line-through text-xs mr-1"><?= formatPrice($product['price']) ?></span>
                        <span class="text-accent font-bold"><?= formatPrice($product['sale_price']) ?></span>
                    <?php else: ?>
                        <span class="text-accent font-bold"><?= formatPrice($product['price']) ?></span>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-12 sm:hidden">
            <a href="<?= $base ?>/loja/" class="inline-flex items-center gap-2 px-6 py-3 border border-primary text-primary text-sm font-medium rounded-full hover:bg-primary hover:text-white transition-all">
                <?= $isEnglish ? 'View All Products' : 'Ver Todos os Produtos' ?>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- PARALLAX QUOTE BANNER -->
<section class="relative py-24 md:py-32 overflow-hidden" style="background-image: url('<?= asset('images/MogadouroNeve2.jpeg') ?>'); background-size: cover; background-position: center; background-attachment: fixed;">
    <div class="absolute inset-0 bg-primary/75"></div>
    <div class="max-w-3xl mx-auto px-6 text-center relative z-10 animate-on-scroll">
        <div class="flex justify-center gap-1.5 mb-8">
            <?php for ($s = 0; $s < 5; $s++): ?>
            <svg class="w-5 h-5 text-accent fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            <?php endfor; ?>
        </div>
        <blockquote class="font-serif text-xl md:text-3xl lg:text-4xl text-white italic leading-relaxed mb-8">
            <?= $isEnglish
                ? '"A place where time stops and the soul breathes."'
                : '"Um lugar onde o tempo para e a alma respira."' ?>
        </blockquote>
        <div class="flex items-center justify-center gap-3">
            <div class="w-8 h-px bg-accent"></div>
            <span class="text-cream/50 text-xs uppercase tracking-[0.3em]">A Casa do Gi</span>
            <div class="w-8 h-px bg-accent"></div>
        </div>
    </div>
</section>


<!-- ACTIVITIES -->
<?php if (!empty($featuredActivities)): ?>
<section class="py-20 md:py-28 bg-cream-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-12">
            <div class="animate-on-scroll">
                <span class="text-accent text-xs font-bold tracking-[0.3em] uppercase block mb-3"><?= $isEnglish ? 'Explore' : 'Explore' ?></span>
                <h2 class="font-serif text-3xl md:text-4xl text-primary"><?= $isEnglish ? 'Activities & Experiences' : 'Atividades & Experiencias' ?></h2>
            </div>
            <a href="<?= $base ?>/atividades/" class="hidden sm:inline-flex items-center gap-2 text-primary text-sm font-semibold hover:text-accent transition-colors animate-on-scroll">
                <span><?= $isEnglish ? 'View All' : 'Ver Todas' ?></span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <!-- Bento: 1st large, rest smaller -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php foreach ($featuredActivities as $i => $act):
                $actImage = !empty($act['cover_image']) ? $base . $act['cover_image'] : asset('images/MogadouroAtividades.jpg');
                $actUrl = $base . '/atividades/' . e($act['slug']) . '/';
                $catLabel = $categoryLabels[$act['category']] ?? $act['category'];
                $isFirst = ($i === 0);
            ?>
            <a href="<?= $actUrl ?>" class="group relative rounded-2xl overflow-hidden <?= $isFirst ? 'md:col-span-2 lg:col-span-2 md:row-span-2 min-h-[300px] md:min-h-[500px]' : 'min-h-[220px]' ?> animate-on-scroll" data-delay="<?= $i * 100 ?>">
                <img src="<?= $actImage ?>" alt="<?= e($act['title']) ?>"
                     class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>
                <div class="absolute top-4 left-4 z-10">
                    <span class="px-3 py-1 bg-white/20 backdrop-blur-sm text-white text-[10px] font-bold rounded-full uppercase tracking-wider"><?= $catLabel ?></span>
                </div>
                <?php if ($act['distance_km']): ?>
                <div class="absolute top-4 right-4 z-10">
                    <span class="px-2.5 py-1 bg-white/90 text-primary text-[10px] font-bold rounded-full"><?= $act['distance_km'] ?> km</span>
                </div>
                <?php endif; ?>
                <div class="absolute bottom-0 left-0 right-0 p-6 z-10">
                    <h3 class="font-serif <?= $isFirst ? 'text-2xl md:text-3xl' : 'text-lg md:text-xl' ?> text-white mb-1"><?= e($act['title']) ?></h3>
                    <?php if ($isFirst && $act['short_description']): ?>
                    <p class="text-white/60 text-sm leading-relaxed line-clamp-2 max-w-md"><?= e($act['short_description']) ?></p>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-12 sm:hidden">
            <a href="<?= $base ?>/atividades/" class="inline-flex items-center gap-2 px-6 py-3 border border-primary text-primary text-sm font-medium rounded-full hover:bg-primary hover:text-white transition-all">
                <?= $isEnglish ? 'View All Activities' : 'Ver Todas as Atividades' ?>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>




<style>
@keyframes fade-in-up {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fade-in-up 1s ease-out forwards; opacity: 0; }
.animation-delay-200 { animation-delay: 0.2s; }
.animation-delay-400 { animation-delay: 0.4s; }

.split-hero-left {
    opacity: 0; transform: translateX(-80px);
    transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}
.split-hero-right {
    opacity: 0; transform: translateX(80px);
    transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    transition-delay: 0.15s;
}
.split-center-logo {
    opacity: 0; transform: translate(-50%, -50%) scale(0.5);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    transition-delay: 0.3s;
}
.split-content-left, .split-content-right {
    opacity: 0; transform: translateY(20px);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    transition-delay: 0.4s;
}
#split-hero.in-view .split-hero-left,
#split-hero.in-view .split-hero-right { opacity: 1; transform: translateX(0); }
#split-hero.in-view .split-center-logo { opacity: 1; transform: translate(-50%, -50%) scale(1); }
#split-hero.in-view .split-content-left,
#split-hero.in-view .split-content-right { opacity: 1; transform: translateY(0); }

@media (max-width: 768px) {
    .split-hero-left { transform: translateY(-40px); }
    .split-hero-right { transform: translateY(40px); transition-delay: 0.2s; }
    #split-hero.in-view .split-hero-left,
    #split-hero.in-view .split-hero-right { transform: translateY(0); }
    #mountain-hero { background-attachment: scroll !important; }
}

#hero-content { will-change: transform, opacity; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Split hero - triggers earlier (threshold 0.05)
    const splitHero = document.getElementById('split-hero');
    if (splitHero) {
        new IntersectionObserver(function(entries) {
            entries.forEach(function(e) { if (e.isIntersecting) e.target.classList.add('in-view'); });
        }, { threshold: 0.05 }).observe(splitHero);
    }

    // Hero parallax fade
    var heroContent = document.getElementById('hero-content');
    var mountainHero = document.getElementById('mountain-hero');
    if (heroContent && mountainHero) {
        var ticking = false;
        window.addEventListener('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(function() {
                    var scrollY = window.scrollY;
                    var h = mountainHero.offsetHeight;
                    if (scrollY < h) {
                        var p = scrollY / h;
                        heroContent.style.transform = 'translateY(' + (scrollY * 0.35) + 'px)';
                        heroContent.style.opacity = Math.max(0, 1 - p * 1.5);
                    }
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });
    }
});
</script>

<?php include INCLUDES_PATH . '/footer.php'; ?>
