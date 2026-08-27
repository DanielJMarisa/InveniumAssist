<?php

declare(strict_types=1);

namespace Core\Auth;

use Core\Session\Session;

final class Auth
{
    /**
     * Prevent instantiation.
     */
    private function __construct()
    {
    }


    /**
     * Determine if a user is authenticated.
     */
    public static function check(): bool
    {
        return Session::has(

            'auth.user_id'

        );
    }


    /**
     * Determine if user is guest.
     */
    public static function guest(): bool
    {
        return !self::check();
    }


    /**
     * Return authenticated user id.
     */
    public static function id(): ?int
    {
        $id = Session::get(

            'auth.user_id'

        );


        return $id !== null

            ? (int) $id

            : null;
    }


    /**
     * Return authenticated username.
     */
    public static function username(): ?string
    {
        $username = Session::get(

            'auth.username'

        );


        return is_string($username)

            ? $username

            : null;
    }


    /**
     * Return authenticated role.
     */
    public static function role(): ?string
    {
        $role = Session::get(

            'auth.role'

        );


        return is_string($role)

            ? $role

            : null;
    }


    /**
     * Determine whether user has role.
     */
    public static function hasRole(
        string $role
    ): bool
    {
        return self::role() === $role;
    }


    /**
     * Return authenticated user data.
     *
     * @return array<string,mixed>|null
     */
    public static function user(): ?array
    {
        if (!self::check()) {

            return null;

        }


        return [

            'id' => self::id(),

            'username' => self::username(),

            'role' => self::role()

        ];
    }


    /**
     * Clear authentication session.
     */
    public static function logout(): void
    {
        Session::forget(

            'auth.user_id'

        );

        Session::forget(

            'auth.username'

        );

        Session::forget(

            'auth.role'

        );
    }
}