<?php
require_once __DIR__ . '/includes/init.php';

try {
    $db = \Core\Database::getInstance()->getPdo();

    echo "=== Colunas da tabela product_images ===\n";
    $stmt = $db->query('SHOW COLUMNS FROM product_images');
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($cols as $col) {
        echo $col['Field'] . ' (' . $col['Type'] . ')' . PHP_EOL;
    }

} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . PHP_EOL;
}
