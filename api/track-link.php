<?php

require_once dirname(__DIR__) . '/includes/init.php';

use Core\Database;

header('Content-Type: application/json');

$linkId = (int)($_GET['id'] ?? 0);

if (!$linkId) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid link ID']);
    exit;
}

$db = Database::getInstance();

$link = $db->fetch("SELECT * FROM external_links WHERE id = ? AND is_active = 1", [$linkId]);

if (!$link) {
    http_response_code(404);
    echo json_encode(['error' => 'Link not found']);
    exit;
}

$db->query(
    "UPDATE external_links SET clicks = clicks + 1 WHERE id = ?",
    [$linkId]
);

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$isFetch = isset($_SERVER['HTTP_SEC_FETCH_MODE']) && $_SERVER['HTTP_SEC_FETCH_MODE'] !== 'navigate';
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';

if ($isAjax || $isFetch || $isPost) {
    echo json_encode(['status' => 'ok', 'url' => $link['url']]);
    exit;
}

header('Location: ' . $link['url']);
exit;
