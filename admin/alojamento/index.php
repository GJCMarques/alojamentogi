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

// Ensure all languages have translations (create if missing)
foreach ($languages as $lang) {
    $exists = $db->fetch(
        "SELECT id FROM accommodation_translations WHERE accommodation_id = ? AND language_id = ?",
        [$accommodation['id'], $lang['id']]
    );

    if (!$exists) {
        // Create missing translation
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

// Get amenities with all translations
$amenities = $db->fetchAll(
    "SELECT a.*,
            (SELECT name FROM amenity_translations WHERE amenity_id = a.id AND language_id = (SELECT id FROM languages WHERE code = 'pt' LIMIT 1)) as name_pt,
            (SELECT name FROM amenity_translations WHERE amenity_id = a.id AND language_id = (SELECT id FROM languages WHERE code = 'en' LIMIT 1)) as name_en
     FROM amenities a
     ORDER BY a.sort_order, a.id"
);
// Add translated_name for display (use PT as default)
foreach ($amenities as &$amenity) {
    $amenity['translated_name'] = $amenity['name_pt'] ?: $amenity['name_en'] ?: $amenity['icon'];
}
unset($amenity);

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

// Handle amenity add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_amenity'])) {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $icon = sanitize($_POST['amenity_icon'] ?? '');
        $namePt = sanitize($_POST['amenity_name_pt'] ?? '');
        $nameEn = sanitize($_POST['amenity_name_en'] ?? '');

        if ($namePt || $nameEn) {
            // Get max sort order
            $maxOrder = $db->fetch("SELECT MAX(sort_order) as max_order FROM amenities")['max_order'] ?? 0;

            // Insert amenity
            $db->insert('amenities', [
                'icon' => $icon,
                'sort_order' => $maxOrder + 1
            ]);
            $amenityId = $db->lastInsertId();

            // Insert translations
            foreach ($languages as $lang) {
                $name = $lang['code'] === 'pt' ? $namePt : $nameEn;
                if ($name) {
                    $db->insert('amenity_translations', [
                        'amenity_id' => $amenityId,
                        'language_id' => $lang['id'],
                        'name' => $name
                    ]);
                }
            }

            Session::flash('success', 'Comodidade adicionada com sucesso.');
        }
    }
    redirect('/admin/alojamento/');
}

// Handle amenity edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_amenity'])) {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $amenityId = (int)$_POST['amenity_id'];
        $icon = sanitize($_POST['amenity_icon'] ?? '');
        $namePt = sanitize($_POST['amenity_name_pt'] ?? '');
        $nameEn = sanitize($_POST['amenity_name_en'] ?? '');

        // Update amenity icon
        $db->update('amenities', ['icon' => $icon], 'id = ?', [$amenityId]);

        // Update translations
        foreach ($languages as $lang) {
            $name = $lang['code'] === 'pt' ? $namePt : $nameEn;
            $existing = $db->fetch(
                "SELECT id FROM amenity_translations WHERE amenity_id = ? AND language_id = ?",
                [$amenityId, $lang['id']]
            );

            if ($existing) {
                $db->update('amenity_translations', ['name' => $name], 'id = ?', [$existing['id']]);
            } else {
                $db->insert('amenity_translations', [
                    'amenity_id' => $amenityId,
                    'language_id' => $lang['id'],
                    'name' => $name
                ]);
            }
        }

        Session::flash('success', 'Comodidade atualizada com sucesso.');
    }
    redirect('/admin/alojamento/');
}

// Handle amenity delete
if (isset($_GET['delete_amenity']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $amenityId = (int)$_GET['delete_amenity'];

        // Delete translations first
        $db->delete('amenity_translations', 'amenity_id = ?', [$amenityId]);
        // Delete from accommodation_amenities
        $db->delete('accommodation_amenities', 'amenity_id = ?', [$amenityId]);
        // Delete amenity
        $db->delete('amenities', 'id = ?', [$amenityId]);

        Session::flash('success', 'Comodidade eliminada.');
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
                        <button type="button"
                                data-delete-image-id="<?= $image['id'] ?>"
                                data-delete-image-name="<?= e($image['original_name'] ?? 'imagem') ?>"
                                class="btn-delete-image absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-lg opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Upload Drop Zone -->
                <div id="galleryDropZone" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-secondary-500 hover:bg-secondary-50 transition-all cursor-pointer">
                    <input type="file" name="gallery[]" multiple accept="image/jpeg,image/png,image/webp" id="galleryInput" class="hidden">
                    <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p class="text-gray-600">Arraste imagens aqui ou clique para selecionar</p>
                    <p class="text-xs text-gray-400 mt-1">JPEG, PNG, WebP - Maximo 5MB</p>
                </div>

                <!-- Upload Preview -->
                <div id="galleryPreviewArea" class="hidden mt-4">
                    <div class="flex items-center justify-between mb-3">
                        <p class="font-medium text-gray-700">Novas imagens a carregar:</p>
                        <button type="button" id="clearGalleryFiles" class="text-sm text-red-500 hover:text-red-700">Limpar</button>
                    </div>
                    <div id="galleryPreviewList" class="grid grid-cols-3 md:grid-cols-4 gap-3"></div>
                    <div id="galleryPreviewTotal" class="mt-3 pt-3 border-t border-gray-200 text-sm font-medium text-gray-700"></div>
                </div>
            </div>

            <!-- Amenities -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-medium text-gray-800">Comodidades</h2>
                    <button type="button" id="btnAddAmenity" class="text-sm text-secondary-600 hover:text-secondary-700 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Adicionar Comodidade
                    </button>
                </div>

                <?php if (empty($amenities)): ?>
                <p class="text-gray-500 text-sm mb-4">Nenhuma comodidade definida. Clique em "Adicionar Comodidade" para criar.</p>
                <?php else: ?>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <?php foreach ($amenities as $amenity): ?>
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 group">
                        <label class="flex items-center cursor-pointer flex-1">
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
                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button type="button"
                                    data-edit-amenity='<?= json_encode(['id' => $amenity['id'], 'icon' => $amenity['icon'], 'name_pt' => $amenity['name_pt'] ?? '', 'name_en' => $amenity['name_en'] ?? ''], JSON_HEX_APOS | JSON_HEX_QUOT) ?>'
                                    class="btn-edit-amenity p-1 text-gray-400 hover:text-secondary-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button type="button"
                                    data-delete-amenity-id="<?= $amenity['id'] ?>"
                                    data-delete-amenity-name="<?= e($amenity['translated_name'] ?? $amenity['icon']) ?>"
                                    class="btn-delete-amenity p-1 text-gray-400 hover:text-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
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

<!-- Delete Image Modal -->
<div id="deleteImageModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4" onclick="event.stopPropagation()">
        <div class="p-6 text-center">
            <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Eliminar Imagem</h3>
            <p class="text-gray-600 mb-1">Tem a certeza que deseja eliminar:</p>
            <p id="deleteImageName" class="font-medium text-gray-800 mb-6 break-all"></p>
            <div class="flex gap-3 justify-center">
                <a id="deleteImageConfirmBtn" href="#"
                   class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors">
                    Eliminar
                </a>
                <button type="button" id="deleteImageCancelBtn"
                        class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition-colors">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Amenity Modal -->
<div id="amenityModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
            <h3 id="amenityModalTitle" class="text-lg font-medium text-gray-800">Adicionar Comodidade</h3>
            <button type="button" id="amenityModalClose" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="amenityForm" action="" method="post" class="p-6">
            <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
            <input type="hidden" name="add_amenity" id="amenityFormAction" value="1">
            <input type="hidden" name="amenity_id" id="amenityFormId" value="">

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Icone (emoji ou texto)</label>
                    <input type="text"
                           name="amenity_icon"
                           id="amenityIcon"
                           placeholder="🛏️ ou Wi-Fi"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500">
                    <p class="text-xs text-gray-400 mt-1">Pode usar emojis: 🛏️ 📺 🌐 🅿️ 🍳 ❄️ 🔥 🧺</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome (Portugues)</label>
                    <input type="text"
                           name="amenity_name_pt"
                           id="amenityNamePt"
                           placeholder="Ex: Wi-Fi Gratis"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome (Ingles)</label>
                    <input type="text"
                           name="amenity_name_en"
                           id="amenityNameEn"
                           placeholder="Ex: Free Wi-Fi"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500">
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="submit" class="px-6 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700 font-medium">
                    Guardar
                </button>
                <button type="button" id="amenityModalCancel"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Amenity Modal -->
<div id="deleteAmenityModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4" onclick="event.stopPropagation()">
        <div class="p-6 text-center">
            <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Eliminar Comodidade</h3>
            <p class="text-gray-600 mb-1">Tem a certeza que deseja eliminar:</p>
            <p id="deleteAmenityName" class="font-medium text-gray-800 mb-6"></p>
            <div class="flex gap-3 justify-center">
                <a id="deleteAmenityConfirmBtn" href="#"
                   class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors">
                    Eliminar
                </a>
                <button type="button" id="deleteAmenityCancelBtn"
                        class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition-colors">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';

    const csrfToken = '<?= CSRF::getToken() ?>';

    // =====================
    // LANGUAGE TABS
    // =====================
    window.switchTab = function(langCode) {
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
    };

    // =====================
    // GALLERY UPLOAD PREVIEW
    // =====================
    const galleryInput = document.getElementById('galleryInput');
    const galleryDropZone = document.getElementById('galleryDropZone');
    const galleryPreviewArea = document.getElementById('galleryPreviewArea');
    const galleryPreviewList = document.getElementById('galleryPreviewList');
    const galleryPreviewTotal = document.getElementById('galleryPreviewTotal');

    function formatSize(bytes) {
        if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
        if (bytes >= 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return bytes + ' B';
    }

    function updateGalleryPreview(files) {
        if (!files || files.length === 0) {
            galleryPreviewArea.classList.add('hidden');
            galleryPreviewList.innerHTML = '';
            galleryPreviewTotal.innerHTML = '';
            return;
        }

        galleryPreviewArea.classList.remove('hidden');
        galleryPreviewList.innerHTML = '';

        let totalSize = 0;
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            totalSize += file.size;

            const item = document.createElement('div');
            item.className = 'relative bg-gray-100 rounded-lg overflow-hidden aspect-video';

            const img = document.createElement('img');
            img.className = 'w-full h-full object-cover';
            img.alt = file.name;

            const overlay = document.createElement('div');
            overlay.className = 'absolute bottom-0 left-0 right-0 bg-black/60 px-2 py-1';
            overlay.innerHTML = '<p class="text-white text-xs truncate">' + file.name + '</p>' +
                               '<p class="text-gray-300 text-xs">' + formatSize(file.size) + '</p>';

            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);

            item.appendChild(img);
            item.appendChild(overlay);
            galleryPreviewList.appendChild(item);
        }

        galleryPreviewTotal.textContent = 'Total: ' + files.length + ' ficheiro(s) - ' + formatSize(totalSize);
    }

    function clearGalleryFiles() {
        galleryInput.value = '';
        galleryPreviewArea.classList.add('hidden');
        galleryPreviewList.innerHTML = '';
        galleryPreviewTotal.innerHTML = '';
    }

    galleryInput.addEventListener('change', function(e) {
        updateGalleryPreview(e.target.files);
    });

    galleryDropZone.addEventListener('click', function() {
        galleryInput.click();
    });

    galleryDropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.add('border-secondary-500', 'bg-secondary-50');
    });

    galleryDropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('border-secondary-500', 'bg-secondary-50');
    });

    galleryDropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('border-secondary-500', 'bg-secondary-50');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const dt = new DataTransfer();
            for (let i = 0; i < files.length; i++) {
                dt.items.add(files[i]);
            }
            galleryInput.files = dt.files;
            updateGalleryPreview(files);
        }
    });

    document.getElementById('clearGalleryFiles').addEventListener('click', clearGalleryFiles);

    // =====================
    // DELETE IMAGE MODAL
    // =====================
    const deleteImageModal = document.getElementById('deleteImageModal');

    function openDeleteImageModal(id, name) {
        document.getElementById('deleteImageName').textContent = name;
        document.getElementById('deleteImageConfirmBtn').href = '?delete_image=' + id + '&token=' + csrfToken;
        deleteImageModal.classList.remove('hidden');
    }

    function closeDeleteImageModal() {
        deleteImageModal.classList.add('hidden');
    }

    document.getElementById('deleteImageCancelBtn').addEventListener('click', closeDeleteImageModal);
    deleteImageModal.addEventListener('click', function(e) {
        if (e.target === this) closeDeleteImageModal();
    });

    // =====================
    // AMENITY MODAL
    // =====================
    const amenityModal = document.getElementById('amenityModal');

    function openAmenityModal(data) {
        if (data) {
            // Edit mode
            document.getElementById('amenityModalTitle').textContent = 'Editar Comodidade';
            document.getElementById('amenityFormAction').name = 'edit_amenity';
            document.getElementById('amenityFormId').value = data.id;
            document.getElementById('amenityIcon').value = data.icon || '';
            document.getElementById('amenityNamePt').value = data.name_pt || '';
            document.getElementById('amenityNameEn').value = data.name_en || '';
        } else {
            // Add mode
            document.getElementById('amenityModalTitle').textContent = 'Adicionar Comodidade';
            document.getElementById('amenityFormAction').name = 'add_amenity';
            document.getElementById('amenityFormId').value = '';
            document.getElementById('amenityIcon').value = '';
            document.getElementById('amenityNamePt').value = '';
            document.getElementById('amenityNameEn').value = '';
        }
        amenityModal.classList.remove('hidden');
    }

    function closeAmenityModal() {
        amenityModal.classList.add('hidden');
    }

    document.getElementById('btnAddAmenity').addEventListener('click', function() {
        openAmenityModal(null);
    });
    document.getElementById('amenityModalClose').addEventListener('click', closeAmenityModal);
    document.getElementById('amenityModalCancel').addEventListener('click', closeAmenityModal);
    amenityModal.addEventListener('click', function(e) {
        if (e.target === this) closeAmenityModal();
    });

    // =====================
    // DELETE AMENITY MODAL
    // =====================
    const deleteAmenityModal = document.getElementById('deleteAmenityModal');

    function openDeleteAmenityModal(id, name) {
        document.getElementById('deleteAmenityName').textContent = name;
        document.getElementById('deleteAmenityConfirmBtn').href = '?delete_amenity=' + id + '&token=' + csrfToken;
        deleteAmenityModal.classList.remove('hidden');
    }

    function closeDeleteAmenityModal() {
        deleteAmenityModal.classList.add('hidden');
    }

    document.getElementById('deleteAmenityCancelBtn').addEventListener('click', closeDeleteAmenityModal);
    deleteAmenityModal.addEventListener('click', function(e) {
        if (e.target === this) closeDeleteAmenityModal();
    });

    // =====================
    // EVENT DELEGATION
    // =====================
    document.addEventListener('click', function(e) {
        // Delete image button
        const deleteImageBtn = e.target.closest('.btn-delete-image');
        if (deleteImageBtn) {
            e.preventDefault();
            e.stopPropagation();
            const id = deleteImageBtn.getAttribute('data-delete-image-id');
            const name = deleteImageBtn.getAttribute('data-delete-image-name');
            openDeleteImageModal(id, name);
            return;
        }

        // Edit amenity button
        const editAmenityBtn = e.target.closest('.btn-edit-amenity');
        if (editAmenityBtn) {
            e.preventDefault();
            e.stopPropagation();
            try {
                const data = JSON.parse(editAmenityBtn.getAttribute('data-edit-amenity'));
                openAmenityModal(data);
            } catch (err) {
                console.error('Error parsing amenity data:', err);
            }
            return;
        }

        // Delete amenity button
        const deleteAmenityBtn = e.target.closest('.btn-delete-amenity');
        if (deleteAmenityBtn) {
            e.preventDefault();
            e.stopPropagation();
            const id = deleteAmenityBtn.getAttribute('data-delete-amenity-id');
            const name = deleteAmenityBtn.getAttribute('data-delete-amenity-name');
            openDeleteAmenityModal(id, name);
            return;
        }
    });

    // =====================
    // KEYBOARD NAVIGATION
    // =====================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!deleteImageModal.classList.contains('hidden')) closeDeleteImageModal();
            if (!amenityModal.classList.contains('hidden')) closeAmenityModal();
            if (!deleteAmenityModal.classList.contains('hidden')) closeDeleteAmenityModal();
        }
    });

})();
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
