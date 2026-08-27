<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Exceptions\AuthenticationException;
use Core\Exceptions\AuthorizationException;
use Core\Http\Request;
use Core\Middleware\RoleMiddleware;
use Core\Session\Session;


/*
|--------------------------------------------------------------------------
| Test Isolation
|--------------------------------------------------------------------------
*/

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/admin';

$_POST = [];
$_GET = [];

Session::start();
Session::flush();

$request = new Request();


/*
|--------------------------------------------------------------------------
| Helper
|--------------------------------------------------------------------------
*/

function assertThrows(
    string $exceptionClass,
    callable $callback,
    string $failureMessage
): void {
    try {

        $callback();

    } catch (\Throwable $exception) {

        if (!$exception instanceof $exceptionClass) {

            throw new RuntimeException(
                $failureMessage
                . ' Unexpected exception: '
                . get_class($exception)
            );

        }

        return;
    }

    throw new RuntimeException(
        $failureMessage
    );
}


/*
|--------------------------------------------------------------------------
| Test 1 — Unauthenticated User
|--------------------------------------------------------------------------
*/

$middleware = new RoleMiddleware('Administrator');

$executed = false;

assertThrows(
    AuthenticationException::class,

    function () use (
        $middleware,
        $request,
        &$executed
    ): void {

        $middleware->handle(

            $request,

            function () use (&$executed): string {

                $executed = true;

                return 'should-not-execute';

            }

        );

    },

    'Unauthenticated user was allowed through RoleMiddleware.'
);

if ($executed) {

    throw new RuntimeException(
        'Unauthenticated request continued through RoleMiddleware.'
    );

}


/*
|--------------------------------------------------------------------------
| Test 2 — Administrator Can Access Administrator Route
|--------------------------------------------------------------------------
*/

Session::put(
    'auth.user_id',
    1
);

Session::put(
    'auth.username',
    'daniel@inveniumtech.com'
);

Session::put(
    'auth.role',
    'Administrator'
);

$middleware = new RoleMiddleware('Administrator');

$executed = false;

$result = $middleware->handle(

    $request,

    function () use (&$executed): string {

        $executed = true;

        return 'administrator-authorized';

    }

);

if (!$executed || $result !== 'administrator-authorized') {

    throw new RuntimeException(
        'Administrator was not authorized for Administrator access.'
    );

}


/*
|--------------------------------------------------------------------------
| Test 3 — Technician Cannot Access Administrator Route
|--------------------------------------------------------------------------
*/

Session::put(
    'auth.role',
    'Technician'
);

$middleware = new RoleMiddleware('Administrator');

$executed = false;

assertThrows(
    AuthorizationException::class,

    function () use (
        $middleware,
        $request,
        &$executed
    ): void {

        $middleware->handle(

            $request,

            function () use (&$executed): string {

                $executed = true;

                return 'should-not-execute';

            }

        );

    },

    'Technician was incorrectly authorized for Administrator access.'
);

if ($executed) {

    throw new RuntimeException(
        'Technician continued through Administrator middleware.'
    );

}


/*
|--------------------------------------------------------------------------
| Test 4 — Customer Cannot Access Administrator Route
|--------------------------------------------------------------------------
*/

Session::put(
    'auth.role',
    'Customer'
);

$middleware = new RoleMiddleware('Administrator');

$executed = false;

assertThrows(
    AuthorizationException::class,

    function () use (
        $middleware,
        $request,
        &$executed
    ): void {

        $middleware->handle(

            $request,

            function () use (&$executed): string {

                $executed = true;

                return 'should-not-execute';

            }

        );

    },

    'Customer was incorrectly authorized for Administrator access.'
);

if ($executed) {

    throw new RuntimeException(
        'Customer continued through Administrator middleware.'
    );

}


/*
|--------------------------------------------------------------------------
| Test 5 — Super Admin Can Access Administrator Route
|--------------------------------------------------------------------------
*/

Session::put(
    'auth.role',
    'Super Admin'
);

$middleware = new RoleMiddleware(
    [
        'Super Admin',
        'Administrator'
    ]
);

$executed = false;

$result = $middleware->handle(

    $request,

    function () use (&$executed): string {

        $executed = true;

        return 'super-admin-authorized';

    }

);

if (!$executed || $result !== 'super-admin-authorized') {

    throw new RuntimeException(
        'Super Admin was not authorized for Administrator access.'
    );

}


/*
|--------------------------------------------------------------------------
| Test 6 — Technician Can Access Technician Route
|--------------------------------------------------------------------------
*/

Session::put(
    'auth.role',
    'Technician'
);

$middleware = new RoleMiddleware('Technician');

$executed = false;

$result = $middleware->handle(

    $request,

    function () use (&$executed): string {

        $executed = true;

        return 'technician-authorized';

    }

);

if (!$executed || $result !== 'technician-authorized') {

    throw new RuntimeException(
        'Technician was not authorized for Technician access.'
    );

}


/*
|--------------------------------------------------------------------------
| Test 7 — Customer Can Access Customer Route
|--------------------------------------------------------------------------
*/

Session::put(
    'auth.role',
    'Customer'
);

$middleware = new RoleMiddleware('Customer');

$executed = false;

$result = $middleware->handle(

    $request,

    function () use (&$executed): string {

        $executed = true;

        return 'customer-authorized';

    }

);

if (!$executed || $result !== 'customer-authorized') {

    throw new RuntimeException(
        'Customer was not authorized for Customer access.'
    );

}


/*
|--------------------------------------------------------------------------
| Test 8 — Multiple Allowed Roles
|--------------------------------------------------------------------------
*/

Session::put(
    'auth.role',
    'Technician'
);

$middleware = new RoleMiddleware(
    [
        'Super Admin',
        'Administrator',
        'Technician'
    ]
);

$executed = false;

$result = $middleware->handle(

    $request,

    function () use (&$executed): string {

        $executed = true;

        return 'multi-role-authorized';

    }

);

if (!$executed || $result !== 'multi-role-authorized') {

    throw new RuntimeException(
        'Technician was rejected from a route allowing multiple roles.'
    );

}


/*
|--------------------------------------------------------------------------
| Test 9 — Customer Rejected From Technician Route
|--------------------------------------------------------------------------
*/

Session::put(
    'auth.role',
    'Customer'
);

$middleware = new RoleMiddleware(
    [
        'Super Admin',
        'Administrator',
        'Technician'
    ]
);

$executed = false;

assertThrows(
    AuthorizationException::class,

    function () use (
        $middleware,
        $request,
        &$executed
    ): void {

        $middleware->handle(

            $request,

            function () use (&$executed): string {

                $executed = true;

                return 'should-not-execute';

            }

        );

    },

    'Customer was incorrectly authorized for Technician access.'
);

if ($executed) {

    throw new RuntimeException(
        'Customer continued through restricted Technician middleware.'
    );

}


/*
|--------------------------------------------------------------------------
| Test 10 — Authorization Exception Message
|--------------------------------------------------------------------------
*/

Session::put(
    'auth.role',
    'Customer'
);

$middleware = new RoleMiddleware('Administrator');

try {

    $middleware->handle(

        $request,

        function (): string {

            return 'should-not-execute';

        }

    );

    throw new RuntimeException(
        'Authorization middleware unexpectedly allowed Customer access.'
    );

} catch (AuthorizationException $exception) {

    if (
        $exception->getMessage()
        !==
        'You do not have permission to access this resource.'
    ) {

        throw new RuntimeException(
            'Unexpected authorization exception message.'
        );

    }

}


/*
|--------------------------------------------------------------------------
| Cleanup
|--------------------------------------------------------------------------
*/

Session::flush();


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

echo 'Role middleware tests passed successfully.'
    . PHP_EOL;