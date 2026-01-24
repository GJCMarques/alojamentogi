<?php
/**
 * A Casa do Gi - Accommodation Page (Portuguese)
 */

require_once dirname(__DIR__) . '/includes/init.php';

use Core\Database;
use Core\Language;

$lang = Language::getInstance();
$db = Database::getInstance();
$base = basePath();

// Get accommodation data
$accommodation = $db->fetch("SELECT * FROM accommodation LIMIT 1");
$accTranslation = $db->fetch(
    "SELECT * FROM accommodation_translations WHERE accommodation_id = ? AND language_id = ?",
    [$accommodation['id'] ?? 1, $lang->getCurrentLangId()]
);

// Get amenities
$amenities = $db->fetchAll(
    "SELECT a.*, at.name FROM amenities a
     JOIN amenity_translations at ON a.id = at.amenity_id
     JOIN accommodation_amenities aa ON a.id = aa.amenity_id
     WHERE at.language_id = ? AND a.is_active = 1
     ORDER BY a.sort_order",
    [$lang->getCurrentLangId()]
);

// Get gallery images
$galleryImages = $db->fetchAll(
    "SELECT * FROM media WHERE category = 'gallery' ORDER BY sort_order LIMIT 12"
);

// Get bedrooms
$bedrooms = $db->fetchAll(
    "SELECT b.*, bt.beds_description FROM bedrooms b
     JOIN bedroom_translations bt ON b.id = bt.bedroom_id
     WHERE bt.language_id = ?
     ORDER BY b.bedroom_number",
    [$lang->getCurrentLangId()]
);

// Booking URLs
$guestreadyUrl = setting('guestready_url');
$bookingUrl = setting('booking_url');
$airbnbUrl = setting('airbnb_url');

// Page configuration
$pageTitle = __('accommodation_title', 'Alojamento');
$pageDescription = 'A Casa do Gi - Alojamento Local em Mogadouro. Casa de ferias de 100m2 para 6 hospedes.';

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="relative h-[60vh] min-h-[400px] -mt-20 overflow-hidden">
    <div class="absolute inset-0 bg-cover bg-center parallax-bg" style="background-image: url('<?= asset('images/accommodation-hero.jpg') ?>');">
        <!-- Overlay Gradient -->
        <div class="absolute inset-0 bg-primary/40 mix-blend-multiply"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-primary/90 via-primary/40 to-transparent"></div>
    </div>
    <div class="relative z-10 h-full flex items-center justify-center text-center px-4 pt-20">
        <div class="max-w-4xl animate-slide-up space-y-4">
            <span class="inline-block text-accent text-lg font-medium tracking-[0.2em] uppercase mb-2 drop-shadow-md">
                Bem-vindo a
            </span>
            <h1 class="font-cursive text-6xl md:text-7xl lg:text-8xl text-cream drop-shadow-2xl leading-none">
                <?= e($accTranslation['title'] ?? 'A Casa do Gi') ?>
            </h1>
            <p class="font-serif text-xl md:text-2xl text-cream/90 max-w-2xl mx-auto italic font-light leading-relaxed mt-6 drop-shadow-md">
                "<?= e($accTranslation['short_description'] ?? 'Casa de ferias de 100m2') ?>"
            </p>
        </div>
    </div>
</section>

<!-- Quick Info Bar -->
<section class="bg-secondary py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div class="text-cream">
                <div class="text-2xl font-bold"><?= $accommodation['max_guests'] ?? 6 ?></div>
                <div class="text-cream-200 text-sm">Hospedes</div>
            </div>
            <div class="text-cream">
                <div class="text-2xl font-bold"><?= $accommodation['bedrooms'] ?? 3 ?></div>
                <div class="text-cream-200 text-sm">Quartos</div>
            </div>
            <div class="text-cream">
                <div class="text-2xl font-bold"><?= $accommodation['bathrooms'] ?? 2 ?></div>
                <div class="text-cream-200 text-sm">Casas de Banho</div>
            </div>
            <div class="text-cream">
                <div class="text-2xl font-bold"><?= (int)$accommodation['area_sqm'] ?? 100 ?>m<sup>2</sup></div>
                <div class="text-cream-200 text-sm">Area</div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="py-16 lg:py-24 bg-cream-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-12">
            <!-- Left Column - Description -->
            <div class="lg:col-span-2 space-y-12">
                <!-- Description -->
                <div class="animate-on-scroll">
                    <h2 class="font-serif text-2xl md:text-3xl text-primary mb-6">Sobre o Alojamento</h2>
                    <div class="prose prose-lg text-charcoal max-w-none">
                        <?= $accTranslation['full_description'] ?? '<p>A Casa do Gi e sinonimo de simplicidade, acolhimento, momentos de convivio marcantes, calor da familia, alegria, diversao, gargalhadas e muito amor!</p>' ?>
                    </div>
                </div>

                <!-- Gallery -->
                <div class="animate-on-scroll">
                    <h2 class="font-serif text-2xl md:text-3xl text-primary mb-6">Galeria</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <?php if (empty($galleryImages)): ?>
                        <!-- Placeholder images -->
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                        <div class="aspect-square bg-cream-200 rounded-lg overflow-hidden border border-accent/20">
                            <div class="w-full h-full flex items-center justify-center text-charcoal-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        <?php endfor; ?>
                        <?php else: ?>
                            <?php foreach ($galleryImages as $image): ?>
                            <a href="<?= upload($image['file_path']) ?>" class="aspect-square rounded-lg overflow-hidden group shadow-md hover:shadow-xl transition-all border border-accent/20" data-lightbox="gallery">
                                <img src="<?= upload($image['file_path']) ?>"
                                     alt="<?= e($image['alt_text_pt'] ?? 'A Casa do Gi') ?>"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                     loading="lazy">
                            </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Bedrooms -->
                <div class="animate-on-scroll">
                    <h2 class="font-serif text-2xl md:text-3xl text-primary mb-6">Quartos</h2>
                    <div class="grid md:grid-cols-3 gap-6">
                        <?php if (empty($bedrooms)): ?>
                        <div class="bg-cream p-6 rounded-lg shadow-md border border-accent/20">
                            <h3 class="font-semibold text-primary mb-2">Quarto 1</h3>
                            <p class="text-charcoal text-sm">2 camas de solteiro</p>
                        </div>
                        <div class="bg-cream p-6 rounded-lg shadow-md border border-accent/20">
                            <h3 class="font-semibold text-primary mb-2">Quarto 2</h3>
                            <p class="text-charcoal text-sm">Sofa-cama de solteiro, Cama de casal</p>
                        </div>
                        <div class="bg-cream p-6 rounded-lg shadow-md border border-accent/20">
                            <h3 class="font-semibold text-primary mb-2">Quarto 3</h3>
                            <p class="text-charcoal text-sm">Cama de casal</p>
                        </div>
                        <?php else: ?>
                            <?php foreach ($bedrooms as $bedroom): ?>
                            <div class="bg-cream p-6 rounded-lg shadow-md border border-accent/20">
                                <h3 class="font-semibold text-primary mb-2">Quarto <?= $bedroom['bedroom_number'] ?></h3>
                                <p class="text-charcoal text-sm"><?= e($bedroom['beds_description']) ?></p>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Amenities -->
                <div class="animate-on-scroll">
                    <h2 class="font-serif text-2xl md:text-3xl text-primary mb-6">Comodidades</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <?php
                        $defaultAmenities = [
                            ['icon' => 'wifi', 'name' => 'Internet Wifi'],
                            ['icon' => 'ac', 'name' => 'Ar condicionado'],
                            ['icon' => 'heater', 'name' => 'Aquecedores'],
                            ['icon' => 'parking', 'name' => 'Estacionamento incluido'],
                            ['icon' => 'pool-private', 'name' => 'Piscina privada'],
                            ['icon' => 'pool-shared', 'name' => 'Piscina partilhada'],
                            ['icon' => 'garden', 'name' => 'Jardim'],
                            ['icon' => 'terrace', 'name' => 'Terraco'],
                            ['icon' => 'washing-machine', 'name' => 'Maquina de lavar'],
                            ['icon' => 'dishwasher', 'name' => 'Lava-louca'],
                            ['icon' => 'hairdryer', 'name' => 'Secador de cabelo'],
                            ['icon' => 'workspace', 'name' => 'Area de trabalho'],
                        ];
                        $displayAmenities = empty($amenities) ? $defaultAmenities : $amenities;
                        ?>
                        <?php foreach ($displayAmenities as $amenity): ?>
                        <div class="flex items-center space-x-3 p-3 bg-cream rounded-lg shadow-md border border-accent/20">
                            <div class="w-10 h-10 bg-secondary/10 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="text-charcoal text-sm"><?= e($amenity['name']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column - Booking Card -->
            <div class="lg:col-span-1">
                <div class="sticky top-24">
                    <div class="bg-cream rounded-lg shadow-xl p-6 border border-accent/30">
                        <div class="text-center mb-6">
                            <p class="text-secondary font-medium mb-2">Reembolso total ate 5 dias antes da chegada</p>
                            <div class="flex items-center justify-center space-x-2 text-charcoal">
                                <svg class="w-5 h-5 text-accent" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="font-semibold">4.3</span>
                                <span class="text-charcoal-400">|</span>
                                <span>11 avaliacoes</span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <h3 class="font-serif text-lg text-primary text-center mb-4">Reserve a Sua Estadia</h3>

                            <?php 
                            component('booking-buttons', [
                                'layout' => 'vertical', 
                                'size' => 'default',
                                'show_labels' => true
                            ]); 
                            ?>

                            <?php if (!$guestreadyUrl && !$bookingUrl && !$airbnbUrl): ?>
                            <p class="text-center text-charcoal-600 text-sm">
                                Entre em contacto para mais informacoes sobre reservas.
                            </p>
                            <a href="<?= $base ?>/contactos/" class="flex items-center justify-center w-full py-3 px-4 bg-secondary text-cream font-semibold rounded-lg hover:bg-secondary-600 transition-all shadow-md hover:shadow-lg hover:scale-105">
                                Contactar-nos
                            </a>
                            <?php endif; ?>
                        </div>

                        <div class="mt-6 pt-6 border-t border-accent/20">
                            <div class="space-y-3 text-sm text-charcoal">
                                <div class="flex justify-between">
                                    <span>Check-in</span>
                                    <span class="font-medium">Apos as <?= date('H:i', strtotime($accommodation['check_in_time'] ?? '16:00')) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Check-out</span>
                                    <span class="font-medium">Ate as <?= date('H:i', strtotime($accommodation['check_out_time'] ?? '11:00')) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Licenca</span>
                                    <span class="font-medium"><?= e($accommodation['license_number'] ?? '146729/AL') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Card -->
                    <div class="mt-6 bg-primary rounded-lg p-6 text-cream shadow-lg">
                        <h3 class="font-serif text-lg mb-4">Tem questoes?</h3>
                        <p class="text-cream-200 text-sm mb-4">Estamos disponiveis para ajudar com qualquer informacao adicional.</p>
                        <a href="<?= $base ?>/contactos/" class="inline-flex items-center text-accent hover:text-accent-300 text-sm font-medium transition-colors">
                            Enviar mensagem
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Location Section -->
<section class="py-16 bg-cream">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10 animate-on-scroll">
            <span class="inline-block text-accent text-sm font-medium tracking-[0.2em] uppercase mb-3">Localizacao</span>
            <h2 class="font-serif text-2xl md:text-3xl text-primary mb-4">Onde Estamos</h2>
            <p class="text-charcoal/70">Mogadouro, no coracao de Tras-os-Montes</p>
        </div>

        <div class="animate-on-scroll" data-delay="200">
            <!-- Map Container -->
            <div class="relative rounded-2xl overflow-hidden shadow-xl border border-charcoal/10">
                <div id="location-map" class="w-full h-[400px] md:h-[450px]"></div>

                <!-- Map Overlay Card -->
                <div class="absolute bottom-4 left-4 right-4 md:right-auto md:max-w-sm bg-white/95 backdrop-blur-sm rounded-xl shadow-lg p-5 border border-charcoal/10 z-[5000]">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-secondary/10 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-primary text-lg">A Casa do Gi</h3>
                            <p class="text-charcoal/80 text-sm mt-1">
                                Av. N. Sr. do Caminho 52<br>
                                5200-207 Mogadouro
                            </p>
                            <a href="https://www.google.com/maps/dir/?api=1&destination=41.3397,-6.7147"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex items-center text-secondary hover:text-secondary-700 text-sm font-medium mt-3 group">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                                Obter direcoes
                                <svg class="w-3 h-3 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // A Casa do Gi coordinates (Mogadouro)
    const lat = 41.3397;
    const lng = -6.7147;

    // Initialize map
    const map = L.map('location-map', {
        scrollWheelZoom: false
    }).setView([lat, lng], 15);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Custom marker icon with brand colors
    const customIcon = L.divIcon({
        className: 'custom-map-marker',
        html: `
            <div style="
                width: 40px;
                height: 40px;
                background: linear-gradient(135deg, #264653 0%, #1d3a47 100%);
                border-radius: 50% 50% 50% 0;
                transform: rotate(-45deg);
                box-shadow: 0 4px 12px rgba(38, 70, 83, 0.4);
                display: flex;
                align-items: center;
                justify-content: center;
            ">
                <svg style="transform: rotate(45deg); width: 20px; height: 20px; color: #C5A059;" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
            </div>
        `,
        iconSize: [40, 40],
        iconAnchor: [20, 40],
        popupAnchor: [0, -40]
    });

    // Add marker with popup
    const marker = L.marker([lat, lng], { icon: customIcon }).addTo(map);

    marker.bindPopup(`
        <div style="text-align: center; padding: 8px;">
            <strong style="color: #264653; font-size: 14px;">A Casa do Gi</strong><br>
            <span style="color: #2D3748; font-size: 12px;">Av. N. Sr. do Caminho 52</span><br>
            <span style="color: #2D3748; font-size: 12px;">5200-207 Mogadouro</span>
        </div>
    `, {
        className: 'custom-popup'
    });

    // Enable scroll zoom on click
    map.on('click', function() {
        map.scrollWheelZoom.enable();
    });

    map.on('mouseout', function() {
        map.scrollWheelZoom.disable();
    });
});
</script>

<style>
.custom-popup .leaflet-popup-content-wrapper {
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}
.custom-popup .leaflet-popup-tip {
    background: white;
}
.leaflet-control-attribution {
    font-size: 10px;
    background: rgba(255, 255, 255, 0.8) !important;
}
</style>

<?php include INCLUDES_PATH . '/footer.php'; ?>
