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
// DYNAMIC PAGES - PRODUCTS
// ============================================================

try {
    $db = db();

    $products = $db->query("
        SELECT p.slug, p.updated_at, p.created_at
        FROM products p
        WHERE p.is_active = 1
        ORDER BY p.created_at DESC
    ")->fetchAll();

    foreach ($products as $product) {

        $lastmod = $product['updated_at'] ?: $product['created_at'];
        outputUrl(
            $siteUrl . '/loja/produto/?slug=' . urlencode($product['slug']),
            $lastmod,
            'weekly',
            '0.7'
        );

        outputUrl(
            $siteUrl . '/en/shop/product/?slug=' . urlencode($product['slug']),
            $lastmod,
            'weekly',
            '0.6'
        );
    }

    // ============================================================
    // DYNAMIC PAGES - ACTIVITIES
    // ============================================================

    $activities = $db->query("
        SELECT a.slug, a.updated_at, a.created_at
        FROM activities a
        WHERE a.is_active = 1
        ORDER BY a.sort_order ASC, a.created_at DESC
    ")->fetchAll();

    foreach ($activities as $activity) {

        $lastmod = $activity['updated_at'] ?: $activity['created_at'];
        outputUrl(
            $siteUrl . '/atividades/?slug=' . urlencode($activity['slug']),
            $lastmod,
            'weekly',
            '0.7'
        );

        outputUrl(
            $siteUrl . '/en/activities/?slug=' . urlencode($activity['slug']),
            $lastmod,
            'weekly',
            '0.6'
        );
    }

} catch (Exception $e) {

    error_log('Sitemap generation error: ' . $e->getMessage());
}

echo '</urlset>';
