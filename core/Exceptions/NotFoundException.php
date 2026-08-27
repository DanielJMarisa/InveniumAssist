<?php

declare(strict_types=1);

namespace Core\Exceptions;

final class NotFoundException extends HttpException
{
    /**
     * Create 404 exception.
     */
    public function __construct(
        string $message = 'Resource not found.',
        array $data = []
    )
    {
        parent::__construct(

            404,

            $message,

            $data

        );
    }
}