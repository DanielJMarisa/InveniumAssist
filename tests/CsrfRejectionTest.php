<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';


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
| Create Temporary Test Script
|--------------------------------------------------------------------------
*/

$script = <<<'PHP'
<?php

declare(strict_types=1);

require_once __AUTOLOAD_PATH__;

use Core\Http\Request;
use Core\Middleware\CsrfMiddleware;
use Core\Session\Session;

$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/csrf-rejection';

$_POST = [];
$_GET = [];

Session::start();

Session::flush();

$middleware = new CsrfMiddleware();

$request = new Request();

$middleware->handle(
    $request,
    function (): string {
        return 'should-not-execute';
    }
);
PHP;


/*
|--------------------------------------------------------------------------
| Inject Absolute Autoloader Path
|--------------------------------------------------------------------------
*/

$script = str_replace(

    '__AUTOLOAD_PATH__',

    var_export(

        $autoloadPath,

        true

    ),

    $script

);


/*
|--------------------------------------------------------------------------
| Create Temporary File
|--------------------------------------------------------------------------
*/

$tempFile = tempnam(

    sys_get_temp_dir(),

    'invenium_csrf_'

);


if ($tempFile === false) {

    throw new RuntimeException(
        'Unable to create temporary CSRF test file.'
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
        'Unable to write temporary CSRF test file.'
    );

}


/*
|--------------------------------------------------------------------------
| Execute Child PHP Process
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| Cleanup
|--------------------------------------------------------------------------
*/

@unlink($tempFile);


/*
|--------------------------------------------------------------------------
| Verify Middleware Rejected Request
|--------------------------------------------------------------------------
*/

if ($exitCode === 0) {

    throw new RuntimeException(
        'CSRF middleware unexpectedly allowed a POST request without a token.'
    );

}


/*
|--------------------------------------------------------------------------
| Verify Expected Response
|--------------------------------------------------------------------------
*/

$combinedOutput = implode(

    PHP_EOL,

    $output

);


if (
    !str_contains(
        $combinedOutput,
        'Page expired. Please refresh and try again.'
    )
) {

    throw new RuntimeException(

        'CSRF rejection did not produce the expected response message.'
        . PHP_EOL
        . $combinedOutput

    );

}


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

echo 'CSRF rejection tests passed successfully.'
    . PHP_EOL;