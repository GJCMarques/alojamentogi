<?php
/**
 * A Casa do Gi - Admin Activities List
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;

$db = Database::getInstance();

// Handle delete
if (isset($_GET['delete']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $id = (int)$_GET['delete'];
        $activity = $db->fetch("SELECT * FROM activities WHERE id = ?", [$id]);

        if ($activity) {
            // Delete image
            if ($activity['image'] && file_exists(ROOT_PATH . $activity['image'])) {
                unlink(ROOT_PATH . $activity['image']);
            }
            // Delete translations
            $db->delete('activity_translations', 'activity_id = ?', [$id]);
            // Delete activity
            $db->delete('activities', 'id = ?', [$id]);
            Session::flash('success', 'Atividade eliminada.');
        }
    }
    redirect('/admin/atividades/');
}

// Handle toggle active
if (isset($_GET['toggle']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $id = (int)$_GET['toggle'];
        $db->query("UPDATE activities SET is_active = NOT is_active WHERE id = ?", [$id]);
        Session::flash('success', 'Estado atualizado.');
    }
    redirect('/admin/atividades/');
}

// Filters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 15;
$offset = ($page - 1) * $perPage;

// Build query
$where = "WHERE 1=1";
$params = [];

if ($category) {
    $where .= " AND a.category = ?";
    $params[] = $category;
}

if ($search) {
    $where .= " AND (at.title LIKE ? OR at.short_description LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

// Get total
$total = $db->fetch(
    "SELECT COUNT(DISTINCT a.id) as c
     FROM activities a
     LEFT JOIN activity_translations at ON a.id = at.activity_id AND at.language_id = 1
     {$where}",
    $params
)['c'];
$totalPages = ceil($total / $perPage);

// Get activities
$activities = $db->fetchAll(
    "SELECT a.*, at.title as name, at.short_description
     FROM activities a
     LEFT JOIN activity_translations at ON a.id = at.activity_id AND at.language_id = 1
     {$where}
     ORDER BY a.is_featured DESC, a.sort_order, a.id DESC
     LIMIT {$perPage} OFFSET {$offset}",
    $params
);

// Categories
$categories = [
    'natureza' => 'Natureza',
    'cultura' => 'Cultura',
    'gastronomia' => 'Gastronomia',
    'aventura' => 'Aventura',
];

$pageTitle = 'Atividades';
$currentPage = 'atividades';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Atividades</h1>
        <p class="text-gray-600"><?= $total ?> atividade(s)</p>
    </div>
    <a href="<?= basePath() ?>/admin/atividades/criar.php" class="px-4 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Nova Atividade
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm mb-6 p-4">
    <form action="" method="get" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Pesquisar</label>
            <input type="text" name="search" value="<?= e($search) ?>"
                   placeholder="Nome, descricao..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
            <select name="category" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                <option value="">Todas</option>
                <?php foreach ($categories as $key => $label): ?>
                <option value="<?= $key ?>" <?= $category === $key ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
            Filtrar
        </button>
        <?php if ($search || $category): ?>
        <a href="<?= basePath() ?>/admin/atividades/" class="px-4 py-2 text-gray-500 hover:text-gray-700">Limpar</a>
        <?php endif; ?>
    </form>
</div>

<!-- Activities Table -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <?php if (empty($activities)): ?>
    <div class="p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <h3 class="text-lg font-medium text-gray-700 mb-2">Sem atividades</h3>
        <p class="text-gray-500 mb-4">Comece por adicionar atividades turisticas.</p>
        <a href="<?= basePath() ?>/admin/atividades/criar.php" class="inline-flex items-center px-4 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700">
            Nova Atividade
        </a>
    </div>
    <?php else: ?>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Atividade</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoria</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Destaque</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acoes</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php foreach ($activities as $activity): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        <?php if ($activity['image']): ?>
                        <img src="<?= basePath() . e($activity['image']) ?>" alt=""
                             class="w-12 h-12 rounded-lg object-cover mr-4">
                        <?php else: ?>
                        <div class="w-12 h-12 rounded-lg bg-gray-100 mr-4 flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <?php endif; ?>
                        <div>
                            <div class="text-sm font-medium text-gray-900"><?= e($activity['name'] ?? 'Sem nome') ?></div>
                            <?php if ($activity['short_description']): ?>
                            <div class="text-xs text-gray-500 truncate max-w-xs"><?= e($activity['short_description']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-800">
                        <?= $categories[$activity['category']] ?? $activity['category'] ?>
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <?php if ($activity['is_featured']): ?>
                    <span class="text-yellow-500">&#9733;</span>
                    <?php else: ?>
                    <span class="text-gray-300">&#9734;</span>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4 text-center">
                    <a href="?toggle=<?= $activity['id'] ?>&token=<?= CSRF::getToken() ?>"
                       class="inline-flex px-2 py-1 text-xs font-medium rounded <?= $activity['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' ?>">
                        <?= $activity['is_active'] ? 'Ativo' : 'Inativo' ?>
                    </a>
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="<?= basePath() ?>/admin/atividades/editar.php?id=<?= $activity['id'] ?>"
                       class="text-secondary-600 hover:text-olive-800 text-sm mr-3">Editar</a>
                    <a href="?delete=<?= $activity['id'] ?>&token=<?= CSRF::getToken() ?>"
                       onclick="return confirm('Eliminar esta atividade?')"
                       class="text-red-600 hover:text-red-800 text-sm">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
        <div class="text-sm text-gray-500">
            Pagina <?= $page ?> de <?= $totalPages ?>
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

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
