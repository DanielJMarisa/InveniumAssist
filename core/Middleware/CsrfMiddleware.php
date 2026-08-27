<?php

declare(strict_types=1);

namespace Core\Middleware;

use Core\Http\Request;
use Core\Security\Csrf;

final class CsrfMiddleware extends Middleware
{
    /**
     * HTTP methods requiring CSRF protection.
     *
     * @var array<int,string>
     */
    private array $protectedMethods = [

        'POST',

        'PUT',

        'PATCH',

        'DELETE'

    ];


    /**
     * Handle CSRF validation.
     */
    public function handle(
        Request $request,
        callable $next
    ): mixed
    {
        /*
        |--------------------------------------------------------------------------
        | Determine Request Method
        |--------------------------------------------------------------------------
        */

        $method = strtoupper(

            $request->method()

        );


        /*
        |--------------------------------------------------------------------------
        | Skip Safe Requests
        |--------------------------------------------------------------------------
        */

        if (!in_array(

            $method,

            $this->protectedMethods,

            true

        )) {

            return $this->next(

                $request,

                $next

            );

        }


        /*
        |--------------------------------------------------------------------------
        | Validate Token
        |--------------------------------------------------------------------------
        */

        if (!Csrf::verify(

            $request->input('_token')

        )) {

            $this->abort(

                419,

                'Page expired. Please refresh and try again.'

            );

        }


        /*
        |--------------------------------------------------------------------------
        | Continue Pipeline
        |--------------------------------------------------------------------------
        */

        return $this->next(

            $request,

            $next

        );
    }
}