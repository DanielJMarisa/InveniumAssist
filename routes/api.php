<?php

declare(strict_types=1);

use Core\Http\Response;
use Core\Kernel\Application;

return function (Application $app): void {

    $router = $app->router();

    /*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    */

    $router->group(
        '/api',
        [],
        function ($router): void {

            $router->get(
                '/health',
                function (): Response {

                    return Response::json([
                        'success' => true,
                        'service' => 'Invenium Assist',
                        'status' => 'ok'
                    ]);
                }
            );
        }
    );
};