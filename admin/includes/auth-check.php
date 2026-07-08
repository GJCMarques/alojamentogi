<?php

use Core\Auth;

if (!Auth::check()) {
    $rateLimiter = \Core\RateLimiter::getInstance();

    if (!$rateLimiter->check('admin_page_access', 30, 300)) {
        logMessage("Admin page brute force blocked from " . getClientIp(), 'warning');
        http_response_code(429);
        exit('Too many requests. Please try again later.');
    }
}

Auth::requireAuth('/admin/login.php');

$adminSessionLifetime = 1800;
if (!isset($_SESSION['_admin_last_activity'])) {
    $_SESSION['_admin_last_activity'] = time();
} elseif (time() - $_SESSION['_admin_last_activity'] > $adminSessionLifetime) {

    Auth::logout();
    \Core\Session::flash('error', 'Sessão expirada. Por favor, faça login novamente.');
    redirect('/admin/login.php');
}

$_SESSION['_admin_last_activity'] = time();

if (!isset($_SESSION['_last_regenerated'])) {
    $_SESSION['_last_regenerated'] = time();
} elseif (time() - $_SESSION['_last_regenerated'] > 300) {
    // Regeneração não-destrutiva: mantém a sessão antiga válida por breves instantes,
    // evitando "sessão expirada" ao usar o botão "voltar" ou pedidos concorrentes.
    session_regenerate_id(false);
    $_SESSION['_last_regenerated'] = time();
}

$currentAdmin = Auth::user();
