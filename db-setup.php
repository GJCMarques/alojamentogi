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

try {
    $pdo = new PDO("mysql:host=$host;dbname=$name;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "[OK] Connected to MySQL\n";
} catch (PDOException $e) {
    die("[FAIL] Connection: " . $e->getMessage() . "\n");
}

$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "[INFO] Tables before import: " . count($tables) . "\n";

if (count($tables) > 5) {
    echo "[SKIP] Already has " . count($tables) . " tables — import skipped.\n";
    echo implode(', ', $tables) . "\n";
    echo '</pre>';
    exit;
}

if (!file_exists($sqlFile)) {
    die("[FAIL] SQL file not found: $sqlFile\n");
}

$sql = file_get_contents($sqlFile);

// Strip UTF-8 BOM (phpMyAdmin adds it)
if (substr($sql, 0, 3) === "\xEF\xBB\xBF") {
    $sql = substr($sql, 3);
    echo "[INFO] Stripped UTF-8 BOM\n";
}

echo "[INFO] File size: " . number_format(strlen($sql)) . " bytes\n";

$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
$pdo->exec("SET NAMES utf8mb4");

// Line-by-line parser — correct approach for phpMyAdmin dumps
// Accumulates lines into a statement until it ends with ';'
$lines = preg_split('/\r?\n/', $sql);
$stmt  = '';
$ok    = 0;
$errors = [];

foreach ($lines as $line) {
    $trimmed = ltrim($line);

    // Skip empty lines and comment-only lines while not mid-statement
    if (empty(trim($line))
        || str_starts_with($trimmed, '--')
        || str_starts_with($trimmed, '#')) {
        continue;
    }

    $stmt .= $line . "\n";

    // A statement is complete when the trimmed line ends with ';'
    if (str_ends_with(rtrim($line), ';')) {
        $stmt = trim($stmt);

        if (!empty($stmt)) {
            try {
                $pdo->exec($stmt);
                $ok++;
            } catch (PDOException $e) {
                $msg = $e->getMessage();

                // VIEWs exported by phpMyAdmin use DEFINER=`root`@`localhost`
                // which fails when running as casadogi_user — retry without DEFINER
                if (str_contains($msg, 'DEFINER') || str_contains($msg, '1227')) {
                    $clean = preg_replace(
                        '/DEFINER\s*=\s*`[^`]+`@`[^`]+`\s*/i',
                        '',
                        $stmt
                    );
                    try {
                        $pdo->exec($clean);
                        $ok++;
                    } catch (PDOException $e2) {
                        $errors[] = '[VIEW] ' . $e2->getMessage() . "\n  > " . substr($clean, 0, 120);
                    }
                } else {
                    $errors[] = $msg . "\n  > " . substr($stmt, 0, 120);
                }
            }
        }

        $stmt = '';
    }
}

$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

echo "[INFO] Statements executed OK: $ok\n";

$tablesAfter = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "[INFO] Tables after import: " . count($tablesAfter) . "\n";

if (!empty($errors)) {
    echo "\n[WARNINGS] " . count($errors) . " statement(s) failed:\n";
    foreach (array_slice($errors, 0, 30) as $err) {
        echo "  - $err\n";
    }
}

if (count($tablesAfter) > 5) {
    echo "\n✅ DATABASE IMPORT COMPLETE!\n";
    echo "Tables: " . implode(', ', $tablesAfter) . "\n";
    echo "\nIMPORTANT: Delete this file after verifying the site works:\n";
    echo "  git rm db-setup.php && git commit -m 'Remove setup script' && git push\n";
} else {
    echo "\n⚠️  Something went wrong — fewer tables than expected.\n";
}

echo '</pre>';
