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
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?= asset('images/accommodation-hero.jpg') ?>');">
        <div class="absolute inset-0 bg-primary/70"></div>
    </div>
    <div class="relative z-10 h-full flex items-center justify-center text-center px-4">
        <div class="max-w-3xl animate-slide-up">
            <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl text-cream mb-4 drop-shadow-lg">
                <?= e($accTranslation['title'] ?? 'A Casa do Gi') ?>
            </h1>
            <p class="text-xl text-cream-100">
                <?= e($accTranslation['short_description'] ?? 'Casa de ferias de 100m2') ?>
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
                <div>
                    <h2 class="font-serif text-2xl md:text-3xl text-primary mb-6">Sobre o Alojamento</h2>
                    <div class="prose prose-lg text-charcoal max-w-none">
                        <?= $accTranslation['full_description'] ?? '<p>A Casa do Gi e sinonimo de simplicidade, acolhimento, momentos de convivio marcantes, calor da familia, alegria, diversao, gargalhadas e muito amor!</p>' ?>
                    </div>
                </div>

                <!-- Gallery -->
                <div>
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
                <div>
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
                <div>
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

                            <?php if ($guestreadyUrl): ?>
                            <a href="<?= e($guestreadyUrl) ?>" target="_blank" rel="noopener"
                               class="flex items-center justify-center w-full py-3 px-4 bg-secondary text-cream font-semibold rounded-lg hover:bg-secondary-600 transition-all shadow-md hover:shadow-lg hover:scale-105">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Reservar no GuestReady
                            </a>
                            <?php endif; ?>

                            <?php if ($bookingUrl): ?>
                            <a href="<?= e($bookingUrl) ?>" target="_blank" rel="noopener"
                               class="flex items-center justify-center w-full py-3 px-4 bg-primary text-cream font-semibold rounded-lg hover:bg-primary-600 transition-all shadow-md hover:shadow-lg hover:scale-105">
                                Reservar no Booking.com
                            </a>
                            <?php endif; ?>

                            <?php if ($airbnbUrl): ?>
                            <a href="<?= e($airbnbUrl) ?>" target="_blank" rel="noopener"
                               class="flex items-center justify-center w-full py-3 px-4 bg-pink-600 text-white font-semibold rounded-lg hover:bg-pink-700 transition-all shadow-md hover:shadow-lg hover:scale-105">
                                Reservar no Airbnb
                            </a>
                            <?php endif; ?>

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
        <div class="text-center mb-10">
            <h2 class="font-serif text-2xl md:text-3xl text-primary mb-4">Localizacao</h2>
            <p class="text-charcoal">Mogadouro, Portugal</p>
        </div>

        <!-- Map Placeholder -->
        <div class="aspect-video bg-cream-200 rounded-lg overflow-hidden shadow-lg border border-accent/20">
            <div class="w-full h-full flex items-center justify-center text-charcoal-400">
                <div class="text-center">
                    <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <p class="text-charcoal">Av. N. Sr. do Caminho 52</p>
                    <p class="text-charcoal">5200-207 Mogadouro, Portugal</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
