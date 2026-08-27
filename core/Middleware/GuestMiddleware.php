<?php

declare(strict_types=1);

namespace Core\Middleware;

use Core\Auth\Guard;
use Core\Http\Request;

final class GuestMiddleware extends Middleware
{
    /**
     * Handle guest-only routes.
     */
    public function handle(
        Request $request,
        callable $next
    ): mixed
    {
        /*
        |--------------------------------------------------------------------------
        | Already Authenticated
        |--------------------------------------------------------------------------
        */

        if (Guard::authenticated()) {

            $this->redirect(
                'dashboard'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Continue Request Pipeline
        |--------------------------------------------------------------------------
        */

        return $this->next(
            $request,
            $next
        );
    }
}