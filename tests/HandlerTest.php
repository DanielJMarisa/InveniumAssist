<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Exceptions\Handler;
use Core\Exceptions\HttpException;


/*
|--------------------------------------------------------------------------
| Project Paths
|--------------------------------------------------------------------------
*/

$autoloadPath = realpath(
    __DIR__ . '/../vendor/autoload.php'
);

if ($autoloadPath === false) {
    throw new RuntimeException(
        'Unable to locate Composer autoloader.'
    );
}


/*
|--------------------------------------------------------------------------
| Temporary Test Runner
|--------------------------------------------------------------------------
*/

function runHandlerTest(
    string $autoloadPath,
    string $requestUri,
    string $environment,
    string $exceptionCode
): array {
    $script = <<<'PHP'
<?php

declare(strict_types=1);

require_once __AUTOLOAD_PATH__;

use Core\Exceptions\Handler;
use Core\Exceptions\HttpException;

$_SERVER['REQUEST_URI'] = __REQUEST_URI__;

if (__ENVIRONMENT__ !== '') {
    define('ENVIRONMENT', __ENVIRONMENT__);
}

switch (__EXCEPTION_CODE__) {

    case 'http404':

        Handler::handle(
            new HttpException(
                404,
                'Secret internal message'
            )
        );

        break;

    case 'http403':

        Handler::handle(
            new HttpException(
                403,
                'Forbidden internal message'
            )
        );

        break;

    default:

        Handler::handle(
            new RuntimeException(
                'Unexpected internal failure'
            )
        );
}

PHP;

    $script = str_replace(
        '__AUTOLOAD_PATH__',
        var_export($autoloadPath, true),
        $script
    );

    $script = str_replace(
        '__REQUEST_URI__',
        var_export($requestUri, true),
        $script
    );

    $script = str_replace(
        '__ENVIRONMENT__',
        var_export($environment, true),
        $script
    );

    $script = str_replace(
        '__EXCEPTION_CODE__',
        var_export($exceptionCode, true),
        $script
    );

    $tempFile = tempnam(
        sys_get_temp_dir(),
        'invenium_handler_'
    );

    if ($tempFile === false) {
        throw new RuntimeException(
            'Unable to create temporary Handler test file.'
        );
    }

    if (
        file_put_contents(
            $tempFile,
            $script
        ) === false
    ) {
        @unlink($tempFile);

        throw new RuntimeException(
            'Unable to write temporary Handler test file.'
        );
    }

    $output = [];

    $exitCode = 0;

    $command = escapeshellarg(PHP_BINARY)
        . ' '
        . escapeshellarg($tempFile);

    exec(
        $command . ' 2>&1',
        $output,
        $exitCode
    );

    @unlink($tempFile);

    return [
        'output' => implode(PHP_EOL, $output),
        'exitCode' => $exitCode
    ];
}


/*
|--------------------------------------------------------------------------
| Production HTML Exception
|--------------------------------------------------------------------------
*/

$result = runHandlerTest(
    $autoloadPath,
    '/',
    'production',
    'http404'
);

if (
    !str_contains(
        $result['output'],
        'Page not found.'
    )
) {
    throw new RuntimeException(
        'Handler production HTML response message failed.'
        . PHP_EOL
        . $result['output']
    );
}

if (
    str_contains(
        $result['output'],
        'Secret internal message'
    )
) {
    throw new RuntimeException(
        'Handler exposed an internal exception message in production.'
        . PHP_EOL
        . $result['output']
    );
}


/*
|--------------------------------------------------------------------------
| Production API Exception
|--------------------------------------------------------------------------
*/

$result = runHandlerTest(
    $autoloadPath,
    '/api/test',
    'production',
    'http403'
);

if (
    !str_contains(
        $result['output'],
        'Access denied.'
    )
) {
    throw new RuntimeException(
        'Handler production API response failed.'
        . PHP_EOL
        . $result['output']
    );
}

if (
    !str_contains(
        $result['output'],
        '"success":false'
    )
) {
    throw new RuntimeException(
        'Handler API response did not contain the expected success flag.'
        . PHP_EOL
        . $result['output']
    );
}


/*
|--------------------------------------------------------------------------
| Development Exception
|--------------------------------------------------------------------------
*/

$result = runHandlerTest(
    $autoloadPath,
    '/',
    'development',
    'http404'
);

if (
    !str_contains(
        $result['output'],
        'HttpException'
    )
) {
    throw new RuntimeException(
        'Handler development response did not expose exception type.'
        . PHP_EOL
        . $result['output']
    );
}

if (
    !str_contains(
        $result['output'],
        'Secret internal message'
    )
) {
    throw new RuntimeException(
        'Handler development response did not expose debug message.'
        . PHP_EOL
        . $result['output']
    );
}


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

echo 'Handler tests passed successfully.'
    . PHP_EOL;