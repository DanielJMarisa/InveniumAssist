<?php

declare(strict_types=1);

namespace Core\Middleware;

use Core\Http\Request;
use Core\Auth\Guard;
use Core\Exceptions\AuthenticationException;
use Core\Exceptions\AuthorizationException;

final class RoleMiddleware extends Middleware
{
    /**
     * Allowed roles.
     *
     * @var array<int,string>
     */
    private array $roles;


    /**
     * @param string|array<int,string> $roles
     */
    public function __construct(
        string|array $roles
    )
    {
        $this->roles = is_array($roles)

            ? $roles

            : [$roles];
    }


    /**
     * Handle role authorization.
     *
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function handle(
        Request $request,
        callable $next
    ): mixed
    {
        /*
        |--------------------------------------------------------------------------
        | Verify Authentication
        |--------------------------------------------------------------------------
        */

        Guard::requireAuthentication();


        /*
        |--------------------------------------------------------------------------
        | Verify Role
        |--------------------------------------------------------------------------
        */

        foreach ($this->roles as $role) {

            if (Guard::role($role)) {

                return $this->next(

                    $request,

                    $next

                );

            }

        }


        throw new AuthorizationException(

            'You do not have permission to access this resource.'

        );
    }
}