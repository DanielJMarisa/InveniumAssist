<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Base URI
|--------------------------------------------------------------------------
|
| The application is served from the public directory.
| This works for both Apache requests and CLI tests.
|
*/

if (PHP_SAPI === 'cli') {

    define(
        'BASE_URI',
        '/invenium%20remote%20assist/assist/public'
    );

} else {

    $scriptName = str_replace(
        '\\',
        '/',
        $_SERVER['SCRIPT_NAME'] ?? ''
    );

    $baseUri = dirname($scriptName);

    if ($baseUri === '/' || $baseUri === '\\' || $baseUri === '.') {

        $baseUri = '';

    }

    define(
        'BASE_URI',
        rtrim($baseUri, '/')
    );
}