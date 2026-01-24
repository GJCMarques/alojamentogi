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
            <div class="px-6 pt-4 space-y-2">
                <?php foreach (\Core\Session::getFlash() as $type => $messages): ?>
                    <?php foreach ($messages as $message): ?>
                    <div class="p-4 rounded-lg flex items-center justify-between
                        <?php
                        switch ($type) {
                            case 'success': echo 'bg-secondary-100 text-secondary-700 border border-secondary-200'; break;
                            case 'error': echo 'bg-red-100 text-red-700 border border-red-200'; break;
                            case 'warning': echo 'bg-accent-100 text-accent-700 border border-accent-200'; break;
                            default: echo 'bg-primary-100 text-primary-700 border border-primary-200';
                        }
                        ?>">
                        <span class="text-sm"><?= e($message) ?></span>
                        <button type="button" onclick="this.parentElement.remove()" class="ml-4 opacity-70 hover:opacity-100">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Page Content -->
            <main class="flex-1 p-6">
