<?php
/**
 * Database Structure Fix Script
 * Run this to check and fix missing columns in the database
 */

require_once __DIR__ . '/includes/init.php';

use Core\Database;

$db = Database::getInstance();

echo "<h2>Database Structure Check</h2>";
echo "<pre style='background:#f5f5f5;padding:15px;border-radius:5px;'>";

// Fix 1: Check languages table
echo "=== CHECKING LANGUAGES ===\n";
try {
    $languages = $db->fetchAll("SELECT * FROM languages");
    if (empty($languages)) {
        echo "No languages found. Inserting defaults...\n";
        $db->query("INSERT INTO languages (code, name, locale, is_default, is_active) VALUES
            ('pt', 'Português', 'pt_PT', 1, 1),
            ('en', 'English', 'en_GB', 0, 1)
            ON DUPLICATE KEY UPDATE is_active = 1");
        echo "Languages inserted!\n";
    } else {
        echo "Found " . count($languages) . " language(s): ";
        foreach ($languages as $l) {
            echo $l['code'] . "(" . ($l['is_active'] ? "active" : "inactive") . ") ";
        }
        echo "\n";

        // Make sure EN is active
        $db->query("UPDATE languages SET is_active = 1 WHERE code IN ('pt', 'en')");
    }
} catch (Exception $e) {
    echo "Error checking languages: " . $e->getMessage() . "\n";
}

// Fix 2: Check accommodation_translations table structure
echo "\n=== CHECKING ACCOMMODATION_TRANSLATIONS TABLE ===\n";
try {
    $columns = $db->fetchAll("SHOW COLUMNS FROM accommodation_translations");
    $columnNames = array_column($columns, 'Field');
    echo "Current columns: " . implode(", ", $columnNames) . "\n";

    $requiredColumns = [
        'name' => "VARCHAR(255) NOT NULL DEFAULT 'A Casa do Gi'",
        'tagline' => "VARCHAR(255) DEFAULT NULL",
        'description' => "TEXT",
        'house_rules' => "TEXT"
    ];

    foreach ($requiredColumns as $col => $definition) {
        if (!in_array($col, $columnNames)) {
            echo "Missing column '$col' - Adding...\n";
            try {
                $db->query("ALTER TABLE accommodation_translations ADD COLUMN $col $definition");
                echo "  Added '$col' successfully!\n";
            } catch (Exception $e) {
                echo "  Error adding '$col': " . $e->getMessage() . "\n";
            }
        }
    }
} catch (Exception $e) {
    echo "Error checking table: " . $e->getMessage() . "\n";
    echo "Table might not exist. Creating...\n";

    try {
        $db->query("CREATE TABLE IF NOT EXISTS accommodation_translations (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            accommodation_id INT UNSIGNED NOT NULL,
            language_id INT UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL,
            tagline VARCHAR(255) DEFAULT NULL,
            description TEXT,
            house_rules TEXT,
            UNIQUE KEY unique_acc_lang (accommodation_id, language_id)
        ) ENGINE=InnoDB");
        echo "Table created!\n";
    } catch (Exception $e2) {
        echo "Error creating table: " . $e2->getMessage() . "\n";
    }
}

// Fix 3: Check if accommodation record exists with translations
echo "\n=== CHECKING ACCOMMODATION RECORDS ===\n";
try {
    $accommodation = $db->fetch("SELECT * FROM accommodation LIMIT 1");

    if (!$accommodation) {
        echo "No accommodation found. Creating default...\n";
        $db->query("INSERT INTO accommodation (slug, max_guests, bedrooms, bathrooms, area_sqm, is_active)
                    VALUES ('casa-do-gi', 6, 3, 2, 100, 1)");
        $accommodation = $db->fetch("SELECT * FROM accommodation ORDER BY id DESC LIMIT 1");
        echo "Created accommodation ID: " . $accommodation['id'] . "\n";
    } else {
        echo "Accommodation found - ID: " . $accommodation['id'] . "\n";
    }

    // Check translations exist
    $langs = $db->fetchAll("SELECT * FROM languages WHERE is_active = 1");
    foreach ($langs as $lang) {
        $translation = $db->fetch(
            "SELECT * FROM accommodation_translations WHERE accommodation_id = ? AND language_id = ?",
            [$accommodation['id'], $lang['id']]
        );

        if (!$translation) {
            echo "Missing " . strtoupper($lang['code']) . " translation - Creating...\n";
            $name = $lang['code'] === 'pt' ? 'A Casa do Gi' : 'A Casa do Gi';
            $tagline = $lang['code'] === 'pt'
                ? 'Simplicidade, acolhimento e muito amor'
                : 'Simplicity, warmth and love';

            $db->insert('accommodation_translations', [
                'accommodation_id' => $accommodation['id'],
                'language_id' => $lang['id'],
                'name' => $name,
                'tagline' => $tagline,
                'description' => ''
            ]);
            echo "Created " . strtoupper($lang['code']) . " translation!\n";
        } else {
            echo strtoupper($lang['code']) . " translation exists - Name: " . ($translation['name'] ?? 'N/A') . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error checking accommodation: " . $e->getMessage() . "\n";
}

// Fix 4: Check media table category column
echo "\n=== CHECKING MEDIA TABLE ===\n";
try {
    $columns = $db->fetchAll("SHOW COLUMNS FROM media WHERE Field = 'category'");
    if (empty($columns)) {
        echo "Missing 'category' column in media table - Adding...\n";
        $db->query("ALTER TABLE media ADD COLUMN category ENUM('gallery', 'products', 'activities', 'content', 'other') DEFAULT 'other'");
        echo "Added 'category' column!\n";
    } else {
        echo "Media table has 'category' column.\n";
    }
} catch (Exception $e) {
    echo "Error checking media table: " . $e->getMessage() . "\n";
}

// Fix 5: Check amenities tables
echo "\n=== CHECKING AMENITIES TABLES ===\n";
try {
    // Check if amenities table exists
    $tableExists = $db->fetch("SHOW TABLES LIKE 'amenities'");
    if (!$tableExists) {
        echo "Creating amenities table...\n";
        $db->query("CREATE TABLE amenities (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            icon VARCHAR(50) DEFAULT NULL,
            sort_order INT UNSIGNED DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB");
        echo "Created amenities table!\n";
    } else {
        echo "Amenities table exists.\n";
    }

    // Check if amenity_translations table exists
    $tableExists = $db->fetch("SHOW TABLES LIKE 'amenity_translations'");
    if (!$tableExists) {
        echo "Creating amenity_translations table...\n";
        $db->query("CREATE TABLE amenity_translations (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            amenity_id INT UNSIGNED NOT NULL,
            language_id INT UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL,
            UNIQUE KEY unique_amenity_lang (amenity_id, language_id)
        ) ENGINE=InnoDB");
        echo "Created amenity_translations table!\n";
    } else {
        echo "Amenity_translations table exists.\n";
    }

    // Check if accommodation_amenities table exists
    $tableExists = $db->fetch("SHOW TABLES LIKE 'accommodation_amenities'");
    if (!$tableExists) {
        echo "Creating accommodation_amenities table...\n";
        $db->query("CREATE TABLE accommodation_amenities (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            accommodation_id INT UNSIGNED NOT NULL,
            amenity_id INT UNSIGNED NOT NULL,
            UNIQUE KEY unique_acc_amenity (accommodation_id, amenity_id)
        ) ENGINE=InnoDB");
        echo "Created accommodation_amenities table!\n";
    } else {
        echo "Accommodation_amenities table exists.\n";
    }

    // Check if there are any amenities
    $amenityCount = $db->fetch("SELECT COUNT(*) as c FROM amenities")['c'];
    if ($amenityCount == 0) {
        echo "No amenities found. Inserting defaults...\n";

        // Get language IDs
        $ptLang = $db->fetch("SELECT id FROM languages WHERE code = 'pt'");
        $enLang = $db->fetch("SELECT id FROM languages WHERE code = 'en'");

        $defaultAmenities = [
            ['icon' => '🌐', 'pt' => 'Wi-Fi Grátis', 'en' => 'Free Wi-Fi'],
            ['icon' => '🅿️', 'pt' => 'Estacionamento', 'en' => 'Parking'],
            ['icon' => '❄️', 'pt' => 'Ar Condicionado', 'en' => 'Air Conditioning'],
            ['icon' => '🔥', 'pt' => 'Aquecimento', 'en' => 'Heating'],
            ['icon' => '📺', 'pt' => 'TV', 'en' => 'TV'],
            ['icon' => '🍳', 'pt' => 'Cozinha Equipada', 'en' => 'Equipped Kitchen'],
            ['icon' => '🧺', 'pt' => 'Máquina de Lavar', 'en' => 'Washing Machine'],
            ['icon' => '🛏️', 'pt' => 'Roupa de Cama', 'en' => 'Bed Linen'],
            ['icon' => '🛁', 'pt' => 'Toalhas', 'en' => 'Towels'],
        ];

        foreach ($defaultAmenities as $i => $amenity) {
            $db->insert('amenities', [
                'icon' => $amenity['icon'],
                'sort_order' => $i + 1
            ]);
            $amenityId = $db->lastInsertId();

            if ($ptLang) {
                $db->insert('amenity_translations', [
                    'amenity_id' => $amenityId,
                    'language_id' => $ptLang['id'],
                    'name' => $amenity['pt']
                ]);
            }
            if ($enLang) {
                $db->insert('amenity_translations', [
                    'amenity_id' => $amenityId,
                    'language_id' => $enLang['id'],
                    'name' => $amenity['en']
                ]);
            }
        }
        echo "Inserted " . count($defaultAmenities) . " default amenities!\n";
    } else {
        echo "Found $amenityCount amenities.\n";
    }

} catch (Exception $e) {
    echo "Error checking amenities: " . $e->getMessage() . "\n";
}

echo "\n=== DONE ===\n";
echo "</pre>";

echo "<p style='margin-top:20px;'>";
echo "<a href='/alojamentogi/admin/alojamento/' style='color:#768A68;font-weight:bold;'>Go to Admin Alojamento &rarr;</a>";
echo "</p>";
