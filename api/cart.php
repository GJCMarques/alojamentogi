<?php

require_once dirname(__DIR__) . '/includes/init.php';

use Core\Cart;
use Core\CSRF;

header('Content-Type: application/json; charset=utf-8');

$rateLimiter = \Core\RateLimiter::getInstance();
if (!$rateLimiter->check('cart_api', 60, 60)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Too many requests. Please slow down.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
    exit;
}

$csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $input['csrf_token'] ?? null;

if (!$csrfToken || !CSRF::validate($csrfToken)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

$cart = Cart::getInstance();
$action = $input['action'] ?? '';

switch ($action) {
    case 'add':
        $productId = (int)($input['product_id'] ?? 0);
        $quantity = max(1, (int)($input['quantity'] ?? 1));

        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'ID do produto inválido']);
            exit;
        }

        $result = $cart->add($productId, $quantity);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Produto adicionado ao carrinho',
                'cart' => $cart->toArray()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Não foi possível adicionar o produto. Verifique o stock disponível.'
            ]);
        }
        break;

    case 'update':
        $productId = (int)($input['product_id'] ?? 0);
        $quantity = (int)($input['quantity'] ?? 0);

        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'ID do produto inválido']);
            exit;
        }

        $result = $cart->update($productId, $quantity);

        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Carrinho atualizado' : 'Erro ao atualizar',
            'cart' => $cart->toArray()
        ]);
        break;

    case 'remove':
        $productId = (int)($input['product_id'] ?? 0);

        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'ID do produto inválido']);
            exit;
        }

        $result = $cart->remove($productId);

        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Produto removido' : 'Produto não encontrado',
            'cart' => $cart->toArray()
        ]);
        break;

    case 'clear':
        $cart->clear();

        echo json_encode([
            'success' => true,
            'message' => 'Carrinho limpo',
            'cart' => $cart->toArray()
        ]);
        break;

    case 'get':
        echo json_encode([
            'success' => true,
            'cart' => $cart->toArray()
        ]);
        break;

    case 'validate':
        $errors = $cart->validate();

        echo json_encode([
            'success' => empty($errors),
            'errors' => $errors,
            'cart' => $cart->toArray()
        ]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
        break;
}
