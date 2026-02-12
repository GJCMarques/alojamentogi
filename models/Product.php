<?php

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

    public ?string $name = null;
    public ?string $short_description = null;
    public ?string $description = null;

    public ?ProductCategory $category = null;
    public array $images = [];

    public static function findWithTranslation(int $id): ?self
    {
        $db = Database::getInstance();
        $lang = Language::getInstance();
        $langId = $lang->getCurrentLangId();

        $sql = "SELECT p.*,
                       COALESCE(pt.name, pt_def.name) as name,
                       COALESCE(pt.short_description, pt_def.short_description) as short_description,
                       COALESCE(pt.description, pt_def.description) as description
                FROM products p
                LEFT JOIN product_translations pt
                    ON p.id = pt.product_id AND pt.language_id = ?
                LEFT JOIN languages l_def ON l_def.is_default = 1
                LEFT JOIN product_translations pt_def
                    ON p.id = pt_def.product_id AND pt_def.language_id = l_def.id
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

    public static function findBySlug(string $slug): ?self
    {
        $db = Database::getInstance();
        $lang = Language::getInstance();
        $langId = $lang->getCurrentLangId();

        $sql = "SELECT p.*,
                       COALESCE(pt.name, pt_def.name) as name,
                       COALESCE(pt.short_description, pt_def.short_description) as short_description,
                       COALESCE(pt.description, pt_def.description) as description
                FROM products p
                LEFT JOIN product_translations pt
                    ON p.id = pt.product_id AND pt.language_id = ?
                LEFT JOIN languages l_def ON l_def.is_default = 1
                LEFT JOIN product_translations pt_def
                    ON p.id = pt_def.product_id AND pt_def.language_id = l_def.id
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

    public static function findBySku(string $sku): ?self
    {
        $db = Database::getInstance();
        $lang = Language::getInstance();
        $langId = $lang->getCurrentLangId();

        $sql = "SELECT p.*,
                       COALESCE(pt.name, pt_def.name) as name,
                       COALESCE(pt.short_description, pt_def.short_description) as short_description,
                       COALESCE(pt.description, pt_def.description) as description
                FROM products p
                LEFT JOIN product_translations pt
                    ON p.id = pt.product_id AND pt.language_id = ?
                LEFT JOIN languages l_def ON l_def.is_default = 1
                LEFT JOIN product_translations pt_def
                    ON p.id = pt_def.product_id AND pt_def.language_id = l_def.id
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

    public static function getAllActive(?int $categoryId = null, ?int $limit = null, int $offset = 0): array
    {
        $db = Database::getInstance();
        $lang = Language::getInstance();
        $langId = $lang->getCurrentLangId();

        $params = [$langId];
        $where = "WHERE p.is_active = 1";

        if ($categoryId !== null) {
            $where .= " AND p.category_id = ?";

        }

        $sql = "SELECT p.*,
                       COALESCE(pt.name, pt_def.name) as name,
                       COALESCE(pt.short_description, pt_def.short_description) as short_description,
                       COALESCE(pt.description, pt_def.description) as description,
                       c.slug as category_slug,
                       COALESCE(ct.name, ct_def.name) as category_name,
                       (SELECT m.file_path FROM product_images pi
                        JOIN media m ON pi.media_id = m.id
                        WHERE pi.product_id = p.id AND pi.is_primary = 1 LIMIT 1) as primary_image
                FROM products p
                LEFT JOIN product_translations pt
                    ON p.id = pt.product_id AND pt.language_id = ?
                LEFT JOIN languages l_def ON l_def.is_default = 1
                LEFT JOIN product_translations pt_def
                    ON p.id = pt_def.product_id AND pt_def.language_id = l_def.id
                LEFT JOIN product_categories c ON p.category_id = c.id
                LEFT JOIN product_category_translations ct
                    ON c.id = ct.category_id AND ct.language_id = ?
                LEFT JOIN product_category_translations ct_def
                    ON c.id = ct_def.category_id AND ct_def.language_id = l_def.id
                {$where}
                ORDER BY p.is_featured DESC, p.created_at DESC";

        $params = [$langId, $langId];
        if ($categoryId !== null) {
            $params[] = $categoryId;
        }

        if ($limit !== null) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }

        $rows = $db->fetchAll($sql, $params);

        $products = [];
        foreach ($rows as $row) {
            $product = new self();
            $product->fill($row);

            $product->category_slug = $row['category_slug'] ?? null;
            $product->category_name = $row['category_name'] ?? null;
            $products[] = $product;
        }

        return $products;
    }

    public static function getFeatured(int $limit = 4): array
    {
        $db = Database::getInstance();
        $lang = Language::getInstance();
        $langId = $lang->getCurrentLangId();

        $sql = "SELECT p.*,
                       COALESCE(pt.name, pt_def.name) as name,
                       COALESCE(pt.short_description, pt_def.short_description) as short_description,
                       COALESCE(pt.description, pt_def.description) as description,
                       (SELECT m.file_path FROM product_images pi
                        JOIN media m ON pi.media_id = m.id
                        WHERE pi.product_id = p.id AND pi.is_primary = 1 LIMIT 1) as primary_image
                FROM products p
                LEFT JOIN product_translations pt
                    ON p.id = pt.product_id AND pt.language_id = ?
                LEFT JOIN languages l_def ON l_def.is_default = 1
                LEFT JOIN product_translations pt_def
                    ON p.id = pt_def.product_id AND pt_def.language_id = l_def.id
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

    public function loadCategory(): void
    {
        $this->category = ProductCategory::findWithTranslation($this->category_id);
    }

    public function getPrimaryImage(): ?string
    {

        if (isset($this->primary_image) && $this->primary_image) {
            return $this->primary_image;
        }

        foreach ($this->images as $image) {
            if ($image['is_primary']) {
                return $image['image_path'];
            }
        }

        if (!empty($this->images)) {
            return $this->images[0]['image_path'];
        }

        return null;
    }

    public function getCurrentPrice(): float
    {
        if ($this->sale_price !== null && $this->sale_price > 0) {
            return $this->sale_price;
        }
        return $this->price;
    }

    public function isOnSale(): bool
    {
        return $this->sale_price !== null && $this->sale_price > 0 && $this->sale_price < $this->price;
    }

    public function isInStock(): bool
    {
        if (!$this->track_inventory) {
            return true;
        }
        return $this->stock_quantity > 0;
    }

    public function getDiscountPercentage(): int
    {
        if (!$this->isOnSale()) {
            return 0;
        }
        return (int)round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    public function saveTranslation(int $languageId, string $name, ?string $shortDescription = null, ?string $description = null): bool
    {
        $db = Database::getInstance();

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

    public function addImage(int $mediaId, bool $isPrimary = false, int $sortOrder = 0): int
    {
        $db = Database::getInstance();

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

    public static function search(string $query, int $limit = 20): array
    {
        $db = Database::getInstance();
        $lang = Language::getInstance();
        $langId = $lang->getCurrentLangId();

        $searchTerm = '%' . $query . '%';

        $sql = "SELECT p.*,
                       COALESCE(pt.name, pt_def.name) as name,
                       COALESCE(pt.short_description, pt_def.short_description) as short_description,
                       COALESCE(pt.description, pt_def.description) as description,
                       (SELECT m.file_path FROM product_images pi
                        JOIN media m ON pi.media_id = m.id
                        WHERE pi.product_id = p.id AND pi.is_primary = 1 LIMIT 1) as primary_image
                FROM products p
                LEFT JOIN product_translations pt
                    ON p.id = pt.product_id AND pt.language_id = ?
                LEFT JOIN languages l_def ON l_def.is_default = 1
                LEFT JOIN product_translations pt_def
                    ON p.id = pt_def.product_id AND pt_def.language_id = l_def.id
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
