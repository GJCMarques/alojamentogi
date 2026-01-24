<?php
/**
 * A Casa do Gi - Footer Template
 */

$lang = \Core\Language::getInstance();
$currentLang = $lang->getCurrentLang();
$isEnglish = $lang->isEnglish();
$base = basePath();

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
$contactAddress = setting('contact_address', '');
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
                        <?php if ($guestreadyUrl = setting('guestready_url')): ?>
                        <a href="<?= e($guestreadyUrl) ?>" target="_blank" rel="noopener"
                           class="flex items-center p-3 bg-secondary rounded-lg hover:bg-secondary-600 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
                            <div class="w-10 h-10 bg-white/20 rounded-md flex items-center justify-center mr-3 text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs text-cream/80 uppercase tracking-wide">Direto</span>
                                <span class="text-white font-bold">GuestReady</span>
                            </div>
                        </a>
                        <?php endif; ?>

                        <?php if ($bookingUrl = setting('booking_url')): ?>
                        <!-- Booking.com Custom Button (#003580) -->
                        <a href="<?= e($bookingUrl) ?>" target="_blank" rel="noopener"
                           class="flex items-center p-3 rounded-lg hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group"
                           style="background-color: #003580;">
                            <div class="w-10 h-10 bg-white/20 rounded-md flex items-center justify-center mr-3 text-white">
                                <span class="font-bold text-lg">B.</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs text-white/80 uppercase tracking-wide">Parceiro</span>
                                <span class="text-white font-bold">Booking.com</span>
                            </div>
                        </a>
                        <?php endif; ?>

                        <?php if ($airbnbUrl = setting('airbnb_url')): ?>
                        <!-- Airbnb Custom Button (#FF385C) -->
                        <a href="<?= e($airbnbUrl) ?>" target="_blank" rel="noopener"
                           class="flex items-center p-3 rounded-lg hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group"
                           style="background-color: #FF385C;">
                            <div class="w-10 h-10 bg-white/20 rounded-md flex items-center justify-center mr-3 text-white">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22.519,4.427C21.949,3.879,21.166,3.606,20.252,3.606c-0.494,0-0.965,0.082-1.401,0.245 c-0.638,0.24-1.258,0.704-1.849,1.383c-1.302,1.496-2.924,4.421-4.996,8.995c-2.071-4.573-3.694-7.498-4.996-8.995 C6.42,4.555,5.801,4.09,5.163,3.851C4.727,3.688,4.256,3.606,3.762,3.606c-0.914,0-1.697,0.273-2.267,0.821 C0.804,5.15,0.463,6.29,0.463,7.96c0,1.935,0.49,4.259,1.455,6.905c1.474,4.043,4.646,7.575,8.933,9.947l1.155,0.64l1.155-0.64 c4.287-2.372,7.459-5.904,8.933-9.947c0.965-2.646,1.455-4.97,1.455-6.905C23.547,6.29,23.206,5.15,22.519,4.427L22.519,4.427z"/>
                                </svg>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs text-white/80 uppercase tracking-wide">Parceiro</span>
                                <span class="text-white font-bold">Airbnb</span>
                            </div>
                        </a>
                        <?php endif; ?>
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

    <!-- Scripts -->
    <script>
        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenuClose = document.getElementById('mobile-menu-close');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.add('open');
                document.body.style.overflow = 'hidden';
            });

            mobileMenuClose.addEventListener('click', () => {
                mobileMenu.classList.remove('open');
                document.body.style.overflow = '';
            });
        }

        // Header Background on Scroll
        const header = document.getElementById('main-header');
        if (header) {
            const updateHeader = () => {
                if (window.scrollY > 50) {
                    header.classList.add('bg-cream-50/95', 'backdrop-blur-sm', 'shadow-sm');
                } else {
                    header.classList.remove('bg-cream-50/95', 'backdrop-blur-sm', 'shadow-sm');
                }
            };
            window.addEventListener('scroll', updateHeader);
            updateHeader();
        }

        // Auto-hide flash messages
        const flashMessages = document.querySelectorAll('.flash-message');
        flashMessages.forEach(msg => {
            setTimeout(() => {
                msg.style.opacity = '0';
                msg.style.transform = 'translateX(100%)';
                setTimeout(() => msg.remove(), 300);
            }, 5000);
        });

        // Update cart count from server
        const updateCartCount = (count) => {
            const badges = document.querySelectorAll('.cart-count, #cart-count');
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
                updateCartCount(data.cart.total_quantity || 0);
            }
        })
        .catch(() => {});
        <?php endif; ?>
    </script>

    <!-- Page-specific scripts -->
    <?php if (isset($pageScripts)): ?>
    <?= $pageScripts ?>
    <?php endif; ?>
</body>
</html>
