<?php
/**
 * A Casa do Gi - Activities / Tourist Guide Page
 * Complete system with Bento Grid, Slug pages, and External Links
 */

require_once dirname(__DIR__) . '/includes/init.php';

use Core\Language;
use Core\Database;

$lang = Language::getInstance();
$db = Database::getInstance();
$base = basePath();
$currentLangId = $lang->getCurrentLangId();

// Check if viewing a specific activity (slug system)
$slug = $_GET['slug'] ?? null;
$viewingActivity = !empty($slug);

// Get hero image from database
$pageHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = 'activities' AND is_active = 1");
$heroMedia = $pageHero ? $db->fetch("SELECT * FROM media WHERE entity_type = 'hero' AND entity_id = ? AND is_cover = 1", [$pageHero['id']]) : null;
$heroImage = $heroMedia['file_path'] ?? 'images/MogadouroAtividades.jpg';
$heroOverlay = $pageHero['hero_overlay_opacity'] ?? 0.40;

// Build hero URL (file_path from media already has leading slash)
$heroUrl = $heroImage[0] === '/' ? basePath() . $heroImage : asset($heroImage);

// Get all categories for filter
$categories = [
    'all' => ['label' => $lang->getCurrentLang() === 'pt' ? 'Todas' : 'All', 'icon' => 'grid'],
    'nature' => ['label' => $lang->getCurrentLang() === 'pt' ? 'Natureza' : 'Nature', 'icon' => 'tree'],
    'culture' => ['label' => $lang->getCurrentLang() === 'pt' ? 'Cultura' : 'Culture', 'icon' => 'landmark'],
    'gastronomy' => ['label' => $lang->getCurrentLang() === 'pt' ? 'Gastronomia' : 'Gastronomy', 'icon' => 'utensils'],
    'restaurants' => ['label' => $lang->getCurrentLang() === 'pt' ? 'Restaurantes' : 'Restaurants', 'icon' => 'utensils-crossed'],
    'cafes' => ['label' => $lang->getCurrentLang() === 'pt' ? 'Cafés' : 'Cafés', 'icon' => 'coffee'],
    'architecture' => ['label' => $lang->getCurrentLang() === 'pt' ? 'Arquitetura' : 'Architecture', 'icon' => 'building'],
    'adventure' => ['label' => $lang->getCurrentLang() === 'pt' ? 'Aventura' : 'Adventure', 'icon' => 'compass'],
    'events' => ['label' => $lang->getCurrentLang() === 'pt' ? 'Eventos' : 'Events', 'icon' => 'calendar'],
];

// If viewing specific activity
if ($viewingActivity) {
    $activity = $db->fetch(
        "SELECT a.*, at.title, at.short_description, at.full_description, at.tips, at.address_description
         FROM activities a
         JOIN activity_translations at ON a.id = at.activity_id
         WHERE a.slug = ? AND a.is_active = 1 AND at.language_id = ?",
        [$slug, $currentLangId]
    );

    if (!$activity) {
        // Activity not found, redirect to main page
        header('Location: ' . $base . '/atividades/');
        exit;
    }

    // Increment views
    $db->query("UPDATE activities SET views_count = views_count + 1 WHERE id = ?", [$activity['id']]);

    // Get activity images from media table
    $activityImages = $db->fetchAll(
        "SELECT * FROM media WHERE entity_type = 'activity' AND entity_id = ? ORDER BY is_cover DESC, sort_order",
        [$activity['id']]
    );

    // Get cover image from media
    $coverImage = null;
    foreach ($activityImages as $img) {
        if ($img['is_cover']) {
            $coverImage = $img;
            break;
        }
    }
    if (!$coverImage && !empty($activityImages)) {
        $coverImage = $activityImages[0];
    }

    $pageTitle = $activity['title'] . ' - O Que Fazer em Mogadouro';
    $pageDescription = $activity['short_description'] ?? 'Descubra ' . $activity['title'] . ' em Mogadouro.';

} else {
    // Main activities page
    $pageTitle = $lang->getCurrentLang() === 'pt' ? 'O Que Fazer em Mogadouro' : 'What to Do in Mogadouro';
    $pageDescription = $lang->getCurrentLang() === 'pt'
        ? 'Descubra as melhores atividades e atrações turísticas em Mogadouro e Trás-os-Montes. Natureza, gastronomia, história e cultura.'
        : 'Discover the best activities and tourist attractions in Mogadouro and Trás-os-Montes. Nature, gastronomy, history and culture.';

    // Get all active activities
    $activities = $db->fetchAll(
        "SELECT a.*, at.title, at.short_description,
                (SELECT file_path FROM media WHERE entity_type = 'activity' AND entity_id = a.id AND is_cover = 1 LIMIT 1) as cover_img
         FROM activities a
         JOIN activity_translations at ON a.id = at.activity_id
         WHERE a.is_active = 1 AND at.language_id = ?
         ORDER BY a.is_featured DESC, a.sort_order, a.id",
        [$currentLangId]
    );

    // Get external links
    $externalLinks = $db->fetchAll(
        "SELECT el.*, elt.title, elt.description
         FROM external_links el
         JOIN external_link_translations elt ON el.id = elt.link_id
         WHERE el.is_active = 1 AND elt.language_id = ?
         ORDER BY el.is_featured DESC, el.sort_order",
        [$currentLangId]
    );
}

include INCLUDES_PATH . '/header.php';
?>

<?php if ($viewingActivity): ?>
<!-- ================================================== -->
<!-- SINGLE ACTIVITY PAGE (SLUG VIEW)                   -->
<!-- ================================================== -->

<!-- Simplified Hero with Breadcrumb -->
<section class="relative h-[50vh] min-h-[400px] flex items-end bg-primary overflow-hidden">
    <div class="absolute inset-0">
        <?php if ($coverImage): ?>
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat"
             style="background-image: url('<?= !empty($coverImage['file_path']) ? $base . $coverImage['file_path'] : asset('images/placeholder-activity.jpg') ?>');">
        </div>
        <?php else: ?>
        <div class="absolute inset-0 bg-gradient-to-br from-secondary/80 to-primary/90"></div>
        <?php endif; ?>
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/20"></div>
    </div>

    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12 z-10">
        <!-- Breadcrumb -->
        <nav class="mb-6 animate-on-scroll" data-animation="fade-up">
            <ol class="flex items-center gap-2 text-sm text-white/70">
                <li>
                    <a href="<?= $base ?>/" class="hover:text-white transition-colors">
                        <?= $lang->getCurrentLang() === 'pt' ? 'Início' : 'Home' ?>
                    </a>
                </li>
                <li>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </li>
                <li>
                    <a href="<?= $base ?>/atividades/" class="hover:text-white transition-colors">
                        <?= $lang->getCurrentLang() === 'pt' ? 'Atividades' : 'Activities' ?>
                    </a>
                </li>
                <li>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </li>
                <li class="text-white font-medium truncate max-w-[200px]"><?= e($activity['title']) ?></li>
            </ol>
        </nav>

        <!-- Category Badge -->
        <span class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm text-white text-xs font-bold tracking-widest uppercase px-4 py-2 rounded-full mb-4 animate-on-scroll" data-animation="fade-up" data-delay="50">
            <?= $categories[$activity['category']]['label'] ?? ucfirst($activity['category']) ?>
        </span>

        <!-- Title -->
        <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl text-white mb-4 drop-shadow-lg animate-on-scroll" data-animation="fade-up" data-delay="100">
            <?= e($activity['title']) ?>
        </h1>

        <?php if (!empty($activity['distance_km'])): ?>
        <div class="flex items-center gap-2 text-white/80 animate-on-scroll" data-animation="fade-up" data-delay="150">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="text-sm font-medium">
                <?= $activity['distance_km'] > 0 ? number_format($activity['distance_km'], 1) . ' km de Mogadouro' : ($lang->getCurrentLang() === 'pt' ? 'Centro de Mogadouro' : 'Mogadouro Center') ?>
            </span>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Activity Content -->
<section class="pt-16 lg:pt-20 pb-0 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 lg:pb-20">
        <div class="grid lg:grid-cols-3 gap-12">

            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-12">

                <!-- Description -->
                <div class="animate-on-scroll" data-animation="fade-up">
                    <?php if (!empty($activity['short_description'])): ?>
                    <p class="text-xl text-charcoal/80 leading-relaxed font-light mb-8 border-l-4 border-accent pl-6">
                        <?= e($activity['short_description']) ?>
                    </p>
                    <?php endif; ?>

                    <?php if (!empty($activity['full_description'])): ?>
                    <div class="prose prose-lg prose-charcoal max-w-none">
                        <?= $activity['full_description'] ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Image Gallery -->
                <?php if (!empty($activityImages) && count($activityImages) > 1): ?>
                <div class="animate-on-scroll" data-animation="fade-up" data-delay="100">
                    <h3 class="font-serif text-2xl text-primary mb-6"><?= $lang->getCurrentLang() === 'pt' ? 'Galeria' : 'Gallery' ?></h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <?php foreach ($activityImages as $idx => $img): ?>
                        <div class="aspect-[4/3] rounded-2xl overflow-hidden cursor-pointer group" onclick="openActivityLightbox(<?= $idx ?>)">
                            <img src="<?= $base . $img['file_path'] ?>"
                                 alt="<?= e($lang->getCurrentLang() === 'pt' ? ($img['alt_text_pt'] ?? $activity['title']) : ($img['alt_text_en'] ?? $activity['title'])) ?>"
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Tips Section -->
                <?php if (!empty($activity['tips'])): ?>
                <div class="bg-accent/10 rounded-2xl p-8 border border-accent/20 animate-on-scroll" data-animation="fade-up" data-delay="150">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-accent/20 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-serif text-lg text-primary mb-2"><?= $lang->getCurrentLang() === 'pt' ? 'Dicas Locais' : 'Local Tips' ?></h4>
                            <p class="text-charcoal/80 leading-relaxed"><?= e($activity['tips']) ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Google Maps -->
                <?php if (!empty($activity['google_maps_embed'])): ?>
                <div class="animate-on-scroll" data-animation="fade-up" data-delay="200">
                    <h3 class="font-serif text-2xl text-primary mb-6"><?= $lang->getCurrentLang() === 'pt' ? 'Localização' : 'Location' ?></h3>
                    <div class="rounded-2xl overflow-hidden shadow-lg border border-cream-200">
                        <?= $activity['google_maps_embed'] ?>
                    </div>
                </div>
                <?php elseif (!empty($activity['latitude']) && !empty($activity['longitude'])): ?>
                <div class="animate-on-scroll" data-animation="fade-up" data-delay="200">
                    <h3 class="font-serif text-2xl text-primary mb-6"><?= $lang->getCurrentLang() === 'pt' ? 'Localização' : 'Location' ?></h3>
                    <div class="rounded-2xl overflow-hidden shadow-lg border border-cream-200">
                        <div id="activity-map" class="w-full h-[400px]"></div>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- Sidebar -->
            <div class="space-y-6 sticky top-32 self-start">

                <!-- Info Card -->
                <div class="bg-cream-50 rounded-3xl p-8 border border-cream-200 animate-on-scroll" data-animation="fade-left">
                    <h3 class="font-serif text-xl text-primary mb-6"><?= $lang->getCurrentLang() === 'pt' ? 'Informações' : 'Information' ?></h3>

                    <div class="space-y-5">
                        <?php if (!empty($activity['address'])): ?>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-white rounded-lg shadow-sm flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs font-bold uppercase tracking-widest text-charcoal/50 block mb-1"><?= $lang->getCurrentLang() === 'pt' ? 'Morada' : 'Address' ?></span>
                                <span class="text-charcoal"><?= e($activity['address']) ?></span>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($activity['phone'])): ?>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-white rounded-lg shadow-sm flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs font-bold uppercase tracking-widest text-charcoal/50 block mb-1"><?= $lang->getCurrentLang() === 'pt' ? 'Telefone' : 'Phone' ?></span>
                                <a href="tel:<?= preg_replace('/\s+/', '', $activity['phone']) ?>" class="text-charcoal hover:text-secondary transition-colors"><?= e($activity['phone']) ?></a>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($activity['website'])): ?>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-white rounded-lg shadow-sm flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs font-bold uppercase tracking-widest text-charcoal/50 block mb-1">Website</span>
                                <a href="<?= e($activity['website']) ?>" target="_blank" rel="noopener noreferrer" class="text-secondary hover:text-primary transition-colors break-all"><?= $lang->getCurrentLang() === 'pt' ? 'Visitar website' : 'Visit website' ?> →</a>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($activity['price_range'])): ?>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-white rounded-lg shadow-sm flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs font-bold uppercase tracking-widest text-charcoal/50 block mb-1"><?= $lang->getCurrentLang() === 'pt' ? 'Preço' : 'Price' ?></span>
                                <?php
                                $priceLabels = [
                                    'free' => $lang->getCurrentLang() === 'pt' ? 'Gratuito' : 'Free',
                                    'budget' => $lang->getCurrentLang() === 'pt' ? 'Económico' : 'Budget',
                                    'moderate' => $lang->getCurrentLang() === 'pt' ? 'Moderado' : 'Moderate',
                                    'expensive' => $lang->getCurrentLang() === 'pt' ? 'Premium' : 'Premium',
                                ];
                                ?>
                                <span class="text-charcoal"><?= $priceLabels[$activity['price_range']] ?? $activity['price_range'] ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- External Link Button -->
                    <?php if (!empty($activity['external_url'])): ?>
                    <a href="<?= e($activity['external_url']) ?>" target="_blank" rel="noopener noreferrer"
                       class="mt-8 w-full inline-flex items-center justify-center gap-2 bg-secondary text-white px-6 py-4 rounded-xl hover:bg-secondary-700 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                        <span class="font-medium"><?= $lang->getCurrentLang() === 'pt' ? 'Ver mais informações' : 'More information' ?></span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                    <?php endif; ?>

                    <!-- Get Directions -->
                    <?php if (!empty($activity['latitude']) && !empty($activity['longitude'])): ?>
                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $activity['latitude'] ?>,<?= $activity['longitude'] ?>"
                       target="_blank" rel="noopener noreferrer"
                       class="mt-4 w-full inline-flex items-center justify-center gap-2 border-2 border-secondary text-secondary px-6 py-4 rounded-xl hover:bg-secondary hover:text-white transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        <span class="font-medium"><?= $lang->getCurrentLang() === 'pt' ? 'Obter direções' : 'Get directions' ?></span>
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Back to Activities -->
                <a href="<?= $base ?>/atividades/" class="flex items-center gap-2 text-charcoal/60 hover:text-secondary transition-colors group">
                    <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span class="font-medium"><?= $lang->getCurrentLang() === 'pt' ? 'Voltar às atividades' : 'Back to activities' ?></span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Map Script -->
<?php if (!empty($activity['latitude']) && !empty($activity['longitude']) && empty($activity['google_maps_embed'])): ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const lat = <?= $activity['latitude'] ?>;
    const lng = <?= $activity['longitude'] ?>;

    const map = L.map('activity-map', { scrollWheelZoom: false }).setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const customIcon = L.divIcon({
        className: 'custom-map-marker',
        html: `<div style="width:40px;height:40px;background:linear-gradient(135deg,#264653 0%,#1d3a47 100%);border-radius:50% 50% 50% 0;transform:rotate(-45deg);box-shadow:0 4px 12px rgba(38,70,83,0.4);display:flex;align-items:center;justify-content:center;"><svg style="transform:rotate(45deg);width:20px;height:20px;color:#C5A059;" fill="currentColor" viewBox="0 0 20 20"><path d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9z"/><path fill="#264653" d="M10 11a2 2 0 100-4 2 2 0 000 4z"/></svg></div>`,
        iconSize: [40, 40],
        iconAnchor: [20, 40],
        popupAnchor: [0, -40]
    });

    L.marker([lat, lng], { icon: customIcon }).addTo(map)
        .bindPopup('<div style="text-align:center;padding:8px;"><strong style="color:#264653;"><?= e($activity['title']) ?></strong></div>');

    map.on('click', () => map.scrollWheelZoom.enable());
    map.on('mouseout', () => map.scrollWheelZoom.disable());
});
</script>
<?php endif; ?>

<!-- Lightbox for Gallery -->
<?php if (!empty($activityImages) && count($activityImages) > 1): ?>
<div id="activity-lightbox" class="fixed inset-0 z-[100] bg-black/95 hidden opacity-0 transition-opacity duration-300 flex items-center justify-center">
    <button onclick="closeActivityLightbox()" class="absolute top-4 right-4 text-white/60 hover:text-white p-2 hover:bg-white/10 rounded-full transition-colors">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>

    <button onclick="prevActivityImage()" class="absolute left-4 text-white/50 hover:text-white p-2 hover:bg-white/10 rounded-full transition-colors">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>

    <img id="activity-lightbox-img" src="" alt="" class="max-w-[90vw] max-h-[90vh] object-contain">

    <button onclick="nextActivityImage()" class="absolute right-4 text-white/50 hover:text-white p-2 hover:bg-white/10 rounded-full transition-colors">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/>
        </svg>
    </button>

    <div class="absolute bottom-4 text-white/60 text-sm">
        <span id="activity-lightbox-counter"></span>
    </div>
</div>

<script>
const activityImages = <?= json_encode(array_map(function($img) use ($base) {
    $path = $base . $img['file_path'];
    return ['url' => $path, 'alt' => $img['alt_text_pt'] ?? ''];
}, $activityImages)) ?>;
let currentActivityImage = 0;

function openActivityLightbox(index) {
    currentActivityImage = index;
    updateActivityLightbox();
    const modal = document.getElementById('activity-lightbox');
    modal.classList.remove('hidden');
    setTimeout(() => modal.classList.remove('opacity-0'), 10);
    document.body.style.overflow = 'hidden';
}

function closeActivityLightbox() {
    const modal = document.getElementById('activity-lightbox');
    modal.classList.add('opacity-0');
    setTimeout(() => modal.classList.add('hidden'), 300);
    document.body.style.overflow = '';
}

function updateActivityLightbox() {
    document.getElementById('activity-lightbox-img').src = activityImages[currentActivityImage].url;
    document.getElementById('activity-lightbox-counter').textContent = (currentActivityImage + 1) + ' / ' + activityImages.length;
}

function nextActivityImage() {
    currentActivityImage = (currentActivityImage + 1) % activityImages.length;
    updateActivityLightbox();
}

function prevActivityImage() {
    currentActivityImage = (currentActivityImage - 1 + activityImages.length) % activityImages.length;
    updateActivityLightbox();
}

document.addEventListener('keydown', function(e) {
    const lightbox = document.getElementById('activity-lightbox');
    if (!lightbox.classList.contains('hidden')) {
        if (e.key === 'Escape') closeActivityLightbox();
        if (e.key === 'ArrowRight') nextActivityImage();
        if (e.key === 'ArrowLeft') prevActivityImage();
    }
});
</script>
<?php endif; ?>


<style>
/* Remove all spacing before footer for Single Activity Page */
footer {
    margin-top: 0 !important;
}
</style>
<?php else: ?>
<!-- ================================================== -->
<!-- MAIN ACTIVITIES PAGE (GRID VIEW)                   -->
<!-- ================================================== -->

<!-- Hero Section -->
<section class="relative h-[75vh] min-h-[600px] flex items-center bg-primary overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat bg-fixed"
             style="background-image: url('<?= $heroUrl ?>');">
        </div>
        <div class="absolute inset-0 bg-black" style="opacity: <?= $heroOverlay ?>"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-black/40"></div>
    </div>

    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <span class="inline-block text-accent text-lg font-medium tracking-[0.2em] uppercase mb-4 animate-on-scroll" data-animation="fade-up">
            <?= $lang->getCurrentLang() === 'pt' ? 'Descubra Mogadouro' : 'Discover Mogadouro' ?>
        </span>

        <h1 class="font-cursive text-6xl md:text-7xl lg:text-8xl text-cream mb-6 drop-shadow-xl animate-on-scroll" data-animation="fade-up" data-delay="100">
            <?= $lang->getCurrentLang() === 'pt' ? 'O Que Fazer' : 'What to Do' ?>
        </h1>

        <p class="text-lg md:text-xl text-cream/90 max-w-2xl mx-auto font-light leading-relaxed animate-on-scroll" data-animation="fade-up" data-delay="200">
            <?= $lang->getCurrentLang() === 'pt'
                ? 'De paisagens deslumbrantes a sabores únicos, o nordeste transmontano tem muito para oferecer.'
                : 'From stunning landscapes to unique flavors, the northeast of Trás-os-Montes has much to offer.' ?>
        </p>
    </div>
</section>

<!-- Search & Filter Section -->
<section class="py-6 bg-gradient-to-b from-white to-cream-50" id="filter-bar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Search Bar -->
        <div class="max-w-3xl mx-auto mb-10 animate-on-scroll" data-animation="fade-up">
            <div class="relative group">
                <input type="text"
                       id="activity-search"
                       placeholder="<?= $lang->getCurrentLang() === 'pt' ? 'Pesquisar atividades, restaurantes, locais...' : 'Search activities, restaurants, places...' ?>"
                       class="w-full px-6 py-5 pl-16 bg-white border-2 border-cream-200 rounded-2xl text-charcoal placeholder-charcoal/40 focus:outline-none focus:ring-4 focus:ring-secondary/20 focus:border-secondary transition-all shadow-sm hover:shadow-md text-base">
                <div class="absolute left-5 top-1/2 -translate-y-1/2 w-10 h-10 bg-cream-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <button id="clear-search" class="absolute right-4 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center text-charcoal/30 hover:text-charcoal hover:bg-cream-50 rounded-lg hidden transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Category Filters -->
        <div class="flex flex-wrap items-center justify-center gap-3 animate-on-scroll" data-animation="fade-up" data-delay="100" id="filters-container">
            <?php foreach ($categories as $key => $cat): ?>
            <button class="activity-filter group relative px-5 md:px-7 py-3 rounded-xl text-sm md:text-base font-semibold tracking-wide transition-all duration-300 <?= $key === 'all' ? 'active bg-secondary text-white shadow-lg shadow-secondary/30 scale-105' : 'bg-white text-charcoal/70 hover:bg-secondary hover:text-white border-2 border-cream-200 hover:border-secondary hover:shadow-lg' ?> hover:scale-105 active:scale-95" data-filter="<?= $key ?>">
                <span><?= $cat['label'] ?></span>
            </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Bento Grid Section -->
<section class="pt-16 lg:pt-20 pb-0 bg-cream-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 lg:pb-20">

        <!-- Results Counter -->
        <div class="mb-8 flex items-center justify-between animate-on-scroll" data-animation="fade-up">
            <p class="text-charcoal/60" id="results-count">
                <span id="count-number"><?= count($activities) ?></span>
                <?= $lang->getCurrentLang() === 'pt' ? 'atividades encontradas' : 'activities found' ?>
            </p>
        </div>

        <!-- Bento Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6" id="activities-grid">
            <?php
            $featuredCount = 0;
            foreach ($activities as $idx => $act):
                // First 2 featured items get larger cards
                $isBig = $act['is_featured'] && $featuredCount < 2;
                if ($act['is_featured']) $featuredCount++;

                // Get cover image from media
                $actImage = $act['cover_img'] ?? null;
                $imageUrl = null;
                if ($actImage) {
                    $imageUrl = $base . $actImage;
                }

                // Category colors
                $categoryColors = [
                    'nature' => 'from-green-700/80 to-primary/80',
                    'culture' => 'from-amber-700/80 to-primary/80',
                    'gastronomy' => 'from-orange-700/80 to-primary/80',
                    'restaurants' => 'from-red-700/80 to-primary/80',
                    'cafes' => 'from-amber-600/80 to-primary/80',
                    'architecture' => 'from-slate-700/80 to-primary/80',
                    'adventure' => 'from-emerald-700/80 to-primary/80',
                    'events' => 'from-purple-700/80 to-primary/80',
                    'wellness' => 'from-teal-700/80 to-primary/80',
                    'rural_tourism' => 'from-lime-700/80 to-primary/80',
                    'leisure' => 'from-sky-700/80 to-primary/80',
                ];
                $gradientClass = $categoryColors[$act['category']] ?? 'from-secondary/80 to-primary/80';
                $animDelay = min($idx * 50, 300); // Cap delay at 300ms
            ?>
            <article class="activity-card group <?= $isBig ? 'md:col-span-2 md:row-span-2' : '' ?> bg-white rounded-xl overflow-hidden border border-cream-200 hover:border-secondary/30 shadow-sm hover:shadow-md transition-all duration-300 animate-on-scroll"
                     data-animation="fade-up"
                     data-delay="<?= $animDelay ?>"
                     data-category="<?= $act['category'] ?>"
                     data-title="<?= strtolower(e($act['title'])) ?>"
                     data-description="<?= strtolower(e($act['short_description'] ?? '')) ?>">
                <a href="<?= $base ?>/atividades/?slug=<?= e($act['slug']) ?>" class="block h-full">
                    <div class="<?= $isBig ? 'aspect-[16/10]' : 'aspect-[4/3]' ?> relative overflow-hidden">
                        <?php if ($imageUrl): ?>
                        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110"
                             style="background-image: url('<?= $imageUrl ?>');">
                        </div>
                        <?php else: ?>
                        <div class="absolute inset-0 bg-gradient-to-br <?= $gradientClass ?> flex items-center justify-center">
                            <svg class="w-16 h-16 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent group-hover:from-black/60 transition-all duration-300"></div>

                        <!-- Category Badge -->
                        <span class="absolute top-3 left-3 md:top-4 md:left-4 bg-white/95 backdrop-blur-sm text-secondary text-[10px] md:text-xs font-semibold tracking-wide px-3 py-1.5 rounded-lg shadow-sm border border-white/20">
                            <?= $categories[$act['category']]['label'] ?? ucfirst($act['category']) ?>
                        </span>

                        <?php if ($act['is_featured']): ?>
                        <!-- Featured Badge -->
                        <span class="absolute top-3 right-3 md:top-4 md:right-4 w-8 h-8 md:w-9 md:h-9 bg-accent/90 backdrop-blur-sm text-white rounded-full shadow-sm flex items-center justify-center" title="<?= $lang->getCurrentLang() === 'pt' ? 'Destaque' : 'Featured' ?>">
                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </span>
                        <?php endif; ?>

                        <!-- Title Overlay -->
                        <div class="absolute bottom-0 left-0 right-0 p-5 md:p-6">
                            <h3 class="font-serif <?= $isBig ? 'text-2xl md:text-3xl' : 'text-lg md:text-xl' ?> text-white font-medium mb-2 leading-tight">
                                <?= e($act['title']) ?>
                            </h3>
                            <?php if (!empty($act['distance_km'])): ?>
                            <div class="flex items-center gap-1.5 text-white/90 text-xs md:text-sm font-medium">
                                <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                <?= $act['distance_km'] > 0 ? number_format($act['distance_km'], 1) . ' km' : ($lang->getCurrentLang() === 'pt' ? 'Centro' : 'Center') ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!$isBig && !empty($act['short_description'])): ?>
                    <div class="p-5 md:p-6 border-t border-cream-100">
                        <p class="text-charcoal/60 text-sm leading-relaxed line-clamp-2">
                            <?= e($act['short_description']) ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </a>
            </article>
            <?php endforeach; ?>
        </div>

        <!-- No Results Message -->
        <div id="no-results" class="hidden text-center py-16">
            <svg class="w-16 h-16 mx-auto text-charcoal/30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-charcoal/60 text-lg">
                <?= $lang->getCurrentLang() === 'pt' ? 'Nenhuma atividade encontrada' : 'No activities found' ?>
            </p>
            <button onclick="resetFilters()" class="mt-4 text-secondary hover:text-primary font-medium transition-colors">
                <?= $lang->getCurrentLang() === 'pt' ? 'Limpar filtros' : 'Clear filters' ?>
            </button>
        </div>

    </div>
</section>

<!-- External Links Section -->
<?php if (!empty($externalLinks)): ?>
<section class="pt-20 lg:pt-24 pb-0 bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 lg:pb-24">
        <div class="text-center mb-12 animate-on-scroll" data-animation="fade-up">
            <span class="text-accent text-sm font-medium tracking-[0.2em] uppercase mb-3 inline-block">
                <?= $lang->getCurrentLang() === 'pt' ? 'Mais Informações' : 'More Information' ?>
            </span>
            <h2 class="font-serif text-3xl md:text-4xl text-primary mb-4">
                <?= $lang->getCurrentLang() === 'pt' ? 'Descubra Mais Sobre Mogadouro' : 'Discover More About Mogadouro' ?>
            </h2>
            <p class="text-charcoal/70 max-w-2xl mx-auto">
                <?= $lang->getCurrentLang() === 'pt'
                    ? 'Explore websites e recursos oficiais sobre turismo em Mogadouro e Trás-os-Montes.'
                    : 'Explore official websites and resources about tourism in Mogadouro and Trás-os-Montes.' ?>
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($externalLinks as $linkIdx => $link):
                $linkDelay = min($linkIdx * 100, 400);
            ?>
            <a href="<?= e($link['url']) ?>"
               target="_blank"
               rel="noopener noreferrer"
               class="group flex items-start gap-4 p-6 bg-cream-50 rounded-2xl hover:bg-white hover:shadow-lg border border-cream-100 hover:border-secondary/30 transition-all duration-300 animate-on-scroll"
               data-animation="fade-up"
               data-delay="<?= $linkDelay ?>"
               onclick="trackLinkClick(<?= $link['id'] ?>)">
                <div class="w-14 h-14 bg-white rounded-xl shadow-sm flex items-center justify-center flex-shrink-0 group-hover:bg-secondary group-hover:text-white transition-all text-secondary">
                    <?php if (!empty($link['icon_image'])): ?>
                    <img src="<?= strpos($link['icon_image'], 'uploads/') === 0 ? $base . '/' . $link['icon_image'] : asset($link['icon_image']) ?>"
                         alt="" class="w-8 h-8 object-contain">
                    <?php else: ?>
                    <?php
                    $icons = [
                        'building' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
                        'tree' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>',
                        'map' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>',
                        'compass' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 9l3 3m0 0l3-3m-3 3V6"/>',
                        'star' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>',
                    ];
                    $iconPath = $icons[$link['icon']] ?? $icons['map'];
                    ?>
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <?= $iconPath ?>
                    </svg>
                    <?php endif; ?>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-serif text-lg text-primary group-hover:text-secondary transition-colors mb-1 truncate">
                        <?= e($link['title']) ?>
                    </h3>
                    <?php if (!empty($link['description'])): ?>
                    <p class="text-charcoal/60 text-sm leading-relaxed line-clamp-2">
                        <?= e($link['description']) ?>
                    </p>
                    <?php endif; ?>
                    <span class="inline-flex items-center gap-1 text-secondary text-xs font-medium mt-2 group-hover:gap-2 transition-all">
                        <?= $lang->getCurrentLang() === 'pt' ? 'Visitar' : 'Visit' ?>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Filter & Search Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.activity-filter');
    const activityCards = document.querySelectorAll('.activity-card');
    const searchInput = document.getElementById('activity-search');
    const clearSearch = document.getElementById('clear-search');
    const countNumber = document.getElementById('count-number');
    const noResults = document.getElementById('no-results');
    const grid = document.getElementById('activities-grid');

    let currentFilter = 'all';
    let searchTerm = '';

    function filterActivities() {
        let visibleCount = 0;

        activityCards.forEach(card => {
            const category = card.dataset.category;
            const title = card.dataset.title || '';
            const description = card.dataset.description || '';

            const matchesFilter = currentFilter === 'all' || category === currentFilter;
            const matchesSearch = !searchTerm ||
                title.includes(searchTerm) ||
                description.includes(searchTerm);

            if (matchesFilter && matchesSearch) {
                card.style.display = 'block';
                card.style.animation = 'fadeInUp 0.4s ease forwards';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        countNumber.textContent = visibleCount;

        if (visibleCount === 0) {
            noResults.classList.remove('hidden');
            grid.classList.add('hidden');
        } else {
            noResults.classList.add('hidden');
            grid.classList.remove('hidden');
        }
    }

    // Filter buttons
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            currentFilter = this.dataset.filter;

            filterButtons.forEach(btn => {
                btn.classList.remove('active', 'bg-secondary', 'text-white', 'shadow-lg', 'shadow-secondary/30', 'scale-105');
                btn.classList.add('bg-white', 'text-charcoal/70', 'border-2', 'border-cream-200');
            });
            this.classList.remove('bg-white', 'text-charcoal/70', 'border-2', 'border-cream-200');
            this.classList.add('active', 'bg-secondary', 'text-white', 'shadow-lg', 'shadow-secondary/30', 'scale-105');

            filterActivities();
        });
    });

    // Search
    searchInput.addEventListener('input', function() {
        searchTerm = this.value.toLowerCase().trim();
        clearSearch.classList.toggle('hidden', !searchTerm);
        filterActivities();
    });

    clearSearch.addEventListener('click', function() {
        searchInput.value = '';
        searchTerm = '';
        clearSearch.classList.add('hidden');
        filterActivities();
    });

    // Reset filters function (global)
    window.resetFilters = function() {
        currentFilter = 'all';
        searchTerm = '';
        searchInput.value = '';
        clearSearch.classList.add('hidden');

        filterButtons.forEach(btn => {
            btn.classList.remove('active', 'bg-secondary', 'text-white', 'shadow-lg', 'shadow-secondary/30', 'scale-105');
            btn.classList.add('bg-white', 'text-charcoal/70', 'border-2', 'border-cream-200');
        });
        filterButtons[0].classList.remove('bg-white', 'text-charcoal/70', 'border-2', 'border-cream-200');
        filterButtons[0].classList.add('active', 'bg-secondary', 'text-white', 'shadow-lg', 'shadow-secondary/30', 'scale-105');

        filterActivities();
    };

    // Track link clicks
    window.trackLinkClick = function(linkId) {
        fetch('<?= $base ?>/api/track-link.php?id=' + linkId, { method: 'POST' }).catch(() => {});
    };
});
</script>

<style>
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}


/* Remove all spacing before footer */
footer {
    margin-top: 0 !important;
}
</style>

<?php endif; ?>

<?php include INCLUDES_PATH . '/footer.php'; ?>
