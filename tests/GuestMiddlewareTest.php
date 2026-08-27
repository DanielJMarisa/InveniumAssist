<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Http\Request;
use Core\Middleware\GuestMiddleware;
use Core\Session\Session;


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
| Test Guest Request
|--------------------------------------------------------------------------
*/

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/login';

$_POST = [];
$_GET = [];

Session::start();
Session::flush();

$middleware = new GuestMiddleware();

$request = new Request();

$executed = false;

$result = $middleware->handle(

    $request,

    function () use (&$executed): string {

        $executed = true;

        return 'guest';

    }

);

if (!$executed) {

    throw new RuntimeException(
        'Guest request did not reach the next middleware.'
    );

}

if ($result !== 'guest') {

    throw new RuntimeException(
        'Guest middleware returned an unexpected result.'
    );

}


/*
|--------------------------------------------------------------------------
| Test Authenticated Redirect
|--------------------------------------------------------------------------
|
| The redirect helper terminates the current process, so this
| scenario is executed in a separate PHP process.
|
*/

$script = <<<'PHP'
<?php

declare(strict_types=1);

require_once __AUTOLOAD_PATH__;

use Core\Http\Request;
use Core\Middleware\GuestMiddleware;
use Core\Session\Session;

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/login';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTPS'] = 'off';

$_POST = [];
$_GET = [];

if (!defined('BASE_URI')) {

    define(
        'BASE_URI',
        '/assist/public'
    );

}

Session::start();

Session::flush();

Session::put(
    'auth.user_id',
    1
);

$middleware = new GuestMiddleware();

$request = new Request();

$middleware->handle(

    $request,

    function (): string {

        return 'should-not-execute';

    }

);

echo 'should-not-execute';
PHP;


/*
|--------------------------------------------------------------------------
| Inject Autoloader
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
| Create Temporary Script
|--------------------------------------------------------------------------
*/

$tempFile = tempnam(

    sys_get_temp_dir(),

    'invenium_guest_'

);

if ($tempFile === false) {

    throw new RuntimeException(
        'Unable to create temporary Guest middleware test.'
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
        'Unable to write temporary Guest middleware test.'
    );

}


/*
|--------------------------------------------------------------------------
| Execute Child Process
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

@unlink($tempFile);


/*
|--------------------------------------------------------------------------
| Verify Redirect Terminated Pipeline
|--------------------------------------------------------------------------
*/

$combinedOutput = implode(
    PHP_EOL,
    $output
);

if (str_contains(
    $combinedOutput,
    'should-not-execute'
)) {

    throw new RuntimeException(
        'Guest middleware allowed an authenticated request to continue.'
    );

}


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

Session::flush();

echo 'Guest middleware tests passed successfully.'
    . PHP_EOL;