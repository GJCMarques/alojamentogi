<?php
/**
 * A Casa do Gi - Application Bootstrap
 *
 * This file initializes the application:
 * - Sets error reporting
 * - Loads constants and configuration
 * - Registers autoloader
 * - Starts session
 * - Sets up database connection
 * - Initializes language handler
 */

// Prevent direct access
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

// Load Composer autoloader (for PHPMailer, etc.)
if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require_once ROOT_PATH . '/vendor/autoload.php';
}

// Load constants first
require_once ROOT_PATH . '/config/constants.php';

// Load configuration
$config = require CONFIG_PATH . '/config.php';

// Set error reporting based on environment
if ($config['app']['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', ROOT_PATH . '/logs/php-errors.log');
}

// Set timezone
date_default_timezone_set($config['app']['timezone']);

// Set character encoding
mb_internal_encoding('UTF-8');

/**
 * PSR-4 style autoloader
 */
spl_autoload_register(function ($class) {
    // Map namespace prefixes to directories
    $prefixes = [
        'Core\\' => CORE_PATH . '/',
        'Models\\' => MODELS_PATH . '/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

// Load helper functions
require_once INCLUDES_PATH . '/functions.php';

// Define URL constants (after config and functions are loaded)
if (!defined('UPLOADS_URL')) {
    define('UPLOADS_URL', basePath() . '/uploads');
}

// Start session
\Core\Session::start();

// Set security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Initialize language handler (detects from URL/session)
$lang = \Core\Language::getInstance();

// Make commonly used variables available
$currentLang = $lang->getCurrentLang();
$langId = $lang->getCurrentLangId();

/**
 * Global helper to get current config
 */
function config(string $key = null, mixed $default = null): mixed
{
    static $config = null;

    if ($config === null) {
        $config = require CONFIG_PATH . '/config.php';
    }

    if ($key === null) {
        return $config;
    }

    $keys = explode('.', $key);
    $value = $config;

    foreach ($keys as $k) {
        if (!isset($value[$k])) {
            return $default;
        }
        $value = $value[$k];
    }

    return $value;
}

/**
 * Global helper for database instance
 */
function db(): \Core\Database
{
    return \Core\Database::getInstance();
}

/**
 * Global helper for language instance
 */
function lang(): \Core\Language
{
    return \Core\Language::getInstance();
}

/**
 * Check if running in CLI mode
 */
function isCli(): bool
{
    return php_sapi_name() === 'cli';
}

/**
 * Check if request is AJAX
 */
function isAjax(): bool
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Check if request method is POST
 */
function isPost(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Check if request method is GET
 */
function isGet(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * Get base URL
 */
function baseUrl(): string
{
    return config('app.url', '');
}

/**
 * Generate full URL
 */
function url(string $path = ''): string
{
    $base = rtrim(baseUrl(), '/');
    $path = ltrim($path, '/');
    return $path ? "{$base}/{$path}" : $base;
}

/**
 * Get asset URL
 */
function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

/**
 * Get upload URL
 */
function upload(string $path): string
{
    return url('uploads/' . ltrim($path, '/'));
}
