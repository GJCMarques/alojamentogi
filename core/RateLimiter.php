<?php
/**
 * A Casa do Gi - Rate Limiter
 *
 * File-based rate limiting for API endpoints and form submissions.
 * Tracks requests per IP + action and enforces configurable limits.
 */

namespace Core;

class RateLimiter
{
    private string $storageDir;

    private static ?RateLimiter $instance = null;

    public function __construct()
    {
        $this->storageDir = ROOT_PATH . '/logs/rate-limits';
        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0755, true);
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Check if request is allowed under rate limit
     *
     * @param string $action Action identifier (e.g., 'checkout', 'payment-callback')
     * @param int $maxAttempts Maximum attempts allowed in the window
     * @param int $windowSeconds Time window in seconds
     * @param string|null $identifier Custom identifier (defaults to client IP)
     * @return bool True if allowed, false if rate limited
     */
    public function check(string $action, int $maxAttempts = 10, int $windowSeconds = 60, ?string $identifier = null): bool
    {
        $identifier = $identifier ?? $this->getClientIdentifier();
        $key = $this->buildKey($action, $identifier);
        $data = $this->loadData($key);

        // Clean old entries outside the window
        $cutoff = time() - $windowSeconds;
        $data['attempts'] = array_filter($data['attempts'], fn($ts) => $ts > $cutoff);

        if (count($data['attempts']) >= $maxAttempts) {
            $this->saveData($key, $data);
            return false;
        }

        $data['attempts'][] = time();
        $this->saveData($key, $data);
        return true;
    }

    /**
     * Record a failed attempt (for progressive penalties)
     */
    public function recordFailure(string $action, ?string $identifier = null): void
    {
        $identifier = $identifier ?? $this->getClientIdentifier();
        $key = $this->buildKey($action . '_failures', $identifier);
        $data = $this->loadData($key);
        $data['attempts'][] = time();
        $this->saveData($key, $data);
    }

    /**
     * Get number of recent failures
     */
    public function getFailureCount(string $action, int $windowSeconds = 3600, ?string $identifier = null): int
    {
        $identifier = $identifier ?? $this->getClientIdentifier();
        $key = $this->buildKey($action . '_failures', $identifier);
        $data = $this->loadData($key);

        $cutoff = time() - $windowSeconds;
        return count(array_filter($data['attempts'], fn($ts) => $ts > $cutoff));
    }

    /**
     * Enforce rate limit - returns true if allowed, sends 429 and exits if not
     */
    public function enforce(string $action, int $maxAttempts = 10, int $windowSeconds = 60): bool
    {
        if (!$this->check($action, $maxAttempts, $windowSeconds)) {
            http_response_code(429);
            header('Retry-After: ' . $windowSeconds);

            if ($this->isJsonRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Too many requests. Please try again later.']);
            } else {
                echo 'Too many requests. Please try again later.';
            }

            logMessage("Rate limit exceeded: {$action} from " . getClientIp(), 'warning');
            exit;
        }

        return true;
    }

    /**
     * Clean up old rate limit files (run periodically)
     */
    public function cleanup(int $maxAgeSeconds = 7200): void
    {
        $files = glob($this->storageDir . '/*.json');
        $cutoff = time() - $maxAgeSeconds;

        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                @unlink($file);
            }
        }
    }

    private function getClientIdentifier(): string
    {
        return md5(getClientIp() . ($_SERVER['HTTP_USER_AGENT'] ?? ''));
    }

    private function buildKey(string $action, string $identifier): string
    {
        return md5($action . ':' . $identifier);
    }

    private function loadData(string $key): array
    {
        $file = $this->storageDir . '/' . $key . '.json';
        if (file_exists($file)) {
            $content = @file_get_contents($file);
            $data = json_decode($content, true);
            if (is_array($data) && isset($data['attempts'])) {
                return $data;
            }
        }
        return ['attempts' => []];
    }

    private function saveData(string $key, array $data): void
    {
        $file = $this->storageDir . '/' . $key . '.json';
        @file_put_contents($file, json_encode($data), LOCK_EX);
    }

    private function isJsonRequest(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        return str_contains($accept, 'application/json') || str_contains($contentType, 'application/json');
    }
}
