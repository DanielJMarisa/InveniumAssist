<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Container\Container;
use Core\Exceptions\MethodNotAllowedException;
use Core\Exceptions\NotFoundException;
use Core\Routing\Router;


$container = new Container();

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

$router->put(

    '/users',

    function (): string {

        return 'updated';

    }

);


/*
|--------------------------------------------------------------------------
| Valid Method
|--------------------------------------------------------------------------
*/

if (
    $router->dispatch(

        'GET',

        '/dashboard'

    ) !== 'dashboard'
) {

    throw new RuntimeException(

        'Valid HTTP method dispatch failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Method Not Allowed
|--------------------------------------------------------------------------
*/

try {

    $router->dispatch(

        'POST',

        '/dashboard'

    );

    throw new RuntimeException(

        'Expected MethodNotAllowedException was not thrown.'

    );

} catch (MethodNotAllowedException $exception) {

    if ($exception->status() !== 405) {

        throw new RuntimeException(

            'MethodNotAllowedException returned incorrect status.'

        );

    }


    if (
        $exception->allowedMethods()
        !==
        ['GET']
    ) {

        throw new RuntimeException(

            'Allowed methods were not detected correctly.'

        );

    }

}


/*
|--------------------------------------------------------------------------
| Multiple Allowed Methods
|--------------------------------------------------------------------------
*/

try {

    $router->dispatch(

        'DELETE',

        '/users'

    );

    throw new RuntimeException(

        'Expected MethodNotAllowedException was not thrown.'

    );

} catch (MethodNotAllowedException $exception) {

    if (
        $exception->allowedMethods()
        !==
        ['POST', 'PUT']
    ) {

        throw new RuntimeException(

            'Multiple allowed methods were not detected correctly.'

        );

    }

}


/*
|--------------------------------------------------------------------------
| Route Not Found
|--------------------------------------------------------------------------
*/

try {

    $router->dispatch(

        'GET',

        '/does-not-exist'

    );

    throw new RuntimeException(

        'Expected NotFoundException was not thrown.'

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
| Test Complete
|--------------------------------------------------------------------------
*/

echo 'Router HTTP method tests passed successfully.'
    . PHP_EOL;