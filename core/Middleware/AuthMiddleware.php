<?php

declare(strict_types=1);

namespace Core\Middleware;

use Core\Auth\Guard;
use Core\Exceptions\AuthenticationException;
use Core\Http\Request;

final class AuthMiddleware extends Middleware
{
    /**
     * Handle authenticated routes.
     *
     * @throws AuthenticationException
     */
    public function handle(
        Request $request,
        callable $next
    ): mixed
    {
        /*
        |--------------------------------------------------------------------------
        | Require Authentication
        |--------------------------------------------------------------------------
        */

        Guard::requireAuthentication();

        /*
        |--------------------------------------------------------------------------
        | Continue Request Pipeline
        |--------------------------------------------------------------------------
        */

        return $this->next(
            $request,
            $next
        );
    }
}