<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Composer Autoloader
|--------------------------------------------------------------------------
*/

$autoload = BASE_PATH . DS . 'vendor' . DS . 'autoload.php';

if (!file_exists($autoload)) {

    throw new RuntimeException(

        'Composer autoloader not found. Run "composer install".'

    );

}

require_once $autoload;