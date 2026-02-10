<?php
/**
 * A Casa do Gi - Admin Invoice Detail
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;
use Core\Invoice;

$db = Database::getInstance();
$invoiceSystem = Invoice::getInstance();

$invoiceId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$invoiceId) {
    redirect(basePath() . '/admin/faturas/');
}

// Get invoice
$invoice = $db->fetch("SELECT * FROM invoices WHERE id = ?", [$invoiceId]);

if (!$invoice) {
    Session::flash('error', 'Fatura não encontrada.');
    redirect(basePath() . '/admin/faturas/');
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && CSRF::validate($_POST['csrf_token'] ?? '')) {
    $action = $_POST['action'] ?? '';

    if ($action === 'mark_paid') {
        $invoiceSystem->markAsPaid($invoiceId);
        $db->update('orders', ['payment_status' => 'paid', 'paid_at' => date('Y-m-d H:i:s')], 'id = ?', [$invoice['order_id']]);
        Session::flash('success', 'Fatura marcada como paga.');
        redirect(basePath() . '/admin/faturas/ver.php?id=' . $invoiceId);
    }

    if ($action === 'mark_refunded') {
        $db->update('invoices', ['payment_status' => 'refunded'], 'id = ?', [$invoiceId]);
        $db->update('orders', ['payment_status' => 'refunded'], 'id = ?', [$invoice['order_id']]);
        Session::flash('success', 'Fatura marcada como reembolsada.');
        redirect(basePath() . '/admin/faturas/ver.php?id=' . $invoiceId);
    }

    if ($action === 'resend_email') {
        $sent = $invoiceSystem->sendEmail($invoiceId);
        Session::flash($sent ? 'success' : 'error', $sent ? 'Email reenviado com sucesso.' : 'Erro ao reenviar email.');
        redirect(basePath() . '/admin/faturas/ver.php?id=' . $invoiceId);
    }
}

// Refresh invoice after possible changes
$invoice = $db->fetch("SELECT * FROM invoices WHERE id = ?", [$invoiceId]);

// Get related order
$order = $db->fetch("SELECT * FROM orders WHERE id = ?", [$invoice['order_id']]);

// Decode items
$items = json_decode($invoice['items_json'], true) ?: [];

// Verify integrity
$verifyResult = $invoiceSystem->verify($invoice['barcode']);

// Format barcode
$barcodeFormatted = substr($invoice['barcode'], 0, 3) . ' ' . substr($invoice['barcode'], 3, 3) . ' ' . substr($invoice['barcode'], 6, 3);

// Payment status labels
$paymentLabels = [
    'paid' => ['label' => 'Pago', 'class' => 'bg-green-100 text-green-800', 'icon' => 'check-circle'],
    'pending' => ['label' => 'Pendente', 'class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'clock'],
    'failed' => ['label' => 'Falhado', 'class' => 'bg-red-100 text-red-800', 'icon' => 'x-circle'],
    'refunded' => ['label' => 'Reembolsado', 'class' => 'bg-gray-100 text-gray-800', 'icon' => 'reply'],
];

$currentStatus = $paymentLabels[$invoice['payment_status']] ?? ['label' => $invoice['payment_status'], 'class' => 'bg-gray-100 text-gray-800'];

$pageTitle = 'Fatura #' . $barcodeFormatted;
$currentPage = 'faturas';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="mb-6">
    <a href="<?= basePath() ?>/admin/faturas/" class="text-secondary-600 hover:text-secondary-700 text-sm flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Voltar às Faturas
    </a>
</div>

<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-charcoal-800">Fatura <?= e($barcodeFormatted) ?></h1>
        <p class="text-charcoal-500 text-sm font-mono mt-1"><?= e($invoice['invoice_uuid']) ?></p>
    </div>
    <div class="flex items-center gap-3">
        <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-full <?= $currentStatus['class'] ?>">
            <?= $currentStatus['label'] ?>
        </span>

        <!-- Integrity Badge -->
        <?php if ($verifyResult['valid']): ?>
        <span class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium rounded-full bg-green-100 text-green-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Íntegra
        </span>
        <?php else: ?>
        <span class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium rounded-full bg-red-100 text-red-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Adulterada
        </span>
        <?php endif; ?>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Invoice Identifiers -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-primary to-primary-700 px-6 py-4">
                <h2 class="text-lg font-semibold text-cream-50">Identificadores da Fatura</h2>
            </div>
            <div class="p-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs text-charcoal-500 uppercase tracking-wide mb-1">Código de Barras</p>
                        <p class="text-3xl font-bold text-primary tracking-wider"><?= e($barcodeFormatted) ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-charcoal-500 uppercase tracking-wide mb-1">UUID</p>
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-mono text-charcoal-700 break-all"><?= e($invoice['invoice_uuid']) ?></p>
                            <button type="button"
                                    onclick="navigator.clipboard.writeText('<?= e($invoice['invoice_uuid']) ?>').then(() => { this.textContent = 'Copiado!'; setTimeout(() => this.textContent = 'Copiar', 2000); })"
                                    class="flex-shrink-0 text-xs px-2 py-1 bg-charcoal-100 text-charcoal-600 rounded hover:bg-charcoal-200 transition-colors">
                                Copiar
                            </button>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-charcoal-500 uppercase tracking-wide mb-1">Encomenda Associada</p>
                        <?php if ($order): ?>
                        <a href="<?= basePath() ?>/admin/encomendas/ver.php?id=<?= $order['id'] ?>" class="text-secondary-600 hover:text-secondary-700 font-medium">
                            #<?= e($order['order_number']) ?>
                        </a>
                        <?php else: ?>
                        <p class="text-charcoal-500">N/A</p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <p class="text-xs text-charcoal-500 uppercase tracking-wide mb-1">Data de Emissão</p>
                        <p class="font-medium text-charcoal-800"><?= formatDateTime($invoice['issued_at']) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-charcoal-200">
                <h2 class="text-lg font-medium text-charcoal-800">Itens da Fatura</h2>
            </div>
            <table class="min-w-full divide-y divide-charcoal-200">
                <thead class="bg-charcoal-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-charcoal-600 uppercase">Produto</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-charcoal-600 uppercase">Qtd</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-charcoal-600 uppercase">Preço Unit.</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-charcoal-600 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-charcoal-200">
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-charcoal-900"><?= e($item['product_name']) ?></div>
                            <?php if (!empty($item['product_sku'])): ?>
                            <div class="text-xs text-charcoal-500">SKU: <?= e($item['product_sku']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-charcoal-600"><?= (int)$item['quantity'] ?></td>
                        <td class="px-6 py-4 text-right text-sm text-charcoal-600"><?= formatPrice($item['unit_price']) ?></td>
                        <td class="px-6 py-4 text-right text-sm font-medium text-charcoal-900"><?= formatPrice($item['total_price']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-charcoal-50">
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right text-sm text-charcoal-600">Subtotal</td>
                        <td class="px-6 py-3 text-right text-sm font-medium text-charcoal-900"><?= formatPrice($invoice['subtotal']) ?></td>
                    </tr>
                    <?php if ($invoice['shipping_fee'] > 0): ?>
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right text-sm text-charcoal-600">Portes de Envio</td>
                        <td class="px-6 py-3 text-right text-sm text-charcoal-900"><?= formatPrice($invoice['shipping_fee']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($invoice['discount_amount'] > 0): ?>
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right text-sm text-charcoal-600">Desconto</td>
                        <td class="px-6 py-3 text-right text-sm text-green-600">-<?= formatPrice($invoice['discount_amount']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr class="border-t-2 border-charcoal-300">
                        <td colspan="3" class="px-6 py-4 text-right text-base font-bold text-charcoal-800">Total</td>
                        <td class="px-6 py-4 text-right text-xl font-bold text-accent-700"><?= formatPrice($invoice['total']) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Customer Info -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-medium text-charcoal-800 mb-4">Dados do Cliente</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-xs text-charcoal-500 uppercase tracking-wide mb-2">Contacto</h3>
                    <p class="text-charcoal-900 font-medium"><?= e($invoice['customer_name']) ?></p>
                    <p class="text-charcoal-600"><?= e($invoice['customer_email']) ?></p>
                    <?php if ($invoice['customer_phone']): ?>
                    <p class="text-charcoal-600"><?= e($invoice['customer_phone']) ?></p>
                    <?php endif; ?>
                    <?php if ($invoice['customer_nif']): ?>
                    <p class="text-charcoal-500 text-sm mt-1">NIF: <?= e($invoice['customer_nif']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <h3 class="text-xs text-charcoal-500 uppercase tracking-wide mb-2">Morada de Faturação</h3>
                    <?php if ($invoice['billing_address']): ?>
                    <p class="text-charcoal-600"><?= nl2br(e($invoice['billing_address'])) ?></p>
                    <?php if ($invoice['billing_postal_code'] || $invoice['billing_city']): ?>
                    <p class="text-charcoal-600"><?= e($invoice['billing_postal_code']) ?> <?= e($invoice['billing_city']) ?></p>
                    <?php endif; ?>
                    <?php else: ?>
                    <p class="text-charcoal-400 italic">Não disponível</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <?php if ($invoice['notes']): ?>
        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
            <h3 class="text-sm font-medium text-yellow-800 mb-2">Notas</h3>
            <p class="text-sm text-yellow-700"><?= nl2br(e($invoice['notes'])) ?></p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">

        <!-- Payment Info -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-medium text-charcoal-800 mb-4">Pagamento</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-charcoal-600 text-sm">Estado</span>
                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full <?= $currentStatus['class'] ?>">
                        <?= $currentStatus['label'] ?>
                    </span>
                </div>
                <?php if ($invoice['payment_method']): ?>
                <div class="flex justify-between">
                    <span class="text-charcoal-600 text-sm">Método</span>
                    <span class="font-medium text-sm"><?= strtoupper(e($invoice['payment_method'])) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($invoice['paid_at']): ?>
                <div class="flex justify-between">
                    <span class="text-charcoal-600 text-sm">Pago em</span>
                    <span class="text-sm"><?= formatDateTime($invoice['paid_at']) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($invoice['emailed_at']): ?>
                <div class="flex justify-between">
                    <span class="text-charcoal-600 text-sm">Email enviado</span>
                    <span class="text-sm"><?= formatDateTime($invoice['emailed_at']) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-medium text-charcoal-800 mb-4">Ações</h2>
            <div class="space-y-3">
                <!-- Resend Email -->
                <form action="" method="post">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
                    <input type="hidden" name="action" value="resend_email">
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700 font-medium transition-colors"
                            onclick="return confirm('Reenviar email da fatura para <?= e($invoice['customer_email']) ?>?')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Reenviar Email
                    </button>
                </form>

                <?php if ($invoice['payment_status'] === 'pending'): ?>
                <!-- Mark as Paid -->
                <form action="" method="post">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
                    <input type="hidden" name="action" value="mark_paid">
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition-colors"
                            onclick="return confirm('Marcar esta fatura como paga?')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Marcar como Paga
                    </button>
                </form>
                <?php endif; ?>

                <?php if ($invoice['payment_status'] === 'paid'): ?>
                <!-- Mark as Refunded -->
                <form action="" method="post">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
                    <input type="hidden" name="action" value="mark_refunded">
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-charcoal-600 text-white rounded-lg hover:bg-charcoal-700 font-medium transition-colors"
                            onclick="return confirm('Marcar esta fatura como reembolsada? Esta ação não pode ser desfeita.')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        Marcar como Reembolsada
                    </button>
                </form>
                <?php endif; ?>

                <!-- View Order -->
                <?php if ($order): ?>
                <a href="<?= basePath() ?>/admin/encomendas/ver.php?id=<?= $order['id'] ?>"
                   class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-700 font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    Ver Encomenda
                </a>
                <?php endif; ?>

                <!-- Verify -->
                <a href="<?= basePath() ?>/admin/faturas/?verify=1&code=<?= e($invoice['barcode']) ?>"
                   class="w-full flex items-center justify-center gap-2 px-4 py-2 border-2 border-accent-500 text-accent-700 rounded-lg hover:bg-accent-50 font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Verificar Autenticidade
                </a>
            </div>
        </div>

        <!-- Integrity Info -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-medium text-charcoal-800 mb-4">Integridade</h2>
            <?php if ($verifyResult['valid']): ?>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium text-green-800">Verificada</span>
                </div>
                <p class="text-sm text-green-700">A hash de integridade HMAC-SHA256 corresponde aos dados. Esta fatura não foi adulterada.</p>
            </div>
            <?php else: ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span class="font-medium text-red-800">Alerta</span>
                </div>
                <p class="text-sm text-red-700"><?= e($verifyResult['message']) ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($invoice['integrity_hash'])): ?>
            <div class="mt-4">
                <p class="text-xs text-charcoal-500 uppercase tracking-wide mb-1">Hash SHA-256</p>
                <p class="text-xs font-mono text-charcoal-600 break-all bg-charcoal-50 p-2 rounded"><?= e($invoice['integrity_hash']) ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
