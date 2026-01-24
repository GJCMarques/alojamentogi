<?php
/**
 * A Casa do Gi - Contact Page (Portuguese)
 */

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

// Check if contact form is enabled
$formEnabled = isContactFormEnabled();

// Form handling
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
    // Verify CSRF
    if (!CSRF::isValid()) {
        $errors['csrf'] = 'Sessão expirada. Por favor, recarregue a página.';
    } else {
        // Honeypot check (anti-spam)
        if (!empty($_POST['website'])) {
            // Bot detected - silently ignore
            $success = true;
        } else {
            // Get form data
            $formData = [
                'name' => sanitize(post('name', '')),
                'email' => sanitizeEmail(post('email', '')),
                'phone' => sanitize(post('phone', '')),
                'subject' => sanitize(post('subject', '')),
                'message' => sanitize(post('message', '')),
            ];

            // Validate
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
                // Rate limiting check
                $recentSubmissions = $db->count(
                    'contact_submissions',
                    "ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
                    [getClientIp()]
                );

                if ($recentSubmissions >= 3) {
                    $errors['limit'] = 'Demasiadas submissões. Por favor, aguarde um pouco antes de tentar novamente.';
                } else {
                    // Save to database
                    $db->insert('contact_submissions', [
                        'name' => $formData['name'],
                        'email' => $formData['email'],
                        'phone' => $formData['phone'] ?: null,
                        'subject' => $formData['subject'] ?: null,
                        'message' => $formData['message'],
                        'ip_address' => getClientIp(),
                        'user_agent' => substr(getUserAgent(), 0, 500),
                        'language' => $lang->getCurrentLang(),
                    ]);

                    // Send email notifications
                    $mailer = new Mailer();
                    $mailer->sendContactNotification($formData);
                    $mailer->sendContactConfirmation($formData);

                    $success = true;

                    // Clear form data
                    $formData = [
                        'name' => '',
                        'email' => '',
                        'phone' => '',
                        'subject' => '',
                        'message' => '',
                    ];

                    Session::flash('success', 'Mensagem enviada com sucesso! Entraremos em contacto brevemente.');
                }
            }
        }
    }
}

// Get contact info
$contactEmail = setting('contact_email', '');
$contactPhone = setting('contact_phone', '');
$contactAddress = setting('contact_address', '');

// Page configuration
$pageTitle = __('contact_title', 'Contactos');
$pageDescription = 'Entre em contacto com A Casa do Gi. Estamos disponíveis para responder às suas questões.';

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="relative py-20 lg:py-32 bg-primary overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmZmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIj48cGF0aCBkPSJNMzYgMzRjMC0yLjIwOS0xLjc5MS00LTQtNHMtNCAxLjc5MS00IDQgMS43OTEgNCA0IDQgNC0xLjc5MSA0LTR6Ii8+PC9nPjwvZz48L3N2Zz4=')]"></div>
    </div>
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-primary/50 to-primary"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <span class="inline-block text-accent text-lg font-medium tracking-[0.2em] uppercase mb-4 animate-fade-in">
            Fale Connosco
        </span>
        <h1 class="font-cursive text-6xl md:text-7xl lg:text-8xl text-cream mb-6 drop-shadow-lg">
            <?= _e('contact_title', 'Contacte-nos') ?>
        </h1>
        <p class="text-xl md:text-2xl text-cream/90 max-w-2xl mx-auto font-light leading-relaxed">
            <?= _e('contact_intro', 'Tem alguma questão? Entre em contacto connosco') ?>
        </p>
    </div>
</section>

<!-- Main Content -->
<section class="py-16 lg:py-24 bg-cream-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-12">
            <!-- Contact Information -->
            <div class="lg:col-span-1">
                <h2 class="font-serif text-2xl text-primary mb-6">Informações de Contacto</h2>

                <div class="space-y-6">
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
                <div class="mt-10 p-6 bg-primary rounded-lg">
                    <h3 class="font-serif text-lg text-cream mb-4">Quer fazer uma reserva?</h3>
                    <p class="text-charcoal/60 text-sm mb-4">
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
            <div class="lg:col-span-2">
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

<?php include INCLUDES_PATH . '/footer.php'; ?>
