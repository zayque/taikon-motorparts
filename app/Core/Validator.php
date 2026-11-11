<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Small rule-based validator.
 *
 *   $v = Validator::make($data, ['email' => 'required|email|max:120']);
 *   if ($v->fails()) { ... $v->errors() ... }
 *
 * Rules: required, email, min:n, max:n (string length), numeric, integer,
 * gte:n (numeric >=), in:a,b,c, date, after_today, confirmed, phone, regex:/x/
 */
final class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $rules
     * @param array<string, string> $labels
     */
    private function __construct(private array $data, array $rules, private array $labels)
    {
        foreach ($rules as $field => $ruleString) {
            $this->validateField($field, explode('|', $ruleString));
        }
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $rules
     * @param array<string, string> $labels
     */
    public static function make(array $data, array $rules, array $labels = []): self
    {
        return new self($data, $rules, $labels);
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    /** @return array<string, string> */
    public function errors(): array
    {
        return $this->errors;
    }

    public function addError(string $field, string $message): void
    {
        $this->errors[$field] ??= $message;
    }

    /** @param list<string> $rules */
    private function validateField(string $field, array $rules): void
    {
        $value = $this->data[$field] ?? null;
        $value = is_string($value) ? trim($value) : $value;
        $empty = $value === null || $value === '' || $value === [];
        $label = $this->labels[$field] ?? ucfirst(str_replace('_', ' ', $field));

        if (!in_array('required', $rules, true) && $empty) {
            return;
        }

        foreach ($rules as $rule) {
            [$name, $param] = array_pad(explode(':', $rule, 2), 2, null);

            $error = match ($name) {
                'required'    => $empty ? "$label is required." : null,
                'email'       => filter_var($value, FILTER_VALIDATE_EMAIL) ? null : "Enter a valid email address.",
                'min'         => mb_strlen((string) $value) < (int) $param ? "$label must be at least $param characters." : null,
                'max'         => mb_strlen((string) $value) > (int) $param ? "$label may not exceed $param characters." : null,
                'numeric'     => is_numeric($value) ? null : "$label must be a number.",
                'integer'     => filter_var($value, FILTER_VALIDATE_INT) !== false ? null : "$label must be a whole number.",
                'gte'         => is_numeric($value) && (float) $value >= (float) $param ? null : "$label must be at least $param.",
                'in'          => in_array((string) $value, explode(',', (string) $param), true) ? null : "Choose a valid $label.",
                'date'        => self::isDate((string) $value) ? null : "$label must be a valid date.",
                'after_today' => self::isDate((string) $value) && $value > date('Y-m-d') ? null : "$label must be a future date.",
                'confirmed'   => ($value === ($this->data[$field . '_confirmation'] ?? null)) ? null : "$label confirmation does not match.",
                'phone'       => preg_match('/^\+?[0-9\s\-()]{8,20}$/', (string) $value) ? null : "Enter a valid phone number.",
                'regex'       => preg_match((string) $param, (string) $value) ? null : "$label format is invalid.",
                default       => null,
            };

            if ($error !== null) {
                $this->addError($field, $error);
                return;
            }
        }
    }

    private static function isDate(string $value): bool
    {
        $d = \DateTimeImmutable::createFromFormat('Y-m-d', $value);

        return $d !== false && $d->format('Y-m-d') === $value;
    }
}
