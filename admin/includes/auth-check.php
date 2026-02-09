<?php
/**
 * A Casa do Gi - Admin Authentication Guard
 *
 * Include this file at the top of every admin page (except login)
 * Includes brute force protection for unauthenticated access attempts.
 */

use Core\Auth;

// Rate limit unauthenticated admin page access to prevent brute force
if (!Auth::check()) {
    $rateLimiter = \Core\RateLimiter::getInstance();

    // Track unauthenticated admin access attempts: 30 per 5 min
    if (!$rateLimiter->check('admin_page_access', 30, 300)) {
        logMessage("Admin page brute force blocked from " . getClientIp(), 'warning');
        http_response_code(429);
        exit('Too many requests. Please try again later.');
    }
}

// Require authentication
Auth::requireAuth('/admin/login.php');

// Hard session expiry: 30 minutes since login/last activity
$adminSessionLifetime = 1800; // 30 minutes
if (!isset($_SESSION['_admin_last_activity'])) {
    $_SESSION['_admin_last_activity'] = time();
} elseif (time() - $_SESSION['_admin_last_activity'] > $adminSessionLifetime) {
    // Session expired - force logout
    Auth::logout();
    \Core\Session::flash('error', 'Sessao expirada. Por favor, faca login novamente.');
    redirect('/admin/login.php');
}
// Update last activity timestamp on each page load
$_SESSION['_admin_last_activity'] = time();

// Regenerate session ID periodically to prevent session fixation
if (!isset($_SESSION['_last_regenerated'])) {
    $_SESSION['_last_regenerated'] = time();
} elseif (time() - $_SESSION['_last_regenerated'] > 300) { // Every 5 minutes
    session_regenerate_id(true);
    $_SESSION['_last_regenerated'] = time();
}

// Get current admin for use in templates
$currentAdmin = Auth::user();
