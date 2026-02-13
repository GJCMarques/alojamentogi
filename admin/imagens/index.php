<?php

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;

$db = Database::getInstance();

$languages = $db->fetchAll("SELECT * FROM languages WHERE is_active = 1 ORDER BY is_default DESC");

$imageDefinitions = [
    'homepage' => [
        'label' => 'Página Inicial',
        'images' => [
            'home_image_split_left' => [
                'label' => 'Split Hero - Imagem Esquerda',
                'description' => 'Imagem do lado esquerdo do hero dividido. Recomenda-se formato vertical.'
            ],
            'home_image_split_right' => [
                'label' => 'Split Hero - Imagem Direita',
                'description' => 'Imagem do lado direito do hero dividido. Recomenda-se formato vertical.'
            ],
            'home_image_about' => [
                'label' => 'Secção Sobre Nós (Composição)',
                'description' => 'Imagem principal da secção "A Nossa História".'
            ],
        ]
    ],
    'about' => [
        'label' => 'Sobre Nós',
        'images' => [
            'about_image_intro' => [
                'label' => 'Imagem Introdução',
                'description' => 'Foto histórica/antiga exibida na introdução.'
            ],
            'about_image_region' => [
                'label' => 'Imagem Região (Imersiva)',
                'description' => 'Imagem de fundo grande para a secção da região.'
            ],
        ]
    ]
];

$currentSection = isset($_GET['section']) && isset($imageDefinitions[$_GET['section']])
    ? $_GET['section']
    : 'homepage';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $section = $imageDefinitions[$currentSection];
        $uploadCount = 0;
        $errors = [];

        foreach ($section['images'] as $imageKey => $imageDef) {

            if (isset($_FILES[$imageKey]) && $_FILES[$imageKey]['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES[$imageKey];
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

                if (!in_array($ext, $allowed)) {
                    $errors[] = "{$imageKey}: formato '.{$ext}' nao permitido";
                    continue;
                }

                $uploadDir = ROOT_PATH . '/uploads/content';
                if (!is_dir($uploadDir)) {
                    @mkdir($uploadDir, 0775, true);
                }

                if (!is_dir($uploadDir)) {
                    $errors[] = "{$imageKey}: impossivel criar dir {$uploadDir}";
                    continue;
                }

                if (!is_writable($uploadDir)) {
                    $errors[] = "{$imageKey}: dir sem permissao escrita: {$uploadDir}";
                    continue;
                }

                $filename = $imageKey . '_' . time() . '.' . $ext;
                $targetPath = $uploadDir . '/' . $filename;
                $dbPath = '/uploads/content/' . $filename;

                if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $err = error_get_last()['message'] ?? 'desconhecido';
                    $errors[] = "{$imageKey}: move_uploaded_file falhou - {$err}";
                    continue;
                }

                if (!file_exists($targetPath)) {
                    $errors[] = "{$imageKey}: ficheiro nao existe apos move: {$targetPath}";
                    continue;
                }

                $db->insert('media', [
                    'filename' => $filename,
                    'original_name' => $file['name'],
                    'file_path' => $dbPath,
                    'file_type' => $file['type'],
                    'file_size' => $file['size'],
                    'category' => 'content',
                    'uploaded_by' => Session::get('admin_id'),
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                foreach ($languages as $lang) {
                    $existing = $db->fetch(
                        "SELECT id FROM content_blocks WHERE block_key = ? AND language_id = ?",
                        [$imageKey, $lang['id']]
                    );

                    if ($existing) {
                        $db->update('content_blocks', [
                            'content' => $dbPath,
                            'updated_at' => date('Y-m-d H:i:s')
                        ], 'id = ?', [$existing['id']]);
                    } else {
                        $db->insert('content_blocks', [
                            'block_key' => $imageKey,
                            'language_id' => $lang['id'],
                            'content' => $dbPath
                        ]);
                    }
                }
                $uploadCount++;

            } elseif (isset($_FILES[$imageKey]) && $_FILES[$imageKey]['error'] !== UPLOAD_ERR_NO_FILE) {
                $errorCodes = [
                    1 => 'ficheiro excede upload_max_filesize do php.ini',
                    2 => 'ficheiro excede MAX_FILE_SIZE do form',
                    3 => 'upload parcial',
                    4 => 'nenhum ficheiro',
                    6 => 'sem pasta temporaria',
                    7 => 'falha ao escrever no disco',
                    8 => 'extensao PHP bloqueou upload',
                ];
                $code = $_FILES[$imageKey]['error'];
                $errors[] = "{$imageKey}: erro PHP #{$code} - " . ($errorCodes[$code] ?? 'desconhecido');
            }
        }

        if ($uploadCount > 0 && empty($errors)) {
            Session::flash('success', "{$uploadCount} imagem(ns) atualizada(s) com sucesso.");
        } elseif ($uploadCount > 0 && !empty($errors)) {
            Session::flash('success', "{$uploadCount} imagem(ns) OK. Erros: " . implode(' | ', $errors));
        } elseif (!empty($errors)) {
            Session::flash('error', "Erros: " . implode(' | ', $errors));
        } else {
            Session::flash('info', "Nenhuma alteracao efetuada.");
        }

        redirect('/admin/imagens/?section=' . $currentSection);
    }
}

$currentLangId = $languages[0]['id'];
$imageKeys = array_keys($imageDefinitions[$currentSection]['images']);
$placeholders = implode(',', array_fill(0, count($imageKeys), '?'));
$currentImages = [];

if (!empty($imageKeys)) {

    $rows = $db->fetchAll(
        "SELECT block_key, content FROM content_blocks WHERE block_key IN ({$placeholders}) AND language_id = ?",
        array_merge($imageKeys, [$currentLangId])
    );
    foreach ($rows as $row) {
        $currentImages[$row['block_key']] = $row['content'];
    }
}

$pageTitle = 'Imagens de Conteúdo';
$currentPage = 'imagens';
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-primary">Gestão de Imagens</h1>
        <p class="text-gray-600">Alterar imagens do corpo das páginas</p>
    </div>
</div>

<div class="flex gap-6">
    <!-- Sidebar -->
    <div class="w-64 flex-shrink-0">
        <nav class="bg-white rounded-lg shadow-sm overflow-hidden">
            <?php foreach ($imageDefinitions as $key => $section): ?>
            <a href="?section=<?= $key ?>"
               class="flex items-center px-4 py-3 text-sm font-medium border-b border-gray-100 last:border-0
                      <?= $currentSection === $key
                          ? 'bg-secondary-50 text-secondary-700 border-l-4 border-l-secondary-600'
                          : 'text-gray-600 hover:bg-gray-50' ?>">
                <?= $section['label'] ?>
            </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Content -->
    <div class="flex-1">
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-800"><?= $imageDefinitions[$currentSection]['label'] ?></h2>
            </div>

            <form action="" method="post" enctype="multipart/form-data" class="p-6">
                <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">

                <div class="space-y-8">
                    <?php foreach ($imageDefinitions[$currentSection]['images'] as $imageKey => $imageDef): ?>
                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-100">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-800"><?= $imageDef['label'] ?></h3>
                            <?php if (isset($imageDef['description'])): ?>
                            <p class="text-sm text-gray-500"><?= $imageDef['description'] ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="grid md:grid-cols-2 gap-8">
                            <!-- Current / Preview Image -->
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden relative group h-64 flex items-center justify-center p-2" id="preview-container-<?= $imageKey ?>">
                                <?php
                                $currentPath = $currentImages[$imageKey] ?? '';
                                if ($currentPath):
                                    $displayUrl = $currentPath[0] === '/' ? basePath() . $currentPath : asset($currentPath);
                                ?>
                                    <img src="<?= e($displayUrl) ?>" alt="Preview" class="max-w-full max-h-full object-contain rounded">
                                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <a href="<?= e($displayUrl) ?>" target="_blank" class="text-white text-sm bg-black/50 px-3 py-1 rounded-full hover:bg-black/70 transition-colors">
                                            Ver Tamanho Original
                                        </a>
                                    </div>
                                    <div class="absolute bottom-0 left-0 right-0 bg-black/60 px-3 py-1.5 text-center">
                                        <p class="text-xs text-white truncate"><?= basename($currentPath) ?></p>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="text-sm">Sem imagem definida</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Upload Zone -->
                            <div class="flex flex-col justify-center">
                                <div class="w-full relative group">
                                    <input type="file"
                                           name="<?= $imageKey ?>"
                                           id="input-<?= $imageKey ?>"
                                           accept=".jpg,.jpeg,.png,.webp"
                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                           onchange="previewImage(this, '<?= $imageKey ?>')">

                                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center group-hover:border-secondary-500 group-hover:bg-secondary-50 transition-all h-64 flex flex-col items-center justify-center">
                                        <div class="mb-4 p-3 bg-gray-100 rounded-full group-hover:bg-secondary-100 transition-colors">
                                            <svg class="w-8 h-8 text-gray-400 group-hover:text-secondary-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                        </div>
                                        <p class="text-gray-900 font-medium mb-1">Clique ou arraste para substituir</p>
                                        <p class="text-xs text-gray-500 mb-4">JPG, PNG, WEBP</p>
                                        <button type="button" class="px-4 py-2 bg-white border border-gray-300 rounded text-sm font-medium text-gray-700 shadow-sm group-hover:bg-secondary-600 group-hover:text-white group-hover:border-transparent transition-all">
                                            Selecionar Imagem
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200 flex items-center justify-between sticky bottom-0 bg-white/95 backdrop-blur py-4 -mb-6 -mx-6 px-6 shadow-up z-20">
                    <div class="text-sm text-gray-500">
                        * As alterações só serão aplicadas após guardar.
                    </div>
                    <button type="submit" class="px-8 py-3 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary-500 transition-all shadow-lg font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Guardar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(input, key) {
    const container = document.getElementById('preview-container-' + key);

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();

        // Update upload text if possible or just show preview
        // Note: The upload box is static text, but the preview box updates.

        reader.onload = function(e) {
            // Clear existing content
            container.innerHTML = '';

            // Image
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'max-w-full max-h-full object-contain rounded';

            // Overlay with "New" badge
            const overlay = document.createElement('div');
            overlay.className = 'absolute top-2 right-2 bg-secondary-600 text-white text-xs font-bold px-2 py-1 rounded shadow animate-pulse';
            overlay.innerText = 'NOVA';

            // Filename tag
            const nameTag = document.createElement('div');
            nameTag.className = 'absolute bottom-0 left-0 right-0 bg-secondary-900/80 px-3 py-1.5 text-center';
            nameTag.innerHTML = `<p class="text-xs text-white truncate">${file.name}</p>`;

            container.appendChild(img);
            container.appendChild(overlay);
            container.appendChild(nameTag);
        }

        reader.readAsDataURL(file);
    }
}
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
