<?php

declare(strict_types=1);

namespace Core\Exceptions;

final class AuthorizationException extends HttpException
{
    /**
     * Create authorization exception.
     */
    public function __construct(
        string $message = 'You are not authorised to perform this action.'
    )
    {
        parent::__construct(

            403,

            $message

        );
    }
}