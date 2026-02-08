<?php
/**
 * A Casa do Gi - Base Model Class (Active Record Pattern)
 */

namespace Models;

use Core\Database;

abstract class Model
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [];
    protected static array $hidden = ['password_hash'];

    protected array $attributes = [];
    protected array $original = [];
    protected bool $exists = false;

    protected Database $db;

    public function __construct(array $attributes = [])
    {
        $this->db = Database::getInstance();
        $this->fill($attributes);
        $this->original = $this->attributes;
    }

    /**
     * Fill model with attributes
     */
    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);

            // Also set typed public properties if they exist
            if (property_exists($this, $key)) {
                try {
                    $rp = new \ReflectionProperty($this, $key);
                    if ($rp->isPublic() && !$rp->isStatic()) {
                        $type = $rp->getType();
                        if ($type && !$type->allowsNull() && $value === null) {
                            continue;
                        }
                        $this->$key = $value;
                    }
                } catch (\ReflectionException $e) {
                    // Skip if reflection fails
                }
            }
        }
        return $this;
    }

    /**
     * Set attribute value
     */
    public function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Get attribute value
     */
    public function getAttribute(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Magic getter
     */
    public function __get(string $key): mixed
    {
        return $this->getAttribute($key);
    }

    /**
     * Magic setter
     */
    public function __set(string $key, mixed $value): void
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Magic isset
     */
    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Get all attributes
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Get attributes as array (excluding hidden)
     */
    public function toArray(): array
    {
        $array = $this->attributes;
        foreach (static::$hidden as $key) {
            unset($array[$key]);
        }
        return $array;
    }

    /**
     * Check if model exists in database
     */
    public function exists(): bool
    {
        return $this->exists;
    }

    /**
     * Get primary key value
     */
    public function getKey(): mixed
    {
        return $this->getAttribute(static::$primaryKey);
    }

    /**
     * Find record by ID
     */
    public static function find(int $id): ?static
    {
        $db = Database::getInstance();
        $table = static::$table;
        $pk = static::$primaryKey;

        $row = $db->fetch("SELECT * FROM {$table} WHERE {$pk} = ?", [$id]);

        if (!$row) {
            return null;
        }

        $model = new static($row);
        $model->exists = true;
        $model->original = $row;

        return $model;
    }

    /**
     * Find record by ID or throw exception
     */
    public static function findOrFail(int $id): static
    {
        $model = static::find($id);

        if (!$model) {
            throw new \Exception("Record not found in " . static::$table . " with ID: {$id}");
        }

        return $model;
    }

    /**
     * Get all records
     */
    public static function all(string $orderBy = ''): array
    {
        $db = Database::getInstance();
        $table = static::$table;
        $sql = "SELECT * FROM {$table}";

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }

        $rows = $db->fetchAll($sql);

        return array_map(function ($row) {
            $model = new static($row);
            $model->exists = true;
            $model->original = $row;
            return $model;
        }, $rows);
    }

    /**
     * Find first record matching conditions
     */
    public static function where(string $column, mixed $value, string $operator = '='): QueryBuilder
    {
        return (new QueryBuilder(static::class))->where($column, $value, $operator);
    }

    /**
     * Find by column value
     */
    public static function findBy(string $column, mixed $value): ?static
    {
        $db = Database::getInstance();
        $table = static::$table;

        $row = $db->fetch("SELECT * FROM {$table} WHERE {$column} = ? LIMIT 1", [$value]);

        if (!$row) {
            return null;
        }

        $model = new static($row);
        $model->exists = true;
        $model->original = $row;

        return $model;
    }

    /**
     * Create new record
     */
    public static function create(array $attributes): static
    {
        $model = new static($attributes);
        $model->save();
        return $model;
    }

    /**
     * Save model (insert or update)
     */
    public function save(): bool
    {
        if ($this->exists) {
            return $this->update();
        }

        return $this->insert();
    }

    /**
     * Insert new record
     */
    protected function insert(): bool
    {
        $data = $this->getInsertData();

        if (empty($data)) {
            return false;
        }

        $id = $this->db->insert(static::$table, $data);
        $this->setAttribute(static::$primaryKey, $id);
        $this->exists = true;
        $this->original = $this->attributes;

        return true;
    }

    /**
     * Update existing record
     */
    protected function update(): bool
    {
        $data = $this->getDirtyData();

        if (empty($data)) {
            return true; // Nothing to update
        }

        $pk = static::$primaryKey;
        $id = $this->getKey();

        $affected = $this->db->update(static::$table, $data, "{$pk} = ?", [$id]);
        $this->original = $this->attributes;

        return $affected > 0;
    }

    /**
     * Delete record
     */
    public function delete(): bool
    {
        if (!$this->exists) {
            return false;
        }

        $pk = static::$primaryKey;
        $id = $this->getKey();

        $affected = $this->db->delete(static::$table, "{$pk} = ?", [$id]);

        if ($affected > 0) {
            $this->exists = false;
            return true;
        }

        return false;
    }

    /**
     * Get data for insert (fillable attributes only)
     */
    protected function getInsertData(): array
    {
        if (empty(static::$fillable)) {
            return $this->attributes;
        }

        return array_intersect_key($this->attributes, array_flip(static::$fillable));
    }

    /**
     * Get changed data
     */
    protected function getDirtyData(): array
    {
        $dirty = [];

        foreach ($this->attributes as $key => $value) {
            if (!array_key_exists($key, $this->original) || $this->original[$key] !== $value) {
                if (empty(static::$fillable) || in_array($key, static::$fillable)) {
                    $dirty[$key] = $value;
                }
            }
        }

        return $dirty;
    }

    /**
     * Check if attribute has changed
     */
    public function isDirty(string $key = null): bool
    {
        if ($key) {
            return ($this->attributes[$key] ?? null) !== ($this->original[$key] ?? null);
        }

        return !empty($this->getDirtyData());
    }

    /**
     * Refresh model from database
     */
    public function refresh(): self
    {
        if (!$this->exists) {
            return $this;
        }

        $fresh = static::find($this->getKey());

        if ($fresh) {
            $this->attributes = $fresh->attributes;
            $this->original = $fresh->original;
        }

        return $this;
    }

    /**
     * Count records
     */
    public static function count(string $where = '1=1', array $params = []): int
    {
        return Database::getInstance()->count(static::$table, $where, $params);
    }

    /**
     * Check if record exists
     */
    public static function existsWhere(string $where, array $params = []): bool
    {
        return Database::getInstance()->exists(static::$table, $where, $params);
    }
}

/**
 * Simple Query Builder for Model
 */
class QueryBuilder
{
    private string $modelClass;
    private array $wheres = [];
    private array $params = [];
    private ?string $orderBy = null;
    private ?int $limit = null;
    private ?int $offset = null;

    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    public function where(string $column, mixed $value, string $operator = '='): self
    {
        $this->wheres[] = "{$column} {$operator} ?";
        $this->params[] = $value;
        return $this;
    }

    public function whereIn(string $column, array $values): self
    {
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->wheres[] = "{$column} IN ({$placeholders})";
        $this->params = array_merge($this->params, $values);
        return $this;
    }

    public function whereNull(string $column): self
    {
        $this->wheres[] = "{$column} IS NULL";
        return $this;
    }

    public function whereNotNull(string $column): self
    {
        $this->wheres[] = "{$column} IS NOT NULL";
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $this->orderBy = "{$column} {$direction}";
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function first(): ?Model
    {
        $this->limit = 1;
        $results = $this->get();
        return $results[0] ?? null;
    }

    public function get(): array
    {
        $modelClass = $this->modelClass;
        $table = $modelClass::$table;
        $db = Database::getInstance();

        $sql = "SELECT * FROM {$table}";

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }

        if ($this->orderBy) {
            $sql .= " ORDER BY {$this->orderBy}";
        }

        if ($this->limit) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset) {
            $sql .= " OFFSET {$this->offset}";
        }

        $rows = $db->fetchAll($sql, $this->params);

        return array_map(function ($row) use ($modelClass) {
            $model = new $modelClass($row);
            $model->exists = true;
            $model->original = $row;
            return $model;
        }, $rows);
    }

    public function count(): int
    {
        $modelClass = $this->modelClass;
        $table = $modelClass::$table;
        $db = Database::getInstance();

        $sql = "SELECT COUNT(*) FROM {$table}";

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }

        return (int) $db->fetchColumn($sql, $this->params);
    }

    public function exists(): bool
    {
        return $this->count() > 0;
    }

    public function delete(): int
    {
        $modelClass = $this->modelClass;
        $table = $modelClass::$table;
        $db = Database::getInstance();

        if (empty($this->wheres)) {
            throw new \Exception("Cannot delete without WHERE clause");
        }

        $sql = "DELETE FROM {$table} WHERE " . implode(' AND ', $this->wheres);

        return $db->query($sql, $this->params)->rowCount();
    }
}
