<?php
/**
 * A Casa do Gi - Admin Product Categories
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;

$db = Database::getInstance();

// Get languages
$languages = $db->fetchAll("SELECT * FROM languages WHERE is_active = 1 ORDER BY is_default DESC");

// Handle delete
if (isset($_GET['delete']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $categoryId = (int)$_GET['delete'];

        // Check if category has products
        $hasProducts = $db->fetch("SELECT COUNT(*) as count FROM products WHERE category_id = ?", [$categoryId])['count'] > 0;

        if ($hasProducts) {
            Session::flash('error', 'Não é possível eliminar uma categoria com produtos.');
        } else {
            $db->delete('product_category_translations', 'category_id = ?', [$categoryId]);
            $db->delete('product_categories', 'id = ?', [$categoryId]);
            Session::flash('success', 'Categoria eliminada com sucesso.');
        }
    }
    redirect('/admin/categorias/');
}

// Handle form submission
$errors = [];
$editCategory = null;
$editTranslations = [];

// Check if editing
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $editCategory = $db->fetch("SELECT * FROM product_categories WHERE id = ?", [$editId]);

    if ($editCategory) {
        $editTrans = $db->fetchAll("SELECT * FROM product_category_translations WHERE category_id = ?", [$editId]);
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
        $slug = sanitize($_POST['slug'] ?? '');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $translations = $_POST['translations'] ?? [];

        // Validate
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

        // Auto-generate slug if empty
        if (empty($slug)) {
            $name = $translations[1]['name'] ?? $translations[2]['name'] ?? 'categoria';
            $slug = createSlug($name);
        }

        // Check slug uniqueness
        $existingSlug = $db->fetch(
            "SELECT id FROM product_categories WHERE slug = ? AND id != ?",
            [$slug, $categoryId]
        );
        if ($existingSlug) {
            $slug = $slug . '-' . time();
        }

        if (empty($errors)) {
            $db->beginTransaction();

            try {
                if ($categoryId) {
                    // Update
                    $db->update('product_categories', [
                        'slug' => $slug,
                        'sort_order' => $sortOrder,
                        'is_active' => $isActive
                    ], 'id = ?', [$categoryId]);

                    // Update translations
                    foreach ($translations as $langId => $trans) {
                        $existing = $db->fetch(
                            "SELECT id FROM product_category_translations WHERE category_id = ? AND language_id = ?",
                            [$categoryId, $langId]
                        );

                        if ($existing) {
                            $db->update('product_category_translations', [
                                'name' => sanitize($trans['name'] ?? ''),
                                'description' => sanitize($trans['description'] ?? '')
                            ], 'id = ?', [$existing['id']]);
                        } elseif (!empty($trans['name'])) {
                            $db->insert('product_category_translations', [
                                'category_id' => $categoryId,
                                'language_id' => (int)$langId,
                                'name' => sanitize($trans['name']),
                                'description' => sanitize($trans['description'] ?? '')
                            ]);
                        }
                    }

                    Session::flash('success', 'Categoria atualizada com sucesso.');
                } else {
                    // Create
                    $categoryId = $db->insert('product_categories', [
                        'slug' => $slug,
                        'sort_order' => $sortOrder,
                        'is_active' => $isActive
                    ]);

                    // Insert translations
                    foreach ($translations as $langId => $trans) {
                        if (!empty($trans['name'])) {
                            $db->insert('product_category_translations', [
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
                redirect('/admin/categorias/');

            } catch (\Exception $e) {
                $db->rollback();
                $errors[] = 'Erro ao guardar: ' . $e->getMessage();
            }
        }
    }
}

// Get all categories
$categories = $db->fetchAll(
    "SELECT c.*, ct.name,
            (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count
     FROM product_categories c
     LEFT JOIN product_category_translations ct ON c.id = ct.category_id AND ct.language_id = 1
     ORDER BY c.sort_order ASC, c.id ASC"
);

$pageTitle = 'Categorias de Produtos';
$currentPage = 'categorias';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Categorias de Produtos</h1>
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
                           value="<?= e($editTranslations[$lang['id']]['name'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-olive-500"
                           <?= $lang['is_default'] ? 'required' : '' ?>>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Descrição (<?= e($lang['name']) ?>)
                    </label>
                    <textarea name="translations[<?= $lang['id'] ?>][description]"
                              rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-olive-500"><?= e($editTranslations[$lang['id']]['description'] ?? '') ?></textarea>
                </div>
                <?php endforeach; ?>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug (URL)</label>
                    <input type="text"
                           name="slug"
                           value="<?= e($editCategory['slug'] ?? '') ?>"
                           placeholder="auto-gerado"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-olive-500">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ordem</label>
                    <input type="number"
                           name="sort_order"
                           value="<?= e($editCategory['sort_order'] ?? 0) ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-olive-500">
                </div>

                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               <?= ($editCategory['is_active'] ?? 1) ? 'checked' : '' ?>
                               class="w-4 h-4 text-olive-600 border-gray-300 rounded focus:ring-olive-500">
                        <span class="ml-2 text-gray-700">Ativa</span>
                    </label>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-olive-600 text-white rounded-lg hover:bg-olive-700">
                        <?= $editCategory ? 'Guardar' : 'Criar' ?>
                    </button>
                    <?php if ($editCategory): ?>
                    <a href="<?= basePath() ?>/admin/categorias/" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
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
                <p>Nenhuma categoria criada.</p>
            </div>
            <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoria</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produtos</th>
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
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?= $cat['product_count'] ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($cat['is_active']): ?>
                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Ativa</span>
                            <?php else: ?>
                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">Inativa</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="?edit=<?= $cat['id'] ?>" class="text-olive-600 hover:text-olive-900 mr-3">Editar</a>
                            <?php if ($cat['product_count'] == 0): ?>
                            <a href="?delete=<?= $cat['id'] ?>&token=<?= CSRF::getToken() ?>"
                               class="text-red-600 hover:text-red-900"
                               onclick="return confirm('Eliminar esta categoria?')">
                                Eliminar
                            </a>
                            <?php else: ?>
                            <span class="text-gray-400" title="Não pode eliminar - tem produtos">Eliminar</span>
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

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
