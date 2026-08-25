<?php

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;

$db = Database::getInstance();

if (isset($_GET['read']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $id = (int)$_GET['read'];
        $db->update('contact_submissions', ['is_read' => 1], 'id = ?', [$id]);
        Session::flash('success', 'Mensagem marcada como lida.');
    }
    redirect('/admin/mensagens/');
}

if (isset($_GET['unread']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $id = (int)$_GET['unread'];
        $db->update('contact_submissions', ['is_read' => 0], 'id = ?', [$id]);
        Session::flash('success', 'Mensagem marcada como não lida.');
    }
    redirect('/admin/mensagens/');
}

if (isset($_GET['delete']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $id = (int)$_GET['delete'];
        $db->delete('contact_submissions', 'id = ?', [$id]);
        Session::flash('success', 'Mensagem eliminada.');
    }
    redirect('/admin/mensagens/');
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$where = "WHERE 1=1 AND is_spam = 0 AND is_ignored = 0";
$params = [];

switch ($filter) {
    case 'unread':
        $where .= " AND is_read = 0";
        break;
    case 'read':
        $where .= " AND is_read = 1";
        break;
}

$counts = [
    'all' => $db->fetch("SELECT COUNT(*) as c FROM contact_submissions WHERE is_spam = 0 AND is_ignored = 0")['c'],
    'unread' => $db->fetch("SELECT COUNT(*) as c FROM contact_submissions WHERE is_read = 0 AND is_spam = 0 AND is_ignored = 0")['c'],
    'read' => $db->fetch("SELECT COUNT(*) as c FROM contact_submissions WHERE is_read = 1 AND is_spam = 0 AND is_ignored = 0")['c'],
];

$total = $db->fetch("SELECT COUNT(*) as c FROM contact_submissions {$where}", $params)['c'];
$totalPages = ceil($total / $perPage);

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
        <h1 class="text-2xl font-bold text-primary">Mensagens de Contacto</h1>
        <p class="text-gray-600"><?= $total ?> mensagem(ns)</p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm mb-6">
    <div class="flex border-b border-gray-200">
        <a href="?filter=all" class="px-6 py-3 text-sm font-medium <?= $filter === 'all' ? 'text-secondary-600 border-b-2 border-secondary-600' : 'text-gray-500 hover:text-gray-700' ?>">
            Todas (<?= $counts['all'] ?>)
        </a>
        <a href="?filter=unread" class="px-6 py-3 text-sm font-medium <?= $filter === 'unread' ? 'text-secondary-600 border-b-2 border-secondary-600' : 'text-gray-500 hover:text-gray-700' ?>">
            Não lidas (<?= $counts['unread'] ?>)
        </a>
        <a href="?filter=read" class="px-6 py-3 text-sm font-medium <?= $filter === 'read' ? 'text-secondary-600 border-b-2 border-secondary-600' : 'text-gray-500 hover:text-gray-700' ?>">
            Lidas (<?= $counts['read'] ?>)
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
        <div class="p-6 hover:bg-gray-50 transition-colors cursor-pointer message-row <?= !$msg['is_read'] ? 'bg-blue-50' : '' ?>"
             data-id="<?= $msg['id'] ?>">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-2">
                        <?php if (!$msg['is_read']): ?>
                        <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 rounded-lg">Nova</span>
                        <?php endif; ?>
                        <h3 class="text-sm font-semibold text-gray-900">
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
                    <!-- Mark as read/unread -->
                    <?php if (!$msg['is_read']): ?>
                    <a href="?read=<?= $msg['id'] ?>&filter=<?= $filter ?>&token=<?= CSRF::getToken() ?>"
                       class="text-sm text-blue-600 hover:text-blue-800 transition-colors"
                       title="Marcar como lida">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </a>
                    <?php else: ?>
                    <a href="?unread=<?= $msg['id'] ?>&filter=<?= $filter ?>&token=<?= CSRF::getToken() ?>"
                       class="text-sm text-gray-400 hover:text-gray-600 transition-colors"
                       title="Marcar como não lida">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"/>
                        </svg>
                    </a>
                    <?php endif; ?>

                    <!-- Reply -->
                    <a href="mailto:<?= e($msg['email']) ?>?subject=Re: <?= e($msg['subject'] ?? 'Contacto Casa do Gi') ?>"
                       class="text-sm text-green-600 hover:text-green-800 transition-colors"
                       title="Responder">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                    </a>

                    <!-- Delete -->
                    <button type="button"
                            class="delete-message-btn text-sm text-red-600 hover:text-red-800 transition-colors"
                            data-id="<?= $msg['id'] ?>"
                            data-name="<?= e($msg['name']) ?>"
                            title="Eliminar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Hidden message details for modal -->
        <div id="message-data-<?= $msg['id'] ?>" style="display:none;">
            <?= json_encode([
                'id' => $msg['id'],
                'name' => $msg['name'],
                'email' => $msg['email'],
                'phone' => $msg['phone'],
                'subject' => $msg['subject'],
                'message' => $msg['message'],
                'created_at' => formatDateTime($msg['created_at']),
                'ip_address' => $msg['ip_address'],
                'user_agent' => $msg['user_agent'],
                'language' => $msg['language'],
                'is_read' => $msg['is_read'],
                'is_spam' => $msg['is_spam'],
                'is_ignored' => $msg['is_ignored'],
            ]) ?>
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
               class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                Anterior
            </a>
            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>"
               class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                Seguinte
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="flex items-start mb-4">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-gray-900">Eliminar Mensagem</h3>
                <p class="mt-2 text-sm text-gray-500">
                    Tem a certeza que pretende eliminar a mensagem de <span id="messageName" class="font-semibold text-gray-700"></span>?<br>
                    Esta ação não pode ser desfeita.
                </p>
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Cancelar
            </button>
            <button type="button" id="confirmDeleteBtn" class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 transition-colors">
                Eliminar
            </button>
        </div>
    </div>
</div>

<!-- Message Details Modal -->
<div id="messageModal" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden">
        <!-- Header -->
        <div class="sticky top-0 bg-primary p-6 flex items-center justify-between rounded-t-2xl">
            <h2 class="text-xl font-semibold text-cream">Detalhes da Mensagem</h2>
            <button onclick="closeMessageModal()" class="text-cream hover:text-accent transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 overflow-y-auto" style="max-height: calc(90vh - 88px);">
            <div class="space-y-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                        <div id="modal-name" class="text-gray-900"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <a id="modal-email" href="" class="text-blue-600 hover:underline"></a>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                        <div id="modal-phone" class="text-gray-900"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data</label>
                        <div id="modal-date" class="text-gray-900"></div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assunto</label>
                    <div id="modal-subject" class="text-gray-900"></div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mensagem</label>
                    <div id="modal-message" class="text-gray-900 whitespace-pre-wrap bg-gray-50 p-4 rounded-lg border border-gray-200"></div>
                </div>

                <div class="grid md:grid-cols-2 gap-4 text-xs text-gray-500">
                    <div>
                        <label class="block font-medium mb-1">IP Address</label>
                        <div id="modal-ip"></div>
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Idioma</label>
                        <div id="modal-language"></div>
                    </div>
                </div>

                <div class="text-xs text-gray-500">
                    <label class="block font-medium mb-1">User Agent</label>
                    <div id="modal-useragent" class="break-all"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var csrfToken = '<?= CSRF::getToken() ?>';
    var currentFilter = '<?= e($filter) ?>';
    var pendingDeleteUrl = null;

    // Modal helpers
    function openModal(id) {
        var modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeModal(id) {
        var modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }

    function closeDeleteModal() { closeModal('deleteModal'); }
    function closeMessageModal() { closeModal('messageModal'); }

    window.closeDeleteModal = closeDeleteModal;
    window.closeMessageModal = closeMessageModal;

    // Confirm Delete - navigate via window.location
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (pendingDeleteUrl) {
            window.location.href = pendingDeleteUrl;
        }
    });

    // Event delegation for action buttons
    document.addEventListener('click', function(e) {
        // Delete button
        var deleteBtn = e.target.closest('.delete-message-btn');
        if (deleteBtn) {
            e.preventDefault();
            e.stopPropagation();

            var id = deleteBtn.dataset.id;
            var name = deleteBtn.dataset.name;

            pendingDeleteUrl = '?delete=' + id + '&filter=' + currentFilter + '&token=' + csrfToken;
            document.getElementById('messageName').textContent = name;
            openModal('deleteModal');
            return;
        }

        // Stop propagation for any action button/link in the actions area
        var actionEl = e.target.closest('.ml-4.flex.items-center.gap-2 a, .ml-4.flex.items-center.gap-2 button');
        if (actionEl) {
            e.stopPropagation();
            return;
        }

        // Message row click - open details modal
        var messageRow = e.target.closest('.message-row');
        if (messageRow) {
            var id = messageRow.dataset.id;
            if (id) {
                openMessageModal(id);
            }
        }
    });

    // Message Details Modal
    function openMessageModal(id) {
        var dataEl = document.getElementById('message-data-' + id);
        if (!dataEl) return;

        var data = JSON.parse(dataEl.textContent);

        document.getElementById('modal-name').textContent = data.name;
        document.getElementById('modal-email').textContent = data.email;
        document.getElementById('modal-email').href = 'mailto:' + data.email;
        document.getElementById('modal-phone').textContent = data.phone || '-';
        document.getElementById('modal-subject').textContent = data.subject || '-';
        document.getElementById('modal-message').textContent = data.message;
        document.getElementById('modal-date').textContent = data.created_at;
        document.getElementById('modal-ip').textContent = data.ip_address;
        document.getElementById('modal-language').textContent = data.language.toUpperCase();
        document.getElementById('modal-useragent').textContent = data.user_agent;

        openModal('messageModal');

        // Mark as read via background fetch (don't follow redirects)
        if (!data.is_read) {
            var row = document.querySelector('.message-row[data-id="' + id + '"]');
            if (row) {
                row.classList.remove('bg-blue-50');
                var badge = row.querySelector('.bg-blue-100');
                if (badge) badge.remove();
            }
            data.is_read = 1;
            fetch('?read=' + id + '&token=' + csrfToken, { redirect: 'manual' }).catch(function() {});
        }
    }
    window.openMessageModal = openMessageModal;

    // Close modals on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
            closeMessageModal();
        }
    });

    // Close modals on background click
    ['deleteModal', 'messageModal'].forEach(function(id) {
        var modal = document.getElementById(id);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal(id);
                }
            });
        }
    });
});
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
