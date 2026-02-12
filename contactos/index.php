<?php

require_once dirname(__DIR__) . '/includes/init.php';

use Core\Database;
use Core\Language;
use Core\Validator;
use Core\Mailer;
use Core\CSRF;
use Core\Session;

$lang = Language::getInstance();
$db = Database::getInstance();
$base = basePath();

$formEnabled = isContactFormEnabled();

$success = false;
$errors = [];
$formData = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'subject' => '',
    'message' => '',
];

if (isPost() && $formEnabled) {

    if (!CSRF::isValid()) {
        $errors['csrf'] = 'Sessão expirada. Por favor, recarregue a página.';
    } else {

        if (!empty($_POST['website'])) {

            $success = true;
        } else {

            $formData = [
                'name' => sanitize(post('name', '')),
                'email' => sanitizeEmail(post('email', '')),
                'phone' => sanitize(post('phone', '')),
                'subject' => sanitize(post('subject', '')),
                'message' => sanitize(post('message', '')),
            ];

            $validator = Validator::make($formData, [
                'name' => 'required|min:2|max:100',
                'email' => 'required|email|max:255',
                'phone' => 'phone|max:20',
                'subject' => 'max:255',
                'message' => 'required|min:10|max:5000',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
            } else {

                $recentSubmissions = $db->count(
                    'contact_submissions',
                    "ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
                    [getClientIp()]
                );

                if ($recentSubmissions >= 3) {
                    $errors['limit'] = 'Demasiadas submissões. Por favor, aguarde um pouco antes de tentar novamente.';
                } else {

                    $isSpamEmail = $db->fetch(
                        "SELECT id FROM spam_emails WHERE email = ?",
                        [$formData['email']]
                    );

                    $db->insert('contact_submissions', [
                        'name' => $formData['name'],
                        'email' => $formData['email'],
                        'phone' => $formData['phone'] ?: null,
                        'subject' => $formData['subject'] ?: null,
                        'message' => $formData['message'],
                        'ip_address' => getClientIp(),
                        'user_agent' => substr(getUserAgent(), 0, 500),
                        'language' => $lang->getCurrentLang(),
                        'is_spam' => $isSpamEmail ? 1 : 0,
                    ]);

                    $mailer = new Mailer();
                    $mailer->sendContactNotification($formData);
                    $mailer->sendContactConfirmation($formData);

                    $success = true;

                    $formData = [
                        'name' => '',
                        'email' => '',
                        'phone' => '',
                        'subject' => '',
                        'message' => '',
                    ];

                    Session::flash('success', content('contact_success_message', 'Mensagem enviada com sucesso! Entraremos em contacto brevemente.'));
                }
            }
        }
    }
}

$contactEmail = setting('contact_email', '');
$contactPhone = setting('contact_phone', '');
$contactAddress = '52 Avenida Nossa Senhora do Caminho, Mogadouro';

$pageHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = 'contact' AND is_active = 1");
$heroMedia = $pageHero ? $db->fetch("SELECT * FROM media WHERE entity_type = 'hero' AND entity_id = ? AND is_cover = 1", [$pageHero['id']]) : null;
$heroImage = $heroMedia['file_path'] ?? 'images/MogadouroContacto.jpg';
$heroOverlay = $pageHero['hero_overlay_opacity'] ?? 0.40;

$heroUrl = $heroImage[0] === '/' ? basePath() . $heroImage : asset($heroImage);

$pageTitle = __('contact_title', 'Contactos');
$pageDescription = 'Entre em contacto com A Casa do Gi. Estamos disponíveis para responder às suas questões.';

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="relative h-screen md:h-[75vh] min-h-[600px] flex items-center bg-primary overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat bg-fixed"
             style="background-image: url('<?= $heroUrl ?>');">
        </div>
        <div class="absolute inset-0 bg-black" style="opacity: <?= $heroOverlay ?>"></div>
    </div>

    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <span class="inline-block text-accent text-lg font-medium tracking-[0.2em] uppercase mb-4 animate-on-scroll" data-animation="fade-up">
            <?= content('contact_hero_tagline') ?>
        </span>
        <h1 class="font-cursive text-6xl md:text-7xl lg:text-8xl text-cream mb-6 drop-shadow-lg animate-on-scroll" data-animation="fade-up" data-delay="200">
            <?= content('contact_hero_title') ?>
        </h1>
        <p class="text-xl md:text-2xl text-cream/90 max-w-2xl mx-auto font-light leading-relaxed animate-on-scroll" data-animation="fade-up" data-delay="400">
            <?= content('contact_hero_subtitle') ?>
        </p>
    </div>
</section>

<!-- Main Content -->
<section class="py-16 lg:py-24 bg-cream-100 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-12">
            <!-- Contact Information -->
            <div class="lg:col-span-1 animate-on-scroll" data-animation="fade-right">
                <h2 class="font-serif text-2xl text-primary mb-6">Informações de Contacto</h2>

                <div class="space-y-6 animate-slide-right">
                    <?php if ($contactAddress): ?>
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-accent/10 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-primary mb-1">Morada</h3>
                            <p class="text-charcoal"><?= nl2br(e($contactAddress)) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($contactPhone): ?>
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-accent/10 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-primary mb-1">Telefone</h3>
                            <a href="tel:<?= e(preg_replace('/\s+/', '', $contactPhone)) ?>" class="text-charcoal hover:text-secondary transition-colors">
                                <?= e($contactPhone) ?>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($contactEmail): ?>
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-accent/10 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-primary mb-1">Email</h3>
                            <a href="mailto:<?= e($contactEmail) ?>" class="text-charcoal hover:text-secondary transition-colors">
                                <?= e($contactEmail) ?>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Booking Links -->
                <div class="mt-10 p-6 bg-primary rounded-lg animate-on-scroll" data-animation="fade-up" data-delay="300">
                    <h3 class="font-serif text-lg text-cream mb-4">Quer fazer uma reserva?</h3>
                    <p class="text-cream/80 text-sm mb-4">
                        Reserve a sua estadia através das nossas plataformas parceiras.
                    </p>
                    <a href="<?= $base ?>/alojamento/" class="inline-flex items-center text-accent hover:text-accent/70 text-sm font-medium">
                        Ver opções de reserva
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="lg:col-span-2 animate-on-scroll" data-animation="fade-left" data-delay="200">
                <div class="bg-white rounded-lg shadow-sm p-8 border border-charcoal/20">
                    <h2 class="font-serif text-2xl text-primary mb-6">Envie-nos uma Mensagem</h2>

                    <?php if (!$formEnabled): ?>
                    <div class="p-6 bg-charcoal/10 rounded-lg text-center">
                        <svg class="w-12 h-12 mx-auto text-charcoal/60 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-charcoal">O formulário de contacto está temporariamente indisponível.</p>
                        <p class="text-charcoal/70 text-sm mt-2">Por favor, contacte-nos através do email ou telefone.</p>
                    </div>
                    <?php elseif ($success): ?>
                    <div class="p-8 bg-accent/5 rounded-lg text-center border border-accent/20">
                        <div class="w-16 h-16 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h3 class="font-serif text-xl text-primary mb-2">Mensagem Enviada!</h3>
                        <p class="text-charcoal mb-4">Obrigado pelo seu contacto. Iremos responder o mais brevemente possível.</p>
                        <a href="<?= $base ?>/contactos/" class="inline-flex items-center text-secondary hover:text-olive-700 font-medium">
                            Enviar nova mensagem
                        </a>
                    </div>
                    <?php else: ?>

                    <?php if (!empty($errors)): ?>
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="font-medium text-red-700">Por favor, corrija os erros abaixo:</p>
                                <ul class="mt-2 text-sm text-red-600 list-disc list-inside">
                                    <?php foreach ($errors as $field => $fieldErrors): ?>
                                        <?php foreach ((array)$fieldErrors as $error): ?>
                                        <li><?= e($error) ?></li>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="" class="space-y-6">
                        <?= CSRF::tokenField() ?>

                        <!-- Honeypot field (hidden) -->
                        <div style="display:none;" aria-hidden="true">
                            <label for="website">Website</label>
                            <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-primary mb-2">
                                    Nome <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       value="<?= e($formData['name']) ?>"
                                       required
                                       class="w-full px-4 py-3 border border-charcoal/20 rounded focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-colors <?= isset($errors['name']) ? 'border-terracotta-500' : '' ?>"
                                       placeholder="O seu nome">
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-primary mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       value="<?= e($formData['email']) ?>"
                                       required
                                       class="w-full px-4 py-3 border border-charcoal/20 rounded focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-colors <?= isset($errors['email']) ? 'border-terracotta-500' : '' ?>"
                                       placeholder="O seu email">
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-primary mb-2">
                                    Telefone
                                </label>
                                <input type="tel"
                                       id="phone"
                                       name="phone"
                                       value="<?= e($formData['phone']) ?>"
                                       class="w-full px-4 py-3 border border-charcoal/20 rounded focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-colors"
                                       placeholder="+351 XXX XXX XXX">
                            </div>

                            <!-- Subject -->
                            <div>
                                <label for="subject" class="block text-sm font-medium text-primary mb-2">
                                    Assunto
                                </label>
                                <input type="text"
                                       id="subject"
                                       name="subject"
                                       value="<?= e($formData['subject']) ?>"
                                       class="w-full px-4 py-3 border border-charcoal/20 rounded focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-colors"
                                       placeholder="Assunto da mensagem">
                            </div>
                        </div>

                        <!-- Message -->
                        <div>
                            <label for="message" class="block text-sm font-medium text-primary mb-2">
                                Mensagem <span class="text-red-500">*</span>
                            </label>
                            <textarea id="message"
                                      name="message"
                                      rows="6"
                                      required
                                      class="w-full px-4 py-3 border border-charcoal/20 rounded focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-colors resize-none <?= isset($errors['message']) ? 'border-terracotta-500' : '' ?>"
                                      placeholder="A sua mensagem..."><?= e($formData['message']) ?></textarea>
                        </div>

                        <div class="flex items-center justify-between">
                            <p class="text-sm text-charcoal/70">
                                <span class="text-red-500">*</span> Campos obrigatórios
                            </p>
                            <button type="submit"
                                    class="inline-flex items-center px-8 py-3 bg-secondary text-white font-medium rounded hover:bg-secondary-700 focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Enviar Mensagem
                            </button>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="relative pt-12 bg-white overflow-hidden" style="padding-bottom: 3rem;">

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

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // A Casa do Gi coordinates (Mogadouro)
    const lat = 41.34217;
    const lng = -6.71347;

    // Initialize map
    const map = L.map('contact-map', {
        scrollWheelZoom: false // Disable scroll zoom for better UX
    }).setView([lat, lng], 15);

    // Add OpenStreetMap tiles with custom styling
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
            <span style="color: #2D3748; font-size: 12px;">Casa do Gi</span><br>
            <span style="color: #2D3748; font-size: 12px;">52 Avenida Nossa Senhora do Caminho, Mogadouro</span>
        </div>
    `, {
        className: 'custom-popup'
    });

    // Enable scroll zoom on click
    map.on('click', function() {
        map.scrollWheelZoom.enable();
    });

    // Disable scroll zoom when mouse leaves
    map.on('mouseout', function() {
        map.scrollWheelZoom.disable();
    });
});
</script>

<style>
/* Custom popup styling */
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
/* Remove footer margin for this page */
footer {
    margin-top: 0 !important;
}
</style>

<?php include INCLUDES_PATH . '/footer.php'; ?>
