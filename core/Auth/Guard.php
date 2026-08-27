<?php

declare(strict_types=1);

namespace Core\Auth;

use Core\Exceptions\AuthenticationException;
use Core\Exceptions\AuthorizationException;

final class Guard
{
    /**
     * Prevent instantiation.
     */
    private function __construct()
    {
    }


    /**
     * Determine if current user has a role.
     */
    public static function role(
        string $role
    ): bool
    {
        return Auth::hasRole($role);
    }


    /**
     * Determine if current user has any role.
     *
     * @param array<int,string> $roles
     */
    public static function anyRole(
        array $roles
    ): bool
    {
        foreach ($roles as $role) {

            if (self::role($role)) {

                return true;

            }

        }

        return false;
    }


    /**
     * Determine if user authenticated.
     */
    public static function authenticated(): bool
    {
        return Auth::check();
    }


    /**
     * Require authentication.
     *
     * @throws AuthenticationException
     */
    public static function requireAuthentication(): void
    {
        if (!self::authenticated()) {

            throw new AuthenticationException(

                'Authentication required.'

            );

        }
    }


    /**
     * Require specific role.
     *
     * @throws AuthorizationException
     */
    public static function requireRole(
        string $role
    ): void
    {
        if (!self::role($role)) {

            throw new AuthorizationException(

                'You do not have permission to access this resource.'

            );

        }
    }


    /**
     * Require guest user.
     */
    public static function guest(): bool
    {
        return Auth::guest();
    }


    /**
     * Determine if user can perform action.
     *
     * Placeholder for future permission engine.
     */
    public static function allows(
        string $permission
    ): bool
    {
        return false;
    }


    /**
     * Determine if access should be denied.
     */
    public static function denies(
        string $permission
    ): bool
    {
        return !self::allows($permission);
    }
}