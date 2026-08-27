<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Container\Container;
use Core\Http\Request;
use Core\Routing\MiddlewarePipeline;


/*
|--------------------------------------------------------------------------
| Test Middleware
|--------------------------------------------------------------------------
*/

final class TestMiddleware
{
    public function __construct(
        TestDependency $dependency
    )
    {
        if (!$dependency instanceof TestDependency) {

            throw new RuntimeException(

                'Dependency injection failed.'

            );

        }
    }


    public function handle(
        Request $request,
        callable $next
    ): mixed
    {
        return $next();
    }
}


/*
|--------------------------------------------------------------------------
| Test Dependency
|--------------------------------------------------------------------------
*/

final class TestDependency
{
}


/*
|--------------------------------------------------------------------------
| Create Container
|--------------------------------------------------------------------------
*/

$container = new Container();


/*
|--------------------------------------------------------------------------
| Resolve Pipeline
|--------------------------------------------------------------------------
*/

$pipeline = $container->make(

    MiddlewarePipeline::class

);


if (!$pipeline instanceof MiddlewarePipeline) {

    throw new RuntimeException(

        'MiddlewarePipeline resolution failed.'

    );
}


/*
|--------------------------------------------------------------------------
| Create Request
|--------------------------------------------------------------------------
*/

$request = $container->make(

    Request::class

);


/*
|--------------------------------------------------------------------------
| Execute Middleware
|--------------------------------------------------------------------------
*/

$executed = false;


$result = $pipeline

    ->request(

        $request

    )

    ->through(

        [

            TestMiddleware::class

        ]

    )

    ->then(

        function () use (&$executed): string {

            $executed = true;

            return 'pipeline-complete';

        }

    );


/*
|--------------------------------------------------------------------------
| Verify Destination
|--------------------------------------------------------------------------
*/

if ($result !== 'pipeline-complete') {

    throw new RuntimeException(

        'Middleware pipeline destination failed.'

    );

}


if (!$executed) {

    throw new RuntimeException(

        'Middleware pipeline destination was not executed.'

    );

}


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

echo "Middleware pipeline container tests passed successfully."

    . PHP_EOL;

