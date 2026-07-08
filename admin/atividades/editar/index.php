<?php

require_once dirname(dirname(dirname(__DIR__))) . '/includes/init.php';
require_once dirname(dirname(__DIR__)) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;

$db = Database::getInstance();

$activityId = (int)($_GET['id'] ?? 0);
if (!$activityId) {
    redirect('/admin/atividades/');
}

$activity = $db->fetch("SELECT * FROM activities WHERE id = ?", [$activityId]);
if (!$activity) {
    Session::flash('error', 'Atividade não encontrada.');
    redirect('/admin/atividades/');
}

$languages = $db->fetchAll("SELECT * FROM languages WHERE is_active = 1 ORDER BY is_default DESC");

$translations = [];
foreach ($languages as $lang) {
    $trans = $db->fetch(
        "SELECT * FROM activity_translations WHERE activity_id = ? AND language_id = ?",
        [$activityId, $lang['id']]
    );
    $translations[$lang['id']] = $trans ?: [
        'title' => '',
        'short_description' => '',
        'full_description' => '',
        'tips' => ''
    ];
}

$galleryImages = $db->fetchAll(
    "SELECT * FROM media WHERE entity_type = 'activity' AND entity_id = ? ORDER BY sort_order ASC",
    [$activityId]
);

$currentCoverImage = $db->fetch(
    "SELECT * FROM media WHERE entity_type = 'activity' AND entity_id = ? AND is_cover = 1",
    [$activityId]
);

$categories = $db->fetchAll(
    "SELECT c.id, ct.name
     FROM categories c
     INNER JOIN category_translations ct ON c.id = ct.category_id
     WHERE c.type = 'activity' AND c.is_active = 1 AND ct.language_id = 1
     ORDER BY c.sort_order ASC"
);

$priceRanges = [
    '' => 'Não especificado',
    'free' => 'Gratuito',
    'budget' => 'Económico',
    'moderate' => 'Moderado',
    'expensive' => 'Premium',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $errors = [];

        $titlePt = trim($_POST['title_1'] ?? '');
        if (empty($titlePt)) {
            $errors[] = 'O título (PT) é obrigatório.';
        }

        $categoryId = (int)($_POST['category_id'] ?? 0);
        if ($categoryId <= 0) {
            $errors[] = 'Categoria inválida.';
        }

        if (empty($errors)) {

            $slug = $_POST['slug'] ?? '';
            if (empty($slug)) {
                $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $titlePt));
                $slug = trim($slug, '-');
            }

            $existingSlug = $db->fetch("SELECT id FROM activities WHERE slug = ? AND id != ?", [$slug, $activityId]);
            if ($existingSlug) {
                $slug .= '-' . time();
            }

            if (!empty($_FILES['cover_image']['name']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = ROOT_PATH . '/uploads/activities/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                if (in_array($_FILES['cover_image']['type'], $allowedTypes)) {
                    $originalName = $_FILES['cover_image']['name'];
                    $fileSize = $_FILES['cover_image']['size'];
                    $fileType = $_FILES['cover_image']['type'];
                    $ext = pathinfo($originalName, PATHINFO_EXTENSION);
                    $newName = 'activity_cover_' . $activityId . '_' . uniqid() . '.' . $ext;

                    if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $uploadDir . $newName)) {

                        $oldCover = $db->fetch("SELECT * FROM media WHERE entity_type = 'activity' AND entity_id = ? AND is_cover = 1", [$activityId]);
                        if ($oldCover) {
                            $oldPath = ROOT_PATH . ltrim($oldCover['file_path'], '/');
                            if (file_exists($oldPath)) @unlink($oldPath);
                            $db->delete('media', 'id = ?', [$oldCover['id']]);
                        }

                        $db->insert('media', [
                            'filename' => $newName,
                            'original_name' => $originalName,
                            'file_path' => '/uploads/activities/' . $newName,
                            'file_type' => $fileType,
                            'file_size' => $fileSize,
                            'category' => 'activities',
                            'entity_type' => 'activity',
                            'entity_id' => $activityId,
                            'is_cover' => 1,
                            'sort_order' => 0,
                            'uploaded_by' => $_SESSION['admin_id'] ?? null
                        ]);
                    }
                }
            }

            $db->update('activities', [
                'slug' => $slug,
                'category_id' => $categoryId,
                'address' => sanitize($_POST['address'] ?? ''),
                'phone' => sanitize($_POST['phone'] ?? ''),
                'email' => sanitize($_POST['email'] ?? ''),
                'website' => sanitize($_POST['website'] ?? ''),
                'external_url' => sanitize($_POST['external_url'] ?? ''),
                'latitude' => !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null,
                'longitude' => !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null,
                'distance_km' => !empty($_POST['distance_km']) ? (float)$_POST['distance_km'] : null,
                'price_range' => $_POST['price_range'] ?: null,
                'google_maps_embed' => $_POST['google_maps_embed'] ?? '',
                'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'sort_order' => (int)($_POST['sort_order'] ?? 0)
            ], 'id = ?', [$activityId]);

            foreach ($languages as $lang) {
                $existingTrans = $db->fetch(
                    "SELECT id FROM activity_translations WHERE activity_id = ? AND language_id = ?",
                    [$activityId, $lang['id']]
                );

                $transData = [
                    'title' => sanitize($_POST['title_' . $lang['id']] ?? ''),
                    'short_description' => sanitize($_POST['short_description_' . $lang['id']] ?? ''),
                    'full_description' => $_POST['full_description_' . $lang['id']] ?? '',
                    'tips' => sanitize($_POST['tips_' . $lang['id']] ?? ''),
                ];

                if ($existingTrans) {
                    $db->update('activity_translations', $transData, 'id = ?', [$existingTrans['id']]);
                } else {
                    $transData['activity_id'] = $activityId;
                    $transData['language_id'] = $lang['id'];
                    $db->insert('activity_translations', $transData);
                }
            }

            if (!empty($_FILES['gallery']['name'][0])) {
                $uploadDir = ROOT_PATH . '/uploads/activities/';
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

                $maxSort = $db->fetch("SELECT MAX(sort_order) as max_sort FROM media WHERE entity_type = 'activity' AND entity_id = ?", [$activityId]);
                $sortOrder = ($maxSort['max_sort'] ?? 0) + 1;

                foreach ($_FILES['gallery']['tmp_name'] as $key => $tmpName) {
                    if ($_FILES['gallery']['error'][$key] !== UPLOAD_ERR_OK) continue;
                    if (!in_array($_FILES['gallery']['type'][$key], $allowedTypes)) continue;

                    $originalName = $_FILES['gallery']['name'][$key];
                    $fileSize = $_FILES['gallery']['size'][$key];
                    $fileType = $_FILES['gallery']['type'][$key];
                    $ext = pathinfo($originalName, PATHINFO_EXTENSION);
                    $newName = 'activity_gallery_' . $activityId . '_' . uniqid() . '.' . $ext;

                    if (move_uploaded_file($tmpName, $uploadDir . $newName)) {
                        $db->insert('media', [
                            'filename' => $newName,
                            'original_name' => $originalName,
                            'file_path' => '/uploads/activities/' . $newName,
                            'file_type' => $fileType,
                            'file_size' => $fileSize,
                            'category' => 'activities',
                            'entity_type' => 'activity',
                            'entity_id' => $activityId,
                            'is_cover' => 0,
                            'sort_order' => $sortOrder++,
                            'uploaded_by' => $_SESSION['admin_id'] ?? null
                        ]);
                    }
                }
            }

            if (!empty($_POST['delete_images_list'])) {
                $imageIds = array_filter(array_map('intval', explode(',', $_POST['delete_images_list'])));
                foreach ($imageIds as $imageId) {
                    $image = $db->fetch("SELECT * FROM media WHERE id = ? AND entity_type = 'activity' AND entity_id = ?", [$imageId, $activityId]);
                    if ($image) {

                        $filePath = ROOT_PATH . ltrim($image['file_path'], '/');
                        if (file_exists($filePath)) {
                            @unlink($filePath);
                        }

                        $db->delete('media', 'id = ?', [$imageId]);
                    }
                }
            }

            Session::flash('success', 'Atividade atualizada com sucesso.');
            redirect('/admin/atividades/');
        } else {
            Session::flash('error', implode('<br>', $errors));
        }
    }
}

$pageTitle = 'Editar Atividade';
include dirname(dirname(__DIR__)) . '/includes/header.php';
?>

<div class="p-6 lg:p-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-4">
            <a href="<?= basePath() ?>/admin/atividades/" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">Editar Atividade</h1>
                <p class="text-gray-600 mt-1"><?= htmlspecialchars($translations[1]['title'] ?? 'Sem título') ?></p>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if ($errorMessages = Session::getFlash('error')): $error = implode('<br>', $errorMessages); ?>
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
        <?= $error ?>
    </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-8">
        <?= CSRF::tokenField() ?>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Basic Info Card -->
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="font-bold text-gray-800">Informações Básicas</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Title tabs for languages -->
                        <div>
                            <div class="flex gap-2 mb-4" id="lang-tabs">
                                <?php foreach ($languages as $idx => $lang): ?>
                                <button type="button"
                                        onclick="switchLangTab('<?= $lang['code'] ?>')"
                                        class="lang-tab px-4 py-2 rounded-lg text-sm font-semibold transition-all <?= $idx === 0 ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>"
                                        data-lang="<?= $lang['code'] ?>">
                                    <?= strtoupper($lang['code']) ?>
                                </button>
                                <?php endforeach; ?>
                            </div>

                            <?php foreach ($languages as $idx => $lang): ?>
                            <div class="lang-content space-y-6 <?= $idx !== 0 ? 'hidden' : '' ?>" data-lang="<?= $lang['code'] ?>">
                                <!-- Title -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Título (<?= strtoupper($lang['code']) ?>) <?= $lang['is_default'] ? '<span class="text-red-500">*</span>' : '' ?>
                                    </label>
                                    <input type="text"
                                           name="title_<?= $lang['id'] ?>"
                                           value="<?= htmlspecialchars($translations[$lang['id']]['title'] ?? '') ?>"
                                           <?= $lang['is_default'] ? 'required' : '' ?>
                                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all"
                                           placeholder="Nome da atividade">
                                </div>

                                <!-- Short Description -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Descrição Curta (<?= strtoupper($lang['code']) ?>)
                                    </label>
                                    <textarea name="short_description_<?= $lang['id'] ?>"
                                              rows="2"
                                              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all resize-none"
                                              placeholder="Breve descrição para os cards..."><?= htmlspecialchars($translations[$lang['id']]['short_description'] ?? '') ?></textarea>
                                </div>

                                <!-- Full Description -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Descrição Completa (<?= strtoupper($lang['code']) ?>)
                                    </label>
                                    <textarea name="full_description_<?= $lang['id'] ?>"
                                              rows="8"
                                              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all resize-none"
                                              placeholder="Descrição completa com HTML permitido..."><?= htmlspecialchars($translations[$lang['id']]['full_description'] ?? '') ?></textarea>
                                    <p class="text-xs text-gray-500 mt-1">Pode usar HTML básico (&lt;p&gt;, &lt;strong&gt;, etc.)</p>
                                </div>

                                <!-- Tips -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Dicas Locais (<?= strtoupper($lang['code']) ?>)
                                    </label>
                                    <textarea name="tips_<?= $lang['id'] ?>"
                                              rows="2"
                                              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all resize-none"
                                              placeholder="Dicas para visitantes..."><?= htmlspecialchars($translations[$lang['id']]['tips'] ?? '') ?></textarea>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Location Card -->
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="font-bold text-gray-800">Localização e Contacto</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Address -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Morada</label>
                            <input type="text" name="address"
                                   value="<?= htmlspecialchars($activity['address'] ?? '') ?>"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all"
                                   placeholder="Rua, número, código postal, cidade">
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <!-- Phone -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Telefone</label>
                                <input type="tel" name="phone"
                                       value="<?= htmlspecialchars($activity['phone'] ?? '') ?>"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all"
                                       placeholder="+351 XXX XXX XXX">
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                                <input type="email" name="email"
                                       value="<?= htmlspecialchars($activity['email'] ?? '') ?>"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all"
                                       placeholder="email@exemplo.com">
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <!-- Website -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Website Oficial</label>
                                <input type="url" name="website"
                                       value="<?= htmlspecialchars($activity['website'] ?? '') ?>"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all"
                                       placeholder="https://...">
                            </div>

                            <!-- External URL -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Link Externo (mais info)</label>
                                <input type="url" name="external_url"
                                       value="<?= htmlspecialchars($activity['external_url'] ?? '') ?>"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all"
                                       placeholder="https://...">
                            </div>
                        </div>

                        <div class="grid md:grid-cols-3 gap-4">
                            <!-- Latitude -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Latitude</label>
                                <input type="text" name="latitude"
                                       value="<?= htmlspecialchars($activity['latitude'] ?? '') ?>"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all"
                                       placeholder="41.34217">
                            </div>

                            <!-- Longitude -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Longitude</label>
                                <input type="text" name="longitude"
                                       value="<?= htmlspecialchars($activity['longitude'] ?? '') ?>"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all"
                                       placeholder="-6.71347">
                            </div>

                            <!-- Distance -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Distância de Mogadouro (km)</label>
                                <input type="number" name="distance_km" step="0.1" min="0"
                                       value="<?= htmlspecialchars($activity['distance_km'] ?? '') ?>"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all"
                                       placeholder="0.0">
                            </div>
                        </div>

                        <!-- Google Maps Embed -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Google Maps Embed (iframe)</label>
                            <textarea name="google_maps_embed"
                                      rows="3"
                                      class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all resize-none font-mono text-sm"
                                      placeholder='<iframe src="https://www.google.com/maps/embed?..." ...></iframe>'><?= htmlspecialchars($activity['google_maps_embed'] ?? '') ?></textarea>
                            <p class="text-xs text-gray-500 mt-1">Cole o código iframe do Google Maps. Se vazio, será usado Leaflet com as coordenadas.</p>
                        </div>
                    </div>
                </div>

                <!-- Images Card -->
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="font-bold text-gray-800">Imagens</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Cover Image with Preview -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Imagem de Capa</label>
                            <div class="flex items-start gap-4">
                                <div id="cover-preview" class="w-32 h-24 bg-gray-100 rounded-xl overflow-hidden flex items-center justify-center border-2 border-dashed border-gray-300">
                                    <?php if ($currentCoverImage): ?>
                                    <img loading="lazy" decoding="async" src="<?= basePath() ?><?= htmlspecialchars($currentCoverImage['file_path']) ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1">
                                    <input type="file"
                                           name="cover_image"
                                           id="cover_image"
                                           accept="image/jpeg,image/png,image/webp"
                                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary/10 file:text-primary file:font-medium"
                                           onchange="previewImage(this, 'cover-preview')">
                                    <p class="text-xs text-gray-500 mt-1">JPG, PNG ou WebP. Máx. 5MB. Recomendado: 1200x800px</p>
                                    <?php if ($currentCoverImage): ?>
                                    <p class="text-xs text-gray-600 mt-1">Atual: <?= htmlspecialchars($currentCoverImage['original_name']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Current Gallery Images -->
                        <?php if (!empty($galleryImages)): ?>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Galeria Atual (<?= count($galleryImages) ?> imagens)</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                <?php foreach ($galleryImages as $img): ?>
                                <div class="relative group bg-white border border-gray-200 rounded-lg overflow-hidden hover:border-red-300 transition-colors">
                                    <div class="aspect-video bg-gray-100">
                                        <img loading="lazy" decoding="async" src="<?= basePath() ?><?= htmlspecialchars($img['file_path']) ?>"
                                             alt="<?= htmlspecialchars($img['alt_text_pt'] ?? 'Gallery image') ?>"
                                             class="w-full h-full object-cover">
                                    </div>
                                    <button type="button"
                                            onclick="confirmDeleteGalleryImage(<?= $img['id'] ?>, '<?= addslashes($img['original_name']) ?>')"
                                            class="delete-btn absolute top-2 right-2 p-2 bg-red-500 text-white rounded-lg opacity-0 group-hover:opacity-100 transition-all hover:bg-red-600 shadow-lg"
                                            title="Eliminar imagem"
                                            data-image-id="<?= $img['id'] ?>">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Add New Gallery Images -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Adicionar Novas Imagens à Galeria</label>

                            <div id="galleryDropZone" class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-secondary hover:bg-secondary/5 transition-all cursor-pointer">
                                <input type="file"
                                       name="gallery[]"
                                       id="galleryInput"
                                       multiple
                                       accept="image/jpeg,image/png,image/webp"
                                       class="hidden">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <p class="text-sm font-medium text-gray-700 mb-1">Clique ou arraste imagens para aqui</p>
                                <p class="text-xs text-gray-500">JPG, PNG ou WebP - Máx. 5MB cada</p>
                            </div>

                            <div id="galleryPreviewArea" class="hidden mt-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                                <div class="flex items-center justify-between mb-3">
                                    <p class="font-semibold text-gray-700">Imagens selecionadas:</p>
                                    <button type="button" id="clearGalleryFiles" class="text-sm text-red-600 hover:text-red-700 font-medium">Limpar</button>
                                </div>
                                <div id="galleryPreviewList" class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3"></div>
                                <div id="galleryPreviewTotal" class="mt-3 pt-3 border-t border-gray-300 text-sm font-medium text-gray-700"></div>
                            </div>
                        </div>

                        <!-- Hidden input for images to delete -->
                        <input type="hidden" name="delete_images_list" id="delete_images_list" value="">
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Publish Card -->
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="font-bold text-gray-800">Publicar</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-gray-700">Ativo</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" <?= $activity['is_active'] ? 'checked' : '' ?> class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-gray-700">Destaque</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_featured" value="1" <?= $activity['is_featured'] ? 'checked' : '' ?> class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-500/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-500"></div>
                            </label>
                        </div>

                        <hr class="border-gray-200">

                        <button type="submit"
                                class="w-full bg-primary text-white py-3 rounded-xl hover:bg-primary/90 transition-colors font-semibold">
                            Guardar Alterações
                        </button>
                    </div>
                </div>

                <!-- Category Card -->
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="font-bold text-gray-800">Categoria</h2>
                    </div>
                    <div class="p-6">
                        <select name="category_id" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all">
                            <option value="">Selecionar...</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $activity['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Price Range Card -->
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="font-bold text-gray-800">Faixa de Preço</h2>
                    </div>
                    <div class="p-6">
                        <select name="price_range"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all">
                            <?php foreach ($priceRanges as $key => $label): ?>
                            <option value="<?= $key ?>" <?= $activity['price_range'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Sort Order Card -->
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="font-bold text-gray-800">Ordem</h2>
                    </div>
                    <div class="p-6">
                        <input type="number" name="sort_order" value="<?= $activity['sort_order'] ?>" min="0"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all">
                        <p class="text-xs text-gray-500 mt-1">Menor número = aparece primeiro</p>
                    </div>
                </div>

                <!-- Slug Card -->
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="font-bold text-gray-800">URL (Slug)</h2>
                    </div>
                    <div class="p-6">
                        <input type="text" name="slug"
                               value="<?= htmlspecialchars($activity['slug']) ?>"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all"
                               placeholder="Auto-gerado se vazio">
                        <p class="text-xs text-gray-500 mt-1">Atual: /atividades?slug=<?= htmlspecialchars($activity['slug']) ?></p>
                    </div>
                </div>

                <!-- Stats Card -->
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="font-bold text-gray-800">Estatísticas</h2>
                    </div>
                    <div class="p-6 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Visualizações</span>
                            <span class="font-semibold text-gray-800"><?= number_format($activity['views_count'] ?? 0) ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Criado</span>
                            <span class="font-semibold text-gray-800"><?= date('d/m/Y', strtotime($activity['created_at'])) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Language tabs
function switchLangTab(lang) {
    document.querySelectorAll('.lang-tab').forEach(tab => {
        if (tab.dataset.lang === lang) {
            tab.classList.remove('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
            tab.classList.add('bg-primary', 'text-white');
        } else {
            tab.classList.remove('bg-primary', 'text-white');
            tab.classList.add('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
        }
    });

    document.querySelectorAll('.lang-content').forEach(content => {
        if (content.dataset.lang === lang) {
            content.classList.remove('hidden');
        } else {
            content.classList.add('hidden');
        }
    });
}

// Image preview for cover image
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img loading="lazy" decoding="async" src="' + e.target.result + '" class="w-full h-full object-cover">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Gallery deletion - track images to delete
let imagesToDelete = [];
let currentDeleteCard = null;

function confirmDeleteGalleryImage(imageId, imageName) {
    const modal = document.getElementById('deleteImageModal');
    const imageNameEl = document.getElementById('deleteImageName');
    const confirmBtn = document.getElementById('confirmDeleteBtn');

    // Store reference to the card that triggered the delete
    currentDeleteCard = event.target.closest('.relative.group');

    imageNameEl.textContent = imageName;
    modal.classList.remove('hidden');

    confirmBtn.onclick = function() {
        // Add to delete list if not already there
        if (!imagesToDelete.includes(imageId)) {
            imagesToDelete.push(imageId);
            document.getElementById('delete_images_list').value = imagesToDelete.join(',');
        }

        // REMOVE the card completely from view with animation
        if (currentDeleteCard) {
            currentDeleteCard.style.transition = 'all 0.3s ease';
            currentDeleteCard.style.opacity = '0';
            currentDeleteCard.style.transform = 'scale(0.8)';

            setTimeout(() => {
                if (currentDeleteCard && currentDeleteCard.parentNode) {
                    // Store data for undo
                    currentDeleteCard.dataset.deletedImageId = imageId;
                    currentDeleteCard.style.display = 'none';

                    // Update gallery count
                    updateGalleryCount();
                }
            }, 300);
        }

        closeDeleteModal();
        currentDeleteCard = null;
    };
}

function undoDeleteGalleryImage(imageId) {
    // Remove from delete list
    const index = imagesToDelete.indexOf(imageId);
    if (index > -1) {
        imagesToDelete.splice(index, 1);
        document.getElementById('delete_images_list').value = imagesToDelete.join(',');
    }

    // Find the card and restore it
    const cards = document.querySelectorAll('[data-deleted-image-id="' + imageId + '"]');
    cards.forEach(card => {
        card.style.display = '';
        card.style.opacity = '0';
        card.style.transform = 'scale(0.8)';

        setTimeout(() => {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '1';
            card.style.transform = 'scale(1)';
            delete card.dataset.deletedImageId;
        }, 50);

        updateGalleryCount();
    });
}

function updateGalleryCount() {
    const container = document.querySelector('.grid.grid-cols-2');
    if (container) {
        const visibleImages = container.querySelectorAll('.relative.group[style*="display: none"]').length;
        const totalImages = container.querySelectorAll('.relative.group').length;
        const activeImages = totalImages - visibleImages;

        const label = container.closest('div').querySelector('label');
        if (label && activeImages > 0) {
            label.innerHTML = `Galeria Atual (${activeImages} imagens)`;
        }
    }
}

function closeDeleteModal() {
    document.getElementById('deleteImageModal').classList.add('hidden');
}

// Gallery file input and drag & drop
const galleryInput = document.getElementById('galleryInput');
const galleryDropZone = document.getElementById('galleryDropZone');
const galleryPreviewArea = document.getElementById('galleryPreviewArea');
const galleryPreviewList = document.getElementById('galleryPreviewList');
const galleryPreviewTotal = document.getElementById('galleryPreviewTotal');
const clearGalleryBtn = document.getElementById('clearGalleryFiles');

// Click to select files
galleryDropZone.addEventListener('click', () => galleryInput.click());

// File input change
galleryInput.addEventListener('change', handleGalleryFiles);

// Drag & drop handlers
galleryDropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    galleryDropZone.classList.add('border-secondary', 'bg-secondary/10');
});

galleryDropZone.addEventListener('dragleave', () => {
    galleryDropZone.classList.remove('border-secondary', 'bg-secondary/10');
});

galleryDropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    galleryDropZone.classList.remove('border-secondary', 'bg-secondary/10');

    const files = Array.from(e.dataTransfer.files).filter(file =>
        file.type === 'image/jpeg' || file.type === 'image/png' || file.type === 'image/webp'
    );

    if (files.length > 0) {
        const dataTransfer = new DataTransfer();
        files.forEach(file => dataTransfer.items.add(file));
        galleryInput.files = dataTransfer.files;
        handleGalleryFiles();
    }
});

// Clear gallery files
clearGalleryBtn.addEventListener('click', () => {
    galleryInput.value = '';
    galleryPreviewArea.classList.add('hidden');
    galleryPreviewList.innerHTML = '';
});

// Handle gallery files preview
function handleGalleryFiles() {
    const files = Array.from(galleryInput.files);

    if (files.length === 0) {
        galleryPreviewArea.classList.add('hidden');
        return;
    }

    galleryPreviewArea.classList.remove('hidden');
    galleryPreviewList.innerHTML = '';

    let totalSize = 0;

    files.forEach((file, index) => {
        totalSize += file.size;

        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.createElement('div');
            preview.className = 'relative bg-gray-100 rounded-lg overflow-hidden aspect-square border border-gray-200';
            preview.innerHTML = `
                <img loading="lazy" decoding="async" src="${e.target.result}" class="w-full h-full object-cover">
                <div class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-[10px] px-1 py-0.5 truncate">
                    ${file.name}
                </div>
            `;
            galleryPreviewList.appendChild(preview);
        };
        reader.readAsDataURL(file);
    });

    const totalMB = (totalSize / (1024 * 1024)).toFixed(2);
    galleryPreviewTotal.textContent = `${files.length} imagens selecionadas (${totalMB} MB)`;
}
</script>

<!-- Delete Image Confirmation Modal -->
<div id="deleteImageModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <div class="flex items-start gap-4 mb-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-gray-900 mb-1">Eliminar Imagem</h3>
                <p class="text-sm text-gray-600">Tem a certeza que deseja eliminar a imagem <strong id="deleteImageName" class="text-gray-900"></strong>?</p>
                <p class="text-xs text-red-600 mt-2">Esta ação não pode ser desfeita.</p>
            </div>
        </div>
        <div class="flex gap-3 justify-end">
            <button type="button"
                    onclick="closeDeleteModal()"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                Cancelar
            </button>
            <button type="button"
                    id="confirmDeleteBtn"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                Eliminar
            </button>
        </div>
    </div>
</div>

<?php include dirname(dirname(__DIR__)) . '/includes/footer.php'; ?>
