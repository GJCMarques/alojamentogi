<?php

function e(?string $string): string
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function ee(?string $string): void
{
    echo e($string);
}

function __(?string $key, ?string $default = null): ?string
{
    return \Core\Language::get($key, $default);
}

function _e(?string $key, ?string $default = ''): void
{
    echo e(__($key, $default));
}

function _h(?string $key, ?string $default = ''): void
{
    echo __($key, $default);
}

function basePath(): string
{
    static $basePath = null;

    if ($basePath === null) {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $dir = str_replace('\\', '/', dirname($scriptName));

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
            '/manutencao',
            '/termos-condicoes',
            '/politica-privacidade',
        ];

        $cutPosition = strlen($dir);
        foreach ($subfolders as $subfolder) {
            $offset = 0;
            while (($pos = strpos($dir, $subfolder, $offset)) !== false) {
                $charAfter = $dir[$pos + strlen($subfolder)] ?? '';
                if ($charAfter === '' || $charAfter === '/') {
                    if ($pos < $cutPosition) {
                        $cutPosition = $pos;
                    }
                    break;
                }
                $offset = $pos + 1;
            }
        }

        if ($cutPosition < strlen($dir)) {
            $dir = substr($dir, 0, $cutPosition);
        }

        $basePath = rtrim($dir, '/');
    }

    return $basePath;
}

function redirect(string $url, int $statusCode = 302): void
{

    $base = basePath();
    if (str_starts_with($url, '/') && !str_starts_with($url, '//') && !str_starts_with($url, $base . '/')) {
        $url = $base . $url;
    }

    header('Location: ' . $url, true, $statusCode);
    exit;
}

function back(): void
{
    $referer = $_SERVER['HTTP_REFERER'] ?? '/';
    redirect($referer);
}

function redirectWithMessage(string $url, string $type, string $message): void
{
    \Core\Session::flash($type, $message);
    redirect($url);
}

function json(mixed $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function jsonError(string $message, int $statusCode = 400, array $extra = []): void
{
    json(array_merge(['error' => true, 'message' => $message], $extra), $statusCode);
}

function jsonSuccess(string $message = 'Success', array $data = []): void
{
    json(array_merge(['success' => true, 'message' => $message], $data));
}

function post(string $key, mixed $default = null): mixed
{
    return $_POST[$key] ?? $default;
}

function get(string $key, mixed $default = null): mixed
{
    return $_GET[$key] ?? $default;
}

function request(string $key, mixed $default = null): mixed
{
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

function sanitize(?string $input): string
{
    if ($input === null) {
        return '';
    }
    return trim($input);
}

function sanitizeInt(mixed $input): int
{
    return (int) filter_var($input, FILTER_SANITIZE_NUMBER_INT);
}

function sanitizeFloat(mixed $input): float
{
    return (float) filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
}

function sanitizeEmail(?string $input): string
{
    return filter_var($input ?? '', FILTER_SANITIZE_EMAIL);
}

function isValidEmail(?string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function slugify(string $text): string
{

    $text = preg_replace('~[^\pL\d]+~u', '-', $text);

    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    $text = preg_replace('~[^-\w]+~', '', $text);

    $text = trim($text, '-');

    $text = preg_replace('~-+~', '-', $text);

    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}

function createSlug(string $text): string
{
    return slugify($text);
}

function formatPrice(float $price, string $currency = 'EUR'): string
{
    $formatted = number_format($price, 2, ',', '.');
    return "{$formatted} {$currency}";
}

function formatDate(?string $date, string $format = 'd/m/Y'): string
{
    if (!$date) {
        return '';
    }
    $datetime = new DateTime($date);
    return $datetime->format($format);
}

function formatDateTime(?string $date, string $format = 'd/m/Y H:i'): string
{
    if (!$date) {
        return '';
    }
    $datetime = new DateTime($date);
    return $datetime->format($format);
}

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

function truncate(string $text, int $length = 100, string $suffix = '...'): string
{
    if (mb_strlen($text) <= $length) {
        return $text;
    }

    return mb_substr($text, 0, $length) . $suffix;
}

function randomString(int $length = 16): string
{
    return bin2hex(random_bytes($length / 2));
}

function generateOrderNumber(): string
{
    $year = date('Y');
    $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
    return "GI-{$year}-{$random}";
}

function getClientIp(): string
{
    $ip = $_SERVER['HTTP_CLIENT_IP']
        ?? $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['REMOTE_ADDR']
        ?? '0.0.0.0';

    if (strpos($ip, ',') !== false) {
        $ip = trim(explode(',', $ip)[0]);
    }

    return $ip;
}

function getUserAgent(): string
{
    return $_SERVER['HTTP_USER_AGENT'] ?? '';
}

function component(string $name, array $data = []): void
{
    extract($data);
    $file = TEMPLATES_PATH . '/components/' . $name . '.php';

    if (file_exists($file)) {
        include $file;
    }
}

function renderComponent(string $name, array $data = []): string
{
    ob_start();
    component($name, $data);
    return ob_get_clean();
}

function partial(string $name, array $data = []): void
{
    extract($data);
    $file = INCLUDES_PATH . '/partials/' . $name . '.php';

    if (file_exists($file)) {
        include $file;
    }
}

function setting(string $key, mixed $default = null): mixed
{
    static $settings = null;

    if ($settings === null) {
        $db = \Core\Database::getInstance();
        $rows = $db->fetchAll("SELECT setting_key, setting_value, setting_type FROM settings");
        $settings = [];

        foreach ($rows as $row) {
            $value = $row['setting_value'];

            if (\Core\Encryption::isSensitive($row['setting_key']) && is_string($value) && !empty($value)) {
                $value = \Core\Encryption::decrypt($value);
            }

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

function isMaintenanceMode(): bool
{
    return (bool) setting('maintenance_mode', false);
}

function isShopEnabled(): bool
{
    return (bool) setting('shop_enabled', true);
}

function isContactFormEnabled(): bool
{
    return (bool) setting('contact_form_enabled', true);
}

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

function csrfField(): string
{
    return \Core\CSRF::tokenField();
}

function csrfToken(): string
{
    return \Core\CSRF::getToken();
}

function verifyCsrf(): void
{
    \Core\CSRF::check();
}

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
