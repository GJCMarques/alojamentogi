<?php

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

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadLanguages(): void
    {
        $result = $this->db->fetchAll(
            "SELECT * FROM languages WHERE is_active = 1 ORDER BY is_default DESC"
        );

        foreach ($result as $lang) {
            $this->languages[$lang['code']] = $lang;
        }
    }

    private function detectLanguage(): void
    {

        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);

        $base = $this->getBasePath();

        $relativePath = $path;
        if ($base !== '' && strpos($path, $base) === 0) {
            $relativePath = substr($path, strlen($base));
        }

        if (empty($relativePath) || $relativePath[0] !== '/') {
            $relativePath = '/' . $relativePath;
        }

        if (strpos($relativePath, '/en/') === 0 || $relativePath === '/en') {
            $this->setLanguage(LANG_EN);
            return;
        }

        if (defined('IS_404') && Session::get(SESSION_LANG) === LANG_EN) {
            $this->setLanguage(LANG_EN);
            return;
        }

        $this->setLanguage(LANG_PT);
    }

    private function getBasePath(): string
    {
        static $basePath = null;

        if ($basePath === null) {
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $dir = dirname($scriptName);
            $dir = str_replace('\\', '/', $dir);

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

    public function setLanguage(string $code): void
    {
        if (!isset($this->languages[$code])) {
            $code = DEFAULT_LANG;
        }

        $this->currentLang = $code;
        $this->currentLangId = (int) $this->languages[$code]['id'];
        Session::setLanguage($code);

        $this->contentCache = [];
    }

    public function getCurrentLang(): string
    {
        return $this->currentLang;
    }

    public function getCurrentLangId(): int
    {
        return $this->currentLangId;
    }

    public function isEnglish(): bool
    {
        return $this->currentLang === LANG_EN;
    }

    public function isPortuguese(): bool
    {
        return $this->currentLang === LANG_PT;
    }

    public function getLanguages(): array
    {
        return $this->languages;
    }

    public function getLanguageInfo(string $code): ?array
    {
        return $this->languages[$code] ?? null;
    }

    public function getContent(string $key, ?string $default = null): ?string
    {

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

    public function getContentWithFallback(string $key, ?string $default = null): ?string
    {
        $content = $this->getContent($key);

        if ($content === null && $this->currentLang !== DEFAULT_LANG) {

            $result = $this->db->fetch(
                "SELECT content FROM content_blocks WHERE block_key = ? AND language_id = ?",
                [$key, $this->languages[DEFAULT_LANG]['id']]
            );
            $content = $result['content'] ?? null;
        }

        return $content ?? $default;
    }

    public function getContents(array $keys): array
    {
        $contents = [];
        $keysToFetch = [];

        foreach ($keys as $key) {
            $cacheKey = $this->currentLang . '_' . $key;
            if (isset($this->contentCache[$cacheKey])) {
                $contents[$key] = $this->contentCache[$cacheKey];
            } else {
                $keysToFetch[] = $key;
            }
        }

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

    public function getSwitchUrl(string $toLang): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        $query = parse_url($uri, PHP_URL_QUERY);
        $queryString = $query ? '?' . $query : '';

        $base = parse_url(basePath(), PHP_URL_PATH) ?? '';
        $base = rtrim($base, '/');

        $relPath = $path;
        if ($base !== '' && strpos($path, $base) === 0) {
            $relPath = substr($path, strlen($base));
        }

        if (empty($relPath) || $relPath[0] !== '/') {
            $relPath = '/' . $relPath;
        }

        if ($this->currentLang === LANG_PT && $toLang === LANG_EN) {
            $pathMap = [
                '/' => '/en/',
                '/alojamento' => '/en/accommodation',

                '/loja/produto' => '/en/shop/product',
                '/loja/carrinho' => '/en/shop/cart',
                '/loja/checkout' => '/en/shop/checkout',
                '/loja' => '/en/shop',
                '/atividades' => '/en/activities',
                '/contactos' => '/en/contact',
                '/sobre-nos' => '/en/about-us',
                '/politica-privacidade' => '/en/privacy-policy',
                '/termos-condicoes' => '/en/terms-and-conditions',
            ];

            foreach ($pathMap as $pt => $en) {

                if ($relPath === $pt || ($pt !== '/' && strpos($relPath, $pt . '/') === 0)) {

                    $newRelPath = ($pt === '/') ? $en : str_replace($pt, $en, $relPath);
                    return $base . $newRelPath . $queryString;
                }
            }

            if (strpos($relPath, '/en/') !== 0) {

                if ($relPath === '/') {
                    return $base . '/en/' . $queryString;
                }
                return $base . '/en' . $relPath . $queryString;
            }
        }

        if ($this->currentLang === LANG_EN && $toLang === LANG_PT) {
            $pathMap = [
                '/en/accommodation' => '/alojamento',

                '/en/shop/product' => '/loja/produto',
                '/en/shop/cart' => '/loja/carrinho',
                '/en/shop/checkout' => '/loja/checkout',
                '/en/shop' => '/loja',
                '/en/activities' => '/atividades',
                '/en/contact' => '/contactos',
                '/en/about-us' => '/sobre-nos',
                '/en/privacy-policy' => '/politica-privacidade',
                '/en/terms-and-conditions' => '/termos-condicoes',
                '/en/' => '/',
                '/en' => '/',
            ];

            foreach ($pathMap as $en => $pt) {
                if ($relPath === $en || strpos($relPath, $en . '/') === 0) {
                    $newRelPath = str_replace($en, $pt, $relPath);

                    if ($newRelPath === '') $newRelPath = '/';
                    return $base . $newRelPath . $queryString;
                }
            }

            $newRelPath = preg_replace('/^\/en(\/|$)/', '/', $relPath);
            return $base . $newRelPath . $queryString;
        }

        return $uri;
    }

    public function getUrlPrefix(): string
    {
        return $this->currentLang === LANG_EN ? '/en' : '';
    }

    public function url(string $path): string
    {
        $base = basePath();
        $prefix = $this->getUrlPrefix();
        return $base . $prefix . '/' . ltrim($path, '/');
    }

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
