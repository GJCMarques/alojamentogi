<?php

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require_once ROOT_PATH . '/vendor/autoload.php';
}

require_once ROOT_PATH . '/config/constants.php';

$config = require CONFIG_PATH . '/config.php';

if ($config['app']['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', ROOT_PATH . '/logs/php-errors.log');
}

date_default_timezone_set($config['app']['timezone']);

mb_internal_encoding('UTF-8');

spl_autoload_register(function ($class) {

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

require_once INCLUDES_PATH . '/functions.php';

if (!defined('UPLOADS_URL')) {
    define('UPLOADS_URL', basePath() . '/uploads');
}

\Core\Session::start();

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

$lang = \Core\Language::getInstance();

$currentLang = $lang->getCurrentLang();
$langId = $lang->getCurrentLangId();

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

function db(): \Core\Database
{
    return \Core\Database::getInstance();
}

function lang(): \Core\Language
{
    return \Core\Language::getInstance();
}

function isCli(): bool
{
    return php_sapi_name() === 'cli';
}

function isAjax(): bool
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function isPost(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function isGet(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

function baseUrl(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host . basePath();
}

function url(string $path = ''): string
{
    $base = rtrim(baseUrl(), '/');
    $path = ltrim($path, '/');
    return $path ? "{$base}/{$path}" : $base;
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function upload(string $path): string
{
    return url('uploads/' . ltrim($path, '/'));
}

if (!defined('MAINTENANCE_PAGE')) {
    if (isMaintenanceMode()) {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

        $isExempt = false;

        if (strpos($requestUri, '/admin') !== false) {
            $isExempt = true;
        }
        if (strpos($scriptName, 'login.php') !== false) {
            $isExempt = true;
        }
        if (strpos($scriptName, 'logout.php') !== false) {
            $isExempt = true;
        }

        if (!$isExempt) {
            redirect('/manutencao/');
        }
    }
}
