<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Http\URL;


/*
|--------------------------------------------------------------------------
| Test Configuration
|--------------------------------------------------------------------------
*/

if (!defined('BASE_URI')) {

    define('BASE_URI', '/assist');

}


/*
|--------------------------------------------------------------------------
| Preserve Server State
|--------------------------------------------------------------------------
*/

$originalServer = $_SERVER;


/*
|--------------------------------------------------------------------------
| HTTP Base URL
|--------------------------------------------------------------------------
*/

$_SERVER = [

    'HTTP_HOST' => 'localhost:8080',

    'REQUEST_URI' => '/assist/dashboard',

    'HTTPS' => 'off',

];


if (URL::base() !== 'http://localhost:8080/assist') {

    throw new RuntimeException(

        'URL::base() failed.'

    );

}


/*
|--------------------------------------------------------------------------
| HTTPS Base URL
|--------------------------------------------------------------------------
*/

$_SERVER['HTTPS'] = 'on';


if (URL::base() !== 'https://localhost:8080/assist') {

    throw new RuntimeException(

        'HTTPS URL generation failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Current URL
|--------------------------------------------------------------------------
*/

if (
    URL::current()
    !==
    'https://localhost:8080/assist/dashboard'
) {

    throw new RuntimeException(

        'URL::current() failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Path
|--------------------------------------------------------------------------
*/

if (URL::path() !== '/dashboard') {

    throw new RuntimeException(

        'URL::path() failed.'

    );

}


/*
|--------------------------------------------------------------------------
| URL::to()
|--------------------------------------------------------------------------
*/

if (
    URL::to('/login')
    !==
    'https://localhost:8080/assist/login'
) {

    throw new RuntimeException(

        'URL::to() failed.'

    );

}


/*
|--------------------------------------------------------------------------
| URL::to() Without Leading Slash
|--------------------------------------------------------------------------
*/

if (
    URL::to('login')
    !==
    'https://localhost:8080/assist/login'
) {

    throw new RuntimeException(

        'URL::to() normalisation failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Asset URL
|--------------------------------------------------------------------------
*/

if (
    URL::asset('/css/app.css')
    !==
    'https://localhost:8080/assist/css/app.css'
) {

    throw new RuntimeException(

        'URL::asset() failed.'

    );

}


/*
|--------------------------------------------------------------------------
| API URL
|--------------------------------------------------------------------------
*/

if (
    URL::api('/users')
    !==
    'https://localhost:8080/assist/api/users'
) {

    throw new RuntimeException(

        'URL::api() failed.'

    );

}


/*
|--------------------------------------------------------------------------
| API URL Without Leading Slash
|--------------------------------------------------------------------------
*/

if (
    URL::api('users')
    !==
    'https://localhost:8080/assist/api/users'
) {

    throw new RuntimeException(

        'URL::api() normalisation failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Forwarded HTTPS
|--------------------------------------------------------------------------
*/

$_SERVER = [

    'HTTP_HOST' => 'localhost:8080',

    'REQUEST_URI' => '/assist/dashboard',

    'HTTP_X_FORWARDED_PROTO' => 'https',

    'HTTPS' => 'off',

];


if (URL::base() !== 'https://localhost:8080/assist') {

    throw new RuntimeException(

        'Forwarded HTTPS URL generation failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Restore Server State
|--------------------------------------------------------------------------
*/

$_SERVER = $originalServer;


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

echo 'URL tests passed successfully.'
    . PHP_EOL;