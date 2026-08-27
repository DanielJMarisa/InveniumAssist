<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Routing\Route;
use Core\Routing\RouteCollection;



/*
|--------------------------------------------------------------------------
| Create Collection
|--------------------------------------------------------------------------
*/

$routes = new RouteCollection();


/*
|--------------------------------------------------------------------------
| Add Route
|--------------------------------------------------------------------------
*/

$route = new Route(

    'GET',

    '/dashboard',

    function (): string {

        return 'dashboard';

    }

);

$routes->add($route);


/*
|--------------------------------------------------------------------------
| Match Route
|--------------------------------------------------------------------------
*/

$matched = $routes->match(

    'GET',

    '/dashboard/'

);


if ($matched !== $route) {

    throw new RuntimeException(

        'Route matching failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Method Case
|--------------------------------------------------------------------------
*/

if (
    !$routes->has(
        'get',
        '/dashboard'
    )
) {

    throw new RuntimeException(

        'Case-insensitive method matching failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Count
|--------------------------------------------------------------------------
*/

if ($routes->count() !== 1) {

    throw new RuntimeException(

        'Route count failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Duplicate Route
|--------------------------------------------------------------------------
*/

$duplicateRejected = false;

try {

    $routes->add(

        new Route(

            'GET',

            '/dashboard',

            function (): string {

                return 'duplicate';

            }

        )

    );

} catch (RuntimeException $exception) {

    $duplicateRejected = str_contains(

        $exception->getMessage(),

        'Route already registered'

    );

}


if (!$duplicateRejected) {

    throw new RuntimeException(

        'Duplicate route was not rejected.'

    );

}


/*
|--------------------------------------------------------------------------
| Missing Route
|--------------------------------------------------------------------------
*/

if (
    $routes->match(
        'GET',
        '/missing'
    ) !== null
) {

    throw new RuntimeException(

        'Missing route unexpectedly matched.'

    );

}


/*
|--------------------------------------------------------------------------
| Clear
|--------------------------------------------------------------------------
*/

$routes->clear();


if ($routes->count() !== 0) {

    throw new RuntimeException(

        'Route collection clear failed.'

    );

}


echo 'Route collection tests passed successfully.'
    . PHP_EOL;