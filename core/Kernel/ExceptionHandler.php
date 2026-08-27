<?php

declare(strict_types=1);

namespace Core\Kernel;

use Throwable;
use Core\Http\Response;
use Core\Exceptions\HttpException;
use Core\Exceptions\ValidationException;

final class ExceptionHandler
{
    /**
     * Handle application exception.
     */
    public static function handle(
        Throwable $exception
    ): never
    {
        /*
        |--------------------------------------------------------------------------
        | HTTP Exceptions
        |--------------------------------------------------------------------------
        */

        if ($exception instanceof HttpException) {

            return self::handleHttpException(

                $exception

            );

        }

         /*
        |--------------------------------------------------------------------------
        | Unknown Exceptions
        |--------------------------------------------------------------------------
        */

        return self::handleUnexpectedException(

            $exception

        );
    }


    /**
     * Handle known HTTP exceptions.
     */
    private static function handleHttpException(
        HttpException $exception
    ): never
    {
        $status = $exception->status();


        $data = [

            'message' => $exception->getMessage()

        ];


        /*
        |--------------------------------------------------------------------------
        | Validation Errors
        |--------------------------------------------------------------------------
        */

        if ($exception instanceof ValidationException) {

            $data['errors'] = $exception->errors();

        }


        Response::json(

            $data,

            $status

        );
    }


    /**
     * Handle unexpected exceptions.
     */
    private static function handleUnexpectedException(
        Throwable $exception
    ): never
    {
        /*
        |--------------------------------------------------------------------------
        | Development Environment
        |--------------------------------------------------------------------------
        */

        if (self::isDevelopment()) {

            Response::json(

                [

                    'message' => $exception->getMessage(),

                    'file' => $exception->getFile(),

                    'line' => $exception->getLine()

                ],

                500

            );

        }


        /*
        |--------------------------------------------------------------------------
        | Production Environment
        |--------------------------------------------------------------------------
        */

        Response::json(

            [

                'message' => 'Internal server error.'

            ],

            500

        );
    }


    /**
     * Determine application environment.
     */
    private static function isDevelopment(): bool
    {
        return defined('APP_ENV')
            &&
            APP_ENV === 'development';
    }
}