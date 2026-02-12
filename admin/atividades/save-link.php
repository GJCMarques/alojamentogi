<?php

require_once dirname(dirname(__DIR__)) . '/includes/init.php';
require_once dirname(__DIR__) . '/includes/auth-check.php';

use Core\Database;
use Core\Session;
use Core\CSRF;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/atividades/?section=links');
}

if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
    Session::flash('error', 'Sessão expirada. Por favor tente novamente.');
    redirect('/admin/atividades/?section=links');
}

$db = Database::getInstance();

$linkId = (int)($_POST['link_id'] ?? 0);
$url = sanitize($_POST['url'] ?? '');
$titlePt = sanitize($_POST['title_pt'] ?? '');
$titleEn = sanitize($_POST['title_en'] ?? '');
$descPt = sanitize($_POST['description_pt'] ?? '');
$descEn = sanitize($_POST['description_en'] ?? '');
$category = $_POST['category'] ?? 'tourism';
$icon = $_POST['icon'] ?? 'map';
$sortOrder = (int)($_POST['sort_order'] ?? 0);

if (empty($url) || empty($titlePt)) {
    Session::flash('error', 'URL e Título (PT) são obrigatórios.');
    redirect('/admin/atividades/?section=links');
}

if (!filter_var($url, FILTER_VALIDATE_URL)) {
    Session::flash('error', 'URL inválido.');
    redirect('/admin/atividades/?section=links');
}

try {
    if ($linkId > 0) {

        $db->update('external_links', [
            'url' => $url,
            'category' => $category,
            'icon' => $icon,
            'sort_order' => $sortOrder,
        ], 'id = ?', [$linkId]);

        $existsPt = $db->fetch(
            "SELECT id FROM external_link_translations WHERE link_id = ? AND language_id = 1",
            [$linkId]
        );

        if ($existsPt) {
            $db->update('external_link_translations', [
                'title' => $titlePt,
                'description' => $descPt,
            ], 'id = ?', [$existsPt['id']]);
        } else {
            $db->insert('external_link_translations', [
                'link_id' => $linkId,
                'language_id' => 1,
                'title' => $titlePt,
                'description' => $descPt,
            ]);
        }

        $existsEn = $db->fetch(
            "SELECT id FROM external_link_translations WHERE link_id = ? AND language_id = 2",
            [$linkId]
        );

        if ($existsEn) {
            $db->update('external_link_translations', [
                'title' => $titleEn ?: $titlePt,
                'description' => $descEn ?: $descPt,
            ], 'id = ?', [$existsEn['id']]);
        } else {
            $db->insert('external_link_translations', [
                'link_id' => $linkId,
                'language_id' => 2,
                'title' => $titleEn ?: $titlePt,
                'description' => $descEn ?: $descPt,
            ]);
        }

        Session::flash('success', 'Link atualizado com sucesso.');

    } else {

        $db->insert('external_links', [
            'url' => $url,
            'category' => $category,
            'icon' => $icon,
            'is_featured' => 0,
            'is_active' => 1,
            'sort_order' => $sortOrder,
        ]);

        $newLinkId = $db->lastInsertId();

        $db->insert('external_link_translations', [
            'link_id' => $newLinkId,
            'language_id' => 1,
            'title' => $titlePt,
            'description' => $descPt,
        ]);

        $db->insert('external_link_translations', [
            'link_id' => $newLinkId,
            'language_id' => 2,
            'title' => $titleEn ?: $titlePt,
            'description' => $descEn ?: $descPt,
        ]);

        Session::flash('success', 'Link criado com sucesso.');
    }

} catch (Exception $e) {
    Session::flash('error', 'Erro ao guardar o link: ' . $e->getMessage());
}

redirect('/admin/atividades/?section=links');
