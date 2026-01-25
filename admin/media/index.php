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

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            $uploaded = 0;
            $errors = [];

            foreach ($_FILES['files']['tmp_name'] as $key => $tmpName) {
                $fileName = $_FILES['files']['name'][$key];
                $fileSize = $_FILES['files']['size'][$key];
                $fileType = $_FILES['files']['type'][$key];
                $fileError = $_FILES['files']['error'][$key];

                if ($fileError !== UPLOAD_ERR_OK) {
                    $errors[] = "Erro ao carregar: {$fileName}";
                    continue;
                }

                if (!in_array($fileType, $allowedTypes)) {
                    $errors[] = "Tipo nao permitido: {$fileName}";
                    continue;
                }

                if ($fileSize > $maxSize) {
                    $errors[] = "Ficheiro muito grande: {$fileName}";
                    continue;
                }

                // Generate unique filename
                $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                $newName = uniqid() . '_' . time() . '.' . $ext;
                $targetPath = $uploadDir . $newName;

                if (move_uploaded_file($tmpName, $targetPath)) {
                    // Get image dimensions
                    $imageInfo = getimagesize($targetPath);
                    $width = $imageInfo[0] ?? 0;
                    $height = $imageInfo[1] ?? 0;

                    // Save to database
                    $db->insert('media', [
                        'file_name' => $newName,
                        'original_name' => $fileName,
                        'file_path' => '/uploads/media/' . $newName,
                        'file_type' => $fileType,
                        'file_size' => $fileSize,
                        'width' => $width,
                        'height' => $height,
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
            Session::flash('success', 'Ficheiro eliminado.');
        }
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
    $where .= " AND (original_name LIKE ? OR file_name LIKE ?)";
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
        <h1 class="text-2xl font-bold text-gray-800">Biblioteca de Media</h1>
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
        <p class="text-gray-500 mb-4">Carregue imagens para comecar.</p>
        <button onclick="document.getElementById('uploadModal').classList.remove('hidden')"
                class="px-4 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700">
            Carregar Ficheiros
        </button>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 p-4">
        <?php foreach ($media as $item): ?>
        <div class="group relative bg-gray-100 rounded-lg overflow-hidden aspect-square">
            <img src="<?= basePath() . e($item['file_path']) ?>"
                 alt="<?= e($item['original_name']) ?>"
                 class="w-full h-full object-cover">

            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-2 p-2">
                <div class="flex gap-2">
                    <button onclick="copyToClipboard('<?= e($item['file_path']) ?>')"
                            class="p-2 bg-white rounded-lg text-gray-700 hover:bg-gray-100" title="Copiar URL">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                        </svg>
                    </button>
                    <a href="?delete=<?= $item['id'] ?>&token=<?= CSRF::getToken() ?>"
                       onclick="return confirm('Eliminar este ficheiro?')"
                       class="p-2 bg-red-500 rounded-lg text-white hover:bg-red-600" title="Eliminar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </a>
                </div>
                <p class="text-white text-xs text-center truncate w-full px-2"><?= e($item['original_name']) ?></p>
                <p class="text-gray-300 text-xs"><?= formatFileSize($item['file_size']) ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

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

<!-- Upload Modal -->
<div id="uploadModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] flex flex-col">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-800">Carregar Ficheiros</h3>
            <button onclick="closeUploadModal()"
                    class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="" method="post" enctype="multipart/form-data" id="uploadForm" class="flex flex-col flex-1 overflow-hidden">
            <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
            <input type="hidden" name="upload" value="1">

            <div class="p-6 flex-1 overflow-y-auto">
                <!-- Drop Zone -->
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-secondary-500 transition-colors cursor-pointer"
                     id="dropZone" onclick="document.getElementById('fileInput').click()">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p class="text-gray-600 mb-2 text-lg">Arraste imagens aqui</p>
                    <p class="text-gray-400 mb-4">ou</p>
                    <span class="inline-block px-6 py-3 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700 font-medium">
                        Escolher do Computador
                    </span>
                    <input type="file" name="files[]" multiple accept="image/jpeg,image/png,image/gif,image/webp" class="hidden" id="fileInput">
                    <p class="text-xs text-gray-400 mt-4">JPEG, PNG, GIF, WebP - Maximo 5MB por ficheiro</p>
                </div>

                <!-- Selected Files Preview -->
                <div id="previewSection" class="hidden mt-6">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-medium text-gray-700">
                            <span id="fileCount">0</span> ficheiro(s) selecionado(s)
                        </h4>
                        <button type="button" onclick="clearFiles()" class="text-sm text-red-500 hover:text-red-700">
                            Limpar tudo
                        </button>
                    </div>
                    <div id="previewGrid" class="grid grid-cols-3 sm:grid-cols-4 gap-3 max-h-64 overflow-y-auto p-1">
                        <!-- Previews will be inserted here -->
                    </div>
                </div>

                <!-- Upload Progress -->
                <div id="uploadProgress" class="hidden mt-6">
                    <div class="flex items-center gap-3 mb-2">
                        <svg class="animate-spin w-5 h-5 text-secondary-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-gray-700 font-medium">A carregar ficheiros...</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div id="progressBar" class="bg-secondary-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <p id="progressText" class="text-sm text-gray-500 mt-1">Aguarde...</p>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-between items-center">
                <p id="totalSize" class="text-sm text-gray-500"></p>
                <div class="flex gap-3">
                    <button type="button" onclick="closeUploadModal()"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">
                        Cancelar
                    </button>
                    <button type="submit" id="submitBtn" disabled
                            class="px-6 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700 font-medium disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <span id="submitText">Carregar</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(window.location.origin + text);
    // Show toast instead of alert
    showToast('URL copiado para a area de transferencia!');
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Upload functionality
const fileInput = document.getElementById('fileInput');
const dropZone = document.getElementById('dropZone');
const previewSection = document.getElementById('previewSection');
const previewGrid = document.getElementById('previewGrid');
const fileCount = document.getElementById('fileCount');
const totalSize = document.getElementById('totalSize');
const submitBtn = document.getElementById('submitBtn');
const uploadForm = document.getElementById('uploadForm');
const uploadProgress = document.getElementById('uploadProgress');
const progressBar = document.getElementById('progressBar');
const progressText = document.getElementById('progressText');
const submitText = document.getElementById('submitText');

let selectedFiles = [];

fileInput.addEventListener('change', handleFileSelect);

function handleFileSelect(e) {
    const files = e.target.files || e.dataTransfer.files;
    addFiles(files);
}

function addFiles(files) {
    const maxSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    for (let file of files) {
        // Validate
        if (!allowedTypes.includes(file.type)) {
            showToast(`Tipo nao permitido: ${file.name}`, 'error');
            continue;
        }
        if (file.size > maxSize) {
            showToast(`Ficheiro muito grande (max 5MB): ${file.name}`, 'error');
            continue;
        }
        // Check if already added
        if (selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
            continue;
        }
        selectedFiles.push(file);
    }

    updatePreview();
}

function updatePreview() {
    if (selectedFiles.length === 0) {
        previewSection.classList.add('hidden');
        submitBtn.disabled = true;
        totalSize.textContent = '';
        return;
    }

    previewSection.classList.remove('hidden');
    submitBtn.disabled = false;
    previewGrid.innerHTML = '';

    let total = 0;
    selectedFiles.forEach((file, index) => {
        total += file.size;

        const div = document.createElement('div');
        div.className = 'relative aspect-square bg-gray-100 rounded-lg overflow-hidden group';

        // Create thumbnail
        const reader = new FileReader();
        reader.onload = function(e) {
            div.innerHTML = `
                <img src="${e.target.result}" class="w-full h-full object-cover" alt="${file.name}">
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <button type="button" onclick="removeFile(${index})" class="p-2 bg-red-500 rounded-full text-white hover:bg-red-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-xs p-1 truncate">
                    ${file.name}
                </div>
            `;
        };
        reader.readAsDataURL(file);

        previewGrid.appendChild(div);
    });

    fileCount.textContent = selectedFiles.length;
    totalSize.textContent = `Tamanho total: ${formatBytes(total)}`;
    submitText.textContent = `Carregar ${selectedFiles.length} ficheiro(s)`;

    // Update the actual file input
    updateFileInput();
}

function updateFileInput() {
    const dt = new DataTransfer();
    selectedFiles.forEach(file => dt.items.add(file));
    fileInput.files = dt.files;
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    updatePreview();
}

function clearFiles() {
    selectedFiles = [];
    updatePreview();
}

function formatBytes(bytes) {
    if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
    if (bytes >= 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return bytes + ' B';
}

function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
    // Don't clear files - user might want to come back
}

// Drag and drop
dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    e.stopPropagation();
    dropZone.classList.add('border-secondary-500', 'bg-secondary-50');
});

dropZone.addEventListener('dragleave', (e) => {
    e.preventDefault();
    e.stopPropagation();
    dropZone.classList.remove('border-secondary-500', 'bg-secondary-50');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    e.stopPropagation();
    dropZone.classList.remove('border-secondary-500', 'bg-secondary-50');
    addFiles(e.dataTransfer.files);
});

// Form submit with progress simulation
uploadForm.addEventListener('submit', function(e) {
    if (selectedFiles.length === 0) {
        e.preventDefault();
        return;
    }

    // Show progress
    dropZone.classList.add('hidden');
    previewSection.classList.add('hidden');
    uploadProgress.classList.remove('hidden');
    submitBtn.disabled = true;

    // Simulate progress (actual upload progress requires XHR/fetch)
    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 15;
        if (progress > 90) progress = 90;
        progressBar.style.width = progress + '%';
        progressText.textContent = `A processar... ${Math.round(progress)}%`;
    }, 200);

    // The form will submit normally
    // Clean up on page unload
    window.addEventListener('beforeunload', () => clearInterval(interval));
});

// Prevent clicking inside preview from triggering dropzone
previewSection.addEventListener('click', (e) => e.stopPropagation());
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
