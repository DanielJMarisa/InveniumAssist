<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Container\Container;
use Core\Exceptions\MethodNotAllowedException;
use Core\Exceptions\NotFoundException;
use Core\Routing\Router;


/*
|--------------------------------------------------------------------------
| Test Controller
|--------------------------------------------------------------------------
*/

final class RouterNotFoundMethodTestController
{
    public function show(
        string $id
    ): string {

        return 'user:' . $id;
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
| Register GET Route
|--------------------------------------------------------------------------
*/

$router->get(

    '/users/{id}',

    [
        RouterNotFoundMethodTestController::class,
        'show'
    ]

);


/*
|--------------------------------------------------------------------------
| Valid Route
|--------------------------------------------------------------------------
*/

$result = $router->dispatch(

    'GET',

    '/users/123'

);


if ($result !== 'user:123') {

    throw new RuntimeException(

        'Valid GET route did not execute correctly.'

    );
}


/*
|--------------------------------------------------------------------------
| Method Not Allowed
|--------------------------------------------------------------------------
*/

$methodRejected = false;

try {

    $router->dispatch(

        'POST',

        '/users/123'

    );

} catch (MethodNotAllowedException $exception) {

    $methodRejected = true;

    if ($exception->status() !== 405) {

        throw new RuntimeException(

            'MethodNotAllowedException returned incorrect status.'

        );
    }
}


if (!$methodRejected) {

    throw new RuntimeException(

        'Unsupported HTTP method was not rejected with 405.'

    );
}


/*
|--------------------------------------------------------------------------
| Not Found
|--------------------------------------------------------------------------
*/

$notFound = false;

try {

    $router->dispatch(

        'GET',

        '/does-not-exist'

    );

} catch (NotFoundException $exception) {

    $notFound = true;

    if ($exception->status() !== 404) {

        throw new RuntimeException(

            'NotFoundException returned incorrect status.'

        );
    }
}


if (!$notFound) {

    throw new RuntimeException(

        'Unknown route was not rejected with 404.'

    );
}


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

echo 'Router 404/405 tests passed successfully.'
    . PHP_EOL;