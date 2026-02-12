<?php

require_once dirname(__DIR__) . '/includes/init.php';

use Core\Database;
use Core\Language;
use Core\Session;

$lang = Language::getInstance();
$db = Database::getInstance();
$base = basePath();

$casaParam = $_GET['casa'] ?? null;
$showMainPage = ($casaParam === null);

$selectedAccommodationNumber = null;
if ($casaParam !== null) {
    $selectedAccommodationNumber = (int)$casaParam;
    if (!in_array($selectedAccommodationNumber, [1, 2])) {
        $selectedAccommodationNumber = 1;
    }
    Session::set('selected_accommodation', $selectedAccommodationNumber);
}

if ($selectedAccommodationNumber) {
    $accommodation = $db->fetch(
        "SELECT * FROM accommodation WHERE accommodation_number = ? AND is_active = 1",
        [$selectedAccommodationNumber]
    );
} else {

    $accommodation = $db->fetch("SELECT * FROM accommodation WHERE is_active = 1 LIMIT 1");
}

if (!$accommodation) {
    $accommodation = $db->fetch("SELECT * FROM accommodation WHERE is_active = 1 LIMIT 1");
}

$accTranslation = $db->fetch(
    "SELECT * FROM accommodation_translations WHERE accommodation_id = ? AND language_id = ?",
    [$accommodation['id'] ?? 1, $lang->getCurrentLangId()]
);

$mainPageHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = 'accommodation_main'");

$allAccommodations = $db->fetchAll("SELECT * FROM accommodation WHERE is_active = 1 ORDER BY accommodation_number");

function getAccommodationImageUrl($imagePath, $default = '') {
    if (!$imagePath) return $default;
    if (strpos($imagePath, 'uploads/') === 0) {
        return basePath() . '/' . $imagePath;
    }
    return asset($imagePath);
}

$highlightedAmenities = $db->fetchAll(
    "SELECT a.*, at.name, a.category, aa.sort_order FROM amenities a
     JOIN amenity_translations at ON a.id = at.amenity_id
     JOIN accommodation_amenities aa ON a.id = aa.amenity_id
     WHERE aa.accommodation_id = ? AND at.language_id = ? AND aa.is_highlighted = 1
     ORDER BY aa.sort_order
     LIMIT 8",
    [$accommodation['id'], $lang->getCurrentLangId()]
);

$allAmenities = $db->fetchAll(
    "SELECT a.*, at.name, a.category FROM amenities a
     JOIN amenity_translations at ON a.id = at.amenity_id
     JOIN accommodation_amenities aa ON a.id = aa.amenity_id
     WHERE aa.accommodation_id = ? AND at.language_id = ?
     ORDER BY a.category, a.sort_order",
    [$accommodation['id'], $lang->getCurrentLangId()]
);

$amenitiesByCategory = [];
foreach ($allAmenities as $amenity) {
    $cat = $amenity['category'] ?: 'general';
    if (!isset($amenitiesByCategory[$cat])) {
        $amenitiesByCategory[$cat] = [];
    }
    $amenitiesByCategory[$cat][] = $amenity;
}

$categoryLabels = [
    'general' => 'Geral',
    'kitchen' => 'Cozinha',
    'bedroom' => 'Quarto',
    'bathroom' => 'Casa de Banho',
    'outdoor' => 'Exterior',
    'entertainment' => 'Entretenimento',
    'safety' => 'Segurança',
    'children' => 'Crianças',
    'sports' => 'Desporto',
    'services' => 'Serviços'
];

$galleryImages = $db->fetchAll(
    "SELECT * FROM media WHERE category = 'gallery' AND accommodation_id = ? ORDER BY sort_order LIMIT 12",
    [$accommodation['id']]
);

$bedrooms = $db->fetchAll(
    "SELECT b.*, bt.beds_description, bt.name as room_name FROM bedrooms b
     LEFT JOIN bedroom_translations bt ON b.id = bt.bedroom_id AND bt.language_id = ?
     WHERE b.accommodation_id = ?
     ORDER BY b.bedroom_number",
    [$lang->getCurrentLangId(), $accommodation['id']]
);

$bathrooms = $db->fetchAll(
    "SELECT b.*, bt.description, bt.name as bathroom_name FROM bathrooms b
     LEFT JOIN bathroom_translations bt ON b.id = bt.bathroom_id AND bt.language_id = ?
     WHERE b.accommodation_id = ?
     ORDER BY b.bathroom_number",
    [$lang->getCurrentLangId(), $accommodation['id']]
);

$highlightedRules = $db->fetchAll(
    "SELECT hr.*, hrt.rule_text FROM house_rules hr
     JOIN house_rule_translations hrt ON hr.id = hrt.rule_id
     WHERE hr.accommodation_id = ? AND hrt.language_id = ? AND hr.is_highlighted = 1
     ORDER BY hr.sort_order
     LIMIT 3",
    [$accommodation['id'], $lang->getCurrentLangId()]
);

$allRules = $db->fetchAll(
    "SELECT hr.*, hrt.rule_text FROM house_rules hr
     JOIN house_rule_translations hrt ON hr.id = hrt.rule_id
     WHERE hr.accommodation_id = ? AND hrt.language_id = ?
     ORDER BY hr.sort_order",
    [$accommodation['id'], $lang->getCurrentLangId()]
);

$guestreadyUrl = $accommodation['guestready_url'] ?? null;
$bookingUrl = $accommodation['booking_url'] ?? null;
$airbnbUrl = $accommodation['airbnb_url'] ?? null;

$pageTitle = $showMainPage ? 'Alojamento' : 'Casa do Gi ' . $selectedAccommodationNumber;
$pageDescription = 'A Casa do Gi - Alojamento Local em Mogadouro. Casa de férias de 100m² para 6 hóspedes.';

include INCLUDES_PATH . '/header.php';
?>

<?php if ($showMainPage): ?>
<!-- ========================================== -->
<!-- MAIN PAGE: A Casa do Gi - Choose Your Casa -->
<!-- ========================================== -->

<!-- Hero Section - Main -->
<?php
$mainHeroMedia = $mainPageHero ? $db->fetch("SELECT * FROM media WHERE entity_type = 'hero' AND entity_id = ? AND is_cover = 1", [$mainPageHero['id']]) : null;
$mainHeroImage = $mainHeroMedia['file_path'] ?? 'images/MogadouroAlojamento.jpg';
$mainHeroUrl = $mainHeroImage[0] === '/' ? basePath() . $mainHeroImage : asset($mainHeroImage);
$mainHeroOverlay = $mainPageHero['hero_overlay_opacity'] ?? 0.40;
?>
<section class="relative h-screen md:h-[75vh] min-h-[600px] flex items-center bg-primary overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat bg-fixed"
             style="background-image: url('<?= $mainHeroUrl ?>');">
        </div>
        <div class="absolute inset-0 bg-black" style="opacity: <?= $mainHeroOverlay ?>"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-black/40"></div>
    </div>

    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <span class="inline-block text-white/80 text-lg font-medium tracking-[0.2em] uppercase mb-4 animate-on-scroll" data-animation="fade-up">
            <?= content('accommodation_hero_tagline') ?>
        </span>

        <h1 class="font-cursive text-6xl md:text-7xl lg:text-8xl text-cream mb-6 drop-shadow-xl animate-on-scroll" data-animation="fade-up" data-delay="100">
            <?= content('accommodation_hero_title') ?>
        </h1>

        <p class="text-xl md:text-2xl text-cream/90 max-w-2xl mx-auto font-light leading-relaxed animate-on-scroll" data-animation="fade-up" data-delay="200">
            <?= content('accommodation_hero_subtitle') ?>
        </p>
    </div>
</section>

<!-- Choose Your Casa Section -->
<section class="pt-24 pb-0 bg-white relative overflow-hidden" id="choose-casa">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-16 animate-on-scroll" data-animation="fade-up">
            <span class="text-accent text-sm font-bold tracking-[0.2em] uppercase mb-4 block"><?= content('accommodation_section_subtitle') ?></span>
            <h2 class="font-serif text-3xl md:text-5xl text-primary mb-6"><?= content('accommodation_section_title') ?></h2>
            <p class="text-charcoal/70 text-lg max-w-2xl mx-auto">
                <?= content('accommodation_intro') ?>
            </p>
        </div>

        <!-- Casa Selection Cards -->
        <div class="grid md:grid-cols-2 gap-8 lg:gap-12">
            <?php foreach ($allAccommodations as $idx => $casa):

                $coverImage = $casa['cover_image'] ?? ($casa['accommodation_number'] == 1 ? 'images/IgrejaMatriz.jpg' : 'images/Castelo.jpg');
                $coverUrl = getAccommodationImageUrl($coverImage, asset($casa['accommodation_number'] == 1 ? 'images/IgrejaMatriz.jpg' : 'images/Castelo.jpg'));
            ?>
            <a href="?casa=<?= $casa['accommodation_number'] ?>" class="group relative bg-white rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden animate-on-scroll" data-delay="<?= ($idx + 1) * 100 ?>">
                <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110" style="background-image: url('<?= $coverUrl ?>');"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-primary/90 via-primary/40 to-transparent group-hover:from-primary/95 transition-all duration-300"></div>

                <div class="relative h-[500px] flex flex-col justify-end p-8 text-white">
                    <div class="transform transition-transform duration-500 translate-y-4 group-hover:translate-y-0">
                        <span class="text-accent text-xs font-bold tracking-widest uppercase mb-3 block">Alojamento</span>
                        <h3 class="font-cursive text-5xl md:text-6xl mb-4 drop-shadow-lg">Casa do Gi <?= $casa['accommodation_number'] ?></h3>
                        <p class="text-white/80 mb-6 opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100">
                            <?= $casa['accommodation_number'] == 1
                                ? 'Descubra o conforto e a tradição transmontana nesta casa acolhedora, perfeita para famílias e grupos de amigos.'
                                : 'Um espaço único com vista para as paisagens transmontanas, ideal para momentos de descanso e conexão com a natureza.' ?>
                        </p>
                        <div class="flex items-center gap-6 text-sm text-white/70 mb-6">
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <?= $casa['max_guests'] ?? 6 ?> Hóspedes
                            </span>
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <?= $casa['bedrooms'] ?? 3 ?> Quartos
                            </span>
                        </div>
                        <span class="inline-flex items-center text-sm font-bold uppercase tracking-widest text-accent group-hover:text-white transition-colors">
                            Ver Detalhes
                            <svg class="w-5 h-5 ml-2 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Common Features -->
        <div class="mt-20 mb-16 text-center animate-on-scroll" data-animation="fade-up" data-delay="300">
            <h3 class="font-serif text-2xl text-primary mb-8"><?= content('accommodation_features_title') ?></h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="flex flex-col items-center gap-3 p-4">
                    <div class="w-14 h-14 rounded-full bg-cream-100 flex items-center justify-center text-secondary">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>
                    </div>
                    <span class="text-sm font-medium text-charcoal"><?= content('accommodation_feature_1') ?></span>
                </div>
                <div class="flex flex-col items-center gap-3 p-4">
                    <div class="w-14 h-14 rounded-full bg-cream-100 flex items-center justify-center text-secondary">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </div>
                    <span class="text-sm font-medium text-charcoal"><?= content('accommodation_feature_2') ?></span>
                </div>
                <div class="flex flex-col items-center gap-3 p-4">
                    <div class="w-14 h-14 rounded-full bg-cream-100 flex items-center justify-center text-secondary">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    </div>
                    <span class="text-sm font-medium text-charcoal"><?= content('accommodation_feature_3') ?></span>
                </div>
                <div class="flex flex-col items-center gap-3 p-4">
                    <div class="w-14 h-14 rounded-full bg-cream-100 flex items-center justify-center text-secondary">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <span class="text-sm font-medium text-charcoal"><?= content('accommodation_feature_4') ?></span>
                </div>
            </div>
        </div>
    </div>
</section>

<?php else: ?>
<!-- ========================================== -->
<!-- SPECIFIC CASA PAGE: Casa do Gi 1 or 2     -->
<!-- ========================================== -->

<!-- Hero Section -->
<?php

$casaHeroImage = $accommodation['hero_image'] ?? 'images/MogadouroAlojamento.jpg';
$casaHeroUrl = getAccommodationImageUrl($casaHeroImage, asset('images/MogadouroAlojamento.jpg'));
?>
<section class="relative h-screen md:h-[75vh] min-h-[600px] flex items-center bg-primary overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat bg-fixed"
             style="background-image: url('<?= $casaHeroUrl ?>');">
        </div>
        <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/30 to-black/60"></div>
    </div>

    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <!-- Rating Badge -->
        <?php if (!empty($accommodation['rating'])): ?>
        <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-5 py-2.5 rounded-full mb-8 animate-on-scroll" data-animation="fade-up" data-delay="50">
            <div class="flex items-center gap-1.5 text-accent">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span class="font-bold text-white text-lg"><?= number_format($accommodation['rating'], 1) ?></span>
            </div>
            <?php if (!empty($accommodation['reviews_count'])): ?>
            <div class="h-4 w-px bg-white/30 mx-1"></div>
            <span class="text-white/90 text-sm font-medium tracking-wide uppercase"><?= $accommodation['reviews_count'] ?> Avaliações</span>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <h1 class="font-cursive text-6xl md:text-7xl lg:text-8xl text-cream mb-8 drop-shadow-lg animate-on-scroll" data-animation="fade-up" data-delay="100">
            Casa do Gi <?= $selectedAccommodationNumber ?>
        </h1>

        <!-- Location -->
        <div class="flex items-center justify-center gap-2 text-cream/80 animate-on-scroll" data-animation="fade-up" data-delay="300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="tracking-widest uppercase text-sm font-medium"><?= e($accommodation['city'] ?? 'Mogadouro') ?>, <?= e($accommodation['region'] ?? 'Trás-os-Montes') ?></span>
        </div>

        <!-- Casa Switcher -->
        <div class="flex flex-col md:flex-row items-center justify-center gap-4 mt-10 animate-on-scroll" data-animation="fade-up" data-delay="400">
            <a href="?casa=1"
               class="inline-flex items-center justify-center px-10 py-4 backdrop-blur-md <?= $selectedAccommodationNumber === 1 ? 'bg-white/95 text-charcoal border-2 border-white shadow-xl scale-105' : 'bg-white/10 text-white border border-white/30 hover:bg-white/20 hover:border-white/50 hover:scale-105' ?> font-medium tracking-widest uppercase text-xs rounded-full transition-all duration-300 shadow-lg hover:shadow-2xl cursor-pointer min-w-[180px]">
                Casa do Gi 1
            </a>
            <a href="?casa=2"
               class="inline-flex items-center justify-center px-10 py-4 backdrop-blur-md <?= $selectedAccommodationNumber === 2 ? 'bg-white/95 text-charcoal border-2 border-white shadow-xl scale-105' : 'bg-white/10 text-white border border-white/30 hover:bg-white/20 hover:border-white/50 hover:scale-105' ?> font-medium tracking-widest uppercase text-xs rounded-full transition-all duration-300 shadow-lg hover:shadow-2xl cursor-pointer min-w-[180px]">
                Casa do Gi 2
            </a>
        </div>

        <!-- Back Link -->
        <div class="mt-8 animate-on-scroll" data-animation="fade-up" data-delay="500">
            <a href="<?= $base ?>/alojamento/" class="inline-flex items-center gap-2 text-white/60 hover:text-white transition-colors group">
                <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span class="text-sm font-medium">Ver Todas as Casas</span>
            </a>
        </div>
    </div>
</section>

<!-- Quick Info Bar -->
<section class="bg-white py-12 border-b border-cream-100">
    <div class="px-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-3xl shadow-[0_15px_40px_rgba(0,0,0,0.05)] border border-cream-200 p-6 md:p-8 animate-on-scroll" data-animation="fade-up" data-delay="400">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-0 lg:divide-x lg:divide-secondary/10">

                <!-- Guests -->
                <div class="flex items-center justify-center gap-4 lg:px-4 group cursor-default">
                    <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center transition-transform duration-300 group-hover:scale-110 group-hover:bg-accent/20">
                        <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-3xl font-serif font-bold text-primary leading-none transition-colors"><?= $accommodation['max_guests'] ?? 6 ?></span>
                        <span class="text-xs font-semibold text-charcoal/50 uppercase tracking-widest mt-1">Hóspedes</span>
                    </div>
                </div>

                <!-- Bedrooms -->
                <div class="flex items-center justify-center gap-4 lg:px-4 group cursor-default">
                    <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center transition-transform duration-300 group-hover:scale-110 group-hover:bg-accent/20">
                        <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 18v3m16-3v3M4 18h16M4 13h16v5H4v-5zM5 13V8a2 2 0 012-2h10a2 2 0 012 2v5M8 10h3M13 10h3"/>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-3xl font-serif font-bold text-primary leading-none transition-colors"><?= $accommodation['bedrooms'] ?? 3 ?></span>
                        <span class="text-xs font-semibold text-charcoal/50 uppercase tracking-widest mt-1">Quartos</span>
                    </div>
                </div>

                <!-- Bathrooms -->
                <div class="flex items-center justify-center gap-4 lg:px-4 group cursor-default">
                     <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center transition-transform duration-300 group-hover:scale-110 group-hover:bg-accent/20">
                        <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 2v4 M4 6h16l-2 5h-12L4 6z M8 14v6 M12 14v8 M16 14v6"/>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-3xl font-serif font-bold text-primary leading-none transition-colors"><?= $accommodation['bathrooms'] ?? 2 ?></span>
                        <span class="text-xs font-semibold text-charcoal/50 uppercase tracking-widest mt-1">Casa de Banho</span>
                    </div>
                </div>

                <!-- Area -->
                <div class="flex items-center justify-center gap-4 lg:px-4 group cursor-default">
                    <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center transition-transform duration-300 group-hover:scale-110 group-hover:bg-accent/20">
                        <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-3xl font-serif font-bold text-primary leading-none transition-colors"><?= (int)($accommodation['area_sqm'] ?? 100) ?></span>
                        <span class="text-xs font-semibold text-charcoal/50 uppercase tracking-widest mt-1">m² Área</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Main Content Section -->
<section class="py-20 lg:py-28 bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid lg:grid-cols-12 gap-12 lg:gap-20">
            <!-- Left Column: Details & Experience -->
            <div class="lg:col-span-8 space-y-16">

                <!-- 1. The Experience (Description) -->
                <div class="animate-on-scroll" data-animation="fade-up">
                    <span class="text-accent font-bold tracking-[0.2em] uppercase text-xs block mb-4">A Experiência</span>
                    <h2 class="font-serif text-3xl md:text-5xl text-primary mb-8 !leading-[1.2]" style="line-height: 1.2 !important;">
                        Um refúgio de <span class="italic text-accent">charme</span> e tranquilidade.
                    </h2>
                    <div class="prose prose-lg prose-charcoal max-w-none font-light leading-relaxed text-charcoal/80">
                        <?= $accTranslation['description'] ?? '<p>A Casa do Gi é sinónimo de simplicidade, acolhimento e momentos de convívio marcantes.</p>' ?>
                    </div>
                </div>

                <!-- 2. Highlights (Icons) -->
                 <div class="grid grid-cols-1 md:grid-cols-3 gap-6 py-8 border-y border-cream-200 animate-on-scroll" data-animation="fade-up" data-delay="100">
                    <div class="flex items-center gap-4 group">
                        <div class="w-12 h-12 rounded-full bg-cream-50 border border-cream-200 flex items-center justify-center text-secondary group-hover:scale-110 group-hover:bg-secondary group-hover:text-white transition-all duration-300">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        </div>
                        <span class="font-medium text-primary text-sm tracking-wide">Check-in autónomo</span>
                    </div>
                    <div class="flex items-center gap-4 group">
                        <div class="w-12 h-12 rounded-full bg-cream-50 border border-cream-200 flex items-center justify-center text-secondary group-hover:scale-110 group-hover:bg-secondary group-hover:text-white transition-all duration-300">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <span class="font-medium text-primary text-sm tracking-wide">máx. <?= $accommodation['max_guests'] ?? 6 ?> hóspedes</span>
                    </div>
                     <div class="flex items-center gap-4 group">
                        <div class="w-12 h-12 rounded-full bg-cream-50 border border-cream-200 flex items-center justify-center text-secondary group-hover:scale-110 group-hover:bg-secondary group-hover:text-white transition-all duration-300">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 6h12a1 1 0 011 1v2a1 1 0 01-1 1H6a1 1 0 01-1-1V7a1 1 0 011-1zM5 11h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1v-2a1 1 0 011-1zM4 16h16a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2a1 1 0 011-1z"/></svg>
                        </div>
                        <span class="font-medium text-primary text-sm tracking-wide">Toalhas e lençóis</span>
                    </div>
                </div>

                <!-- 3. Spaces (Detailed List - from database) -->
                <?php if (!empty($bedrooms) || !empty($bathrooms)): ?>
                <div class="animate-on-scroll" data-animation="fade-up">
                    <h3 class="font-serif text-2xl text-primary mb-8">Os Espaços</h3>
                    <div class="bg-cream-50 rounded-2xl p-8 border border-cream-100">
                        <div class="grid md:grid-cols-2 gap-x-12 gap-y-10">
                            <!-- Dormidas -->
                            <?php if (!empty($bedrooms)): ?>
                            <div class="space-y-6">
                                <h4 class="text-xs font-bold uppercase tracking-[0.2em] text-accent mb-6 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                                    Dormidas
                                </h4>
                                <ul class="space-y-5">
                                    <?php foreach ($bedrooms as $bedroom): ?>
                                    <li class="flex items-start gap-3">
                                        <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2 flex-shrink-0"></div>
                                        <div>
                                            <span class="block font-semibold text-primary"><?= e($bedroom['room_name'] ?? 'Quarto ' . $bedroom['bedroom_number']) ?></span>
                                            <span class="text-charcoal/70 text-sm font-light"><?= e($bedroom['beds_description'] ?? 'Cama') ?></span>
                                        </div>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>

                            <!-- Higiene -->
                            <?php if (!empty($bathrooms)): ?>
                            <div class="space-y-6">
                                <h4 class="text-xs font-bold uppercase tracking-[0.2em] text-accent mb-6 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 2v4 M4 6h16l-2 5h-12L4 6z M8 14v6 M12 14v8 M16 14v6"/></svg>
                                    Higiene
                                </h4>
                                <ul class="space-y-5">
                                    <?php foreach ($bathrooms as $bathroom): ?>
                                    <li class="flex items-start gap-3">
                                        <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2 flex-shrink-0"></div>
                                        <div>
                                            <span class="block font-semibold text-primary"><?= e($bathroom['bathroom_name'] ?? 'Casa de banho ' . $bathroom['bathroom_number']) ?></span>
                                            <span class="text-charcoal/70 text-sm font-light"><?= e($bathroom['description'] ?? 'Sanita, chuveiro') ?></span>
                                        </div>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- 4. Amenities Highlight (Only highlighted amenities) -->
                <div class="animate-on-scroll" data-animation="fade-up">
                    <div class="flex items-end justify-between mb-8 border-b border-cream-200 pb-4">
                         <h3 class="font-serif text-2xl text-primary">Comodidades</h3>
                         <?php if (count($allAmenities) > count($highlightedAmenities)): ?>
                         <button onclick="openAmenitiesModal()" class="text-secondary hover:text-primary transition-colors text-sm font-medium tracking-wide uppercase border-b border-secondary/30 pb-0.5 hover:border-primary">
                             Ver Tudo (<?= count($allAmenities) ?>)
                         </button>
                         <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <?php
                        $displayAmenities = !empty($highlightedAmenities) ? $highlightedAmenities : array_slice($allAmenities, 0, 8);
                        foreach ($displayAmenities as $am):
                        ?>
                        <div class="flex items-center gap-3 p-4 bg-white rounded-xl border border-cream-100 hover:border-accent/30 transition-all duration-300 hover:shadow-md group">
                            <div class="w-2 h-2 rounded-full bg-cream-200 group-hover:bg-accent transition-colors flex-shrink-0"></div>
                            <span class="text-sm text-charcoal-600 font-medium group-hover:text-primary transition-colors"><?= e($am['name']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

            <!-- Right Column: Booking Sidebar (Sticky) -->
            <div class="lg:col-span-4 space-y-6">

                <!-- Booking Card -->
                <div class="bg-white rounded-3xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] p-8 border border-cream-200 sticky top-32 animate-on-scroll" data-animation="fade-left">
                    <div class="text-center mb-8">
                        <span class="text-accent text-xs font-bold tracking-widest uppercase mb-2 block">Disponibilidade</span>
                        <h3 class="font-serif text-3xl text-primary">Reserve Já</h3>
                        <p class="text-charcoal/60 text-sm mt-2">Escolha a sua plataforma preferida</p>
                    </div>

                    <div class="space-y-4">
                        <?php if ($guestreadyUrl): ?>
                        <a href="<?= e($guestreadyUrl) ?>" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between p-4 rounded-xl border border-cream-200 bg-white hover:bg-[#800020]/10 hover:border-[#800020] group transition-all duration-300 shadow-sm hover:shadow-lg relative overflow-hidden">
                            <div class="flex items-center gap-4 relative z-10">
                                <div class="w-10 h-10 bg-cream-50 rounded-lg flex items-center justify-center p-1.5 group-hover:bg-white transition-colors">
                                     <img src="<?= asset('images/guestreadylogo.png') ?>" alt="GuestReady" class="w-full h-full object-contain transition-all">
                                </div>
                                <span class="font-semibold text-primary group-hover:text-[#800020] transition-colors">GuestReady</span>
                            </div>
                            <svg class="w-5 h-5 text-cream-300 group-hover:text-[#800020] relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        <?php endif; ?>

                        <?php if ($bookingUrl): ?>
                        <a href="<?= e($bookingUrl) ?>" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between p-4 rounded-xl border border-cream-200 bg-white hover:bg-[#003580]/10 hover:border-[#003580] group transition-all duration-300 shadow-sm hover:shadow-lg relative overflow-hidden">
                             <div class="flex items-center gap-4 relative z-10">
                                <div class="w-10 h-10 bg-cream-50 rounded-lg flex items-center justify-center p-1.5 group-hover:bg-white transition-colors">
                                     <img src="<?= asset('images/bookinglogo.jpg') ?>" alt="Booking" class="w-full h-full object-contain mix-blend-multiply group-hover:mix-blend-normal transition-all">
                                </div>
                                <span class="font-semibold text-primary group-hover:text-[#003580] transition-colors">Booking.com</span>
                            </div>
                            <svg class="w-5 h-5 text-cream-300 group-hover:text-[#003580] relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        <?php endif; ?>

                        <?php if ($airbnbUrl): ?>
                        <a href="<?= e($airbnbUrl) ?>" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between p-4 rounded-xl border border-cream-200 bg-white hover:bg-[#FF385C]/10 hover:border-[#FF385C] group transition-all duration-300 shadow-sm hover:shadow-lg relative overflow-hidden">
                             <div class="flex items-center gap-4 relative z-10">
                                <div class="w-10 h-10 bg-cream-50 rounded-lg flex items-center justify-center p-1.5 group-hover:bg-white transition-colors">
                                      <img src="<?= asset('images/airbnblogo.png') ?>" alt="Airbnb" class="w-full h-full object-contain transition-all">
                                </div>
                                <span class="font-semibold text-primary group-hover:text-[#FF385C] transition-colors">Airbnb</span>
                            </div>
                            <svg class="w-5 h-5 text-cream-300 group-hover:text-[#FF385C] relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        <?php endif; ?>
                    </div>

                     <!-- Need Help CTA -->
                    <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                        <p class="text-charcoal/60 text-sm mb-4">Tem alguma dúvida ou pedido especial?</p>
                        <a href="<?= $base . ($lang->isEnglish() ? '/en/contact/' : '/contactos/') ?>" class="inline-flex items-center justify-center w-full px-6 py-3 border-2 border-primary text-primary font-medium rounded-xl hover:bg-primary hover:text-white transition-all duration-300 group">
                            <span class="mr-2">Entrar em Contacto</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </a>
                    </div>

                    <!-- Essential Info Summary -->
                    <div class="mt-8 pt-6 border-t border-gray-100 bg-cream-50/50 -mx-8 -mb-8 p-8 rounded-b-3xl">
                        <h4 class="font-serif text-sm font-bold text-primary mb-4 uppercase tracking-widest">Check-in / Out</h4>
                        <div class="flex justify-between items-center mb-4">
                            <div class="text-center">
                                <span class="block text-xs uppercase text-charcoal/50 font-bold tracking-wide mb-1">Entrada</span>
                                <span class="font-serif text-xl text-primary"><?= substr($accommodation['check_in_time'] ?? '16:00', 0, 5) ?></span>
                            </div>
                            <div class="h-8 w-px bg-cream-300"></div>
                            <div class="text-center">
                                <span class="block text-xs uppercase text-charcoal/50 font-bold tracking-wide mb-1">Saída</span>
                                <span class="font-serif text-xl text-primary"><?= substr($accommodation['check_out_time'] ?? '11:00', 0, 5) ?></span>
                            </div>
                        </div>
                         <div class="text-center">
                            <span class="text-[10px] uppercase text-charcoal/40 tracking-widest">Licença <?= $accommodation['license_number'] ?? '146729/AL' ?></span>
                         </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

<!-- Gallery Section -->
<section class="py-16 pt-24 bg-cream-50 overflow-hidden" id="gallery-section">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-14 animate-on-scroll">
            <span class="text-accent text-sm font-medium tracking-[0.2em] uppercase mb-3 block">Interiores</span>
            <h2 class="font-serif text-3xl md:text-4xl text-primary mb-4">
                Galeria de Imagens
            </h2>
            <p class="text-charcoal/70 max-w-2xl leading-relaxed">
                Um olhar por dentro da Casa do Gi <?= $selectedAccommodationNumber ?>, onde cada detalhe conta uma história.
            </p>
        </div>

        <?php if (!empty($galleryImages)): ?>
        <div class="grid grid-cols-2 md:grid-cols-4 md:grid-rows-2 gap-3 md:gap-4 h-auto md:h-[550px] animate-on-scroll" data-delay="200">
            <!-- Main Image -->
            <?php if (isset($galleryImages[0])): ?>
            <div class="col-span-2 row-span-2 relative group rounded-2xl overflow-hidden cursor-pointer shadow-lg" onclick="openLightbox(0)">
                <img src="<?= upload(str_replace('uploads/', '', $galleryImages[0]['file_path'])) ?>"
                     alt="<?= e($lang->getCurrentLang() === 'pt' ? ($galleryImages[0]['alt_text_pt'] ?? 'Alojamento') : ($galleryImages[0]['alt_text_en'] ?? 'Accommodation')) ?>"
                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="absolute bottom-4 right-4 w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                    </svg>
                </div>
            </div>
            <?php endif; ?>

            <?php for ($i = 1; $i <= 4; $i++): ?>
            <?php if (isset($galleryImages[$i])): ?>
            <div class="relative group rounded-2xl overflow-hidden cursor-pointer shadow-lg aspect-[4/3] md:aspect-auto" onclick="openLightbox(<?= $i ?>)">
                <img src="<?= upload(str_replace('uploads/', '', $galleryImages[$i]['file_path'])) ?>"
                     alt="<?= e($lang->getCurrentLang() === 'pt' ? ($galleryImages[$i]['alt_text_pt'] ?? 'Alojamento') : ($galleryImages[$i]['alt_text_en'] ?? 'Accommodation')) ?>"
                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>

                <?php if ($i === 4 && count($galleryImages) > 5): ?>
                <div class="absolute inset-0 bg-primary/70 group-hover:bg-primary/80 transition-colors flex flex-col items-center justify-center text-white">
                    <span class="font-serif text-3xl font-bold">+<?= count($galleryImages) - 5 ?></span>
                    <span class="text-sm">mais fotos</span>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php endfor; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-12 text-charcoal/50 bg-cream-50 rounded-2xl">
            <p>Galeria de imagens brevemente disponível.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Location & Policy Combined Section -->
<section class="relative py-20 bg-white overflow-hidden">
    <!-- Top Wave Divider -->
    <div class="absolute top-0 left-0 w-full overflow-hidden leading-[0]">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none" class="relative block w-full h-[60px] text-cream-50 fill-current">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"></path>
        </svg>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-start">

            <!-- Left: Location & Activities -->
            <div class="space-y-8 animate-on-scroll" data-animation="fade-right">
                <div>
                    <span class="text-accent text-sm font-medium tracking-[0.2em] uppercase mb-3 block">O Destino</span>
                    <h2 class="font-serif text-3xl md:text-4xl text-primary mb-4">
                        <?= e($accTranslation['activity_section_title'] ?? 'Mogadouro & Envolvência') ?>
                    </h2>
                    <div class="prose prose-charcoal text-charcoal/80 leading-relaxed mb-8">
                        <?= $accTranslation['activity_section_description'] ?? 'Mogadouro é uma vila histórica no coração do Planalto Mirandês, onde a tradição se funde com a natureza. A partir da Casa do Gi, poderá explorar o Castelo de Mogadouro, percorrer trilhos no Parque Natural do Douro Internacional e saborear a gastronomia local única.' ?>
                    </div>
                     <div class="flex items-center gap-3 text-charcoal/80 font-medium">
                        <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span><?= e($accommodation['city'] ?? 'Mogadouro') ?>, Portugal</span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <a href="<?= $base ?>/atividades/" class="inline-flex items-center justify-center gap-2 bg-secondary text-white px-8 py-4 rounded-full hover:bg-secondary-700 transition-all shadow-lg hover:shadow-xl hover:-translate-y-1">
                        <span class="font-medium tracking-wide">Descobrir Atividades</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>

                    <button onclick="document.getElementById('map-section-title').scrollIntoView({behavior: 'smooth'})" class="inline-flex items-center justify-center gap-2 border border-secondary text-secondary px-8 py-4 rounded-full hover:bg-secondary hover:text-white transition-all">
                        <span class="font-medium tracking-wide">Ver no Mapa</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    </button>
                </div>
            </div>

            <!-- Right: Cancellation & Policies -->
            <div class="bg-cream-50 rounded-3xl p-8 md:p-10 border border-cream-100 animate-on-scroll" data-animation="fade-left">
                <h3 class="font-serif text-2xl text-primary mb-6">Políticas da Estadia</h3>

                <div class="space-y-6">
                    <!-- Cancellation -->
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 rounded-full bg-cream-200 flex items-center justify-center">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h4 class="font-bold text-primary">Cancelamento</h4>
                        </div>
                        <p class="text-charcoal/70 text-sm leading-relaxed pl-11">
                            <?= e($accTranslation['cancellation_policy'] ?? 'Cancelamento gratuito até 30 dias antes do check-in. Cancelamentos após este período sujeitos a taxas de acordo com a plataforma de reserva.') ?>
                        </p>
                    </div>

                    <?php if (!empty($accTranslation['refund_policy'])): ?>
                    <div class="h-px bg-cream-200 w-full"></div>

                    <!-- Refund Policy -->
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 rounded-full bg-cream-200 flex items-center justify-center">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </div>
                            <h4 class="font-bold text-primary">Reembolso</h4>
                        </div>
                        <p class="text-charcoal/70 text-sm leading-relaxed pl-11">
                            <?= e($accTranslation['refund_policy']) ?>
                        </p>
                    </div>
                    <?php endif; ?>

                    <div class="h-px bg-cream-200 w-full"></div>

                    <!-- House Rules Summary -->
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 rounded-full bg-cream-200 flex items-center justify-center">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h4 class="font-bold text-primary">Regras da Casa</h4>
                        </div>
                        <ul class="space-y-2 pl-11 text-sm text-charcoal/70">
                            <?php if (!empty($highlightedRules)): ?>
                                <?php foreach ($highlightedRules as $rule): ?>
                                <li>• <?= e($rule['rule_text']) ?></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>• Não são permitidas festas ou eventos.</li>
                                <li>• Horário de silêncio: 22h00 - 08h00.</li>
                                <li>• Proibido fumar no interior.</li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <?php if (count($allRules) > count($highlightedRules)): ?>
                    <div class="mt-6 pt-6">
                         <button onclick="openPoliciesModal()" class="text-secondary hover:text-primary transition-colors text-sm font-medium border-b border-secondary/30 pb-0.5 hover:border-primary">
                             Ler todas as regras (<?= count($allRules) ?>)
                         </button>
                    </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>
</section>

<!-- Map Section -->
<section class="relative pt-20 pb-16 bg-cream-50">
    <div class="absolute top-0 left-0 w-full overflow-hidden leading-[0]">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none" class="relative block w-full h-[60px] text-white fill-current">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"></path>
        </svg>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16 animate-on-scroll" id="map-section-title">
            <span class="text-accent text-sm font-medium tracking-[0.2em] uppercase mb-3 inline-block">Localização</span>
            <h2 class="font-serif text-3xl md:text-4xl text-primary mb-4">Onde Estamos</h2>
            <p class="text-charcoal/70 max-w-2xl mx-auto">
                Visite-nos em Mogadouro, no coração de Trás-os-Montes.
            </p>
        </div>

        <div class="animate-on-scroll" data-delay="200">
            <div class="relative rounded-2xl overflow-hidden shadow-xl border border-gray-100">
                <div id="contact-map" class="w-full h-[400px] md:h-[450px]"></div>

                <!-- Map Card -->
                <div class="absolute bottom-4 left-4 right-4 md:right-auto md:max-w-sm bg-white/95 backdrop-blur-sm rounded-xl shadow-lg p-5 z-[5000]">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-secondary/10 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-primary text-lg">A Casa do Gi <?= $selectedAccommodationNumber ?></h3>
                            <p class="text-charcoal/70 text-sm mt-1">5200-207 Mogadouro</p>
                            <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $accommodation['latitude'] ?? '41.34217' ?>,<?= $accommodation['longitude'] ?? '-6.71347' ?>"
                               target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center text-secondary hover:text-secondary-700 text-sm font-medium mt-3 group">
                                Obter direções
                                <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Lightbox Modal -->
<div id="lightbox-modal" class="fixed inset-0 z-[100] bg-black/95 hidden opacity-0 transition-opacity duration-300 flex flex-col">
    <div class="flex items-center justify-between px-4 md:px-8 py-4 bg-gradient-to-b from-black/50 to-transparent">
        <div class="text-white/80 text-sm font-medium">
            <span id="lightbox-counter">1</span> / <span id="lightbox-total"><?= count($galleryImages) ?></span>
        </div>
        <div class="absolute left-1/2 -translate-x-1/2 text-white font-medium text-lg text-center px-4 max-w-[60%] truncate hidden md:block" id="lightbox-title"></div>
        <button onclick="closeLightbox()" class="text-white/60 hover:text-white transition-colors p-2 hover:bg-white/10 rounded-full">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <div class="md:hidden text-white/90 text-center px-4 py-2 font-medium" id="lightbox-title-mobile"></div>

    <div class="flex-1 flex items-center justify-center relative px-4 md:px-20">
        <button onclick="prevImage()" class="absolute left-2 md:left-6 text-white/50 hover:text-white transition-all z-[101] p-2 hover:bg-white/10 rounded-full">
            <svg class="w-8 h-8 md:w-12 md:h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <div class="relative max-w-full max-h-[60vh] md:max-h-[70vh]">
            <img id="lightbox-image" src="" class="max-w-full max-h-[60vh] md:max-h-[70vh] object-contain rounded-lg shadow-2xl transition-opacity duration-300">
            <div id="lightbox-loader" class="absolute inset-0 flex items-center justify-center hidden">
                <div class="w-10 h-10 border-4 border-white/20 border-t-white rounded-full animate-spin"></div>
            </div>
        </div>

        <button onclick="nextImage()" class="absolute right-2 md:right-6 text-white/50 hover:text-white transition-all z-[101] p-2 hover:bg-white/10 rounded-full">
            <svg class="w-8 h-8 md:w-12 md:h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>

    <div class="bg-gradient-to-t from-black/70 to-transparent py-4 md:py-6">
        <div class="max-w-4xl mx-auto px-4">
            <div id="thumbnail-carousel" class="flex items-center justify-center gap-2 md:gap-3 overflow-x-auto py-4 scrollbar-hide">
                <?php foreach ($galleryImages as $index => $image): ?>
                <button onclick="goToImage(<?= $index ?>)"
                        class="thumbnail-item flex-shrink-0 w-16 h-12 md:w-20 md:h-14 rounded-lg overflow-hidden border-2 transition-all duration-300 relative <?= $index === 0 ? 'border-accent opacity-100 scale-110 z-20 shadow-lg' : 'border-transparent opacity-50 z-0 hover:opacity-80' ?>"
                        data-index="<?= $index ?>">
                    <img src="<?= upload(str_replace('uploads/', '', $image['file_path'])) ?>" alt="" class="w-full h-full object-cover">
                </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- All Amenities Modal -->
<div id="amenities-modal" class="fixed inset-0 z-[100] bg-black/50 hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] overflow-hidden" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-serif text-xl text-primary">Todas as Comodidades</h3>
            <button onclick="closeAmenitiesModal()" class="text-charcoal/40 hover:text-charcoal transition-colors p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(80vh-80px)]">
            <?php foreach ($amenitiesByCategory as $category => $catAmenities): ?>
            <div class="mb-6 last:mb-0">
                <h4 class="text-sm font-semibold text-charcoal/60 uppercase tracking-wider mb-3"><?= $categoryLabels[$category] ?? ucfirst($category) ?></h4>
                <div class="grid grid-cols-2 gap-3">
                    <?php foreach ($catAmenities as $amenity): ?>
                    <div class="flex items-center gap-3 p-2">
                        <svg class="w-5 h-5 text-secondary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-sm text-charcoal"><?= e($amenity['name']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Policies Modal (House Rules) -->
<div id="policies-modal" class="fixed inset-0 z-[100] bg-black/50 hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] overflow-hidden" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-serif text-xl text-primary">Regras da Casa</h3>
            <button onclick="closePoliciesModal()" class="text-charcoal/40 hover:text-charcoal transition-colors p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(80vh-80px)]">
            <ul class="space-y-3">
                <?php foreach ($allRules as $rule): ?>
                <li class="flex items-start gap-3 p-3 bg-cream-50 rounded-lg">
                    <svg class="w-5 h-5 text-secondary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm text-charcoal"><?= e($rule['rule_text']) ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<!-- Leaflet CSS/JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
// Gallery Data
const galleryImages = <?= json_encode(array_map(function($img) use ($lang) {
    $path = str_replace('uploads/', '', $img['file_path']);
    $url = empty($path) ? asset('images/placeholder.jpg') : upload($path);
    $alt = $lang->getCurrentLang() === 'pt'
        ? ($img['alt_text_pt'] ?? '')
        : ($img['alt_text_en'] ?? '');
    return ['url' => $url, 'alt' => $alt];
}, $galleryImages)) ?>;

let currentImageIndex = 0;
const totalImages = galleryImages.length;

// Lightbox Functions
function openLightbox(index) {
    currentImageIndex = index;
    updateLightboxImage();
    updateThumbnails();
    updateCounter();
    const modal = document.getElementById('lightbox-modal');
    modal.classList.remove('hidden');
    setTimeout(() => modal.classList.remove('opacity-0'), 10);
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const modal = document.getElementById('lightbox-modal');
    modal.classList.add('opacity-0');
    setTimeout(() => modal.classList.add('hidden'), 300);
    document.body.style.overflow = '';
}

function updateLightboxImage() {
    const img = document.getElementById('lightbox-image');
    const loader = document.getElementById('lightbox-loader');
    const titleEl = document.getElementById('lightbox-title');
    const titleMobileEl = document.getElementById('lightbox-title-mobile');

    loader.classList.remove('hidden');
    img.style.opacity = '0';

    const currentImg = galleryImages[currentImageIndex];

    if (titleEl) titleEl.textContent = currentImg.alt;
    if (titleMobileEl) titleMobileEl.textContent = currentImg.alt;

    const newImg = new Image();
    newImg.onload = function() {
        img.src = currentImg.url;
        loader.classList.add('hidden');
        img.style.opacity = '1';
    };
    newImg.src = currentImg.url;
}

function updateThumbnails() {
    document.querySelectorAll('.thumbnail-item').forEach((thumb, index) => {
        if (index === currentImageIndex) {
            thumb.classList.add('border-accent', 'opacity-100', 'scale-110');
            thumb.classList.remove('border-transparent', 'opacity-50');
            thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        } else {
            thumb.classList.remove('border-accent', 'opacity-100', 'scale-110');
            thumb.classList.add('border-transparent', 'opacity-50');
        }
    });
}

function updateCounter() {
    document.getElementById('lightbox-counter').textContent = currentImageIndex + 1;
}

function goToImage(index) {
    currentImageIndex = index;
    updateLightboxImage();
    updateThumbnails();
    updateCounter();
}

function nextImage() {
    currentImageIndex = (currentImageIndex + 1) % totalImages;
    updateLightboxImage();
    updateThumbnails();
    updateCounter();
}

function prevImage() {
    currentImageIndex = (currentImageIndex - 1 + totalImages) % totalImages;
    updateLightboxImage();
    updateThumbnails();
    updateCounter();
}

// Amenities Modal
function openAmenitiesModal() {
    const modal = document.getElementById('amenities-modal');
    modal.classList.remove('hidden');
    setTimeout(() => modal.classList.remove('opacity-0'), 10);
    document.body.style.overflow = 'hidden';
}

function closeAmenitiesModal() {
    const modal = document.getElementById('amenities-modal');
    modal.classList.add('opacity-0');
    setTimeout(() => modal.classList.add('hidden'), 300);
    document.body.style.overflow = '';
}

// Policies Modal
function openPoliciesModal() {
    const modal = document.getElementById('policies-modal');
    modal.classList.remove('hidden');
    setTimeout(() => modal.classList.remove('opacity-0'), 10);
    document.body.style.overflow = 'hidden';
}

function closePoliciesModal() {
    const modal = document.getElementById('policies-modal');
    modal.classList.add('opacity-0');
    setTimeout(() => modal.classList.add('hidden'), 300);
    document.body.style.overflow = '';
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    const lightbox = document.getElementById('lightbox-modal');
    const amenitiesModal = document.getElementById('amenities-modal');
    const policiesModal = document.getElementById('policies-modal');

    if (e.key === 'Escape') {
        if (!lightbox.classList.contains('hidden')) closeLightbox();
        if (!amenitiesModal.classList.contains('hidden')) closeAmenitiesModal();
        if (!policiesModal.classList.contains('hidden')) closePoliciesModal();
    }
    if (!lightbox.classList.contains('hidden')) {
        if (e.key === 'ArrowRight') nextImage();
        if (e.key === 'ArrowLeft') prevImage();
    }
});

// Touch support
let touchStartX = 0;
document.getElementById('lightbox-modal').addEventListener('touchstart', e => touchStartX = e.changedTouches[0].screenX, { passive: true });
document.getElementById('lightbox-modal').addEventListener('touchend', e => {
    const diff = touchStartX - e.changedTouches[0].screenX;
    if (Math.abs(diff) > 50) diff > 0 ? nextImage() : prevImage();
}, { passive: true });

// Close modals on backdrop click
document.getElementById('amenities-modal').addEventListener('click', function(e) {
    if (e.target === this) closeAmenitiesModal();
});
document.getElementById('policies-modal').addEventListener('click', function(e) {
    if (e.target === this) closePoliciesModal();
});

// Map
document.addEventListener('DOMContentLoaded', function() {
    const lat = <?= $accommodation['latitude'] ?? 41.34217 ?>;
    const lng = <?= $accommodation['longitude'] ?? -6.71347 ?>;

    const map = L.map('contact-map', { scrollWheelZoom: false }).setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const customIcon = L.divIcon({
        className: 'custom-map-marker',
        html: `<div style="width:40px;height:40px;background:linear-gradient(135deg,#264653 0%,#1d3a47 100%);border-radius:50% 50% 50% 0;transform:rotate(-45deg);box-shadow:0 4px 12px rgba(38,70,83,0.4);display:flex;align-items:center;justify-content:center;"><svg style="transform:rotate(45deg);width:20px;height:20px;color:#C5A059;" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg></div>`,
        iconSize: [40, 40],
        iconAnchor: [20, 40],
        popupAnchor: [0, -40]
    });

    L.marker([lat, lng], { icon: customIcon }).addTo(map)
        .bindPopup('<div style="text-align:center;padding:8px;"><strong style="color:#264653;">A Casa do Gi ' + <?= $selectedAccommodationNumber ?> + '</strong><br><span style="color:#2D3748;font-size:12px;">5200-207 Mogadouro</span></div>');

    map.on('click', () => map.scrollWheelZoom.enable());
    map.on('mouseout', () => map.scrollWheelZoom.disable());
});
</script>

<style>
.custom-popup .leaflet-popup-content-wrapper { border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
.custom-popup .leaflet-popup-tip { background: white; }
.leaflet-control-attribution { font-size: 10px; background: rgba(255,255,255,0.8) !important; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
.scrollbar-hide::-webkit-scrollbar { display: none; }
.thumbnail-item { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.thumbnail-item:hover { transform: scale(1.05); }
.thumbnail-item.scale-110 { transform: scale(1.1); z-index: 10; }
#lightbox-image { transition: opacity 0.3s ease-in-out; }
</style>

<?php endif; ?>

<style>
footer { margin-top: 0 !important; }
</style>

<script>
    // Simple, Robust Scroll Lock (Matching Header Logic)
    document.addEventListener('DOMContentLoaded', () => {
        const modalIds = ['lightbox-modal', 'amenities-modal', 'policies-modal', 'bookingModal'];

        const observer = new MutationObserver(() => {
            let isLocked = false;

            // Check visibility of any tracked modal
            modalIds.forEach(id => {
                const el = document.getElementById(id);
                if (el && !el.classList.contains('hidden')) {
                    isLocked = true;
                }
            });

            if (isLocked) {
                document.body.style.overflow = 'hidden';
                document.documentElement.style.overflow = 'hidden';
            } else {
                 // Only unlock if mobile menu is closed
                 const mobileMenu = document.getElementById('mobile-menu');
                 if (!mobileMenu || !mobileMenu.classList.contains('open')) {
                    document.body.style.overflow = '';
                    document.documentElement.style.overflow = '';
                 }
            }
        });

        // Loop to attach observers
        modalIds.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                observer.observe(el, {
                    attributes: true,
                    attributeFilter: ['class', 'style', 'hidden']
                });
            }
        });
    });
</script>

<?php include INCLUDES_PATH . '/footer.php'; ?>
