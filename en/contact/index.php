<?php
/**
 * A Casa do Gi - Contact Page (English)
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';

use Core\Database;
use Core\Language;
use Core\Validator;
use Core\Mailer;
use Core\CSRF;
use Core\Session;

// Force English language
Language::getInstance()->setLanguage(LANG_EN);
$lang = Language::getInstance();
$db = Database::getInstance();
$base = basePath();

// Check if contact form is enabled
$formEnabled = isContactFormEnabled();

// Get settings
$contactEmail = setting('contact_email', '');
$contactPhone = setting('contact_phone', '');
$contactAddress = setting('contact_address', '');

// Form handling
$errors = [];
$success = false;
$formData = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'subject' => '',
    'message' => ''
];

if (isPost() && $formEnabled) {
    CSRF::check();

    $formData = [
        'name' => sanitize(post('name', '')),
        'email' => sanitizeEmail(post('email', '')),
        'phone' => sanitize(post('phone', '')),
        'subject' => sanitize(post('subject', '')),
        'message' => sanitize(post('message', ''))
    ];

    // Validation
    $validator = new Validator();
    $validator->required($formData['name'], 'name', 'Name is required');
    $validator->email($formData['email'], 'email', 'Please enter a valid email');
    $validator->required($formData['message'], 'message', 'Message is required');
    $validator->minLength($formData['message'], 10, 'message', 'Message must be at least 10 characters');

    $errors = $validator->getErrors();

    if (empty($errors)) {
        // Save to database
        $saved = $db->insert('contact_submissions', [
            'name' => $formData['name'],
            'email' => $formData['email'],
            'phone' => $formData['phone'],
            'subject' => $formData['subject'],
            'message' => $formData['message'],
            'ip_address' => getClientIp(),
            'user_agent' => substr(getUserAgent(), 0, 500)
        ]);

        if ($saved) {
            // Send email notification
            try {
                $mailer = new Mailer();
                $mailer->sendContactNotification($formData);
            } catch (\Exception $e) {
                logMessage("Failed to send contact notification: " . $e->getMessage(), 'error');
            }

            Session::flash('success', 'Your message has been sent successfully. We will reply as soon as possible.');
            redirect($base . '/en/contact/');
        } else {
            $errors['general'] = 'There was an error sending your message. Please try again.';
        }
    }
}

// Page configuration
$pageTitle = 'Contact';
$pageDescription = 'Contact A Casa do Gi - local accommodation in Mogadouro. We are here to help you plan your stay.';

include INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="relative py-16 lg:py-24 bg-granite-800 -mt-20 pt-32">
    <div class="absolute inset-0 parallax-bg opacity-20" style="background-image: url('<?= asset('images/contact-hero.jpg') ?>');"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-block text-olive-400 text-sm font-medium uppercase tracking-wider mb-4">
            Get in Touch
        </span>
        <h1 class="font-serif text-4xl md:text-5xl text-cream-50 mb-4">
            Contact Us
        </h1>
        <p class="text-lg text-cream-200 max-w-2xl mx-auto">
            Have questions? We're here to help you plan your perfect stay.
        </p>
    </div>
</section>

<!-- Contact Section -->
<section class="py-16 lg:py-24 bg-cream-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-12">
            <!-- Contact Info -->
            <div class="lg:col-span-1">
                <h2 class="font-serif text-2xl text-granite-800 mb-6">Contact Information</h2>

                <div class="space-y-6">
                    <?php if ($contactAddress): ?>
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-olive-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-5 h-5 text-olive-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-granite-800 mb-1">Address</h3>
                            <p class="text-granite-600 text-sm whitespace-pre-line"><?= e($contactAddress) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($contactPhone): ?>
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-olive-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-5 h-5 text-olive-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-granite-800 mb-1">Phone</h3>
                            <a href="tel:<?= e(preg_replace('/\s+/', '', $contactPhone)) ?>" class="text-olive-600 hover:text-olive-700">
                                <?= e($contactPhone) ?>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($contactEmail): ?>
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-olive-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-5 h-5 text-olive-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-granite-800 mb-1">Email</h3>
                            <a href="mailto:<?= e($contactEmail) ?>" class="text-olive-600 hover:text-olive-700">
                                <?= e($contactEmail) ?>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm p-8">
                    <h2 class="font-serif text-2xl text-granite-800 mb-6">Send us a Message</h2>

                    <?php if (!$formEnabled): ?>
                    <div class="p-6 bg-cream-100 rounded text-center">
                        <p class="text-granite-600">The contact form is temporarily unavailable. Please contact us directly by email or phone.</p>
                    </div>
                    <?php else: ?>

                    <?php if (!empty($errors['general'])): ?>
                    <div class="mb-6 p-4 bg-terracotta-500/10 border border-terracotta-200 rounded text-terracotta-600">
                        <?= e($errors['general']) ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="" class="space-y-6">
                        <?= CSRF::tokenField() ?>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-granite-700 mb-2">
                                    Name <span class="text-terracotta-500">*</span>
                                </label>
                                <input type="text" id="name" name="name" value="<?= e($formData['name']) ?>" required
                                       class="w-full px-4 py-3 border <?= isset($errors['name']) ? 'border-terracotta-500' : 'border-granite-200' ?> rounded focus:ring-2 focus:ring-olive-500 focus:border-olive-500 outline-none">
                                <?php if (isset($errors['name'])): ?>
                                <p class="mt-1 text-sm text-terracotta-500"><?= e($errors['name']) ?></p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-granite-700 mb-2">
                                    Email <span class="text-terracotta-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" value="<?= e($formData['email']) ?>" required
                                       class="w-full px-4 py-3 border <?= isset($errors['email']) ? 'border-terracotta-500' : 'border-granite-200' ?> rounded focus:ring-2 focus:ring-olive-500 focus:border-olive-500 outline-none">
                                <?php if (isset($errors['email'])): ?>
                                <p class="mt-1 text-sm text-terracotta-500"><?= e($errors['email']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="phone" class="block text-sm font-medium text-granite-700 mb-2">
                                    Phone
                                </label>
                                <input type="tel" id="phone" name="phone" value="<?= e($formData['phone']) ?>"
                                       class="w-full px-4 py-3 border border-granite-200 rounded focus:ring-2 focus:ring-olive-500 focus:border-olive-500 outline-none">
                            </div>

                            <div>
                                <label for="subject" class="block text-sm font-medium text-granite-700 mb-2">
                                    Subject
                                </label>
                                <input type="text" id="subject" name="subject" value="<?= e($formData['subject']) ?>"
                                       class="w-full px-4 py-3 border border-granite-200 rounded focus:ring-2 focus:ring-olive-500 focus:border-olive-500 outline-none">
                            </div>
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-granite-700 mb-2">
                                Message <span class="text-terracotta-500">*</span>
                            </label>
                            <textarea id="message" name="message" rows="5" required
                                      class="w-full px-4 py-3 border <?= isset($errors['message']) ? 'border-terracotta-500' : 'border-granite-200' ?> rounded focus:ring-2 focus:ring-olive-500 focus:border-olive-500 outline-none resize-none"><?= e($formData['message']) ?></textarea>
                            <?php if (isset($errors['message'])): ?>
                            <p class="mt-1 text-sm text-terracotta-500"><?= e($errors['message']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <button type="submit"
                                    class="w-full md:w-auto px-8 py-3 bg-olive-600 text-white font-medium rounded hover:bg-olive-700 focus:ring-2 focus:ring-olive-500 focus:ring-offset-2 transition-colors">
                                Send Message
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
