<?php

declare(strict_types=1);

namespace Core\Security;

use Core\Config\Config;
use RuntimeException;

final class Encryption
{
    /**
     * Cipher algorithm.
     */
    private const CIPHER = 'aes-256-gcm';


    /**
     * Prevent instantiation.
     */
    private function __construct()
    {
    }


    /**
     * Encrypt a value.
     */
    public static function encrypt(
        string $value
    ): string
    {
        $key = self::key();


        $iv = random_bytes(

            openssl_cipher_iv_length(

                self::CIPHER

            )

        );


        $tag = '';


        $encrypted = openssl_encrypt(

            $value,

            self::CIPHER,

            $key,

            OPENSSL_RAW_DATA,

            $iv,

            $tag

        );


        if ($encrypted === false) {

            throw new RuntimeException(

                'Encryption failed.'

            );

        }


        return base64_encode(

            $iv

            .

            $tag

            .

            $encrypted

        );
    }


    /**
     * Decrypt a value.
     */
    public static function decrypt(
        string $payload
    ): string
    {
        $key = self::key();


        $decoded = base64_decode(

            $payload,

            true

        );


        if ($decoded === false) {

            throw new RuntimeException(

                'Invalid encrypted payload.'

            );

        }


        $ivLength = openssl_cipher_iv_length(

            self::CIPHER

        );


        $tagLength = 16;


        $iv = substr(

            $decoded,

            0,

            $ivLength

        );


        $tag = substr(

            $decoded,

            $ivLength,

            $tagLength

        );


        $cipherText = substr(

            $decoded,

            $ivLength + $tagLength

        );


        $decrypted = openssl_decrypt(

            $cipherText,

            self::CIPHER,

            $key,

            OPENSSL_RAW_DATA,

            $iv,

            $tag

        );


        if ($decrypted === false) {

            throw new RuntimeException(

                'Decryption failed.'

            );

        }


        return $decrypted;
    }


    /**
     * Retrieve encryption key.
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

                'Encryption key not configured.'

            );

        }


        return hash(

            'sha256',

            $key,

            true

        );
    }
}