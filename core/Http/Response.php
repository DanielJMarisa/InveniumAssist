<?php

declare(strict_types=1);

namespace Core\Http;

final class Response
{
    /**
     * Response content.
     */
    private string $content;

    /**
     * HTTP status code.
     */
    private int $status;

    /**
     * Response headers.
     *
     * @var array<string,string>
     */
    private array $headers = [];

    /**
     * Create response.
     *
     * @param array<string,string> $headers
     */
    public function __construct(
        string $content = '',
        int $status = 200,
        array $headers = []
    ) {
        $this->content = $content;
        $this->status = $status;
        $this->headers = $headers;
    }

    /**
     * Create a plain response.
     */
    public static function make(
        string $content = '',
        int $status = 200
    ): self {
        return new self(
            $content,
            $status,
            [
                'Content-Type' => 'text/html; charset=UTF-8'
            ]
        );
    }

    /**
     * Create a JSON response.
     *
     * @param array<string,mixed> $data
     */
    public static function json(
        array $data,
        int $status = 200
    ): self {
        return new self(
            json_encode(
                $data,
                JSON_THROW_ON_ERROR
                | JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
            ),
            $status,
            [
                'Content-Type' => 'application/json; charset=UTF-8'
            ]
        );
    }

    /**
     * Create a redirect response.
     */
    public static function redirect(
        string $path,
        int $status = 302
    ): self {
        return new self(
            '',
            $status,
            [
                'Location' => URL::to($path)
            ]
        );
    }

    /**
     * Create an error response.
     */
    public static function error(
        int $status,
        string $message
    ): self {
        return new self(
            $message,
            $status,
            [
                'Content-Type' => 'text/html; charset=UTF-8'
            ]
        );
    }

    /**
     * Return response content.
     */
    public function content(): string
    {
        return $this->content;
    }

    /**
     * Return HTTP status code.
     */
    public function status(): int
    {
        return $this->status;
    }

    /**
     * Return response headers.
     *
     * @return array<string,string>
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Set a response header.
     */
    public function header(
        string $name,
        string $value
    ): self {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * Set HTTP status.
     */
    public function withStatus(
        int $status
    ): self {
        $this->status = $status;

        return $this;
    }

    /**
     * Send response to client.
     */
    public function send(): never
    {
        /*
        |--------------------------------------------------------------------------
        | Set HTTP Status
        |--------------------------------------------------------------------------
        |
        | PHP/Apache can normalize non-standard HTTP status codes such as
        | 419 to 500 when only http_response_code() is used.
        |
        | Explicitly provide the status line so the application controls
        | the actual HTTP response code.
        |
        */

        $statusText = self::statusText(
            $this->status
        );

        header(
            sprintf(
                'HTTP/1.1 %d %s',
                $this->status,
                $statusText
            ),
            true,
            $this->status
        );

        /*
        |--------------------------------------------------------------------------
        | Response Headers
        |--------------------------------------------------------------------------
        */

        foreach ($this->headers as $name => $value) {

            header(
                $name . ': ' . $value
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Response Body
        |--------------------------------------------------------------------------
        */

        echo $this->content;

        exit;
    }

    /**
     * Abort request immediately.
     */
    public static function abort(
        int $status = 404,
        string $message = ''
    ): never {
        self::error(
            $status,
            $message
        )->send();
    }

    /**
     * Return HTTP status text.
     */
    private static function statusText(
        int $status
    ): string {
        return match ($status) {

            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            204 => 'No Content',

            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',

            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Content Too Large',
            414 => 'URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => "I'm a teapot",
            419 => 'Page Expired',
            422 => 'Unprocessable Content',
            423 => 'Locked',
            425 => 'Too Early',
            429 => 'Too Many Requests',

            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',

            default => 'HTTP Response'
        };
    }
}