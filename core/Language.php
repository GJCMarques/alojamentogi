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

        // Check if path starts with /en/
        if (strpos($path, '/en/') === 0 || $path === '/en') {
            $this->setLanguage(LANG_EN);
            return;
        }

        // Check session
        Session::start();
        $sessionLang = Session::getLanguage();

        if (isset($this->languages[$sessionLang])) {
            $this->currentLang = $sessionLang;
            $this->currentLangId = (int) $this->languages[$sessionLang]['id'];
            return;
        }

        // Default to PT
        $this->setLanguage(DEFAULT_LANG);
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

        // Current is PT, switching to EN
        if ($this->currentLang === LANG_PT && $toLang === LANG_EN) {
            // Map PT paths to EN paths
            $pathMap = [
                '/' => '/en/',
                '/alojamento' => '/en/accommodation',
                '/loja' => '/en/shop',
                '/atividades' => '/en/activities',
                '/contactos' => '/en/contact',
                '/sobre-nos' => '/en/about-us',
            ];

            foreach ($pathMap as $pt => $en) {
                if ($path === $pt || strpos($path, $pt . '/') === 0) {
                    return str_replace($pt, $en, $path) . $queryString;
                }
            }

            return '/en/' . ltrim($path, '/') . $queryString;
        }

        // Current is EN, switching to PT
        if ($this->currentLang === LANG_EN && $toLang === LANG_PT) {
            // Map EN paths to PT paths
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
                if ($path === $en || strpos($path, $en . '/') === 0) {
                    $newPath = str_replace($en, $pt, $path);
                    return ($newPath === '' ? '/' : $newPath) . $queryString;
                }
            }

            // Remove /en/ prefix
            $newPath = preg_replace('/^\/en\/?/', '/', $path);
            return ($newPath === '' ? '/' : $newPath) . $queryString;
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
