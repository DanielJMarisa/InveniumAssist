<?php

declare(strict_types=1);

use Core\Kernel\Application;
use Modules\Auth\AuthController;

return function (Application $app): void {

    $router = $app->router();

    /*
    |--------------------------------------------------------------------------
    | Authentication Routes
    |--------------------------------------------------------------------------
    */

    $router->get(

        '/login',

        [AuthController::class, 'index']

    );

    $router->post(

        '/login',

        [AuthController::class, 'login']

    );

    $router->post(

        '/logout',

        [AuthController::class, 'logout']

    );
};