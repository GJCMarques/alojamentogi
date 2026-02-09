<?php
/**
 * A Casa do Gi - Admin Dashboard
 */

require_once dirname(__DIR__) . '/includes/init.php';
require_once __DIR__ . '/includes/auth-check.php';

use Core\Database;
use Core\Auth;

$db = Database::getInstance();
$base = basePath();

// Get shop mode
$shopMode = setting('shop_mode', 'active');

// Get statistics
$stats = [
    'products' => $db->count('products', 'is_active = 1'),
    'orders_pending' => $db->count('orders', "status IN ('pending', 'confirmed')"),
    'orders_total' => $db->count('orders'),
    'orders_shipped' => $db->count('orders', "status = 'shipped'"),
    'messages_unread' => $db->count('contact_submissions', 'is_read = 0'),
    'activities' => $db->count('activities', 'is_active = 1'),
    'invoices_total' => $db->count('invoices'),
    'invoices_pending' => $db->count('invoices', "payment_status = 'pending'"),
    'manual_orders_new' => $db->count('manual_orders', "status = 'new'"),
];

// Get recent orders
$recentOrders = $db->fetchAll(
    "SELECT * FROM orders ORDER BY created_at DESC LIMIT 5"
);

// Get recent messages
$recentMessages = $db->fetchAll(
    "SELECT * FROM contact_submissions ORDER BY created_at DESC LIMIT 5"
);

// Get recent manual orders
$recentManualOrders = $db->fetchAll(
    "SELECT * FROM manual_orders WHERE status = 'new' ORDER BY created_at DESC LIMIT 5"
);

// Revenue this month
$monthRevenue = $db->fetchColumn(
    "SELECT COALESCE(SUM(total), 0) FROM orders
     WHERE payment_status = 'paid' AND MONTH(created_at) = MONTH(CURRENT_DATE())
     AND YEAR(created_at) = YEAR(CURRENT_DATE())"
);

// Revenue total
$totalRevenue = $db->fetchColumn(
    "SELECT COALESCE(SUM(total), 0) FROM orders WHERE payment_status = 'paid'"
);

$pageTitle = 'Dashboard';

include __DIR__ . '/includes/header.php';
?>

<!-- Shop Mode Banner -->
<?php if ($shopMode !== 'active'): ?>
<div class="mb-6 p-4 rounded-lg flex items-center gap-3
    <?= $shopMode === 'closed' ? 'bg-red-50 border border-red-200' : 'bg-yellow-50 border border-yellow-200' ?>">
    <div class="flex-shrink-0">
        <?php if ($shopMode === 'closed'): ?>
        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
        </svg>
        <?php else: ?>
        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <?php endif; ?>
    </div>
    <div class="flex-1">
        <p class="text-sm font-medium <?= $shopMode === 'closed' ? 'text-red-800' : 'text-yellow-800' ?>">
            Loja <?= $shopMode === 'closed' ? 'Fechada' : 'em Modo Manual' ?>
        </p>
        <p class="text-xs <?= $shopMode === 'closed' ? 'text-red-600' : 'text-yellow-600' ?> mt-0.5">
            <?= $shopMode === 'closed'
                ? 'A loja esta fechada ao publico. Nenhuma encomenda pode ser feita.'
                : 'Os clientes podem enviar pedidos mas o pagamento e tratado manualmente.' ?>
        </p>
    </div>
    <a href="<?= $base ?>/admin/loja/" class="px-3 py-1.5 text-xs font-medium rounded-lg
        <?= $shopMode === 'closed' ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' ?> transition-colors">
        Gerir Loja
    </a>
</div>
<?php endif; ?>

<!-- Stats Cards - Row 1 -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Revenue This Month -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-granite-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-granite-500">Receita (Este Mes)</p>
                <p class="text-2xl font-bold text-granite-800 mt-1"><?= formatPrice($monthRevenue) ?></p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-xs text-granite-400 mt-3">Total acumulado: <?= formatPrice($totalRevenue) ?></p>
    </div>

    <!-- Pending Orders -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-granite-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-granite-500">Encomendas Pendentes</p>
                <p class="text-2xl font-bold text-granite-800 mt-1"><?= $stats['orders_pending'] ?></p>
            </div>
            <div class="w-12 h-12 <?= $stats['orders_pending'] > 0 ? 'bg-terracotta-100' : 'bg-granite-100' ?> rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 <?= $stats['orders_pending'] > 0 ? 'text-terracotta-600' : 'text-granite-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
        </div>
        <a href="<?= $base ?>/admin/encomendas/" class="inline-flex items-center text-xs text-secondary-600 hover:text-secondary-700 mt-3">
            Ver encomendas
            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <!-- Manual Orders -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-granite-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-granite-500">Pedidos Manuais</p>
                <p class="text-2xl font-bold text-granite-800 mt-1"><?= $stats['manual_orders_new'] ?></p>
            </div>
            <div class="w-12 h-12 <?= $stats['manual_orders_new'] > 0 ? 'bg-accent-100' : 'bg-granite-100' ?> rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 <?= $stats['manual_orders_new'] > 0 ? 'text-accent-600' : 'text-granite-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
            </div>
        </div>
        <a href="<?= $base ?>/admin/pedidos-manuais/" class="inline-flex items-center text-xs text-secondary-600 hover:text-secondary-700 mt-3">
            Ver pedidos
            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <!-- Messages -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-granite-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-granite-500">Mensagens por Ler</p>
                <p class="text-2xl font-bold text-granite-800 mt-1"><?= $stats['messages_unread'] ?></p>
            </div>
            <div class="w-12 h-12 <?= $stats['messages_unread'] > 0 ? 'bg-blue-100' : 'bg-granite-100' ?> rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 <?= $stats['messages_unread'] > 0 ? 'text-blue-600' : 'text-granite-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
        <a href="<?= $base ?>/admin/mensagens/" class="inline-flex items-center text-xs text-secondary-600 hover:text-secondary-700 mt-3">
            Ver mensagens
            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</div>

<!-- Stats Cards - Row 2 -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Products -->
    <div class="bg-white rounded-lg shadow-sm p-5 border border-granite-200">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-secondary-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <div>
                <p class="text-lg font-bold text-granite-800"><?= $stats['products'] ?></p>
                <p class="text-xs text-granite-500">Produtos Ativos</p>
            </div>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="bg-white rounded-lg shadow-sm p-5 border border-granite-200">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
            </div>
            <div>
                <p class="text-lg font-bold text-granite-800"><?= $stats['orders_total'] ?></p>
                <p class="text-xs text-granite-500">Total Encomendas</p>
            </div>
        </div>
    </div>

    <!-- Invoices -->
    <div class="bg-white rounded-lg shadow-sm p-5 border border-granite-200">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-lg font-bold text-granite-800"><?= $stats['invoices_total'] ?></p>
                <p class="text-xs text-granite-500">Faturas Emitidas</p>
            </div>
        </div>
    </div>

    <!-- Activities -->
    <div class="bg-white rounded-lg shadow-sm p-5 border border-granite-200">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-lg font-bold text-granite-800"><?= $stats['activities'] ?></p>
                <p class="text-xs text-granite-500">Atividades Ativas</p>
            </div>
        </div>
    </div>
</div>

<!-- Content Grid -->
<div class="grid lg:grid-cols-2 gap-6 mb-6">
    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow-sm border border-granite-200">
        <div class="px-6 py-4 border-b border-granite-200 flex items-center justify-between">
            <h2 class="font-semibold text-granite-800">Encomendas Recentes</h2>
            <a href="<?= $base ?>/admin/encomendas/" class="text-sm text-secondary-600 hover:text-secondary-700">Ver todas</a>
        </div>
        <div class="divide-y divide-granite-100">
            <?php if (empty($recentOrders)): ?>
            <div class="px-6 py-8 text-center text-granite-500">
                <svg class="w-12 h-12 mx-auto text-granite-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p>Ainda nao ha encomendas</p>
            </div>
            <?php else: ?>
                <?php foreach ($recentOrders as $order): ?>
                <a href="<?= $base ?>/admin/encomendas/ver.php?id=<?= $order['id'] ?>" class="block px-6 py-4 hover:bg-granite-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-granite-800"><?= e($order['order_number']) ?></p>
                            <p class="text-sm text-granite-500"><?= e($order['customer_name']) ?></p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-granite-800"><?= formatPrice($order['total']) ?></p>
                            <?php
                            $statusLabels = [
                                'pending' => ['Pendente', 'bg-yellow-100 text-yellow-700'],
                                'confirmed' => ['Confirmada', 'bg-blue-100 text-blue-700'],
                                'processing' => ['Em Processamento', 'bg-purple-100 text-purple-700'],
                                'shipped' => ['Enviada', 'bg-indigo-100 text-indigo-700'],
                                'delivered' => ['Entregue', 'bg-green-100 text-green-700'],
                                'cancelled' => ['Cancelada', 'bg-red-100 text-red-700'],
                            ];
                            $sl = $statusLabels[$order['status']] ?? [ucfirst($order['status']), 'bg-granite-100 text-granite-700'];
                            ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?= $sl[1] ?>">
                                <?= $sl[0] ?>
                            </span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Messages -->
    <div class="bg-white rounded-lg shadow-sm border border-granite-200">
        <div class="px-6 py-4 border-b border-granite-200 flex items-center justify-between">
            <h2 class="font-semibold text-granite-800">Mensagens Recentes</h2>
            <a href="<?= $base ?>/admin/mensagens/" class="text-sm text-secondary-600 hover:text-secondary-700">Ver todas</a>
        </div>
        <div class="divide-y divide-granite-100">
            <?php if (empty($recentMessages)): ?>
            <div class="px-6 py-8 text-center text-granite-500">
                <svg class="w-12 h-12 mx-auto text-granite-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <p>Ainda nao ha mensagens</p>
            </div>
            <?php else: ?>
                <?php foreach ($recentMessages as $msg): ?>
                <a href="<?= $base ?>/admin/mensagens/?id=<?= $msg['id'] ?>" class="block px-6 py-4 hover:bg-granite-50 transition-colors">
                    <div class="flex items-start space-x-3">
                        <?php if (!$msg['is_read']): ?>
                        <span class="w-2 h-2 bg-terracotta-500 rounded-full mt-2 flex-shrink-0"></span>
                        <?php else: ?>
                        <span class="w-2 h-2 flex-shrink-0"></span>
                        <?php endif; ?>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="font-medium text-granite-800 truncate"><?= e($msg['name']) ?></p>
                                <span class="text-xs text-granite-400 flex-shrink-0 ml-2"><?= timeAgo($msg['created_at']) ?></span>
                            </div>
                            <p class="text-sm text-granite-600 truncate"><?= e($msg['subject'] ?: 'Sem assunto') ?></p>
                            <p class="text-sm text-granite-500 truncate"><?= e(truncate($msg['message'], 60)) ?></p>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Manual Orders (if any pending) -->
<?php if (!empty($recentManualOrders)): ?>
<div class="bg-white rounded-lg shadow-sm border border-granite-200 mb-6">
    <div class="px-6 py-4 border-b border-granite-200 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <h2 class="font-semibold text-granite-800">Pedidos Manuais Novos</h2>
            <span class="bg-accent text-primary text-xs font-bold px-2 py-0.5 rounded-full"><?= $stats['manual_orders_new'] ?></span>
        </div>
        <a href="<?= $base ?>/admin/pedidos-manuais/" class="text-sm text-secondary-600 hover:text-secondary-700">Ver todos</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-granite-200">
            <thead class="bg-granite-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-granite-500 uppercase">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-granite-500 uppercase">Telefone</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-granite-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-granite-500 uppercase">Data</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-granite-100">
                <?php foreach ($recentManualOrders as $mo): ?>
                <tr class="hover:bg-granite-50">
                    <td class="px-6 py-3">
                        <p class="text-sm font-medium text-granite-800"><?= e($mo['customer_name']) ?></p>
                        <p class="text-xs text-granite-500"><?= e($mo['customer_email']) ?></p>
                    </td>
                    <td class="px-6 py-3 text-sm text-granite-600"><?= e($mo['customer_phone']) ?></td>
                    <td class="px-6 py-3 text-sm font-medium text-granite-800 text-right"><?= formatPrice($mo['total']) ?></td>
                    <td class="px-6 py-3 text-xs text-granite-500 text-right"><?= timeAgo($mo['created_at']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Quick Actions -->
<div class="bg-white rounded-lg shadow-sm border border-granite-200 p-6">
    <h2 class="font-semibold text-granite-800 mb-4">Acoes Rapidas</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <a href="<?= $base ?>/admin/loja/" class="flex flex-col items-center p-4 bg-granite-50 rounded-lg hover:bg-granite-100 transition-colors text-center">
            <div class="w-10 h-10 bg-secondary-100 rounded-lg flex items-center justify-center mb-2">
                <svg class="w-5 h-5 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-granite-700">Gerir Loja</span>
        </a>

        <a href="<?= $base ?>/admin/produtos/novo/" class="flex flex-col items-center p-4 bg-granite-50 rounded-lg hover:bg-granite-100 transition-colors text-center">
            <div class="w-10 h-10 bg-accent-100 rounded-lg flex items-center justify-center mb-2">
                <svg class="w-5 h-5 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-granite-700">Novo Produto</span>
        </a>

        <a href="<?= $base ?>/admin/encomendas/" class="flex flex-col items-center p-4 bg-granite-50 rounded-lg hover:bg-granite-100 transition-colors text-center">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-granite-700">Encomendas</span>
        </a>

        <a href="<?= $base ?>/admin/faturas/" class="flex flex-col items-center p-4 bg-granite-50 rounded-lg hover:bg-granite-100 transition-colors text-center">
            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mb-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-granite-700">Faturas</span>
        </a>

        <a href="<?= $base ?>/admin/conteudos/" class="flex flex-col items-center p-4 bg-granite-50 rounded-lg hover:bg-granite-100 transition-colors text-center">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mb-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-granite-700">Conteudos</span>
        </a>

        <a href="<?= $base ?>/admin/media/" class="flex flex-col items-center p-4 bg-granite-50 rounded-lg hover:bg-granite-100 transition-colors text-center">
            <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center mb-2">
                <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-granite-700">Media</span>
        </a>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
