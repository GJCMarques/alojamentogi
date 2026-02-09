<?php
/**
 * A Casa do Gi - Product Model
 */

namespace Models;

use Core\Database;
use Core\Language;

class Product extends Model
{
    protected static string $table = 'products';

    public int $id;
    public int $category_id;
    public string $slug;
    public string $sku;
    public float $price;
    public ?float $sale_price = null;
    public int $stock_quantity = 0;
    public bool $track_inventory = true;
    public ?float $weight = null;
    public int $is_featured = 0;
    public int $is_active = 1;
    public string $created_at;
    public string $updated_at;

    // Translation fields (loaded dynamically)
    public ?string $name = null;
    public ?string $short_description = null;
    public ?string $description = null;

    // Related data
    public ?ProductCategory $category = null;
    public array $images = [];

    /**
     * Get product with translation for current language
     */
    public static function findWithTranslation(int $id): ?self
    {
        $db = Database::getInstance();
        $lang = Language::getInstance();
        $langId = $lang->getCurrentLangId();

        $sql = "SELECT p.*, pt.name, pt.short_description, pt.description
                FROM products p
                LEFT JOIN product_translations pt
                    ON p.id = pt.product_id AND pt.language_id = ?
                WHERE p.id = ?";

        $row = $db->fetch($sql, [$langId, $id]);

        if (!$row) {
            return null;
        }

        $product = new self();
        $product->fill($row);
        $product->loadImages();
        $product->loadCategory();

        return $product;
    }

    /**
     * Get product by slug with translation
     */
    public static function findBySlug(string $slug): ?self
    {
        $db = Database::getInstance();
        $lang = Language::getInstance();
        $langId = $lang->getCurrentLangId();

        $sql = "SELECT p.*, pt.name, pt.short_description, pt.description
                FROM products p
                LEFT JOIN product_translations pt
                    ON p.id = pt.product_id AND pt.language_id = ?
                WHERE p.slug = ? AND p.is_active = 1";

        $row = $db->fetch($sql, [$langId, $slug]);

        if (!$row) {
            return null;
        }

        $product = new self();
        $product->fill($row);
        $product->loadImages();
        $product->loadCategory();

        return $product;
    }

    /**
     * Get product by SKU with translation
     */
    public static function findBySku(string $sku): ?self
    {
        $db = Database::getInstance();
        $lang = Language::getInstance();
        $langId = $lang->getCurrentLangId();

        $sql = "SELECT p.*, pt.name, pt.short_description, pt.description
                FROM products p
                LEFT JOIN product_translations pt
                    ON p.id = pt.product_id AND pt.language_id = ?
                WHERE p.sku = ? AND p.is_active = 1";

        $row = $db->fetch($sql, [$langId, $sku]);

        if (!$row) {
            return null;
        }

        $product = new self();
        $product->fill($row);
        $product->loadImages();
        $product->loadCategory();

        return $product;
    }

    /**
     * Get all active products with translations
     */
    public static function getAllActive(?int $categoryId = null, ?int $limit = null, int $offset = 0): array
    {
        $db = Database::getInstance();
        $lang = Language::getInstance();
        $langId = $lang->getCurrentLangId();

        $params = [$langId];
        $where = "WHERE p.is_active = 1";

        if ($categoryId !== null) {
            $where .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }

        $sql = "SELECT p.*, pt.name, pt.short_description, pt.description,
                       (SELECT m.file_path FROM product_images pi
                        JOIN media m ON pi.media_id = m.id
                        WHERE pi.product_id = p.id AND pi.is_primary = 1 LIMIT 1) as primary_image
                FROM products p
                LEFT JOIN product_translations pt
                    ON p.id = pt.product_id AND pt.language_id = ?
                {$where}
                ORDER BY p.is_featured DESC, p.created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }

        $rows = $db->fetchAll($sql, $params);

        $products = [];
        foreach ($rows as $row) {
            $product = new self();
            $product->fill($row);
            $products[] = $product;
        }

        return $products;
    }

    /**
     * Get featured products
     */
    public static function getFeatured(int $limit = 4): array
    {
        $db = Database::getInstance();
        $lang = Language::getInstance();
        $langId = $lang->getCurrentLangId();

        $sql = "SELECT p.*, pt.name, pt.short_description, pt.description,
                       (SELECT m.file_path FROM product_images pi
                        JOIN media m ON pi.media_id = m.id
                        WHERE pi.product_id = p.id AND pi.is_primary = 1 LIMIT 1) as primary_image
                FROM products p
                LEFT JOIN product_translations pt
                    ON p.id = pt.product_id AND pt.language_id = ?
                WHERE p.is_active = 1 AND p.is_featured = 1
                ORDER BY p.created_at DESC
                LIMIT {$limit}";

        $rows = $db->fetchAll($sql, [$langId]);

        $products = [];
        foreach ($rows as $row) {
            $product = new self();
            $product->fill($row);
            $products[] = $product;
        }

        return $products;
    }

    /**
     * Count total active products
     */
    public static function countActive(?int $categoryId = null): int
    {
        $db = Database::getInstance();

        $params = [];
        $where = "WHERE is_active = 1";

        if ($categoryId !== null) {
            $where .= " AND category_id = ?";
            $params[] = $categoryId;
        }

        $sql = "SELECT COUNT(*) as count FROM products {$where}";
        $row = $db->fetch($sql, $params);

        return (int)($row['count'] ?? 0);
    }

    /**
     * Load product images
     */
    public function loadImages(): void
    {
        $db = Database::getInstance();

        $sql = "SELECT pi.*, m.file_path as image_path
                FROM product_images pi
                JOIN media m ON pi.media_id = m.id
                WHERE pi.product_id = ?
                ORDER BY pi.is_primary DESC, pi.sort_order ASC";

        $this->images = $db->fetchAll($sql, [$this->id]);
    }

    /**
     * Load product category
     */
    public function loadCategory(): void
    {
        $this->category = ProductCategory::findWithTranslation($this->category_id);
    }

    /**
     * Get primary image path
     */
    public function getPrimaryImage(): ?string
    {
        // Check if primary_image was loaded in the query
        if (isset($this->primary_image) && $this->primary_image) {
            return $this->primary_image;
        }

        // Otherwise search in images array
        foreach ($this->images as $image) {
            if ($image['is_primary']) {
                return $image['image_path'];
            }
        }

        // Return first image if no primary
        if (!empty($this->images)) {
            return $this->images[0]['image_path'];
        }

        return null;
    }

    /**
     * Get current price (sale price if available, otherwise regular price)
     */
    public function getCurrentPrice(): float
    {
        if ($this->sale_price !== null && $this->sale_price > 0) {
            return $this->sale_price;
        }
        return $this->price;
    }

    /**
     * Check if product is on sale
     */
    public function isOnSale(): bool
    {
        return $this->sale_price !== null && $this->sale_price > 0 && $this->sale_price < $this->price;
    }

    /**
     * Check if product is in stock
     */
    public function isInStock(): bool
    {
        if (!$this->track_inventory) {
            return true;
        }
        return $this->stock_quantity > 0;
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentage(): int
    {
        if (!$this->isOnSale()) {
            return 0;
        }
        return (int)round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    /**
     * Save translation for a specific language
     */
    public function saveTranslation(int $languageId, string $name, ?string $shortDescription = null, ?string $description = null): bool
    {
        $db = Database::getInstance();

        // Check if translation exists
        $existing = $db->fetch(
            "SELECT id FROM product_translations WHERE product_id = ? AND language_id = ?",
            [$this->id, $languageId]
        );

        if ($existing) {
            return $db->update('product_translations', [
                'name' => $name,
                'short_description' => $shortDescription,
                'description' => $description
            ], 'id = ?', [$existing['id']]) !== false;
        } else {
            return $db->insert('product_translations', [
                'product_id' => $this->id,
                'language_id' => $languageId,
                'name' => $name,
                'short_description' => $shortDescription,
                'description' => $description
            ]) > 0;
        }
    }

    /**
     * Add image to product (using media_id)
     */
    public function addImage(int $mediaId, bool $isPrimary = false, int $sortOrder = 0): int
    {
        $db = Database::getInstance();

        // If this is primary, unset other primaries
        if ($isPrimary) {
            $db->update('product_images', ['is_primary' => 0], 'product_id = ?', [$this->id]);
        }

        return $db->insert('product_images', [
            'product_id' => $this->id,
            'media_id' => $mediaId,
            'is_primary' => $isPrimary ? 1 : 0,
            'sort_order' => $sortOrder
        ]);
    }

    /**
     * Search products
     */
    public static function search(string $query, int $limit = 20): array
    {
        $db = Database::getInstance();
        $lang = Language::getInstance();
        $langId = $lang->getCurrentLangId();

        $searchTerm = '%' . $query . '%';

        $sql = "SELECT p.*, pt.name, pt.short_description, pt.description,
                       (SELECT m.file_path FROM product_images pi
                        JOIN media m ON pi.media_id = m.id
                        WHERE pi.product_id = p.id AND pi.is_primary = 1 LIMIT 1) as primary_image
                FROM products p
                LEFT JOIN product_translations pt
                    ON p.id = pt.product_id AND pt.language_id = ?
                WHERE p.is_active = 1
                  AND (pt.name LIKE ? OR pt.short_description LIKE ? OR p.sku LIKE ?)
                ORDER BY p.is_featured DESC, pt.name ASC
                LIMIT {$limit}";

        $rows = $db->fetchAll($sql, [$langId, $searchTerm, $searchTerm, $searchTerm]);

        $products = [];
        foreach ($rows as $row) {
            $product = new self();
            $product->fill($row);
            $products[] = $product;
        }

        return $products;
    }
}
