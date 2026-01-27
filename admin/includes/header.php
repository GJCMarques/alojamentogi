<?php
/**
 * A Casa do Gi - Admin Header
 *
 * Variables:
 * - $pageTitle: Page title
 * - $currentAdmin: Current logged in admin (from auth-check.php)
 */

$pageTitle = $pageTitle ?? 'Dashboard';
$base = basePath();
?>
<!DOCTYPE html>
<html lang="pt" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($pageTitle) ?> | Admin - A Casa do Gi</title>
    <link rel="icon" type="image/x-icon" href="<?= asset('images/CGsimbUpNB.ico') ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Great+Vibes&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Sophisticated Nature Palette - Admin
                        // Primary - Deep Slate Petrol (Sidebar, Headers)
                        'primary': {
                            50: '#e8edef', 100: '#d1dbdf', 200: '#a3b7bf', 300: '#75939f',
                            400: '#476f7f', 500: '#264653', 600: '#1e3842', 700: '#172a32',
                            800: '#0f1c21', 900: '#080e11', DEFAULT: '#264653'
                        },
                        // Secondary - Olive Sage (Active states, CTA)
                        'secondary': {
                            50: '#f2f4f0', 100: '#e5e9e1', 200: '#cbd3c3', 300: '#b1bda5',
                            400: '#97a787', 500: '#768A68', 600: '#5e6e53', 700: '#47533e',
                            800: '#2f372a', 900: '#181c15', DEFAULT: '#768A68'
                        },
                        // Accent - Gold Ocre (Highlights, Badges)
                        'accent': {
                            50: '#faf6ed', 100: '#f5eddb', 200: '#ebdbb7', 300: '#e1c993',
                            400: '#d7b76f', 500: '#C5A059', 600: '#9e8047', 700: '#766035',
                            800: '#4f4024', 900: '#272012', DEFAULT: '#C5A059'
                        },
                        // Background - Warm Cream
                        'cream': {
                            50: '#FDFBF7', 100: '#faf5eb', 200: '#f5ebd7', 300: '#f0e1c3',
                            400: '#ebd7af', DEFAULT: '#FDFBF7'
                        },
                        // Text - Charcoal
                        'charcoal': {
                            50: '#f7f8f8', 100: '#ebedef', 200: '#d4d8dc', 300: '#b8bfc5',
                            400: '#9aa3ab', 500: '#7b8792', 600: '#5f6a74', 700: '#4a5259',
                            800: '#2D3748', 900: '#1a2028', DEFAULT: '#2D3748'
                        },
                        // Legacy mappings for backwards compatibility
                        'sky': {
                            50: '#e8edef', 100: '#d1dbdf', 200: '#a3b7bf', 300: '#75939f',
                            400: '#476f7f', 500: '#264653', 600: '#1e3842', 700: '#172a32',
                            800: '#0f1c21', 900: '#080e11'
                        },
                        'nature': {
                            50: '#f2f4f0', 100: '#e5e9e1', 200: '#cbd3c3', 300: '#b1bda5',
                            400: '#97a787', 500: '#768A68', 600: '#5e6e53', 700: '#47533e',
                            800: '#2f372a', 900: '#181c15'
                        },
                        'earth': {
                            50: '#FDFBF7', 100: '#faf5eb', 200: '#f5ebd7', 300: '#f0e1c3',
                            400: '#ebd7af', 500: '#7b8792', 600: '#5f6a74', 700: '#4a5259',
                            800: '#2D3748', 900: '#1a2028'
                        },
                        'pure': { 50: '#FDFBF7', 100: '#faf5eb', 200: '#f5ebd7' }
                    },
                    fontFamily: { 'sans': ['Inter', 'system-ui', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        .font-cursive { font-family: 'Great Vibes', cursive; }
    </style>

    <!-- CSRF Token -->
    <?= \Core\CSRF::tokenMeta() ?>

    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }

        /* Sidebar transitions */
        .sidebar-collapsed .sidebar-text { display: none; }
        .sidebar-collapsed .sidebar { width: 5rem; }
        .sidebar-collapsed .main-content { margin-left: 5rem; }

        /* Table styles */
        .admin-table th { @apply px-6 py-3 text-left text-xs font-semibold text-charcoal-600 uppercase tracking-wider bg-cream-100; }
        .admin-table td { @apply px-6 py-4 whitespace-nowrap text-sm text-charcoal-700; }
        .admin-table tr:hover td { @apply bg-secondary-50; }

        /* Custom Scrollbar for Sidebar */
        .sidebar nav::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar nav::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar nav::-webkit-scrollbar-thumb {
            background-color: rgba(253, 251, 247, 0.1); /* cream-50 with low opacity */
            border-radius: 20px;
        }
        .sidebar nav::-webkit-scrollbar-thumb:hover {
            background-color: rgba(253, 251, 247, 0.3);
        }
    </style>
</head>
<body class="h-full bg-cream-50">
    <div class="flex h-full">
        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar.php'; ?>

        <!-- Main Content Area -->
        <div class="main-content flex-1 ml-64 flex flex-col min-h-screen transition-all duration-300">
            <!-- Top Bar -->
            <header class="bg-white border-b border-accent/20 sticky top-0 z-40">
                <div class="flex items-center justify-between px-6 py-4">
                    <!-- Page Title & Breadcrumb -->
                    <div>
                        <h1 class="text-xl font-semibold text-primary"><?= e($pageTitle) ?></h1>
                    </div>

                    <!-- Right Side -->
                    <div class="flex items-center space-x-4">
                        <!-- View Site Link -->
                        <a href="<?= $base ?>/" target="_blank"
                           class="text-charcoal-500 hover:text-secondary transition-colors"
                           title="Ver website">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>

                        <!-- User Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button onclick="this.nextElementSibling.classList.toggle('hidden')"
                                    data-dropdown-toggle="true"
                                    class="flex items-center space-x-3 text-sm focus:outline-none">
                                <img src="<?= e($currentAdmin->getAvatarUrl()) ?>"
                                     alt="<?= e($currentAdmin->getDisplayName()) ?>"
                                     class="w-8 h-8 rounded-full object-cover border border-accent/30">
                                <span class="text-charcoal-700"><?= e($currentAdmin->getDisplayName()) ?></span>
                                <svg class="w-4 h-4 text-charcoal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-accent/20 py-1 z-50">
                                <a href="<?= $base ?>/admin/perfil/" class="block px-4 py-2 text-sm text-charcoal-700 hover:bg-secondary-50">
                                    O Meu Perfil
                                </a>
                                <a href="<?= $base ?>/admin/configuracoes/" class="block px-4 py-2 text-sm text-charcoal-700 hover:bg-secondary-50">
                                    Configuracoes
                                </a>
                                <hr class="my-1 border-accent/20">
                                <a href="<?= $base ?>/admin/logout.php" class="block px-4 py-2 text-sm text-secondary font-medium hover:bg-secondary-50">
                                    Terminar Sessao
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            <?php if (\Core\Session::hasFlash()): ?>
            <div class="px-6 pt-4 space-y-2" id="flashMessages">
                <?php foreach (\Core\Session::getFlash() as $type => $messages): ?>
                    <?php foreach ($messages as $message): ?>
                    <?php
                    $icon = '';
                    $classes = '';
                    switch ($type) {
                        case 'success':
                            $classes = 'bg-green-50 text-green-800 border border-green-200';
                            $icon = '<svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>';
                            break;
                        case 'error':
                            $classes = 'bg-red-50 text-red-800 border border-red-200';
                            $icon = '<svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>';
                            break;
                        case 'warning':
                            $classes = 'bg-yellow-50 text-yellow-800 border border-yellow-200';
                            $icon = '<svg class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>';
                            break;
                        default:
                            $classes = 'bg-blue-50 text-blue-800 border border-blue-200';
                            $icon = '<svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>';
                    }
                    ?>
                    <div class="p-4 rounded-lg shadow-sm flex items-start gap-3 animate-slideDown <?= $classes ?>">
                        <?= $icon ?>
                        <div class="flex-1 text-sm"><?= $message ?></div>
                        <button type="button" onclick="this.parentElement.style.opacity='0';setTimeout(()=>this.parentElement.remove(),300)" class="opacity-60 hover:opacity-100 transition-opacity">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
            <style>
                @keyframes slideDown {
                    from { opacity: 0; transform: translateY(-10px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .animate-slideDown { animation: slideDown 0.3s ease-out; transition: opacity 0.3s; }
            </style>
            <?php endif; ?>

            <!-- Page Content -->
            <main class="flex-1 p-6">
