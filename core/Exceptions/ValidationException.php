<?php

declare(strict_types=1);

namespace Core\Exceptions;

final class ValidationException extends HttpException
{
    /**
     * Validation errors.
     *
     * @var array<string,mixed>
     */
    private array $errors;


    /**
     * Create validation exception.
     *
     * @param array<string,mixed> $errors
     */
    public function __construct(
        array $errors,
        string $message = 'Validation failed.'
    )
    {
        parent::__construct(
            422,
            $message,
            [
                'errors' => $errors
            ]
        );

        $this->errors = $errors;
    }


    /**
     * Return validation errors.
     *
     * @return array<string,mixed>
     */
    public function errors(): array
    {
        return $this->errors;
    }
}