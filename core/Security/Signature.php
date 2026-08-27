<?php

declare(strict_types=1);

namespace Core\Security;

use Core\Config\Config;
use RuntimeException;

final class Signature
{
    /**
     * Prevent instantiation.
     */
    private function __construct()
    {
    }


    /**
     * Create signature.
     */
    public static function make(
        string $value
    ): string
    {
        return hash_hmac(

            'sha256',

            $value,

            self::key()

        );
    }


    /**
     * Verify signature.
     */
    public static function verify(
        string $value,
        string $signature
    ): bool
    {
        return hash_equals(

            self::make($value),

            $signature

        );
    }


    /**
     * Generate signed payload.
     *
     * @param array<string,mixed> $data
     */
    public static function signPayload(
        array $data
    ): array
    {
        $payload = json_encode(

            $data,

            JSON_UNESCAPED_UNICODE
            |
            JSON_UNESCAPED_SLASHES

        );


        if ($payload === false) {

            throw new RuntimeException(

                'Unable to encode payload.'

            );

        }


        return [

            'payload' => base64_encode($payload),

            'signature' => self::make($payload)

        ];
    }


    /**
     * Validate signed payload.
     *
     * @return array<string,mixed>|null
     */
    public static function verifyPayload(
        string $payload,
        string $signature
    ): ?array
    {
        $decoded = base64_decode(

            $payload,

            true

        );


        if ($decoded === false) {

            return null;

        }


        if (!self::verify(

            $decoded,

            $signature

        )) {

            return null;

        }


        $data = json_decode(

            $decoded,

            true

        );


        return is_array($data)

            ? $data

            : null;
    }


    /**
     * Retrieve signing key.
     */
    private static function key(): string
    {
        $key = Config::get(

            'app.key'

        );


        if (

            !is_string($key)

            ||

            $key === ''

        ) {

            throw new RuntimeException(

                'Signing key not configured.'

            );

        }


        return $key;
    }
}