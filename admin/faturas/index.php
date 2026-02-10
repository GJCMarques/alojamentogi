<?php
/**
 * A Casa do Gi - Admin Invoices Management
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;
use Core\Invoice;

$db = Database::getInstance();
$invoiceSystem = Invoice::getInstance();

// Handle verification via GET
$verifyResult = null;
if (isset($_GET['verify']) && !empty($_GET['code'])) {
    $code = sanitize($_GET['code']);
    $verifyResult = $invoiceSystem->verify($code);
}

// Handle resend email
if (isset($_GET['resend']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $invoiceId = (int)$_GET['resend'];
        $sent = $invoiceSystem->sendEmail($invoiceId);
        Session::flash($sent ? 'success' : 'error', $sent ? 'Email reenviado com sucesso.' : 'Erro ao reenviar email.');
    }
    redirect(basePath() . '/admin/faturas/');
}

// Pagination and filtering
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$statusFilter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$dateFrom = isset($_GET['date_from']) ? sanitize($_GET['date_from']) : '';
$dateTo = isset($_GET['date_to']) ? sanitize($_GET['date_to']) : '';

$filters = [];
if ($search) $filters['search'] = $search;
if ($statusFilter) $filters['status'] = $statusFilter;
if ($dateFrom) $filters['date_from'] = $dateFrom;
if ($dateTo) $filters['date_to'] = $dateTo;

$invoices = $invoiceSystem->getAll($filters, $perPage, $offset);
$totalInvoices = $invoiceSystem->count($filters);
$totalPages = ceil($totalInvoices / $perPage);

// Status counts
$statusCounts = $db->fetchAll("SELECT payment_status, COUNT(*) as count FROM invoices GROUP BY payment_status");
$counts = ['all' => 0];
foreach ($statusCounts as $sc) {
    $counts[$sc['payment_status']] = $sc['count'];
    $counts['all'] += $sc['count'];
}

// Payment status labels
$paymentLabels = [
    'paid' => ['label' => 'Pago', 'class' => 'bg-green-100 text-green-800'],
    'pending' => ['label' => 'Pendente', 'class' => 'bg-yellow-100 text-yellow-800'],
    'failed' => ['label' => 'Falhado', 'class' => 'bg-red-100 text-red-800'],
    'refunded' => ['label' => 'Reembolsado', 'class' => 'bg-gray-100 text-gray-800'],
];

$pageTitle = 'Faturas';
$currentPage = 'faturas';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-primary">Gestão de Faturas</h1>
        <p class="text-charcoal-600"><?= $totalInvoices ?> fatura(s)</p>
    </div>
</div>

<!-- Invoice Verification Tool -->
<div class="bg-white rounded-lg shadow-sm mb-6 overflow-hidden">
    <div class="bg-gradient-to-r from-primary to-primary-700 px-6 py-4">
        <h2 class="text-lg font-semibold text-cream-50 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Ferramenta de Verificação de Autenticidade
        </h2>
        <p class="text-cream-100 text-sm mt-1">Verifique a autenticidade de uma fatura usando o código de barras ou UUID</p>
    </div>
    <div class="p-6">
        <form action="" method="get" class="flex items-center gap-4">
            <input type="hidden" name="verify" value="1">
            <div class="flex-1">
                <input type="text"
                       name="code"
                       value="<?= isset($_GET['code']) ? e($_GET['code']) : '' ?>"
                       placeholder="Digite o código de barras (123 456 789) ou UUID (a1b2c3d4-e5f6-4789-abcd-ef0123456789)"
                       class="w-full px-4 py-3 border-2 border-charcoal-300 rounded-lg focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500 text-lg"
                       required>
            </div>
            <button type="submit" class="px-6 py-3 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700 font-medium flex items-center gap-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Verificar
            </button>
            <?php if (isset($_GET['verify'])): ?>
            <a href="<?= basePath() ?>/admin/faturas/" class="text-sm text-charcoal-500 hover:text-charcoal-700 underline">Limpar</a>
            <?php endif; ?>
        </form>

        <?php if ($verifyResult): ?>
        <!-- Verification Result -->
        <div class="mt-6">
            <?php if ($verifyResult['valid'] && $verifyResult['invoice']): ?>
            <!-- Valid Invoice -->
            <div class="border-2 border-green-500 rounded-lg p-6 bg-green-50">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-green-800 mb-2"><?= e($verifyResult['message']) ?></h3>
                        <p class="text-green-700 text-sm mb-4">Esta fatura é autêntica e não foi adulterada. Todos os dados de integridade estão corretos.</p>

                        <?php $invoice = $verifyResult['invoice']; ?>
                        <div class="bg-white rounded-lg p-4 border border-green-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-charcoal-500 uppercase tracking-wide mb-1">Código de Barras</p>
                                    <p class="text-xl font-bold text-primary"><?= e(substr($invoice['barcode'], 0, 3) . ' ' . substr($invoice['barcode'], 3, 3) . ' ' . substr($invoice['barcode'], 6, 3)) ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-charcoal-500 uppercase tracking-wide mb-1">UUID</p>
                                    <p class="text-sm font-mono text-charcoal-700 break-all"><?= e($invoice['invoice_uuid']) ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-charcoal-500 uppercase tracking-wide mb-1">Cliente</p>
                                    <p class="font-medium text-charcoal-800"><?= e($invoice['customer_name']) ?></p>
                                    <p class="text-sm text-charcoal-600"><?= e($invoice['customer_email']) ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-charcoal-500 uppercase tracking-wide mb-1">Valor Total</p>
                                    <p class="text-2xl font-bold text-accent"><?= formatPrice($invoice['total']) ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-charcoal-500 uppercase tracking-wide mb-1">Estado de Pagamento</p>
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full <?= $paymentLabels[$invoice['payment_status']]['class'] ?? 'bg-gray-100 text-gray-800' ?>">
                                        <?= $paymentLabels[$invoice['payment_status']]['label'] ?? e($invoice['payment_status']) ?>
                                    </span>
                                </div>
                                <div>
                                    <p class="text-xs text-charcoal-500 uppercase tracking-wide mb-1">Data de Emissão</p>
                                    <p class="font-medium text-charcoal-800"><?= formatDateTime($invoice['issued_at']) ?></p>
                                </div>
                            </div>

                            <?php if (!empty($invoice['items_json'])): ?>
                            <?php $items = json_decode($invoice['items_json'], true); ?>
                            <div class="mt-4 pt-4 border-t border-charcoal-200">
                                <p class="text-xs text-charcoal-500 uppercase tracking-wide mb-2">Itens da Fatura</p>
                                <div class="space-y-2">
                                    <?php foreach ($items as $item): ?>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-charcoal-700"><?= e($item['product_name']) ?> (x<?= $item['quantity'] ?>)</span>
                                        <span class="font-medium text-charcoal-800"><?= formatPrice($item['total_price']) ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="mt-4 flex gap-2">
                                <a href="<?= basePath() ?>/admin/faturas/ver.php?id=<?= $invoice['id'] ?>"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-700 text-sm font-medium transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Ver Detalhes Completos
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php elseif (!$verifyResult['valid'] && $verifyResult['invoice']): ?>
            <!-- Invalid/Tampered Invoice -->
            <div class="border-2 border-red-500 rounded-lg p-6 bg-red-50">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-red-800 mb-2">ALERTA: Possível Adulteração Detectada</h3>
                        <p class="text-red-700 mb-4"><?= e($verifyResult['message']) ?></p>
                        <div class="bg-white rounded-lg p-4 border border-red-200">
                            <p class="text-sm text-red-900 font-medium mb-2">A hash de integridade desta fatura não corresponde aos dados armazenados. Isto pode indicar:</p>
                            <ul class="list-disc list-inside text-sm text-red-800 space-y-1 ml-2">
                                <li>Alteração nos valores da fatura</li>
                                <li>Modificação dos itens ou quantidades</li>
                                <li>Alteração de dados do cliente</li>
                                <li>Manipulação do código de barras ou UUID</li>
                            </ul>
                            <p class="text-sm text-red-900 font-medium mt-4">Recomenda-se investigação imediata e contacto com o cliente.</p>
                        </div>
                    </div>
                </div>
            </div>

            <?php else: ?>
            <!-- Not Found -->
            <div class="border-2 border-charcoal-300 rounded-lg p-6 bg-charcoal-50">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <svg class="w-12 h-12 text-charcoal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-charcoal-800 mb-2">Fatura Não Encontrada</h3>
                        <p class="text-charcoal-700"><?= e($verifyResult['message']) ?></p>
                        <p class="text-sm text-charcoal-600 mt-2">Verifique se o código foi digitado corretamente. O código pode ser um barcode de 9 dígitos ou um UUID.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm mb-6">
    <div class="p-4 border-b border-charcoal-200">
        <form action="" method="get" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-charcoal-700 mb-1">Pesquisar</label>
                    <input type="text"
                           name="search"
                           value="<?= e($search) ?>"
                           placeholder="Código de barras, UUID, nome ou email..."
                           class="w-full px-3 py-2 border border-charcoal-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1">Data Inicial</label>
                    <input type="date"
                           name="date_from"
                           value="<?= e($dateFrom) ?>"
                           class="w-full px-3 py-2 border border-charcoal-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-charcoal-700 mb-1">Data Final</label>
                    <input type="date"
                           name="date_to"
                           value="<?= e($dateTo) ?>"
                           class="w-full px-3 py-2 border border-charcoal-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700 font-medium transition-colors">
                    Aplicar Filtros
                </button>
                <?php if ($search || $statusFilter || $dateFrom || $dateTo): ?>
                <a href="<?= basePath() ?>/admin/faturas/" class="text-sm text-charcoal-500 hover:text-charcoal-700 underline">Limpar Filtros</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Status Tabs -->
    <div class="flex flex-wrap border-b border-charcoal-200">
        <a href="?<?= http_build_query(array_merge($_GET, ['status' => '', 'page' => 1])) ?>"
           class="px-4 py-3 text-sm font-medium <?= !$statusFilter ? 'text-secondary-600 border-b-2 border-secondary-600' : 'text-charcoal-500 hover:text-charcoal-700' ?>">
            Todas (<?= $counts['all'] ?? 0 ?>)
        </a>
        <?php foreach ($paymentLabels as $key => $info): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['status' => $key, 'page' => 1])) ?>"
           class="px-4 py-3 text-sm font-medium <?= $statusFilter === $key ? 'text-secondary-600 border-b-2 border-secondary-600' : 'text-charcoal-500 hover:text-charcoal-700' ?>">
            <?= $info['label'] ?> (<?= $counts[$key] ?? 0 ?>)
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Invoices Table -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <?php if (empty($invoices)): ?>
    <div class="p-12 text-center">
        <svg class="w-16 h-16 text-charcoal-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="text-lg font-medium text-charcoal-900 mb-2">Nenhuma fatura encontrada</h3>
        <p class="text-charcoal-600">
            <?php if ($search || $statusFilter || $dateFrom || $dateTo): ?>
                Não foram encontradas faturas com os filtros aplicados.
            <?php else: ?>
                As faturas são geradas automaticamente quando as encomendas são processadas.
            <?php endif; ?>
        </p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-charcoal-200 admin-table">
            <thead>
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-charcoal-600 uppercase tracking-wider">
                        Código de Barras
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-charcoal-600 uppercase tracking-wider">
                        Cliente
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-charcoal-600 uppercase tracking-wider">
                        Valor Total
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-charcoal-600 uppercase tracking-wider">
                        Pagamento
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-charcoal-600 uppercase tracking-wider">
                        Data de Emissão
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-charcoal-600 uppercase tracking-wider">
                        Ações
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-charcoal-200">
                <?php foreach ($invoices as $invoice): ?>
                <tr class="hover:bg-secondary-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <div class="text-sm font-mono font-bold text-primary">
                                <?= e(substr($invoice['barcode'], 0, 3) . ' ' . substr($invoice['barcode'], 3, 3) . ' ' . substr($invoice['barcode'], 6, 3)) ?>
                            </div>
                            <div class="text-xs text-charcoal-500 font-mono mt-1 group relative cursor-help" title="<?= e($invoice['invoice_uuid']) ?>">
                                <?= e(substr($invoice['invoice_uuid'], 0, 8)) ?>...
                                <button type="button"
                                        onclick="navigator.clipboard.writeText('<?= e($invoice['invoice_uuid']) ?>').then(() => { this.innerHTML = 'Copiado!'; setTimeout(() => this.innerHTML = 'Copiar UUID', 2000); })"
                                        class="ml-1 text-secondary-600 hover:text-secondary-700 text-xs underline">
                                    Copiar UUID
                                </button>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-charcoal-900"><?= e($invoice['customer_name']) ?></div>
                        <div class="text-sm text-charcoal-500"><?= e($invoice['customer_email']) ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-accent-700"><?= formatPrice($invoice['total']) ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full <?= $paymentLabels[$invoice['payment_status']]['class'] ?? 'bg-gray-100 text-gray-800' ?>">
                            <?= $paymentLabels[$invoice['payment_status']]['label'] ?? e($invoice['payment_status']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-charcoal-900"><?= formatDateTime($invoice['issued_at']) ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-2">
                            <a href="<?= basePath() ?>/admin/faturas/ver.php?id=<?= $invoice['id'] ?>"
                               class="text-primary hover:text-primary-700 font-medium transition-colors"
                               title="Ver detalhes">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="?resend=<?= $invoice['id'] ?>&token=<?= CSRF::getToken() ?>"
                               class="text-secondary-600 hover:text-secondary-700 font-medium transition-colors"
                               title="Reenviar email"
                               onclick="return confirm('Tem certeza que deseja reenviar o email desta fatura?')">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </a>
                            <a href="?verify=1&code=<?= e($invoice['barcode']) ?>"
                               class="text-accent-600 hover:text-accent-700 font-medium transition-colors"
                               title="Verificar autenticidade">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="bg-cream-50 px-6 py-4 border-t border-charcoal-200">
        <div class="flex items-center justify-between">
            <div class="text-sm text-charcoal-600">
                Página <?= $page ?> de <?= $totalPages ?> (<?= $totalInvoices ?> fatura(s) no total)
            </div>
            <div class="flex gap-2">
                <?php if ($page > 1): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>"
                   class="px-4 py-2 bg-white border border-charcoal-300 rounded-lg text-charcoal-700 hover:bg-charcoal-50 font-medium transition-colors">
                    Anterior
                </a>
                <?php endif; ?>

                <?php
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);

                for ($i = $startPage; $i <= $endPage; $i++):
                ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"
                   class="px-4 py-2 <?= $i === $page ? 'bg-secondary-600 text-white' : 'bg-white border border-charcoal-300 text-charcoal-700 hover:bg-charcoal-50' ?> rounded-lg font-medium transition-colors">
                    <?= $i ?>
                </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>"
                   class="px-4 py-2 bg-white border border-charcoal-300 rounded-lg text-charcoal-700 hover:bg-charcoal-50 font-medium transition-colors">
                    Próximo
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<script>
// Auto-hide flash messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const flashMessages = document.getElementById('flashMessages');
    if (flashMessages) {
        setTimeout(() => {
            flashMessages.querySelectorAll('div').forEach(msg => {
                msg.style.opacity = '0';
                setTimeout(() => msg.remove(), 300);
            });
        }, 5000);
    }
});
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
