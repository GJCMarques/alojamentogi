<?php
/**
 * A Casa do Gi - Admin Order Details
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;

$db = Database::getInstance();

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$orderId) {
    redirect('/admin/encomendas/');
}

// Get order
$order = $db->fetch("SELECT * FROM orders WHERE id = ?", [$orderId]);

if (!$order) {
    Session::flash('error', 'Encomenda não encontrada.');
    redirect('/admin/encomendas/');
}

// Handle status change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $newStatus = sanitize($_POST['status']);
        $notes = sanitize($_POST['notes'] ?? '');

        $validStatuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (in_array($newStatus, $validStatuses)) {
            $updateData = ['status' => $newStatus];

            // Add tracking code if provided
            if (!empty($_POST['tracking_code'])) {
                $updateData['tracking_code'] = sanitize($_POST['tracking_code']);
            }

            $db->update('orders', $updateData, 'id = ?', [$orderId]);

            // Add to history
            $db->insert('order_status_history', [
                'order_id' => $orderId,
                'status' => $newStatus,
                'notes' => $notes,
                'changed_by' => $_SESSION['admin_id'] ?? null
            ]);

            Session::flash('success', 'Estado da encomenda atualizado.');
            redirect('/admin/encomendas/ver.php?id=' . $orderId);
        }
    }
}

// Get order items
$items = $db->fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$orderId]);

// Get status history
$history = $db->fetchAll(
    "SELECT h.*, a.full_name as admin_name
     FROM order_status_history h
     LEFT JOIN admins a ON h.changed_by = a.id
     WHERE h.order_id = ?
     ORDER BY h.created_at DESC",
    [$orderId]
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
    'pending' => 'Pendente',
    'processing' => 'A processar',
    'paid' => 'Pago',
    'failed' => 'Falhado',
    'refunded' => 'Reembolsado',
];

$pageTitle = 'Encomenda #' . $order['order_number'];
$currentPage = 'encomendas';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="mb-6">
    <a href="/admin/encomendas/" class="text-secondary-600 hover:text-secondary-700 text-sm">&larr; Voltar às Encomendas</a>
</div>

<div class="flex justify-between items-start mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Encomenda #<?= e($order['order_number']) ?></h1>
        <p class="text-gray-600"><?= formatDateTime($order['created_at']) ?></p>
    </div>
    <span class="inline-flex px-3 py-1 text-sm font-medium rounded <?= $statusLabels[$order['status']]['class'] ?>">
        <?= $statusLabels[$order['status']]['label'] ?>
    </span>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Order Items -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-800">Produtos</h2>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produto</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qtd</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Preço</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900"><?= e($item['product_name']) ?></div>
                            <?php if ($item['product_sku']): ?>
                            <div class="text-xs text-gray-500">SKU: <?= e($item['product_sku']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-600">
                            <?= $item['quantity'] ?>
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-gray-600">
                            <?= formatPrice($item['unit_price']) ?>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                            <?= formatPrice($item['total_price']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right text-sm text-gray-600">Subtotal</td>
                        <td class="px-6 py-3 text-right text-sm font-medium text-gray-900"><?= formatPrice($order['subtotal']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right text-sm text-gray-600">Portes</td>
                        <td class="px-6 py-3 text-right text-sm text-gray-900"><?= formatPrice($order['shipping_fee']) ?></td>
                    </tr>
                    <?php if ($order['discount_amount'] > 0): ?>
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right text-sm text-gray-600">Desconto</td>
                        <td class="px-6 py-3 text-right text-sm text-green-600">-<?= formatPrice($order['discount_amount']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right text-sm font-bold text-gray-800">Total</td>
                        <td class="px-6 py-3 text-right text-lg font-bold text-secondary-600"><?= formatPrice($order['total']) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Customer Info -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-medium text-gray-800 mb-4">Dados do Cliente</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Contacto</h3>
                    <p class="text-gray-900 font-medium"><?= e($order['customer_name']) ?></p>
                    <p class="text-gray-600"><?= e($order['customer_email']) ?></p>
                    <p class="text-gray-600"><?= e($order['customer_phone']) ?></p>
                    <?php if ($order['customer_nif']): ?>
                    <p class="text-gray-500 text-sm mt-1">NIF: <?= e($order['customer_nif']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Morada de Faturação</h3>
                    <p class="text-gray-600"><?= nl2br(e($order['billing_address'])) ?></p>
                    <p class="text-gray-600"><?= e($order['billing_postal_code']) ?> <?= e($order['billing_city']) ?></p>
                    <p class="text-gray-600"><?= e($order['billing_country']) ?></p>
                </div>
                <?php if (!$order['shipping_same_as_billing']): ?>
                <div class="md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Morada de Envio</h3>
                    <p class="text-gray-600"><?= nl2br(e($order['shipping_address'])) ?></p>
                    <p class="text-gray-600"><?= e($order['shipping_postal_code']) ?> <?= e($order['shipping_city']) ?></p>
                    <p class="text-gray-600"><?= e($order['shipping_country']) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Status History -->
        <?php if (!empty($history)): ?>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-medium text-gray-800 mb-4">Histórico</h2>
            <div class="space-y-4">
                <?php foreach ($history as $h): ?>
                <div class="flex items-start gap-3">
                    <div class="w-2 h-2 mt-2 rounded-full bg-secondary-500"></div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded <?= $statusLabels[$h['status']]['class'] ?? 'bg-gray-100' ?>">
                                <?= $statusLabels[$h['status']]['label'] ?? $h['status'] ?>
                            </span>
                            <span class="text-xs text-gray-500"><?= formatDateTime($h['created_at']) ?></span>
                            <?php if ($h['admin_name']): ?>
                            <span class="text-xs text-gray-400">por <?= e($h['admin_name']) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if ($h['notes']): ?>
                        <p class="text-sm text-gray-600 mt-1"><?= e($h['notes']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Payment Info -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-medium text-gray-800 mb-4">Pagamento</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Método</span>
                    <span class="font-medium"><?= strtoupper($order['payment_method']) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Estado</span>
                    <span class="font-medium"><?= $paymentLabels[$order['payment_status']] ?? $order['payment_status'] ?></span>
                </div>
                <?php if ($order['payment_reference']): ?>
                <div class="flex justify-between">
                    <span class="text-gray-600">Referência</span>
                    <span class="font-mono text-sm"><?= e($order['payment_reference']) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($order['payment_entity']): ?>
                <div class="flex justify-between">
                    <span class="text-gray-600">Entidade</span>
                    <span class="font-mono text-sm"><?= e($order['payment_entity']) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($order['paid_at']): ?>
                <div class="flex justify-between">
                    <span class="text-gray-600">Pago em</span>
                    <span class="text-sm"><?= formatDateTime($order['paid_at']) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Update Status -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-medium text-gray-800 mb-4">Atualizar Estado</h2>
            <form action="" method="post">
                <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
                <input type="hidden" name="update_status" value="1">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Novo Estado</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                            <?php foreach ($statusLabels as $key => $info): ?>
                            <option value="<?= $key ?>" <?= $order['status'] === $key ? 'selected' : '' ?>>
                                <?= $info['label'] ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Código de Rastreio</label>
                        <input type="text"
                               name="tracking_code"
                               value="<?= e($order['tracking_code'] ?? '') ?>"
                               placeholder="CTT, DPD, etc."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                        <textarea name="notes"
                                  rows="3"
                                  placeholder="Notas internas..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500"></textarea>
                    </div>

                    <button type="submit" class="w-full px-4 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700">
                        Atualizar
                    </button>
                </div>
            </form>
        </div>

        <!-- Admin Notes -->
        <?php if ($order['admin_notes']): ?>
        <div class="bg-yellow-50 rounded-lg p-4">
            <h3 class="text-sm font-medium text-yellow-800 mb-2">Notas Internas</h3>
            <p class="text-sm text-yellow-700"><?= nl2br(e($order['admin_notes'])) ?></p>
        </div>
        <?php endif; ?>

        <!-- Customer Notes -->
        <?php if ($order['notes']): ?>
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Notas do Cliente</h3>
            <p class="text-sm text-gray-600"><?= nl2br(e($order['notes'])) ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
