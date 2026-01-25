<?php
/**
 * Language Check and Fix Script
 * Run this to verify languages are properly set up
 */

require_once __DIR__ . '/includes/init.php';

use Core\Database;

$db = Database::getInstance();

echo "<h2>Language Check</h2>";
echo "<pre>";

// Get all languages
$languages = $db->fetchAll("SELECT * FROM languages");

if (empty($languages)) {
    echo "ERROR: No languages found in database!\n";
    echo "Inserting default languages...\n";

    // Insert PT
    $db->query("INSERT INTO languages (code, name, is_default, is_active) VALUES ('pt', 'Português', 1, 1) ON DUPLICATE KEY UPDATE is_active = 1");
    // Insert EN
    $db->query("INSERT INTO languages (code, name, is_default, is_active) VALUES ('en', 'English', 0, 1) ON DUPLICATE KEY UPDATE is_active = 1");

    echo "Languages inserted!\n\n";
    $languages = $db->fetchAll("SELECT * FROM languages");
}

echo "Languages in database:\n";
echo str_repeat("-", 60) . "\n";
printf("%-5s | %-8s | %-15s | %-10s | %-8s\n", "ID", "Code", "Name", "Default", "Active");
echo str_repeat("-", 60) . "\n";

foreach ($languages as $lang) {
    printf("%-5s | %-8s | %-15s | %-10s | %-8s\n",
        $lang['id'],
        $lang['code'],
        $lang['name'],
        $lang['is_default'] ? 'Yes' : 'No',
        $lang['is_active'] ? 'Yes' : 'No'
    );
}
echo str_repeat("-", 60) . "\n\n";

// Check for issues
$issues = [];

$pt = null;
$en = null;
foreach ($languages as $lang) {
    if ($lang['code'] === 'pt') $pt = $lang;
    if ($lang['code'] === 'en') $en = $lang;
}

if (!$pt) {
    $issues[] = "Portuguese (pt) language is missing!";
}
if (!$en) {
    $issues[] = "English (en) language is missing!";
}
if ($pt && !$pt['is_active']) {
    $issues[] = "Portuguese language is INACTIVE!";
}
if ($en && !$en['is_active']) {
    $issues[] = "English language is INACTIVE!";
}

if (empty($issues)) {
    echo "STATUS: OK - Both languages are properly configured.\n";
} else {
    echo "ISSUES FOUND:\n";
    foreach ($issues as $issue) {
        echo "  - $issue\n";
    }

    echo "\nAttempting to fix...\n";

    // Fix issues
    if (!$pt) {
        $db->query("INSERT INTO languages (code, name, is_default, is_active) VALUES ('pt', 'Português', 1, 1)");
        echo "  - Inserted Portuguese language\n";
    } elseif (!$pt['is_active']) {
        $db->update('languages', ['is_active' => 1], 'code = ?', ['pt']);
        echo "  - Activated Portuguese language\n";
    }

    if (!$en) {
        $db->query("INSERT INTO languages (code, name, is_default, is_active) VALUES ('en', 'English', 0, 1)");
        echo "  - Inserted English language\n";
    } elseif (!$en['is_active']) {
        $db->update('languages', ['is_active' => 1], 'code = ?', ['en']);
        echo "  - Activated English language\n";
    }

    echo "\nFixed! Refresh this page to verify.\n";
}

echo "</pre>";
echo "<p><a href='/admin/conteudos/'>Go to Admin Content Page</a></p>";
