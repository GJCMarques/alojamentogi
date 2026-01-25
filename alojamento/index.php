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
$pageDescription = 'A Casa do Gi - Alojamento Local em Mogadouro. Casa de férias de 100m2 para 6 hóspedes.';

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="relative h-[75vh] min-h-[600px] flex items-center bg-primary overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat bg-fixed" 
             style="background-image: url('<?= asset('images/MogadouroAlojamento.jpg') ?>');">
        </div>
        <div class="absolute inset-0 bg-black/40"></div>
    </div>

    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <span class="inline-block text-accent text-lg font-medium tracking-[0.2em] uppercase mb-4 animate-on-scroll" data-animation="fade-up">
            Bem-vindo a
        </span>
        <h1 class="font-cursive text-6xl md:text-7xl lg:text-8xl text-cream mb-6 drop-shadow-lg animate-on-scroll" data-animation="fade-up" data-delay="200">
            <?= e($accTranslation['title'] ?? 'A Casa do Gi') ?>
        </h1>
        <p class="text-xl md:text-2xl text-cream/90 max-w-2xl mx-auto font-light leading-relaxed animate-on-scroll" data-animation="fade-up" data-delay="400">
            <?= e($accTranslation['short_description'] ?? 'Casa de férias de 100m2') ?>
        </p>
    </div>
</section>

<!-- Stats Bar (Restored Overlap) -->
<div class="relative z-20 -mt-16 sm:-mt-20 px-4">
    <div class="max-w-5xl mx-auto bg-cream-50 rounded-xl shadow-xl overflow-hidden animate-on-scroll" data-animation="fade-up" data-delay="600">
        <div class="grid grid-cols-2 lg:grid-cols-4 divide-x divide-gray-100">
            <!-- Guests -->
            <div class="p-6 text-center group hover:bg-cream-50 transition-colors">
                <div class="inline-flex items-center justify-center w-12 h-12 mb-3 rounded-full bg-accent/10 text-secondary group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="text-2xl font-serif text-primary font-bold mb-1"><?= $accommodation['max_guests'] ?? 6 ?></div>
                <div class="text-xs uppercase tracking-wider text-charcoal/60">Hóspedes</div>
            </div>

            <!-- Bedrooms -->
            <div class="p-6 text-center group hover:bg-cream-50 transition-colors">
                <div class="inline-flex items-center justify-center w-12 h-12 mb-3 rounded-full bg-accent/10 text-secondary group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="text-2xl font-serif text-primary font-bold mb-1"><?= $accommodation['bedrooms'] ?? 3 ?></div>
                <div class="text-xs uppercase tracking-wider text-charcoal/60">Quartos</div>
            </div>

            <!-- Bathrooms -->
            <div class="p-6 text-center group hover:bg-cream-50 transition-colors">
                <div class="inline-flex items-center justify-center w-12 h-12 mb-3 rounded-full bg-accent/10 text-secondary group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m8-2a2 2 0 104 0m-4 0a2 2 0 114 0m-6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                    </svg>
                </div>
                <div class="text-2xl font-serif text-primary font-bold mb-1"><?= $accommodation['bathrooms'] ?? 2 ?></div>
                <div class="text-xs uppercase tracking-wider text-charcoal/60">Casas de Banho</div>
            </div>

            <!-- Area -->
            <div class="p-6 text-center group hover:bg-cream-50 transition-colors">
                <div class="inline-flex items-center justify-center w-12 h-12 mb-3 rounded-full bg-accent/10 text-secondary group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                </div>
                <div class="text-2xl font-serif text-primary font-bold mb-1"><?= (int)$accommodation['area_sqm'] ?? 100 ?></div>
                <div class="text-xs uppercase tracking-wider text-charcoal/60">Metros Quadrados</div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<section class="pb-16 lg:pb-24 pt-12 bg-cream-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-12 lg:gap-16">
            
            <!-- Left Column Content -->
            <div class="lg:col-span-2 space-y-16">
                
                <!-- Description -->
                <div class="animate-on-scroll" data-animation="fade-up">
                    <h2 class="font-serif text-3xl text-primary mb-6 relative inline-block">
                        Sobre o Alojamento
                        <span class="absolute -bottom-2 left-0 w-12 h-1 bg-accent"></span>
                    </h2>
                    <div class="prose prose-lg text-charcoal/80 font-light leading-relaxed">
                        <?= $accTranslation['full_description'] ?? '<p>A Casa do Gi é sinónimo de simplicidade, acolhimento e momentos de convívio marcantes.</p>' ?>
                    </div>
                </div>

                <!-- Bedrooms -->
                <div class="animate-on-scroll" data-animation="fade-up">
                    <h2 class="font-serif text-3xl text-primary mb-8 relative inline-block">
                        O Seu Descanso
                        <span class="absolute -bottom-2 left-0 w-12 h-1 bg-accent"></span>
                    </h2>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <?php if (empty($bedrooms)): ?>
                            <!-- Fallback -->
                            <div class="bg-white p-6 rounded-xl shadow-sm border border-charcoal/5">
                                <h3 class="font-serif text-lg text-primary mb-2">Quartos Indisponíveis</h3>
                                <p class="text-charcoal/70 text-sm">Informação não disponível.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($bedrooms as $index => $bedroom): ?>
                            <div class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 border border-charcoal/5">
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="w-10 h-10 bg-primary/5 rounded-full flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-cream transition-colors">
                                            <span class="font-serif font-bold"><?= $index + 1 ?></span>
                                        </div>
                                        <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 20h14M3 20V9a2 2 0 012-2h14a2 2 0 012 2v11a2 2 0 002 2H1a2 2 0 002-2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="font-serif text-xl text-primary mb-2">Quarto <?= $bedroom['bedroom_number'] ?></h3>
                                    <p class="text-charcoal/70 text-sm leading-relaxed"><?= e($bedroom['beds_description']) ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Amenities -->
                <div class="animate-on-scroll" data-animation="fade-up">
                    <h2 class="font-serif text-3xl text-primary mb-8 relative inline-block">
                        Comodidades
                        <span class="absolute -bottom-2 left-0 w-12 h-1 bg-accent"></span>
                    </h2>

                    <div class="bg-white rounded-2xl shadow-sm border border-charcoal/5 p-8">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-y-6 gap-x-4">
                            <?php
                            $displayAmenities = !empty($amenities) ? $amenities : [
                                ['icon' => 'wifi', 'name' => 'WIFI Gratuito'],
                                ['icon' => 'ac', 'name' => 'Ar Condicionado'],
                                ['icon' => 'parking', 'name' => 'Estacionamento'],
                                // ... other fallbacks
                            ];
                            
                            foreach ($displayAmenities as $amenity): 
                            ?>
                            <div class="flex items-center space-x-3 text-charcoal/80">
                                <span class="bg-secondary/10 p-2 rounded-full text-secondary flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </span>
                                <span class="text-sm font-medium"><?= e($amenity['name']) ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column - Sidebar -->
            <div class="lg:col-span-1">
                <div class="sticky top-28 space-y-8 animate-on-scroll" data-animation="fade-left" data-delay="200">
                    
                    <!-- Booking Card -->
                    <div class="bg-white rounded-2xl shadow-lg border border-accent/20 overflow-hidden relative">
                        <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-primary via-secondary to-accent"></div>
                        
                        <div class="p-8">
                            <h3 class="font-serif text-2xl text-center text-primary mb-2">Reserve a Sua Estadia</h3>
                            <p class="text-center text-charcoal/60 text-sm mb-8">Escolha a sua plataforma preferida</p>

                            <div class="space-y-4">
                                <?php if ($guestreadyUrl): ?>
                                <a href="<?= e($guestreadyUrl) ?>" target="_blank" rel="noopener noreferrer" 
                                   class="group flex items-center w-full px-6 py-4 bg-white hover:bg-[#F8F9FA] text-primary border border-charcoal/10 rounded-xl transition-all duration-300 shadow-sm hover:shadow-md">
                                    <img src="<?= asset('images/guestreadylogo.png') ?>" alt="GuestReady" class="w-8 h-8 object-contain mr-4">
                                    <span class="font-medium flex-1">GuestReady</span>
                                    <svg class="w-5 h-5 text-accent opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </a>
                                <?php endif; ?>

                                <?php if ($bookingUrl): ?>
                                <a href="<?= e($bookingUrl) ?>" target="_blank" rel="noopener noreferrer" 
                                   class="group flex items-center w-full px-6 py-4 bg-[#003580]/5 hover:bg-blue-50 text-[#003580] border border-[#003580]/10 rounded-xl transition-all duration-300 shadow-sm hover:shadow-md">
                                    <img src="<?= asset('images/bookinglogo.jpg') ?>" alt="Booking.com" class="w-8 h-8 object-contain mr-4 mix-blend-multiply rounded-sm">
                                    <span class="font-medium flex-1">Booking.com</span>
                                    <svg class="w-5 h-5 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </a>
                                <?php endif; ?>

                                <?php if ($airbnbUrl): ?>
                                <a href="<?= e($airbnbUrl) ?>" target="_blank" rel="noopener noreferrer" 
                                   class="group flex items-center w-full px-6 py-4 bg-[#FF5A5F]/5 hover:bg-red-50 text-[#FF5A5F] border border-[#FF5A5F]/10 rounded-xl transition-all duration-300 shadow-sm hover:shadow-md">
                                    <img src="<?= asset('images/airbnblogo.png') ?>" alt="Airbnb" class="w-8 h-8 object-contain mr-4">
                                    <span class="font-medium flex-1">Airbnb</span>
                                    <svg class="w-5 h-5 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </a>
                                <?php endif; ?>

                                <?php if (!$guestreadyUrl && !$bookingUrl && !$airbnbUrl): ?>
                                <div class="text-center py-4 text-charcoal/60 bg-gray-50 rounded-lg">
                                    Reservas online temporariamente indisponíveis.
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="mt-8 pt-6 border-t border-charcoal/10 text-center">
                                <p class="text-charcoal/60 text-sm mb-3">Tem alguma dúvida?</p>
                                <a href="<?= $base ?>/contactos/" class="inline-flex items-center text-secondary hover:text-secondary-700 font-medium transition-colors">
                                    Fale Connosco
                                    <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Info Card -->
                    <div class="bg-primary rounded-2xl p-8 text-cream shadow-lg">
                        <h4 class="font-serif text-xl mb-4">Informações Úteis</h4>
                        <ul class="space-y-4 text-sm text-cream/80">
                            <li class="flex justify-between border-b border-white/10 pb-2">
                                <span>Check-in</span>
                                <span class="font-semibold text-white">16:00 - 22:00</span>
                            </li>
                            <li class="flex justify-between border-b border-white/10 pb-2">
                                <span>Check-out</span>
                                <span class="font-semibold text-white">Até às 11:00</span>
                            </li>
                            <li class="flex justify-between pt-1">
                                <span>Licença AL</span>
                                <span class="font-semibold text-white"><?= e($accommodation['license_number'] ?? '146729/AL') ?></span>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<!-- Gallery Section (Bento Grid Inspired) -->
<section class="py-16 bg-white overflow-hidden" id="gallery-section">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-10 flex items-end justify-between animate-on-scroll">
            <div>
                <span class="inline-block text-accent text-sm font-medium tracking-[0.2em] uppercase mb-3">Galeria</span>
                <h2 class="font-serif text-3xl md:text-4xl text-primary">Um Olhar por Dentro</h2>
            </div>
            <!-- Optional: Link to see all photos if we had a separate page, for now just decorative or triggers modal -->
        </div>

        <?php if (!empty($galleryImages)): ?>
        <div class="grid grid-cols-1 md:grid-cols-4 md:grid-rows-2 gap-4 h-auto md:h-[600px] animate-on-scroll" data-delay="200">
            <!-- Main Image (Left, spans 2 rows, 2 cols) -->
            <?php if (isset($galleryImages[0])): ?>
            <div class="md:col-span-2 md:row-span-2 relative group rounded-2xl overflow-hidden cursor-pointer shadow-lg" onclick="openLightbox(0)">
                <img src="<?= upload(str_replace('uploads/', '', $galleryImages[0]['file_path'])) ?>" 
                     alt="<?= e($galleryImages[0]['alt_text_pt'] ?? 'Alobjamento Principal') ?>" 
                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-colors"></div>
            </div>
            <?php endif; ?>

            <!-- Second Image (Top Right, 1x1) -->
            <?php if (isset($galleryImages[1])): ?>
            <div class="md:col-span-1 md:row-span-1 relative group rounded-2xl overflow-hidden cursor-pointer shadow-lg" onclick="openLightbox(1)">
                <img src="<?= upload(str_replace('uploads/', '', $galleryImages[1]['file_path'])) ?>" 
                     alt="<?= e($galleryImages[1]['alt_text_pt'] ?? 'Alojamento') ?>" 
                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
            </div>
            <?php endif; ?>

            <!-- Third Image (Top Right, 1x1) -->
            <?php if (isset($galleryImages[2])): ?>
            <div class="md:col-span-1 md:row-span-1 relative group rounded-2xl overflow-hidden cursor-pointer shadow-lg" onclick="openLightbox(2)">
                <img src="<?= upload(str_replace('uploads/', '', $galleryImages[2]['file_path'])) ?>" 
                     alt="<?= e($galleryImages[2]['alt_text_pt'] ?? 'Alojamento') ?>" 
                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
            </div>
            <?php endif; ?>

            <!-- Fourth Image (Bottom Right, 1x1) -->
            <?php if (isset($galleryImages[3])): ?>
            <div class="md:col-span-1 md:row-span-1 relative group rounded-2xl overflow-hidden cursor-pointer shadow-lg" onclick="openLightbox(3)">
                <img src="<?= upload(str_replace('uploads/', '', $galleryImages[3]['file_path'])) ?>" 
                     alt="<?= e($galleryImages[3]['alt_text_pt'] ?? 'Alojamento') ?>" 
                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
            </div>
            <?php endif; ?>

            <!-- Fifth Image (Bottom Right, 1x1) with "See More" overlay -->
            <?php if (isset($galleryImages[4])): ?>
            <div class="md:col-span-1 md:row-span-1 relative group rounded-2xl overflow-hidden cursor-pointer shadow-lg" onclick="openLightbox(4)">
                <img src="<?= upload(str_replace('uploads/', '', $galleryImages[4]['file_path'])) ?>" 
                     alt="<?= e($galleryImages[4]['alt_text_pt'] ?? 'Alojamento') ?>" 
                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                
                <!-- "See All" Overlay -->
                <div class="absolute inset-0 bg-primary/60 group-hover:bg-primary/70 transition-colors flex flex-col items-center justify-center text-white p-4 text-center">
                    <span class="font-serif text-2xl font-bold">+ Ver mais</span>
                    <span class="text-xs uppercase tracking-wider mt-1 opacity-80">todas as fotos</span>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-12 text-charcoal/50 bg-gray-50 rounded-lg">
            <p>Galeria de imagens brevemente disponível.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Map Section (Same as Contactos) -->
<section class="relative pb-16 lg:pb-24 pt-8 bg-white">
    <!-- Bottom Gradient -->
    <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-b from-white to-cream-100"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-12 animate-on-scroll">
            <span class="inline-block text-accent text-sm font-medium tracking-[0.2em] uppercase mb-3">Localização</span>
            <h2 class="font-serif text-3xl md:text-4xl text-primary mb-4">Onde Estamos</h2>
            <p class="text-charcoal/70 max-w-2xl mx-auto">
                Visite-nos em Mogadouro, no coração de Trás-os-Montes
            </p>
        </div>

        <div class="animate-on-scroll" data-delay="200">
            <!-- Map Container -->
            <div class="relative rounded-2xl overflow-hidden shadow-xl border border-charcoal/10">
                <div id="contact-map" class="w-full h-[400px] md:h-[500px]"></div>

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
                                A Casa do Gi<br>
                                5200-207 Mogadouro
                            </p>
                            <a href="https://www.google.com/maps/dir/?api=1&destination=41.34217,-6.71347"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex items-center text-secondary hover:text-secondary-700 text-sm font-medium mt-3 group">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                                Obter direções
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

<!-- Lightbox Modal -->
<div id="lightbox-modal" class="fixed inset-0 z-[100] bg-black/95 hidden opacity-0 transition-opacity duration-300 flex items-center justify-center">
    <button onclick="closeLightbox()" class="absolute top-6 right-6 text-white/50 hover:text-white transition-colors z-[101]">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    
    <button onclick="prevImage()" class="absolute left-4 md:left-8 text-white/50 hover:text-white transition-colors z-[101]">
        <svg class="w-10 h-10 md:w-16 md:h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>

    <div class="relative w-full h-full max-w-7xl max-h-[90vh] p-4 flex items-center justify-center">
        <img id="lightbox-image" src="" class="max-w-full max-h-full object-contain rounded-lg shadow-2xl">
    </div>

    <button onclick="nextImage()" class="absolute right-4 md:right-8 text-white/50 hover:text-white transition-colors z-[101]">
        <svg class="w-10 h-10 md:w-16 md:h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/>
        </svg>
    </button>
</div>

<!-- Leaflet CSS/JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
// Gallery Data
const galleryImages = <?= json_encode(array_map(function($img) use ($base) {
    // Strip 'uploads/' if present to avoid duplication by upload() helper
    $path = str_replace('uploads/', '', $img['file_path']);
    // Filter empty paths
    if (empty($path)) return asset('images/placeholder.jpg'); // Fallback
    return upload($path);
}, $galleryImages)) ?>;
let currentImageIndex = 0;

// Lightbox Functions
function openLightbox(index) {
    currentImageIndex = index;
    updateLightboxImage();
    const modal = document.getElementById('lightbox-modal');
    modal.classList.remove('hidden');
    // small delay for opacity transition to work
    setTimeout(() => {
        modal.classList.remove('opacity-0');
    }, 10);
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const modal = document.getElementById('lightbox-modal');
    modal.classList.add('opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
    document.body.style.overflow = '';
}

function updateLightboxImage() {
    const img = document.getElementById('lightbox-image');
    // Add fade effect
    img.style.opacity = '0';
    setTimeout(() => {
        img.src = galleryImages[currentImageIndex];
        img.onload = () => {
            img.style.opacity = '1';
        };
    }, 200);
}

function nextImage() {
    currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
    updateLightboxImage();
}

function prevImage() {
    currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
    updateLightboxImage();
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (document.getElementById('lightbox-modal').classList.contains('hidden')) return;
    
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowRight') nextImage();
    if (e.key === 'ArrowLeft') prevImage();
});

// Map Initialization
document.addEventListener('DOMContentLoaded', function() {
    const lat = 41.34217;
    const lng = -6.71347;

    const map = L.map('contact-map', {
        scrollWheelZoom: false
    }).setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

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

    const marker = L.marker([lat, lng], { icon: customIcon }).addTo(map);

    marker.bindPopup(`
        <div style="text-align: center; padding: 8px;">
            <strong style="color: #264653; font-size: 14px;">A Casa do Gi</strong><br>
            <span style="color: #2D3748; font-size: 12px;">Casa do Gi</span><br>
            <span style="color: #2D3748; font-size: 12px;">5200-207 Mogadouro</span>
        </div>
    `, {
        className: 'custom-popup'
    });

    map.on('click', () => map.scrollWheelZoom.enable());
    map.on('mouseout', () => map.scrollWheelZoom.disable());
});
</script>

<style>
/* Custom popup styling from Contact Page */
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
/* IMPORTANT: Remove footer margin for this page so the map overlay flows into footer area if desired, 
   or simply to match contact page style */
footer {
    margin-top: 0 !important;
}
body {
    background-color: rgb(250 245 235 / var(--tw-bg-opacity, 1)) !important;
}
</style>

<?php include INCLUDES_PATH . '/footer.php'; ?>
