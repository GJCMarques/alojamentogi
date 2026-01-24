<?php
/**
 * A Casa do Gi - Accommodation Page (English)
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';

use Core\Database;
use Core\Language;

// Force English language
Language::getInstance()->setLanguage(LANG_EN);
$lang = Language::getInstance();
$db = Database::getInstance();
$base = basePath();

// Get accommodation data
$accommodation = $db->fetch("SELECT * FROM accommodation LIMIT 1");
$accTranslation = $db->fetch(
    "SELECT * FROM accommodation_translations WHERE accommodation_id = ? AND language_id = ?",
    [$accommodation['id'] ?? 1, $lang->getCurrentLangId()]
);

// Get amenities (Correct query using updated schema)
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
$pageTitle = $accTranslation['title'] ?? 'Accommodation';
$pageDescription = $accTranslation['short_description'] ?? 'Local accommodation in Mogadouro.';

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
                Welcome to
            </span>
            <h1 class="font-cursive text-6xl md:text-7xl lg:text-8xl text-cream drop-shadow-2xl leading-none">
                <?= e($accTranslation['title'] ?? 'A Casa do Gi') ?>
            </h1>
            <p class="font-serif text-xl md:text-2xl text-cream/90 max-w-2xl mx-auto italic font-light leading-relaxed mt-6 drop-shadow-md">
                "<?= e($accTranslation['short_description'] ?? 'Holiday home in Mogadouro') ?>"
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
                <div class="text-cream-200 text-sm">Guests</div>
            </div>
            <div class="text-cream">
                <div class="text-2xl font-bold"><?= $accommodation['bedrooms'] ?? 3 ?></div>
                <div class="text-cream-200 text-sm">Bedrooms</div>
            </div>
            <div class="text-cream">
                <div class="text-2xl font-bold"><?= $accommodation['bathrooms'] ?? 2 ?></div>
                <div class="text-cream-200 text-sm">Bathrooms</div>
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
                    <h2 class="font-serif text-2xl md:text-3xl text-primary mb-6">About the Accommodation</h2>
                    <div class="prose prose-lg text-charcoal max-w-none">
                        <?= $accTranslation['full_description'] ?? '<p>A Casa do Gi is synonymous with simplicity, warmth, memorable moments, family joy, fun, laughter, and lots of love!</p>' ?>
                    </div>
                </div>

                <!-- Gallery -->
                <div>
                    <h2 class="font-serif text-2xl md:text-3xl text-primary mb-6">Gallery</h2>
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
                                     alt="<?= e($image['alt_text_en'] ?? 'A Casa do Gi') ?>"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                     loading="lazy">
                            </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Bedrooms -->
                <div>
                    <h2 class="font-serif text-2xl md:text-3xl text-primary mb-6">Bedrooms</h2>
                    <div class="grid md:grid-cols-3 gap-6">
                        <?php if (empty($bedrooms)): ?>
                        <div class="bg-cream p-6 rounded-lg shadow-md border border-accent/20">
                            <h3 class="font-semibold text-primary mb-2">Bedroom 1</h3>
                            <p class="text-charcoal text-sm">2 Single beds</p>
                        </div>
                        <div class="bg-cream p-6 rounded-lg shadow-md border border-accent/20">
                            <h3 class="font-semibold text-primary mb-2">Bedroom 2</h3>
                            <p class="text-charcoal text-sm">Single sofa-bed, Double bed</p>
                        </div>
                        <div class="bg-cream p-6 rounded-lg shadow-md border border-accent/20">
                            <h3 class="font-semibold text-primary mb-2">Bedroom 3</h3>
                            <p class="text-charcoal text-sm">Double bed</p>
                        </div>
                        <?php else: ?>
                            <?php foreach ($bedrooms as $bedroom): ?>
                            <div class="bg-cream p-6 rounded-lg shadow-md border border-accent/20">
                                <h3 class="font-semibold text-primary mb-2">Bedroom <?= $bedroom['bedroom_number'] ?></h3>
                                <p class="text-charcoal text-sm"><?= e($bedroom['beds_description']) ?></p>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Amenities -->
                <div>
                    <h2 class="font-serif text-2xl md:text-3xl text-primary mb-6">Amenities</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <?php if (empty($amenities)): ?>
                        <p class="col-span-3 text-charcoal">Amenities details available soon.</p>
                        <?php else: ?>
                            <?php foreach ($amenities as $amenity): ?>
                            <div class="flex items-center space-x-3 p-3 bg-cream rounded-lg shadow-md border border-accent/20">
                                <div class="w-10 h-10 bg-secondary/10 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="text-charcoal text-sm"><?= e($amenity['name']) ?></span>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column - Booking Card -->
            <div class="lg:col-span-1">
                <div class="sticky top-24">
                    <div class="bg-cream rounded-lg shadow-xl p-6 border border-accent/30">
                        <div class="text-center mb-6">
                            <p class="text-secondary font-medium mb-2">Full refund up to 5 days before arrival</p>
                            <div class="flex items-center justify-center space-x-2 text-charcoal">
                                <svg class="w-5 h-5 text-accent" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="font-semibold">4.8</span>
                                <span class="text-charcoal-400">|</span>
                                <span>Guest Favorites</span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <h3 class="font-serif text-lg text-primary text-center mb-4">Book Your Stay</h3>

                            <?php if ($guestreadyUrl): ?>
                            <a href="<?= e($guestreadyUrl) ?>" target="_blank" rel="noopener"
                               class="flex items-center p-3 bg-secondary rounded-lg hover:bg-secondary-600 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
                                <div class="w-10 h-10 bg-white/20 rounded-md flex items-center justify-center mr-3 text-white">
                                    <!-- GuestReady / Direct Key Icon -->
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </div>
                                <div class="flex flex-col text-left">
                                    <span class="text-xs text-cream/80 uppercase tracking-wide">Direct</span>
                                    <span class="text-white font-bold">GuestReady</span>
                                </div>
                            </a>
                            <?php endif; ?>

                            <?php if ($bookingUrl): ?>
                            <a href="<?= e($bookingUrl) ?>" target="_blank" rel="noopener"
                               class="flex items-center p-3 rounded-lg hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group"
                               style="background-color: #003580;">
                                <div class="w-10 h-10 bg-white/20 rounded-md flex items-center justify-center mr-3 text-white">
                                    <svg class="h-5 w-auto" viewbox="0 0 24 24" fill="none">
                                         <path d="M4 3C2.89543 3 2 3.89543 2 5V19C2 20.1046 2.89543 21 4 21H13V15H11V13H13V3H4Z" fill="white"/>
                                         <path d="M19 8C19.5523 8 20 8.44772 20 9V11C20 11.5523 19.5523 12 19 12H15V8H19Z" fill="white"/>
                                         <path d="M19 14C19.5523 14 20 14.4477 20 15V19C20 19.5523 19.5523 20 19 20H15V14H19Z" fill="white"/>
                                    </svg> 
                                </div>
                                <div class="flex flex-col text-left">
                                    <span class="text-xs text-white/80 uppercase tracking-wide">Partner</span>
                                    <span class="text-white font-bold">Booking.com</span>
                                </div>
                            </a>
                            <?php endif; ?>

                            <?php if ($airbnbUrl): ?>
                            <a href="<?= e($airbnbUrl) ?>" target="_blank" rel="noopener"
                               class="flex items-center p-3 rounded-lg hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group"
                               style="background-color: #FF385C;">
                                <div class="w-10 h-10 bg-white/20 rounded-md flex items-center justify-center mr-3 text-white">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M22.519,4.427C21.949,3.879,21.166,3.606,20.252,3.606c-0.494,0-0.965,0.082-1.401,0.245 c-0.638,0.24-1.258,0.704-1.849,1.383c-1.302,1.496-2.924,4.421-4.996,8.995c-2.071-4.573-3.694-7.498-4.996-8.995 C6.42,4.555,5.801,4.09,5.163,3.851C4.727,3.688,4.256,3.606,3.762,3.606c-0.914,0-1.697,0.273-2.267,0.821 C0.804,5.15,0.463,6.29,0.463,7.96c0,1.935,0.49,4.259,1.455,6.905c1.474,4.043,4.646,7.575,8.933,9.947l1.155,0.64l1.155-0.64 c4.287-2.372,7.459-5.904,8.933-9.947c0.965-2.646,1.455-4.97,1.455-6.905C23.547,6.29,23.206,5.15,22.519,4.427L22.519,4.427z"/>
                                    </svg>
                                </div>
                                <div class="flex flex-col text-left">
                                    <span class="text-xs text-white/80 uppercase tracking-wide">Partner</span>
                                    <span class="text-white font-bold">Airbnb</span>
                                </div>
                            </a>
                            <?php endif; ?>

                            <?php if (!$guestreadyUrl && !$bookingUrl && !$airbnbUrl): ?>
                            <p class="text-center text-charcoal-600 text-sm">
                                Contact us for more information about bookings.
                            </p>
                            <a href="<?= $base ?>/en/contact/" class="flex items-center justify-center w-full py-3 px-4 bg-secondary text-cream font-semibold rounded-lg hover:bg-secondary-600 transition-all shadow-md hover:shadow-lg hover:scale-105">
                                Contact Us
                            </a>
                            <?php endif; ?>
                        </div>

                        <div class="mt-6 pt-6 border-t border-accent/20">
                            <div class="space-y-3 text-sm text-charcoal">
                                <div class="flex justify-between">
                                    <span>Check-in</span>
                                    <span class="font-medium">After <?= date('H:i', strtotime($accommodation['check_in_time'] ?? '16:00')) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Check-out</span>
                                    <span class="font-medium">Before <?= date('H:i', strtotime($accommodation['check_out_time'] ?? '11:00')) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>License</span>
                                    <span class="font-medium"><?= e($accommodation['license_number'] ?? '146729/AL') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Card -->
                    <div class="mt-6 bg-primary rounded-lg p-6 text-cream shadow-lg">
                        <h3 class="font-serif text-lg mb-4">Questions?</h3>
                        <p class="text-cream-200 text-sm mb-4">We are available to help with any additional information.</p>
                        <a href="<?= $base ?>/en/contact/" class="inline-flex items-center text-accent hover:text-accent-300 text-sm font-medium transition-colors">
                            Send Message
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
            <h2 class="font-serif text-2xl md:text-3xl text-primary mb-4">Location</h2>
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
