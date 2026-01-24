<?php
/**
 * A Casa do Gi - Admin Login Page
 */

require_once dirname(__DIR__) . '/includes/init.php';

use Core\Auth;
use Core\CSRF;
use Core\Session;

// Redirect if already logged in
if (Auth::check()) {
    redirect('/admin/');
}

$error = '';
$username = '';

// Handle form submission
if (isPost()) {
    // Verify CSRF
    if (!CSRF::isValid()) {
        $error = 'Sessao expirada. Por favor, tente novamente.';
    } else {
        $username = sanitize(post('username', ''));
        $password = post('password', '');

        if (empty($username) || empty($password)) {
            $error = 'Por favor, preencha todos os campos.';
        } else {
            $result = Auth::attempt($username, $password);

            if ($result['success']) {
                // Redirect to dashboard
                $redirectTo = Session::get('redirect_after_login', '/admin/');
                Session::remove('redirect_after_login');
                redirect($redirectTo);
            } else {
                $error = $result['message'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Login | A Casa do Gi</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Great+Vibes&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': {
                            DEFAULT: '#264653',
                            500: '#264653',
                            600: '#1e3842',
                            700: '#172a32',
                        },
                        'secondary': {
                            DEFAULT: '#768A68',
                            500: '#768A68',
                            600: '#5e6e53',
                        },
                        'accent': {
                            DEFAULT: '#C5A059',
                            500: '#C5A059',
                        },
                        'cream': {
                            DEFAULT: '#FDFBF7',
                            100: '#faf5eb',
                            200: '#f5ebd7',
                        },
                        'charcoal': {
                            DEFAULT: '#2D3748',
                            200: '#d4d8dc',
                            500: '#7b8792',
                            700: '#4a5259',
                        }
                    },
                    fontFamily: {
                        'sans': ['Poppins', 'system-ui', 'sans-serif'],
                        'cursive': ['Great Vibes', 'cursive'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Poppins', system-ui, sans-serif; }
        .font-cursive { font-family: 'Great Vibes', cursive; }
    </style>
</head>
<body class="h-full bg-cream-100">
    <div class="min-h-full flex">
        <!-- Left Side - Image -->
        <div class="hidden lg:block lg:w-1/2 relative bg-primary-700">
            <!-- Image Placeholder -->
            <img src="<?= asset('images/mogadouroLogin.png') ?>" 
                 alt="Mogadouro" 
                 class="absolute inset-0 w-full h-full object-cover opacity-60">
            
            <div class="relative h-full flex flex-col justify-between p-12 text-white z-10">
                <div>
                    <h1 class="text-5xl font-cursive text-cream tracking-wide drop-shadow-md pb-1">A Casa do Gi</h1>
                    <p class="mt-2 text-cream-200 text-lg font-light">Painel de Administracao</p>
                </div>
                <div>
                    <blockquote class="text-xl text-cream-200 italic font-light leading-relaxed">
                        "Simplicidade, acolhimento, momentos de convivio marcantes, calor da familia, alegria, diversao, gargalhadas e muito amor!"
                    </blockquote>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <h1 class="text-4xl font-cursive text-primary drop-shadow-sm pb-1">A Casa do Gi</h1>
                    <p class="text-charcoal-500">Painel de Administracao</p>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-8 border border-accent/20">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-primary">Bem-vindo</h2>
                        <p class="text-charcoal-500 mt-2">Inicie sessao para continuar</p>
                    </div>

                    <?php if ($error): ?>
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded text-red-600 text-sm">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <?= e($error) ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (Session::hasFlash('error')): ?>
                    <?php foreach (Session::getFlash('error') as $msg): ?>
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded text-red-600 text-sm">
                        <?= e($msg) ?>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>

                    <form method="POST" action="" class="space-y-6">
                        <?= CSRF::tokenField() ?>

                        <div>
                            <label for="username" class="block text-sm font-medium text-charcoal-700 mb-2">
                                Utilizador ou Email
                            </label>
                            <input type="text"
                                   id="username"
                                   name="username"
                                   value="<?= e($username) ?>"
                                   required
                                   autocomplete="username"
                                   class="w-full px-4 py-3 border border-charcoal-200 rounded focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-colors"
                                   placeholder="O seu utilizador">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-charcoal-700 mb-2">
                                Palavra-passe
                            </label>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   required
                                   autocomplete="current-password"
                                   class="w-full px-4 py-3 border border-charcoal-200 rounded focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-colors"
                                   placeholder="A sua palavra-passe">
                        </div>

                        <button type="submit"
                                class="w-full py-3 px-4 bg-secondary text-cream font-medium rounded hover:bg-secondary-600 focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition-colors">
                            Entrar
                        </button>
                    </form>
                </div>

                <p class="mt-8 text-center text-sm text-charcoal-500">
                    <a href="<?= basePath() ?>/" class="hover:text-secondary transition-colors">
                        Voltar ao website
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
