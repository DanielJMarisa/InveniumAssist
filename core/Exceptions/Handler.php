<?php

declare(strict_types=1);

namespace Core\Exceptions;

use Core\Http\Response;
use Throwable;

final class Handler
{
    /**
     * Prevent duplicate global handler registration.
     */
    private static bool $registered = false;


    /**
     * Register global exception and error handlers.
     */
    public static function register(): void
    {
        if (self::$registered) {

            return;

        }


        set_exception_handler(

            [
                self::class,
                'handle'
            ]

        );


        set_error_handler(

            [
                self::class,
                'error'
            ]

        );


        self::$registered = true;
    }


    /**
     * Handle uncaught exceptions.
     */
    public static function handle(
        Throwable $exception
    ): never
    {
        $status = self::status(

            $exception

        );


        /*
        |--------------------------------------------------------------------------
        | API Response
        |--------------------------------------------------------------------------
        */

        if (self::isApiRequest()) {

            if (self::isDevelopment()) {

                Response::json(

                    [
                        'success' => false,
                        'message' => $exception->getMessage(),
                        'status' => $status
                    ],

                    $status

                )->send();

            }


            Response::json(

                [
                    'success' => false,
                    'message' => self::productionMessage($status),
                    'status' => $status
                ],

                $status

            )->send();

        }


        /*
        |--------------------------------------------------------------------------
        | HTML Response
        |--------------------------------------------------------------------------
        */

        self::httpResponse(

            $exception,

            $status

        )->send();
    }


    /**
     * Create HTTP error response.
     */
    private static function httpResponse(
        Throwable $exception,
        int $status
    ): Response
    {
        $content = self::isDevelopment()

            ? self::debugPage($exception)

            : self::productionMessage($status);


        $response = Response::make(

            $content,

            $status

        );


        /*
        |--------------------------------------------------------------------------
        | Method Not Allowed
        |--------------------------------------------------------------------------
        */

        if ($exception instanceof MethodNotAllowedException) {

            $response->header(

                'Allow',

                implode(

                    ', ',

                    $exception->allowedMethods()

                )

            );

        }


        return $response;
    }


    /**
     * Convert PHP errors into exceptions.
     *
     * Returning true tells PHP that the error has been handled.
     */
    public static function error(
        int $level,
        string $message,
        string $file,
        int $line
    ): bool
    {
        /*
        |--------------------------------------------------------------------------
        | Respect Suppressed Errors
        |--------------------------------------------------------------------------
        */

        if (!(error_reporting() & $level)) {

            return true;

        }


        /*
        |--------------------------------------------------------------------------
        | Convert Error To Exception
        |--------------------------------------------------------------------------
        */

        throw new \ErrorException(

            $message,

            0,

            $level,

            $file,

            $line

        );
    }


    /**
     * Determine HTTP status from exception.
     */
    private static function status(
        Throwable $exception
    ): int
    {
        /*
        |--------------------------------------------------------------------------
        | HTTP Exceptions
        |--------------------------------------------------------------------------
        */

        if ($exception instanceof HttpException) {

            return $exception->status();

        }

        /*
        |--------------------------------------------------------------------------
        | Authentication
        |--------------------------------------------------------------------------
        */

        if ($exception instanceof AuthenticationException) {

            return 401;

        }

        /*
        |--------------------------------------------------------------------------
        | Authorization
        |--------------------------------------------------------------------------
        */

        if ($exception instanceof AuthorizationException) {

            return 403;

        }

        /*
        |--------------------------------------------------------------------------
        | Unexpected Exception
        |--------------------------------------------------------------------------
        */

        return 500;
    }


    /**
     * Determine whether request targets the API.
     */
    private static function isApiRequest(): bool
    {
        $path = parse_url(

            $_SERVER['REQUEST_URI'] ?? '/',

            PHP_URL_PATH

        );


        if (!is_string($path)) {

            return false;

        }


        return $path === '/api'

            || str_starts_with(

                $path,

                '/api/'

            );
    }


    /**
     * Determine application environment.
     */
    private static function isDevelopment(): bool
    {
        return defined('ENVIRONMENT')
            && ENVIRONMENT === 'development';
    }


    /**
     * Render development error page.
     */
    private static function debugPage(
        Throwable $exception
    ): string
    {
        return sprintf(

            '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Application Error</title>
            </head>
            <body>
                <h1>%s</h1>
                <p>%s</p>
                <pre>%s</pre>
            </body>
            </html>',

            htmlspecialchars(

                get_class($exception),

                ENT_QUOTES,

                'UTF-8'

            ),

            htmlspecialchars(

                $exception->getMessage(),

                ENT_QUOTES,

                'UTF-8'

            ),

            htmlspecialchars(

                $exception->getTraceAsString(),

                ENT_QUOTES,

                'UTF-8'

            )

        );
    }


    /**
     * Return safe production error message.
     */
    private static function productionMessage(
        int $status
    ): string
    {
        return match ($status) {

            400 => 'Bad request.',

            401 => 'Authentication required.',

            403 => 'Access denied.',

            404 => 'Page not found.',

            405 => 'Method not allowed.',

            419 => 'Page expired. Please refresh and try again.',

            422 => 'The submitted data is invalid.',

            429 => 'Too many requests. Please try again later.',

            default => 'An unexpected error occurred.'

        };
    }
}