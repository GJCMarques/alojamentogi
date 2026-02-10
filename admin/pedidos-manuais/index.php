<?php
require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;

$db = Database::getInstance();
$base = basePath();

// Action Handlers with CSRF protection
if (isset($_GET['mark_contacted']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $id = (int)$_GET['mark_contacted'];
        $db->update('manual_orders', [
            'status' => 'contacted',
            'contacted_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$id]);
        Session::flash('success', 'Pedido marcado como contactado.');
    } else {
        Session::flash('error', 'Token CSRF inválido.');
    }
    redirect($base . '/admin/pedidos-manuais/');
}

if (isset($_GET['mark_converted']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $id = (int)$_GET['mark_converted'];
        $manualOrder = $db->fetch("SELECT * FROM manual_orders WHERE id = ?", [$id]);

        if ($manualOrder && $manualOrder['status'] !== 'converted') {
            try {
                $db->beginTransaction();

                // Create real order
                $orderNumber = generateOrderNumber();
                $db->insert('orders', [
                    'order_number' => $orderNumber,
                    'customer_name' => $manualOrder['customer_name'],
                    'customer_email' => $manualOrder['customer_email'],
                    'customer_phone' => $manualOrder['customer_phone'],
                    'billing_address' => $manualOrder['shipping_address'] ?? '',
                    'billing_postal_code' => $manualOrder['shipping_postal_code'] ?? '',
                    'billing_city' => $manualOrder['shipping_city'] ?? '',
                    'shipping_same_as_billing' => 1,
                    'shipping_address' => $manualOrder['shipping_address'],
                    'shipping_postal_code' => $manualOrder['shipping_postal_code'],
                    'shipping_city' => $manualOrder['shipping_city'],
                    'subtotal' => $manualOrder['subtotal'],
                    'shipping_fee' => $manualOrder['shipping_fee'],
                    'total' => $manualOrder['total'],
                    'payment_method' => 'transfer',
                    'payment_status' => 'paid',
                    'paid_at' => date('Y-m-d H:i:s'),
                    'status' => 'confirmed',
                    'notes' => $manualOrder['notes'],
                    'admin_notes' => 'Convertido do pedido manual #' . $manualOrder['id'] . ($manualOrder['admin_notes'] ? "\n" . $manualOrder['admin_notes'] : ''),
                    'ip_address' => $manualOrder['ip_address'],
                    'user_agent' => $manualOrder['user_agent'],
                ]);

                $orderId = $db->lastInsertId();

                // Create order items from JSON
                $items = json_decode($manualOrder['items_json'], true) ?? [];
                foreach ($items as $item) {
                    $db->insert('order_items', [
                        'order_id' => $orderId,
                        'product_id' => $item['product_id'] ?? null,
                        'product_name' => $item['product_name'] ?? 'Produto',
                        'product_sku' => $item['product_sku'] ?? null,
                        'quantity' => (int)($item['quantity'] ?? 1),
                        'unit_price' => (float)($item['unit_price'] ?? 0),
                        'total_price' => (float)($item['subtotal'] ?? 0),
                    ]);
                }

                // Link manual order to real order
                $db->update('manual_orders', [
                    'status' => 'converted',
                    'converted_order_id' => $orderId,
                    'updated_at' => date('Y-m-d H:i:s')
                ], 'id = ?', [$id]);

                $db->commit();
                Session::flash('success', 'Pedido convertido em encomenda #' . $orderNumber . '.');
            } catch (\Exception $e) {
                $db->rollback();
                logMessage("Error converting manual order #{$id}: " . $e->getMessage(), 'error');
                Session::flash('error', 'Erro ao converter pedido: ' . $e->getMessage());
            }
        } else {
            Session::flash('error', 'Pedido não encontrado ou já convertido.');
        }
    } else {
        Session::flash('error', 'Token CSRF inválido.');
    }
    redirect($base . '/admin/pedidos-manuais/');
}

if (isset($_GET['mark_cancelled']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $id = (int)$_GET['mark_cancelled'];
        $db->update('manual_orders', [
            'status' => 'cancelled',
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$id]);
        Session::flash('success', 'Pedido cancelado.');
    } else {
        Session::flash('error', 'Token CSRF inválido.');
    }
    redirect($base . '/admin/pedidos-manuais/');
}

if (isset($_GET['delete']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $id = (int)$_GET['delete'];
        $db->delete('manual_orders', 'id = ?', [$id]);
        Session::flash('success', 'Pedido eliminado.');
    } else {
        Session::flash('error', 'Token CSRF inválido.');
    }
    redirect($base . '/admin/pedidos-manuais/');
}

// Admin Notes Update Handler
if (isset($_POST['update_notes'])) {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $id = (int)$_POST['order_id'];
        $notes = sanitize($_POST['admin_notes']);
        $db->update('manual_orders', [
            'admin_notes' => $notes,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$id]);
        Session::flash('success', 'Notas atualizadas.');
    } else {
        Session::flash('error', 'Token CSRF inválido.');
    }
    redirect($base . '/admin/pedidos-manuais/');
}

$pageTitle = 'Pedidos Manuais';
$currentPage = 'pedidos-manuais';

// Status filter
$statusFilter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$where = "WHERE 1=1";
$params = [];

if ($statusFilter) {
    $where .= " AND status = ?";
    $params[] = $statusFilter;
}

if ($search) {
    $where .= " AND (customer_name LIKE ? OR customer_email LIKE ? OR customer_phone LIKE ?)";
    $params = array_merge($params, ["%{$search}%", "%{$search}%", "%{$search}%"]);
}

// Counts for tabs
$counts = ['all' => 0, 'new' => 0, 'contacted' => 0, 'converted' => 0, 'cancelled' => 0];
$statusCounts = $db->fetchAll("SELECT status, COUNT(*) as c FROM manual_orders GROUP BY status");
foreach ($statusCounts as $sc) {
    $counts[$sc['status']] = (int)$sc['c'];
    $counts['all'] += (int)$sc['c'];
}

// Fetch orders
$orders = $db->fetchAll("SELECT * FROM manual_orders {$where} ORDER BY created_at DESC", $params);

include dirname(__DIR__) . '/includes/header.php';
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-primary">Pedidos Manuais</h1>
            <p class="mt-2 text-sm text-gray-600">Gerir pedidos recebidos quando a loja está em modo manual</p>
        </div>

        <!-- Status Tabs -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px overflow-x-auto">
                    <a href="<?php echo e($base); ?>/admin/pedidos-manuais/"
                       class="<?php echo $statusFilter === '' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                        Todos
                        <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium <?php echo $statusFilter === '' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-900'; ?>">
                            <?php echo e($counts['all']); ?>
                        </span>
                    </a>
                    <a href="<?php echo e($base); ?>/admin/pedidos-manuais/?status=new"
                       class="<?php echo $statusFilter === 'new' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                        Novos
                        <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium <?php echo $statusFilter === 'new' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-900'; ?>">
                            <?php echo e($counts['new']); ?>
                        </span>
                    </a>
                    <a href="<?php echo e($base); ?>/admin/pedidos-manuais/?status=contacted"
                       class="<?php echo $statusFilter === 'contacted' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                        Contactados
                        <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium <?php echo $statusFilter === 'contacted' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-900'; ?>">
                            <?php echo e($counts['contacted']); ?>
                        </span>
                    </a>
                    <a href="<?php echo e($base); ?>/admin/pedidos-manuais/?status=converted"
                       class="<?php echo $statusFilter === 'converted' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                        Convertidos
                        <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium <?php echo $statusFilter === 'converted' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-900'; ?>">
                            <?php echo e($counts['converted']); ?>
                        </span>
                    </a>
                    <a href="<?php echo e($base); ?>/admin/pedidos-manuais/?status=cancelled"
                       class="<?php echo $statusFilter === 'cancelled' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                        Cancelados
                        <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium <?php echo $statusFilter === 'cancelled' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-900'; ?>">
                            <?php echo e($counts['cancelled']); ?>
                        </span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="mb-6">
            <form method="GET" action="<?php echo e($base); ?>/admin/pedidos-manuais/" class="flex gap-4">
                <?php if ($statusFilter): ?>
                    <input type="hidden" name="status" value="<?php echo e($statusFilter); ?>">
                <?php endif; ?>
                <div class="flex-1">
                    <input type="text"
                           name="search"
                           value="<?php echo e($search); ?>"
                           placeholder="Pesquisar por nome, email ou telefone..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                </div>
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary-600 transition-colors">
                    Pesquisar
                </button>
                <?php if ($search): ?>
                    <a href="<?php echo e($base); ?>/admin/pedidos-manuais/<?php echo $statusFilter ? '?status=' . e($statusFilter) : ''; ?>"
                       class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Limpar
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Orders Grid -->
        <?php if (empty($orders)): ?>
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Sem pedidos</h3>
                <p class="mt-1 text-sm text-gray-500">
                    <?php echo $search ? 'Nenhum pedido encontrado com os critérios de pesquisa.' : 'Ainda não existem pedidos manuais.'; ?>
                </p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 gap-6">
                <?php foreach ($orders as $order):
                    $items = json_decode($order['items_json'], true) ?? [];

                    // Status badge colors
                    $statusColors = [
                        'new' => 'bg-blue-100 text-blue-800',
                        'contacted' => 'bg-yellow-100 text-yellow-800',
                        'converted' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-red-100 text-red-800'
                    ];

                    $statusLabels = [
                        'new' => 'Novo',
                        'contacted' => 'Contactado',
                        'converted' => 'Convertido',
                        'cancelled' => 'Cancelado'
                    ];

                    $statusColor = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-800';
                    $statusLabel = $statusLabels[$order['status']] ?? $order['status'];
                ?>
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6">
                        <!-- Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900"><?php echo e($order['customer_name']); ?></h3>
                                    <span class="<?php echo e($statusColor); ?> px-3 py-1 rounded-full text-xs font-medium">
                                        <?php echo e($statusLabel); ?>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500">Pedido #<?php echo e($order['id']); ?> - <?php echo e(timeAgo($order['created_at'])); ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-primary"><?php echo formatPrice($order['total']); ?></p>
                            </div>
                        </div>

                        <!-- Customer Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 pb-4 border-b border-gray-200">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Email</p>
                                <a href="mailto:<?php echo e($order['customer_email']); ?>" class="text-sm text-primary hover:underline">
                                    <?php echo e($order['customer_email']); ?>
                                </a>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Telefone</p>
                                <a href="tel:<?php echo e($order['customer_phone']); ?>" class="text-sm text-primary hover:underline">
                                    <?php echo e($order['customer_phone']); ?>
                                </a>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-sm font-medium text-gray-700">Morada de Envio</p>
                                <p class="text-sm text-gray-600">
                                    <?php echo e($order['shipping_address']); ?><br>
                                    <?php echo e($order['shipping_postal_code']); ?> <?php echo e($order['shipping_city']); ?>
                                </p>
                            </div>
                        </div>

                        <!-- Products List -->
                        <div class="mb-4 pb-4 border-b border-gray-200">
                            <p class="text-sm font-medium text-gray-700 mb-2">Produtos</p>
                            <div class="space-y-2">
                                <?php foreach ($items as $item): ?>
                                    <div class="flex justify-between items-start text-sm">
                                        <div class="flex-1">
                                            <p class="text-gray-900"><?php echo e($item['product_name'] ?? 'Produto'); ?></p>
                                            <p class="text-gray-500">Quantidade: <?php echo e($item['quantity'] ?? 1); ?></p>
                                        </div>
                                        <p class="text-gray-900 font-medium"><?php echo formatPrice($item['unit_price'] ?? 0); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-100 space-y-1">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="text-gray-900"><?php echo formatPrice($order['subtotal']); ?></span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Portes</span>
                                    <span class="text-gray-900"><?php echo formatPrice($order['shipping_fee']); ?></span>
                                </div>
                                <div class="flex justify-between text-base font-semibold pt-2 border-t border-gray-200">
                                    <span class="text-gray-900">Total</span>
                                    <span class="text-primary"><?php echo formatPrice($order['total']); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Notes -->
                        <?php if (!empty($order['notes'])): ?>
                            <div class="mb-4 pb-4 border-b border-gray-200">
                                <p class="text-sm font-medium text-gray-700 mb-1">Notas do Cliente</p>
                                <p class="text-sm text-gray-600 bg-gray-50 rounded p-3"><?php echo nl2br(e($order['notes'])); ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Admin Notes -->
                        <div class="mb-4 pb-4 border-b border-gray-200">
                            <form method="POST" action="<?php echo e($base); ?>/admin/pedidos-manuais/">
                                <input type="hidden" name="csrf_token" value="<?php echo CSRF::getToken(); ?>">
                                <input type="hidden" name="order_id" value="<?php echo e($order['id']); ?>">
                                <label for="admin_notes_<?php echo e($order['id']); ?>" class="text-sm font-medium text-gray-700 mb-2 block">
                                    Notas da Administração
                                </label>
                                <textarea
                                    id="admin_notes_<?php echo e($order['id']); ?>"
                                    name="admin_notes"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary text-sm mb-2"
                                    placeholder="Adicionar notas internas sobre este pedido..."><?php echo e($order['admin_notes']); ?></textarea>
                                <button type="submit" name="update_notes" class="px-4 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors">
                                    Guardar Notas
                                </button>
                            </form>
                        </div>

                        <!-- Additional Info -->
                        <div class="mb-4 text-xs text-gray-500 space-y-1">
                            <p>Data de Criação: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                            <?php if ($order['contacted_at']): ?>
                                <p>Contactado: <?php echo date('d/m/Y H:i', strtotime($order['contacted_at'])); ?></p>
                            <?php endif; ?>
                            <?php if ($order['converted_order_id']): ?>
                                <?php $linkedOrder = $db->fetch("SELECT order_number FROM orders WHERE id = ?", [$order['converted_order_id']]); ?>
                                <?php if ($linkedOrder): ?>
                                <p class="text-green-600 font-medium">
                                    Encomenda:
                                    <a href="<?php echo e($base); ?>/admin/encomendas/ver.php?id=<?php echo e($order['converted_order_id']); ?>" class="underline hover:text-green-800">
                                        #<?php echo e($linkedOrder['order_number']); ?>
                                    </a>
                                </p>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if ($order['ip_address']): ?>
                                <p>IP: <?php echo e($order['ip_address']); ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-2">
                            <?php if ($order['status'] === 'new'): ?>
                                <a href="<?php echo e($base); ?>/admin/pedidos-manuais/?mark_contacted=<?php echo e($order['id']); ?>&token=<?php echo CSRF::getToken(); ?>"
                                   class="px-4 py-2 bg-yellow-500 text-white text-sm rounded-lg hover:bg-yellow-600 transition-colors">
                                    Marcar Contactado
                                </a>
                            <?php endif; ?>

                            <?php if ($order['status'] === 'new' || $order['status'] === 'contacted'): ?>
                                <a href="<?php echo e($base); ?>/admin/pedidos-manuais/?mark_converted=<?php echo e($order['id']); ?>&token=<?php echo CSRF::getToken(); ?>"
                                   class="px-4 py-2 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 transition-colors">
                                    Marcar Convertido
                                </a>
                                <a href="<?php echo e($base); ?>/admin/pedidos-manuais/?mark_cancelled=<?php echo e($order['id']); ?>&token=<?php echo CSRF::getToken(); ?>"
                                   class="px-4 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition-colors">
                                    Cancelar
                                </a>
                            <?php endif; ?>

                            <button type="button"
                                    class="delete-order-btn px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300 transition-colors"
                                    data-order-id="<?php echo e($order['id']); ?>"
                                    data-customer-name="<?php echo e($order['customer_name']); ?>">
                                Eliminar
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>


<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeDeleteModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md m-4 transform transition-all scale-100">
        <div class="text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Eliminar Pedido</h3>
            <p class="text-gray-600 mb-6">
                Tem a certeza que deseja eliminar o pedido de <strong id="deleteCustomerName" class="text-gray-900"></strong>?
                Esta ação não pode ser revertida.
            </p>
            <div class="flex gap-3 justify-center">
                <button type="button"
                        onclick="closeDeleteModal()"
                        class="px-6 py-2.5 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-medium">
                    Cancelar
                </button>
                <a href="#"
                   id="confirmDelete"
                   class="px-6 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors font-medium shadow-sm">
                    Eliminar
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('deleteModal');
    const confirmBtn = document.getElementById('confirmDelete');
    const customerNameSpan = document.getElementById('deleteCustomerName');

    // Event delegation for delete buttons
    document.addEventListener('click', function(e) {
        // Find closest button with class delete-order-btn
        const btn = e.target.closest('.delete-order-btn');
        if (btn) {
            e.preventDefault();
            const orderId = btn.dataset.orderId;
            const customerName = btn.dataset.customerName;
            const deleteUrl = '<?php echo e($base); ?>/admin/pedidos-manuais/?delete=' + orderId + '&token=<?php echo CSRF::getToken(); ?>';

            customerNameSpan.textContent = customerName;
            confirmBtn.href = deleteUrl;
            modal.classList.remove('hidden');
        }
    });

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeDeleteModal();
        }
    });
});
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
