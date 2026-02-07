<?php
/**
 * A Casa do Gi - Footer Template
 */

$lang = \Core\Language::getInstance();
$currentLang = $lang->getCurrentLang();
$isEnglish = $lang->isEnglish();
$base = basePath();

// Get accommodation booking URLs
$db = \Core\Database::getInstance();
$casa1 = $db->fetch(
    "SELECT a.*, at.name
     FROM accommodation a
     LEFT JOIN accommodation_translations at ON a.id = at.accommodation_id AND at.language_id = ?
     WHERE a.accommodation_number = 1",
    [$lang->getCurrentLangId()]
);
$casa2 = $db->fetch(
    "SELECT a.*, at.name
     FROM accommodation a
     LEFT JOIN accommodation_translations at ON a.id = at.accommodation_id AND at.language_id = ?
     WHERE a.accommodation_number = 2",
    [$lang->getCurrentLangId()]
);

// Quick links
$quickLinks = $isEnglish ? [
    ['url' => $base . '/en/accommodation/', 'label' => 'Accommodation'],
    ['url' => $base . '/en/shop/', 'label' => 'Regional Products'],
    ['url' => $base . '/en/activities/', 'label' => 'Things To Do'],
    ['url' => $base . '/en/contact/', 'label' => 'Contact Us'],
] : [
    ['url' => $base . '/alojamento/', 'label' => 'Alojamento'],
    ['url' => $base . '/loja/', 'label' => 'Produtos Regionais'],
    ['url' => $base . '/atividades/', 'label' => 'O Que Fazer'],
    ['url' => $base . '/contactos/', 'label' => 'Contactos'],
];

// Get settings
$siteName = setting('site_name', 'A Casa do Gi');
$contactEmail = setting('contact_email', '');
$contactPhone = setting('contact_phone', '');
$contactAddress = '52 Avenida Nossa Senhora do Caminho, Mogadouro';
$facebookUrl = setting('facebook_url', '');
$instagramUrl = setting('instagram_url', '');
?>
    </main>

    <!-- Footer -->
    <footer class="bg-primary text-cream-100 mt-20 border-t border-accent/20">
        <!-- Main Footer -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                <!-- Brand Column -->
                <div class="lg:col-span-1">
                    <h3 class="font-cursive text-4xl text-cream mb-6">A Casa do Gi</h3>
                    <p class="text-cream-200 leading-relaxed mb-6 font-light">
                        <?= $isEnglish
                            ? 'Simplicity, warmth and love in Mogadouro, Portugal.'
                            : 'Simplicidade, acolhimento e muito amor em Mogadouro, Portugal.' ?>
                    </p>
                    <!-- Social Links -->
                    <?php if ($facebookUrl || $instagramUrl): ?>
                    <div class="flex space-x-4">
                        <?php if ($facebookUrl): ?>
                        <a href="<?= e($facebookUrl) ?>" target="_blank" rel="noopener noreferrer"
                           class="w-12 h-12 rounded-full bg-cream/10 flex items-center justify-center text-cream hover:bg-secondary hover:text-white transition-all duration-300 hover:-translate-y-1"
                           aria-label="Facebook">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <?php endif; ?>
                        <?php if ($instagramUrl): ?>
                        <a href="<?= e($instagramUrl) ?>" target="_blank" rel="noopener noreferrer"
                           class="w-12 h-12 rounded-full bg-cream/10 flex items-center justify-center text-cream hover:bg-secondary hover:text-white transition-all duration-300 hover:-translate-y-1"
                           aria-label="Instagram">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                            </svg>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="font-serif text-xl font-bold text-cream mb-6 border-b border-accent/30 pb-2 inline-block">
                        <?= $isEnglish ? 'Quick Links' : 'Links Rápidos' ?>
                    </h4>
                    <ul class="space-y-4">
                        <?php foreach ($quickLinks as $link): ?>
                        <li>
                            <a href="<?= $link['url'] ?>" class="text-cream-200 hover:text-accent transition-all duration-300 inline-flex items-center group">
                                <span class="w-1.5 h-1.5 rounded-full bg-accent mr-2 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                <?= e($link['label']) ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="font-serif text-xl font-bold text-cream mb-6 border-b border-accent/30 pb-2 inline-block">
                        <?= $isEnglish ? 'Contact' : 'Contacto' ?>
                    </h4>
                    <ul class="space-y-4">
                        <?php if ($contactAddress): ?>
                        <li class="flex items-start space-x-3 group">
                            <div class="mt-1 p-1 bg-cream/10 rounded-md group-hover:bg-accent transition-colors">
                                <svg class="w-4 h-4 text-cream" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <span class="text-cream-200 leading-tight"><?= nl2br(e($contactAddress)) ?></span>
                        </li>
                        <?php endif; ?>
                        <?php if ($contactPhone): ?>
                        <li class="flex items-center space-x-3 group">
                            <div class="p-1 bg-cream/10 rounded-md group-hover:bg-accent transition-colors">
                                <svg class="w-4 h-4 text-cream" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <a href="tel:<?= e(preg_replace('/\s+/', '', $contactPhone)) ?>" class="text-cream-200 hover:text-white transition-colors">
                                <?= e($contactPhone) ?>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if ($contactEmail): ?>
                        <li class="flex items-center space-x-3 group">
                            <div class="p-1 bg-cream/10 rounded-md group-hover:bg-accent transition-colors">
                                <svg class="w-4 h-4 text-cream" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <a href="mailto:<?= e($contactEmail) ?>" class="text-cream-200 hover:text-white transition-colors">
                                <?= e($contactEmail) ?>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Book Now -->
                <div>
                    <h4 class="font-serif text-xl font-bold text-cream mb-6 border-b border-accent/30 pb-2 inline-block">
                        <?= $isEnglish ? 'Book Now' : 'Reserve Já' ?>
                    </h4>
                    <div class="space-y-4">
                        <button onclick="openBookingModal('guestready')" type="button"
                           class="w-full flex items-center p-3 bg-[#FAF9F6] border border-[#800020]/10 rounded-lg hover:bg-[#EAE8E0] hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-pointer">
                            <div class="w-10 h-10 bg-[#800020]/10 rounded-md flex items-center justify-center mr-3 text-white p-1">
                                <!-- GuestReady Logo -->
                                <img src="<?= $base ?>/assets/images/guestreadylogo.png" alt="GuestReady" class="w-full h-full object-contain">
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs text-[#800020]/80 uppercase tracking-wide">Parceiro</span>
                                <span class="text-[#800020] font-bold">GuestReady</span>
                            </div>
                        </button>

                        <!-- Booking.com Custom Button (#003580) -->
                        <button onclick="openBookingModal('booking')" type="button"
                           class="w-full flex items-center p-3 rounded-lg hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-pointer"
                           style="background-color: #003580;">
                            <div class="w-10 h-10 bg-white/20 rounded-md flex items-center justify-center mr-3 text-white p-1">
                                <img src="<?= $base ?>/assets/images/bookinglogo.jpg" alt="Booking.com" class="w-full h-full object-contain">
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs text-white/80 uppercase tracking-wide">Parceiro</span>
                                <span class="text-white font-bold">Booking.com</span>
                            </div>
                        </button>

                        <!-- Airbnb Custom Button (#FF385C) -->
                        <button onclick="openBookingModal('airbnb')" type="button"
                           class="w-full flex items-center p-3 rounded-lg hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-pointer"
                           style="background-color: #FF385C;">
                            <div class="w-10 h-10 bg-white/20 rounded-md flex items-center justify-center mr-3 text-white p-1">
                                <img src="<?= $base ?>/assets/images/airbnblogo.png" alt="Airbnb" class="w-full h-full object-contain">
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs text-white/80 uppercase tracking-wide">Parceiro</span>
                                <span class="text-white font-bold">Airb nb</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-accent/20 bg-primary-700/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">
                    <p class="text-cream-300 text-sm font-light">
                        &copy; <?= date('Y') ?> <?= e($siteName) ?>. <?= $isEnglish ? 'All rights reserved.' : 'Todos os direitos reservados.' ?>
                    </p>
                    <div class="flex items-center space-x-6 text-sm">
                        <a href="<?= $lang->url('politica-privacidade') ?>" class="text-cream-300 hover:text-accent transition-colors">
                            <?= $isEnglish ? 'Privacy Policy' : 'Política de Privacidade' ?>
                        </a>
                        <a href="<?= $lang->url('termos-condicoes') ?>" class="text-cream-300 hover:text-accent transition-colors">
                            <?= $isEnglish ? 'Terms & Conditions' : 'Termos e Condicoes' ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Booking Modal -->
    <div id="bookingModal" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-[100] px-4">
        <div class="max-w-5xl w-full max-h-[90vh] overflow-hidden">
            <div class="bg-primary rounded-t-2xl">
                <!-- Modal Header -->
                <div class="sticky top-0 p-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-serif font-bold text-white"><?= $isEnglish ? 'Choose Your Casa' : 'Escolha a Sua Casa' ?></h3>
                        <p class="text-cream-200 text-sm mt-1"><?= $isEnglish ? 'Select which accommodation to book' : 'Selecione qual alojamento quer reservar' ?></p>
                    </div>
                    <button onclick="closeBookingModal()" class="text-white/80 hover:text-white p-2 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-8 bg-cream-50 rounded-b-2xl shadow-2xl">
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Casa do Gi 1 -->
                    <div class="bg-white p-6 rounded-xl">
                        <h4 class="font-serif text-lg font-bold text-primary mb-4 pb-3 border-b"><?= e($casa1['name'] ?? 'A Casa do Gi 1') ?></h4>

                        <div class="space-y-3">
                            <?php if (!empty($casa1['guestready_url'])): ?>
                            <a href="<?= e($casa1['guestready_url']) ?>" target="_blank" rel="noopener"
                               class="flex items-center p-3 bg-[#FAF9F6] hover:bg-[#EAE8E0] transition-colors group w-full rounded-lg">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 p-1">
                                    <img src="<?= $base ?>/assets/images/guestreadylogo.png" alt="GuestReady" class="w-full h-full object-contain">
                                </div>
                                <div class="flex-1">
                                    <span class="text-[#800020] font-semibold">GuestReady</span>
                                </div>
                                <svg class="w-4 h-4 text-[#800020]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                            <?php endif; ?>

                            <?php if (!empty($casa1['booking_url'])): ?>
                            <a href="<?= e($casa1['booking_url']) ?>" target="_blank" rel="noopener"
                               class="flex items-center p-3 transition-opacity hover:opacity-90 group w-full rounded-lg"
                               style="background-color: #003580;">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 p-1">
                                    <img src="<?= $base ?>/assets/images/bookinglogo.jpg" alt="Booking.com" class="w-full h-full object-contain">
                                </div>
                                <div class="flex-1">
                                    <span class="text-white font-semibold">Booking.com</span>
                                </div>
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                            <?php endif; ?>

                            <?php if (!empty($casa1['airbnb_url'])): ?>
                            <a href="<?= e($casa1['airbnb_url']) ?>" target="_blank" rel="noopener"
                               class="flex items-center p-3 transition-opacity hover:opacity-90 group w-full rounded-lg"
                               style="background-color: #FF385C;">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 p-1">
                                    <img src="<?= $base ?>/assets/images/airbnblogo.png" alt="Airbnb" class="w-full h-full object-contain">
                                </div>
                                <div class="flex-1">
                                    <span class="text-white font-semibold">Airbnb</span>
                                </div>
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Casa do Gi 2 -->
                    <div class="bg-white p-6 rounded-xl">
                        <h4 class="font-serif text-lg font-bold text-primary mb-4 pb-3 border-b"><?= e($casa2['name'] ?? 'A Casa do Gi 2') ?></h4>

                        <div class="space-y-3">
                            <?php if (!empty($casa2['guestready_url'])): ?>
                            <a href="<?= e($casa2['guestready_url']) ?>" target="_blank" rel="noopener"
                               class="flex items-center p-3 bg-[#FAF9F6] hover:bg-[#EAE8E0] transition-colors group w-full rounded-lg">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 p-1">
                                    <img src="<?= $base ?>/assets/images/guestreadylogo.png" alt="GuestReady" class="w-full h-full object-contain">
                                </div>
                                <div class="flex-1">
                                    <span class="text-[#800020] font-semibold">GuestReady</span>
                                </div>
                                <svg class="w-4 h-4 text-[#800020]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                            <?php endif; ?>

                            <?php if (!empty($casa2['booking_url'])): ?>
                            <a href="<?= e($casa2['booking_url']) ?>" target="_blank" rel="noopener"
                               class="flex items-center p-3 transition-opacity hover:opacity-90 group w-full rounded-lg"
                               style="background-color: #003580;">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 p-1">
                                    <img src="<?= $base ?>/assets/images/bookinglogo.jpg" alt="Booking.com" class="w-full h-full object-contain">
                                </div>
                                <div class="flex-1">
                                    <span class="text-white font-semibold">Booking.com</span>
                                </div>
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                            <?php endif; ?>

                            <?php if (!empty($casa2['airbnb_url'])): ?>
                            <a href="<?= e($casa2['airbnb_url']) ?>" target="_blank" rel="noopener"
                               class="flex items-center p-3 transition-opacity hover:opacity-90 group w-full rounded-lg"
                               style="background-color: #FF385C;">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 p-1">
                                    <img src="<?= $base ?>/assets/images/airbnblogo.png" alt="Airbnb" class="w-full h-full object-contain">
                                </div>
                                <div class="flex-1">
                                    <span class="text-white font-semibold">Airbnb</span>
                                </div>
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Auto-hide flash messages
        const flashMessages = document.querySelectorAll('.flash-message');
        flashMessages.forEach(msg => {
            setTimeout(() => {
                msg.style.opacity = '0';
                msg.style.transform = 'translateX(100%)';
                setTimeout(() => msg.remove(), 300);
            }, 5000);
        });

        // Update cart count from server (global function)
        window.updateCartCount = (count) => {
            const badges = document.querySelectorAll('.cart-count, #cart-count, #mobile-cart-count');
            badges.forEach(badge => {
                if (count > 0) {
                    badge.textContent = count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            });
        };

        // Fetch cart on page load
        <?php if (isShopEnabled()): ?>
        fetch('<?= $base ?>/api/cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({ action: 'get' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.cart) {
                window.updateCartCount(data.cart.total_quantity || 0);
            }
        })
        .catch(() => {});
        <?php endif; ?>

        // Booking Modal Functions
        function openBookingModal(platform) {
            const modal = document.getElementById('bookingModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeBookingModal() {
            const modal = document.getElementById('bookingModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }
        }

        // Close modal when clicking outside
        document.getElementById('bookingModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeBookingModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeBookingModal();
            }
        });
    </script>

    <!-- Page-specific scripts -->
    <?php if (isset($pageScripts)): ?>
    <?= $pageScripts ?>
    <?php endif; ?>
</body>
</html>
