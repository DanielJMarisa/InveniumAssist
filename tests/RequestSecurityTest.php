<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Http\Request;


/*
|--------------------------------------------------------------------------
| Preserve Global State
|--------------------------------------------------------------------------
*/

$originalServer = $_SERVER;


/*
|--------------------------------------------------------------------------
| HTTPS Detection
|--------------------------------------------------------------------------
*/

$_SERVER = [];

if (Request::secure()) {

    throw new RuntimeException(
        'Request incorrectly detected HTTPS.'
    );
}


$_SERVER['HTTPS'] = 'on';

if (!Request::secure()) {

    throw new RuntimeException(
        'HTTPS detection failed.'
    );
}


/*
|--------------------------------------------------------------------------
| HTTPS Off
|--------------------------------------------------------------------------
*/

$_SERVER['HTTPS'] = 'off';

if (Request::secure()) {

    throw new RuntimeException(
        'HTTPS "off" was incorrectly detected as secure.'
    );
}


/*
|--------------------------------------------------------------------------
| Forwarded HTTPS
|--------------------------------------------------------------------------
*/

$_SERVER = [
    'HTTP_X_FORWARDED_PROTO' => 'https',
];

if (!Request::secure()) {

    throw new RuntimeException(
        'Forwarded HTTPS detection failed.'
    );
}


/*
|--------------------------------------------------------------------------
| Client IP
|--------------------------------------------------------------------------
*/

$_SERVER = [
    'REMOTE_ADDR' => '192.168.1.50',
];

if (Request::ip() !== '192.168.1.50') {

    throw new RuntimeException(
        'Remote address detection failed.'
    );
}


/*
|--------------------------------------------------------------------------
| Cloudflare Connecting IP
|--------------------------------------------------------------------------
*/

$_SERVER = [
    'REMOTE_ADDR' => '192.168.1.50',
    'HTTP_CF_CONNECTING_IP' => '203.0.113.10',
];

if (Request::ip() !== '203.0.113.10') {

    throw new RuntimeException(
        'Cloudflare client IP detection failed.'
    );
}


/*
|--------------------------------------------------------------------------
| Forwarded For
|--------------------------------------------------------------------------
*/

$_SERVER = [
    'REMOTE_ADDR' => '192.168.1.50',
    'HTTP_X_FORWARDED_FOR' => '203.0.113.20, 192.168.1.50',
];

if (Request::ip() !== '203.0.113.20') {

    throw new RuntimeException(
        'X-Forwarded-For client IP detection failed.'
    );
}


/*
|--------------------------------------------------------------------------
| JSON
|--------------------------------------------------------------------------
*/

$_SERVER = [];

$originalInput = file_get_contents(
    'php://input'
);


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

$_SERVER = $originalServer;

echo 'Request security tests passed successfully.'
    . PHP_EOL;