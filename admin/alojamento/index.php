<?php
/**
 * A Casa do Gi - Admin Accommodation
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;

$db = Database::getInstance();

// Get languages
$languages = $db->fetchAll("SELECT * FROM languages WHERE is_active = 1 ORDER BY is_default DESC");

// Get or create accommodation
$accommodation = $db->fetch("SELECT * FROM accommodation LIMIT 1");

if (!$accommodation) {
    // Create default accommodation record
    $db->insert('accommodation', [
        'max_guests' => 4,
        'bedrooms' => 2,
        'bathrooms' => 1,
        'is_active' => 1
    ]);
    $accommodation = $db->fetch("SELECT * FROM accommodation ORDER BY id DESC LIMIT 1");

    // Create translations
    foreach ($languages as $lang) {
        $db->insert('accommodation_translations', [
            'accommodation_id' => $accommodation['id'],
            'language_id' => $lang['id'],
            'name' => 'A Casa do Gi',
            'tagline' => '',
            'description' => ''
        ]);
    }
}

// Get translations
$translations = [];
$translationRows = $db->fetchAll(
    "SELECT * FROM accommodation_translations WHERE accommodation_id = ?",
    [$accommodation['id']]
);
foreach ($translationRows as $row) {
    $translations[$row['language_id']] = $row;
}

// Get amenities
$amenities = $db->fetchAll(
    "SELECT a.*, at.name as translated_name
     FROM amenities a
     LEFT JOIN amenity_translations at ON a.id = at.amenity_id AND at.language_id = 1
     ORDER BY a.sort_order, a.id"
);

// Get accommodation amenities
$accommodationAmenities = $db->fetchAll(
    "SELECT amenity_id FROM accommodation_amenities WHERE accommodation_id = ?",
    [$accommodation['id']]
);
$selectedAmenities = array_column($accommodationAmenities, 'amenity_id');

// Get gallery images from media table (accommodation_gallery table doesn't exist)
$galleryImages = $db->fetchAll(
    "SELECT * FROM media WHERE category = 'gallery' ORDER BY sort_order"
);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        // Update accommodation
        $db->update('accommodation', [
            'max_guests' => (int)$_POST['max_guests'],
            'bedrooms' => (int)$_POST['bedrooms'],
            'bathrooms' => (int)$_POST['bathrooms'],
            'area_sqm' => (int)($_POST['area_sqm'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ], 'id = ?', [$accommodation['id']]);

        // Update translations
        foreach ($languages as $lang) {
            $db->update('accommodation_translations', [
                'name' => sanitize($_POST['name_' . $lang['id']] ?? ''),
                'tagline' => sanitize($_POST['tagline_' . $lang['id']] ?? ''),
                'description' => $_POST['description_' . $lang['id']] ?? ''
            ], 'accommodation_id = ? AND language_id = ?', [$accommodation['id'], $lang['id']]);
        }

        // Update amenities
        $db->delete('accommodation_amenities', 'accommodation_id = ?', [$accommodation['id']]);
        if (!empty($_POST['amenities'])) {
            foreach ($_POST['amenities'] as $amenityId) {
                $db->insert('accommodation_amenities', [
                    'accommodation_id' => $accommodation['id'],
                    'amenity_id' => (int)$amenityId
                ]);
            }
        }

        // Handle image upload
        if (!empty($_FILES['gallery']['name'][0])) {
            $uploadDir = ROOT_PATH . '/uploads/accommodation/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $maxOrder = $db->fetch(
                "SELECT MAX(sort_order) as max_order FROM media WHERE category = 'gallery'",
                []
            )['max_order'] ?? 0;

            foreach ($_FILES['gallery']['tmp_name'] as $key => $tmpName) {
                if ($_FILES['gallery']['error'][$key] !== UPLOAD_ERR_OK) continue;
                if (!in_array($_FILES['gallery']['type'][$key], $allowedTypes)) continue;

                $ext = pathinfo($_FILES['gallery']['name'][$key], PATHINFO_EXTENSION);
                $newName = 'accommodation_' . uniqid() . '.' . $ext;

                if (move_uploaded_file($tmpName, $uploadDir . $newName)) {
                    $maxOrder++;
                    $fileSize = $_FILES['gallery']['size'][$key];
                    $fileType = $_FILES['gallery']['type'][$key];
                    $originalName = $_FILES['gallery']['name'][$key];

                    $db->insert('media', [
                        'filename' => $newName,
                        'original_name' => $originalName,
                        'file_path' => '/uploads/accommodation/' . $newName,
                        'file_type' => $fileType,
                        'file_size' => $fileSize,
                        'category' => 'gallery',
                        'sort_order' => $maxOrder
                    ]);
                }
            }
        }

        Session::flash('success', 'Alojamento atualizado com sucesso.');
        redirect('/admin/alojamento/');
    }
}

// Handle image delete
if (isset($_GET['delete_image']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $imageId = (int)$_GET['delete_image'];
        $image = $db->fetch("SELECT * FROM media WHERE id = ?", [$imageId]);

        if ($image) {
            $filePath = ROOT_PATH . $image['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $db->delete('media', 'id = ?', [$imageId]);
            Session::flash('success', 'Imagem eliminada.');
        }
    }
    redirect('/admin/alojamento/');
}

$pageTitle = 'Alojamento';
$currentPage = 'alojamento';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Alojamento</h1>
        <p class="text-gray-600">Gerir informacoes do alojamento</p>
    </div>
    <a href="<?= basePath() ?>/alojamento/" target="_blank" class="text-secondary-600 hover:text-secondary-700 text-sm flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
        </svg>
        Ver no site
    </a>
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                            <input type="text"
                                   name="name_<?= $lang['id'] ?>"
                                   value="<?= e($translations[$lang['id']]['name'] ?? '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tagline</label>
                            <input type="text"
                                   name="tagline_<?= $lang['id'] ?>"
                                   value="<?= e($translations[$lang['id']]['tagline'] ?? '') ?>"
                                   placeholder="Uma frase curta descritiva..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descricao</label>
                            <textarea name="description_<?= $lang['id'] ?>"
                                      rows="6"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500"><?= e($translations[$lang['id']]['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Gallery -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-medium text-gray-800 mb-4">Galeria de Imagens</h2>

                <?php if (!empty($galleryImages)): ?>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <?php foreach ($galleryImages as $image): ?>
                    <div class="relative group aspect-video rounded-lg overflow-hidden bg-gray-100">
                        <img src="<?= basePath() . e($image['file_path']) ?>" alt="" class="w-full h-full object-cover">
                        <a href="?delete_image=<?= $image['id'] ?>&token=<?= CSRF::getToken() ?>"
                           onclick="return confirm('Eliminar esta imagem?')"
                           class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                    <input type="file" name="gallery[]" multiple accept="image/*" id="galleryInput" class="hidden">
                    <label for="galleryInput" class="cursor-pointer">
                        <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <p class="text-gray-600">Clique para adicionar imagens</p>
                        <p class="text-xs text-gray-400 mt-1">JPEG, PNG, WebP</p>
                    </label>
                </div>
            </div>

            <!-- Amenities -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-medium text-gray-800 mb-4">Comodidades</h2>

                <?php if (empty($amenities)): ?>
                <p class="text-gray-500 text-sm">Nenhuma comodidade definida. Adicione na base de dados.</p>
                <?php else: ?>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <?php foreach ($amenities as $amenity): ?>
                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox"
                               name="amenities[]"
                               value="<?= $amenity['id'] ?>"
                               <?= in_array($amenity['id'], $selectedAmenities) ? 'checked' : '' ?>
                               class="w-4 h-4 text-secondary-600 border-gray-300 rounded focus:ring-secondary-500">
                        <span class="ml-2 text-sm text-gray-700">
                            <?php if ($amenity['icon']): ?>
                            <span class="mr-1"><?= $amenity['icon'] ?></span>
                            <?php endif; ?>
                            <?= e($amenity['translated_name'] ?? $amenity['icon']) ?>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Details -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-medium text-gray-800 mb-4">Detalhes</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max. Hospedes</label>
                        <input type="number"
                               name="max_guests"
                               value="<?= e($accommodation['max_guests']) ?>"
                               min="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quartos</label>
                        <input type="number"
                               name="bedrooms"
                               value="<?= e($accommodation['bedrooms']) ?>"
                               min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Casas de Banho</label>
                        <input type="number"
                               name="bathrooms"
                               value="<?= e($accommodation['bathrooms']) ?>"
                               min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Area (m²)</label>
                        <input type="number"
                               name="area_sqm"
                               value="<?= e($accommodation['area_sqm'] ?? '') ?>"
                               min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-medium text-gray-800 mb-4">Estado</h2>

                <label class="flex items-center">
                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           <?= $accommodation['is_active'] ? 'checked' : '' ?>
                           class="w-4 h-4 text-secondary-600 border-gray-300 rounded focus:ring-secondary-500">
                    <span class="ml-2 text-sm text-gray-700">Visivel no site</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <button type="submit" class="w-full px-4 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700">
                    Guardar Alteracoes
                </button>
            </div>
        </div>
    </div>
</form>

<script>
function switchTab(langCode) {
    // Update tabs
    document.querySelectorAll('.lang-tab').forEach(tab => {
        if (tab.dataset.lang === langCode) {
            tab.classList.add('border-secondary-600', 'text-secondary-600');
            tab.classList.remove('border-transparent', 'text-gray-500');
        } else {
            tab.classList.remove('border-secondary-600', 'text-secondary-600');
            tab.classList.add('border-transparent', 'text-gray-500');
        }
    });

    // Update content
    document.querySelectorAll('.lang-content').forEach(content => {
        if (content.dataset.lang === langCode) {
            content.classList.remove('hidden');
        } else {
            content.classList.add('hidden');
        }
    });
}
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
