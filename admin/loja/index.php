<?php
/**
 * A Casa do Gi - Admin Shop Management
 * Manage shop mode and view shop statistics
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;

$db = Database::getInstance();

// Handle mode change
if (isset($_POST['update_mode'])) {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $mode = sanitize($_POST['shop_mode']);
        if (in_array($mode, ['active', 'manual', 'closed'])) {
            // Update or insert the setting
            $existing = $db->fetch("SELECT id FROM settings WHERE setting_key = 'shop_mode'");

            if ($existing) {
                $db->update('settings', ['setting_value' => $mode], 'setting_key = ?', ['shop_mode']);
            } else {
                $db->insert('settings', [
                    'setting_key' => 'shop_mode',
                    'setting_value' => $mode,
                    'setting_type' => 'text',
                    'setting_group' => 'shop',
                    'description' => 'Modo de funcionamento da loja',
                    'is_public' => 0
                ]);
            }

            Session::flash('success', 'Modo da loja atualizado com sucesso.');
        } else {
            Session::flash('error', 'Modo inválido selecionado.');
        }
    } else {
        Session::flash('error', 'Token de segurança inválido.');
    }
    redirect(basePath() . '/admin/loja/');
}

// Get current mode
$shopMode = setting('shop_mode', 'active');

// Get statistics
$stats = [
    'total_orders' => 0,
    'pending_orders' => 0,
    'total_revenue' => 0,
    'pending_manual' => 0
];

// Total orders count
$result = $db->fetch("SELECT COUNT(*) as c FROM orders");
$stats['total_orders'] = $result ? (int)$result['c'] : 0;

// Pending orders count
$result = $db->fetch("SELECT COUNT(*) as c FROM orders WHERE status = 'pending'");
$stats['pending_orders'] = $result ? (int)$result['c'] : 0;

// Total revenue from paid orders
$result = $db->fetch("SELECT COALESCE(SUM(total), 0) as total FROM orders WHERE payment_status = 'paid'");
$stats['total_revenue'] = $result ? (float)$result['total'] : 0;

// Pending manual orders
$result = $db->fetch("SELECT COUNT(*) as c FROM manual_orders WHERE status = 'new'");
$stats['pending_manual'] = $result ? (int)$result['c'] : 0;

// Get recent orders (last 5)
$recentOrders = $db->fetchAll(
    "SELECT id, order_number, customer_name, customer_email, total, status, payment_status, created_at
     FROM orders
     ORDER BY created_at DESC
     LIMIT 5"
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

$pageTitle = 'Gestão da Loja';
$currentPage = 'loja';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-charcoal-800 mb-2">Gestão da Loja</h1>
        <p class="text-charcoal-600">Configure o modo de funcionamento da loja e visualize estatísticas.</p>
    </div>

    <!-- Shop Mode Configuration -->
    <div class="bg-white rounded-lg shadow-sm border border-accent/20 mb-8">
        <div class="px-6 py-4 border-b border-accent/20">
            <h2 class="text-lg font-semibold text-primary flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                </svg>
                Modo de Funcionamento
            </h2>
        </div>
        <div class="p-6">
            <form action="" method="post" class="space-y-6">
                <?= CSRF::tokenField() ?>

                <!-- Mode Selection Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Active Mode -->
                    <label class="relative cursor-pointer">
                        <input type="radio"
                               name="shop_mode"
                               value="active"
                               <?= $shopMode === 'active' ? 'checked' : '' ?>
                               class="peer sr-only">
                        <div class="border-2 rounded-lg p-6 transition-all duration-200
                                    border-gray-200 hover:border-secondary-300 hover:shadow-md
                                    peer-checked:border-secondary-500 peer-checked:bg-secondary-50 peer-checked:shadow-lg">
                            <div class="flex flex-col items-center text-center space-y-3">
                                <!-- Icon -->
                                <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center
                                            peer-checked:bg-secondary-200 transition-colors">
                                    <svg class="w-8 h-8 text-green-600 peer-checked:text-secondary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <!-- Title -->
                                <div>
                                    <h3 class="text-lg font-semibold text-charcoal-800">Ativa</h3>
                                    <p class="text-sm text-charcoal-600 mt-1">
                                        Loja totalmente funcional com pagamentos automáticos online.
                                    </p>
                                </div>
                                <!-- Current Badge -->
                                <?php if ($shopMode === 'active'): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-secondary-600 text-white">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Modo Atual
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </label>

                    <!-- Manual Mode -->
                    <label class="relative cursor-pointer">
                        <input type="radio"
                               name="shop_mode"
                               value="manual"
                               <?= $shopMode === 'manual' ? 'checked' : '' ?>
                               class="peer sr-only">
                        <div class="border-2 rounded-lg p-6 transition-all duration-200
                                    border-gray-200 hover:border-secondary-300 hover:shadow-md
                                    peer-checked:border-secondary-500 peer-checked:bg-secondary-50 peer-checked:shadow-lg">
                            <div class="flex flex-col items-center text-center space-y-3">
                                <!-- Icon -->
                                <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center
                                            peer-checked:bg-secondary-200 transition-colors">
                                    <svg class="w-8 h-8 text-blue-600 peer-checked:text-secondary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <!-- Title -->
                                <div>
                                    <h3 class="text-lg font-semibold text-charcoal-800">Manual</h3>
                                    <p class="text-sm text-charcoal-600 mt-1">
                                        Carrinho enviado para backoffice. Pagamento combinado por telefone.
                                    </p>
                                </div>
                                <!-- Current Badge -->
                                <?php if ($shopMode === 'manual'): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-secondary-600 text-white">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Modo Atual
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </label>

                    <!-- Closed Mode -->
                    <label class="relative cursor-pointer">
                        <input type="radio"
                               name="shop_mode"
                               value="closed"
                               <?= $shopMode === 'closed' ? 'checked' : '' ?>
                               class="peer sr-only">
                        <div class="border-2 rounded-lg p-6 transition-all duration-200
                                    border-gray-200 hover:border-secondary-300 hover:shadow-md
                                    peer-checked:border-secondary-500 peer-checked:bg-secondary-50 peer-checked:shadow-lg">
                            <div class="flex flex-col items-center text-center space-y-3">
                                <!-- Icon -->
                                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center
                                            peer-checked:bg-secondary-200 transition-colors">
                                    <svg class="w-8 h-8 text-red-600 peer-checked:text-secondary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                                <!-- Title -->
                                <div>
                                    <h3 class="text-lg font-semibold text-charcoal-800">Fechada</h3>
                                    <p class="text-sm text-charcoal-600 mt-1">
                                        Loja completamente desativada. Visitantes não podem fazer encomendas.
                                    </p>
                                </div>
                                <!-- Current Badge -->
                                <?php if ($shopMode === 'closed'): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-secondary-600 text-white">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Modo Atual
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end pt-4 border-t border-accent/20">
                    <button type="submit"
                            name="update_mode"
                            class="inline-flex items-center px-6 py-3 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700 focus:outline-none focus:ring-2 focus:ring-secondary-500 focus:ring-offset-2 transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Dashboard -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-charcoal-800 mb-4">Estatísticas Rápidas</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Orders -->
            <div class="bg-white rounded-lg shadow-sm border border-accent/20 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-charcoal-600 uppercase tracking-wider">Total de Encomendas</p>
                        <p class="mt-2 text-3xl font-bold text-primary"><?= number_format($stats['total_orders']) ?></p>
                    </div>
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="<?= basePath() ?>/admin/encomendas/" class="text-sm text-secondary-600 hover:text-secondary-800 font-medium flex items-center">
                        Ver todas
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Pending Orders -->
            <div class="bg-white rounded-lg shadow-sm border border-accent/20 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-charcoal-600 uppercase tracking-wider">Encomendas Pendentes</p>
                        <p class="mt-2 text-3xl font-bold text-yellow-600"><?= number_format($stats['pending_orders']) ?></p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="<?= basePath() ?>/admin/encomendas/?status=pending" class="text-sm text-secondary-600 hover:text-secondary-800 font-medium flex items-center">
                        Ver pendentes
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white rounded-lg shadow-sm border border-accent/20 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-charcoal-600 uppercase tracking-wider">Receita Total</p>
                        <p class="mt-2 text-3xl font-bold text-green-600"><?= formatPrice($stats['total_revenue']) ?></p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-charcoal-500">Apenas encomendas pagas</span>
                </div>
            </div>

            <!-- Pending Manual Orders -->
            <div class="bg-white rounded-lg shadow-sm border border-accent/20 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-charcoal-600 uppercase tracking-wider">Pedidos Manuais</p>
                        <p class="mt-2 text-3xl font-bold text-blue-600"><?= number_format($stats['pending_manual']) ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="<?= basePath() ?>/admin/pedidos-manuais/" class="text-sm text-secondary-600 hover:text-secondary-800 font-medium flex items-center">
                        Ver pedidos
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow-sm border border-accent/20">
        <div class="px-6 py-4 border-b border-accent/20 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-primary flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Últimas Encomendas
            </h2>
            <a href="<?= basePath() ?>/admin/encomendas/" class="text-sm text-secondary-600 hover:text-secondary-800 font-medium">
                Ver todas
            </a>
        </div>

        <?php if (empty($recentOrders)): ?>
        <div class="p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-700 mb-2">Nenhuma encomenda ainda</h3>
            <p class="text-gray-500">As encomendas aparecerão aqui quando começarem a chegar.</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-cream-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-charcoal-600 uppercase tracking-wider">Encomenda</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-charcoal-600 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-charcoal-600 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-charcoal-600 uppercase tracking-wider">Pagamento</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-charcoal-600 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-charcoal-600 uppercase tracking-wider">Data</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-charcoal-600 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($recentOrders as $order): ?>
                    <tr class="hover:bg-secondary-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="<?= basePath() ?>/admin/encomendas/ver.php?id=<?= $order['id'] ?>"
                               class="font-medium text-secondary-600 hover:text-secondary-800">
                                #<?= e($order['order_number']) ?>
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-charcoal-900"><?= e($order['customer_name']) ?></div>
                            <div class="text-sm text-charcoal-500"><?= e($order['customer_email']) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-semibold text-charcoal-900"><?= formatPrice($order['total']) ?></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm <?= $paymentLabels[$order['payment_status']]['class'] ?? 'text-gray-600' ?>">
                                <?= $paymentLabels[$order['payment_status']]['label'] ?? $order['payment_status'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded <?= $statusLabels[$order['status']]['class'] ?? 'bg-gray-100 text-gray-800' ?>">
                                <?= $statusLabels[$order['status']]['label'] ?? $order['status'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-charcoal-600">
                            <?= formatDateTime($order['created_at']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <a href="<?= basePath() ?>/admin/encomendas/ver.php?id=<?= $order['id'] ?>"
                               class="text-secondary-600 hover:text-secondary-800 font-medium">
                                Ver detalhes
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
