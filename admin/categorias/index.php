<?php

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;

$db = Database::getInstance();

$currentType = $_GET['type'] ?? 'activity';
if (!in_array($currentType, ['activity', 'product'])) {
    $currentType = 'activity';
}

$languages = $db->fetchAll("SELECT * FROM languages WHERE is_active = 1 ORDER BY is_default DESC");

$availableIcons = [
    'tree' => 'Árvore (Natureza)',
    'building' => 'Edifício',
    'utensils' => 'Talheres (Gastronomia)',
    'coffee' => 'Café',
    'mountain' => 'Montanha (Aventura)',
    'calendar' => 'Calendário (Eventos)',
    'spa' => 'Spa (Bem-estar)',
    'tractor' => 'Trator (Turismo Rural)',
    'gamepad' => 'Gamepad (Lazer)',
    'landmark' => 'Monumento',
    'church' => 'Igreja',
    'water' => 'Água',
    'hiking' => 'Caminhada',
    'wine-glass' => 'Copo de Vinho',
    'palette' => 'Paleta (Arte)',
    'music' => 'Música',
    'camera' => 'Câmara',
    'shopping-bag' => 'Saco de Compras',
    'gift' => 'Presente',
    'heart' => 'Coração'
];

if (isset($_GET['delete']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $categoryId = (int)$_GET['delete'];

        $category = $db->fetch("SELECT type FROM categories WHERE id = ?", [$categoryId]);

        if ($category) {

            if ($category['type'] === 'activity') {
                $hasItems = $db->fetch("SELECT COUNT(*) as count FROM activities WHERE category_id = ?", [$categoryId])['count'] > 0;
                $itemType = 'atividades';
            } else {
                $hasItems = $db->fetch("SELECT COUNT(*) as count FROM products WHERE category_id = ?", [$categoryId])['count'] > 0;
                $itemType = 'produtos';
            }

            if ($hasItems) {
                Session::flash('error', "Não é possível eliminar uma categoria com {$itemType}.");
            } else {
                $db->delete('category_translations', 'category_id = ?', [$categoryId]);
                $db->delete('categories', 'id = ?', [$categoryId]);
                Session::flash('success', 'Categoria eliminada com sucesso.');
            }
        }
    }
    redirect('/admin/categorias/?type=' . $currentType);
}

$errors = [];
$editCategory = null;
$editTranslations = [];

if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $editCategory = $db->fetch("SELECT * FROM categories WHERE id = ?", [$editId]);

    if ($editCategory) {
        $editTrans = $db->fetchAll("SELECT * FROM category_translations WHERE category_id = ?", [$editId]);
        foreach ($editTrans as $trans) {
            $editTranslations[$trans['language_id']] = $trans;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token de segurança inválido.';
    } else {
        $categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
        $type = sanitize($_POST['type'] ?? 'activity');
        $slug = sanitize($_POST['slug'] ?? '');
        $icon = $type === 'activity' ? sanitize($_POST['icon'] ?? '') : null;
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $translations = $_POST['translations'] ?? [];

        $hasName = false;
        foreach ($translations as $trans) {
            if (!empty($trans['name'])) {
                $hasName = true;
                break;
            }
        }

        if (!$hasName) {
            $errors[] = 'É necessário definir um nome para pelo menos uma língua.';
        }

        if (empty($slug)) {
            $name = $translations[1]['name'] ?? $translations[2]['name'] ?? 'categoria';
            $slug = createSlug($name);
        }

        $existingSlug = $db->fetch(
            "SELECT id FROM categories WHERE slug = ? AND type = ? AND id != ?",
            [$slug, $type, $categoryId]
        );
        if ($existingSlug) {
            $slug = $slug . '-' . time();
        }

        if (empty($errors)) {
            $db->beginTransaction();

            try {
                if ($categoryId) {

                    $updateData = [
                        'slug' => $slug,
                        'sort_order' => $sortOrder,
                        'is_active' => $isActive
                    ];

                    if ($type === 'activity') {
                        $updateData['icon'] = $icon;
                    }

                    $db->update('categories', $updateData, 'id = ?', [$categoryId]);

                    foreach ($translations as $langId => $trans) {
                        $existing = $db->fetch(
                            "SELECT id FROM category_translations WHERE category_id = ? AND language_id = ?",
                            [$categoryId, $langId]
                        );

                        if ($existing) {
                            $db->update('category_translations', [
                                'name' => sanitize($trans['name'] ?? ''),
                                'description' => sanitize($trans['description'] ?? '')
                            ], 'id = ?', [$existing['id']]);
                        } elseif (!empty($trans['name'])) {
                            $db->insert('category_translations', [
                                'category_id' => $categoryId,
                                'language_id' => (int)$langId,
                                'name' => sanitize($trans['name']),
                                'description' => sanitize($trans['description'] ?? '')
                            ]);
                        }
                    }

                    Session::flash('success', 'Categoria atualizada com sucesso.');
                } else {

                    $insertData = [
                        'type' => $type,
                        'slug' => $slug,
                        'sort_order' => $sortOrder,
                        'is_active' => $isActive
                    ];

                    if ($type === 'activity') {
                        $insertData['icon'] = $icon;
                    }

                    $categoryId = $db->insert('categories', $insertData);

                    foreach ($translations as $langId => $trans) {
                        if (!empty($trans['name'])) {
                            $db->insert('category_translations', [
                                'category_id' => $categoryId,
                                'language_id' => (int)$langId,
                                'name' => sanitize($trans['name']),
                                'description' => sanitize($trans['description'] ?? '')
                            ]);
                        }
                    }

                    Session::flash('success', 'Categoria criada com sucesso.');
                }

                $db->commit();
                redirect('/admin/categorias/?type=' . $type);

            } catch (\Exception $e) {
                $db->rollback();
                $errors[] = 'Erro ao guardar: ' . $e->getMessage();
            }
        }
    }
}

$categories = $db->fetchAll(
    "SELECT c.*, ct.name,
            (SELECT COUNT(*) FROM " . ($currentType === 'activity' ? 'activities' : 'products') . " WHERE category_id = c.id) as item_count
     FROM categories c
     LEFT JOIN category_translations ct ON c.id = ct.category_id AND ct.language_id = 1
     WHERE c.type = ?
     ORDER BY c.sort_order ASC, c.id ASC",
    [$currentType]
);

$pageTitle = $currentType === 'activity' ? 'Categorias de Atividades' : 'Categorias de Produtos';
$currentPage = 'categorias';
include dirname(__DIR__) . '/includes/header.php';
?>

<!-- Type Switcher -->
<div class="mb-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-primary">
            <?= $currentType === 'activity' ? 'Categorias de Atividades' : 'Categorias de Produtos' ?>
        </h1>

        <!-- Type Toggle -->
        <div class="inline-flex rounded-lg border border-gray-200 bg-white p-1">
            <a href="?type=activity"
               class="px-4 py-2 text-sm font-medium rounded-md transition-colors <?= $currentType === 'activity' ? 'bg-secondary-600 text-white' : 'text-gray-700 hover:bg-gray-50' ?>">
                Atividades
            </a>
            <a href="?type=product"
               class="px-4 py-2 text-sm font-medium rounded-md transition-colors <?= $currentType === 'product' ? 'bg-secondary-600 text-white' : 'text-gray-700 hover:bg-gray-50' ?>">
                Produtos
            </a>
        </div>
    </div>
    <p class="text-gray-600 mt-2">
        Gerir categorias para <?= $currentType === 'activity' ? 'a página de atividades turísticas' : 'a loja de produtos' ?>
    </p>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <!-- Category Form -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-medium text-gray-800 mb-4">
                <?= $editCategory ? 'Editar Categoria' : 'Nova Categoria' ?>
            </h2>

            <?php if (!empty($errors)): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded mb-4 text-sm">
                <?php foreach ($errors as $error): ?>
                <p><?= e($error) ?></p>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <form action="" method="post">
                <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
                <input type="hidden" name="type" value="<?= $currentType ?>">
                <?php if ($editCategory): ?>
                <input type="hidden" name="category_id" value="<?= $editCategory['id'] ?>">
                <?php endif; ?>

                <?php foreach ($languages as $lang): ?>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nome (<?= e($lang['name']) ?>) <?= $lang['is_default'] ? '*' : '' ?>
                    </label>
                    <input type="text"
                           name="translations[<?= $lang['id'] ?>][name]"
                           id="name_<?= $lang['id'] ?>"
                           value="<?= e($editTranslations[$lang['id']]['name'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500"
                           <?= $lang['is_default'] && $lang['id'] == 1 ? 'oninput="generateSlug(this.value)"' : '' ?>
                           <?= $lang['is_default'] ? 'required' : '' ?>>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Descrição (<?= e($lang['name']) ?>)
                    </label>
                    <textarea name="translations[<?= $lang['id'] ?>][description]"
                              rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500"><?= e($editTranslations[$lang['id']]['description'] ?? '') ?></textarea>
                </div>
                <?php endforeach; ?>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug (URL)</label>
                    <input type="text"
                           name="slug"
                           id="slug_input"
                           value="<?= e($editCategory['slug'] ?? '') ?>"
                           placeholder="auto-gerado"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                    <p class="text-xs text-gray-500 mt-1">Deixe vazio para gerar automaticamente</p>
                </div>

                <?php if ($currentType === 'activity'): ?>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ícone</label>
                    <select name="icon" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        <option value="">Sem ícone</option>
                        <?php foreach ($availableIcons as $iconValue => $iconLabel): ?>
                        <option value="<?= e($iconValue) ?>" <?= ($editCategory['icon'] ?? '') === $iconValue ? 'selected' : '' ?>>
                            <?= e($iconLabel) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ordem</label>
                    <input type="number"
                           name="sort_order"
                           value="<?= e($editCategory['sort_order'] ?? 0) ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                </div>

                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               <?= ($editCategory['is_active'] ?? 1) ? 'checked' : '' ?>
                               class="w-4 h-4 text-secondary-600 border-gray-300 rounded focus:ring-secondary-500">
                        <span class="ml-2 text-gray-700">Ativa</span>
                    </label>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700 transition-colors">
                        <?= $editCategory ? 'Guardar' : 'Criar' ?>
                    </button>
                    <?php if ($editCategory): ?>
                    <a href="<?= basePath() ?>/admin/categorias/?type=<?= $currentType ?>"
                       class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 flex items-center justify-center">
                        Cancelar
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Categories List -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <?php if (empty($categories)): ?>
            <div class="p-8 text-center text-gray-500">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <p class="font-medium">Nenhuma categoria criada ainda</p>
                <p class="text-sm mt-1">Use o formulário ao lado para criar a primeira categoria</p>
            </div>
            <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoria</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                        <?php if ($currentType === 'activity'): ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ícone</th>
                        <?php endif; ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"><?= $currentType === 'activity' ? 'Atividades' : 'Produtos' ?></th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($categories as $cat): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900"><?= e($cat['name'] ?? 'Sem nome') ?></div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?= e($cat['slug']) ?>
                        </td>
                        <?php if ($currentType === 'activity'): ?>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?= $cat['icon'] ? e($availableIcons[$cat['icon']] ?? $cat['icon']) : '-' ?>
                        </td>
                        <?php endif; ?>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <span class="<?= $cat['item_count'] > 0 ? 'font-semibold text-gray-900' : '' ?>">
                                <?= $cat['item_count'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($cat['is_active']): ?>
                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Ativa</span>
                            <?php else: ?>
                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Inativa</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="?type=<?= $currentType ?>&edit=<?= $cat['id'] ?>"
                               class="text-secondary-600 hover:text-secondary-900 mr-3 font-medium">
                                Editar
                            </a>
                            <?php if ($cat['item_count'] == 0): ?>
                            <button type="button"
                                    class="delete-category-btn text-red-600 hover:text-red-900 font-medium"
                                    data-id="<?= $cat['id'] ?>"
                                    data-name="<?= e($cat['name'] ?? 'Categoria') ?>">
                                Eliminar
                            </button>
                            <?php else: ?>
                            <span class="text-gray-400 cursor-not-allowed" title="Não pode eliminar - tem <?= $currentType === 'activity' ? 'atividades' : 'produtos' ?> associados">
                                Eliminar
                            </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Info Box -->
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm text-blue-800">
            <p class="font-semibold mb-1">Sobre as Categorias</p>
            <ul class="list-disc list-inside space-y-1 text-blue-700">
                <?php if ($currentType === 'activity'): ?>
                <li>As categorias de atividades são usadas para filtrar e organizar as experiências turísticas</li>
                <li>Cada categoria pode ter um ícone associado para melhor identificação visual</li>
                <li>Não é possível eliminar categorias que estejam associadas a atividades existentes</li>
                <?php else: ?>
                <li>As categorias de produtos serão usadas na loja online (funcionalidade futura)</li>
                <li>Organize os produtos por categorias para facilitar a navegação dos clientes</li>
                <li>Não é possível eliminar categorias que estejam associadas a produtos</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
        <div class="p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Confirmar Eliminação</h3>
                    <p class="text-sm text-gray-500">Esta ação não pode ser revertida</p>
                </div>
            </div>

            <p class="text-gray-700 mb-6">
                Tem a certeza que deseja eliminar a categoria <strong id="categoryName"></strong>?
            </p>

            <div class="flex gap-3">
                <button onclick="closeDeleteModal()"
                        class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition-colors">
                    Cancelar
                </button>
                <a href="#"
                   id="confirmDeleteBtn"
                   class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors text-center">
                    Eliminar
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-generate slug from Portuguese name
function generateSlug(value) {
    if (!value) return;

    const slug = value
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '') // Remove accents
        .replace(/[^a-z0-9\s-]/g, '') // Remove special chars
        .trim()
        .replace(/\s+/g, '-') // Replace spaces with hyphens
        .replace(/-+/g, '-'); // Remove duplicate hyphens

    document.getElementById('slug_input').value = slug;
}

// Delete confirmation modal with event delegation
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('deleteModal');
    const categoryName = document.getElementById('categoryName');
    const confirmBtn = document.getElementById('confirmDeleteBtn');

    // Event delegation for all delete buttons
    document.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('.delete-category-btn');

        if (deleteBtn) {
            e.preventDefault();
            e.stopPropagation();

            const id = deleteBtn.dataset.id;
            const name = deleteBtn.dataset.name;

            if (modal && categoryName && confirmBtn) {
                categoryName.textContent = name;
                confirmBtn.href = '?type=<?= $currentType ?>&delete=' + id + '&token=<?= CSRF::getToken() ?>';
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            return false;
        }
    });
});

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
