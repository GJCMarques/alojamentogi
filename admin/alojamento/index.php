<?php

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;

$db = Database::getInstance();

$selectedAccommodationNumber = (int)($_GET['casa'] ?? Session::get('admin_selected_accommodation') ?? 1);
if (!in_array($selectedAccommodationNumber, [1, 2])) {
    $selectedAccommodationNumber = 1;
}
Session::set('admin_selected_accommodation', $selectedAccommodationNumber);

$languages = $db->fetchAll("SELECT * FROM languages WHERE is_active = 1 ORDER BY is_default DESC");

$accommodation = $db->fetch(
    "SELECT * FROM accommodation WHERE accommodation_number = ?",
    [$selectedAccommodationNumber]
);

if (!$accommodation) {
    Session::flash('error', 'Alojamento não encontrado. Execute a migração 003_dual_accommodation_system.sql');
    redirect('/admin/');
}

foreach ($languages as $lang) {
    $exists = $db->fetch(
        "SELECT id FROM accommodation_translations WHERE accommodation_id = ? AND language_id = ?",
        [$accommodation['id'], $lang['id']]
    );

    if (!$exists) {
        $db->insert('accommodation_translations', [
            'accommodation_id' => $accommodation['id'],
            'language_id' => $lang['id'],
            'name' => 'A Casa do Gi ' . $selectedAccommodationNumber,
            'tagline' => '',
            'description' => ''
        ]);
    }
}

$translations = [];
$translationRows = $db->fetchAll(
    "SELECT * FROM accommodation_translations WHERE accommodation_id = ?",
    [$accommodation['id']]
);
foreach ($translationRows as $row) {
    $translations[$row['language_id']] = $row;
}

$amenities = $db->fetchAll(
    "SELECT a.*,
            (SELECT name FROM amenity_translations WHERE amenity_id = a.id AND language_id = (SELECT id FROM languages WHERE code = 'pt' LIMIT 1)) as name_pt,
            (SELECT name FROM amenity_translations WHERE amenity_id = a.id AND language_id = (SELECT id FROM languages WHERE code = 'en' LIMIT 1)) as name_en
     FROM amenities a
     ORDER BY a.category, a.sort_order, a.id"
);

$amenitiesByCategory = [];
foreach ($amenities as &$amenity) {
    $amenity['translated_name'] = $amenity['name_pt'] ?: $amenity['name_en'] ?: $amenity['icon'];
    $category = $amenity['category'] ?: 'general';
    if (!isset($amenitiesByCategory[$category])) {
        $amenitiesByCategory[$category] = [];
    }
    $amenitiesByCategory[$category][] = $amenity;
}
unset($amenity);

$categoryLabels = [
    'general' => 'Geral',
    'kitchen' => 'Cozinha',
    'bedroom' => 'Quarto',
    'bathroom' => 'Casa de Banho',
    'outdoor' => 'Exterior',
    'entertainment' => 'Entretenimento',
    'safety' => 'Segurança',
    'children' => 'Crianças',
    'sports' => 'Desporto',
    'services' => 'Serviços'
];

$accommodationAmenitiesRows = $db->fetchAll(
    "SELECT amenity_id, is_highlighted, sort_order FROM accommodation_amenities WHERE accommodation_id = ?",
    [$accommodation['id']]
);
$selectedAmenities = array_column($accommodationAmenitiesRows, 'amenity_id');
$highlightedAmenities = [];
foreach ($accommodationAmenitiesRows as $row) {
    if ($row['is_highlighted']) {
        $highlightedAmenities[] = $row['amenity_id'];
    }
}

$galleryImages = $db->fetchAll(
    "SELECT * FROM media WHERE category = 'gallery' AND accommodation_id = ? ORDER BY sort_order",
    [$accommodation['id']]
);

$bedrooms = $db->fetchAll(
    "SELECT b.*,
            (SELECT beds_description FROM bedroom_translations WHERE bedroom_id = b.id AND language_id = (SELECT id FROM languages WHERE code = 'pt' LIMIT 1)) as beds_pt,
            (SELECT beds_description FROM bedroom_translations WHERE bedroom_id = b.id AND language_id = (SELECT id FROM languages WHERE code = 'en' LIMIT 1)) as beds_en,
            (SELECT name FROM bedroom_translations WHERE bedroom_id = b.id AND language_id = (SELECT id FROM languages WHERE code = 'pt' LIMIT 1)) as name_pt,
            (SELECT name FROM bedroom_translations WHERE bedroom_id = b.id AND language_id = (SELECT id FROM languages WHERE code = 'en' LIMIT 1)) as name_en
     FROM bedrooms b
     WHERE b.accommodation_id = ?
     ORDER BY b.bedroom_number",
    [$accommodation['id']]
);

$bathrooms = $db->fetchAll(
    "SELECT b.*,
            (SELECT description FROM bathroom_translations WHERE bathroom_id = b.id AND language_id = (SELECT id FROM languages WHERE code = 'pt' LIMIT 1)) as desc_pt,
            (SELECT description FROM bathroom_translations WHERE bathroom_id = b.id AND language_id = (SELECT id FROM languages WHERE code = 'en' LIMIT 1)) as desc_en,
            (SELECT name FROM bathroom_translations WHERE bathroom_id = b.id AND language_id = (SELECT id FROM languages WHERE code = 'pt' LIMIT 1)) as name_pt,
            (SELECT name FROM bathroom_translations WHERE bathroom_id = b.id AND language_id = (SELECT id FROM languages WHERE code = 'en' LIMIT 1)) as name_en
     FROM bathrooms b
     WHERE b.accommodation_id = ?
     ORDER BY b.bathroom_number",
    [$accommodation['id']]
);

$houseRules = $db->fetchAll(
    "SELECT hr.*,
            (SELECT rule_text FROM house_rule_translations WHERE rule_id = hr.id AND language_id = (SELECT id FROM languages WHERE code = 'pt' LIMIT 1)) as rule_pt,
            (SELECT rule_text FROM house_rule_translations WHERE rule_id = hr.id AND language_id = (SELECT id FROM languages WHERE code = 'en' LIMIT 1)) as rule_en
     FROM house_rules hr
     WHERE hr.accommodation_id = ?
     ORDER BY hr.sort_order",
    [$accommodation['id']]
);

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && !isset($_POST['add_amenity']) && !isset($_POST['edit_amenity'])
    && !isset($_POST['add_bedroom']) && !isset($_POST['edit_bedroom'])
    && !isset($_POST['add_bathroom']) && !isset($_POST['edit_bathroom'])
    && !isset($_POST['add_rule']) && !isset($_POST['edit_rule'])
) {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {

        $db->update('accommodation', [
            'max_guests' => (int)$_POST['max_guests'],
            'bedrooms' => (int)$_POST['bedrooms'],
            'bathrooms' => (int)$_POST['bathrooms'],
            'area_sqm' => (float)($_POST['area_sqm'] ?? 0),
            'rating' => !empty($_POST['rating']) ? (float)$_POST['rating'] : null,
            'reviews_count' => (int)($_POST['reviews_count'] ?? 0),
            'city' => trim($_POST['city'] ?? 'Mogadouro'),
            'region' => trim($_POST['region'] ?? 'Trás-os-Montes'),
            'country' => trim($_POST['country'] ?? 'Portugal'),
            'host_type' => $_POST['host_type'] ?? 'standard',
            'checkin_type' => $_POST['checkin_type'] ?? 'self_checkin',
            'floor_number' => (int)($_POST['floor_number'] ?? 1),
            'has_elevator' => isset($_POST['has_elevator']) ? 1 : 0,
            'towels_linens_included' => isset($_POST['towels_linens_included']) ? 1 : 0,
            'min_nights' => (int)($_POST['min_nights'] ?? 1),
            'instant_booking' => isset($_POST['instant_booking']) ? 1 : 0,
            'check_in_time' => $_POST['check_in_time'] ?? '16:00:00',
            'check_out_time' => $_POST['check_out_time'] ?? '11:00:00',
            'license_number' => trim($_POST['license_number'] ?? ''),
            'latitude' => !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null,
            'longitude' => !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null,
            'guestready_url' => trim($_POST['guestready_url'] ?? ''),
            'booking_url' => null,
            'airbnb_url' => null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ], 'id = ?', [$accommodation['id']]);

        foreach ($languages as $lang) {
            $db->update('accommodation_translations', [
                'name' => trim($_POST['name_' . $lang['id']] ?? ''),
                'tagline' => trim($_POST['tagline_' . $lang['id']] ?? ''),
                'description' => trim($_POST['description_' . $lang['id']] ?? ''),
                'location_description' => trim($_POST['location_description_' . $lang['id']] ?? ''),
                'refund_policy' => trim($_POST['refund_policy_' . $lang['id']] ?? ''),
                'checkin_description' => trim($_POST['checkin_description_' . $lang['id']] ?? ''),
                'host_description' => trim($_POST['host_description_' . $lang['id']] ?? ''),
                'cancellation_policy' => trim($_POST['cancellation_policy_' . $lang['id']] ?? ''),
                'activity_section_title' => trim($_POST['activity_section_title_' . $lang['id']] ?? ''),
                'activity_section_description' => trim($_POST['activity_section_description_' . $lang['id']] ?? '')
            ], 'accommodation_id = ? AND language_id = ?', [$accommodation['id'], $lang['id']]);
        }

        $db->delete('accommodation_amenities', 'accommodation_id = ?', [$accommodation['id']]);
        if (!empty($_POST['amenities'])) {
            $sortOrder = 1;
            foreach ($_POST['amenities'] as $amenityId) {
                $isHighlighted = isset($_POST['highlighted_amenities']) && in_array($amenityId, $_POST['highlighted_amenities']) ? 1 : 0;
                $db->insert('accommodation_amenities', [
                    'accommodation_id' => $accommodation['id'],
                    'amenity_id' => (int)$amenityId,
                    'is_highlighted' => $isHighlighted,
                    'sort_order' => $sortOrder++
                ]);
            }
        }

        if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = ROOT_PATH . '/uploads/accommodation/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (in_array($_FILES['hero_image']['type'], $allowedTypes)) {
                $ext = pathinfo($_FILES['hero_image']['name'], PATHINFO_EXTENSION);
                $newName = 'hero_casa' . $selectedAccommodationNumber . '_' . time() . '.' . $ext;

                if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $uploadDir . $newName)) {

                    if ($accommodation['hero_image'] && strpos($accommodation['hero_image'], 'uploads/') === 0) {
                        $oldPath = ROOT_PATH . '/' . $accommodation['hero_image'];
                        if (file_exists($oldPath)) {
                            @unlink($oldPath);
                        }
                    }
                    $db->update('accommodation', ['hero_image' => 'uploads/accommodation/' . $newName], 'id = ?', [$accommodation['id']]);
                }
            }
        }

        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = ROOT_PATH . '/uploads/accommodation/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (in_array($_FILES['cover_image']['type'], $allowedTypes)) {
                $ext = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
                $newName = 'cover_casa' . $selectedAccommodationNumber . '_' . time() . '.' . $ext;

                if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $uploadDir . $newName)) {

                    if ($accommodation['cover_image'] && strpos($accommodation['cover_image'], 'uploads/') === 0) {
                        $oldPath = ROOT_PATH . '/' . $accommodation['cover_image'];
                        if (file_exists($oldPath)) {
                            @unlink($oldPath);
                        }
                    }
                    $db->update('accommodation', ['cover_image' => 'uploads/accommodation/' . $newName], 'id = ?', [$accommodation['id']]);
                }
            }
        }

        if (!empty($_FILES['gallery']['name'][0])) {
            $uploadDir = ROOT_PATH . '/uploads/accommodation/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $maxOrder = $db->fetch(
                "SELECT MAX(sort_order) as max_order FROM media WHERE category = 'gallery' AND accommodation_id = ?",
                [$accommodation['id']]
            )['max_order'] ?? 0;

            foreach ($_FILES['gallery']['tmp_name'] as $key => $tmpName) {
                if ($_FILES['gallery']['error'][$key] !== UPLOAD_ERR_OK) continue;
                if (!in_array($_FILES['gallery']['type'][$key], $allowedTypes)) continue;

                $ext = pathinfo($_FILES['gallery']['name'][$key], PATHINFO_EXTENSION);
                $newName = 'accommodation_' . $selectedAccommodationNumber . '_' . uniqid() . '.' . $ext;

                if (move_uploaded_file($tmpName, $uploadDir . $newName)) {
                    $maxOrder++;
                    $fileSize = $_FILES['gallery']['size'][$key];
                    $fileType = $_FILES['gallery']['type'][$key];
                    $originalName = $_FILES['gallery']['name'][$key];

                    $db->insert('media', [
                        'filename' => $newName,
                        'original_name' => $originalName,
                        'file_path' => '/uploads/accommodation/' . $newName,
                        'file_type' => $fileType,
                        'file_size' => $fileSize,
                        'category' => 'gallery',
                        'accommodation_id' => $accommodation['id'],
                        'sort_order' => $maxOrder
                    ]);
                }
            }
        }

        if (isset($_POST['gallery_alt_pt']) && is_array($_POST['gallery_alt_pt'])) {
            foreach ($_POST['gallery_alt_pt'] as $id => $altPt) {
                $id = (int)$id;
                $altEn = $_POST['gallery_alt_en'][$id] ?? '';

                $db->update('media', [
                    'alt_text_pt' => trim($altPt),
                    'alt_text_en' => trim($altEn)
                ], 'id = ?', [$id]);
            }
        }

        Session::flash('success', 'Alojamento atualizado com sucesso.');
        $activeTab = $_POST['active_tab'] ?? 'info';
        redirect('/admin/alojamento/?casa=' . $selectedAccommodationNumber . '#' . $activeTab);
    }
}

if (isset($_GET['delete_image']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $imageId = (int)$_GET['delete_image'];
        $image = $db->fetch("SELECT * FROM media WHERE id = ? AND accommodation_id = ?", [$imageId, $accommodation['id']]);

        if ($image) {
            $filePath = ROOT_PATH . $image['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $db->delete('media', 'id = ?', [$imageId]);
            Session::flash('success', 'Imagem eliminada.');
        }
    }
    redirect('/admin/alojamento/?casa=' . $selectedAccommodationNumber);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_amenity'])) {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $icon = trim($_POST['amenity_icon'] ?? '');
        $namePt = trim($_POST['amenity_name_pt'] ?? '');
        $nameEn = trim($_POST['amenity_name_en'] ?? '');
        $category = $_POST['amenity_category'] ?? 'general';

        if ($namePt || $nameEn) {
            $maxOrder = $db->fetch("SELECT MAX(sort_order) as max_order FROM amenities")['max_order'] ?? 0;

            $db->insert('amenities', [
                'icon' => $icon,
                'category' => $category,
                'sort_order' => $maxOrder + 1
            ]);
            $amenityId = $db->lastInsertId();

            foreach ($languages as $lang) {
                $name = $lang['code'] === 'pt' ? $namePt : $nameEn;
                if ($name) {
                    $db->insert('amenity_translations', [
                        'amenity_id' => $amenityId,
                        'language_id' => $lang['id'],
                        'name' => $name
                    ]);
                }
            }

            Session::flash('success', 'Comodidade adicionada com sucesso.');
        }
    }
    redirect('/admin/alojamento/?casa=' . $selectedAccommodationNumber);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_amenity'])) {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $amenityId = (int)$_POST['amenity_id'];
        $icon = trim($_POST['amenity_icon'] ?? '');
        $namePt = trim($_POST['amenity_name_pt'] ?? '');
        $nameEn = trim($_POST['amenity_name_en'] ?? '');
        $category = $_POST['amenity_category'] ?? 'general';

        $db->update('amenities', ['icon' => $icon, 'category' => $category], 'id = ?', [$amenityId]);

        foreach ($languages as $lang) {
            $name = $lang['code'] === 'pt' ? $namePt : $nameEn;
            $existing = $db->fetch(
                "SELECT id FROM amenity_translations WHERE amenity_id = ? AND language_id = ?",
                [$amenityId, $lang['id']]
            );

            if ($existing) {
                $db->update('amenity_translations', ['name' => $name], 'id = ?', [$existing['id']]);
            } else {
                $db->insert('amenity_translations', [
                    'amenity_id' => $amenityId,
                    'language_id' => $lang['id'],
                    'name' => $name
                ]);
            }
        }

        Session::flash('success', 'Comodidade atualizada com sucesso.');
    }
    redirect('/admin/alojamento/?casa=' . $selectedAccommodationNumber);
}

if (isset($_GET['delete_amenity']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $amenityId = (int)$_GET['delete_amenity'];
        $db->delete('amenity_translations', 'amenity_id = ?', [$amenityId]);
        $db->delete('accommodation_amenities', 'amenity_id = ?', [$amenityId]);
        $db->delete('amenities', 'id = ?', [$amenityId]);
        Session::flash('success', 'Comodidade eliminada.');
    }
    redirect('/admin/alojamento/?casa=' . $selectedAccommodationNumber);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_bedroom'])) {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $maxNum = $db->fetch(
            "SELECT MAX(bedroom_number) as max_num FROM bedrooms WHERE accommodation_id = ?",
            [$accommodation['id']]
        )['max_num'] ?? 0;

        $db->insert('bedrooms', [
            'accommodation_id' => $accommodation['id'],
            'bedroom_number' => $maxNum + 1
        ]);
        $bedroomId = $db->lastInsertId();

        foreach ($languages as $lang) {
            $name = trim($_POST['bedroom_name_' . $lang['code']] ?? '');
            $beds = trim($_POST['bedroom_beds_' . $lang['code']] ?? '');
            if ($name || $beds) {
                $db->insert('bedroom_translations', [
                    'bedroom_id' => $bedroomId,
                    'language_id' => $lang['id'],
                    'name' => $name,
                    'beds_description' => $beds
                ]);
            }
        }

        Session::flash('success', 'Quarto adicionado com sucesso.');
    }
    redirect('/admin/alojamento/?casa=' . $selectedAccommodationNumber . '#spaces');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_bedroom'])) {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $bedroomId = (int)$_POST['bedroom_id'];

        foreach ($languages as $lang) {
            $name = trim($_POST['bedroom_name_' . $lang['code']] ?? '');
            $beds = trim($_POST['bedroom_beds_' . $lang['code']] ?? '');

            $existing = $db->fetch(
                "SELECT id FROM bedroom_translations WHERE bedroom_id = ? AND language_id = ?",
                [$bedroomId, $lang['id']]
            );

            if ($existing) {
                $db->update('bedroom_translations', [
                    'name' => $name,
                    'beds_description' => $beds
                ], 'id = ?', [$existing['id']]);
            } else {
                $db->insert('bedroom_translations', [
                    'bedroom_id' => $bedroomId,
                    'language_id' => $lang['id'],
                    'name' => $name,
                    'beds_description' => $beds
                ]);
            }
        }

        Session::flash('success', 'Quarto atualizado com sucesso.');
    }
    redirect('/admin/alojamento/?casa=' . $selectedAccommodationNumber . '#spaces');
}

if (isset($_GET['delete_bedroom']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $bedroomId = (int)$_GET['delete_bedroom'];
        $db->delete('bedroom_translations', 'bedroom_id = ?', [$bedroomId]);
        $db->delete('bedrooms', 'id = ? AND accommodation_id = ?', [$bedroomId, $accommodation['id']]);
        Session::flash('success', 'Quarto eliminado.');
    }
    redirect('/admin/alojamento/?casa=' . $selectedAccommodationNumber . '#spaces');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_bathroom'])) {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $maxNum = $db->fetch(
            "SELECT MAX(bathroom_number) as max_num FROM bathrooms WHERE accommodation_id = ?",
            [$accommodation['id']]
        )['max_num'] ?? 0;

        $db->insert('bathrooms', [
            'accommodation_id' => $accommodation['id'],
            'bathroom_number' => $maxNum + 1,
            'has_shower' => isset($_POST['has_shower']) ? 1 : 0,
            'has_bathtub' => isset($_POST['has_bathtub']) ? 1 : 0,
            'has_bidet' => isset($_POST['has_bidet']) ? 1 : 0,
            'is_ensuite' => isset($_POST['is_ensuite']) ? 1 : 0
        ]);
        $bathroomId = $db->lastInsertId();

        foreach ($languages as $lang) {
            $name = trim($_POST['bathroom_name_' . $lang['code']] ?? '');
            $desc = trim($_POST['bathroom_desc_' . $lang['code']] ?? '');
            if ($name || $desc) {
                $db->insert('bathroom_translations', [
                    'bathroom_id' => $bathroomId,
                    'language_id' => $lang['id'],
                    'name' => $name,
                    'description' => $desc
                ]);
            }
        }

        Session::flash('success', 'Casa de banho adicionada com sucesso.');
    }
    redirect('/admin/alojamento/?casa=' . $selectedAccommodationNumber . '#spaces');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_bathroom'])) {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $bathroomId = (int)$_POST['bathroom_id'];

        $db->update('bathrooms', [
            'has_shower' => isset($_POST['has_shower']) ? 1 : 0,
            'has_bathtub' => isset($_POST['has_bathtub']) ? 1 : 0,
            'has_bidet' => isset($_POST['has_bidet']) ? 1 : 0,
            'is_ensuite' => isset($_POST['is_ensuite']) ? 1 : 0
        ], 'id = ? AND accommodation_id = ?', [$bathroomId, $accommodation['id']]);

        foreach ($languages as $lang) {
            $name = trim($_POST['bathroom_name_' . $lang['code']] ?? '');
            $desc = trim($_POST['bathroom_desc_' . $lang['code']] ?? '');

            $existing = $db->fetch(
                "SELECT id FROM bathroom_translations WHERE bathroom_id = ? AND language_id = ?",
                [$bathroomId, $lang['id']]
            );

            if ($existing) {
                $db->update('bathroom_translations', [
                    'name' => $name,
                    'description' => $desc
                ], 'id = ?', [$existing['id']]);
            } else {
                $db->insert('bathroom_translations', [
                    'bathroom_id' => $bathroomId,
                    'language_id' => $lang['id'],
                    'name' => $name,
                    'description' => $desc
                ]);
            }
        }

        Session::flash('success', 'Casa de banho atualizada com sucesso.');
    }
    redirect('/admin/alojamento/?casa=' . $selectedAccommodationNumber . '#spaces');
}

if (isset($_GET['delete_bathroom']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $bathroomId = (int)$_GET['delete_bathroom'];
        $db->delete('bathroom_translations', 'bathroom_id = ?', [$bathroomId]);
        $db->delete('bathrooms', 'id = ? AND accommodation_id = ?', [$bathroomId, $accommodation['id']]);
        Session::flash('success', 'Casa de banho eliminada.');
    }
    redirect('/admin/alojamento/?casa=' . $selectedAccommodationNumber . '#spaces');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_rule'])) {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $maxOrder = $db->fetch(
            "SELECT MAX(sort_order) as max_order FROM house_rules WHERE accommodation_id = ?",
            [$accommodation['id']]
        )['max_order'] ?? 0;

        $db->insert('house_rules', [
            'accommodation_id' => $accommodation['id'],
            'is_highlighted' => isset($_POST['rule_highlighted']) ? 1 : 0,
            'sort_order' => $maxOrder + 1
        ]);
        $ruleId = $db->lastInsertId();

        foreach ($languages as $lang) {
            $text = trim($_POST['rule_text_' . $lang['code']] ?? '');
            if ($text) {
                $db->insert('house_rule_translations', [
                    'rule_id' => $ruleId,
                    'language_id' => $lang['id'],
                    'rule_text' => $text
                ]);
            }
        }

        Session::flash('success', 'Regra adicionada com sucesso.');
    }
    redirect('/admin/alojamento/?casa=' . $selectedAccommodationNumber . '#rules');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_rule'])) {
    if (CSRF::validate($_POST['csrf_token'] ?? '')) {
        $ruleId = (int)$_POST['rule_id'];

        $db->update('house_rules', [
            'is_highlighted' => isset($_POST['rule_highlighted']) ? 1 : 0
        ], 'id = ? AND accommodation_id = ?', [$ruleId, $accommodation['id']]);

        foreach ($languages as $lang) {
            $text = trim($_POST['rule_text_' . $lang['code']] ?? '');

            $existing = $db->fetch(
                "SELECT id FROM house_rule_translations WHERE rule_id = ? AND language_id = ?",
                [$ruleId, $lang['id']]
            );

            if ($existing) {
                $db->update('house_rule_translations', [
                    'rule_text' => $text
                ], 'id = ?', [$existing['id']]);
            } else {
                $db->insert('house_rule_translations', [
                    'rule_id' => $ruleId,
                    'language_id' => $lang['id'],
                    'rule_text' => $text
                ]);
            }
        }

        Session::flash('success', 'Regra atualizada com sucesso.');
    }
    redirect('/admin/alojamento/?casa=' . $selectedAccommodationNumber . '#rules');
}

if (isset($_GET['delete_rule']) && isset($_GET['token'])) {
    if (CSRF::validate($_GET['token'])) {
        $ruleId = (int)$_GET['delete_rule'];
        $db->delete('house_rule_translations', 'rule_id = ?', [$ruleId]);
        $db->delete('house_rules', 'id = ? AND accommodation_id = ?', [$ruleId, $accommodation['id']]);
        Session::flash('success', 'Regra eliminada.');
    }
    redirect('/admin/alojamento/?casa=' . $selectedAccommodationNumber . '#rules');
}

$pageTitle = 'Alojamento';
$currentPage = 'alojamento';
include dirname(__DIR__) . '/includes/header.php';
?>

<!-- Accommodation Switcher -->
<div class="bg-white rounded-lg shadow-sm mb-6 p-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <span class="text-sm font-medium text-gray-600">A gerir:</span>
            <div class="flex gap-2">
                <a href="?casa=1" class="px-6 py-2.5 rounded-lg font-medium transition-all <?= $selectedAccommodationNumber === 1 ? 'bg-secondary-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>">
                    Casa do Gi 1
                </a>
                <a href="?casa=2" class="px-6 py-2.5 rounded-lg font-medium transition-all <?= $selectedAccommodationNumber === 2 ? 'bg-secondary-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>">
                    Casa do Gi 2
                </a>
            </div>
        </div>
        <a href="<?= basePath() ?>/alojamento/" target="_blank" class="text-secondary-600 hover:text-secondary-700 text-sm flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            Ver no site
        </a>
    </div>
</div>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-primary">Casa do Gi <?= $selectedAccommodationNumber ?></h1>
        <p class="text-gray-600">Gerir informações do alojamento</p>
    </div>
</div>

<!-- Tab Navigation -->
<div class="bg-white rounded-lg shadow-sm mb-6">
    <div class="border-b border-gray-200">
        <nav class="flex -mb-px overflow-x-auto" id="mainTabs">
            <button type="button" id="tab-btn-info" class="nav-tab-btn px-6 py-3 text-sm font-medium border-b-2 border-secondary-600 text-secondary-600 whitespace-nowrap">
                Informações
            </button>
            <button type="button" id="tab-btn-content" class="nav-tab-btn px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap">
                Conteúdos
            </button>
            <button type="button" id="tab-btn-spaces" class="nav-tab-btn px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap">
                Quartos & Casas de Banho
            </button>
            <button type="button" id="tab-btn-gallery" class="nav-tab-btn px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap">
                Galeria
            </button>
            <button type="button" id="tab-btn-amenities" class="nav-tab-btn px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap">
                Comodidades
            </button>
            <button type="button" id="tab-btn-rules" class="nav-tab-btn px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap">
                Regras da Casa
            </button>
            <button type="button" id="tab-btn-policies" class="nav-tab-btn px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap">
                Políticas
            </button>
        </nav>
    </div>
</div>

<form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
    <input type="hidden" name="active_tab" id="activeTabField" value="info">

    <!-- Tab: Info -->
    <div id="panel-info" class="admin-tab-panel">
        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Details -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-medium text-gray-800 mb-4">Detalhes Básicos</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Máx. Hóspedes</label>
                            <input type="number" name="max_guests" value="<?= e($accommodation['max_guests'] ?? 6) ?>" min="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quartos</label>
                            <input type="number" name="bedrooms" value="<?= e($accommodation['bedrooms'] ?? 3) ?>" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Casas de Banho</label>
                            <input type="number" name="bathrooms" value="<?= e($accommodation['bathrooms'] ?? 2) ?>" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Área (m²)</label>
                            <input type="number" name="area_sqm" value="<?= e($accommodation['area_sqm'] ?? 100) ?>" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        </div>
                    </div>
                </div>

                <!-- URL de Reserva -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-medium text-gray-800 mb-4">URL de Reserva</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">GuestReady URL</label>
                            <input type="url" name="guestready_url" value="<?= e($accommodation['guestready_url'] ?? '') ?>"
                                   placeholder="https://book.guestready.com/..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        </div>
                    </div>
                </div>

                <!-- Rating & Reviews -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-medium text-gray-800 mb-4">Avaliação & Reviews</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Classificação (0-5)</label>
                            <input type="number" name="rating" value="<?= e($accommodation['rating'] ?? '') ?>" min="0" max="5" step="0.1"
                                   placeholder="Ex: 4.8"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Número de Reviews</label>
                            <input type="number" name="reviews_count" value="<?= e($accommodation['reviews_count'] ?? 0) ?>" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        </div>
                    </div>
                </div>

                <!-- Location -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-medium text-gray-800 mb-4">Localização</h2>
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                            <input type="text" name="city" value="<?= e($accommodation['city'] ?? 'Mogadouro') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Região</label>
                            <input type="text" name="region" value="<?= e($accommodation['region'] ?? 'Trás-os-Montes') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">País</label>
                            <input type="text" name="country" value="<?= e($accommodation['country'] ?? 'Portugal') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                            <input type="text" name="latitude" value="<?= e($accommodation['latitude'] ?? '41.34217') ?>"
                                   placeholder="41.34217"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                            <input type="text" name="longitude" value="<?= e($accommodation['longitude'] ?? '-6.71347') ?>"
                                   placeholder="-6.71347"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        </div>
                    </div>
                </div>

                <!-- Building Info -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-medium text-gray-800 mb-4">Informações do Edifício</h2>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Andar</label>
                            <input type="number" name="floor_number" value="<?= e($accommodation['floor_number'] ?? 1) ?>" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Licença AL</label>
                            <input type="text" name="license_number" value="<?= e($accommodation['license_number'] ?? '') ?>"
                                   placeholder="Ex: 146729/AL"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="has_elevator" value="1" <?= ($accommodation['has_elevator'] ?? 0) ? 'checked' : '' ?>
                                   class="w-4 h-4 text-secondary-600 border-gray-300 rounded focus:ring-secondary-500">
                            <span class="ml-2 text-sm text-gray-700">Tem elevador</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="towels_linens_included" value="1" <?= ($accommodation['towels_linens_included'] ?? 1) ? 'checked' : '' ?>
                                   class="w-4 h-4 text-secondary-600 border-gray-300 rounded focus:ring-secondary-500">
                            <span class="ml-2 text-sm text-gray-700">Toalhas e roupa de cama incluídas</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Host Type -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-medium text-gray-800 mb-4">Tipo de Anfitrião</h2>
                    <select name="host_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        <option value="standard" <?= ($accommodation['host_type'] ?? '') === 'standard' ? 'selected' : '' ?>>Standard</option>
                        <option value="superhost" <?= ($accommodation['host_type'] ?? '') === 'superhost' ? 'selected' : '' ?>>Superhost</option>
                        <option value="professional" <?= ($accommodation['host_type'] ?? '') === 'professional' ? 'selected' : '' ?>>Profissional</option>
                    </select>
                </div>

                <!-- Check-in/out -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-medium text-gray-800 mb-4">Check-in / Check-out</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Check-in</label>
                            <select name="checkin_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                                <option value="self_checkin" <?= ($accommodation['checkin_type'] ?? '') === 'self_checkin' ? 'selected' : '' ?>>Self Check-in</option>
                                <option value="meet_host" <?= ($accommodation['checkin_type'] ?? '') === 'meet_host' ? 'selected' : '' ?>>Encontro com Anfitrião</option>
                                <option value="key_lockbox" <?= ($accommodation['checkin_type'] ?? '') === 'key_lockbox' ? 'selected' : '' ?>>Cofre de Chaves</option>
                                <option value="smart_lock" <?= ($accommodation['checkin_type'] ?? '') === 'smart_lock' ? 'selected' : '' ?>>Fechadura Inteligente</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Check-in</label>
                                <input type="time" name="check_in_time" value="<?= e(substr($accommodation['check_in_time'] ?? '16:00', 0, 5)) ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Check-out</label>
                                <input type="time" name="check_out_time" value="<?= e(substr($accommodation['check_out_time'] ?? '11:00', 0, 5)) ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Options -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-medium text-gray-800 mb-4">Opções de Reserva</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Noites Mínimas</label>
                            <input type="number" name="min_nights" value="<?= e($accommodation['min_nights'] ?? 1) ?>" min="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        </div>
                        <label class="flex items-center">
                            <input type="checkbox" name="instant_booking" value="1" <?= ($accommodation['instant_booking'] ?? 0) ? 'checked' : '' ?>
                                   class="w-4 h-4 text-secondary-600 border-gray-300 rounded focus:ring-secondary-500">
                            <span class="ml-2 text-sm text-gray-700">Reserva instantânea</span>
                        </label>
                    </div>
                </div>

                <!-- Status -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-medium text-gray-800 mb-4">Estado</h2>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" <?= $accommodation['is_active'] ? 'checked' : '' ?>
                               class="w-4 h-4 text-secondary-600 border-gray-300 rounded focus:ring-secondary-500">
                        <span class="ml-2 text-sm text-gray-700">Visível no site</span>
                    </label>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <button type="submit" class="w-full px-4 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700">
                        Guardar Alterações
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab: Content -->
    <div id="panel-content" class="admin-tab-panel hidden">
        <div class="bg-white rounded-lg shadow-sm">
            <div class="border-b border-gray-200">
                <nav class="flex" id="langTabs">
                    <?php foreach ($languages as $i => $lang): ?>
                    <button type="button" onclick="switchLangTab('<?= $lang['code'] ?>')"
                            class="lang-tab px-6 py-3 text-sm font-medium border-b-2 <?= $i === 0 ? 'border-secondary-600 text-secondary-600' : 'border-transparent text-gray-500 hover:text-gray-700' ?>"
                            data-lang="<?= $lang['code'] ?>">
                        <?= strtoupper($lang['code']) ?>
                    </button>
                    <?php endforeach; ?>
                </nav>
            </div>

            <?php foreach ($languages as $i => $lang): ?>
            <div class="lang-content p-6 <?= $i > 0 ? 'hidden' : '' ?>" data-lang="<?= $lang['code'] ?>">
                <div class="space-y-6">
                    <!-- Basic Info -->
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                            <input type="text" name="name_<?= $lang['id'] ?>" value="<?= e($translations[$lang['id']]['name'] ?? '') ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tagline</label>
                            <input type="text" name="tagline_<?= $lang['id'] ?>" value="<?= e($translations[$lang['id']]['tagline'] ?? '') ?>"
                                   placeholder="Uma frase curta descritiva..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição Geral</label>
                        <textarea name="description_<?= $lang['id'] ?>" rows="5"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500"><?= e($translations[$lang['id']]['description'] ?? '') ?></textarea>
                    </div>

                    <!-- Check-in Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição do Check-in</label>
                        <input type="text" name="checkin_description_<?= $lang['id'] ?>" value="<?= e($translations[$lang['id']]['checkin_description'] ?? '') ?>"
                               placeholder="Ex: Self check-in com cofre de chaves..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                    </div>

                    <!-- Location Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição da Localização</label>
                        <textarea name="location_description_<?= $lang['id'] ?>" rows="3"
                                  placeholder="Descreva o bairro, acessos, pontos de interesse próximos..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500"><?= e($translations[$lang['id']]['location_description'] ?? '') ?></textarea>
                    </div>

                    <!-- Host Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sobre o Anfitrião</label>
                        <textarea name="host_description_<?= $lang['id'] ?>" rows="3"
                                  placeholder="Apresentação do anfitrião..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500"><?= e($translations[$lang['id']]['host_description'] ?? '') ?></textarea>
                    </div>

                    <!-- Activity Section -->
                    <div class="pt-4 border-t border-gray-200">
                        <h3 class="text-base font-medium text-gray-800 mb-3">Secção "Mogadouro & Envolvência"</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                                <input type="text" name="activity_section_title_<?= $lang['id'] ?>" value="<?= e($translations[$lang['id']]['activity_section_title'] ?? '') ?>"
                                       placeholder="<?= $lang['code'] === 'pt' ? 'Mogadouro & Envolvência' : 'Mogadouro & Surroundings' ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                                <textarea name="activity_section_description_<?= $lang['id'] ?>" rows="4"
                                          placeholder="Texto introdutório sobre Mogadouro e arredores..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500"><?= e($translations[$lang['id']]['activity_section_description'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-6">
            <button type="submit" class="px-6 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700">
                Guardar Alterações
            </button>
        </div>
    </div>

    <!-- Tab: Spaces (Bedrooms & Bathrooms) -->
    <div id="panel-spaces" class="admin-tab-panel hidden">
        <!-- Bedrooms -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-medium text-gray-800">Quartos</h2>
                <button type="button" id="btnAddBedroom" class="px-4 py-2 bg-secondary-600 text-white text-sm rounded-lg hover:bg-secondary-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Adicionar Quarto
                </button>
            </div>

            <?php if (!empty($bedrooms)): ?>
            <div class="space-y-3">
                <?php foreach ($bedrooms as $bedroom): ?>
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-secondary-100 text-secondary-700 text-xs font-bold"><?= $bedroom['bedroom_number'] ?></span>
                            <h3 class="font-medium text-gray-800"><?= e($bedroom['name_pt'] ?? 'Quarto ' . $bedroom['bedroom_number']) ?></h3>
                        </div>
                        <div class="ml-9 space-y-0.5">
                            <p class="text-sm text-gray-600"><span class="font-medium text-gray-500">PT:</span> <?= e($bedroom['beds_pt'] ?? '-') ?></p>
                            <p class="text-sm text-gray-600"><span class="font-medium text-gray-500">EN:</span> <?= e($bedroom['beds_en'] ?? '-') ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 ml-4">
                        <button type="button" class="btn-edit-bedroom p-2 text-gray-400 hover:text-secondary-600 rounded-lg hover:bg-secondary-50"
                                data-edit-bedroom="<?= e(json_encode([
                                    'id' => $bedroom['id'],
                                    'name_pt' => $bedroom['name_pt'] ?? '',
                                    'name_en' => $bedroom['name_en'] ?? '',
                                    'beds_pt' => $bedroom['beds_pt'] ?? '',
                                    'beds_en' => $bedroom['beds_en'] ?? '',
                                    'number' => $bedroom['bedroom_number']
                                ])) ?>">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button type="button" class="btn-delete-bedroom p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50"
                                data-delete-bedroom-id="<?= $bedroom['id'] ?>"
                                data-delete-bedroom-name="Quarto <?= $bedroom['bedroom_number'] ?>">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-gray-500 text-sm">Nenhum quarto definido.</p>
            <?php endif; ?>
        </div>

        <!-- Bathrooms -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-medium text-gray-800">Casas de Banho</h2>
                <button type="button" id="btnAddBathroom" class="px-4 py-2 bg-secondary-600 text-white text-sm rounded-lg hover:bg-secondary-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Adicionar Casa de Banho
                </button>
            </div>

            <?php if (!empty($bathrooms)): ?>
            <div class="space-y-3">
                <?php foreach ($bathrooms as $bathroom): ?>
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-100 text-blue-700 text-xs font-bold"><?= $bathroom['bathroom_number'] ?></span>
                            <h3 class="font-medium text-gray-800"><?= e($bathroom['name_pt'] ?? 'WC ' . $bathroom['bathroom_number']) ?></h3>
                        </div>
                        <div class="ml-9 space-y-0.5">
                            <p class="text-sm text-gray-600"><span class="font-medium text-gray-500">PT:</span> <?= e($bathroom['desc_pt'] ?? '-') ?></p>
                            <p class="text-sm text-gray-600"><span class="font-medium text-gray-500">EN:</span> <?= e($bathroom['desc_en'] ?? '-') ?></p>
                        </div>
                        <div class="ml-9 mt-2 flex flex-wrap gap-1.5">
                            <?php if ($bathroom['has_shower']): ?><span class="px-2 py-0.5 text-xs bg-blue-50 text-blue-700 rounded">Chuveiro</span><?php endif; ?>
                            <?php if ($bathroom['has_bathtub']): ?><span class="px-2 py-0.5 text-xs bg-blue-50 text-blue-700 rounded">Banheira</span><?php endif; ?>
                            <?php if ($bathroom['has_bidet']): ?><span class="px-2 py-0.5 text-xs bg-blue-50 text-blue-700 rounded">Bidé</span><?php endif; ?>
                            <?php if ($bathroom['is_ensuite']): ?><span class="px-2 py-0.5 text-xs bg-purple-50 text-purple-700 rounded">Ensuite</span><?php endif; ?>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 ml-4">
                        <button type="button" class="btn-edit-bathroom p-2 text-gray-400 hover:text-secondary-600 rounded-lg hover:bg-secondary-50"
                                data-edit-bathroom="<?= e(json_encode([
                                    'id' => $bathroom['id'],
                                    'name_pt' => $bathroom['name_pt'] ?? '',
                                    'name_en' => $bathroom['name_en'] ?? '',
                                    'desc_pt' => $bathroom['desc_pt'] ?? '',
                                    'desc_en' => $bathroom['desc_en'] ?? '',
                                    'number' => $bathroom['bathroom_number'],
                                    'has_shower' => (int)$bathroom['has_shower'],
                                    'has_bathtub' => (int)$bathroom['has_bathtub'],
                                    'has_bidet' => (int)$bathroom['has_bidet'],
                                    'is_ensuite' => (int)$bathroom['is_ensuite']
                                ])) ?>">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button type="button" class="btn-delete-bathroom p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50"
                                data-delete-bathroom-id="<?= $bathroom['id'] ?>"
                                data-delete-bathroom-name="WC <?= $bathroom['bathroom_number'] ?>">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-gray-500 text-sm">Nenhuma casa de banho definida.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab: Gallery -->
    <div id="panel-gallery" class="admin-tab-panel hidden">
        <!-- Main Images: Hero & Cover -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-medium text-gray-800 mb-2">Imagens Principais - Casa <?= $selectedAccommodationNumber ?></h2>
            <p class="text-sm text-gray-500 mb-6">Defina as imagens de destaque para esta casa.</p>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Hero Image -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <h3 class="font-medium text-gray-700">Imagem do Hero</h3>
                        <p class="text-xs text-gray-500">Aparece no topo da página da Casa <?= $selectedAccommodationNumber ?></p>
                    </div>
                    <div class="p-4">
                        <?php
                        $heroImage = $accommodation['hero_image'] ?? '';
                        $heroUrl = '';
                        if ($heroImage) {
                            if (strpos($heroImage, 'uploads/') === 0) {
                                $heroUrl = basePath() . '/' . $heroImage;
                            } else {
                                $heroUrl = asset($heroImage);
                            }
                        }
                        ?>
                        <div class="relative aspect-video bg-gray-100 rounded-lg overflow-hidden mb-3">
                            <?php if ($heroUrl): ?>
                            <img loading="lazy" decoding="async" src="<?= $heroUrl ?>" alt="Hero Image" id="preview-hero-img" class="w-full h-full object-cover">
                            <?php else: ?>
                            <div id="placeholder-hero" class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <img loading="lazy" decoding="async" src="" alt="Hero Image" id="preview-hero-img" class="w-full h-full object-cover hidden absolute inset-0">
                            <?php endif; ?>
                        </div>
                        <input type="file" name="hero_image" accept="image/jpeg,image/png,image/webp"
                               onchange="previewAccommodationImage(this, 'hero')"
                               class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-secondary-50 file:text-secondary-700 hover:file:bg-secondary-100 cursor-pointer">
                        <p class="text-xs text-gray-400 mt-2">JPG, PNG ou WebP (recomendado: 1920x1080)</p>
                    </div>
                </div>

                <!-- Cover Image -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <h3 class="font-medium text-gray-700">Imagem de Capa</h3>
                        <p class="text-xs text-gray-500">Aparece no cartão de seleção da página principal</p>
                    </div>
                    <div class="p-4">
                        <?php
                        $coverImage = $accommodation['cover_image'] ?? '';
                        $coverUrl = '';
                        if ($coverImage) {
                            if (strpos($coverImage, 'uploads/') === 0) {
                                $coverUrl = basePath() . '/' . $coverImage;
                            } else {
                                $coverUrl = asset($coverImage);
                            }
                        }
                        ?>
                        <div class="relative aspect-video bg-gray-100 rounded-lg overflow-hidden mb-3">
                            <?php if ($coverUrl): ?>
                            <img loading="lazy" decoding="async" src="<?= $coverUrl ?>" alt="Cover Image" id="preview-cover-img" class="w-full h-full object-cover">
                            <?php else: ?>
                            <div id="placeholder-cover" class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <img loading="lazy" decoding="async" src="" alt="Cover Image" id="preview-cover-img" class="w-full h-full object-cover hidden absolute inset-0">
                            <?php endif; ?>
                        </div>
                        <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp"
                               onchange="previewAccommodationImage(this, 'cover')"
                               class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-secondary-50 file:text-secondary-700 hover:file:bg-secondary-100 cursor-pointer">
                        <p class="text-xs text-gray-400 mt-2">JPG, PNG ou WebP (recomendado: 800x600)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gallery Images -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-medium text-gray-800 mb-4">Galeria de Imagens - Casa <?= $selectedAccommodationNumber ?></h2>

            <?php if (!empty($galleryImages)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                <?php foreach ($galleryImages as $image): ?>
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    <div class="relative group aspect-video bg-gray-100">
                        <img loading="lazy" decoding="async" src="<?= basePath() . e($image['file_path']) ?>" alt="" class="w-full h-full object-cover">
                        <button type="button"
                                data-delete-image-id="<?= $image['id'] ?>"
                                data-delete-image-name="<?= e($image['original_name'] ?? 'imagem') ?>"
                                class="btn-delete-image absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-lg opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600 shadow-sm"
                                title="Eliminar imagem">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="p-3 space-y-3 bg-gray-50">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Título (PT)</label>
                            <input type="text" name="gallery_alt_pt[<?= $image['id'] ?>]"
                                   value="<?= e($image['alt_text_pt'] ?? '') ?>"
                                   class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-secondary-500 focus:border-secondary-500"
                                   placeholder="Título da imagem">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Título (EN)</label>
                            <input type="text" name="gallery_alt_en[<?= $image['id'] ?>]"
                                   value="<?= e($image['alt_text_en'] ?? '') ?>"
                                   class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-secondary-500 focus:border-secondary-500"
                                   placeholder="Image title">
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-gray-500 text-sm mb-4">Nenhuma imagem adicionada ainda para esta casa.</p>
            <?php endif; ?>

            <div id="galleryDropZone" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-secondary-500 hover:bg-secondary-50 transition-all cursor-pointer">
                <input type="file" name="gallery[]" multiple accept="image/jpeg,image/png,image/webp" id="galleryInput" class="hidden">
                <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <p class="text-gray-600">Arraste imagens aqui ou clique para selecionar</p>
                <p class="text-xs text-gray-400 mt-1">JPEG, PNG, WebP - Máximo 5MB</p>
            </div>

            <div id="galleryPreviewArea" class="hidden mt-4">
                <div class="flex items-center justify-between mb-3">
                    <p class="font-medium text-gray-700">Novas imagens a carregar:</p>
                    <button type="button" id="clearGalleryFiles" class="text-sm text-red-500 hover:text-red-700">Limpar</button>
                </div>
                <div id="galleryPreviewList" class="grid grid-cols-3 md:grid-cols-4 gap-3"></div>
                <div id="galleryPreviewTotal" class="mt-3 pt-3 border-t border-gray-200 text-sm font-medium text-gray-700"></div>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="px-6 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700">
                Guardar Alterações
            </button>
        </div>
    </div>

    <!-- Tab: Amenities -->
    <div id="panel-amenities" class="admin-tab-panel hidden">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-medium text-gray-800">Comodidades</h2>
                    <p class="text-sm text-gray-600 mt-1">Selecione as comodidades e marque até 8 para destacar na página principal</p>
                </div>
                <button type="button" id="btnAddAmenity" class="px-4 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Adicionar Comodidade
                </button>
            </div>

            <?php if (empty($amenities)): ?>
            <p class="text-gray-500 text-sm mb-4">Nenhuma comodidade definida.</p>
            <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($amenitiesByCategory as $category => $categoryAmenities): ?>
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-3"><?= $categoryLabels[$category] ?? ucfirst($category) ?></h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <?php foreach ($categoryAmenities as $amenity): ?>
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 group">
                            <label class="flex items-center cursor-pointer flex-1">
                                <input type="checkbox" name="amenities[]" value="<?= $amenity['id'] ?>"
                                       <?= in_array($amenity['id'], $selectedAmenities) ? 'checked' : '' ?>
                                       class="w-4 h-4 text-secondary-600 border-gray-300 rounded focus:ring-secondary-500">
                                <span class="ml-3 text-sm text-gray-700 flex-1">
                                    <?php if ($amenity['icon']): ?>
                                    <span class="mr-1"><?= $amenity['icon'] ?></span>
                                    <?php endif; ?>
                                    <?= e($amenity['translated_name'] ?? $amenity['icon']) ?>
                                </span>
                            </label>
                            <div class="flex items-center gap-2">
                                <label class="flex items-center cursor-pointer" title="Destacar na página principal (máx. 8)">
                                    <input type="checkbox" name="highlighted_amenities[]" value="<?= $amenity['id'] ?>"
                                           <?= in_array($amenity['id'], $highlightedAmenities) ? 'checked' : '' ?>
                                           class="w-4 h-4 text-accent-600 border-gray-300 rounded focus:ring-accent-500">
                                    <svg class="w-4 h-4 ml-1 text-accent-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </label>
                                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button"
                                            data-edit-amenity='<?= json_encode(['id' => $amenity['id'], 'icon' => $amenity['icon'], 'category' => $amenity['category'], 'name_pt' => $amenity['name_pt'] ?? '', 'name_en' => $amenity['name_en'] ?? ''], JSON_HEX_APOS | JSON_HEX_QUOT) ?>'
                                            class="btn-edit-amenity p-1 text-gray-400 hover:text-secondary-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button type="button"
                                            data-delete-amenity-id="<?= $amenity['id'] ?>"
                                            data-delete-amenity-name="<?= e($amenity['translated_name'] ?? $amenity['icon']) ?>"
                                            class="btn-delete-amenity p-1 text-gray-400 hover:text-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="mt-6">
            <button type="submit" class="px-6 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700">
                Guardar Alterações
            </button>
        </div>
    </div>

    <!-- Tab: House Rules -->
    <div id="panel-rules" class="admin-tab-panel hidden">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-medium text-gray-800">Regras da Casa</h2>
                <button type="button" id="btnAddRule" class="px-4 py-2 bg-secondary-600 text-white text-sm rounded-lg hover:bg-secondary-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Adicionar Regra
                </button>
            </div>

            <?php if (!empty($houseRules)): ?>
            <div class="space-y-3">
                <?php foreach ($houseRules as $rule): ?>
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-200 text-gray-600 text-xs font-bold"><?= $rule['sort_order'] ?></span>
                            <p class="text-sm text-gray-800 font-medium"><?= e($rule['rule_pt'] ?? $rule['rule_en'] ?? 'Sem texto') ?></p>
                        </div>
                        <?php if ($rule['rule_en']): ?>
                        <p class="text-sm text-gray-500 ml-8"><?= e($rule['rule_en']) ?></p>
                        <?php endif; ?>
                        <div class="ml-8 mt-1.5">
                            <?php if ($rule['is_highlighted']): ?>
                            <span class="inline-block px-2 py-0.5 text-xs bg-accent-100 text-accent-700 rounded">Destacada</span>
                            <?php else: ?>
                            <span class="inline-block px-2 py-0.5 text-xs bg-gray-100 text-gray-500 rounded">Normal</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 ml-4">
                        <button type="button" class="btn-edit-rule p-2 text-gray-400 hover:text-secondary-600 rounded-lg hover:bg-secondary-50"
                                data-edit-rule="<?= e(json_encode([
                                    'id' => $rule['id'],
                                    'rule_pt' => $rule['rule_pt'] ?? '',
                                    'rule_en' => $rule['rule_en'] ?? '',
                                    'is_highlighted' => (int)$rule['is_highlighted']
                                ])) ?>">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button type="button" class="btn-delete-rule p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50"
                                data-delete-rule-id="<?= $rule['id'] ?>"
                                data-delete-rule-name="Regra #<?= $rule['sort_order'] ?>">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-gray-500 text-sm">Nenhuma regra definida.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab: Policies -->
    <div id="panel-policies" class="admin-tab-panel hidden">
        <div class="bg-white rounded-lg shadow-sm">
            <div class="border-b border-gray-200">
                <nav class="flex">
                    <?php foreach ($languages as $i => $lang): ?>
                    <button type="button" onclick="switchPolicyLangTab('<?= $lang['code'] ?>')"
                            class="policy-lang-tab px-6 py-3 text-sm font-medium border-b-2 <?= $i === 0 ? 'border-secondary-600 text-secondary-600' : 'border-transparent text-gray-500 hover:text-gray-700' ?>"
                            data-lang="<?= $lang['code'] ?>">
                        <?= strtoupper($lang['code']) ?>
                    </button>
                    <?php endforeach; ?>
                </nav>
            </div>

            <?php foreach ($languages as $i => $lang): ?>
            <div class="policy-lang-content p-6 <?= $i > 0 ? 'hidden' : '' ?>" data-lang="<?= $lang['code'] ?>">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Política de Cancelamento</label>
                        <textarea name="cancellation_policy_<?= $lang['id'] ?>" rows="6"
                                  placeholder="Descreva a política de cancelamento..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500"><?= e($translations[$lang['id']]['cancellation_policy'] ?? '') ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Política de Reembolso</label>
                        <textarea name="refund_policy_<?= $lang['id'] ?>" rows="6"
                                  placeholder="Descreva a política de reembolso..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500"><?= e($translations[$lang['id']]['refund_policy'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-6">
            <button type="submit" class="px-6 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700">
                Guardar Alterações
            </button>
        </div>
    </div>
</form>

<!-- Delete Image Modal -->
<div id="deleteImageModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4" onclick="event.stopPropagation()">
        <div class="p-6 text-center">
            <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Eliminar Imagem</h3>
            <p class="text-gray-600 mb-1">Tem a certeza que deseja eliminar:</p>
            <p id="deleteImageName" class="font-medium text-gray-800 mb-6 break-all"></p>
            <div class="flex gap-3 justify-center">
                <a id="deleteImageConfirmBtn" href="#" class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors">Eliminar</a>
                <button type="button" id="deleteImageCancelBtn" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition-colors">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Amenity Modal -->
<div id="amenityModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
            <h3 id="amenityModalTitle" class="text-lg font-medium text-gray-800">Adicionar Comodidade</h3>
            <button type="button" id="amenityModalClose" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="amenityForm" action="" method="post" class="p-6">
            <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
            <input type="hidden" name="add_amenity" id="amenityFormAction" value="1">
            <input type="hidden" name="amenity_id" id="amenityFormId" value="">

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ícone (emoji)</label>
                        <input type="text" name="amenity_icon" id="amenityIcon" placeholder="Ex: 🛏️"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                        <select name="amenity_category" id="amenityCategory" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                            <?php foreach ($categoryLabels as $key => $label): ?>
                            <option value="<?= $key ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome (Português)</label>
                    <input type="text" name="amenity_name_pt" id="amenityNamePt" placeholder="Ex: Wi-Fi Grátis" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome (Inglês)</label>
                    <input type="text" name="amenity_name_en" id="amenityNameEn" placeholder="Ex: Free Wi-Fi"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="submit" class="px-6 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700 font-medium">Guardar</button>
                <button type="button" id="amenityModalCancel" class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Amenity Modal -->
<div id="deleteAmenityModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4" onclick="event.stopPropagation()">
        <div class="p-6 text-center">
            <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Eliminar Comodidade</h3>
            <p class="text-gray-600 mb-1">Tem a certeza que deseja eliminar:</p>
            <p id="deleteAmenityName" class="font-medium text-gray-800 mb-6"></p>
            <div class="flex gap-3 justify-center">
                <a id="deleteAmenityConfirmBtn" href="#" class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors">Eliminar</a>
                <button type="button" id="deleteAmenityCancelBtn" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition-colors">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- Bedroom Modal (Add/Edit) -->
<div id="bedroomModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
            <h3 id="bedroomModalTitle" class="text-lg font-medium text-gray-800">Adicionar Quarto</h3>
            <button type="button" class="modal-close text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="bedroomForm" action="" method="post" class="p-6">
            <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
            <input type="hidden" name="add_bedroom" id="bedroomFormAction" value="1">
            <input type="hidden" name="bedroom_id" id="bedroomFormId" value="">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome (PT)</label>
                        <input type="text" name="bedroom_name_pt" id="bedroomNamePt" placeholder="Ex: Suite Principal"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome (EN)</label>
                        <input type="text" name="bedroom_name_en" id="bedroomNameEn" placeholder="Ex: Master Suite"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Camas (PT)</label>
                    <input type="text" name="bedroom_beds_pt" id="bedroomBedsPt" placeholder="Ex: 2 camas de solteiro" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Camas (EN)</label>
                    <input type="text" name="bedroom_beds_en" id="bedroomBedsEn" placeholder="Ex: 2 single beds"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="submit" class="px-6 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700 font-medium">Guardar</button>
                <button type="button" class="modal-close px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Bathroom Modal (Add/Edit) -->
<div id="bathroomModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
            <h3 id="bathroomModalTitle" class="text-lg font-medium text-gray-800">Adicionar Casa de Banho</h3>
            <button type="button" class="modal-close text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="bathroomForm" action="" method="post" class="p-6">
            <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
            <input type="hidden" name="add_bathroom" id="bathroomFormAction" value="1">
            <input type="hidden" name="bathroom_id" id="bathroomFormId" value="">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome (PT)</label>
                        <input type="text" name="bathroom_name_pt" id="bathroomNamePt" placeholder="Ex: Casa de Banho Principal"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome (EN)</label>
                        <input type="text" name="bathroom_name_en" id="bathroomNameEn" placeholder="Ex: Main Bathroom"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descrição (PT)</label>
                    <input type="text" name="bathroom_desc_pt" id="bathroomDescPt" placeholder="Ex: Banheira, chuveiro, bidé" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descrição (EN)</label>
                    <input type="text" name="bathroom_desc_en" id="bathroomDescEn" placeholder="Ex: Bathtub, shower, bidet"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" name="has_shower" id="bathroomShower" value="1" class="rounded border-gray-300 text-secondary-600 focus:ring-secondary-500">
                        <span class="text-sm text-gray-700">Chuveiro</span>
                    </label>
                    <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" name="has_bathtub" id="bathroomBathtub" value="1" class="rounded border-gray-300 text-secondary-600 focus:ring-secondary-500">
                        <span class="text-sm text-gray-700">Banheira</span>
                    </label>
                    <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" name="has_bidet" id="bathroomBidet" value="1" class="rounded border-gray-300 text-secondary-600 focus:ring-secondary-500">
                        <span class="text-sm text-gray-700">Bidé</span>
                    </label>
                    <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" name="is_ensuite" id="bathroomEnsuite" value="1" class="rounded border-gray-300 text-secondary-600 focus:ring-secondary-500">
                        <span class="text-sm text-gray-700">Ensuite</span>
                    </label>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="submit" class="px-6 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700 font-medium">Guardar</button>
                <button type="button" class="modal-close px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Rule Modal (Add/Edit) -->
<div id="ruleModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
            <h3 id="ruleModalTitle" class="text-lg font-medium text-gray-800">Adicionar Regra</h3>
            <button type="button" class="modal-close text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="ruleForm" action="" method="post" class="p-6">
            <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
            <input type="hidden" name="add_rule" id="ruleFormAction" value="1">
            <input type="hidden" name="rule_id" id="ruleFormId" value="">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Regra (PT) *</label>
                    <textarea name="rule_text_pt" id="ruleTextPt" rows="2" placeholder="Ex: Não são permitidas festas ou eventos." required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Regra (EN)</label>
                    <textarea name="rule_text_en" id="ruleTextEn" rows="2" placeholder="Ex: No parties or events allowed."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary-500"></textarea>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="rule_highlighted" id="ruleHighlighted" value="1" class="rounded border-gray-300 text-accent-600 focus:ring-accent-500">
                    <span class="text-sm text-gray-700">Destacada <span class="text-gray-400">(aparece na página principal, não só no modal)</span></span>
                </label>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="submit" class="px-6 py-2 bg-secondary-600 text-white rounded-lg hover:bg-secondary-700 font-medium">Guardar</button>
                <button type="button" class="modal-close px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal (shared for bedrooms, bathrooms, rules) -->
<div id="deleteItemModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4" onclick="event.stopPropagation()">
        <div class="p-6 text-center">
            <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Eliminar</h3>
            <p class="text-gray-600 mb-1">Tem a certeza que deseja eliminar:</p>
            <p id="deleteItemName" class="font-medium text-gray-800 mb-6"></p>
            <div class="flex gap-3 justify-center">
                <button type="button" id="deleteItemConfirmBtn" class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors">Eliminar</button>
                <button type="button" class="modal-close px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition-colors">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';

    const csrfToken = '<?= CSRF::getToken() ?>';
    const currentCasa = <?= $selectedAccommodationNumber ?>;

    // Image Preview for Hero and Cover Images
    window.previewAccommodationImage = function(input, type) {
        const previewImg = document.getElementById('preview-' + type + '-img');
        const placeholder = document.getElementById('placeholder-' + type);

        if (input.files && input.files[0]) {
            const file = input.files[0];

            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                alert('Formato inválido. Use JPG, PNG ou WebP.');
                input.value = '';
                return;
            }

            // Validate file size (10MB)
            if (file.size > 10 * 1024 * 1024) {
                alert('Ficheiro demasiado grande. Máximo 10MB.');
                input.value = '';
                return;
            }

            const reader = new FileReader();

            reader.onload = function(e) {
                // Show preview image
                previewImg.src = e.target.result;
                previewImg.classList.remove('hidden');

                // Hide placeholder if exists
                if (placeholder) {
                    placeholder.classList.add('hidden');
                }
            };

            reader.readAsDataURL(file);
        }
    };

    // Tab Switching
    function switchTabById(tabName) {
        const tabs = ['info', 'content', 'spaces', 'gallery', 'amenities', 'rules', 'policies'];

        tabs.forEach(t => {
            const btn = document.getElementById('tab-btn-' + t);
            const panel = document.getElementById('panel-' + t);

            if (t === tabName) {
                if (btn) {
                    btn.classList.add('border-secondary-600', 'text-secondary-600');
                    btn.classList.remove('border-transparent', 'text-gray-500');
                }
                if (panel) panel.classList.remove('hidden');
            } else {
                if (btn) {
                    btn.classList.remove('border-secondary-600', 'text-secondary-600');
                    btn.classList.add('border-transparent', 'text-gray-500');
                }
                if (panel) panel.classList.add('hidden');
            }
        });

        // Track active tab for form redirects
        const tabField = document.getElementById('activeTabField');
        if (tabField) tabField.value = tabName;
    }

    document.getElementById('tab-btn-info')?.addEventListener('click', () => switchTabById('info'));
    document.getElementById('tab-btn-content')?.addEventListener('click', () => switchTabById('content'));
    document.getElementById('tab-btn-spaces')?.addEventListener('click', () => switchTabById('spaces'));
    document.getElementById('tab-btn-gallery')?.addEventListener('click', () => switchTabById('gallery'));
    document.getElementById('tab-btn-amenities')?.addEventListener('click', () => switchTabById('amenities'));
    document.getElementById('tab-btn-rules')?.addEventListener('click', () => switchTabById('rules'));
    document.getElementById('tab-btn-policies')?.addEventListener('click', () => switchTabById('policies'));

    // Language Tab Switching (Content)
    window.switchLangTab = function(langCode) {
        document.querySelectorAll('.lang-tab').forEach(tab => {
            if (tab.dataset.lang === langCode) {
                tab.classList.add('border-secondary-600', 'text-secondary-600');
                tab.classList.remove('border-transparent', 'text-gray-500');
            } else {
                tab.classList.remove('border-secondary-600', 'text-secondary-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            }
        });
        document.querySelectorAll('.lang-content').forEach(content => {
            content.classList.toggle('hidden', content.dataset.lang !== langCode);
        });
    };

    // Language Tab Switching (Policies)
    window.switchPolicyLangTab = function(langCode) {
        document.querySelectorAll('.policy-lang-tab').forEach(tab => {
            if (tab.dataset.lang === langCode) {
                tab.classList.add('border-secondary-600', 'text-secondary-600');
                tab.classList.remove('border-transparent', 'text-gray-500');
            } else {
                tab.classList.remove('border-secondary-600', 'text-secondary-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            }
        });
        document.querySelectorAll('.policy-lang-content').forEach(content => {
            content.classList.toggle('hidden', content.dataset.lang !== langCode);
        });
    };

    // Gallery Upload
    const galleryInput = document.getElementById('galleryInput');
    const galleryDropZone = document.getElementById('galleryDropZone');
    const galleryPreviewArea = document.getElementById('galleryPreviewArea');
    const galleryPreviewList = document.getElementById('galleryPreviewList');
    const galleryPreviewTotal = document.getElementById('galleryPreviewTotal');

    function formatSize(bytes) {
        if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
        if (bytes >= 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return bytes + ' B';
    }

    function updateGalleryPreview(files) {
        if (!files || files.length === 0) {
            galleryPreviewArea.classList.add('hidden');
            galleryPreviewList.innerHTML = '';
            return;
        }

        galleryPreviewArea.classList.remove('hidden');
        galleryPreviewList.innerHTML = '';

        let totalSize = 0;
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            totalSize += file.size;

            const item = document.createElement('div');
            item.className = 'relative bg-gray-100 rounded-lg overflow-hidden aspect-video';

            const img = document.createElement('img');
            img.className = 'w-full h-full object-cover';

            const overlay = document.createElement('div');
            overlay.className = 'absolute bottom-0 left-0 right-0 bg-black/60 px-2 py-1';
            overlay.innerHTML = '<p class="text-white text-xs truncate">' + file.name + '</p><p class="text-gray-300 text-xs">' + formatSize(file.size) + '</p>';

            const reader = new FileReader();
            reader.onload = e => img.src = e.target.result;
            reader.readAsDataURL(file);

            item.appendChild(img);
            item.appendChild(overlay);
            galleryPreviewList.appendChild(item);
        }

        galleryPreviewTotal.textContent = 'Total: ' + files.length + ' ficheiro(s) - ' + formatSize(totalSize);
    }

    galleryInput?.addEventListener('change', e => updateGalleryPreview(e.target.files));
    galleryDropZone?.addEventListener('click', () => galleryInput.click());
    galleryDropZone?.addEventListener('dragover', e => { e.preventDefault(); galleryDropZone.classList.add('border-secondary-500', 'bg-secondary-50'); });
    galleryDropZone?.addEventListener('dragleave', e => { e.preventDefault(); galleryDropZone.classList.remove('border-secondary-500', 'bg-secondary-50'); });
    galleryDropZone?.addEventListener('drop', e => {
        e.preventDefault();
        galleryDropZone.classList.remove('border-secondary-500', 'bg-secondary-50');
        const dt = new DataTransfer();
        for (let i = 0; i < e.dataTransfer.files.length; i++) dt.items.add(e.dataTransfer.files[i]);
        galleryInput.files = dt.files;
        updateGalleryPreview(dt.files);
    });
    document.getElementById('clearGalleryFiles')?.addEventListener('click', () => {
        galleryInput.value = '';
        galleryPreviewArea.classList.add('hidden');
    });

    // Delete Image Modal
    const deleteImageModal = document.getElementById('deleteImageModal');
    function openDeleteImageModal(id, name) {
        document.getElementById('deleteImageName').textContent = name;
        document.getElementById('deleteImageConfirmBtn').href = '?delete_image=' + id + '&token=' + csrfToken + '&casa=' + currentCasa;
        deleteImageModal.classList.remove('hidden');
    }
    document.getElementById('deleteImageCancelBtn')?.addEventListener('click', () => deleteImageModal.classList.add('hidden'));
    deleteImageModal?.addEventListener('click', e => { if (e.target === deleteImageModal) deleteImageModal.classList.add('hidden'); });

    // Amenity Modal
    const amenityModal = document.getElementById('amenityModal');
    function openAmenityModal(data) {
        if (data) {
            document.getElementById('amenityModalTitle').textContent = 'Editar Comodidade';
            document.getElementById('amenityFormAction').name = 'edit_amenity';
            document.getElementById('amenityFormId').value = data.id;
            document.getElementById('amenityIcon').value = data.icon || '';
            document.getElementById('amenityCategory').value = data.category || 'general';
            document.getElementById('amenityNamePt').value = data.name_pt || '';
            document.getElementById('amenityNameEn').value = data.name_en || '';
        } else {
            document.getElementById('amenityModalTitle').textContent = 'Adicionar Comodidade';
            document.getElementById('amenityFormAction').name = 'add_amenity';
            document.getElementById('amenityFormId').value = '';
            document.getElementById('amenityIcon').value = '';
            document.getElementById('amenityCategory').value = 'general';
            document.getElementById('amenityNamePt').value = '';
            document.getElementById('amenityNameEn').value = '';
        }
        amenityModal.classList.remove('hidden');
    }
    document.getElementById('btnAddAmenity')?.addEventListener('click', () => openAmenityModal(null));
    document.getElementById('amenityModalClose')?.addEventListener('click', () => amenityModal.classList.add('hidden'));
    document.getElementById('amenityModalCancel')?.addEventListener('click', () => amenityModal.classList.add('hidden'));
    amenityModal?.addEventListener('click', e => { if (e.target === amenityModal) amenityModal.classList.add('hidden'); });

    // Delete Amenity Modal
    const deleteAmenityModal = document.getElementById('deleteAmenityModal');
    function openDeleteAmenityModal(id, name) {
        document.getElementById('deleteAmenityName').textContent = name;
        document.getElementById('deleteAmenityConfirmBtn').href = '?delete_amenity=' + id + '&token=' + csrfToken + '&casa=' + currentCasa;
        deleteAmenityModal.classList.remove('hidden');
    }
    document.getElementById('deleteAmenityCancelBtn')?.addEventListener('click', () => deleteAmenityModal.classList.add('hidden'));
    deleteAmenityModal?.addEventListener('click', e => { if (e.target === deleteAmenityModal) deleteAmenityModal.classList.add('hidden'); });

    // --- Bedroom Modal ---
    const bedroomModal = document.getElementById('bedroomModal');
    function openBedroomModal(data) {
        if (data) {
            document.getElementById('bedroomModalTitle').textContent = 'Editar Quarto ' + data.number;
            document.getElementById('bedroomFormAction').name = 'edit_bedroom';
            document.getElementById('bedroomFormId').value = data.id;
            document.getElementById('bedroomNamePt').value = data.name_pt || '';
            document.getElementById('bedroomNameEn').value = data.name_en || '';
            document.getElementById('bedroomBedsPt').value = data.beds_pt || '';
            document.getElementById('bedroomBedsEn').value = data.beds_en || '';
        } else {
            document.getElementById('bedroomModalTitle').textContent = 'Adicionar Quarto';
            document.getElementById('bedroomFormAction').name = 'add_bedroom';
            document.getElementById('bedroomFormId').value = '';
            document.getElementById('bedroomNamePt').value = '';
            document.getElementById('bedroomNameEn').value = '';
            document.getElementById('bedroomBedsPt').value = '';
            document.getElementById('bedroomBedsEn').value = '';
        }
        bedroomModal.classList.remove('hidden');
    }
    document.getElementById('btnAddBedroom')?.addEventListener('click', () => openBedroomModal(null));

    // --- Bathroom Modal ---
    const bathroomModal = document.getElementById('bathroomModal');
    function openBathroomModal(data) {
        if (data) {
            document.getElementById('bathroomModalTitle').textContent = 'Editar WC ' + data.number;
            document.getElementById('bathroomFormAction').name = 'edit_bathroom';
            document.getElementById('bathroomFormId').value = data.id;
            document.getElementById('bathroomNamePt').value = data.name_pt || '';
            document.getElementById('bathroomNameEn').value = data.name_en || '';
            document.getElementById('bathroomDescPt').value = data.desc_pt || '';
            document.getElementById('bathroomDescEn').value = data.desc_en || '';
            document.getElementById('bathroomShower').checked = !!data.has_shower;
            document.getElementById('bathroomBathtub').checked = !!data.has_bathtub;
            document.getElementById('bathroomBidet').checked = !!data.has_bidet;
            document.getElementById('bathroomEnsuite').checked = !!data.is_ensuite;
        } else {
            document.getElementById('bathroomModalTitle').textContent = 'Adicionar Casa de Banho';
            document.getElementById('bathroomFormAction').name = 'add_bathroom';
            document.getElementById('bathroomFormId').value = '';
            document.getElementById('bathroomNamePt').value = '';
            document.getElementById('bathroomNameEn').value = '';
            document.getElementById('bathroomDescPt').value = '';
            document.getElementById('bathroomDescEn').value = '';
            document.getElementById('bathroomShower').checked = true;
            document.getElementById('bathroomBathtub').checked = false;
            document.getElementById('bathroomBidet').checked = false;
            document.getElementById('bathroomEnsuite').checked = false;
        }
        bathroomModal.classList.remove('hidden');
    }
    document.getElementById('btnAddBathroom')?.addEventListener('click', () => openBathroomModal(null));

    // --- Rule Modal ---
    const ruleModal = document.getElementById('ruleModal');
    function openRuleModal(data) {
        if (data) {
            document.getElementById('ruleModalTitle').textContent = 'Editar Regra';
            document.getElementById('ruleFormAction').name = 'edit_rule';
            document.getElementById('ruleFormId').value = data.id;
            document.getElementById('ruleTextPt').value = data.rule_pt || '';
            document.getElementById('ruleTextEn').value = data.rule_en || '';
            document.getElementById('ruleHighlighted').checked = !!data.is_highlighted;
        } else {
            document.getElementById('ruleModalTitle').textContent = 'Adicionar Regra';
            document.getElementById('ruleFormAction').name = 'add_rule';
            document.getElementById('ruleFormId').value = '';
            document.getElementById('ruleTextPt').value = '';
            document.getElementById('ruleTextEn').value = '';
            document.getElementById('ruleHighlighted').checked = false;
        }
        ruleModal.classList.remove('hidden');
    }
    document.getElementById('btnAddRule')?.addEventListener('click', () => openRuleModal(null));

    // --- Delete Item Modal (shared) ---
    const deleteItemModal = document.getElementById('deleteItemModal');
    let pendingDeleteUrl = null;
    document.getElementById('deleteItemConfirmBtn')?.addEventListener('click', () => {
        if (pendingDeleteUrl) window.location.href = pendingDeleteUrl;
    });
    function openDeleteItemModal(name, url) {
        document.getElementById('deleteItemName').textContent = name;
        pendingDeleteUrl = url;
        deleteItemModal.classList.remove('hidden');
    }

    // --- Close all modals via .modal-close buttons ---
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.closest('.fixed')?.classList.add('hidden');
        });
    });

    // Close modals on backdrop click
    [bedroomModal, bathroomModal, ruleModal, deleteItemModal].forEach(modal => {
        modal?.addEventListener('click', e => {
            if (e.target === modal) modal.classList.add('hidden');
        });
    });

    // Extended event delegation for new buttons
    document.addEventListener('click', e => {
        const deleteImageBtn = e.target.closest('.btn-delete-image');
        if (deleteImageBtn) {
            e.preventDefault();
            openDeleteImageModal(deleteImageBtn.dataset.deleteImageId, deleteImageBtn.dataset.deleteImageName);
            return;
        }

        const editAmenityBtn = e.target.closest('.btn-edit-amenity');
        if (editAmenityBtn) {
            e.preventDefault();
            try { openAmenityModal(JSON.parse(editAmenityBtn.dataset.editAmenity)); } catch (err) { console.error(err); }
            return;
        }

        const deleteAmenityBtn = e.target.closest('.btn-delete-amenity');
        if (deleteAmenityBtn) {
            e.preventDefault();
            openDeleteAmenityModal(deleteAmenityBtn.dataset.deleteAmenityId, deleteAmenityBtn.dataset.deleteAmenityName);
            return;
        }

        const editBedroomBtn = e.target.closest('.btn-edit-bedroom');
        if (editBedroomBtn) {
            e.preventDefault();
            try { openBedroomModal(JSON.parse(editBedroomBtn.dataset.editBedroom)); } catch (err) { console.error(err); }
            return;
        }

        const deleteBedroomBtn = e.target.closest('.btn-delete-bedroom');
        if (deleteBedroomBtn) {
            e.preventDefault();
            openDeleteItemModal(
                deleteBedroomBtn.dataset.deleteBedroomName,
                '?delete_bedroom=' + deleteBedroomBtn.dataset.deleteBedroomId + '&token=' + csrfToken + '&casa=' + currentCasa
            );
            return;
        }

        const editBathroomBtn = e.target.closest('.btn-edit-bathroom');
        if (editBathroomBtn) {
            e.preventDefault();
            try { openBathroomModal(JSON.parse(editBathroomBtn.dataset.editBathroom)); } catch (err) { console.error(err); }
            return;
        }

        const deleteBathroomBtn = e.target.closest('.btn-delete-bathroom');
        if (deleteBathroomBtn) {
            e.preventDefault();
            openDeleteItemModal(
                deleteBathroomBtn.dataset.deleteBathroomName,
                '?delete_bathroom=' + deleteBathroomBtn.dataset.deleteBathroomId + '&token=' + csrfToken + '&casa=' + currentCasa
            );
            return;
        }

        const editRuleBtn = e.target.closest('.btn-edit-rule');
        if (editRuleBtn) {
            e.preventDefault();
            try { openRuleModal(JSON.parse(editRuleBtn.dataset.editRule)); } catch (err) { console.error(err); }
            return;
        }

        const deleteRuleBtn = e.target.closest('.btn-delete-rule');
        if (deleteRuleBtn) {
            e.preventDefault();
            openDeleteItemModal(
                deleteRuleBtn.dataset.deleteRuleName,
                '?delete_rule=' + deleteRuleBtn.dataset.deleteRuleId + '&token=' + csrfToken + '&casa=' + currentCasa
            );
            return;
        }
    });

    // Keyboard - close all modals on Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            deleteImageModal?.classList.add('hidden');
            amenityModal?.classList.add('hidden');
            deleteAmenityModal?.classList.add('hidden');
            bedroomModal?.classList.add('hidden');
            bathroomModal?.classList.add('hidden');
            ruleModal?.classList.add('hidden');
            deleteItemModal?.classList.add('hidden');
        }
    });

    // Auto-switch to tab from URL hash
    const hash = window.location.hash.replace('#', '');
    if (hash && ['info', 'content', 'spaces', 'gallery', 'amenities', 'rules', 'policies'].includes(hash)) {
        switchTabById(hash);
    }
})();
</script>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
