<?php

use Core\Auth;
use Core\Database;

$base = basePath();
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$db = Database::getInstance();

$unreadMessages = $db->count('contact_submissions', 'is_read = 0');

$menuItems = [
    [
        'label' => 'Dashboard',
        'url' => $base . '/admin/',
        'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'active' => $currentPath === $base . '/admin/' || $currentPath === $base . '/admin/index.php'
    ],
    ['type' => 'separator', 'label' => 'Website'],
    [
        'label' => 'Conteúdos',
        'url' => $base . '/admin/conteudos/',
        'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'active' => strpos($currentPath, $base . '/admin/conteudos') === 0
    ],
    [
        'label' => 'Termos & Privacidade',
        'url' => $base . '/admin/legal/',
        'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'active' => strpos($currentPath, $base . '/admin/legal') === 0
    ],
    [
        'label' => 'Imagens',
        'url' => $base . '/admin/imagens/',
        'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
        'active' => strpos($currentPath, $base . '/admin/imagens') === 0
    ],
    [
        'label' => 'Media',
        'url' => $base . '/admin/media/',
        'icon' => 'M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z',
        'active' => strpos($currentPath, $base . '/admin/media') === 0
    ],
    [
        'label' => 'Heroes',
        'url' => $base . '/admin/heroes/',
        'icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z',
        'active' => strpos($currentPath, $base . '/admin/heroes') === 0
    ],
    [
        'label' => 'Alojamento',
        'url' => $base . '/admin/alojamento/',
        'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
        'active' => strpos($currentPath, $base . '/admin/alojamento') === 0
    ],
    [
        'label' => 'Atividades',
        'url' => $base . '/admin/atividades/',
        'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z',
        'active' => strpos($currentPath, $base . '/admin/atividades') === 0
    ],
    ['type' => 'separator', 'label' => 'Comunicação'],
    [
        'label' => 'Mensagens',
        'url' => $base . '/admin/mensagens/',
        'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'active' => strpos($currentPath, $base . '/admin/mensagens') === 0,
        'badge' => $unreadMessages > 0 ? $unreadMessages : null
    ],
];

// Loja migrada para serviço externo (shopk.it) — sem gestão de loja no painel.

$adminItems = [];
if (Auth::canManageUsers()) {
    $adminItems[] = [
        'label' => 'Utilizadores',
        'url' => $base . '/admin/utilizadores/',
        'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
        'active' => strpos($currentPath, $base . '/admin/utilizadores') === 0
    ];
}

$adminItems[] = [
    'label' => 'Configurações',
    'url' => $base . '/admin/configuracoes/',
    'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
    'active' => strpos($currentPath, $base . '/admin/configuracoes') === 0
];
?>

<aside class="sidebar fixed inset-y-0 left-0 w-64 bg-primary flex flex-col transition-all duration-300 z-50">
    <!-- Logo -->
    <div class="flex items-center h-16 px-6 border-b border-primary-600">
        <a href="<?= $base ?>/admin/" class="flex items-center space-x-3 justify-center w-full">
            <span class="sidebar-text text-cream font-cursive text-3xl pt-2">A Casa do Gi</span>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-4 overflow-y-auto">
        <!-- Main Menu -->
        <div class="space-y-0.5">
            <?php foreach ($menuItems as $item): ?>
                <?php if (isset($item['type']) && $item['type'] === 'separator'): ?>
                <div class="pt-5 pb-2">
                    <p class="sidebar-text px-3 text-[10px] font-semibold text-cream/30 uppercase tracking-widest"><?= $item['label'] ?></p>
                </div>
                <?php else: ?>
                <a href="<?= $item['url'] ?>"
                   class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                          <?= $item['active']
                              ? 'bg-secondary text-cream'
                              : 'text-cream-200 hover:bg-primary-600 hover:text-cream' ?>">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?= $item['icon'] ?>"/>
                    </svg>
                    <span class="sidebar-text ml-3"><?= e($item['label']) ?></span>
                    <?php if (!empty($item['badge'])): ?>
                    <span class="sidebar-text ml-auto bg-accent text-primary text-xs font-bold px-2 py-0.5 rounded-full shadow">
                        <?= $item['badge'] ?>
                    </span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- Admin Menu -->
        <?php if (!empty($adminItems)): ?>
        <div class="mt-6 pt-5 border-t border-primary-600">
            <p class="sidebar-text px-3 mb-2 text-[10px] font-semibold text-accent uppercase tracking-widest">
                Administração
            </p>
            <div class="space-y-0.5">
                <?php foreach ($adminItems as $item): ?>
                <a href="<?= $item['url'] ?>"
                   class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                          <?= $item['active']
                              ? 'bg-secondary text-cream'
                              : 'text-cream-200 hover:bg-primary-600 hover:text-cream' ?>">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?= $item['icon'] ?>"/>
                    </svg>
                    <span class="sidebar-text ml-3"><?= e($item['label']) ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </nav>

    <!-- Footer -->
    <div class="p-4 border-t border-primary-600">
        <a href="<?= $base ?>/"
           target="_blank"
           class="flex items-center px-3 py-2 text-sm text-cream-200 hover:text-accent transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            <span class="sidebar-text ml-3">Ver Website</span>
        </a>
    </div>
</aside>
