<?php
/**
 * Script para corrigir o schema da base de dados
 * Adiciona colunas em falta às tabelas
 */

require_once __DIR__ . '/includes/init.php';

try {
    $db = \Core\Database::getInstance()->getPdo();

    echo "=== A verificar e corrigir schema da base de dados ===\n\n";

    // Verificar se a coluna description existe em product_translations
    $stmt = $db->query("SHOW COLUMNS FROM product_translations LIKE 'description'");
    $descriptionExists = $stmt->fetch();

    if (!$descriptionExists) {
        echo "✗ Coluna 'description' não existe em product_translations\n";
        echo "→ A adicionar coluna 'description'...\n";

        $db->exec("ALTER TABLE product_translations ADD COLUMN description TEXT AFTER short_description");

        echo "✓ Coluna 'description' adicionada com sucesso!\n\n";
    } else {
        echo "✓ Coluna 'description' já existe em product_translations\n\n";
    }

    // Verificar se a coluna short_description existe
    $stmt = $db->query("SHOW COLUMNS FROM product_translations LIKE 'short_description'");
    $shortDescExists = $stmt->fetch();

    if (!$shortDescExists) {
        echo "✗ Coluna 'short_description' não existe em product_translations\n";
        echo "→ A adicionar coluna 'short_description'...\n";

        $db->exec("ALTER TABLE product_translations ADD COLUMN short_description TEXT AFTER name");

        echo "✓ Coluna 'short_description' adicionada com sucesso!\n\n";
    } else {
        echo "✓ Coluna 'short_description' já existe em product_translations\n\n";
    }

    // Verificar se existem produtos sem traduções
    $stmt = $db->query("
        SELECT COUNT(*) as total
        FROM products p
        LEFT JOIN product_translations pt ON p.id = pt.product_id
        WHERE pt.id IS NULL
    ");
    $result = $stmt->fetch();

    if ($result['total'] > 0) {
        echo "⚠ Existem {$result['total']} produtos sem traduções\n";
        echo "→ Por favor, execute o seed.sql para inserir dados de exemplo\n\n";
    } else {
        echo "✓ Todos os produtos têm traduções\n\n";
    }

    // Listar colunas da tabela product_translations
    echo "=== Estrutura da tabela product_translations ===\n";
    $stmt = $db->query("SHOW COLUMNS FROM product_translations");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($columns as $column) {
        echo "  - {$column['Field']} ({$column['Type']})" .
             ($column['Null'] === 'NO' ? ' NOT NULL' : '') .
             "\n";
    }

    echo "\n=== Schema corrigido com sucesso! ===\n";
    echo "Pode agora aceder à loja em: http://localhost/alojamentogi/loja/\n";

} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    exit(1);
}
