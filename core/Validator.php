<?php
/**
 * A Casa do Gi - Input Validation Class
 */

namespace Core;

class Validator
{
    private array $errors = [];
    private array $data = [];
    private array $rules = [];

    /**
     * Create new validator instance
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Static factory method
     */
    public static function make(array $data, array $rules): self
    {
        $validator = new self($data);
        $validator->rules = $rules;
        $validator->validate();
        return $validator;
    }

    /**
     * Validate data against rules
     */
    public function validate(): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $ruleSet) {
            $rules = is_array($ruleSet) ? $ruleSet : explode('|', $ruleSet);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    /**
     * Apply a single rule to a field
     */
    private function applyRule(string $field, mixed $value, string $rule): void
    {
        // Parse rule with parameters (e.g., "min:3" or "between:1,10")
        $params = [];
        if (strpos($rule, ':') !== false) {
            [$rule, $paramStr] = explode(':', $rule, 2);
            $params = explode(',', $paramStr);
        }

        $method = 'validate' . ucfirst($rule);

        if (method_exists($this, $method)) {
            if (!$this->$method($field, $value, $params)) {
                $this->addRuleError($field, $rule, $params);
            }
        }
    }

    /**
     * Add error message from validation rule
     */
    private function addRuleError(string $field, string $rule, array $params = []): void
    {
        $messages = [
            'required' => 'O campo :field é obrigatório.',
            'email' => 'O campo :field deve ser um email válido.',
            'min' => 'O campo :field deve ter pelo menos :param0 caracteres.',
            'max' => 'O campo :field não pode ter mais de :param0 caracteres.',
            'numeric' => 'O campo :field deve ser numérico.',
            'integer' => 'O campo :field deve ser um número inteiro.',
            'phone' => 'O campo :field deve ser um número de telefone válido.',
            'url' => 'O campo :field deve ser um URL válido.',
            'date' => 'O campo :field deve ser uma data válida.',
            'confirmed' => 'A confirmação do campo :field não corresponde.',
            'unique' => 'O valor do campo :field já existe.',
            'exists' => 'O valor do campo :field não existe.',
            'in' => 'O valor do campo :field é inválido.',
            'regex' => 'O formato do campo :field é inválido.',
            'alpha' => 'O campo :field deve conter apenas letras.',
            'alphanumeric' => 'O campo :field deve conter apenas letras e números.',
            'decimal' => 'O campo :field deve ser um número decimal válido.',
            'positive' => 'O campo :field deve ser um número positivo.',
        ];

        $message = $messages[$rule] ?? "O campo :field é inválido.";

        // Replace placeholders
        $message = str_replace(':field', $this->getFieldLabel($field), $message);
        foreach ($params as $i => $param) {
            $message = str_replace(":param{$i}", $param, $message);
        }

        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    /**
     * Get human-readable field label
     */
    private function getFieldLabel(string $field): string
    {
        $labels = [
            'name' => 'nome',
            'email' => 'email',
            'phone' => 'telefone',
            'subject' => 'assunto',
            'message' => 'mensagem',
            'password' => 'palavra-passe',
            'password_confirmation' => 'confirmação da palavra-passe',
            'username' => 'utilizador',
            'title' => 'título',
            'description' => 'descrição',
            'price' => 'preço',
            'quantity' => 'quantidade',
            'address' => 'morada',
            'city' => 'cidade',
            'postal_code' => 'código postal',
        ];

        return $labels[$field] ?? str_replace('_', ' ', $field);
    }

    // ==================== Validation Rules ====================

    protected function validateRequired(string $field, mixed $value, array $params): bool
    {
        if (is_null($value)) return false;
        if (is_string($value) && trim($value) === '') return false;
        if (is_array($value) && count($value) === 0) return false;
        return true;
    }

    protected function validateEmail(string $field, mixed $value, array $params): bool
    {
        if (empty($value)) return true; // Let 'required' handle empty
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function validateMin(string $field, mixed $value, array $params): bool
    {
        if (empty($value)) return true;
        $min = (int) ($params[0] ?? 0);
        return mb_strlen($value) >= $min;
    }

    protected function validateMax(string $field, mixed $value, array $params): bool
    {
        if (empty($value)) return true;
        $max = (int) ($params[0] ?? 0);
        return mb_strlen($value) <= $max;
    }

    protected function validateNumeric(string $field, mixed $value, array $params): bool
    {
        if (empty($value)) return true;
        return is_numeric($value);
    }

    protected function validateInteger(string $field, mixed $value, array $params): bool
    {
        if (empty($value)) return true;
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    protected function validateDecimal(string $field, mixed $value, array $params): bool
    {
        if (empty($value)) return true;
        return preg_match('/^\d+(\.\d{1,2})?$/', $value);
    }

    protected function validatePositive(string $field, mixed $value, array $params): bool
    {
        if (empty($value)) return true;
        return is_numeric($value) && $value > 0;
    }

    protected function validatePhone(string $field, mixed $value, array $params): bool
    {
        if (empty($value)) return true;
        // Portuguese phone format or international
        $cleaned = preg_replace('/[\s\-\(\)]+/', '', $value);
        return preg_match('/^(\+?351)?[0-9]{9,15}$/', $cleaned);
    }

    protected function validateUrl(string $field, mixed $value, array $params): bool
    {
        if (empty($value)) return true;
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    protected function validateDate(string $field, mixed $value, array $params): bool
    {
        if (empty($value)) return true;
        $format = $params[0] ?? 'Y-m-d';
        $d = \DateTime::createFromFormat($format, $value);
        return $d && $d->format($format) === $value;
    }

    protected function validateConfirmed(string $field, mixed $value, array $params): bool
    {
        $confirmField = $field . '_confirmation';
        return isset($this->data[$confirmField]) && $value === $this->data[$confirmField];
    }

    protected function validateIn(string $field, mixed $value, array $params): bool
    {
        if (empty($value)) return true;
        return in_array($value, $params);
    }

    protected function validateRegex(string $field, mixed $value, array $params): bool
    {
        if (empty($value)) return true;
        return preg_match($params[0] ?? '/.*/', $value);
    }

    protected function validateAlpha(string $field, mixed $value, array $params): bool
    {
        if (empty($value)) return true;
        return preg_match('/^[\pL\s]+$/u', $value);
    }

    protected function validateAlphanumeric(string $field, mixed $value, array $params): bool
    {
        if (empty($value)) return true;
        return preg_match('/^[\pL\pN\s]+$/u', $value);
    }

    // ==================== Fluent Convenience Methods ====================

    /**
     * Validate a value is not empty (fluent API)
     */
    public function required(mixed $value, string $field, string $message = ''): self
    {
        $isEmpty = is_null($value) || (is_string($value) && trim($value) === '') || (is_array($value) && count($value) === 0);
        if ($isEmpty) {
            if (!isset($this->errors[$field])) {
                $this->errors[$field] = [];
            }
            $this->errors[$field][] = $message ?: "O campo {$field} e obrigatorio.";
        }
        return $this;
    }

    /**
     * Validate a value is a valid email (fluent API)
     */
    public function email(mixed $value, string $field, string $message = ''): self
    {
        if (!empty($value) && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            if (!isset($this->errors[$field])) {
                $this->errors[$field] = [];
            }
            $this->errors[$field][] = $message ?: "O campo {$field} deve ser um email valido.";
        }
        return $this;
    }

    /**
     * Add a custom error (public, used by controllers)
     */
    public function addError(string $field, string $message): self
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
        return $this;
    }

    /**
     * Get errors as flat associative array (first error per field)
     */
    public function getErrors(): array
    {
        $flat = [];
        foreach ($this->errors as $field => $messages) {
            $flat[$field] = $messages[0] ?? '';
        }
        return $flat;
    }

    // ==================== Result Methods ====================

    /**
     * Check if validation passed
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Check if validation failed
     */
    public function fails(): bool
    {
        return !$this->passes();
    }

    /**
     * Get all errors
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Get first error for a field
     */
    public function error(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    /**
     * Get all errors as flat array
     */
    public function allErrors(): array
    {
        $all = [];
        foreach ($this->errors as $fieldErrors) {
            $all = array_merge($all, $fieldErrors);
        }
        return $all;
    }

    /**
     * Get first error message
     */
    public function firstError(): ?string
    {
        foreach ($this->errors as $fieldErrors) {
            if (!empty($fieldErrors)) {
                return $fieldErrors[0];
            }
        }
        return null;
    }

    /**
     * Get validated data (only fields that were in rules)
     */
    public function validated(): array
    {
        return array_intersect_key($this->data, $this->rules);
    }

    /**
     * Check if field has error
     */
    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]) && !empty($this->errors[$field]);
    }
}
