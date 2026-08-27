<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Container\Container;
use Core\Exceptions\MethodNotAllowedException;
use Core\Exceptions\NotFoundException;
use Core\Routing\Router;


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

$router = new Router(

    $container

);


/*
|--------------------------------------------------------------------------
| Register Routes
|--------------------------------------------------------------------------
*/

$router->get(

    '/dashboard',

    function (): string {

        return 'dashboard';

    }

);


$router->post(

    '/users',

    function (): string {

        return 'created';

    }

);


/*
|--------------------------------------------------------------------------
| 404 — Route Does Not Exist
|--------------------------------------------------------------------------
*/

try {

    $router->dispatch(

        'GET',

        '/does-not-exist'

    );

    throw new RuntimeException(

        'Router unexpectedly allowed a missing route.'

    );

} catch (NotFoundException $exception) {

    if ($exception->status() !== 404) {

        throw new RuntimeException(

            'NotFoundException returned incorrect status.'

        );

    }

}


/*
|--------------------------------------------------------------------------
| 405 — URI Exists But Method Does Not
|--------------------------------------------------------------------------
*/

try {

    $router->dispatch(

        'GET',

        '/users'

    );

    throw new RuntimeException(

        'Router unexpectedly allowed an unsupported method.'

    );

} catch (MethodNotAllowedException $exception) {

    if ($exception->status() !== 405) {

        throw new RuntimeException(

            'MethodNotAllowedException returned incorrect status.'

        );

    }

}


/*
|--------------------------------------------------------------------------
| Valid Route Still Works
|--------------------------------------------------------------------------
*/

$response = $router->dispatch(

    'GET',

    '/dashboard'

);


if ($response !== 'dashboard') {

    throw new RuntimeException(

        'Valid route dispatch failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

echo 'Router HTTP exception tests passed successfully.'

    . PHP_EOL;