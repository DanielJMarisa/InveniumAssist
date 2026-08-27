<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Http\Request;
use Core\Middleware\CsrfMiddleware;
use Core\Security\Csrf;
use Core\Session\Session;


/*
|--------------------------------------------------------------------------
| Test Isolation
|--------------------------------------------------------------------------
*/

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/csrf-test';

$_POST = [];
$_GET = [];


/*
|--------------------------------------------------------------------------
| Reset Session
|--------------------------------------------------------------------------
*/

Session::start();

Session::flush();


/*
|--------------------------------------------------------------------------
| CSRF Token Generation
|--------------------------------------------------------------------------
*/

$token = Csrf::token();


if ($token === '') {

    throw new RuntimeException(
        'CSRF token was not generated.'
    );

}


if (strlen($token) !== 64) {

    throw new RuntimeException(
        'CSRF token must contain 64 hexadecimal characters.'
    );

}


if (!ctype_xdigit($token)) {

    throw new RuntimeException(
        'CSRF token contains invalid characters.'
    );

}


/*
|--------------------------------------------------------------------------
| Token Retrieval
|--------------------------------------------------------------------------
*/

if (Csrf::token() !== $token) {

    throw new RuntimeException(
        'CSRF token was not consistently retrieved from the session.'
    );

}


/*
|--------------------------------------------------------------------------
| Valid Token Verification
|--------------------------------------------------------------------------
*/

if (!Csrf::verify($token)) {

    throw new RuntimeException(
        'Valid CSRF token failed verification.'
    );

}


/*
|--------------------------------------------------------------------------
| Invalid Token Verification
|--------------------------------------------------------------------------
*/

if (Csrf::verify('invalid-token')) {

    throw new RuntimeException(
        'Invalid CSRF token was accepted.'
    );

}


/*
|--------------------------------------------------------------------------
| Missing Token Verification
|--------------------------------------------------------------------------
*/

if (Csrf::verify(null)) {

    throw new RuntimeException(
        'Missing CSRF token was accepted.'
    );

}


/*
|--------------------------------------------------------------------------
| Regeneration
|--------------------------------------------------------------------------
*/

$newToken = Csrf::regenerate();


if ($newToken === $token) {

    throw new RuntimeException(
        'CSRF token regeneration did not create a new token.'
    );

}


if (!Csrf::verify($newToken)) {

    throw new RuntimeException(
        'Regenerated CSRF token failed verification.'
    );

}


if (Csrf::verify($token)) {

    throw new RuntimeException(
        'Previous CSRF token remained valid after regeneration.'
    );

}


/*
|--------------------------------------------------------------------------
| Restore New Token
|--------------------------------------------------------------------------
*/

$token = $newToken;


/*
|--------------------------------------------------------------------------
| Middleware
|--------------------------------------------------------------------------
*/

$middleware = new CsrfMiddleware();

$request = new Request();


/*
|--------------------------------------------------------------------------
| Safe GET Request
|--------------------------------------------------------------------------
*/

$_SERVER['REQUEST_METHOD'] = 'GET';

$_POST = [];

$result = $middleware->handle(

    $request,

    function (): string {

        return 'csrf-get-ok';

    }

);


if ($result !== 'csrf-get-ok') {

    throw new RuntimeException(
        'GET request was incorrectly blocked by CSRF middleware.'
    );

}


/*
|--------------------------------------------------------------------------
| Valid POST Request
|--------------------------------------------------------------------------
*/

$_SERVER['REQUEST_METHOD'] = 'POST';

$_POST = [

    '_token' => $token

];


$result = $middleware->handle(

    $request,

    function (): string {

        return 'csrf-post-ok';

    }

);


if ($result !== 'csrf-post-ok') {

    throw new RuntimeException(
        'Valid POST request was blocked by CSRF middleware.'
    );

}


/*
|--------------------------------------------------------------------------
| Valid PUT Request
|--------------------------------------------------------------------------
*/

$_SERVER['REQUEST_METHOD'] = 'PUT';

$_POST = [

    '_token' => $token

];


$result = $middleware->handle(

    $request,

    function (): string {

        return 'csrf-put-ok';

    }

);


if ($result !== 'csrf-put-ok') {

    throw new RuntimeException(
        'Valid PUT request was blocked by CSRF middleware.'
    );

}


/*
|--------------------------------------------------------------------------
| Valid PATCH Request
|--------------------------------------------------------------------------
*/

$_SERVER['REQUEST_METHOD'] = 'PATCH';

$_POST = [

    '_token' => $token

];


$result = $middleware->handle(

    $request,

    function (): string {

        return 'csrf-patch-ok';

    }

);


if ($result !== 'csrf-patch-ok') {

    throw new RuntimeException(
        'Valid PATCH request was blocked by CSRF middleware.'
    );

}


/*
|--------------------------------------------------------------------------
| Valid DELETE Request
|--------------------------------------------------------------------------
*/

$_SERVER['REQUEST_METHOD'] = 'DELETE';

$_POST = [

    '_token' => $token

];


$result = $middleware->handle(

    $request,

    function (): string {

        return 'csrf-delete-ok';

    }

);


if ($result !== 'csrf-delete-ok') {

    throw new RuntimeException(
        'Valid DELETE request was blocked by CSRF middleware.'
    );

}


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

echo 'CSRF tests passed successfully.'

    . PHP_EOL;