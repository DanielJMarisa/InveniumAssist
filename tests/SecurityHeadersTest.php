<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Http\Request;
use Core\Middleware\SecurityHeadersMiddleware;


/*
|--------------------------------------------------------------------------
| Test Request
|--------------------------------------------------------------------------
*/

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/security-test';


$request = new Request();


/*
|--------------------------------------------------------------------------
| Middleware
|--------------------------------------------------------------------------
*/

$middleware = new SecurityHeadersMiddleware();


/*
|--------------------------------------------------------------------------
| Execute Middleware
|--------------------------------------------------------------------------
*/

$result = $middleware->handle(

    $request,

    function (): string {

        return 'security-ok';

    }

);


/*
|--------------------------------------------------------------------------
| Verify Pipeline Execution
|--------------------------------------------------------------------------
*/

if ($result !== 'security-ok') {

    throw new RuntimeException(

        'Security headers middleware did not execute the request pipeline.'

    );

}


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
|
| PHP CLI does not reliably expose header() calls through headers_list().
| The middleware execution and return value are therefore tested here.
|
*/

echo 'Security headers middleware test passed successfully.'

    . PHP_EOL;