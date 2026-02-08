<?php
/**
 * A Casa do Gi - Admin Page Heroes Manager
 * Manage hero images for all site pages
 */

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;

$db = Database::getInstance();
$base = basePath();

// Default page heroes configuration
$defaultHeroes = [
    ['page_key' => 'home', 'page_name_pt' => 'Página Inicial', 'page_name_en' => 'Homepage', 'sort_order' => 1],
    ['page_key' => 'accommodation_main', 'page_name_pt' => 'Alojamento (Página Principal)', 'page_name_en' => 'Accommodation (Main Page)', 'sort_order' => 2],
    ['page_key' => 'activities', 'page_name_pt' => 'Atividades', 'page_name_en' => 'Activities', 'sort_order' => 3],
    ['page_key' => 'about', 'page_name_pt' => 'Sobre Nós', 'page_name_en' => 'About Us', 'sort_order' => 4],
    ['page_key' => 'contact', 'page_name_pt' => 'Contactos', 'page_name_en' => 'Contact', 'sort_order' => 5],
    ['page_key' => 'shop', 'page_name_pt' => 'Loja', 'page_name_en' => 'Shop', 'sort_order' => 6],
    ['page_key' => 'product_detail', 'page_name_pt' => 'Produto (Detalhe)', 'page_name_en' => 'Product (Detail)', 'sort_order' => 7],
    ['page_key' => 'cart', 'page_name_pt' => 'Carrinho de Compras', 'page_name_en' => 'Shopping Cart', 'sort_order' => 8],
    ['page_key' => 'checkout', 'page_name_pt' => 'Finalizar Compra', 'page_name_en' => 'Checkout', 'sort_order' => 9],
];

// Auto-seed missing page heroes if table exists
try {
    $existingKeys = $db->fetchAll("SELECT page_key FROM page_heroes");
    if ($existingKeys !== false) {
        $existingKeysList = array_column($existingKeys, 'page_key');
        foreach ($defaultHeroes as $hero) {
            if (!in_array($hero['page_key'], $existingKeysList)) {
                $db->insert('page_heroes', [
                    'page_key' => $hero['page_key'],
                    'page_name_pt' => $hero['page_name_pt'],
                    'page_name_en' => $hero['page_name_en'],
                    'hero_overlay_opacity' => 0.40,
                    'is_active' => 1,
                    'sort_order' => $hero['sort_order']
                ]);
            }
        }
    }
} catch (Exception $e) {
    // Table doesn't exist yet, will show migration message
}

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_hero'])) {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $pageKey = sanitize($_POST['page_key'] ?? '');
        $heroOverlay = floatval($_POST['hero_overlay'] ?? 0.40);

        // Validate page key exists
        $pageHero = $db->fetch("SELECT * FROM page_heroes WHERE page_key = ?", [$pageKey]);

        if ($pageHero) {
            $updateData = [
                'hero_overlay_opacity' => max(0, min(1, $heroOverlay))
            ];

            // Handle file upload
            if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = ROOT_PATH . '/uploads/heroes/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                $maxSize = 10 * 1024 * 1024; // 10MB for hero images

                $fileType = $_FILES['hero_image']['type'];
                $fileSize = $_FILES['hero_image']['size'];

                if (in_array($fileType, $allowedTypes) && $fileSize <= $maxSize) {
                    $ext = pathinfo($_FILES['hero_image']['name'], PATHINFO_EXTENSION);
                    $originalName = $_FILES['hero_image']['name'];
                    $newName = 'hero_' . $pageKey . '_' . time() . '.' . $ext;
                    $targetPath = $uploadDir . $newName;

                    if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $targetPath)) {
                        // Delete old image from media table and filesystem
                        $oldMedia = $db->fetch(
                            "SELECT * FROM media WHERE entity_type = 'hero' AND entity_id = ? AND is_cover = 1",
                            [$pageHero['id']]
                        );

                        if ($oldMedia) {
                            // Delete physical file
                            $oldPath = ROOT_PATH . ltrim($oldMedia['file_path'], '/');
                            if (file_exists($oldPath)) {
                                @unlink($oldPath);
                            }
                            // Delete database record
                            $db->delete('media', 'id = ?', [$oldMedia['id']]);
                        }

                        // Insert new image into media table
                        $db->insert('media', [
                            'filename' => $newName,
                            'original_name' => $originalName,
                            'file_path' => '/uploads/heroes/' . $newName,
                            'file_type' => $fileType,
                            'file_size' => $fileSize,
                            'category' => 'content',
                            'entity_type' => 'hero',
                            'entity_id' => $pageHero['id'],
                            'is_cover' => 1,
                            'sort_order' => 0,
                            'uploaded_by' => $_SESSION['admin_id'] ?? null
                        ]);
                    }
                } else {
                    Session::flash('error', 'Ficheiro inválido. Use JPG, PNG ou WebP até 10MB.');
                    redirect('/admin/heroes/');
                }
            }

            $db->update('page_heroes', $updateData, 'page_key = ?', [$pageKey]);
            Session::flash('success', 'Hero atualizado com sucesso.');
        }
    }
    redirect('/admin/heroes/');
}

// Get all page heroes
$pageHeroes = $db->fetchAll("SELECT * FROM page_heroes WHERE is_active = 1 ORDER BY sort_order");

// If table doesn't exist yet, show migration message
if ($pageHeroes === false) {
    $pageHeroes = [];
}

// Fetch hero images from media table
$heroImages = [];
if (!empty($pageHeroes)) {
    foreach ($pageHeroes as $hero) {
        $image = $db->fetch(
            "SELECT * FROM media WHERE entity_type = 'hero' AND entity_id = ? AND is_cover = 1",
            [$hero['id']]
        );
        if ($image) {
            $heroImages[$hero['id']] = $image;
        }
    }
}

$pageTitle = 'Gestão de Heroes';
$currentPage = 'heroes';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Gestão de Heroes</h1>
    <p class="text-gray-600">Configure as imagens de hero para cada página do site</p>
</div>

<?php if (empty($pageHeroes)): ?>
<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
    <svg class="w-12 h-12 text-yellow-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <h3 class="text-lg font-semibold text-yellow-800 mb-2">Migração Necessária</h3>
    <p class="text-yellow-700 mb-4">A tabela de heroes não existe. Execute a migração:</p>
    <code class="bg-yellow-100 px-4 py-2 rounded text-sm">database/migrations/004_hero_images_system.sql</code>
</div>
<?php else: ?>

<!-- Page Heroes Grid -->
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($pageHeroes as $hero): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow flex flex-col">
        <!-- Preview Image -->
        <div class="relative h-48 bg-gray-100 overflow-hidden group flex-shrink-0 rounded-t-xl">
            <?php
            $currentImage = $heroImages[$hero['id']] ?? null;
            $imageUrl = '';
            if ($currentImage && $currentImage['file_path']) {
                $imageUrl = $base . $currentImage['file_path'];
            }
            ?>
            <?php if ($imageUrl): ?>
            <img src="<?= $imageUrl ?>"
                 alt="<?= e($hero['page_name_pt']) ?>"
                 id="preview-img-<?= e($hero['page_key']) ?>"
                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
            <div class="absolute inset-0 bg-black transition-opacity"
                 id="overlay-<?= e($hero['page_key']) ?>"
                 style="opacity: <?= $hero['hero_overlay_opacity'] ?? 0.4 ?>"></div>
            <?php else: ?>
            <div class="w-full h-full flex items-center justify-center bg-gray-200" id="placeholder-<?= e($hero['page_key']) ?>">
                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <img src="" alt="" id="preview-img-<?= e($hero['page_key']) ?>" class="w-full h-full object-cover hidden absolute inset-0">
            <div class="absolute inset-0 bg-black transition-opacity hidden" id="overlay-<?= e($hero['page_key']) ?>" style="opacity: <?= $hero['hero_overlay_opacity'] ?? 0.4 ?>"></div>
            <?php endif; ?>

            <!-- Page Label -->
            <div class="absolute top-3 left-3">
                <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-primary text-xs font-bold rounded-full shadow">
                    <?= e($hero['page_name_pt']) ?>
                </span>
            </div>

        </div>

        <!-- Edit Form -->
        <form action="" method="POST" enctype="multipart/form-data" class="p-4 space-y-4 flex-grow">
            <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
            <input type="hidden" name="page_key" value="<?= e($hero['page_key']) ?>">
            <input type="hidden" name="update_hero" value="1">

            <!-- Page Name -->
            <div class="font-medium text-gray-800 text-sm"><?= e($hero['page_name_pt']) ?></div>

            <!-- Current Image Path -->
            <?php $currentImage = $heroImages[$hero['id']] ?? null; ?>
            <div class="text-xs text-gray-500 truncate" title="<?= $currentImage ? e($currentImage['file_path']) : '' ?>">
                <?= $currentImage ? e($currentImage['file_path']) : 'Sem imagem definida' ?>
            </div>

            <!-- Upload New Image -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nova Imagem</label>
                <input type="file"
                       name="hero_image"
                       accept="image/jpeg,image/png,image/webp"
                       data-page-key="<?= e($hero['page_key']) ?>"
                       onchange="previewImage(this, '<?= e($hero['page_key']) ?>')"
                       class="hero-file-input w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-secondary-50 file:text-secondary-700 hover:file:bg-secondary-100 cursor-pointer">
                <p class="text-xs text-gray-400 mt-1">JPG, PNG ou WebP (max. 10MB)</p>
            </div>

            <!-- Overlay Opacity -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Opacidade do Overlay: <span id="opacity-value-<?= e($hero['page_key']) ?>"><?= number_format($hero['hero_overlay_opacity'] * 100, 0) ?>%</span>
                </label>
                <input type="range"
                       name="hero_overlay"
                       min="0"
                       max="1"
                       step="0.05"
                       value="<?= $hero['hero_overlay_opacity'] ?>"
                       class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-secondary-600"
                       oninput="updateOverlayPreview(this, '<?= e($hero['page_key']) ?>')">
            </div>

            <!-- Submit Button -->
            <button type="submit"
                    class="w-full px-4 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700 transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Guardar Alterações
            </button>
        </form>
    </div>
    <?php endforeach; ?>
</div>

<?php endif; ?>

<!-- Info Section -->
<div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm text-blue-800">
            <p class="font-semibold mb-1">Sobre as Imagens de Hero</p>
            <ul class="list-disc list-inside space-y-1 text-blue-700">
                <li>Tamanho recomendado: 1920x1080 pixels ou superior</li>
                <li>Formatos aceites: JPG, PNG, WebP</li>
                <li>O overlay escurece a imagem para melhor legibilidade do texto</li>
                <li>As imagens das Casas (Casa 1 e Casa 2) são geridas na página de Alojamento</li>
            </ul>
        </div>
    </div>
</div>

<!-- Image Preview Script -->
<script>
function previewImage(input, pageKey) {
    const previewImg = document.getElementById('preview-img-' + pageKey);
    const placeholder = document.getElementById('placeholder-' + pageKey);
    const overlay = document.getElementById('overlay-' + pageKey);

    if (input.files && input.files[0]) {
        const file = input.files[0];

        // Validate file type
        const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            alert('Formato inválido. Use JPG, PNG ou WebP.');
            input.value = '';
            return;
        }

        // Validate file size (10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('Ficheiro demasiado grande. Máximo 10MB.');
            input.value = '';
            return;
        }

        const reader = new FileReader();

        reader.onload = function(e) {
            // Show preview image
            previewImg.src = e.target.result;
            previewImg.classList.remove('hidden');

            // Hide placeholder if exists
            if (placeholder) {
                placeholder.classList.add('hidden');
            }

            // Show overlay
            if (overlay) {
                overlay.classList.remove('hidden');
            }
        };

        reader.readAsDataURL(file);
    }
}

function updateOverlayPreview(input, pageKey) {
    const overlay = document.getElementById('overlay-' + pageKey);
    const valueDisplay = document.getElementById('opacity-value-' + pageKey);

    if (overlay) {
        overlay.style.opacity = input.value;
    }
    if (valueDisplay) {
        valueDisplay.textContent = Math.round(input.value * 100) + '%';
    }
}
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
