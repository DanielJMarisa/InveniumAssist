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

final class RouterRouteParameterTestController
{
    public function show(
        int $id
    ): string {

        return 'customer:' . $id;
    }


    public function order(
        int $customerId,
        int $orderId
    ): string {

        return 'customer:'
            . $customerId
            . '|order:'
            . $orderId;
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
| Single Parameter
|--------------------------------------------------------------------------
*/

$router->get(

    '/customers/{id}',

    [
        RouterRouteParameterTestController::class,
        'show'
    ]

);


$result = $router->dispatch(

    'GET',

    '/customers/42'

);


if ($result !== 'customer:42') {

    throw new RuntimeException(

        'Single route parameter controller injection failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Multiple Parameters
|--------------------------------------------------------------------------
*/

$router->get(

    '/customers/{customerId}/orders/{orderId}',

    [
        RouterRouteParameterTestController::class,
        'order'
    ]

);


$result = $router->dispatch(

    'GET',

    '/customers/15/orders/928'

);


if ($result !== 'customer:15|order:928') {

    throw new RuntimeException(

        'Multiple route parameter controller injection failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Invalid Integer
|--------------------------------------------------------------------------
*/

$invalidRejected = false;

try {

    $router->dispatch(

        'GET',

        '/customers/not-an-integer'

    );

} catch (RuntimeException $exception) {

    $invalidRejected = str_contains(

        $exception->getMessage(),

        'Invalid integer route parameter'

    );

}


if (!$invalidRejected) {

    throw new RuntimeException(

        'Invalid integer route parameter was not rejected.'

    );

}


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

echo 'Router route parameter tests passed successfully.'
    . PHP_EOL;