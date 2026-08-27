<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Container\Container;
use Core\Http\Request;
use Core\Kernel\Application;
use Core\Routing\MiddlewarePipeline;
use Core\Routing\Router;

echo "Running Application/Container integration tests..." . PHP_EOL;


/*
|--------------------------------------------------------------------------
| Test 1: Application exposes Container
|--------------------------------------------------------------------------
*/

$app = new Application();

$container = $app->container();

if (!$container instanceof Container) {

    throw new RuntimeException(

        'Application container test failed.'

    );
}


/*
|--------------------------------------------------------------------------
| Test 2: Application exposes Router
|--------------------------------------------------------------------------
*/

$router = $app->router();

if (!$router instanceof Router) {

    throw new RuntimeException(

        'Application router test failed.'

    );
}


/*
|--------------------------------------------------------------------------
| Test 3: Router is shared through Container
|--------------------------------------------------------------------------
*/

$resolvedRouter = $container->make(

    Router::class

);

if ($resolvedRouter !== $router) {

    throw new RuntimeException(

        'Router singleton test failed.'

    );
}


/*
|--------------------------------------------------------------------------
| Test 4: Application is registered in Container
|--------------------------------------------------------------------------
*/

$resolvedApplication = $container->make(

    Application::class

);

if ($resolvedApplication !== $app) {

    throw new RuntimeException(

        'Application container instance test failed.'

    );
}


/*
|--------------------------------------------------------------------------
| Test 5: MiddlewarePipeline can be resolved
|--------------------------------------------------------------------------
*/

$pipeline = $container->make(

    MiddlewarePipeline::class

);

if (!$pipeline instanceof MiddlewarePipeline) {

    throw new RuntimeException(

        'MiddlewarePipeline resolution test failed.'

    );
}


/*
|--------------------------------------------------------------------------
| Test 6: Request can be resolved automatically
|--------------------------------------------------------------------------
*/

$request = $container->make(

    Request::class

);

if (!$request instanceof Request) {

    throw new RuntimeException(

        'Request resolution test failed.'

    );
}


/*
|--------------------------------------------------------------------------
| Test 7: Current Request can be registered
|--------------------------------------------------------------------------
*/

$container->instance(

    Request::class,

    $request

);


$resolvedRequest = $container->make(

    Request::class

);

if ($resolvedRequest !== $request) {

    throw new RuntimeException(

        'Request instance registration test failed.'

    );
}


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

echo "Application/Container integration tests passed successfully."

    . PHP_EOL;
