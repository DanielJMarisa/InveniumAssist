<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Http\Response;


/*
|--------------------------------------------------------------------------
| Header Mutation
|--------------------------------------------------------------------------
*/

$response = Response::make(

    'Hello',

    200

);


$result = $response->header(

    'X-Test',

    'invenium'

);


if ($result !== $response) {

    throw new RuntimeException(

        'Response::header() did not return the response instance.'

    );

}


if (
    $response->headers()['X-Test']
    !==
    'invenium'
) {

    throw new RuntimeException(

        'Response header mutation failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Header Replacement
|--------------------------------------------------------------------------
*/

$response->header(

    'X-Test',

    'updated'

);


if (
    $response->headers()['X-Test']
    !==
    'updated'
) {

    throw new RuntimeException(

        'Response header replacement failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Status Mutation
|--------------------------------------------------------------------------
*/

$result = $response->withStatus(

    201

);


if ($result !== $response) {

    throw new RuntimeException(

        'Response::withStatus() did not return the response instance.'

    );

}


if ($response->status() !== 201) {

    throw new RuntimeException(

        'Response status mutation failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Chained Mutation
|--------------------------------------------------------------------------
*/

$response
    ->header(
        'X-Powered-By',
        'Invenium Assist'
    )
    ->withStatus(202)
    ->header(
        'X-Test-Chain',
        'passed'
    );


if ($response->status() !== 202) {

    throw new RuntimeException(

        'Response mutation chaining failed.'

    );

}


if (
    $response->headers()['X-Test-Chain']
    !==
    'passed'
) {

    throw new RuntimeException(

        'Response chained header mutation failed.'

    );

}


/*
|--------------------------------------------------------------------------
| JSON Response
|--------------------------------------------------------------------------
*/

$json = Response::json(

    [
        'success' => true,
        'message' => 'OK',
    ],

    200

);


if ($json->status() !== 200) {

    throw new RuntimeException(

        'JSON response status failed.'

    );

}


if (
    $json->headers()['Content-Type']
    !==
    'application/json; charset=UTF-8'
) {

    throw new RuntimeException(

        'JSON response content type failed.'

    );

}


$decoded = json_decode(

    $json->content(),

    true

);


if (
    !is_array($decoded)
    ||
    $decoded['success'] !== true
    ||
    $decoded['message'] !== 'OK'
) {

    throw new RuntimeException(

        'JSON response encoding failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Redirect Response
|--------------------------------------------------------------------------
*/

if (!defined('BASE_URI')) {

    define(

        'BASE_URI',

        '/assist'

    );

}


$_SERVER['HTTP_HOST'] = 'localhost';

$_SERVER['HTTPS'] = 'on';


$redirect = Response::redirect(

    '/login'

);


if ($redirect->status() !== 302) {

    throw new RuntimeException(

        'Redirect response status failed.'

    );

}


if (
    $redirect->headers()['Location']
    !==
    'https://localhost/assist/login'
) {

    throw new RuntimeException(

        'Redirect response Location header failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

echo 'Response mutation tests passed successfully.'
    . PHP_EOL;