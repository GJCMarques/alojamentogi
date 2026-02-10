<?php
/**
 * A Casa do Gi - Admin Create Product
 */

require_once dirname(dirname(dirname(__DIR__))) . '/includes/init.php';
require_once dirname(dirname(__DIR__)) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;
use Core\Validator;

$db = Database::getInstance();

// Get categories
$categories = $db->fetchAll(
    "SELECT c.id, ct.name
     FROM product_categories c
     LEFT JOIN product_category_translations ct ON c.id = ct.category_id AND ct.language_id = 1
     WHERE c.is_active = 1
     ORDER BY ct.name"
);

// Get languages
$languages = $db->fetchAll("SELECT * FROM languages WHERE is_active = 1 ORDER BY is_default DESC");

$errors = [];
$product = [
    'category_id' => '',
    'slug' => '',
    'sku' => '',
    'price' => '',
    'sale_price' => '',
    'stock_quantity' => 0,
    'track_inventory' => 1,
    'weight' => '',
    'is_featured' => 0,
    'is_active' => 1
];
$translations = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF
    if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token de segurança inválido. Por favor, tente novamente.';
    } else {
        // Get form data
        $product = [
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'slug' => sanitize($_POST['slug'] ?? ''),
            'sku' => sanitize($_POST['sku'] ?? ''),
            'price' => (float)str_replace(',', '.', $_POST['price'] ?? 0),
            'sale_price' => !empty($_POST['sale_price']) ? (float)str_replace(',', '.', $_POST['sale_price']) : null,
            'stock_quantity' => (int)($_POST['stock_quantity'] ?? 0),
            'track_inventory' => isset($_POST['track_inventory']) ? 1 : 0,
            'weight' => !empty($_POST['weight']) ? (float)str_replace(',', '.', $_POST['weight']) : null,
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        $translations = $_POST['translations'] ?? [];

        // Validation
        $validator = new Validator();
        $validator->required($product['category_id'], 'category_id', 'Categoria');
        $validator->required($product['sku'], 'sku', 'SKU');
        $validator->required($product['price'], 'price', 'Preço');

        // Check SKU uniqueness
        $existingSku = $db->fetch("SELECT id FROM products WHERE sku = ?", [$product['sku']]);
        if ($existingSku) {
            $validator->addError('sku', 'Este SKU já existe.');
        }

        // Generate slug if empty
        if (empty($product['slug'])) {
            $name = $translations[1]['name'] ?? $translations[2]['name'] ?? 'produto';
            $product['slug'] = createSlug($name);
        }

        // Check slug uniqueness
        $existingSlug = $db->fetch("SELECT id FROM products WHERE slug = ?", [$product['slug']]);
        if ($existingSlug) {
            $product['slug'] = $product['slug'] . '-' . time();
        }

        // Check for translation names
        $hasName = false;
        foreach ($translations as $langId => $trans) {
            if (!empty($trans['name'])) {
                $hasName = true;
                break;
            }
        }
        if (!$hasName) {
            $validator->addError('name', 'É necessário definir um nome para pelo menos uma língua.');
        }

        $errors = $validator->getErrors();

        if (empty($errors)) {
            $db->beginTransaction();

            try {
                // Insert product
                $productId = $db->insert('products', $product);

                // Insert translations
                foreach ($translations as $langId => $trans) {
                    if (!empty($trans['name'])) {
                        $db->insert('product_translations', [
                            'product_id' => $productId,
                            'language_id' => (int)$langId,
                            'name' => sanitize($trans['name']),
                            'short_description' => sanitize($trans['short_description'] ?? ''),
                            'description' => sanitize($trans['description'] ?? '')
                        ]);
                    }
                }

                // Handle image uploads
                if (!empty($_FILES['images']['name'][0])) {
                    $uploadDir = UPLOADS_PATH . '/products/';

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $isPrimary = true;
                    $sortOrder = 0;

                    foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
                        if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                            $originalName = $_FILES['images']['name'][$key];
                            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                            // Validate extension
                            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                            if (!in_array($extension, $allowedExtensions)) {
                                continue;
                            }

                            // Validate MIME type
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            $mimeType = finfo_file($finfo, $tmpName);
                            finfo_close($finfo);

                            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                            if (!in_array($mimeType, $allowedMimes)) {
                                continue;
                            }

                            // Generate unique filename
                            $newFilename = $product['slug'] . '-' . uniqid() . '.' . $extension;
                            $targetPath = $uploadDir . $newFilename;

                            if (move_uploaded_file($tmpName, $targetPath)) {
                                $fileSize = $_FILES['images']['size'][$key];
                                $fileType = $_FILES['images']['type'][$key];

                                // Insert into media
                                $mediaId = $db->insert('media', [
                                    'filename' => $newFilename,
                                    'original_name' => $originalName,
                                    'file_path' => '/uploads/products/' . $newFilename,
                                    'file_type' => $fileType,
                                    'file_size' => $fileSize,
                                    'category' => 'products'
                                ]);

                                $db->insert('product_images', [
                                    'product_id' => $productId,
                                    'media_id' => $mediaId,
                                    'is_primary' => $isPrimary ? 1 : 0,
                                    'sort_order' => $sortOrder
                                ]);
                                $isPrimary = false;
                                $sortOrder++;
                            }
                        }
                    }
                }

                $db->commit();

                Session::flash('success', 'Produto criado com sucesso.');
                redirect('/admin/produtos/editar/?id=' . $productId);

            } catch (\Exception $e) {
                $db->rollback();
                $errors[] = 'Erro ao criar produto: ' . $e->getMessage();
            }
        }
    }
}

$pageTitle = 'Novo Produto';
$currentPage = 'produtos';
include dirname(dirname(__DIR__)) . '/includes/header.php';
?>

<div class="mb-6">
    <a href="../" class="text-secondary-600 hover:text-secondary-700 text-sm">
        &larr; Voltar aos Produtos
    </a>
</div>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Novo Produto</h1>
</div>

<?php if (!empty($errors)): ?>
<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
    <ul class="list-disc list-inside">
        <?php foreach ($errors as $key => $error): ?>
        <li><?= is_array($error) ? implode(', ', $error) : e($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form action="" method="post" enctype="multipart/form-data" class="space-y-6">
    <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Translations -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px" id="lang-tabs">
                        <?php foreach ($languages as $index => $lang): ?>
                        <button type="button"
                                class="lang-tab px-6 py-3 text-sm font-medium border-b-2 <?= $index === 0 ? 'border-secondary-600 text-secondary-600' : 'border-transparent text-gray-500 hover:text-gray-700' ?>"
                                data-lang="<?= $lang['id'] ?>">
                            <?= e($lang['name']) ?>
                            <?php if ($lang['is_default']): ?>
                            <span class="ml-1 text-xs text-gray-400">(principal)</span>
                            <?php endif; ?>
                        </button>
                        <?php endforeach; ?>
                    </nav>
                </div>

                <?php foreach ($languages as $index => $lang): ?>
                <div class="lang-content p-6 <?= $index === 0 ? '' : 'hidden' ?>" data-lang="<?= $lang['id'] ?>">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nome do Produto <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="translations[<?= $lang['id'] ?>][name]"
                                   value="<?= e($translations[$lang['id']]['name'] ?? '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500"
                                   <?= $lang['is_default'] ? 'required' : '' ?>>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Descrição Curta
                            </label>
                            <input type="text"
                                   name="translations[<?= $lang['id'] ?>][short_description]"
                                   value="<?= e($translations[$lang['id']]['short_description'] ?? '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500"
                                   maxlength="255">
                            <p class="text-xs text-gray-500 mt-1">Máximo 255 caracteres. Aparece na listagem de produtos.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Descrição Completa
                            </label>
                            <textarea name="translations[<?= $lang['id'] ?>][description]"
                                      rows="6"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500"><?= e($translations[$lang['id']]['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Images -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Imagens</h3>

                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center" id="image-dropzone">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-600 mb-2">Arraste imagens ou clique para selecionar</p>
                    <p class="text-sm text-gray-500">JPG, PNG, WebP ou GIF. Máximo 5MB por imagem.</p>
                    <input type="file"
                           name="images[]"
                           multiple
                           accept="image/jpeg,image/png,image/webp,image/gif"
                           class="hidden"
                           id="image-input">
                    <button type="button"
                            onclick="document.getElementById('image-input').click()"
                            class="mt-4 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Selecionar Imagens
                    </button>
                </div>

                <div id="image-preview" class="mt-4 grid grid-cols-4 gap-4"></div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Estado</h3>

                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               <?= $product['is_active'] ? 'checked' : '' ?>
                               class="w-4 h-4 text-secondary-600 border-gray-300 rounded focus:ring-secondary-500">
                        <span class="ml-2 text-gray-700">Ativo</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox"
                               name="is_featured"
                               value="1"
                               <?= $product['is_featured'] ? 'checked' : '' ?>
                               class="w-4 h-4 text-secondary-600 border-gray-300 rounded focus:ring-secondary-500">
                        <span class="ml-2 text-gray-700">Produto em destaque</span>
                    </label>
                </div>
            </div>

            <!-- Product Details -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Detalhes</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Categoria <span class="text-red-500">*</span>
                        </label>
                        <select name="category_id"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                            <option value="">Selecione...</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            SKU <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="sku"
                               value="<?= e($product['sku']) ?>"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Slug (URL)
                        </label>
                        <input type="text"
                               name="slug"
                               value="<?= e($product['slug']) ?>"
                               placeholder="auto-gerado"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        <p class="text-xs text-gray-500 mt-1">Deixe vazio para gerar automaticamente.</p>
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Preços</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Preço <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">€</span>
                            <input type="text"
                                   name="price"
                                   value="<?= $product['price'] ?>"
                                   required
                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Preço Promocional
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">€</span>
                            <input type="text"
                                   name="sale_price"
                                   value="<?= $product['sale_price'] ?>"
                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Deixe vazio se não estiver em promoção.</p>
                    </div>
                </div>
            </div>

            <!-- Inventory -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Inventário</h3>

                <div class="space-y-4">
                    <label class="flex items-center">
                        <input type="checkbox"
                               name="track_inventory"
                               value="1"
                               <?= $product['track_inventory'] ? 'checked' : '' ?>
                               class="w-4 h-4 text-secondary-600 border-gray-300 rounded focus:ring-secondary-500"
                               id="track-inventory">
                        <span class="ml-2 text-gray-700">Controlar stock</span>
                    </label>

                    <div id="stock-field">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Quantidade em Stock
                        </label>
                        <input type="number"
                               name="stock_quantity"
                               value="<?= $product['stock_quantity'] ?>"
                               min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Peso (kg)
                        </label>
                        <input type="text"
                               name="weight"
                               value="<?= $product['weight'] ?>"
                               placeholder="0.00"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        <p class="text-xs text-gray-500 mt-1">Usado para cálculo de portes.</p>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <button type="submit"
                        class="w-full px-4 py-3 bg-secondary-600 text-white font-medium rounded-lg hover:bg-secondary-700">
                    Criar Produto
                </button>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Language tabs
    const tabs = document.querySelectorAll('.lang-tab');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const langId = this.getAttribute('data-lang');
            
            // Update tabs styling
            tabs.forEach(t => {
                t.classList.remove('border-secondary-600', 'text-secondary-600');
                t.classList.add('border-transparent', 'text-gray-500');
            });
            
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('border-secondary-600', 'text-secondary-600');
            
            // Show/Hide content
            const contents = document.querySelectorAll('.lang-content');
            
            contents.forEach(c => {
                const contentLang = c.getAttribute('data-lang');
                if (contentLang == langId) {
                    c.classList.remove('hidden');
                    c.style.display = 'block';
                } else {
                    c.classList.add('hidden');
                    c.style.display = 'none';
                }
            });
        });
    });

    // Track inventory toggle
    const trackInventory = document.getElementById('track-inventory');
    const stockField = document.getElementById('stock-field');

    if (trackInventory && stockField) {
        trackInventory.addEventListener('change', function() {
            stockField.style.display = this.checked ? 'block' : 'none';
        });
        // Initial state
        stockField.style.display = trackInventory.checked ? 'block' : 'none';
    }

    // Image preview
    const imageInput = document.getElementById('image-input');
    const imagePreview = document.getElementById('image-preview');

    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
            imagePreview.innerHTML = '';

            Array.from(this.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative aspect-square bg-gray-100 rounded overflow-hidden';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-full object-cover">
                        ${index === 0 ? '<span class="absolute top-1 left-1 text-xs bg-secondary-600 text-white px-2 py-1 rounded">Principal</span>' : ''}
                    `;
                    imagePreview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });
    }
});
</script>

<?php include dirname(dirname(__DIR__)) . '/includes/footer.php'; ?>
