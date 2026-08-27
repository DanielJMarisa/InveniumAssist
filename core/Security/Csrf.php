<?php

declare(strict_types=1);

namespace Core\Security;

use Core\Session\Session;

final class Csrf
{
    /**
     * Session key.
     */
    private const SESSION_KEY = '_csrf_token';


    /**
     * Prevent instantiation.
     */
    private function __construct()
    {
    }


    /**
     * Generate or retrieve CSRF token.
     */
    public static function token(): string
    {
        $token = Session::get(

            self::SESSION_KEY

        );


        if (

            !is_string($token)

            ||

            $token === ''

        ) {

            $token = self::generate();

            Session::put(

                self::SESSION_KEY,

                $token

            );

        }


        return $token;
    }


    /**
     * Verify submitted token.
     */
    public static function verify(
        mixed $token
    ): bool
    {
        if (

            !is_string($token)

            ||

            $token === ''

        ) {

            return false;

        }


        $stored = Session::get(

            self::SESSION_KEY

        );


        if (

            !is_string($stored)

            ||

            $stored === ''

        ) {

            return false;

        }


        return hash_equals(

            $stored,

            $token

        );
    }


    /**
     * Regenerate token.
     */
    public static function regenerate(): string
    {
        $token = self::generate();


        Session::put(

            self::SESSION_KEY,

            $token

        );


        return $token;
    }


    /**
     * Generate secure random token.
     */
    private static function generate(): string
    {
        return bin2hex(

            random_bytes(32)

        );
    }
}