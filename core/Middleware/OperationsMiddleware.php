<?php

declare(strict_types=1);

namespace Core\Middleware;

use Core\Auth\Guard;
use Core\Exceptions\AuthenticationException;
use Core\Exceptions\AuthorizationException;
use Core\Http\Request;

final class OperationsMiddleware extends Middleware
{
    /**
     * Handle operational authorization.
     *
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function handle(
        Request $request,
        callable $next
    ): mixed {
        Guard::requireAuthentication();

        if (
            Guard::role('Super Admin')
            || Guard::role('Administrator')
            || Guard::role('Technician')
        ) {
            return $this->next(
                $request,
                $next
            );
        }

        throw new AuthorizationException(
            'You do not have permission to access this resource.'
        );
    }
}