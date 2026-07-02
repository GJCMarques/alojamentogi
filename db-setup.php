<?php
// One-time database setup script. DELETE THIS FILE after running it.

$token = 'casadogi-setup-2026';

if (($_GET['token'] ?? '') !== $token) {
    http_response_code(403);
    die('Forbidden. Usage: /db-setup.php?token=' . $token);
}

$host = 'alojamentogi-mysql-8g3t8r';
$user = 'casadogi_user';
$pass = 'CasadoGi2026';
$name = 'casadogi';
$sqlFile = __DIR__ . '/database/casadogiFinal.sql';

echo '<pre>';

// Connect
try {
    $pdo = new PDO("mysql:host=$host;dbname=$name;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "[OK] Connected to MySQL\n";
} catch (PDOException $e) {
    die("[FAIL] Connection failed: " . $e->getMessage() . "\n");
}

// Check current state
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "[INFO] Tables found before import: " . count($tables) . "\n";

if (count($tables) > 5) {
    echo "[SKIP] Database already has " . count($tables) . " tables. Import skipped.\n";
    echo "Existing tables: " . implode(', ', $tables) . "\n";
    echo '</pre>';
    exit;
}

// Read SQL file
if (!file_exists($sqlFile)) {
    die("[FAIL] SQL file not found: $sqlFile\n");
}

echo "[INFO] Reading $sqlFile ...\n";
$sql = file_get_contents($sqlFile);
echo "[INFO] File size: " . number_format(strlen($sql)) . " bytes\n";

// Split into individual statements
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
$pdo->exec("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO'");

$statements = array_filter(
    array_map('trim', preg_split('/;\s*\n/', $sql)),
    fn($s) => !empty($s) && !str_starts_with($s, '--') && !str_starts_with($s, '/*')
);

$total = count($statements);
$ok = 0;
$errors = [];

foreach ($statements as $stmt) {
    if (empty(trim($stmt))) continue;
    try {
        $pdo->exec($stmt);
        $ok++;
    } catch (PDOException $e) {
        $errors[] = $e->getMessage() . "\n  > " . substr($stmt, 0, 120);
    }
}

$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

echo "[INFO] Executed $ok/$total statements\n";

if ($errors) {
    echo "\n[WARNINGS] " . count($errors) . " statement(s) failed:\n";
    foreach ($errors as $err) {
        echo "  - $err\n";
    }
}

// Verify
$tablesAfter = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "\n[OK] Tables after import: " . count($tablesAfter) . "\n";
echo implode(', ', $tablesAfter) . "\n";

if (count($tablesAfter) > 5) {
    echo "\n✅ DATABASE IMPORT COMPLETE! Delete this file: /db-setup.php\n";
} else {
    echo "\n⚠️  Something may have gone wrong — fewer tables than expected.\n";
}

echo '</pre>';
