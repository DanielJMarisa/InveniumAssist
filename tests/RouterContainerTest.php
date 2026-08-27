<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Container\Container;
use Core\Routing\Router;



/*
|--------------------------------------------------------------------------
| Test Dependency
|--------------------------------------------------------------------------
*/

final class RouterTestDependency
{
    public function value(): string
    {
        return 'dependency-resolved';
    }
}


/*
|--------------------------------------------------------------------------
| Test Controller
|--------------------------------------------------------------------------
*/

final class RouterTestController
{
    private RouterTestDependency $dependency;


    public function __construct(
        RouterTestDependency $dependency
    )
    {
        $this->dependency = $dependency;
    }


    public function index(): string
    {
        return $this->dependency->value();
    }
}


/*
|--------------------------------------------------------------------------
| Create Container
|--------------------------------------------------------------------------
*/

$container = new Container();


/*
|--------------------------------------------------------------------------
| Resolve Router
|--------------------------------------------------------------------------
*/

$router = $container->make(

    Router::class

);


if (!$router instanceof Router) {

    throw new RuntimeException(

        'Router resolution failed.'

    );
}


/*
|--------------------------------------------------------------------------
| Register Controller Route
|--------------------------------------------------------------------------
*/

$router->get(

    '/test-router-di',

    [

        RouterTestController::class,

        'index'

    ]

);


/*
|--------------------------------------------------------------------------
| Dispatch Controller Route
|--------------------------------------------------------------------------
*/

$result = $router->dispatch(

    'GET',

    '/test-router-di'

);


/*
|--------------------------------------------------------------------------
| Verify Dependency Injection
|--------------------------------------------------------------------------
*/

if ($result !== 'dependency-resolved') {

    throw new RuntimeException(

        'Router controller dependency injection failed.'

    );
}


/*
|--------------------------------------------------------------------------
| Verify Router Callable Routes
|--------------------------------------------------------------------------
*/

$router->get(

    '/test-router-callable',

    function (): string {

        return 'callable-route';

    }

);


$callableResult = $router->dispatch(

    'GET',

    '/test-router-callable'

);


if ($callableResult !== 'callable-route') {

    throw new RuntimeException(

        'Router callable route test failed.'

    );
}


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

echo "Router container tests passed successfully."

    . PHP_EOL;
