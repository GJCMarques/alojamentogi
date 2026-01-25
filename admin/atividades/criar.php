<?php
/**
 * A Casa do Gi - Admin Create Activity
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;

$db = Database::getInstance();

// Get languages
$languages = $db->fetchAll("SELECT * FROM languages WHERE is_active = 1 ORDER BY is_default DESC");

// Categories
$categories = [
    'natureza' => 'Natureza',
    'cultura' => 'Cultura',
    'gastronomia' => 'Gastronomia',
    'aventura' => 'Aventura',
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $errors = [];

        // Validate
        $name = trim($_POST['name_1'] ?? '');
        if (empty($name)) {
            $errors[] = 'O nome e obrigatorio.';
        }

        $category = $_POST['category'] ?? '';
        if (!isset($categories[$category])) {
            $errors[] = 'Categoria invalida.';
        }

        if (empty($errors)) {
            // Handle image upload
            $imagePath = null;
            if (!empty($_FILES['image']['name'])) {
                $uploadDir = ROOT_PATH . '/uploads/activities/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                if (in_array($_FILES['image']['type'], $allowedTypes)) {
                    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $newName = 'activity_' . uniqid() . '.' . $ext;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newName)) {
                        $imagePath = '/uploads/activities/' . $newName;
                    }
                }
            }

            // Insert activity
            $db->insert('activities', [
                'category' => $category,
                'image_path' => $imagePath,
                'external_url' => sanitize($_POST['external_url'] ?? ''),
                'location' => sanitize($_POST['location'] ?? ''),
                'distance_km' => !empty($_POST['distance_km']) ? (float)$_POST['distance_km'] : null,
                'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'sort_order' => (int)($_POST['sort_order'] ?? 0)
            ]);

            $activityId = $db->lastInsertId();

            // Insert translations
            foreach ($languages as $lang) {
                $db->insert('activity_translations', [
                    'activity_id' => $activityId,
                    'language_id' => $lang['id'],
                    'name' => sanitize($_POST['name_' . $lang['id']] ?? ''),
                    'short_description' => sanitize($_POST['short_description_' . $lang['id']] ?? ''),
                    'description' => $_POST['description_' . $lang['id']] ?? ''
                ]);
            }

            Session::flash('success', 'Atividade criada com sucesso.');
            redirect('/admin/atividades/');
        } else {
            Session::flash('error', implode('<br>', $errors));
        }
    }
}

$pageTitle = 'Nova Atividade';
$currentPage = 'atividades';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="mb-6">
    <a href="/admin/atividades/" class="text-secondary-600 hover:text-secondary-700 text-sm">&larr; Voltar</a>
</div>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Nova Atividade</h1>
</div>

<form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Translations -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="border-b border-gray-200">
                    <nav class="flex" id="langTabs">
                        <?php foreach ($languages as $i => $lang): ?>
                        <button type="button"
                                onclick="switchTab('<?= $lang['code'] ?>')"
                                class="lang-tab px-6 py-3 text-sm font-medium border-b-2 <?= $i === 0 ? 'border-secondary-600 text-secondary-600' : 'border-transparent text-gray-500 hover:text-gray-700' ?>"
                                data-lang="<?= $lang['code'] ?>">
                            <?= strtoupper($lang['code']) ?>
                        </button>
                        <?php endforeach; ?>
                    </nav>
                </div>

                <?php foreach ($languages as $i => $lang): ?>
                <div class="lang-content p-6 <?= $i > 0 ? 'hidden' : '' ?>" data-lang="<?= $lang['code'] ?>">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nome <?= $lang['is_default'] ? '<span class="text-red-500">*</span>' : '' ?>
                            </label>
                            <input type="text"
                                   name="name_<?= $lang['id'] ?>"
                                   value="<?= e($_POST['name_' . $lang['id']] ?? '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descricao Curta</label>
                            <input type="text"
                                   name="short_description_<?= $lang['id'] ?>"
                                   value="<?= e($_POST['short_description_' . $lang['id']] ?? '') ?>"
                                   placeholder="Uma frase curta..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descricao Completa</label>
                            <textarea name="description_<?= $lang['id'] ?>"
                                      rows="6"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500"><?= e($_POST['description_' . $lang['id']] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Location & Link -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-medium text-gray-800 mb-4">Localizacao</h2>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Local</label>
                        <input type="text"
                               name="location"
                               value="<?= e($_POST['location'] ?? '') ?>"
                               placeholder="Ex: Mogadouro"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Distancia (km)</label>
                        <input type="number"
                               name="distance_km"
                               value="<?= e($_POST['distance_km'] ?? '') ?>"
                               step="0.1"
                               min="0"
                               placeholder="Ex: 5.5"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Link Externo</label>
                        <input type="url"
                               name="external_url"
                               value="<?= e($_POST['external_url'] ?? '') ?>"
                               placeholder="https://..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Image -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-medium text-gray-800 mb-4">Imagem</h2>

                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center" id="imagePreview">
                    <input type="file" name="image" accept="image/*" id="imageInput" class="hidden">
                    <label for="imageInput" class="cursor-pointer">
                        <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-gray-600 text-sm">Clique para selecionar</p>
                    </label>
                </div>
            </div>

            <!-- Category -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-medium text-gray-800 mb-4">Categoria</h2>

                <select name="category" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                    <option value="">Selecionar...</option>
                    <?php foreach ($categories as $key => $label): ?>
                    <option value="<?= $key ?>" <?= ($_POST['category'] ?? '') === $key ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Options -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-medium text-gray-800 mb-4">Opcoes</h2>

                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" checked
                               class="w-4 h-4 text-secondary-600 border-gray-300 rounded focus:ring-secondary-500">
                        <span class="ml-2 text-sm text-gray-700">Ativo</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" value="1"
                               class="w-4 h-4 text-secondary-600 border-gray-300 rounded focus:ring-secondary-500">
                        <span class="ml-2 text-sm text-gray-700">Destaque</span>
                    </label>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ordem</label>
                    <input type="number"
                           name="sort_order"
                           value="<?= e($_POST['sort_order'] ?? '0') ?>"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <button type="submit" class="w-full px-4 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700">
                    Criar Atividade
                </button>
            </div>
        </div>
    </div>
</form>

<script>
function switchTab(langCode) {
    document.querySelectorAll('.lang-tab').forEach(tab => {
        if (tab.dataset.lang === langCode) {
            tab.classList.add('border-secondary-600', 'text-secondary-600');
            tab.classList.remove('border-transparent', 'text-gray-500');
        } else {
            tab.classList.remove('border-secondary-600', 'text-secondary-600');
            tab.classList.add('border-transparent', 'text-gray-500');
        }
    });

    document.querySelectorAll('.lang-content').forEach(content => {
        if (content.dataset.lang === langCode) {
            content.classList.remove('hidden');
        } else {
            content.classList.add('hidden');
        }
    });
}

// Image preview
document.getElementById('imageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').innerHTML = `
                <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg">
                <p class="text-xs text-gray-500 mt-2">Clique para alterar</p>
            `;
        };
        reader.readAsDataURL(file);
    }
});
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
