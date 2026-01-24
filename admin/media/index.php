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
            class="px-4 py-2 bg-olive-600 text-white rounded-lg hover:bg-olive-700 flex items-center gap-2">
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
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-olive-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
            <select name="type" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-olive-500">
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
                class="px-4 py-2 bg-olive-600 text-white rounded-lg hover:bg-olive-700">
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
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-800">Carregar Ficheiros</h3>
            <button onclick="document.getElementById('uploadModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="" method="post" enctype="multipart/form-data" class="p-6">
            <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
            <input type="hidden" name="upload" value="1">

            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-olive-500 transition-colors"
                 id="dropZone">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-gray-600 mb-2">Arraste ficheiros aqui ou</p>
                <label class="inline-block px-4 py-2 bg-olive-600 text-white rounded-lg cursor-pointer hover:bg-olive-700">
                    Escolher Ficheiros
                    <input type="file" name="files[]" multiple accept="image/*" class="hidden" id="fileInput">
                </label>
                <p class="text-xs text-gray-500 mt-2">JPEG, PNG, GIF, WebP - Max 5MB</p>
            </div>

            <div id="fileList" class="mt-4 space-y-2 hidden"></div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button"
                        onclick="document.getElementById('uploadModal').classList.add('hidden')"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-olive-600 text-white rounded-lg hover:bg-olive-700">
                    Carregar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(window.location.origin + text);
    alert('URL copiado!');
}

// File input preview
const fileInput = document.getElementById('fileInput');
const fileList = document.getElementById('fileList');
const dropZone = document.getElementById('dropZone');

fileInput.addEventListener('change', updateFileList);

function updateFileList() {
    const files = fileInput.files;
    if (files.length > 0) {
        fileList.classList.remove('hidden');
        fileList.innerHTML = '';
        for (let file of files) {
            const div = document.createElement('div');
            div.className = 'flex items-center gap-2 text-sm text-gray-600 bg-gray-50 px-3 py-2 rounded';
            div.innerHTML = `
                <svg class="w-4 h-4 text-olive-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                ${file.name} (${(file.size / 1024).toFixed(1)} KB)
            `;
            fileList.appendChild(div);
        }
    }
}

// Drag and drop
dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('border-olive-500', 'bg-olive-50');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('border-olive-500', 'bg-olive-50');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-olive-500', 'bg-olive-50');
    fileInput.files = e.dataTransfer.files;
    updateFileList();
});
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
