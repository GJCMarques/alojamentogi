<?php
/**
 * A Casa do Gi - Admin Contact Messages
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;

$db = Database::getInstance();

// Handle mark as read
if (isset($_GET['read']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $id = (int)$_GET['read'];
        $db->update('contact_submissions', ['is_read' => 1], 'id = ?', [$id]);
        Session::flash('success', 'Mensagem marcada como lida.');
    }
    redirect('/admin/mensagens/');
}

// Handle mark as spam
if (isset($_GET['spam']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $id = (int)$_GET['spam'];
        $db->update('contact_submissions', ['is_spam' => 1], 'id = ?', [$id]);
        Session::flash('success', 'Mensagem marcada como spam.');
    }
    redirect('/admin/mensagens/');
}

// Handle delete
if (isset($_GET['delete']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $id = (int)$_GET['delete'];
        $db->delete('contact_submissions', 'id = ?', [$id]);
        Session::flash('success', 'Mensagem eliminada.');
    }
    redirect('/admin/mensagens/');
}

// Filters
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Build query
$where = "WHERE 1=1";
$params = [];

switch ($filter) {
    case 'unread':
        $where .= " AND is_read = 0 AND is_spam = 0";
        break;
    case 'read':
        $where .= " AND is_read = 1 AND is_spam = 0";
        break;
    case 'spam':
        $where .= " AND is_spam = 1";
        break;
    default:
        $where .= " AND is_spam = 0";
}

// Get counts
$counts = [
    'all' => $db->fetch("SELECT COUNT(*) as c FROM contact_submissions WHERE is_spam = 0")['c'],
    'unread' => $db->fetch("SELECT COUNT(*) as c FROM contact_submissions WHERE is_read = 0 AND is_spam = 0")['c'],
    'read' => $db->fetch("SELECT COUNT(*) as c FROM contact_submissions WHERE is_read = 1 AND is_spam = 0")['c'],
    'spam' => $db->fetch("SELECT COUNT(*) as c FROM contact_submissions WHERE is_spam = 1")['c'],
];

// Get total
$total = $db->fetch("SELECT COUNT(*) as c FROM contact_submissions {$where}", $params)['c'];
$totalPages = ceil($total / $perPage);

// Get messages
$messages = $db->fetchAll(
    "SELECT * FROM contact_submissions {$where} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}",
    $params
);

$pageTitle = 'Mensagens';
$currentPage = 'mensagens';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Mensagens de Contacto</h1>
        <p class="text-gray-600"><?= $total ?> mensagem(ns)</p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm mb-6">
    <div class="flex border-b border-gray-200">
        <a href="?filter=all" class="px-6 py-3 text-sm font-medium <?= $filter === 'all' ? 'text-olive-600 border-b-2 border-olive-600' : 'text-gray-500 hover:text-gray-700' ?>">
            Todas (<?= $counts['all'] ?>)
        </a>
        <a href="?filter=unread" class="px-6 py-3 text-sm font-medium <?= $filter === 'unread' ? 'text-olive-600 border-b-2 border-olive-600' : 'text-gray-500 hover:text-gray-700' ?>">
            Não lidas (<?= $counts['unread'] ?>)
        </a>
        <a href="?filter=read" class="px-6 py-3 text-sm font-medium <?= $filter === 'read' ? 'text-olive-600 border-b-2 border-olive-600' : 'text-gray-500 hover:text-gray-700' ?>">
            Lidas (<?= $counts['read'] ?>)
        </a>
        <a href="?filter=spam" class="px-6 py-3 text-sm font-medium <?= $filter === 'spam' ? 'text-olive-600 border-b-2 border-olive-600' : 'text-gray-500 hover:text-gray-700' ?>">
            Spam (<?= $counts['spam'] ?>)
        </a>
    </div>
</div>

<!-- Messages List -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <?php if (empty($messages)): ?>
    <div class="p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        <h3 class="text-lg font-medium text-gray-700 mb-2">Nenhuma mensagem</h3>
        <p class="text-gray-500">Não existem mensagens nesta categoria.</p>
    </div>
    <?php else: ?>
    <div class="divide-y divide-gray-200">
        <?php foreach ($messages as $msg): ?>
        <div class="p-6 hover:bg-gray-50 <?= !$msg['is_read'] ? 'bg-olive-50' : '' ?>">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-2">
                        <?php if (!$msg['is_read']): ?>
                        <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-olive-100 text-olive-800 rounded">Nova</span>
                        <?php endif; ?>
                        <h3 class="text-sm font-semibold text-gray-900 truncate">
                            <?= e($msg['name']) ?>
                        </h3>
                        <span class="text-sm text-gray-500">&lt;<?= e($msg['email']) ?>&gt;</span>
                        <?php if ($msg['phone']): ?>
                        <span class="text-sm text-gray-400">| <?= e($msg['phone']) ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if ($msg['subject']): ?>
                    <p class="text-sm font-medium text-gray-700 mb-1"><?= e($msg['subject']) ?></p>
                    <?php endif; ?>

                    <p class="text-sm text-gray-600 line-clamp-2"><?= e($msg['message']) ?></p>

                    <div class="mt-2 text-xs text-gray-400">
                        <?= formatDateTime($msg['created_at']) ?> | IP: <?= e($msg['ip_address']) ?>
                    </div>
                </div>

                <div class="ml-4 flex items-center gap-2">
                    <?php if (!$msg['is_read']): ?>
                    <a href="?read=<?= $msg['id'] ?>&token=<?= CSRF::getToken() ?>"
                       class="text-sm text-olive-600 hover:text-olive-800"
                       title="Marcar como lida">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </a>
                    <?php endif; ?>

                    <a href="mailto:<?= e($msg['email']) ?>?subject=Re: <?= e($msg['subject'] ?? 'Contacto Casa do Gi') ?>"
                       class="text-sm text-blue-600 hover:text-blue-800"
                       title="Responder">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                    </a>

                    <?php if (!$msg['is_spam']): ?>
                    <a href="?spam=<?= $msg['id'] ?>&token=<?= CSRF::getToken() ?>"
                       class="text-sm text-yellow-600 hover:text-yellow-800"
                       title="Marcar como spam">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </a>
                    <?php endif; ?>

                    <a href="?delete=<?= $msg['id'] ?>&token=<?= CSRF::getToken() ?>"
                       class="text-sm text-red-600 hover:text-red-800"
                       title="Eliminar"
                       onclick="return confirm('Eliminar esta mensagem?')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
        <div class="text-sm text-gray-500">
            Página <?= $page ?> de <?= $totalPages ?>
        </div>
        <div class="flex gap-2">
            <?php if ($page > 1): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>"
               class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                Anterior
            </a>
            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>"
               class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                Seguinte
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
