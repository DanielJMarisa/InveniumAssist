<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Routing\Route;


/*
|--------------------------------------------------------------------------
| Single Parameter
|--------------------------------------------------------------------------
*/

$route = new Route(

    'GET',

    '/users/{id}',

    function (): string {

        return 'user';

    }

);


$parameters = $route->match(

    '/users/42'

);


if ($parameters !== ['id' => '42']) {

    throw new RuntimeException(

        'Single route parameter extraction failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Trailing Slash
|--------------------------------------------------------------------------
*/

$parameters = $route->match(

    '/users/42/'

);


if ($parameters !== ['id' => '42']) {

    throw new RuntimeException(

        'Trailing slash parameter matching failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Wrong URI
|--------------------------------------------------------------------------
*/

if (
    $route->match(
        '/users/42/orders'
    ) !== null
) {

    throw new RuntimeException(

        'Invalid URI unexpectedly matched.'

    );

}


/*
|--------------------------------------------------------------------------
| Multiple Parameters
|--------------------------------------------------------------------------
*/

$orderRoute = new Route(

    'GET',

    '/users/{userId}/orders/{orderId}',

    function (): string {

        return 'order';

    }

);


$parameters = $orderRoute->match(

    '/users/15/orders/928'

);


if (
    $parameters
    !==
    [
        'userId' => '15',
        'orderId' => '928'
    ]
) {

    throw new RuntimeException(

        'Multiple route parameter extraction failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Parameter Names
|--------------------------------------------------------------------------
*/

if (
    $orderRoute->parameters()
    !==
    [
        'userId',
        'orderId'
    ]
) {

    throw new RuntimeException(

        'Route parameter names were not registered correctly.'

    );

}


/*
|--------------------------------------------------------------------------
| Has Parameters
|--------------------------------------------------------------------------
*/

if (!$orderRoute->hasParameters()) {

    throw new RuntimeException(

        'hasParameters() returned false incorrectly.'

    );

}


/*
|--------------------------------------------------------------------------
| Static Route
|--------------------------------------------------------------------------
*/

$staticRoute = new Route(

    'GET',

    '/dashboard',

    function (): string {

        return 'dashboard';

    }

);


if ($staticRoute->hasParameters()) {

    throw new RuntimeException(

        'Static route incorrectly reports parameters.'

    );

}


if (
    $staticRoute->match('/dashboard')
    !==
    []
) {

    throw new RuntimeException(

        'Static route matching failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Duplicate Parameter
|--------------------------------------------------------------------------
*/

$duplicateRejected = false;

try {

    new Route(

        'GET',

        '/users/{id}/orders/{id}',

        function (): string {

            return 'invalid';

        }

    );

} catch (RuntimeException $exception) {

    $duplicateRejected = str_contains(

        $exception->getMessage(),

        'Duplicate route parameter'

    );

}


if (!$duplicateRejected) {

    throw new RuntimeException(

        'Duplicate route parameter was not rejected.'

    );

}


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

echo 'Route parameter tests passed successfully.'
    . PHP_EOL;