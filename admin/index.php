<?php

require_once dirname(__DIR__) . '/includes/init.php';
require_once __DIR__ . '/includes/auth-check.php';

use Core\Database;
use Core\Auth;

$db = Database::getInstance();
$base = basePath();

$stats = [
    'messages_unread' => $db->count('contact_submissions', 'is_read = 0'),
    'messages_total'  => $db->count('contact_submissions'),
    'activities'      => $db->count('activities', 'is_active = 1'),
    'accommodations'  => $db->count('accommodation', 'is_active = 1'),
    'media'           => $db->count('media'),
];

$recentMessages = $db->fetchAll(
    "SELECT * FROM contact_submissions ORDER BY created_at DESC LIMIT 6"
);

$pageTitle = 'Dashboard';

include __DIR__ . '/includes/header.php';
?>

    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-primary">Dashboard</h1>
            <p class="text-sm text-gray-500">Bem-vindo ao painel de administração</p>
        </div>
        <div class="text-sm text-gray-500">
            <?= date('d M Y, H:i') ?>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Messages -->
        <div class="admin-card p-6 border-l-4 border-l-blue-500">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-50 rounded-full relative">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <?php if ($stats['messages_unread'] > 0): ?>
                    <span class="absolute top-0 right-0 w-3 h-3 bg-red-500 border-2 border-white rounded-full"></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex justify-between items-end">
                <div>
                    <p class="text-sm font-medium text-gray-500">Mensagens por ler</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= $stats['messages_unread'] ?></h3>
                </div>
                <a href="<?= $base ?>/admin/mensagens/" class="text-sm text-secondary-600 hover:text-secondary-800 font-medium flex items-center gap-1 group">
                    Ver
                    <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        <!-- Accommodations -->
        <div class="admin-card p-6 border-l-4 border-l-green-500">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-green-50 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            <div class="flex justify-between items-end">
                <div>
                    <p class="text-sm font-medium text-gray-500">Alojamentos</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= $stats['accommodations'] ?></h3>
                </div>
                <a href="<?= $base ?>/admin/alojamento/" class="text-sm text-secondary-600 hover:text-secondary-800 font-medium flex items-center gap-1 group">
                    Gerir
                    <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        <!-- Activities -->
        <div class="admin-card p-6 border-l-4 border-l-pink-500">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-pink-50 rounded-full">
                    <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <div class="flex justify-between items-end">
                <div>
                    <p class="text-sm font-medium text-gray-500">Atividades ativas</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= $stats['activities'] ?></h3>
                </div>
                <a href="<?= $base ?>/admin/atividades/" class="text-sm text-secondary-600 hover:text-secondary-800 font-medium flex items-center gap-1 group">
                    Ver
                    <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        <!-- Media -->
        <div class="admin-card p-6 border-l-4 border-l-indigo-500">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-indigo-50 rounded-full">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="flex justify-between items-end">
                <div>
                    <p class="text-sm font-medium text-gray-500">Imagens / Media</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= $stats['media'] ?></h3>
                </div>
                <a href="<?= $base ?>/admin/media/" class="text-sm text-secondary-600 hover:text-secondary-800 font-medium flex items-center gap-1 group">
                    Ver
                    <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid lg:grid-cols-3 gap-6 mb-6">
        <!-- Recent Messages -->
        <div class="admin-card lg:col-span-2">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-bold text-gray-800">Mensagens Recentes</h2>
                <a href="<?= $base ?>/admin/mensagens/" class="text-sm text-secondary-600 hover:text-secondary-800">Ver todas</a>
            </div>
            <div class="divide-y divide-gray-100">
                <?php if (empty($recentMessages)): ?>
                <div class="px-6 py-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <p>Ainda não há mensagens</p>
                </div>
                <?php else: ?>
                    <?php foreach ($recentMessages as $msg): ?>
                    <a href="<?= $base ?>/admin/mensagens/?id=<?= $msg['id'] ?>" class="block px-6 py-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start space-x-3">
                            <?php if (!$msg['is_read']): ?>
                            <span class="w-2 h-2 bg-secondary-500 rounded-full mt-2 flex-shrink-0"></span>
                            <?php else: ?>
                            <span class="w-2 h-2 flex-shrink-0"></span>
                            <?php endif; ?>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="font-medium text-gray-800 truncate"><?= e($msg['name']) ?></p>
                                    <span class="text-xs text-gray-400 flex-shrink-0 ml-2"><?= timeAgo($msg['created_at']) ?></span>
                                </div>
                                <p class="text-sm text-gray-600 truncate"><?= e($msg['subject'] ?: 'Sem assunto') ?></p>
                                <p class="text-sm text-gray-500 truncate"><?= e(truncate($msg['message'], 60)) ?></p>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="admin-card p-6">
            <h2 class="font-bold text-gray-800 mb-4">Ações Rápidas</h2>
            <div class="grid grid-cols-2 gap-4">
                <a href="<?= $base ?>/admin/conteudos/" class="group flex flex-col items-center p-4 bg-gray-50 rounded-xl hover:bg-secondary-50 hover:text-secondary-600 transition-colors text-center border border-gray-100 hover:border-secondary-100">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center mb-2 shadow-sm group-hover:scale-110 transition-transform text-secondary-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold">Conteúdos</span>
                </a>

                <a href="<?= $base ?>/admin/alojamento/" class="group flex flex-col items-center p-4 bg-gray-50 rounded-xl hover:bg-secondary-50 hover:text-secondary-600 transition-colors text-center border border-gray-100 hover:border-secondary-100">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center mb-2 shadow-sm group-hover:scale-110 transition-transform text-secondary-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold">Alojamento</span>
                </a>

                <a href="<?= $base ?>/admin/atividades/" class="group flex flex-col items-center p-4 bg-gray-50 rounded-xl hover:bg-secondary-50 hover:text-secondary-600 transition-colors text-center border border-gray-100 hover:border-secondary-100">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center mb-2 shadow-sm group-hover:scale-110 transition-transform text-secondary-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold">Atividades</span>
                </a>

                <a href="<?= $base ?>/admin/media/" class="group flex flex-col items-center p-4 bg-gray-50 rounded-xl hover:bg-secondary-50 hover:text-secondary-600 transition-colors text-center border border-gray-100 hover:border-secondary-100">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center mb-2 shadow-sm group-hover:scale-110 transition-transform text-secondary-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold">Media</span>
                </a>

                <a href="<?= $base ?>/admin/legal/" class="group flex flex-col items-center p-4 bg-gray-50 rounded-xl hover:bg-secondary-50 hover:text-secondary-600 transition-colors text-center border border-gray-100 hover:border-secondary-100">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center mb-2 shadow-sm group-hover:scale-110 transition-transform text-secondary-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold">Termos &amp; Privacidade</span>
                </a>

                <a href="<?= $base ?>/admin/heroes/" class="group flex flex-col items-center p-4 bg-gray-50 rounded-xl hover:bg-secondary-50 hover:text-secondary-600 transition-colors text-center border border-gray-100 hover:border-secondary-100">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center mb-2 shadow-sm group-hover:scale-110 transition-transform text-secondary-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold">Heroes</span>
                </a>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/includes/footer.php'; ?>
