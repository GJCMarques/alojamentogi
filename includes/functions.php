<?php
/**
 * A Casa do Gi - Global Helper Functions
 */

/**
 * Escape HTML output (XSS prevention)
 */
function e(?string $string): string
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Escape and echo
 */
function ee(?string $string): void
{
    echo e($string);
}

/**
 * Get translated content from database
 */
function __(?string $key, ?string $default = null): ?string
{
    return \Core\Language::get($key, $default);
}

/**
 * Echo translated content (escaped)
 */
function _e(?string $key, ?string $default = ''): void
{
    echo e(__($key, $default));
}

/**
 * Echo translated content (raw HTML allowed)
 */
function _h(?string $key, ?string $default = ''): void
{
    echo __($key, $default);
}

/**
 * Get the base path of the application (e.g., /alojamentogi)
 */
function basePath(): string
{
    static $basePath = null;

    if ($basePath === null) {
        $appUrl = config('app.url', '');

        // Use configured URL if available and valid
        if ($appUrl && filter_var($appUrl, FILTER_VALIDATE_URL)) {
            $parsed = parse_url($appUrl);
            $basePath = rtrim($parsed['path'] ?? '', '/');
        } else {
            // Auto-detect from script name
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $dir = dirname($scriptName);

            // Normalize slashes
            $dir = str_replace('\\', '/', $dir);

            // All known subfolders that need to be removed to get root base
            $subfolders = [
                '/admin',
                '/en',
                '/api',
                '/alojamento',
                '/loja',
                '/atividades',
                '/contactos',
                '/sobre-nos',
                '/accommodation',
                '/shop',
                '/activities',
                '/contact',
                '/about-us',
            ];

            // Find the earliest occurrence of any subfolder and cut there
            $cutPosition = strlen($dir);
            foreach ($subfolders as $subfolder) {
                $pos = strpos($dir, $subfolder);
                if ($pos !== false && $pos < $cutPosition) {
                    $cutPosition = $pos;
                }
            }

            if ($cutPosition < strlen($dir)) {
                $dir = substr($dir, 0, $cutPosition);
            }

            $basePath = rtrim($dir, '/');
        }
    }

    return $basePath;
}

/**
 * Redirect to URL
 */
function redirect(string $url, int $statusCode = 302): void
{
    // If URL starts with /, prepend the base path (but avoid double-prepending)
    $base = basePath();
    if (str_starts_with($url, '/') && !str_starts_with($url, '//') && !str_starts_with($url, $base . '/')) {
        $url = $base . $url;
    }

    header('Location: ' . $url, true, $statusCode);
    exit;
}

/**
 * Redirect back to previous page
 */
function back(): void
{
    $referer = $_SERVER['HTTP_REFERER'] ?? '/';
    redirect($referer);
}

/**
 * Set flash message and redirect
 */
function redirectWithMessage(string $url, string $type, string $message): void
{
    \Core\Session::flash($type, $message);
    redirect($url);
}

/**
 * JSON response
 */
function json(mixed $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * JSON error response
 */
function jsonError(string $message, int $statusCode = 400, array $extra = []): void
{
    json(array_merge(['error' => true, 'message' => $message], $extra), $statusCode);
}

/**
 * JSON success response
 */
function jsonSuccess(string $message = 'Success', array $data = []): void
{
    json(array_merge(['success' => true, 'message' => $message], $data));
}

/**
 * Get POST value
 */
function post(string $key, mixed $default = null): mixed
{
    return $_POST[$key] ?? $default;
}

/**
 * Get GET value
 */
function get(string $key, mixed $default = null): mixed
{
    return $_GET[$key] ?? $default;
}

/**
 * Get request value (POST then GET)
 */
function request(string $key, mixed $default = null): mixed
{
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

/**
 * Sanitize string input
 * Note: Only trims whitespace. Output encoding is handled by e() at display time.
 */
function sanitize(?string $input): string
{
    if ($input === null) {
        return '';
    }
    return trim($input);
}

/**
 * Sanitize integer
 */
function sanitizeInt(mixed $input): int
{
    return (int) filter_var($input, FILTER_SANITIZE_NUMBER_INT);
}

/**
 * Sanitize float
 */
function sanitizeFloat(mixed $input): float
{
    return (float) filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
}

/**
 * Sanitize email
 */
function sanitizeEmail(?string $input): string
{
    return filter_var($input ?? '', FILTER_SANITIZE_EMAIL);
}

/**
 * Validate email
 */
function isValidEmail(?string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate slug from string
 */
function slugify(string $text): string
{
    // Replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);

    // Transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // Remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // Trim
    $text = trim($text, '-');

    // Remove duplicate -
    $text = preg_replace('~-+~', '-', $text);

    // Lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}

/**
 * Alias for slugify
 */
function createSlug(string $text): string
{
    return slugify($text);
}

/**
 * Format price for display
 */
function formatPrice(float $price, string $currency = 'EUR'): string
{
    $formatted = number_format($price, 2, ',', '.');
    return "{$formatted} {$currency}";
}

/**
 * Format date for display
 */
function formatDate(?string $date, string $format = 'd/m/Y'): string
{
    if (!$date) {
        return '';
    }
    $datetime = new DateTime($date);
    return $datetime->format($format);
}

/**
 * Format datetime for display
 */
function formatDateTime(?string $date, string $format = 'd/m/Y H:i'): string
{
    if (!$date) {
        return '';
    }
    $datetime = new DateTime($date);
    return $datetime->format($format);
}

/**
 * Time ago (e.g., "2 hours ago")
 */
function timeAgo(string $datetime): string
{
    $time = strtotime($datetime);
    $diff = time() - $time;

    $lang = \Core\Language::current();
    $isPt = $lang === LANG_PT;

    if ($diff < 60) {
        return $isPt ? 'agora mesmo' : 'just now';
    }

    if ($diff < 3600) {
        $mins = floor($diff / 60);
        return $isPt
            ? "{$mins} minuto" . ($mins > 1 ? 's' : '') . " atras"
            : "{$mins} minute" . ($mins > 1 ? 's' : '') . " ago";
    }

    if ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $isPt
            ? "{$hours} hora" . ($hours > 1 ? 's' : '') . " atras"
            : "{$hours} hour" . ($hours > 1 ? 's' : '') . " ago";
    }

    if ($diff < 604800) {
        $days = floor($diff / 86400);
        return $isPt
            ? "{$days} dia" . ($days > 1 ? 's' : '') . " atras"
            : "{$days} day" . ($days > 1 ? 's' : '') . " ago";
    }

    return formatDate($datetime);
}

/**
 * Truncate text
 */
function truncate(string $text, int $length = 100, string $suffix = '...'): string
{
    if (mb_strlen($text) <= $length) {
        return $text;
    }

    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Generate random string
 */
function randomString(int $length = 16): string
{
    return bin2hex(random_bytes($length / 2));
}

/**
 * Generate order number
 */
function generateOrderNumber(): string
{
    $year = date('Y');
    $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
    return "GI-{$year}-{$random}";
}

/**
 * Get client IP address
 */
function getClientIp(): string
{
    $ip = $_SERVER['HTTP_CLIENT_IP']
        ?? $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['REMOTE_ADDR']
        ?? '0.0.0.0';

    // Handle comma-separated IPs (from proxies)
    if (strpos($ip, ',') !== false) {
        $ip = trim(explode(',', $ip)[0]);
    }

    return $ip;
}

/**
 * Get user agent
 */
function getUserAgent(): string
{
    return $_SERVER['HTTP_USER_AGENT'] ?? '';
}

/**
 * Include template component
 */
function component(string $name, array $data = []): void
{
    extract($data);
    $file = TEMPLATES_PATH . '/components/' . $name . '.php';

    if (file_exists($file)) {
        include $file;
    }
}

/**
 * Render template component and return as string
 */
function renderComponent(string $name, array $data = []): string
{
    ob_start();
    component($name, $data);
    return ob_get_clean();
}

/**
 * Include partial view
 */
function partial(string $name, array $data = []): void
{
    extract($data);
    $file = INCLUDES_PATH . '/partials/' . $name . '.php';

    if (file_exists($file)) {
        include $file;
    }
}

/**
 * Get setting from database
 */
function setting(string $key, mixed $default = null): mixed
{
    static $settings = null;

    if ($settings === null) {
        $db = \Core\Database::getInstance();
        $rows = $db->fetchAll("SELECT setting_key, setting_value, setting_type FROM settings");
        $settings = [];

        foreach ($rows as $row) {
            $value = $row['setting_value'];

            // Auto-decrypt sensitive settings
            if (\Core\Encryption::isSensitive($row['setting_key']) && is_string($value) && !empty($value)) {
                $value = \Core\Encryption::decrypt($value);
            }

            // Type casting
            switch ($row['setting_type']) {
                case 'boolean':
                    $value = (bool) $value;
                    break;
                case 'number':
                    $value = is_numeric($value) ? (strpos($value, '.') !== false ? (float) $value : (int) $value) : $value;
                    break;
                case 'json':
                    $value = json_decode($value, true);
                    break;
            }

            $settings[$row['setting_key']] = $value;
        }
    }

    return $settings[$key] ?? $default;
}

/**
 * Check if maintenance mode is active
 */
function isMaintenanceMode(): bool
{
    return (bool) setting('maintenance_mode', false);
}

/**
 * Check if shop is enabled
 */
function isShopEnabled(): bool
{
    return (bool) setting('shop_enabled', true);
}

/**
 * Check if contact form is enabled
 */
function isContactFormEnabled(): bool
{
    return (bool) setting('contact_form_enabled', true);
}

/**
 * DD (dump and die) for debugging
 */
function dd(mixed ...$vars): void
{
    if (!config('app.debug')) {
        return;
    }

    echo '<pre style="background:#1a1a2e;color:#eee;padding:15px;margin:10px;border-radius:5px;font-size:13px;">';
    foreach ($vars as $var) {
        var_dump($var);
    }
    echo '</pre>';
    exit;
}

/**
 * Dump (without die) for debugging
 */
function dump(mixed ...$vars): void
{
    if (!config('app.debug')) {
        return;
    }

    echo '<pre style="background:#1a1a2e;color:#eee;padding:15px;margin:10px;border-radius:5px;font-size:13px;">';
    foreach ($vars as $var) {
        var_dump($var);
    }
    echo '</pre>';
}

/**
 * Log message to file
 */
function logMessage(string $message, string $level = 'info'): void
{
    $logDir = ROOT_PATH . '/logs';

    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $logFile = $logDir . '/app-' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $level = strtoupper($level);

    $logLine = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
    file_put_contents($logFile, $logLine, FILE_APPEND);
}

/**
 * Get CSRF token field
 */
function csrfField(): string
{
    return \Core\CSRF::tokenField();
}

/**
 * Get CSRF token value
 */
function csrfToken(): string
{
    return \Core\CSRF::getToken();
}

/**
 * Verify CSRF token
 */
function verifyCsrf(): void
{
    \Core\CSRF::check();
}

/**
 * Get content block from database
 */
function content(string $key, string $default = ''): string
{
    static $blocks = null;
    $langId = \Core\Language::getInstance()->getCurrentLangId();

    if ($blocks === null) {
        $db = \Core\Database::getInstance();
        $rows = $db->fetchAll("SELECT block_key, content FROM content_blocks WHERE language_id = ?", [$langId]);
        
        $blocks = [];
        foreach ($rows as $row) {
            $blocks[$row['block_key']] = $row['content'];
        }
    }

    return $blocks[$key] ?? $default;
}

/**
 * Resolve Content Image URL
 */
function resolveContentImage(?string $path): string
{
     if (empty($path)) {
        return '';
     }
     
     if (str_starts_with($path, 'http')) {
        return $path;
     }

     if ($path[0] === '/') {
        return basePath() . $path;
     }

     return asset($path);
}
