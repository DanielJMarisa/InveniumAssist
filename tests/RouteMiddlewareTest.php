<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Container\Container;
use Core\Http\Request;
use Core\Routing\Router;


/*
|--------------------------------------------------------------------------
| Test Middleware
|--------------------------------------------------------------------------
*/

final class RouteMiddlewareTestMiddleware
{
    public static array $events = [];

    public function handle(
        Request $request,
        Closure $next
    ): mixed
    {
        self::$events[] = 'before';

        $response = $next();

        self::$events[] = 'after';

        return $response;
    }
}


/*
|--------------------------------------------------------------------------
| Blocking Middleware
|--------------------------------------------------------------------------
*/

final class RouteMiddlewareBlockingTestMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): mixed
    {
        return 'blocked';
    }
}


/*
|--------------------------------------------------------------------------
| Test Controller
|--------------------------------------------------------------------------
*/

final class RouteMiddlewareTestController
{
    public static bool $executed = false;

    public function index(): string
    {
        self::$executed = true;

        RouteMiddlewareTestMiddleware::$events[] = 'controller';

        return 'controller';
    }
}


/*
|--------------------------------------------------------------------------
| Test Request
|--------------------------------------------------------------------------
*/

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/middleware';


/*
|--------------------------------------------------------------------------
| Container
|--------------------------------------------------------------------------
*/

$container = new Container();

$request = new Request();

$container->instance(

    Request::class,

    $request

);


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
| Register Route
|--------------------------------------------------------------------------
*/

$router

    ->get(

        '/middleware',

        [

            RouteMiddlewareTestController::class,

            'index'

        ]

    )

    ->routes();


/*
|--------------------------------------------------------------------------
| Retrieve Route
|--------------------------------------------------------------------------
*/

$route = $router

    ->routes()['GET']['/middleware'];


/*
|--------------------------------------------------------------------------
| Attach Middleware
|--------------------------------------------------------------------------
*/

$route->middleware(

    RouteMiddlewareTestMiddleware::class

);


/*
|--------------------------------------------------------------------------
| Dispatch
|--------------------------------------------------------------------------
*/

$result = $router->dispatch(

    'GET',

    '/middleware'

);


/*
|--------------------------------------------------------------------------
| Assertions
|--------------------------------------------------------------------------
*/

if ($result !== 'controller') {

    throw new RuntimeException(

        'Route middleware test returned an unexpected response.'

    );

}


if (
    RouteMiddlewareTestMiddleware::$events
    !==
    [
        'before',
        'controller',
        'after'
    ]
) {

    throw new RuntimeException(

        'Route middleware did not execute in the expected order.'

    );

}


if (!RouteMiddlewareTestController::$executed) {

    throw new RuntimeException(

        'Route controller was not executed.'

    );

}


/*
|--------------------------------------------------------------------------
| Blocking Middleware Test
|--------------------------------------------------------------------------
*/

RouteMiddlewareTestController::$executed = false;

$blockingRouter = new Router(

    $container

);

$blockingRouter

    ->get(

        '/blocked',

        [

            RouteMiddlewareTestController::class,

            'index'

        ]

    )

    ->routes();


$blockedRoute = $blockingRouter

    ->routes()['GET']['/blocked'];


$blockedRoute->middleware(

    RouteMiddlewareBlockingTestMiddleware::class

);


$blockedResult = $blockingRouter->dispatch(

    'GET',

    '/blocked'

);


if ($blockedResult !== 'blocked') {

    throw new RuntimeException(

        'Blocking middleware did not return the expected response.'

    );

}


if (RouteMiddlewareTestController::$executed) {

    throw new RuntimeException(

        'Controller executed despite middleware blocking the request.'

    );

}


echo PHP_EOL;

echo 'Route middleware tests passed successfully.';

echo PHP_EOL;