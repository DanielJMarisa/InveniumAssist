<?php

declare(strict_types=1);

namespace Core\Http;

final class Request
{
    /**
     * Prevent instantiation.
     */
    public function __construct()
    {
    }

    /**
     * Return the request method.
     */
    public static function method(): string
    {
        return strtoupper(

            $_SERVER['REQUEST_METHOD'] ?? 'GET'

        );
    }

    /**
     * Determine if request is GET.
     */
    public static function isGet(): bool
    {
        return self::method() === 'GET';
    }

    /**
     * Determine if request is POST.
     */
    public static function isPost(): bool
    {
        return self::method() === 'POST';
    }

    /**
     * Return all POST data.
     *
     * @return array<string,mixed>
     */
    public static function post(): array
    {
        return $_POST;
    }

    /**
     * Return all GET data.
     *
     * @return array<string,mixed>
     */
    public static function get(): array
    {
        return $_GET;
    }

    /**
     * Return request input.
     */
    public static function input(
        string $key,
        mixed $default = null
    ): mixed
    {
        if (array_key_exists($key, $_POST)) {

            return $_POST[$key];

        }

        if (array_key_exists($key, $_GET)) {

            return $_GET[$key];

        }

        return $default;
    }

    /**
     * Return uploaded files.
     *
     * @return array<string,mixed>
     */
    public static function files(): array
    {
        return $_FILES;
    }

    /**
     * Current request path.
     */
    public static function path(): string
    {
        return URL::path();
    }

    /**
     * Determine if request is AJAX.
     */
    public static function isAjax(): bool
    {
        return (

            $_SERVER['HTTP_X_REQUESTED_WITH'] ?? ''

        ) === 'XMLHttpRequest';
    }

    /**
     * Determine if request is HTTPS.
     */
    public static function secure(): bool
    {
        return (
            !empty($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== 'off'
        )
        || (
            ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'
        );
    }

    /**
     * Client IP.
     */
    public static function ip(): string
    {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {

            return $_SERVER['HTTP_CF_CONNECTING_IP'];

        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

            return trim(

                explode(

                    ',',

                    $_SERVER['HTTP_X_FORWARDED_FOR']

                )[0]

            );

        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Browser User-Agent.
     */
    public static function userAgent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

    /**
     * HTTP Referer.
     */
    public static function referer(): ?string
    {
        return $_SERVER['HTTP_REFERER'] ?? null;
    }

    /**
     * Decode JSON payload.
     *
     * @return array<string,mixed>
     */
    public static function json(): array
    {
        $input = file_get_contents(

            'php://input'

        );

        $decoded = json_decode(

            $input,

            true

        );

        return is_array($decoded)

            ? $decoded

            : [];
    }
}