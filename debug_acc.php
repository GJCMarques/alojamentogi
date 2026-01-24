<?php
require_once __DIR__ . '/includes/init.php';
use Core\Database;

try {
    $db = Database::getInstance();
    $rows = $db->fetchAll("DESCRIBE accommodation");
    echo "Columns in accommodation:\n";
    foreach ($rows as $row) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
