<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Container\Container;
use Core\Routing\Router;


/*
|--------------------------------------------------------------------------
| Test Controller
|--------------------------------------------------------------------------
*/

final class RouterRoutePrecedenceTestController
{
    public function dynamic(
        string $id
    ): string {

        return 'dynamic:' . $id;
    }


    public function create(): string
    {
        return 'static:create';
    }
}


/*
|--------------------------------------------------------------------------
| Container
|--------------------------------------------------------------------------
*/

$container = new Container();


/*
|--------------------------------------------------------------------------
| Router
|--------------------------------------------------------------------------
*/

$router = new Router($container);


/*
|--------------------------------------------------------------------------
| Dynamic Route
|--------------------------------------------------------------------------
*/

$router->get(

    '/customers/{id}',

    [
        RouterRoutePrecedenceTestController::class,
        'dynamic'
    ]

);


/*
|--------------------------------------------------------------------------
| Static Route
|--------------------------------------------------------------------------
*/

$router->get(

    '/customers/create',

    [
        RouterRoutePrecedenceTestController::class,
        'create'
    ]

);


/*
|--------------------------------------------------------------------------
| Static Route Must Win
|--------------------------------------------------------------------------
*/

$result = $router->dispatch(

    'GET',

    '/customers/create'

);


if ($result !== 'static:create') {

    throw new RuntimeException(

        'Static route did not take precedence over dynamic route.'

    );
}


/*
|--------------------------------------------------------------------------
| Dynamic Route Still Works
|--------------------------------------------------------------------------
*/

$result = $router->dispatch(

    'GET',

    '/customers/42'

);


if ($result !== 'dynamic:42') {

    throw new RuntimeException(

        'Dynamic route matching failed after static precedence.'

    );
}


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

echo 'Router route precedence tests passed successfully.'
    . PHP_EOL;