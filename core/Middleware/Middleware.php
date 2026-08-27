<?php

declare(strict_types=1);

namespace Core\Middleware;

use Core\Exceptions\HttpException;
use Core\Http\Request;
use Core\Http\Response;

abstract class Middleware
{
    /**
     * Handle incoming request.
     */
    abstract public function handle(
        Request $request,
        callable $next
    ): mixed;


    /**
     * Continue request pipeline.
     */
    protected function next(
        Request $request,
        callable $next
    ): mixed
    {
        return $next($request);
    }


    /**
     * Redirect helper.
     */
    protected function redirect(
        string $path
    ): never
    {
        Response::redirect($path);

        /*
         * Response::redirect() normally returns a Response object.
         * Middleware redirects must terminate the current pipeline.
         */
        exit;
    }


    /**
     * Abort the current request.
     *
     * HTTP failures are represented as exceptions so that the
     * application's global exception handler can provide the
     * appropriate HTML or JSON response.
     *
     * @throws HttpException
     */
    protected function abort(
        int $status = 403,
        string $message = ''
    ): never
    {
        throw new HttpException(
            $status,
            $message
        );
    }
}