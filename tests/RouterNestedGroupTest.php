<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Container\Container;
use Core\Routing\Router;

$container = new Container();

$router = new Router($container);


/*
|--------------------------------------------------------------------------
| Nested Groups
|--------------------------------------------------------------------------
*/

$router->group(

    '/admin',

    [
        'ParentAuth'
    ],

    function (Router $router): void {

        $router->group(

            '/users',

            [
                'RoleAdmin'
            ],

            function (Router $router): void {

                $router->get(

                    '/list',

                    function (): string {
                        return 'users';
                    }

                );

            }

        );

    }

);


/*
|--------------------------------------------------------------------------
| Verify Route Exists
|--------------------------------------------------------------------------
*/

$routes = $router->routes();


if (!isset($routes['GET'])) {

    throw new RuntimeException(

        'GET route collection missing.'

    );

}


if (!isset($routes['GET']['/admin/users/list'])) {

    throw new RuntimeException(

        'Nested route was not registered correctly.'

    );

}


$route = $routes['GET']['/admin/users/list'];


/*
|--------------------------------------------------------------------------
| Verify Middleware Inheritance
|--------------------------------------------------------------------------
*/

$middleware = $route->getMiddleware();


if ($middleware !== [

    'ParentAuth',

    'RoleAdmin'

]) {

    throw new RuntimeException(

        'Nested group middleware inheritance failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Verify Nested Group Isolation
|--------------------------------------------------------------------------
*/

$router->get(

    '/outside',

    function (): string {
        return 'outside';
    }

);


$routes = $router->routes();


if (!isset($routes['GET']['/outside'])) {

    throw new RuntimeException(

        'Route outside nested group was not registered.'

    );

}


if (
    $routes['GET']['/outside']->getMiddleware()
    !== []
) {

    throw new RuntimeException(

        'Nested group middleware leaked into subsequent routes.'

    );

}


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

echo 'Nested router group tests passed successfully.'
    . PHP_EOL;