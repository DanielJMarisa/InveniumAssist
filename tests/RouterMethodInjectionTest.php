<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Container\Container;
use Core\Http\Request;
use Core\Routing\Router;



/*
|--------------------------------------------------------------------------
| Test Dependency
|--------------------------------------------------------------------------
*/

final class RouterMethodTestDependency
{
    public function value(): string
    {
        return 'method-dependency-resolved';
    }
}


/*
|--------------------------------------------------------------------------
| Test Controller
|--------------------------------------------------------------------------
*/

final class RouterMethodTestController
{
    /**
     * Test class dependency injection.
     */
    public function dependency(
        RouterMethodTestDependency $dependency
    ): string
    {
        return $dependency->value();
    }


    /**
     * Test Request injection.
     */
    public function request(
        Request $request
    ): string
    {
        return $request instanceof Request

            ? 'request-resolved'

            : 'request-failed';
    }


    /**
     * Test multiple dependencies.
     */
    public function multiple(
        Request $request,
        RouterMethodTestDependency $dependency
    ): string
    {
        if (
            $request instanceof Request
            &&
            $dependency instanceof RouterMethodTestDependency
        ) {

            return 'multiple-resolved';

        }


        return 'multiple-failed';
    }


    /**
     * Test zero-argument method.
     */
    public function noDependencies(): string
    {
        return 'no-dependencies';
    }


    /**
     * Test optional parameter.
     */
    public function optional(
        ?string $value = 'default-value'
    ): string
    {
        return $value;
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
| Dependency Injection Test
|--------------------------------------------------------------------------
*/

$router->get(

    '/test-method-dependency',

    [

        RouterMethodTestController::class,

        'dependency'

    ]

);


$result = $router->dispatch(

    'GET',

    '/test-method-dependency'

);


if ($result !== 'method-dependency-resolved') {

    throw new RuntimeException(

        'Controller method dependency injection failed.'

    );
}


/*
|--------------------------------------------------------------------------
| Request Injection Test
|--------------------------------------------------------------------------
*/

$router->get(

    '/test-method-request',

    [

        RouterMethodTestController::class,

        'request'

    ]

);


$result = $router->dispatch(

    'GET',

    '/test-method-request'

);


if ($result !== 'request-resolved') {

    throw new RuntimeException(

        'Request method injection failed.'

    );
}


/*
|--------------------------------------------------------------------------
| Multiple Dependency Test
|--------------------------------------------------------------------------
*/

$router->get(

    '/test-method-multiple',

    [

        RouterMethodTestController::class,

        'multiple'

    ]

);


$result = $router->dispatch(

    'GET',

    '/test-method-multiple'

);


if ($result !== 'multiple-resolved') {

    throw new RuntimeException(

        'Multiple controller method dependencies failed.'

    );
}


/*
|--------------------------------------------------------------------------
| Zero Dependency Test
|--------------------------------------------------------------------------
*/

$router->get(

    '/test-method-none',

    [

        RouterMethodTestController::class,

        'noDependencies'

    ]

);


$result = $router->dispatch(

    'GET',

    '/test-method-none'

);


if ($result !== 'no-dependencies') {

    throw new RuntimeException(

        'Zero-argument controller method failed.'

    );
}


/*
|--------------------------------------------------------------------------
| Optional Parameter Test
|--------------------------------------------------------------------------
*/

$router->get(

    '/test-method-optional',

    [

        RouterMethodTestController::class,

        'optional'

    ]

);


$result = $router->dispatch(

    'GET',

    '/test-method-optional'

);


if ($result !== 'default-value') {

    throw new RuntimeException(

        'Optional controller method parameter failed.'

    );
}


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

echo "Router method injection tests passed successfully."

    . PHP_EOL;
