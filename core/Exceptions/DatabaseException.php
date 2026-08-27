<?php

declare(strict_types=1);

namespace Core\Exceptions;

use Throwable;

final class DatabaseException extends HttpException
{
    /**
     * Create database exception.
     *
     * @param array<string,mixed> $data
     */
    public function __construct(
        string $message = 'A database error occurred.',
        array $data = [],
        ?Throwable $previous = null
    )
    {
        parent::__construct(

            500,

            $message,

            $data,

            $previous

        );
    }
}
