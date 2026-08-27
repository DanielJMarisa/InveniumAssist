<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Http\Request;
use Core\Middleware\AuthMiddleware;
use Core\Session\Session;

/*
|--------------------------------------------------------------------------
| Test Isolation
|--------------------------------------------------------------------------
*/

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/protected';

$_POST = [];
$_GET = [];

Session::start();
Session::flush();

/*
|--------------------------------------------------------------------------
| Middleware
|--------------------------------------------------------------------------
*/

$middleware = new AuthMiddleware();

$request = new Request();

$executed = false;

/*
|--------------------------------------------------------------------------
| Unauthenticated Request
|--------------------------------------------------------------------------
*/

try {

    $middleware->handle(

        $request,

        function () use (&$executed): string {

            $executed = true;

            return 'should-not-execute';

        }

    );

    throw new RuntimeException(

        'Authentication middleware unexpectedly allowed an unauthenticated request.'

    );

} catch (\Core\Exceptions\AuthenticationException $exception) {

    if ($executed) {

        throw new RuntimeException(

            'Protected middleware pipeline continued after authentication failure.'

        );

    }

    if (
        $exception->getMessage()
        !==
        'Authentication required.'
    ) {

        throw new RuntimeException(

            'Unexpected authentication exception message.'

        );

    }
}

/*
|--------------------------------------------------------------------------
| Authenticated Request
|--------------------------------------------------------------------------
*/

Session::put(

    'auth.user_id',

    1

);

$executed = false;

$result = $middleware->handle(

    $request,

    function () use (&$executed): string {

        $executed = true;

        return 'authenticated';

    }

);

if (!$executed) {

    throw new RuntimeException(

        'Authenticated request did not reach the next middleware.'

    );

}

if ($result !== 'authenticated') {

    throw new RuntimeException(

        'Authenticated middleware returned an unexpected result.'

    );

}

/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

Session::flush();

echo 'Authentication middleware tests passed successfully.'
    . PHP_EOL;