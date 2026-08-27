<?php

declare(strict_types=1);

namespace Core\Validation;

abstract class Validator
{
    /**
     * Validation errors.
     *
     * @var array<string, string>
     */
    protected array $errors = [];

    /**
     * Safely retrieve an input value.
     *
     * @param array<string, mixed> $input
     */
    protected function input(
        array $input,
        string $key,
        mixed $default = ''
    ): mixed
    {
        return $input[$key] ?? $default;
    }

    /**
     * Child validators must implement validation.
     *
     * @param array<string, mixed> $input
     */
    abstract public function validate(
        array $input
    ): bool;

    /**
     * Add a validation error.
     */
    protected function addError(
        string $field,
        string $message
    ): void
    {
        $this->errors[$field] = $message;
    }

    /**
     * Remove all validation errors.
     */
    protected function clearErrors(): void
    {
        $this->errors = [];
    }

    /**
     * Return all validation errors.
     *
     * @return array<string, string>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Determine whether validation passed.
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Determine whether validation failed.
     */
    public function fails(): bool
    {
        return !$this->passes();
    }

    /**
     * Determine whether a field has an error.
     */
    public function hasError(
        string $field
    ): bool
    {
        return isset($this->errors[$field]);
    }

    /**
     * Return the first error for a field.
     */
    public function firstError(
        string $field
    ): ?string
    {
        return $this->errors[$field] ?? null;
    }

    /**
     * Return the first validation error.
     */
    public function first(): ?string
    {
        if ($this->passes()) {

            return null;
        }

        return reset($this->errors) ?: null;
    }
}

