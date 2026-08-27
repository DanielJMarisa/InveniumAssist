<?php

declare(strict_types=1);

use Core\Exceptions\Handler;

/*
|--------------------------------------------------------------------------
| Invenium Assist Bootstrap
|--------------------------------------------------------------------------
|
| Initializes the framework.
| Every public entry point should include this file.
|
*/

use Core\Config\Config;

/*
|--------------------------------------------------------------------------
| Load Constants
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/constants.php';

/*
|--------------------------------------------------------------------------
| Load Composer Autoloader
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/autoload.php';

/*
|--------------------------------------------------------------------------
| Initialize Environment
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/init.php';

/*
|--------------------------------------------------------------------------
| Load Helper Functions
|--------------------------------------------------------------------------
*/

$helpers = __DIR__ . DS . 'helpers.php';

if (file_exists($helpers)) {

    require_once $helpers;

}

/*
|--------------------------------------------------------------------------
| Load Configuration
|--------------------------------------------------------------------------
*/

Config::load(CONFIG_PATH);

/*
|--------------------------------------------------------------------------
| Configure PHP
|--------------------------------------------------------------------------
*/

date_default_timezone_set(

    Config::get('app.timezone', 'UTC')

);

ini_set(

    'default_charset',

    'UTF-8'

);

mb_internal_encoding(

    'UTF-8'

);

/*
|--------------------------------------------------------------------------
| Error Reporting
|--------------------------------------------------------------------------
*/

if (Config::get('app.debug', false)) {

    error_reporting(E_ALL);

    ini_set('display_errors', '1');

}
else {

    error_reporting(E_ALL);

    ini_set('display_errors', '0');

}

Handler::register();

/*
|--------------------------------------------------------------------------
| Start Secure Session
|--------------------------------------------------------------------------
*/

if (session_status() !== PHP_SESSION_ACTIVE) {

    session_set_cookie_params([

        'lifetime' => 0,

        'path' => '/',

        'secure' => (

            !empty($_SERVER['HTTPS'])

            &&

            $_SERVER['HTTPS'] !== 'off'

        ),

        'httponly' => true,

        'samesite' => 'Lax'

    ]);

    session_start();

}

/*
|--------------------------------------------------------------------------
| Framework Ready
|--------------------------------------------------------------------------
*/

define('APP_BOOTSTRAPPED', true);