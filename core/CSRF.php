<?php

namespace Core;

class CSRF
{
    private static int $tokenLifetime = 3600;

    public static function generateToken(): string
    {
        Session::start();

        $config = require CONFIG_PATH . '/config.php';
        self::$tokenLifetime = $config['security']['csrf_token_lifetime'] ?? 3600;

        $existingToken = Session::get(SESSION_CSRF_TOKEN);
        $tokenTime = Session::get(SESSION_CSRF_TIME);

        if ($existingToken && $tokenTime && (time() - $tokenTime) < self::$tokenLifetime) {
            return $existingToken;
        }

        $token = bin2hex(random_bytes(32));
        Session::set(SESSION_CSRF_TOKEN, $token);
        Session::set(SESSION_CSRF_TIME, time());

        return $token;
    }

    public static function validateToken(?string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        Session::start();

        $storedToken = Session::get(SESSION_CSRF_TOKEN);
        $tokenTime = Session::get(SESSION_CSRF_TIME);

        if (!$storedToken || !$tokenTime) {
            return false;
        }

        $config = require CONFIG_PATH . '/config.php';
        self::$tokenLifetime = $config['security']['csrf_token_lifetime'] ?? 3600;

        if ((time() - $tokenTime) > self::$tokenLifetime) {
            self::invalidateToken();
            return false;
        }

        return hash_equals($storedToken, $token);
    }

    public static function invalidateToken(): void
    {
        Session::remove(SESSION_CSRF_TOKEN);
        Session::remove(SESSION_CSRF_TIME);
    }

    public static function tokenField(): string
    {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function tokenMeta(): string
    {
        $token = self::generateToken();
        return '<meta name="csrf-token" content="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function getToken(): string
    {
        return self::generateToken();
    }

    public static function validate(?string $token): bool
    {
        return self::validateToken($token);
    }

    public static function check(): void
    {

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if (!in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return;
        }

        $token = $_POST['csrf_token']
            ?? $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? null;

        if (!self::validateToken($token)) {

            $requestUri = $_SERVER['REQUEST_URI'] ?? '';
            if (str_contains($requestUri, '/admin/')) {
                Session::destroy();
                http_response_code(403);
                header('Location: ' . basePath() . '/admin/login.php?expired=1');
                exit;
            }

            http_response_code(403);
            die('Invalid or expired security token. Please refresh the page and try again.');
        }
    }

    public static function isValid(): bool
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if (!in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return true;
        }

        $token = $_POST['csrf_token']
            ?? $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? null;

        return self::validateToken($token);
    }
}
