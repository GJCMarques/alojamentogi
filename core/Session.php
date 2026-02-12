<?php

namespace Core;

class Session
{
    private static bool $started = false;
    private array $config;

    public function __construct()
    {
        $this->config = require CONFIG_PATH . '/config.php';
    }

    public static function start(): void
    {
        if (self::$started) {
            return;
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        $config = require CONFIG_PATH . '/config.php';

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', 'Lax');

        if ($config['app']['env'] === 'production') {
            ini_set('session.cookie_secure', '1');
        }

        ini_set('session.gc_maxlifetime', $config['security']['session_lifetime']);

        session_name('casadogi_session');
        session_start();

        self::$started = true;

        if (!isset($_SESSION['_created'])) {
            $_SESSION['_created'] = time();
        } elseif (time() - $_SESSION['_created'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['_created'] = time();
        }
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function all(): array
    {
        self::start();
        return $_SESSION;
    }

    public static function clear(): void
    {
        self::start();
        $_SESSION = [];
    }

    public static function destroy(): void
    {
        self::start();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        self::$started = false;
    }

    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }

    public static function flash(string $type, string $message): void
    {
        self::start();
        if (!isset($_SESSION[SESSION_FLASH])) {
            $_SESSION[SESSION_FLASH] = [];
        }
        $_SESSION[SESSION_FLASH][$type][] = $message;
    }

    public static function getFlash(?string $type = null): array
    {
        self::start();
        $messages = $_SESSION[SESSION_FLASH] ?? [];

        if ($type !== null) {
            $typeMessages = $messages[$type] ?? [];
            unset($_SESSION[SESSION_FLASH][$type]);
            return $typeMessages;
        }

        unset($_SESSION[SESSION_FLASH]);
        return $messages;
    }

    public static function hasFlash(?string $type = null): bool
    {
        self::start();
        if ($type !== null) {
            return !empty($_SESSION[SESSION_FLASH][$type]);
        }
        return !empty($_SESSION[SESSION_FLASH]);
    }

    public static function getLanguage(): string
    {
        return self::get(SESSION_LANG, DEFAULT_LANG);
    }

    public static function setLanguage(string $lang): void
    {
        if (in_array($lang, [LANG_PT, LANG_EN])) {
            self::set(SESSION_LANG, $lang);
        }
    }

    public static function isLoggedIn(): bool
    {
        return self::has(SESSION_USER_ID);
    }

    public static function getAdminId(): ?int
    {
        return self::get(SESSION_USER_ID);
    }

    public static function setAdmin(int $adminId): void
    {
        self::regenerate();
        self::set(SESSION_USER_ID, $adminId);
        self::set(SESSION_USER_IP, $_SERVER['REMOTE_ADDR'] ?? '');
    }

    public static function clearAdmin(): void
    {
        self::remove(SESSION_USER_ID);
        self::remove(SESSION_USER_IP);
        self::regenerate();
    }
}
