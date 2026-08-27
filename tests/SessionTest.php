<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Auth\Auth;
use Core\Security\Csrf;
use Core\Session\Session;


/*
|--------------------------------------------------------------------------
| Test Isolation
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    Session::start();
}

Session::flush();


/*
|--------------------------------------------------------------------------
| Test 1 — Session Starts
|--------------------------------------------------------------------------
*/

if (session_status() !== PHP_SESSION_ACTIVE) {

    throw new RuntimeException(
        'Session did not start correctly.'
    );
}


/*
|--------------------------------------------------------------------------
| Test 2 — Put / Get
|--------------------------------------------------------------------------
*/

Session::put(
    'test.key',
    'test-value'
);

$value = Session::get(
    'test.key'
);

if ($value !== 'test-value') {

    throw new RuntimeException(
        'Session put/get failed.'
    );
}


/*
|--------------------------------------------------------------------------
| Test 3 — Has
|--------------------------------------------------------------------------
*/

if (!Session::has('test.key')) {

    throw new RuntimeException(
        'Session has() failed.'
    );
}


/*
|--------------------------------------------------------------------------
| Test 4 — Forget
|--------------------------------------------------------------------------
*/

Session::forget('test.key');

if (Session::has('test.key')) {

    throw new RuntimeException(
        'Session forget() failed.'
    );
}


/*
|--------------------------------------------------------------------------
| Test 5 — Default Value
|--------------------------------------------------------------------------
*/

$default = Session::get(
    'missing.key',
    'default-value'
);

if ($default !== 'default-value') {

    throw new RuntimeException(
        'Session default value handling failed.'
    );
}


/*
|--------------------------------------------------------------------------
| Test 6 — Flash / Pull
|--------------------------------------------------------------------------
*/

Session::flash(
    'test.message',
    'Flash message'
);

$flash = Session::pull(
    'test.message'
);

if ($flash !== 'Flash message') {

    throw new RuntimeException(
        'Session flash/pull failed.'
    );
}


/*
|--------------------------------------------------------------------------
| Test 7 — Flash Is Removed After Pull
|--------------------------------------------------------------------------
*/

$flash = Session::pull(
    'test.message'
);

if ($flash !== null) {

    throw new RuntimeException(
        'Flash message was not removed after pull().'
    );
}


/*
|--------------------------------------------------------------------------
| Test 8 — Authentication State
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

if (!Auth::check()) {

    throw new RuntimeException(
        'Authentication state was not detected.'
    );
}

if (Auth::id() !== 1) {

    throw new RuntimeException(
        'Authenticated user ID is incorrect.'
    );
}

if (
    Auth::username()
    !==
    'daniel@inveniumtech.com'
) {

    throw new RuntimeException(
        'Authenticated username is incorrect.'
    );
}

if (
    Auth::role()
    !==
    'Administrator'
) {

    throw new RuntimeException(
        'Authenticated role is incorrect.'
    );
}


/*
|--------------------------------------------------------------------------
| Test 9 — CSRF Token Uses Session
|--------------------------------------------------------------------------
*/

$token = Csrf::token();

if (
    !is_string($token)
    ||
    $token === ''
) {

    throw new RuntimeException(
        'CSRF token was not generated.'
    );
}

if (!Csrf::verify($token)) {

    throw new RuntimeException(
        'CSRF token could not be verified.'
    );
}


/*
|--------------------------------------------------------------------------
| Test 10 — Session Regeneration
|--------------------------------------------------------------------------
*/

$oldId = session_id();

if ($oldId === '') {

    throw new RuntimeException(
        'Session ID was not available before regeneration.'
    );
}

if (!Session::regenerate()) {

    throw new RuntimeException(
        'Session ID regeneration failed.'
    );
}

$newId = session_id();

if ($newId === '') {

    throw new RuntimeException(
        'Session ID was empty after regeneration.'
    );
}

if ($oldId === $newId) {

    throw new RuntimeException(
        'Session ID was not regenerated.'
    );
}


/*
|--------------------------------------------------------------------------
| Test 11 — Authentication Survives Regeneration
|--------------------------------------------------------------------------
*/

if (!Auth::check()) {

    throw new RuntimeException(
        'Authentication state was lost during session regeneration.'
    );
}


/*
|--------------------------------------------------------------------------
| Test 12 — Session Destroy
|--------------------------------------------------------------------------
*/

Session::destroy();

if (Auth::check()) {

    throw new RuntimeException(
        'Authentication state survived session destruction.'
    );
}


/*
|--------------------------------------------------------------------------
| Test 13 — Session Can Start Again
|--------------------------------------------------------------------------
*/

Session::start();

Session::put(
    'test.after_destroy',
    'working'
);

if (
    Session::get('test.after_destroy')
    !==
    'working'
) {

    throw new RuntimeException(
        'Session could not be used after destruction.'
    );
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

echo 'Session tests passed successfully.'
    . PHP_EOL;