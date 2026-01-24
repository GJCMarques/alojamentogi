<?php
/**
 * A Casa do Gi - Internationalization (i18n) Handler
 */

namespace Core;

class Language
{
    private static ?Language $instance = null;
    private Database $db;
    private string $currentLang;
    private int $currentLangId;
    private array $languages = [];
    private array $contentCache = [];

    private function __construct()
    {
        $this->db = Database::getInstance();
        $this->loadLanguages();
        $this->detectLanguage();
    }

    /**
     * Get singleton instance
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Load available languages from database
     */
    private function loadLanguages(): void
    {
        $result = $this->db->fetchAll(
            "SELECT * FROM languages WHERE is_active = 1 ORDER BY is_default DESC"
        );

        foreach ($result as $lang) {
            $this->languages[$lang['code']] = $lang;
        }
    }

    /**
     * Detect current language from URL or session
     */
    private function detectLanguage(): void
    {
        // Check URL path for language prefix
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);

        // Get base path to properly detect /en/ prefix
        $base = $this->getBasePath();

        // Remove base path to get relative path
        $relativePath = $path;
        if ($base !== '' && strpos($path, $base) === 0) {
            $relativePath = substr($path, strlen($base));
        }

        // Ensure relativePath starts with /
        if (empty($relativePath) || $relativePath[0] !== '/') {
            $relativePath = '/' . $relativePath;
        }

        // Check if relative path starts with /en/ or is exactly /en
        if (strpos($relativePath, '/en/') === 0 || $relativePath === '/en') {
            $this->setLanguage(LANG_EN);
            return;
        }

        // Any path NOT starting with /en/ is Portuguese
        // This ensures Portuguese pages always show Portuguese content
        // regardless of session language
        $this->setLanguage(LANG_PT);
    }

    /**
     * Get base path (helper to avoid circular dependency with basePath())
     */
    private function getBasePath(): string
    {
        static $basePath = null;

        if ($basePath === null) {
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $dir = dirname($scriptName);
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

        return $basePath;
    }

    /**
     * Set current language
     */
    public function setLanguage(string $code): void
    {
        if (!isset($this->languages[$code])) {
            $code = DEFAULT_LANG;
        }

        $this->currentLang = $code;
        $this->currentLangId = (int) $this->languages[$code]['id'];
        Session::setLanguage($code);

        // Clear content cache when language changes
        $this->contentCache = [];
    }

    /**
     * Get current language code
     */
    public function getCurrentLang(): string
    {
        return $this->currentLang;
    }

    /**
     * Get current language ID
     */
    public function getCurrentLangId(): int
    {
        return $this->currentLangId;
    }

    /**
     * Check if current language is English
     */
    public function isEnglish(): bool
    {
        return $this->currentLang === LANG_EN;
    }

    /**
     * Check if current language is Portuguese
     */
    public function isPortuguese(): bool
    {
        return $this->currentLang === LANG_PT;
    }

    /**
     * Get all available languages
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }

    /**
     * Get language info by code
     */
    public function getLanguageInfo(string $code): ?array
    {
        return $this->languages[$code] ?? null;
    }

    /**
     * Get content block by key
     */
    public function getContent(string $key, ?string $default = null): ?string
    {
        // Check cache first
        $cacheKey = $this->currentLang . '_' . $key;
        if (isset($this->contentCache[$cacheKey])) {
            return $this->contentCache[$cacheKey];
        }

        $result = $this->db->fetch(
            "SELECT content FROM content_blocks WHERE block_key = ? AND language_id = ?",
            [$key, $this->currentLangId]
        );

        $content = $result['content'] ?? $default;
        $this->contentCache[$cacheKey] = $content;

        return $content;
    }

    /**
     * Get content block with fallback to default language
     */
    public function getContentWithFallback(string $key, ?string $default = null): ?string
    {
        $content = $this->getContent($key);

        if ($content === null && $this->currentLang !== DEFAULT_LANG) {
            // Try default language
            $result = $this->db->fetch(
                "SELECT content FROM content_blocks WHERE block_key = ? AND language_id = ?",
                [$key, $this->languages[DEFAULT_LANG]['id']]
            );
            $content = $result['content'] ?? null;
        }

        return $content ?? $default;
    }

    /**
     * Get multiple content blocks at once
     */
    public function getContents(array $keys): array
    {
        $contents = [];
        $keysToFetch = [];

        // Check cache
        foreach ($keys as $key) {
            $cacheKey = $this->currentLang . '_' . $key;
            if (isset($this->contentCache[$cacheKey])) {
                $contents[$key] = $this->contentCache[$cacheKey];
            } else {
                $keysToFetch[] = $key;
            }
        }

        // Fetch uncached keys
        if (!empty($keysToFetch)) {
            $placeholders = implode(',', array_fill(0, count($keysToFetch), '?'));
            $params = array_merge($keysToFetch, [$this->currentLangId]);

            $results = $this->db->fetchAll(
                "SELECT block_key, content FROM content_blocks
                 WHERE block_key IN ({$placeholders}) AND language_id = ?",
                $params
            );

            foreach ($results as $row) {
                $contents[$row['block_key']] = $row['content'];
                $cacheKey = $this->currentLang . '_' . $row['block_key'];
                $this->contentCache[$cacheKey] = $row['content'];
            }
        }

        return $contents;
    }

    /**
     * Get contents for a specific page
     */
    public function getPageContents(string $page): array
    {
        $results = $this->db->fetchAll(
            "SELECT block_key, content FROM content_blocks
             WHERE page = ? AND language_id = ?",
            [$page, $this->currentLangId]
        );

        $contents = [];
        foreach ($results as $row) {
            $contents[$row['block_key']] = $row['content'];
            $cacheKey = $this->currentLang . '_' . $row['block_key'];
            $this->contentCache[$cacheKey] = $row['content'];
        }

        return $contents;
    }

    /**
     * Get URL for language switch
     */
    public function getSwitchUrl(string $toLang): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        $query = parse_url($uri, PHP_URL_QUERY);
        $queryString = $query ? '?' . $query : '';
        
        // Remove base path to get relative path
        $base = parse_url(basePath(), PHP_URL_PATH) ?? '';
        $base = rtrim($base, '/');
        
        // If current path starts with base, remove it
        $relPath = $path;
        if ($base !== '' && strpos($path, $base) === 0) {
            $relPath = substr($path, strlen($base));
        }
        
        // Ensure relPath starts with slash
        if (empty($relPath) || $relPath[0] !== '/') {
            $relPath = '/' . $relPath;
        }

        // Current is PT, switching to EN
        if ($this->currentLang === LANG_PT && $toLang === LANG_EN) {
            $pathMap = [
                '/' => '/en/',
                '/alojamento' => '/en/accommodation',
                '/loja' => '/en/shop',
                '/atividades' => '/en/activities',
                '/contactos' => '/en/contact',
                '/sobre-nos' => '/en/about-us',
            ];

            // Check mappings
            foreach ($pathMap as $pt => $en) {
                // Exact match or prefix match
                if ($relPath === $pt || ($pt !== '/' && strpos($relPath, $pt . '/') === 0)) {
                    // Replace mapping
                    $newRelPath = ($pt === '/') ? $en : str_replace($pt, $en, $relPath);
                    return $base . $newRelPath . $queryString;
                }
            }

            // Fallback: just prepend /en/ to relative path (if not already there)
            if (strpos($relPath, '/en/') !== 0) {
                // If it's root '/', just make it '/en/'
                if ($relPath === '/') {
                    return $base . '/en/' . $queryString;
                }
                return $base . '/en' . $relPath . $queryString;
            }
        }

        // Current is EN, switching to PT
        if ($this->currentLang === LANG_EN && $toLang === LANG_PT) {
            $pathMap = [
                '/en/' => '/',
                '/en' => '/',
                '/en/accommodation' => '/alojamento',
                '/en/shop' => '/loja',
                '/en/activities' => '/atividades',
                '/en/contact' => '/contactos',
                '/en/about-us' => '/sobre-nos',
            ];

            foreach ($pathMap as $en => $pt) {
                if ($relPath === $en || strpos($relPath, $en . '/') === 0) {
                    $newRelPath = str_replace($en, $pt, $relPath);
                    // Ensure we don't end up with empty path if mapping to /
                    if ($newRelPath === '') $newRelPath = '/';
                    return $base . $newRelPath . $queryString;
                }
            }

            // Remove /en prefix
            $newRelPath = preg_replace('/^\/en(\/|$)/', '/', $relPath);
            return $base . $newRelPath . $queryString;
        }

        return $uri;
    }

    /**
     * Get URL prefix for current language
     */
    public function getUrlPrefix(): string
    {
        return $this->currentLang === LANG_EN ? '/en' : '';
    }

    /**
     * Generate localized URL
     */
    public function url(string $path): string
    {
        $base = basePath();
        $prefix = $this->getUrlPrefix();
        return $base . $prefix . '/' . ltrim($path, '/');
    }

    /**
     * Static shorthand methods
     */
    public static function current(): string
    {
        return self::getInstance()->getCurrentLang();
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        return self::getInstance()->getContentWithFallback($key, $default);
    }

    public static function id(): int
    {
        return self::getInstance()->getCurrentLangId();
    }
}
