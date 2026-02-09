<?php
/**
 * A Casa do Gi - CSRF Protection Class
 */

namespace Core;

class CSRF
{
    private static int $tokenLifetime = 3600; // 1 hour default

    /**
     * Generate a CSRF token
     */
    public static function generateToken(): string
    {
        Session::start();

        $config = require CONFIG_PATH . '/config.php';
        self::$tokenLifetime = $config['security']['csrf_token_lifetime'] ?? 3600;

        // Check if token exists and is still valid
        $existingToken = Session::get(SESSION_CSRF_TOKEN);
        $tokenTime = Session::get(SESSION_CSRF_TIME);

        if ($existingToken && $tokenTime && (time() - $tokenTime) < self::$tokenLifetime) {
            return $existingToken;
        }

        // Generate new token
        $token = bin2hex(random_bytes(32));
        Session::set(SESSION_CSRF_TOKEN, $token);
        Session::set(SESSION_CSRF_TIME, time());

        return $token;
    }

    /**
     * Validate a CSRF token
     */
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

        // Check if token has expired
        $config = require CONFIG_PATH . '/config.php';
        self::$tokenLifetime = $config['security']['csrf_token_lifetime'] ?? 3600;

        if ((time() - $tokenTime) > self::$tokenLifetime) {
            self::invalidateToken();
            return false;
        }

        // Constant-time comparison to prevent timing attacks
        return hash_equals($storedToken, $token);
    }

    /**
     * Invalidate current token
     */
    public static function invalidateToken(): void
    {
        Session::remove(SESSION_CSRF_TOKEN);
        Session::remove(SESSION_CSRF_TIME);
    }

    /**
     * Generate hidden input field with CSRF token
     */
    public static function tokenField(): string
    {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Generate meta tag with CSRF token (for AJAX requests)
     */
    public static function tokenMeta(): string
    {
        $token = self::generateToken();
        return '<meta name="csrf-token" content="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Get current token value
     */
    public static function getToken(): string
    {
        return self::generateToken();
    }

    /**
     * Alias for validateToken (backwards compatibility)
     */
    public static function validate(?string $token): bool
    {
        return self::validateToken($token);
    }

    /**
     * Check request and abort if CSRF validation fails
     */
    public static function check(): void
    {
        // Only check POST, PUT, PATCH, DELETE requests
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if (!in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return;
        }

        // Get token from POST data or headers (for AJAX)
        $token = $_POST['csrf_token']
            ?? $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? null;

        if (!self::validateToken($token)) {
            // If this is an admin page, destroy session and redirect to login
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

    /**
     * Middleware-style validation that returns boolean instead of dying
     */
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
