<?php

declare(strict_types=1);

namespace Core\Security;

final class Hash
{
    /**
     * Prevent instantiation.
     */
    private function __construct()
    {
    }


    /**
     * Hash a password.
     */
    public static function make(
        string $value
    ): string
    {
        return password_hash(

            $value,

            PASSWORD_DEFAULT

        );
    }


    /**
     * Verify a hashed value.
     */
    public static function check(
        string $value,
        string $hashedValue
    ): bool
    {
        return password_verify(

            $value,

            $hashedValue

        );
    }


    /**
     * Determine if password requires rehashing.
     */
    public static function needsRehash(
        string $hashedValue
    ): bool
    {
        return password_needs_rehash(

            $hashedValue,

            PASSWORD_DEFAULT

        );
    }
}