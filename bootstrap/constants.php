<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Application Information
|--------------------------------------------------------------------------
*/

define('APP_NAME', 'Invenium Assist');

define('APP_VERSION', '1.0.0');

/*
|--------------------------------------------------------------------------
| Directory Paths
|--------------------------------------------------------------------------
*/
define('DS', DIRECTORY_SEPARATOR);

define('BASE_PATH', realpath(__DIR__ . '/..'));

define('APP_PATH', BASE_PATH . DS . 'app');

define('CONFIG_PATH', BASE_PATH . DS . 'config');

define('CORE_PATH', BASE_PATH . DS . 'core');

define('MODULE_PATH', BASE_PATH . DS . 'modules');

define('TEMPLATE_PATH', BASE_PATH . DS . 'templates');

define('STORAGE_PATH', BASE_PATH . DS . 'storage');

define('PUBLIC_PATH', BASE_PATH . DS . 'public');

define('ROUTES_PATH', BASE_PATH . DS . 'routes');

/*
|--------------------------------------------------------------------------
| Storage
|--------------------------------------------------------------------------
*/

define('LOG_PATH', STORAGE_PATH . '/logs');

define('CACHE_PATH', STORAGE_PATH . '/cache');

define('UPLOAD_PATH', STORAGE_PATH . '/uploads');

define('TEMP_PATH', STORAGE_PATH . '/temp');

/*
|--------------------------------------------------------------------------
| Environment
|--------------------------------------------------------------------------
*/

define('ENVIRONMENT', 'development');