<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Exceptions\AuthenticationException;
use Core\Exceptions\AuthorizationException;
use Core\Exceptions\Handler;
use Core\Exceptions\HttpException;
use Core\Http\Response;


/*
|--------------------------------------------------------------------------
| Test Isolation
|--------------------------------------------------------------------------
*/

$_SERVER['REQUEST_URI'] = '/test';

ob_start();


/*
|--------------------------------------------------------------------------
| Helper Functions
|--------------------------------------------------------------------------
*/

/**
 * Capture a Response object without sending it.
 */
function assertResponseStatus(
    Response $response,
    int $expectedStatus,
    string $message
): void {

    /*
    |----------------------------------------------------------------------
    | Response status is intentionally tested through the object.
    |----------------------------------------------------------------------
    */

    $reflection = new ReflectionClass($response);

    $property = null;

    foreach (['status', 'statusCode'] as $name) {

        if ($reflection->hasProperty($name)) {

            $property = $reflection->getProperty($name);

            $property->setAccessible(true);

            break;
        }
    }

    if ($property === null) {

        throw new RuntimeException(

            'Unable to locate Response status property.'

        );
    }

    $actualStatus = $property->getValue($response);

    if ($actualStatus !== $expectedStatus) {

        throw new RuntimeException(

            $message
            . ' Expected '
            . $expectedStatus
            . ', received '
            . var_export($actualStatus, true)
            . '.'

        );
    }
}


/*
|--------------------------------------------------------------------------
| Test 1 — Authentication Exception
|--------------------------------------------------------------------------
*/

$exception = new AuthenticationException();

if ($exception->status() !== 401) {

    throw new RuntimeException(

        'AuthenticationException did not return HTTP 401.'

    );
}

if (
    $exception->getMessage()
    !==
    'Authentication required.'
) {

    throw new RuntimeException(

        'AuthenticationException returned an unexpected message.'

    );
}


/*
|--------------------------------------------------------------------------
| Test 2 — Authorization Exception
|--------------------------------------------------------------------------
*/

$exception = new AuthorizationException();

if ($exception->status() !== 403) {

    throw new RuntimeException(

        'AuthorizationException did not return HTTP 403.'

    );
}

if (
    $exception->getMessage()
    !==
    'You are not authorised to perform this action.'
) {

    throw new RuntimeException(

        'AuthorizationException returned an unexpected message.'

    );
}


/*
|--------------------------------------------------------------------------
| Test 3 — Generic HTTP Exception
|--------------------------------------------------------------------------
*/

$exception = new HttpException(

    422,

    'Validation failed.'

);

if ($exception->status() !== 422) {

    throw new RuntimeException(

        'HttpException did not preserve its HTTP status.'

    );
}

if (
    $exception->getMessage()
    !==
    'Validation failed.'
) {

    throw new RuntimeException(

        'HttpException did not preserve its message.'

    );
}


/*
|--------------------------------------------------------------------------
| Test 4 — HTTP Exception Data
|--------------------------------------------------------------------------
*/

$exception = new HttpException(

    422,

    'Invalid data.',

    [
        'field' => 'email',
        'reason' => 'Invalid format'
    ]

);

$data = $exception->data();

if (!is_array($data)) {

    throw new RuntimeException(

        'HttpException data() did not return an array.'

    );
}

if (
    ($data['field'] ?? null)
    !==
    'email'
) {

    throw new RuntimeException(

        'HttpException did not preserve exception data.'

    );
}


/*
|--------------------------------------------------------------------------
| Test 5 — Handler Registration
|--------------------------------------------------------------------------
*/

Handler::register();

Handler::register();


/*
|--------------------------------------------------------------------------
| Test 6 — Development Status Mapping
|--------------------------------------------------------------------------
|
| The handler's status() method is private, so test the behavior through
| the exception types that feed into it.
|
*/

$cases = [

    [
        new AuthenticationException(),
        401
    ],

    [
        new AuthorizationException(),
        403
    ],

    [
        new HttpException(
            419,
            'Page expired.'
        ),
        419
    ],

    [
        new HttpException(
            404,
            'Not found.'
        ),
        404
    ],

    [
        new HttpException(
            405,
            'Method not allowed.'
        ),
        405
    ],

    [
        new HttpException(
            422,
            'Invalid data.'
        ),
        422
    ],

    [
        new HttpException(
            429,
            'Too many requests.'
        ),
        429
    ]

];

foreach ($cases as [$exception, $expectedStatus]) {

    if ($exception->status() !== $expectedStatus) {

        throw new RuntimeException(

            sprintf(

                'Exception status mapping failed. Expected %d, received %d.',

                $expectedStatus,

                $exception->status()

            )

        );
    }
}


/*
|--------------------------------------------------------------------------
| Test 7 — Generic Exception Status
|--------------------------------------------------------------------------
*/

$reflection = new ReflectionClass(Handler::class);

$statusMethod = $reflection->getMethod('status');

$statusMethod->setAccessible(true);

$genericException = new RuntimeException(

    'Unexpected failure.'

);

$status = $statusMethod->invoke(

    null,

    $genericException

);

if ($status !== 500) {

    throw new RuntimeException(

        'Generic exceptions did not resolve to HTTP 500.'

    );
}


/*
|--------------------------------------------------------------------------
| Test 8 — Authentication API Detection
|--------------------------------------------------------------------------
*/

$_SERVER['REQUEST_URI'] = '/api/dashboard';

$apiMethod = $reflection->getMethod('isApiRequest');

$apiMethod->setAccessible(true);

$isApi = $apiMethod->invoke(null);

if ($isApi !== true) {

    throw new RuntimeException(

        'API request detection failed for /api/dashboard.'

    );
}


/*
|--------------------------------------------------------------------------
| Test 9 — Normal Browser Request Detection
|--------------------------------------------------------------------------
*/

$_SERVER['REQUEST_URI'] = '/dashboard';

$isApi = $apiMethod->invoke(null);

if ($isApi !== false) {

    throw new RuntimeException(

        'API request detection incorrectly classified /dashboard as API.'

    );
}


/*
|--------------------------------------------------------------------------
| Test 10 — API Root Detection
|--------------------------------------------------------------------------
*/

$_SERVER['REQUEST_URI'] = '/api';

$isApi = $apiMethod->invoke(null);

if ($isApi !== true) {

    throw new RuntimeException(

        'API request detection failed for /api.'

    );
}


/*
|--------------------------------------------------------------------------
| Test 11 — Production Message Mapping
|--------------------------------------------------------------------------
*/

$messageMethod = $reflection->getMethod(

    'productionMessage'

);

$messageMethod->setAccessible(true);

$messages = [

    400 =>
        'Bad request.',

    401 =>
        'Authentication required.',

    403 =>
        'Access denied.',

    404 =>
        'Page not found.',

    405 =>
        'Method not allowed.',

    419 =>
        'Page expired. Please refresh and try again.',

    422 =>
        'The submitted data is invalid.',

    429 =>
        'Too many requests. Please try again later.',

    500 =>
        'An unexpected error occurred.'

];

foreach ($messages as $status => $expectedMessage) {

    $message = $messageMethod->invoke(

        null,

        $status

    );

    if ($message !== $expectedMessage) {

        throw new RuntimeException(

            sprintf(

                'Production message mismatch for HTTP %d.',

                $status

            )

        );
    }
}


/*
|--------------------------------------------------------------------------
| Cleanup
|--------------------------------------------------------------------------
*/

while (ob_get_level() > 0) {

    ob_end_clean();

}

$_SERVER['REQUEST_URI'] = '/';


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

echo 'Exception handler tests passed successfully.'
    . PHP_EOL;