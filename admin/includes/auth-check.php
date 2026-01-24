<?php
/**
 * A Casa do Gi - Admin Authentication Guard
 *
 * Include this file at the top of every admin page (except login)
 */

use Core\Auth;

// Require authentication
Auth::requireAuth('/admin/login.php');

// Get current admin for use in templates
$currentAdmin = Auth::user();
