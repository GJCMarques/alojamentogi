<?php

namespace Models;

use Core\Database;
use Core\Language;

class ProductCategory extends Model
{
    protected static string $table = 'product_categories';

    public int $id;
    public ?int $parent_id = null;
    public string $slug;
    public ?string $image = null;
    public int $sort_order = 0;
    public int $is_active = 1;
    public string $created_at;
    public string $updated_at;

    public ?string $name = null;
    public ?string $description = null;

    public static function findWithTranslation(int $id): ?self
    {
        $db = Database::getInstance();
        $lang = Language::getInstance();
        $langId = $lang->getCurrentLangId();

        $sql = "SELECT c.*,
                       COALESCE(ct.name, ct_def.name) as name,
                       COALESCE(ct.description, ct_def.description) as description
                FROM product_categories c
                LEFT JOIN product_category_translations ct
                    ON c.id = ct.category_id AND ct.language_id = ?
                LEFT JOIN languages l_def ON l_def.is_default = 1
                LEFT JOIN product_category_translations ct_def
                    ON c.id = ct_def.category_id AND ct_def.language_id = l_def.id
                WHERE c.id = ?";

        $row = $db->fetch($sql, [$langId, $id]);

        if (!$row) {
            return null;
        }

        $category = new self();
        $category->fill($row);
        return $category;
    }

    public static function getAllActive(): array
    {
        $db = Database::getInstance();
        $lang = Language::getInstance();
        $langId = $lang->getCurrentLangId();

        $sql = "SELECT c.*,
                       COALESCE(ct.name, ct_def.name) as name,
                       COALESCE(ct.description, ct_def.description) as description
                FROM product_categories c
                LEFT JOIN product_category_translations ct
                    ON c.id = ct.category_id AND ct.language_id = ?
                LEFT JOIN languages l_def ON l_def.is_default = 1
                LEFT JOIN product_category_translations ct_def
                    ON c.id = ct_def.category_id AND ct_def.language_id = l_def.id
                WHERE c.is_active = 1
                ORDER BY c.sort_order ASC, name ASC";

        $rows = $db->fetchAll($sql, [$langId]);

        $categories = [];
        foreach ($rows as $row) {
            $category = new self();
            $category->fill($row);
            $categories[] = $category;
        }

        return $categories;
    }

    public static function findBySlug(string $slug): ?self
    {
        $db = Database::getInstance();
        $lang = Language::getInstance();
        $langId = $lang->getCurrentLangId();

        $sql = "SELECT c.*,
                       COALESCE(ct.name, ct_def.name) as name,
                       COALESCE(ct.description, ct_def.description) as description
                FROM product_categories c
                LEFT JOIN product_category_translations ct
                    ON c.id = ct.category_id AND ct.language_id = ?
                LEFT JOIN languages l_def ON l_def.is_default = 1
                LEFT JOIN product_category_translations ct_def
                    ON c.id = ct_def.category_id AND ct_def.language_id = l_def.id
                WHERE c.slug = ? AND c.is_active = 1";

        $row = $db->fetch($sql, [$langId, $slug]);

        if (!$row) {
            return null;
        }

        $category = new self();
        $category->fill($row);
        return $category;
    }

    public function getProductCount(): int
    {
        $db = Database::getInstance();

        $sql = "SELECT COUNT(*) as count
                FROM products
                WHERE category_id = ? AND is_active = 1";

        $row = $db->fetch($sql, [$this->id]);

        return (int)($row['count'] ?? 0);
    }

    public function saveTranslation(int $languageId, string $name, ?string $description = null): bool
    {
        $db = Database::getInstance();

        $existing = $db->fetch(
            "SELECT id FROM product_category_translations WHERE category_id = ? AND language_id = ?",
            [$this->id, $languageId]
        );

        if ($existing) {
            return $db->update('product_category_translations', [
                'name' => $name,
                'description' => $description
            ], 'id = ?', [$existing['id']]) !== false;
        } else {
            return $db->insert('product_category_translations', [
                'category_id' => $this->id,
                'language_id' => $languageId,
                'name' => $name,
                'description' => $description
            ]) > 0;
        }
    }
}
