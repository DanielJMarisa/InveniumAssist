<?php

declare(strict_types=1);

use Core\Http\Response;
use Core\Kernel\Application;
use Core\Middleware\AuthMiddleware;
use Core\Middleware\AdminMiddleware;
use Core\Middleware\OperationsMiddleware;
use Modules\Dashboard\DashboardController;
use Modules\Users\UserController;
use Modules\Customers\CustomerController;
use Modules\Devices\DeviceController;
use Modules\Monitoring\MonitoringController;
use Modules\Incidents\IncidentController;

return function (Application $app): void {

    $router = $app->router();

    /*
    |--------------------------------------------------------------------------
    | Public
    |--------------------------------------------------------------------------
    */

    $router->get(
        '/',
        function (): Response {
            return Response::make(
                'Invenium Assist'
            );
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Authenticated Application
    |--------------------------------------------------------------------------
    */

    $router->group(
        '',
        [
            AuthMiddleware::class
        ],
        function ($router): void {

            $router->get(
                '/dashboard',
                [DashboardController::class, 'index']
            );
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Administrative Routes
    |--------------------------------------------------------------------------
    */

    $router->group(
        '/admin',
        [
            AuthMiddleware::class,
            AdminMiddleware::class
        ],
        function ($router): void {

            /*
             * Users
             */

            $router->get(
                '/users',
                [UserController::class, 'index']
            );

            $router->get(
                '/users/create',
                [UserController::class, 'create']
            );

            $router->post(
                '/users',
                [UserController::class, 'store']
            );

            $router->get(
                '/users/{id}',
                [UserController::class, 'show']
            );

            $router->get(
                '/users/{id}/edit',
                [UserController::class, 'edit']
            );

            $router->post(
                '/users/{id}',
                [UserController::class, 'update']
            );

            $router->post(
                '/users/{id}/delete',
                [UserController::class, 'delete']
            );
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Operations Routes
    |--------------------------------------------------------------------------
    */

    $router->group(
        '',
        [
            AuthMiddleware::class,
            OperationsMiddleware::class
        ],
        function ($router): void {
            /*
             * Customers
             */
            $router->get(
                '/customers',
                [CustomerController::class, 'index']
            );

            $router->get(
                '/customers/create',
                [CustomerController::class, 'create']
            );

            $router->post(
                '/customers',
                [CustomerController::class, 'store']
            );

            $router->get(
                '/customers/{id}',
                [CustomerController::class, 'show']
            );

            /*
             * Devices
             */

            $router->get(
                '/devices',
                [DeviceController::class, 'index']
            );

            $router->get(
                '/devices/create',
                [DeviceController::class, 'create']
            );

            $router->post(
                '/devices',
                [DeviceController::class, 'store']
            );

            $router->get(
                '/devices/{id}',
                [DeviceController::class, 'show']
            );

            $router->get(
                '/devices/{id}/edit',
                [DeviceController::class, 'edit']
            );

            $router->post(
                '/devices/{id}',
                [DeviceController::class, 'update']
            );

            /*
             * Monitor
             */

            $router->get(
                '/monitor',
                [MonitoringController::class, 'index']
            );



            $router->get(
                '/monitor/status',
                [MonitoringController::class, 'status']
            );

            $router->get(
                '/monitor/{id}',
                [MonitoringController::class, 'show']
            );

            $router->post(
                '/monitor/{id}/enable',
                [MonitoringController::class, 'enable']
            );

            $router->post(
                '/monitor/{id}/disable',
                [MonitoringController::class, 'disable']
            );

            /*
             * Incidents
             */

            $router->get(
                '/incidents',
                [IncidentController::class, 'index']
            );

            $router->get(
                '/incidents/{id}',
                [IncidentController::class, 'show']
            );

            $router->post(
                '/incidents/{id}/notes',
                [IncidentController::class, 'updateNotes']
            );
        }
    );


};