<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Http\Response;


/*
|--------------------------------------------------------------------------
| Plain Response
|--------------------------------------------------------------------------
*/

$response = Response::make(

    'Hello Invenium'

);


if ($response->content() !== 'Hello Invenium') {

    throw new RuntimeException(

        'Plain response content failed.'

    );

}


if ($response->status() !== 200) {

    throw new RuntimeException(

        'Plain response status failed.'

    );

}


if (
    $response->headers()['Content-Type']
    !==
    'text/html; charset=UTF-8'
) {

    throw new RuntimeException(

        'Plain response content type failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Custom Status
|--------------------------------------------------------------------------
*/

$response = Response::make(

    'Created',

    201

);


if ($response->status() !== 201) {

    throw new RuntimeException(

        'Custom response status failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Custom Header
|--------------------------------------------------------------------------
*/

$response->header(

    'X-Test',

    'Invenium'

);


if (
    $response->headers()['X-Test']
    !==
    'Invenium'
) {

    throw new RuntimeException(

        'Custom response header failed.'

    );

}


/*
|--------------------------------------------------------------------------
| JSON Response
|--------------------------------------------------------------------------
*/

$response = Response::json(

    [

        'success' => true,

        'service' => 'Invenium Assist'

    ]

);


if ($response->status() !== 200) {

    throw new RuntimeException(

        'JSON response status failed.'

    );

}


if (
    $response->headers()['Content-Type']
    !==
    'application/json; charset=UTF-8'
) {

    throw new RuntimeException(

        'JSON response content type failed.'

    );

}


$json = json_decode(

    $response->content(),

    true

);


if (
    !is_array($json)
    ||
    $json['success'] !== true
    ||
    $json['service'] !== 'Invenium Assist'
) {

    throw new RuntimeException(

        'JSON response content failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Redirect Response
|--------------------------------------------------------------------------
*/

$_SERVER['HTTP_HOST'] = 'localhost';

$_SERVER['HTTPS'] = 'off';


if (!defined('BASE_URI')) {

    define(

        'BASE_URI',

        '/assist/public'

    );

}


$response = Response::redirect(

    'dashboard'

);


if ($response->status() !== 302) {

    throw new RuntimeException(

        'Redirect response status failed.'

    );

}


if (
    !isset($response->headers()['Location'])
) {

    throw new RuntimeException(

        'Redirect response location missing.'

    );

}


/*
|--------------------------------------------------------------------------
| Error Response
|--------------------------------------------------------------------------
*/

$response = Response::error(

    404,

    'Resource not found'

);


if ($response->status() !== 404) {

    throw new RuntimeException(

        'Error response status failed.'

    );

}


if (
    $response->content()
    !==
    'Resource not found'
) {

    throw new RuntimeException(

        'Error response content failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Empty Response
|--------------------------------------------------------------------------
*/

$response = new Response();


if ($response->status() !== 200) {

    throw new RuntimeException(

        'Default response status failed.'

    );

}


if ($response->content() !== '') {

    throw new RuntimeException(

        'Default response content failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

echo 'Response tests passed successfully.'

    . PHP_EOL;