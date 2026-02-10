<?php
/**
 * A Casa do Gi - Admin Media Manager
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;

$db = Database::getInstance();

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        if (isset($_FILES['files']) && !empty($_FILES['files']['name'][0])) {
            $uploadDir = ROOT_PATH . '/uploads/media/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            $uploaded = 0;
            $errors = [];

            foreach ($_FILES['files']['tmp_name'] as $key => $tmpName) {
                $fileName = $_FILES['files']['name'][$key];
                $fileSize = $_FILES['files']['size'][$key];
                $fileError = $_FILES['files']['error'][$key];

                if ($fileError !== UPLOAD_ERR_OK) {
                    $errors[] = "Erro ao carregar: {$fileName}";
                    continue;
                }

                if ($fileSize > $maxSize) {
                    $errors[] = "Ficheiro muito grande: {$fileName}";
                    continue;
                }

                // Validate extension from original filename
                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowedExtensions)) {
                    $errors[] = "Extensão não permitida: {$fileName}";
                    continue;
                }

                // Validate REAL MIME type using file contents (not client-supplied header)
                $realMimeType = mime_content_type($tmpName);
                if (!in_array($realMimeType, $allowedMimeTypes)) {
                    $errors[] = "Tipo de ficheiro inválido: {$fileName} ({$realMimeType})";
                    continue;
                }

                // Double-check with getimagesize (ensures it's actually an image)
                $imageInfo = @getimagesize($tmpName);
                if ($imageInfo === false) {
                    $errors[] = "Ficheiro não é uma imagem válida: {$fileName}";
                    continue;
                }

                // Use MIME-derived extension to prevent extension spoofing
                $mimeToExt = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif',
                    'image/webp' => 'webp',
                ];
                $safeExt = $mimeToExt[$realMimeType] ?? $ext;

                // Generate unique filename with safe extension
                $newName = bin2hex(random_bytes(16)) . '.' . $safeExt;
                $targetPath = $uploadDir . $newName;
                $fileType = $realMimeType;

                if (move_uploaded_file($tmpName, $targetPath)) {
                    // Save to database (using correct column names from schema)
                    $db->insert('media', [
                        'filename' => $newName,
                        'original_name' => $fileName,
                        'file_path' => '/uploads/media/' . $newName,
                        'file_type' => $fileType,
                        'file_size' => $fileSize,
                        'category' => 'other',
                        'uploaded_by' => $_SESSION['admin_id'] ?? null
                    ]);
                    $uploaded++;
                } else {
                    $errors[] = "Falha ao mover: {$fileName}";
                }
            }

            if ($uploaded > 0) {
                Session::flash('success', "{$uploaded} ficheiro(s) carregado(s) com sucesso.");
            }
            if (!empty($errors)) {
                Session::flash('error', implode('<br>', $errors));
            }
            redirect('/admin/media/');
        }
    }
}

// Handle delete
if (isset($_GET['delete']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $id = (int)$_GET['delete'];
        $media = $db->fetch("SELECT * FROM media WHERE id = ?", [$id]);

        if ($media) {
            // Delete file
            $filePath = ROOT_PATH . $media['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete from database
            $db->delete('media', 'id = ?', [$id]);
            Session::flash('success', 'Ficheiro eliminado com sucesso.');
        }
    }
    redirect('/admin/media/');
}

// Handle edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_media'])) {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $id = (int)$_POST['media_id'];
        $altPt = sanitize($_POST['alt_text_pt'] ?? '');
        $altEn = sanitize($_POST['alt_text_en'] ?? '');
        $category = $_POST['category'] ?? 'other';

        $validCategories = ['gallery', 'products', 'activities', 'content', 'other'];
        if (!in_array($category, $validCategories)) {
            $category = 'other';
        }

        $db->update('media', [
            'alt_text_pt' => $altPt,
            'alt_text_en' => $altEn,
            'category' => $category
        ], 'id = ?', [$id]);

        Session::flash('success', 'Imagem atualizada com sucesso.');
    }
    redirect('/admin/media/');
}

// Filters
$type = isset($_GET['type']) ? $_GET['type'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 24;
$offset = ($page - 1) * $perPage;

// Build query
$where = "WHERE 1=1";
$params = [];

if ($type !== 'all') {
    $where .= " AND file_type LIKE ?";
    $params[] = $type . '%';
}

if ($search) {
    $where .= " AND (original_name LIKE ? OR filename LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

// Get total
$total = $db->fetch("SELECT COUNT(*) as c FROM media {$where}", $params)['c'];
$totalPages = ceil($total / $perPage);

// Get media
$media = $db->fetchAll(
    "SELECT * FROM media {$where} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}",
    $params
);

// Format file size
function formatFileSize($bytes) {
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 1) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 1) . ' KB';
    }
    return $bytes . ' B';
}

$pageTitle = 'Media';
$currentPage = 'media';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-primary">Biblioteca de Media</h1>
        <p class="text-gray-600"><?= $total ?> ficheiro(s)</p>
    </div>
    <button onclick="document.getElementById('uploadModal').classList.remove('hidden')"
            class="px-4 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
        </svg>
        Carregar Ficheiros
    </button>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm mb-6 p-4">
    <form action="" method="get" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Pesquisar</label>
            <input type="text" name="search" value="<?= e($search) ?>"
                   placeholder="Nome do ficheiro..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
            <select name="type" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                <option value="all" <?= $type === 'all' ? 'selected' : '' ?>>Todos</option>
                <option value="image/jpeg" <?= $type === 'image/jpeg' ? 'selected' : '' ?>>JPEG</option>
                <option value="image/png" <?= $type === 'image/png' ? 'selected' : '' ?>>PNG</option>
                <option value="image/gif" <?= $type === 'image/gif' ? 'selected' : '' ?>>GIF</option>
                <option value="image/webp" <?= $type === 'image/webp' ? 'selected' : '' ?>>WebP</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
            Filtrar
        </button>
        <?php if ($search || $type !== 'all'): ?>
        <a href="<?= basePath() ?>/admin/media/" class="px-4 py-2 text-gray-500 hover:text-gray-700">Limpar</a>
        <?php endif; ?>
    </form>
</div>

<!-- Media Grid -->
<div class="bg-white rounded-lg shadow-sm">
    <?php if (empty($media)): ?>
    <div class="p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <h3 class="text-lg font-medium text-gray-700 mb-2">Sem ficheiros</h3>
        <p class="text-gray-500 mb-4">Carregue imagens para começar.</p>
        <button onclick="document.getElementById('uploadModal').classList.remove('hidden')"
                class="px-4 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700">
            Carregar Ficheiros
        </button>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 p-4">
        <?php foreach ($media as $item):
            // Prepare data for JavaScript (JSON encode to handle special chars)
            $itemData = json_encode([
                'id' => $item['id'],
                'name' => $item['original_name'],
                'altPt' => $item['alt_text_pt'] ?? '',
                'altEn' => $item['alt_text_en'] ?? '',
                'category' => $item['category'] ?? 'other'
            ], JSON_HEX_APOS | JSON_HEX_QUOT);
        ?>
        <div class="media-card bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200 hover:shadow-md transition-shadow">
            <!-- Image -->
            <div class="aspect-square bg-gray-100 relative group">
                <img src="<?= basePath() . e($item['file_path']) ?>"
                     alt="<?= e($item['original_name']) ?>"
                     class="w-full h-full object-cover">
                <!-- Quick actions overlay -->
                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <a href="<?= basePath() . e($item['file_path']) ?>" target="_blank"
                       class="p-2 bg-white rounded-full text-gray-700 hover:bg-gray-100 mx-1" title="Ver tamanho original">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                </div>
            </div>
            <!-- Info & Actions -->
            <div class="p-3">
                <p class="text-sm font-medium text-gray-800 truncate mb-1" title="<?= e($item['original_name']) ?>">
                    <?= e($item['original_name']) ?>
                </p>
                <p class="text-xs text-gray-500 mb-3">
                    <?= formatFileSize($item['file_size']) ?>
                    <?php if ($item['category'] && $item['category'] !== 'other'): ?>
                    <span class="ml-2 px-1.5 py-0.5 bg-secondary-100 text-secondary-700 rounded text-xs"><?= e($item['category']) ?></span>
                    <?php endif; ?>
                </p>
                <div class="flex gap-2">
                    <button type="button"
                            data-edit='<?= $itemData ?>'
                            class="btn-edit flex-1 px-3 py-1.5 text-xs font-medium bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">
                        Editar
                    </button>
                    <button type="button"
                            data-delete-id="<?= $item['id'] ?>"
                            data-delete-name="<?= e($item['original_name']) ?>"
                            class="btn-delete px-3 py-1.5 text-xs font-medium bg-red-100 text-red-600 rounded hover:bg-red-200 transition-colors">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

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

<!-- Upload Modal -->
<div id="uploadModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg">
        <!-- Header -->
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Carregar Imagens</h3>
            <button type="button" id="uploadModalClose" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Form -->
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
            <input type="hidden" name="upload" value="1">

            <!-- Content -->
            <div class="p-6">
                <!-- Drop Zone -->
                <div id="dropZone" class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-secondary-500 hover:bg-secondary-50 transition-all cursor-pointer"
                     onclick="document.getElementById('fileInputUpload').click()">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p class="text-gray-700 mb-2">Arraste imagens aqui ou clique para selecionar</p>
                    <p class="text-sm text-gray-400">JPEG, PNG, GIF, WebP - Máximo 5MB por ficheiro</p>
                    <input type="file" id="fileInputUpload" name="files[]" multiple accept="image/jpeg,image/png,image/gif,image/webp" required class="hidden">
                </div>

                <!-- Preview -->
                <div id="uploadPreviewArea" class="hidden mt-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="font-medium text-gray-700">Ficheiros selecionados:</p>
                        <button type="button" onclick="clearUploadFiles()" class="text-sm text-red-500 hover:text-red-700">Limpar</button>
                    </div>
                    <div id="uploadPreviewList" class="grid grid-cols-3 gap-3 max-h-64 overflow-y-auto"></div>
                    <div id="uploadPreviewTotal" class="mt-3 pt-3 border-t border-gray-200 text-sm font-medium text-gray-700"></div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl flex items-center justify-end gap-3">
                <button type="submit"
                        class="px-6 py-2.5 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700 font-semibold transition-colors shadow-sm">
                    Carregar Imagens
                </button>
                <button type="button" id="uploadModalCancel"
                        class="px-5 py-2.5 text-gray-600 hover:text-gray-800 font-medium transition-colors">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-800">Editar Imagem</h3>
            <button type="button" id="editModalClose" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="" method="post" class="p-6">
            <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
            <input type="hidden" name="edit_media" value="1">
            <input type="hidden" name="media_id" id="editMediaId" value="">

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome do ficheiro</label>
                    <p id="editFileName" class="text-sm text-gray-600 bg-gray-50 px-3 py-2 rounded"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Texto alternativo (PT)</label>
                    <input type="text" name="alt_text_pt" id="editAltPt"
                           placeholder="Descrição da imagem em português..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Texto alternativo (EN)</label>
                    <input type="text" name="alt_text_en" id="editAltEn"
                           placeholder="Image description in English..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                    <select name="category" id="editCategory"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500 focus:border-secondary-500">
                        <option value="other">Outro</option>
                        <option value="gallery">Galeria (Alojamento)</option>
                        <option value="products">Produtos</option>
                        <option value="activities">Atividades</option>
                        <option value="content">Conteúdo</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="submit" class="px-6 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700 font-medium">
                    Guardar
                </button>
                <button type="button" id="editModalCancel"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4" onclick="event.stopPropagation()">
        <div class="p-6 text-center">
            <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Eliminar Imagem</h3>
            <p class="text-gray-600 mb-1">Tem a certeza que deseja eliminar:</p>
            <p id="deleteFileName" class="font-medium text-gray-800 mb-6 break-all"></p>
            <div class="flex gap-3 justify-center">
                <a id="deleteConfirmBtn" href="#"
                   class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors">
                    Eliminar
                </a>
                <button type="button" id="deleteCancelBtn"
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

    // DOM Elements
    const fileInput = document.getElementById('fileInputUpload');
    const previewArea = document.getElementById('uploadPreviewArea');
    const previewList = document.getElementById('uploadPreviewList');
    const dropZone = document.getElementById('dropZone');
    const uploadModal = document.getElementById('uploadModal');
    const editModal = document.getElementById('editModal');
    const deleteModal = document.getElementById('deleteModal');
    const csrfToken = '<?= CSRF::getToken() ?>';

    // =====================
    // UTILITY FUNCTIONS
    // =====================
    function formatSize(bytes) {
        if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
        if (bytes >= 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return bytes + ' B';
    }

    // =====================
    // UPLOAD MODAL
    // =====================
    const previewTotal = document.getElementById('uploadPreviewTotal');

    function updatePreview(files) {
        if (!files || files.length === 0) {
            previewArea.classList.add('hidden');
            previewList.innerHTML = '';
            previewTotal.innerHTML = '';
            return;
        }

        previewArea.classList.remove('hidden');
        previewList.innerHTML = '';

        let totalSize = 0;
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            totalSize += file.size;

            // Create thumbnail container
            const item = document.createElement('div');
            item.className = 'relative bg-gray-100 rounded-lg overflow-hidden aspect-square';

            // Create image element
            const img = document.createElement('img');
            img.className = 'w-full h-full object-cover';
            img.alt = file.name;

            // Create file name overlay
            const overlay = document.createElement('div');
            overlay.className = 'absolute bottom-0 left-0 right-0 bg-black/60 px-2 py-1';
            overlay.innerHTML = '<p class="text-white text-xs truncate">' + file.name + '</p>' +
                               '<p class="text-gray-300 text-xs">' + formatSize(file.size) + '</p>';

            // Read file and create thumbnail
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);

            item.appendChild(img);
            item.appendChild(overlay);
            previewList.appendChild(item);
        }

        // Update total
        previewTotal.textContent = 'Total: ' + files.length + ' ficheiro(s) - ' + formatSize(totalSize);
    }

    function clearUploadFiles() {
        fileInput.value = '';
        previewArea.classList.add('hidden');
        previewList.innerHTML = '';
        previewTotal.innerHTML = '';
    }

    function closeUploadModal() {
        uploadModal.classList.add('hidden');
        clearUploadFiles();
    }

    // File input change
    fileInput.addEventListener('change', function(e) {
        updatePreview(e.target.files);
    });

    // Drag and drop
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.add('border-secondary-500', 'bg-secondary-50');
    });

    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('border-secondary-500', 'bg-secondary-50');
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('border-secondary-500', 'bg-secondary-50');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const dt = new DataTransfer();
            for (let i = 0; i < files.length; i++) {
                dt.items.add(files[i]);
            }
            fileInput.files = dt.files;
            updatePreview(files);
        }
    });

    // Upload modal close handlers
    document.getElementById('uploadModalClose').addEventListener('click', closeUploadModal);
    document.getElementById('uploadModalCancel').addEventListener('click', closeUploadModal);
    uploadModal.addEventListener('click', function(e) {
        if (e.target === this) closeUploadModal();
    });

    // =====================
    // EDIT MODAL
    // =====================
    function openEditModal(data) {
        document.getElementById('editMediaId').value = data.id;
        document.getElementById('editFileName').textContent = data.name;
        document.getElementById('editAltPt').value = data.altPt || '';
        document.getElementById('editAltEn').value = data.altEn || '';
        document.getElementById('editCategory').value = data.category || 'other';
        editModal.classList.remove('hidden');
    }

    function closeEditModal() {
        editModal.classList.add('hidden');
    }

    // Edit modal close handlers
    document.getElementById('editModalClose').addEventListener('click', closeEditModal);
    document.getElementById('editModalCancel').addEventListener('click', closeEditModal);
    editModal.addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });

    // =====================
    // DELETE MODAL
    // =====================
    function openDeleteModal(id, name) {
        document.getElementById('deleteFileName').textContent = name;
        document.getElementById('deleteConfirmBtn').href = '?delete=' + id + '&token=' + csrfToken;
        deleteModal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        deleteModal.classList.add('hidden');
    }

    document.getElementById('deleteCancelBtn').addEventListener('click', closeDeleteModal);
    deleteModal.addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });

    // =====================
    // BUTTON EVENT DELEGATION
    // =====================
    document.addEventListener('click', function(e) {
        // Edit button clicked
        const editBtn = e.target.closest('.btn-edit');
        if (editBtn) {
            e.preventDefault();
            e.stopPropagation();
            try {
                const data = JSON.parse(editBtn.getAttribute('data-edit'));
                openEditModal(data);
            } catch (err) {
                console.error('Error parsing edit data:', err);
            }
            return;
        }

        // Delete button clicked
        const deleteBtn = e.target.closest('.btn-delete');
        if (deleteBtn) {
            e.preventDefault();
            e.stopPropagation();
            const id = deleteBtn.getAttribute('data-delete-id');
            const name = deleteBtn.getAttribute('data-delete-name');
            openDeleteModal(id, name);
            return;
        }
    });

    // =====================
    // KEYBOARD NAVIGATION
    // =====================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!uploadModal.classList.contains('hidden')) closeUploadModal();
            if (!editModal.classList.contains('hidden')) closeEditModal();
            if (!deleteModal.classList.contains('hidden')) closeDeleteModal();
        }
    });

    // =====================
    // GLOBAL FUNCTIONS (for onclick handlers in HTML)
    // =====================
    window.clearUploadFiles = clearUploadFiles;

})();
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
