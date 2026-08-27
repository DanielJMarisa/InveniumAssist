<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Container\Container;
use Core\Http\Request;
use Core\Middleware\Middleware;
use Core\Routing\MiddlewarePipeline;


/*
|--------------------------------------------------------------------------
| Test Middleware
|--------------------------------------------------------------------------
*/

final class PipelineFirstMiddleware extends Middleware
{
    public function handle(
        Request $request,
        callable $next
    ): mixed
    {
        $GLOBALS['pipeline_order'][] = 'first-before';

        $response = $this->next(
            $request,
            $next
        );

        $GLOBALS['pipeline_order'][] = 'first-after';

        return $response;
    }
}


final class PipelineSecondMiddleware extends Middleware
{
    public function handle(
        Request $request,
        callable $next
    ): mixed
    {
        $GLOBALS['pipeline_order'][] = 'second-before';

        $response = $this->next(
            $request,
            $next
        );

        $GLOBALS['pipeline_order'][] = 'second-after';

        return $response;
    }
}


final class PipelineThirdMiddleware extends Middleware
{
    public function handle(
        Request $request,
        callable $next
    ): mixed
    {
        $GLOBALS['pipeline_order'][] = 'third-before';

        $response = $this->next(
            $request,
            $next
        );

        $GLOBALS['pipeline_order'][] = 'third-after';

        return $response;
    }
}


/*
|--------------------------------------------------------------------------
| Execute Pipeline
|--------------------------------------------------------------------------
*/

$GLOBALS['pipeline_order'] = [];

$container = new Container();

$request = new Request();

$result = $container
    ->make(MiddlewarePipeline::class)
    ->request($request)
    ->through([
        PipelineFirstMiddleware::class,
        PipelineSecondMiddleware::class,
        PipelineThirdMiddleware::class,
    ])
    ->then(
        function (): string {

            $GLOBALS['pipeline_order'][] = 'destination';

            return 'complete';
        }
    );


/*
|--------------------------------------------------------------------------
| Verify Destination
|--------------------------------------------------------------------------
*/

if ($result !== 'complete') {

    throw new RuntimeException(
        'Pipeline destination did not execute correctly.'
    );

}


/*
|--------------------------------------------------------------------------
| Verify Ordering
|--------------------------------------------------------------------------
*/

$expected = [

    'first-before',

    'second-before',

    'third-before',

    'destination',

    'third-after',

    'second-after',

    'first-after'

];


if ($GLOBALS['pipeline_order'] !== $expected) {

    throw new RuntimeException(

        'Middleware execution order failed.'
        . PHP_EOL
        . 'Expected: '
        . json_encode($expected)
        . PHP_EOL
        . 'Actual: '
        . json_encode($GLOBALS['pipeline_order'])

    );

}


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

echo 'Middleware pipeline order tests passed successfully.'
    . PHP_EOL;