<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Http\Request;


/*
|--------------------------------------------------------------------------
| Preserve Server State
|--------------------------------------------------------------------------
*/

$originalServer = $_SERVER;


/*
|--------------------------------------------------------------------------
| Method
|--------------------------------------------------------------------------
*/

$_SERVER['REQUEST_METHOD'] = 'post';

if (Request::method() !== 'POST') {

    throw new RuntimeException(
        'Request method detection failed.'
    );
}

if (!Request::isPost()) {

    throw new RuntimeException(
        'Request::isPost() failed.'
    );
}

if (Request::isGet()) {

    throw new RuntimeException(
        'Request::isGet() incorrectly returned true.'
    );
}


/*
|--------------------------------------------------------------------------
| GET / POST Input
|--------------------------------------------------------------------------
*/

$_GET = [
    'search' => 'remote',
];

$_POST = [
    'name' => 'Test User',
    'active' => '1',
];


if (Request::get()['search'] !== 'remote') {

    throw new RuntimeException(
        'GET input retrieval failed.'
    );
}

if (Request::post()['name'] !== 'Test User') {

    throw new RuntimeException(
        'POST input retrieval failed.'
    );
}


/*
|--------------------------------------------------------------------------
| Input Priority
|--------------------------------------------------------------------------
*/

if (Request::input('name') !== 'Test User') {

    throw new RuntimeException(
        'POST input priority failed.'
    );
}

if (Request::input('search') !== 'remote') {

    throw new RuntimeException(
        'GET input fallback failed.'
    );
}

if (Request::input('missing', 'fallback') !== 'fallback') {

    throw new RuntimeException(
        'Input default value failed.'
    );
}


/*
|--------------------------------------------------------------------------
| AJAX
|--------------------------------------------------------------------------
*/

$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

if (!Request::isAjax()) {

    throw new RuntimeException(
        'AJAX detection failed.'
    );
}


/*
|--------------------------------------------------------------------------
| User Agent
|--------------------------------------------------------------------------
*/

$_SERVER['HTTP_USER_AGENT'] = 'Invenium-Test-Agent';

if (Request::userAgent() !== 'Invenium-Test-Agent') {

    throw new RuntimeException(
        'User-Agent retrieval failed.'
    );
}


/*
|--------------------------------------------------------------------------
| Referer
|--------------------------------------------------------------------------
*/

$_SERVER['HTTP_REFERER'] = 'https://example.test/source';

if (Request::referer() !== 'https://example.test/source') {

    throw new RuntimeException(
        'Referer retrieval failed.'
    );
}


/*
|--------------------------------------------------------------------------
| Files
|--------------------------------------------------------------------------
*/

$_FILES = [
    'document' => [
        'name' => 'test.txt',
    ],
];

if (
    Request::files()['document']['name']
    !==
    'test.txt'
) {

    throw new RuntimeException(
        'Uploaded files retrieval failed.'
    );
}


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

$_SERVER = $originalServer;

echo 'Request tests passed successfully.'
    . PHP_EOL;