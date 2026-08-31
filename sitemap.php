<?php

require_once __DIR__ . '/includes/init.php';

header('Content-Type: application/xml; charset=utf-8');

$config = require CONFIG_PATH . '/config.php';
$siteUrl = rtrim($config['app']['url'], '/');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
echo '        xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

function outputUrl($loc, $lastmod = null, $changefreq = 'weekly', $priority = '0.5') {
    echo "    <url>\n";
    echo "        <loc>" . htmlspecialchars($loc) . "</loc>\n";
    if ($lastmod) {
        echo "        <lastmod>" . date('Y-m-d', strtotime($lastmod)) . "</lastmod>\n";
    }
    echo "        <changefreq>{$changefreq}</changefreq>\n";
    echo "        <priority>{$priority}</priority>\n";
    echo "    </url>\n";
}

// ============================================================
// STATIC PAGES - PORTUGUESE
// ============================================================

outputUrl($siteUrl . '/', date('Y-m-d'), 'daily', '1.0');

outputUrl($siteUrl . '/alojamento/', null, 'weekly', '0.8');
outputUrl($siteUrl . '/loja/', null, 'daily', '0.8');
outputUrl($siteUrl . '/atividades/', null, 'weekly', '0.8');
outputUrl($siteUrl . '/contactos/', null, 'monthly', '0.6');

outputUrl($siteUrl . '/termos-condicoes/', null, 'yearly', '0.3');
outputUrl($siteUrl . '/politica-privacidade/', null, 'yearly', '0.3');

// ============================================================
// STATIC PAGES - ENGLISH
// ============================================================

outputUrl($siteUrl . '/en/', null, 'daily', '0.9');

outputUrl($siteUrl . '/en/accommodation/', null, 'weekly', '0.7');
outputUrl($siteUrl . '/en/shop/', null, 'daily', '0.7');
outputUrl($siteUrl . '/en/activities/', null, 'weekly', '0.7');
outputUrl($siteUrl . '/en/contact/', null, 'monthly', '0.5');

// ============================================================
// DYNAMIC PAGES (Alojamentos)
// ============================================================
try {
    $dbConfig = $config['db'];
    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['name']};charset={$dbConfig['charset']};port={$dbConfig['port']}";
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT id, updated_at FROM alojamentos WHERE status = 'active' OR status = 1");
    $casas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($casas as $casa) {
        $lastmod = $casa['updated_at'] ? date('Y-m-d', strtotime($casa['updated_at'])) : null;
        
        // PT
        outputUrl($siteUrl . '/alojamento/?casa=' . $casa['id'], $lastmod, 'weekly', '0.9');
        // EN
        outputUrl($siteUrl . '/en/accommodation/?casa=' . $casa['id'], $lastmod, 'weekly', '0.9');
    }
} catch (Exception $e) {
    
}

echo '</urlset>';
