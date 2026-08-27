<?php

declare(strict_types=1);

namespace Core\Http;

final class URL
{
    public static function base(): string
    {
        return self::build();
    }

    public static function current(): string
    {
        return self::build(self::path());
    }

    public static function path(): string
    {
        $uri = parse_url(
            $_SERVER['REQUEST_URI'] ?? '/',
            PHP_URL_PATH
        );

        if (!is_string($uri)) {
            return '/';
        }

        $base = str_replace(
            ' ',
            '%20',
            BASE_URI
        );

        if (
            $base !== '/'
            && str_starts_with($uri, $base)
        ) {
            $uri = substr(
                $uri,
                strlen($base)
            );
        }

        return '/' . ltrim(
            rawurldecode($uri ?: ''),
            '/'
        );
    }

    public static function to(
        string $path
    ): string
    {
        return self::build($path);
    }

    public static function asset(
        string $path
    ): string
    {
        return self::build($path);
    }

    public static function api(
        string $path
    ): string
    {
        return self::build(

            'api/' . ltrim($path, '/')

        );
    }

    /**
     * Build a full application URL.
     */
    private static function build(
        string $path = ''
    ): string
    {
        $url =

            self::scheme()

            . '://'

            . self::host()

            . BASE_URI;

        if ($path !== '') {

            $url .= '/'

                . ltrim($path, '/');

        }

        return rtrim($url, '/');
    }

    /**
     * Detect the current request scheme.
     */
    private static function scheme(): string
    {
        if (

            !empty($_SERVER['HTTP_X_FORWARDED_PROTO'])

            &&

            $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'

        ) {

            return 'https';

        }

        return (

            !empty($_SERVER['HTTPS'])

            &&

            $_SERVER['HTTPS'] !== 'off'

        )

            ? 'https'

            : 'http';
    }

    /**
     * Return current host.
     */
    private static function host(): string
    {
        return $_SERVER['HTTP_HOST']

            ?? 'localhost';
    }
}