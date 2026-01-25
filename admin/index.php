<?php
/**
 * A Casa do Gi - Admin Dashboard
 */

require_once dirname(__DIR__) . '/includes/init.php';
require_once __DIR__ . '/includes/auth-check.php';

use Core\Database;

$db = Database::getInstance();

// Get statistics
$stats = [
    'products' => $db->count('products', 'is_active = 1'),
    'orders_pending' => $db->count('orders', "status IN ('pending', 'confirmed')"),
    'orders_total' => $db->count('orders'),
    'messages_unread' => $db->count('contact_submissions', 'is_read = 0'),
    'activities' => $db->count('activities', 'is_active = 1'),
];

// Get recent orders
$recentOrders = $db->fetchAll(
    "SELECT * FROM orders ORDER BY created_at DESC LIMIT 5"
);

// Get recent messages
$recentMessages = $db->fetchAll(
    "SELECT * FROM contact_submissions ORDER BY created_at DESC LIMIT 5"
);

// Revenue this month
$monthRevenue = $db->fetchColumn(
    "SELECT COALESCE(SUM(total), 0) FROM orders
     WHERE payment_status = 'paid' AND MONTH(created_at) = MONTH(CURRENT_DATE())
     AND YEAR(created_at) = YEAR(CURRENT_DATE())"
);

$pageTitle = 'Dashboard';

include __DIR__ . '/includes/header.php';
?>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Products -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-granite-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-granite-500">Produtos Ativos</p>
                <p class="text-2xl font-bold text-granite-800 mt-1"><?= $stats['products'] ?></p>
            </div>
            <div class="w-12 h-12 bg-secondary-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
        </div>
        <a href="<?= basePath() ?>/admin/produtos/" class="inline-flex items-center text-sm text-secondary-600 hover:text-secondary-700 mt-4">
            Ver todos
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>

    <!-- Pending Orders -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-granite-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-granite-500">Encomendas Pendentes</p>
                <p class="text-2xl font-bold text-granite-800 mt-1"><?= $stats['orders_pending'] ?></p>
            </div>
            <div class="w-12 h-12 bg-terracotta-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-terracotta-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
        </div>
        <a href="<?= basePath() ?>/admin/encomendas/" class="inline-flex items-center text-sm text-secondary-600 hover:text-secondary-700 mt-4">
            Ver encomendas
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>

    <!-- Revenue -->
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
        <p class="text-sm text-granite-500 mt-4"><?= $stats['orders_total'] ?> encomendas no total</p>
    </div>

    <!-- Messages -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-granite-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-granite-500">Mensagens por Ler</p>
                <p class="text-2xl font-bold text-granite-800 mt-1"><?= $stats['messages_unread'] ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
        <a href="<?= basePath() ?>/admin/mensagens/" class="inline-flex items-center text-sm text-secondary-600 hover:text-secondary-700 mt-4">
            Ver mensagens
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</div>

<!-- Content Grid -->
<div class="grid lg:grid-cols-2 gap-6">
    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow-sm border border-granite-200">
        <div class="px-6 py-4 border-b border-granite-200 flex items-center justify-between">
            <h2 class="font-semibold text-granite-800">Encomendas Recentes</h2>
            <a href="<?= basePath() ?>/admin/encomendas/" class="text-sm text-secondary-600 hover:text-secondary-700">Ver todas</a>
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
                <a href="<?= basePath() ?>/admin/encomendas/detalhes/?id=<?= $order['id'] ?>" class="block px-6 py-4 hover:bg-granite-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-granite-800"><?= e($order['order_number']) ?></p>
                            <p class="text-sm text-granite-500"><?= e($order['customer_name']) ?></p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-granite-800"><?= formatPrice($order['total']) ?></p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                <?php
                                switch ($order['status']) {
                                    case 'pending': echo 'bg-yellow-100 text-yellow-700'; break;
                                    case 'confirmed': echo 'bg-blue-100 text-blue-700'; break;
                                    case 'processing': echo 'bg-purple-100 text-purple-700'; break;
                                    case 'shipped': echo 'bg-indigo-100 text-indigo-700'; break;
                                    case 'delivered': echo 'bg-green-100 text-green-700'; break;
                                    case 'cancelled': echo 'bg-red-100 text-red-700'; break;
                                    default: echo 'bg-granite-100 text-granite-700';
                                }
                                ?>">
                                <?= ucfirst($order['status']) ?>
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
            <a href="<?= basePath() ?>/admin/mensagens/" class="text-sm text-secondary-600 hover:text-secondary-700">Ver todas</a>
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
                <a href="<?= basePath() ?>/admin/mensagens/?id=<?= $msg['id'] ?>" class="block px-6 py-4 hover:bg-granite-50 transition-colors">
                    <div class="flex items-start space-x-3">
                        <?php if (!$msg['is_read']): ?>
                        <span class="w-2 h-2 bg-terracotta-500 rounded-full mt-2 flex-shrink-0"></span>
                        <?php else: ?>
                        <span class="w-2 h-2 flex-shrink-0"></span>
                        <?php endif; ?>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="font-medium text-granite-800 truncate"><?= e($msg['name']) ?></p>
                                <span class="text-xs text-granite-400"><?= timeAgo($msg['created_at']) ?></span>
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

<!-- Quick Actions -->
<div class="mt-8 bg-white rounded-lg shadow-sm border border-granite-200 p-6">
    <h2 class="font-semibold text-granite-800 mb-4">Acoes Rapidas</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="<?= basePath() ?>/admin/produtos/novo/" class="flex items-center p-4 bg-granite-50 rounded-lg hover:bg-granite-100 transition-colors">
            <div class="w-10 h-10 bg-secondary-100 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <span class="text-sm font-medium text-granite-700">Novo Produto</span>
        </a>

        <a href="<?= basePath() ?>/admin/atividades/nova/" class="flex items-center p-4 bg-granite-50 rounded-lg hover:bg-granite-100 transition-colors">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <span class="text-sm font-medium text-granite-700">Nova Atividade</span>
        </a>

        <a href="<?= basePath() ?>/admin/conteudos/" class="flex items-center p-4 bg-granite-50 rounded-lg hover:bg-granite-100 transition-colors">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <span class="text-sm font-medium text-granite-700">Editar Conteudos</span>
        </a>

        <a href="<?= basePath() ?>/admin/media/" class="flex items-center p-4 bg-granite-50 rounded-lg hover:bg-granite-100 transition-colors">
            <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="text-sm font-medium text-granite-700">Gerir Media</span>
        </a>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
