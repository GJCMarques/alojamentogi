<?php
/**
 * A Casa do Gi - Admin Products List
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;

$db = Database::getInstance();

// Handle delete
if (isset($_GET['delete']) && isset($_GET['token'])) {
    if (\Core\CSRF::validate($_GET['token'])) {
        $productId = (int)$_GET['delete'];
        $db->delete('products', 'id = ?', [$productId]);
        Session::flash('success', 'Produto eliminado com sucesso.');
    }
    redirect('/admin/produtos/');
}

// Handle toggle active
if (isset($_GET['toggle']) && isset($_GET['token'])) {
    if (\Core\CSRF::validate($_GET['token'])) {
        $productId = (int)$_GET['toggle'];
        $product = $db->fetch("SELECT is_active FROM products WHERE id = ?", [$productId]);
        if ($product) {
            $newStatus = $product['is_active'] ? 0 : 1;
            $db->update('products', ['is_active' => $newStatus], 'id = ?', [$productId]);
            Session::flash('success', 'Estado do produto atualizado.');
        }
    }
    redirect('/admin/produtos/');
}

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Build query
$where = "WHERE 1=1";
$params = [1]; // Portuguese language

if ($search) {
    $where .= " AND (pt.name LIKE ? OR p.sku LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if ($categoryFilter) {
    $where .= " AND p.category_id = ?";
    $params[] = $categoryFilter;
}

// Get total count
$countSql = "SELECT COUNT(DISTINCT p.id) as total
             FROM products p
             LEFT JOIN product_translations pt ON p.id = pt.product_id AND pt.language_id = ?
             {$where}";
$total = $db->fetch($countSql, $params)['total'];
$totalPages = ceil($total / $perPage);

// Get products
$sql = "SELECT p.*, pt.name as name,
               pc.slug as category_slug,
               pct.name as category_name,
               (SELECT m.file_path FROM product_images pi JOIN media m ON pi.media_id = m.id WHERE pi.product_id = p.id AND pi.is_primary = 1 LIMIT 1) as image
        FROM products p
        LEFT JOIN product_translations pt ON p.id = pt.product_id AND pt.language_id = ?
        LEFT JOIN product_categories pc ON p.category_id = pc.id
        LEFT JOIN product_category_translations pct ON pc.id = pct.category_id AND pct.language_id = ?
        {$where}
        ORDER BY p.created_at DESC
        LIMIT {$perPage} OFFSET {$offset}";

$params[] = 1; // Add Portuguese language for category translation
$products = $db->fetchAll($sql, $params);

// Get categories for filter
$categories = $db->fetchAll(
    "SELECT c.id, ct.name
     FROM product_categories c
     LEFT JOIN product_category_translations ct ON c.id = ct.category_id AND ct.language_id = 1
     WHERE c.is_active = 1
     ORDER BY ct.name"
);

$pageTitle = 'Produtos';
$currentPage = 'produtos';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-primary">Produtos</h1>
        <p class="text-gray-600"><?= $total ?> produto(s) encontrado(s)</p>
    </div>
    <a href="./novo/" class="inline-flex items-center px-4 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Novo Produto
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm p-4 mb-6">
    <form action="" method="get" class="flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-[200px]">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pesquisar</label>
            <input type="text"
                   name="search"
                   id="search"
                   value="<?= e($search) ?>"
                   placeholder="Nome ou SKU..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
        </div>
        <div class="w-48">
            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
            <select name="category" id="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                <option value="0">Todas</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $categoryFilter == $cat['id'] ? 'selected' : '' ?>>
                    <?= e($cat['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                Filtrar
            </button>
            <?php if ($search || $categoryFilter): ?>
            <a href="<?= basePath() ?>/admin/produtos/" class="ml-2 text-sm text-gray-500 hover:text-gray-700">Limpar</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Products Table -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <?php if (empty($products)): ?>
    <div class="p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
        </svg>
        <h3 class="text-lg font-medium text-gray-700 mb-2">Nenhum produto encontrado</h3>
        <p class="text-gray-500 mb-4">Comece por criar o seu primeiro produto.</p>
        <a href="./novo/" class="inline-flex items-center px-4 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Criar Produto
        </a>
    </div>
    <?php else: ?>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($products as $product): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="h-12 w-12 flex-shrink-0 bg-gray-100 rounded overflow-hidden">
                            <?php if ($product['image']): ?>
                            <img src="<?= e(basePath() . $product['image']) ?>"
                                 alt="<?= e($product['name']) ?>"
                                 class="h-12 w-12 object-cover">
                            <?php else: ?>
                            <div class="h-12 w-12 flex items-center justify-center text-gray-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                                <?= e($product['name'] ?? 'Sem nome') ?>
                            </div>
                            <div class="text-sm text-gray-500">
                                SKU: <?= e($product['sku']) ?>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm text-gray-600"><?= e($product['category_name'] ?? '-') ?></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900"><?= formatPrice($product['price']) ?></div>
                    <?php if ($product['sale_price']): ?>
                    <div class="text-sm text-green-600"><?= formatPrice($product['sale_price']) ?></div>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <?php if ($product['track_inventory']): ?>
                        <?php if ($product['stock_quantity'] <= 0): ?>
                        <span class="inline-flex px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">
                            Esgotado
                        </span>
                        <?php elseif ($product['stock_quantity'] <= 5): ?>
                        <span class="inline-flex px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded">
                            <?= $product['stock_quantity'] ?> unid.
                        </span>
                        <?php else: ?>
                        <span class="text-sm text-gray-600"><?= $product['stock_quantity'] ?> unid.</span>
                        <?php endif; ?>
                    <?php else: ?>
                    <span class="text-sm text-gray-400">-</span>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <?php if ($product['is_active']): ?>
                    <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">
                        Ativo
                    </span>
                    <?php else: ?>
                    <span class="inline-flex px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">
                        Inativo
                    </span>
                    <?php endif; ?>
                    <?php if ($product['is_featured']): ?>
                    <span class="inline-flex px-2 py-1 text-xs font-medium bg-secondary-100 text-olive-800 rounded ml-1">
                        Destaque
                    </span>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="./editar/?id=<?= $product['id'] ?>"
                       class="text-secondary-600 hover:text-olive-900 mr-3">
                        Editar
                    </a>
                    <a href="<?= basePath() ?>/admin/produtos/?toggle=<?= $product['id'] ?>&token=<?= \Core\CSRF::getToken() ?>"
                       class="text-gray-600 hover:text-gray-900 mr-3"
                       title="<?= $product['is_active'] ? 'Desativar' : 'Ativar' ?>">
                        <?= $product['is_active'] ? 'Desativar' : 'Ativar' ?>
                    </a>
                    <button onclick="openDeleteModal(<?= $product['id'] ?>, '<?= e($product['name']) ?>')"
                            class="text-red-600 hover:text-red-900">
                        Eliminar
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

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
            <h3 class="text-xl font-bold text-gray-800 mb-2">Eliminar Produto</h3>
            <p class="text-gray-600 mb-6">
                Tem a certeza que deseja eliminar "<span id="deleteItemName" class="font-semibold text-gray-900"></span>"?
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
function openDeleteModal(id, name) {
    const modal = document.getElementById('deleteModal');
    const nameSpan = document.getElementById('deleteItemName');
    const confirmBtn = document.getElementById('confirmDelete');

    nameSpan.textContent = name;
    confirmBtn.href = '<?= basePath() ?>/admin/produtos/?delete=' + id + '&token=<?= \Core\CSRF::getToken() ?>';
    modal.classList.remove('hidden');
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('hidden');
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('deleteModal').classList.contains('hidden')) {
        closeDeleteModal();
    }
});
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
