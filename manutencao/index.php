<?php
// Define flag to bypass maintenance check in init.php
define('MAINTENANCE_PAGE', true);
require_once dirname(__DIR__) . '/includes/init.php';

// If maintenance is OFF, redirect to home
if (!isMaintenanceMode()) {
    redirect('/');
}

$bgImage = asset('images/MogadouroAtividades.jpg');
$logo = asset('images/logo_casa_do_gi.png');
$email = setting('contact_email', 'geral@acasadogi.pt');
$phone = setting('contact_phone', '');
$facebook = setting('facebook_url', '');
$instagram = setting('instagram_url', '');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manutenção - A Casa do Gi</title>
    <link rel="icon" type="image/x-icon" href="<?= asset('images/CGsimbUpNB.ico') ?>">
    
    <!-- Open Graph / Social Meta -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="A Casa do Gi - Em Breve / Coming Soon">
    <meta property="og:description" content="Estamos a preparar algo especial. Alojamento Local em Mogadouro. We are crafting something special. Local accommodation in Mogadouro.">
    <meta property="og:url" content="<?= setting('app_url', 'https://acasadogi.pt') ?>">
    <meta property="og:site_name" content="A Casa do Gi">
    <meta property="og:image" content="<?= asset('images/MogadouroAtividades.jpg') ?>">
    <meta property="og:locale" content="pt_PT">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="A Casa do Gi - Em Breve">
    <meta name="twitter:description" content="Estamos a preparar algo especial em Mogadouro.">
    <meta name="twitter:image" content="<?= asset('images/MogadouroAtividades.jpg') ?>">

    <!-- Use Tailwind via CDN for simplicity and isolation -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Lato:wght@300;400;700&family=Great+Vibes&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1A1A1A',
                        secondary: '#C6A87C',
                        cream: '#FDFBF7',
                    },
                    fontFamily: {
                        serif: ['Playfair Display', 'serif'],
                        sans: ['Lato', 'sans-serif'],
                        cursive: ['Great Vibes', 'cursive'],
                    }
                }
            }
        }
    </script>
    <style>
        .lang-en { display: none; }
        .show-en .lang-en { display: block; }
        .show-en .lang-pt { display: none; }
        .show-en .lang-en-inline { display: inline; }
        .show-en .lang-pt-inline { display: none; }
        .lang-en-inline { display: none; }
        
        /* Smooth fade for language switch */
        h1 span, p span {
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body class="h-screen w-full overflow-hidden bg-primary text-cream font-sans">
    
    <!-- Background -->
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?= $bgImage ?>');">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 h-full flex flex-col items-center justify-center p-6 text-center">
        
        <!-- Logo Text -->
        <h1 class="font-cursive text-6xl md:text-8xl text-cream mb-8 drop-shadow-lg">
            A Casa do Gi
        </h1>

        <!-- Main Text -->
        <div class="max-w-2xl mx-auto">
            <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl mb-6 text-secondary">
                <span class="lang-pt">Em Manutenção</span>
                <span class="lang-en">Under Maintenance</span>
            </h1>
            
            <p class="text-lg md:text-xl text-cream/80 mb-8 leading-relaxed">
                <span class="lang-pt">Estamos temporariamente indisponíveis para melhorias. Voltaremos em breve.</span>
                <span class="lang-en">We are temporarily unavailable for improvements. We will be back shortly.</span>
            </p>
        </div>

        <!-- Contacts -->
        <div class="mt-8 space-y-4">
            <?php if ($email): ?>
            <a href="mailto:<?= $email ?>" class="block text-lg hover:text-secondary transition-colors font-light tracking-wide">
                <?= $email ?>
            </a>
            <?php endif; ?>
            
            <?php if ($phone): ?>
            <a href="tel:<?= str_replace(' ', '', $phone) ?>" class="block text-lg hover:text-secondary transition-colors font-light tracking-wide">
                <?= $phone ?>
            </a>
            <?php endif; ?>
        </div>

        <!-- Socials -->
        <div class="mt-10 flex gap-8 justify-center items-center">
            <?php if ($facebook): ?>
            <a href="<?= $facebook ?>" target="_blank" class="text-cream/60 hover:text-white transition-colors transform hover:scale-110 duration-200">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
            </a>
            <?php endif; ?>
            
            <?php if ($instagram): ?>
            <a href="<?= $instagram ?>" target="_blank" class="text-cream/60 hover:text-white transition-colors transform hover:scale-110 duration-200">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                </svg>
            </a>
            <?php endif; ?>
        </div>

        <!-- Lang Toggle -->
        <button id="lang-toggle" onclick="document.body.classList.toggle('show-en')" 
                class="mt-16 px-6 py-2 border border-cream/30 rounded-full text-xs uppercase tracking-widest hover:bg-cream/10 transition-colors text-cream/70 hover:text-cream">
            <span class="lang-pt-inline">English</span>
            <span class="lang-en-inline">Português</span>
        </button>

    </div>

</body>
</html>
