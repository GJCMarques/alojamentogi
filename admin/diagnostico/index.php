<?php

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;

$db = Database::getInstance();
$base = basePath();

$pageTitle = 'Diagnostico';
$currentPage = 'diagnostico';
include dirname(__DIR__) . '/includes/header.php';

$uploadDirs = [
    'uploads' => ROOT_PATH . '/uploads',
    'uploads/content' => ROOT_PATH . '/uploads/content',
    'uploads/heroes' => ROOT_PATH . '/uploads/heroes',
    'uploads/products' => ROOT_PATH . '/uploads/products',
    'uploads/activities' => ROOT_PATH . '/uploads/activities',
    'uploads/gallery' => ROOT_PATH . '/uploads/gallery',
    'uploads/accommodation' => ROOT_PATH . '/uploads/accommodation',
    'uploads/media' => ROOT_PATH . '/uploads/media',
];

$contentImages = $db->fetchAll("SELECT block_key, content, language_id FROM content_blocks WHERE block_key LIKE '%image%' ORDER BY block_key, language_id");

$heroImages = $db->fetchAll("
    SELECT ph.page_key, ph.page_name_pt, m.file_path
    FROM page_heroes ph
    LEFT JOIN media m ON m.entity_type = 'hero' AND m.entity_id = ph.id AND m.is_cover = 1
    WHERE ph.is_active = 1
    ORDER BY ph.sort_order
");
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-primary">Diagnostico do Sistema</h1>
    <p class="text-gray-600">Informacao sobre paths, permissoes e imagens</p>
</div>

<!-- CRITICAL: File existence check -->
<div class="bg-red-50 border-2 border-red-300 rounded-lg p-6 mb-6">
    <h2 class="text-lg font-bold text-red-800 mb-4">Verificacao Critica de Ficheiros</h2>
    <?php
    $testFiles = [
        'uploads/heroes/hero_home_1770400193.jpg',
        'uploads/content/home_image_split_left_1770690443.jpg',
        'uploads/activities/activity_69860c41e0ec9.jpg',
        'assets/images/CGsimbUpNB.ico',
        'assets/images/MogadouroLogin.webp',
    ];
    ?>
    <table class="w-full text-sm mb-4">
        <thead><tr class="border-b"><th class="py-1 text-left">Ficheiro</th><th class="py-1 text-center">Existe no Disco</th><th class="py-1 text-left">Path Completo</th></tr></thead>
        <tbody>
        <?php foreach ($testFiles as $f): ?>
            <?php $full = ROOT_PATH . '/' . $f; $exists = file_exists($full); ?>
            <tr class="border-b">
                <td class="py-1 font-mono text-xs"><?= $f ?></td>
                <td class="py-1 text-center font-bold <?= $exists ? 'text-green-600' : 'text-red-600' ?>"><?= $exists ? 'SIM' : 'NAO' ?></td>
                <td class="py-1 font-mono text-xs"><?= $full ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h3 class="font-bold text-red-800 mb-2">Conteudo de /uploads/heroes/</h3>
    <pre class="bg-white p-3 rounded text-xs font-mono overflow-x-auto"><?php
    $heroDir = ROOT_PATH . '/uploads/heroes';
    if (is_dir($heroDir)) {
        $files = scandir($heroDir);
        echo "Ficheiros: " . count($files) . "\n";
        foreach ($files as $f) {
            if ($f === '.' || $f === '..') continue;
            echo $f . " (" . filesize($heroDir . '/' . $f) . " bytes)\n";
        }
    } else {
        echo "DIRETORIO NAO EXISTE: " . $heroDir;
    }
    ?></pre>

    <h3 class="font-bold text-red-800 mb-2 mt-3">Conteudo de /uploads/content/</h3>
    <pre class="bg-white p-3 rounded text-xs font-mono overflow-x-auto"><?php
    $contentDir = ROOT_PATH . '/uploads/content';
    if (is_dir($contentDir)) {
        $files = scandir($contentDir);
        echo "Ficheiros: " . count($files) . "\n";
        foreach ($files as $f) {
            if ($f === '.' || $f === '..') continue;
            echo $f . " (" . filesize($contentDir . '/' . $f) . " bytes)\n";
        }
    } else {
        echo "DIRETORIO NAO EXISTE: " . $contentDir;
    }
    ?></pre>

    <h3 class="font-bold text-red-800 mb-2 mt-3">Conteudo de /assets/images/ (primeiros 10)</h3>
    <pre class="bg-white p-3 rounded text-xs font-mono overflow-x-auto"><?php
    $assetsDir = ROOT_PATH . '/assets/images';
    if (is_dir($assetsDir)) {
        $files = scandir($assetsDir);
        echo "Ficheiros: " . count($files) . "\n";
        $count = 0;
        foreach ($files as $f) {
            if ($f === '.' || $f === '..') continue;
            echo $f . "\n";
            if (++$count >= 10) { echo "...\n"; break; }
        }
    } else {
        echo "DIRETORIO NAO EXISTE: " . $assetsDir;
    }
    ?></pre>
</div>

<!-- System Info -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">Info do Sistema</h2>
    <table class="w-full text-sm">
        <tr class="border-b"><td class="py-2 font-medium w-48">ROOT_PATH</td><td class="py-2 font-mono text-xs"><?= ROOT_PATH ?></td></tr>
        <tr class="border-b"><td class="py-2 font-medium">basePath()</td><td class="py-2 font-mono text-xs">"<?= basePath() ?>"</td></tr>
        <tr class="border-b"><td class="py-2 font-medium">baseUrl()</td><td class="py-2 font-mono text-xs"><?= baseUrl() ?></td></tr>
        <tr class="border-b"><td class="py-2 font-medium">SCRIPT_NAME</td><td class="py-2 font-mono text-xs"><?= $_SERVER['SCRIPT_NAME'] ?? 'N/A' ?></td></tr>
        <tr class="border-b"><td class="py-2 font-medium">DOCUMENT_ROOT</td><td class="py-2 font-mono text-xs"><?= $_SERVER['DOCUMENT_ROOT'] ?? 'N/A' ?></td></tr>
        <tr class="border-b"><td class="py-2 font-medium">HTTP_HOST</td><td class="py-2 font-mono text-xs"><?= $_SERVER['HTTP_HOST'] ?? 'N/A' ?></td></tr>
        <tr class="border-b"><td class="py-2 font-medium">APP_URL (config)</td><td class="py-2 font-mono text-xs"><?= config('app.url') ?></td></tr>
        <tr class="border-b"><td class="py-2 font-medium">APP_URL (env)</td><td class="py-2 font-mono text-xs"><?= getenv('APP_URL') ?: '(nao definido)' ?></td></tr>
        <tr class="border-b"><td class="py-2 font-medium">PHP Version</td><td class="py-2 font-mono text-xs"><?= phpversion() ?></td></tr>
        <tr><td class="py-2 font-medium">OS</td><td class="py-2 font-mono text-xs"><?= PHP_OS ?></td></tr>
    </table>
</div>

<!-- Upload Directories -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">Diretorios de Upload</h2>
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b bg-gray-50">
                <th class="py-2 px-3 text-left">Diretorio</th>
                <th class="py-2 px-3 text-left">Path</th>
                <th class="py-2 px-3 text-center">Existe</th>
                <th class="py-2 px-3 text-center">Writable</th>
                <th class="py-2 px-3 text-center">Ficheiros</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($uploadDirs as $name => $path): ?>
            <?php
                $exists = is_dir($path);
                $writable = $exists && is_writable($path);
                $fileCount = $exists ? count(array_filter(scandir($path), fn($f) => !in_array($f, ['.', '..', '.gitkeep', '.htaccess']))) : 0;
            ?>
            <tr class="border-b">
                <td class="py-2 px-3 font-medium"><?= $name ?></td>
                <td class="py-2 px-3 font-mono text-xs"><?= $path ?></td>
                <td class="py-2 px-3 text-center">
                    <?php if ($exists): ?>
                        <span class="text-green-600 font-bold">SIM</span>
                    <?php else: ?>
                        <span class="text-red-600 font-bold">NAO</span>
                    <?php endif; ?>
                </td>
                <td class="py-2 px-3 text-center">
                    <?php if ($writable): ?>
                        <span class="text-green-600 font-bold">SIM</span>
                    <?php else: ?>
                        <span class="text-red-600 font-bold">NAO</span>
                    <?php endif; ?>
                </td>
                <td class="py-2 px-3 text-center"><?= $fileCount ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Content Block Images -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">Imagens nos Content Blocks (DB)</h2>
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b bg-gray-50">
                <th class="py-2 px-3 text-left">Block Key</th>
                <th class="py-2 px-3 text-center">Lang</th>
                <th class="py-2 px-3 text-left">Path na DB</th>
                <th class="py-2 px-3 text-left">URL Gerada</th>
                <th class="py-2 px-3 text-center">Ficheiro Existe</th>
                <th class="py-2 px-3 text-center">Teste</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($contentImages): ?>
            <?php foreach ($contentImages as $img): ?>
            <?php
                $dbPath = $img['content'];
                $resolvedUrl = resolveContentImage($dbPath);
                $diskPath = ROOT_PATH . $dbPath;
                $fileExists = !empty($dbPath) && file_exists($diskPath);
            ?>
            <tr class="border-b <?= $fileExists ? '' : 'bg-red-50' ?>">
                <td class="py-2 px-3 font-medium"><?= e($img['block_key']) ?></td>
                <td class="py-2 px-3 text-center"><?= $img['language_id'] ?></td>
                <td class="py-2 px-3 font-mono text-xs"><?= e($dbPath) ?></td>
                <td class="py-2 px-3 font-mono text-xs"><?= e($resolvedUrl) ?></td>
                <td class="py-2 px-3 text-center">
                    <?php if ($fileExists): ?>
                        <span class="text-green-600 font-bold">SIM</span>
                    <?php else: ?>
                        <span class="text-red-600 font-bold">NAO</span>
                        <div class="text-xs text-red-500 mt-1"><?= e($diskPath) ?></div>
                    <?php endif; ?>
                </td>
                <td class="py-2 px-3 text-center">
                    <?php if ($fileExists): ?>
                        <a href="<?= e($resolvedUrl) ?>" target="_blank" class="text-blue-600 underline text-xs">Abrir</a>
                    <?php else: ?>
                        <span class="text-red-400 text-xs">N/A</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr><td colspan="6" class="py-4 text-center text-gray-500">Nenhuma imagem encontrada nos content blocks</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Hero Images -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">Hero Images (DB)</h2>
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b bg-gray-50">
                <th class="py-2 px-3 text-left">Pagina</th>
                <th class="py-2 px-3 text-left">Path na DB</th>
                <th class="py-2 px-3 text-left">URL Gerada</th>
                <th class="py-2 px-3 text-center">Ficheiro Existe</th>
                <th class="py-2 px-3 text-center">Teste</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($heroImages as $hero): ?>
            <?php
                $dbPath = $hero['file_path'] ?? '';
                $resolvedUrl = $dbPath ? (($dbPath[0] === '/') ? basePath() . $dbPath : asset($dbPath)) : '';
                $diskPath = $dbPath ? ROOT_PATH . $dbPath : '';
                $fileExists = !empty($dbPath) && file_exists($diskPath);
            ?>
            <tr class="border-b <?= $fileExists ? '' : 'bg-red-50' ?>">
                <td class="py-2 px-3 font-medium"><?= e($hero['page_name_pt']) ?></td>
                <td class="py-2 px-3 font-mono text-xs"><?= e($dbPath ?: '(sem imagem)') ?></td>
                <td class="py-2 px-3 font-mono text-xs"><?= e($resolvedUrl) ?></td>
                <td class="py-2 px-3 text-center">
                    <?php if (empty($dbPath)): ?>
                        <span class="text-yellow-600">N/A</span>
                    <?php elseif ($fileExists): ?>
                        <span class="text-green-600 font-bold">SIM</span>
                    <?php else: ?>
                        <span class="text-red-600 font-bold">NAO</span>
                        <div class="text-xs text-red-500 mt-1"><?= e($diskPath) ?></div>
                    <?php endif; ?>
                </td>
                <td class="py-2 px-3 text-center">
                    <?php if ($fileExists): ?>
                        <a href="<?= e($resolvedUrl) ?>" target="_blank" class="text-blue-600 underline text-xs">Abrir</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Test Upload -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">Teste de Upload</h2>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
        $testDir = ROOT_PATH . '/uploads/content';
        $result = [];
        $result['tmp_name'] = $_FILES['test_file']['tmp_name'];
        $result['error_code'] = $_FILES['test_file']['error'];
        $result['size'] = $_FILES['test_file']['size'];
        $result['dir_exists'] = is_dir($testDir);
        $result['dir_writable'] = is_writable($testDir);
        $result['dir_path'] = $testDir;

        if ($_FILES['test_file']['error'] === UPLOAD_ERR_OK) {
            $testName = 'test_diagnostic_' . time() . '.jpg';
            $testPath = $testDir . '/' . $testName;
            $moved = @move_uploaded_file($_FILES['test_file']['tmp_name'], $testPath);
            $result['move_result'] = $moved;
            $result['target_path'] = $testPath;
            $result['file_exists_after'] = file_exists($testPath);
            if ($moved) {
                $result['url'] = basePath() . '/uploads/content/' . $testName;
                @unlink($testPath);
                $result['cleanup'] = true;
            } else {
                $result['move_error'] = error_get_last()['message'] ?? 'unknown';
            }
        }

        echo '<div class="bg-gray-100 rounded p-4 mb-4 font-mono text-xs whitespace-pre-wrap">';
        echo e(print_r($result, true));
        echo '</div>';
    }
    ?>
    <form method="POST" enctype="multipart/form-data" class="flex items-center gap-4">
        <input type="hidden" name="csrf_token" value="<?= \Core\CSRF::getToken() ?>">
        <input type="file" name="test_file" accept="image/*" class="text-sm">
        <button type="submit" class="px-4 py-2 bg-secondary-600 text-white rounded text-sm hover:bg-secondary-700">
            Testar Upload
        </button>
    </form>
    <p class="text-xs text-gray-500 mt-2">O ficheiro sera eliminado imediatamente apos o teste.</p>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
