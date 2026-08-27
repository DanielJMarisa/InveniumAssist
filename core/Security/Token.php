<?php

declare(strict_types=1);

namespace Core\Security;

final class Token
{
    /**
     * Prevent instantiation.
     */
    private function __construct()
    {
    }


    /**
     * Generate a secure random token.
     */
    public static function generate(
        int $length = 64
    ): string
    {
        return bin2hex(

            random_bytes(

                (int) ($length / 2)

            )

        );
    }


    /**
     * Generate UUID v4 token.
     */
    public static function uuid(): string
    {
        $data = random_bytes(16);


        /*
        |----------------------------------------------------------------------
        | Set UUID version 4
        |----------------------------------------------------------------------
        */

        $data[6] = chr(

            ord($data[6]) & 0x0f

            |

            0x40

        );


        /*
        |----------------------------------------------------------------------
        | Set UUID variant
        |----------------------------------------------------------------------
        */

        $data[8] = chr(

            ord($data[8]) & 0x3f

            |

            0x80

        );


        return vsprintf(

            '%s%s-%s-%s-%s-%s%s%s',

            str_split(

                bin2hex($data),

                4

            )

        );
    }


    /**
     * Generate short token.
     */
    public static function short(
        int $length = 16
    ): string
    {
        return substr(

            self::generate($length),

            0,

            $length

        );
    }


    /**
     * Hash token for database storage.
     */
    public static function hash(
        string $token
    ): string
    {
        return hash(

            'sha256',

            $token

        );
    }


    /**
     * Verify token against stored hash.
     */
    public static function verify(
        string $token,
        string $hash
    ): bool
    {
        return hash_equals(

            self::hash($token),

            $hash

        );
    }
}