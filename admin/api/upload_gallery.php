<?php
require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\CSRF;
use Core\ImageOptimizer;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método inválido']);
    exit;
}

if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'error' => 'Token CSRF inválido']);
    exit;
}

$accommodationId = (int)($_POST['accommodation_id'] ?? 0);
$selectedAccommodationNumber = (int)($_POST['accommodation_number'] ?? 1);

if (!$accommodationId) {
    echo json_encode(['success' => false, 'error' => 'ID do alojamento em falta']);
    exit;
}

if (empty($_FILES['file'])) {
    echo json_encode(['success' => false, 'error' => 'Nenhum ficheiro recebido']);
    exit;
}

$file = $_FILES['file'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'Erro no upload do ficheiro']);
    exit;
}

$allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'error' => 'Tipo de ficheiro não suportado. Apenas JPEG, PNG, WebP']);
    exit;
}

$db = Database::getInstance();

$uploadDir = ROOT_PATH . '/uploads/accommodation/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$maxOrder = $db->fetch(
    "SELECT MAX(sort_order) as max_order FROM media WHERE category = 'gallery' AND accommodation_id = ?",
    [$accommodationId]
)['max_order'] ?? 0;

$newName = 'accommodation_' . $selectedAccommodationNumber . '_' . uniqid() . '.webp';

if (ImageOptimizer::processUpload($file['tmp_name'], $uploadDir . $newName)) {
    $fileSize = filesize($uploadDir . $newName);
    $fileType = 'image/webp';
    $originalName = $file['name'];

    $mediaId = $db->insert('media', [
        'filename' => $newName,
        'original_name' => $originalName,
        'file_path' => '/uploads/accommodation/' . $newName,
        'file_type' => $fileType,
        'file_size' => $fileSize,
        'category' => 'gallery',
        'entity_type' => 'accommodation',
        'accommodation_id' => $accommodationId,
        'is_cover' => 0,
        'sort_order' => $maxOrder + 1,
        'uploaded_by' => $_SESSION['admin_id'] ?? null
    ]);
    
    // Devolve o HTML para poder ser adicionado visualmente à lista imediatamente
    $html = '
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm flex items-center p-2 gap-3 mb-2">
        <div class="w-16 h-16 bg-gray-100 rounded flex-shrink-0">
            <img src="' . basePath() . '/uploads/accommodation/' . $newName . '" class="w-full h-full object-cover rounded">
        </div>
        <div class="flex-1 truncate">
            <p class="text-sm font-medium text-gray-700 truncate">' . htmlspecialchars($originalName) . '</p>
            <p class="text-xs text-green-600 font-medium mt-1">Concluído</p>
        </div>
    </div>';

    echo json_encode(['success' => true, 'html' => $html]);
} else {
    echo json_encode(['success' => false, 'error' => 'Falha ao processar a imagem.']);
}
