<?php
/**
 * A Casa do Gi - Admin Orders List
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;

$db = Database::getInstance();

// Handle status change
if (isset($_POST['update_status']) && isset($_POST['order_id'])) {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $orderId = (int)$_POST['order_id'];
        $newStatus = sanitize($_POST['status']);
        $notes = sanitize($_POST['notes'] ?? '');

        $validStatuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (in_array($newStatus, $validStatuses)) {
            $db->update('orders', ['status' => $newStatus], 'id = ?', [$orderId]);

            // Add to history
            $db->insert('order_status_history', [
                'order_id' => $orderId,
                'status' => $newStatus,
                'notes' => $notes,
                'changed_by' => $_SESSION['admin_id'] ?? null
            ]);

            Session::flash('success', 'Estado da encomenda atualizado.');
        }
    }
    redirect('/admin/encomendas/');
}

// Filters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Build query
$where = "WHERE 1=1";
$params = [];

if ($status) {
    $where .= " AND status = ?";
    $params[] = $status;
}

if ($search) {
    $where .= " AND (order_number LIKE ? OR customer_name LIKE ? OR customer_email LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

// Get counts by status
$statusCounts = $db->fetchAll("SELECT status, COUNT(*) as count FROM orders GROUP BY status");
$counts = ['all' => 0];
foreach ($statusCounts as $sc) {
    $counts[$sc['status']] = $sc['count'];
    $counts['all'] += $sc['count'];
}

// Get total
$total = $db->fetch("SELECT COUNT(*) as c FROM orders {$where}", $params)['c'];
$totalPages = ceil($total / $perPage);

// Get orders
$orders = $db->fetchAll(
    "SELECT * FROM orders {$where} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}",
    $params
);

// Status labels
$statusLabels = [
    'pending' => ['label' => 'Pendente', 'class' => 'bg-yellow-100 text-yellow-800'],
    'confirmed' => ['label' => 'Confirmada', 'class' => 'bg-blue-100 text-blue-800'],
    'processing' => ['label' => 'Em Preparação', 'class' => 'bg-purple-100 text-purple-800'],
    'shipped' => ['label' => 'Enviada', 'class' => 'bg-indigo-100 text-indigo-800'],
    'delivered' => ['label' => 'Entregue', 'class' => 'bg-green-100 text-green-800'],
    'cancelled' => ['label' => 'Cancelada', 'class' => 'bg-red-100 text-red-800'],
];

$paymentLabels = [
    'pending' => ['label' => 'Pendente', 'class' => 'text-yellow-600'],
    'processing' => ['label' => 'A processar', 'class' => 'text-blue-600'],
    'paid' => ['label' => 'Pago', 'class' => 'text-green-600'],
    'failed' => ['label' => 'Falhado', 'class' => 'text-red-600'],
    'refunded' => ['label' => 'Reembolsado', 'class' => 'text-gray-600'],
];

$pageTitle = 'Encomendas';
$currentPage = 'encomendas';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-primary">Encomendas</h1>
        <p class="text-gray-600"><?= $total ?> encomenda(s)</p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm mb-6">
    <div class="p-4 border-b border-gray-200">
        <form action="" method="get" class="flex items-center gap-4">
            <div class="flex-1">
                <input type="text"
                       name="search"
                       value="<?= e($search) ?>"
                       placeholder="Pesquisar por nº encomenda, nome ou email..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700">
                Pesquisar
            </button>
            <?php if ($search || $status): ?>
            <a href="<?= basePath() ?>/admin/encomendas/" class="text-sm text-gray-500 hover:text-gray-700">Limpar</a>
            <?php endif; ?>
        </form>
    </div>
    <div class="flex flex-wrap border-b border-gray-200">
        <a href="?<?= $search ? 'search=' . urlencode($search) : '' ?>"
           class="px-4 py-3 text-sm font-medium <?= !$status ? 'text-secondary-600 border-b-2 border-secondary-600' : 'text-gray-500 hover:text-gray-700' ?>">
            Todas (<?= $counts['all'] ?? 0 ?>)
        </a>
        <?php foreach ($statusLabels as $key => $info): ?>
        <a href="?status=<?= $key ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
           class="px-4 py-3 text-sm font-medium <?= $status === $key ? 'text-secondary-600 border-b-2 border-secondary-600' : 'text-gray-500 hover:text-gray-700' ?>">
            <?= $info['label'] ?> (<?= $counts[$key] ?? 0 ?>)
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Orders Table -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <?php if (empty($orders)): ?>
    <div class="p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <h3 class="text-lg font-medium text-gray-700 mb-2">Nenhuma encomenda</h3>
        <p class="text-gray-500">Não existem encomendas com estes filtros.</p>
    </div>
    <?php else: ?>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Encomenda</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pagamento</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php foreach ($orders as $order): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <a href="<?= basePath() ?>/admin/encomendas/ver.php?id=<?= $order['id'] ?>" class="font-medium text-secondary-600 hover:text-olive-800">
                        #<?= e($order['order_number']) ?>
                    </a>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900"><?= e($order['customer_name']) ?></div>
                    <div class="text-sm text-gray-500"><?= e($order['customer_email']) ?></div>
                </td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                    <?= formatPrice($order['total']) ?>
                </td>
                <td class="px-6 py-4">
                    <span class="text-sm <?= $paymentLabels[$order['payment_status']]['class'] ?? 'text-gray-600' ?>">
                        <?= $paymentLabels[$order['payment_status']]['label'] ?? $order['payment_status'] ?>
                    </span>
                    <div class="text-xs text-gray-400"><?= strtoupper($order['payment_method']) ?></div>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded <?= $statusLabels[$order['status']]['class'] ?? 'bg-gray-100 text-gray-800' ?>">
                        <?= $statusLabels[$order['status']]['label'] ?? $order['status'] ?>
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    <?= formatDateTime($order['created_at']) ?>
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="<?= basePath() ?>/admin/encomendas/ver.php?id=<?= $order['id'] ?>" class="text-secondary-600 hover:text-olive-800 text-sm">
                        Ver detalhes
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
        <div class="text-sm text-gray-500">Página <?= $page ?> de <?= $totalPages ?></div>
        <div class="flex gap-2">
            <?php if ($page > 1): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>"
               class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Anterior</a>
            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>"
               class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Seguinte</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
