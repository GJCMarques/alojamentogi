<?php
/**
 * Upload Test Script
 */

require_once __DIR__ . '/includes/init.php';

use Core\Database;

echo "<h2>Teste de Upload</h2>";
echo "<pre style='background:#f5f5f5;padding:15px;'>";

// 1. Check uploads directory
$uploadDir = ROOT_PATH . '/uploads/media/';
echo "1. Diretorio de uploads: $uploadDir\n";
echo "   - Existe: " . (is_dir($uploadDir) ? "SIM" : "NAO") . "\n";

if (!is_dir($uploadDir)) {
    echo "   - A criar diretorio...\n";
    if (mkdir($uploadDir, 0755, true)) {
        echo "   - Criado com sucesso!\n";
    } else {
        echo "   - ERRO ao criar!\n";
    }
}

echo "   - Permissao de escrita: " . (is_writable($uploadDir) ? "SIM" : "NAO") . "\n";

// 2. Check media table structure
echo "\n2. Estrutura da tabela media:\n";
$db = Database::getInstance();

try {
    $columns = $db->fetchAll("SHOW COLUMNS FROM media");
    foreach ($columns as $col) {
        echo "   - {$col['Field']} ({$col['Type']})\n";
    }
} catch (Exception $e) {
    echo "   ERRO: " . $e->getMessage() . "\n";
}

// 3. Check existing media
echo "\n3. Ficheiros na base de dados:\n";
try {
    $media = $db->fetchAll("SELECT id, filename, original_name, file_path FROM media ORDER BY id DESC LIMIT 5");
    if (empty($media)) {
        echo "   Nenhum ficheiro encontrado.\n";
    } else {
        foreach ($media as $m) {
            $exists = file_exists(ROOT_PATH . $m['file_path']) ? "existe" : "NAO EXISTE";
            echo "   - ID {$m['id']}: {$m['original_name']} ($exists)\n";
        }
    }
} catch (Exception $e) {
    echo "   ERRO: " . $e->getMessage() . "\n";
}

// 4. Test insert
echo "\n4. Teste de INSERT na tabela media:\n";
try {
    $testData = [
        'filename' => 'test_' . time() . '.jpg',
        'original_name' => 'teste.jpg',
        'file_path' => '/uploads/media/test_' . time() . '.jpg',
        'file_type' => 'image/jpeg',
        'file_size' => 1024,
        'category' => 'other'
    ];

    $db->insert('media', $testData);
    $lastId = $db->lastInsertId();
    echo "   INSERT funcionou! ID: $lastId\n";

    // Clean up test
    $db->delete('media', 'id = ?', [$lastId]);
    echo "   Teste removido.\n";
} catch (Exception $e) {
    echo "   ERRO no INSERT: " . $e->getMessage() . "\n";
}

echo "\n5. PHP Upload Settings:\n";
echo "   - upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "   - post_max_size: " . ini_get('post_max_size') . "\n";
echo "   - max_file_uploads: " . ini_get('max_file_uploads') . "\n";

echo "\n=== TESTE COMPLETO ===\n";
echo "</pre>";

echo "<p><a href='/alojamentogi/admin/media/'>Ir para Media</a></p>";
