<?php

declare(strict_types=1);

namespace Core\Exceptions;

final class AuthenticationException extends HttpException
{
    /**
     * Create authentication exception.
     */
    public function __construct(
        string $message = 'Authentication required.',
        array $data = []
    )
    {
        parent::__construct(

            401,

            $message,

            $data

        );
    }
}