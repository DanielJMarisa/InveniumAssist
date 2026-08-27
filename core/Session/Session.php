<?php

declare(strict_types=1);

namespace Core\Session;

final class Session
{
    /**
     * Prevent instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Start the session if it is not already active.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {

            session_start();

        }
    }

    /**
     * Store a session value.
     */
    public static function put(
        string $key,
        mixed $value
    ): void
    {
        self::start();

        $_SESSION[$key] = $value;
    }

    /**
     * Retrieve a session value.
     */
    public static function get(
        string $key,
        mixed $default = null
    ): mixed
    {
        self::start();

        return $_SESSION[$key] ?? $default;
    }

    /**
     * Determine whether a session key exists.
     */
    public static function has(
        string $key
    ): bool
    {
        self::start();

        return array_key_exists(

            $key,

            $_SESSION

        );
    }

    /**
     * Remove a session value.
     */
    public static function forget(
        string $key
    ): void
    {
        self::start();

        unset($_SESSION[$key]);
    }

    /**
     * Remove all session values.
     */
    public static function flush(): void
    {
        self::start();

        $_SESSION = [];
    }

    /**
     * Regenerate the session ID.
     */
    public static function regenerate(
        bool $deleteOldSession = true
    ): bool
    {
        self::start();

        return session_regenerate_id(

            $deleteOldSession

        );
    }

    /**
     * Destroy the session.
     */
    public static function destroy(): void
    {
        self::start();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {

            $params = session_get_cookie_params();

            setcookie(

                session_name(),

                '',

                time() - 42000,

                $params['path'],

                $params['domain'],

                $params['secure'],

                $params['httponly']

            );

        }

        session_destroy();
    }

    /**
     * Store a flash message.
     */
    public static function flash(
        string $key,
        mixed $value
    ): void
    {
        self::put(

            '_flash_' . $key,

            $value

        );
    }

    /**
     * Retrieve and remove a flash message.
     */
    public static function pull(
        string $key,
        mixed $default = null
    ): mixed
    {
        $value = self::get(

            '_flash_' . $key,

            $default

        );

        self::forget(

            '_flash_' . $key

        );

        return $value;
    }
}