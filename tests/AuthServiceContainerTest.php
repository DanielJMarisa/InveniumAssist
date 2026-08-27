<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/bootstrap.php';

use Core\Container\Container;
use Modules\Auth\AuthRepository;
use Modules\Auth\AuthService;
use Modules\Auth\AuthValidator;

/*
|--------------------------------------------------------------------------
| Create Container
|--------------------------------------------------------------------------
*/

$container = new Container();


/*
|--------------------------------------------------------------------------
| Resolve AuthService
|--------------------------------------------------------------------------
*/

$service = $container->make(

    AuthService::class

);


/*
|--------------------------------------------------------------------------
| Verify AuthService
|--------------------------------------------------------------------------
*/

if (!$service instanceof AuthService) {

    throw new RuntimeException(

        'Container failed to resolve AuthService.'

    );

}


/*
|--------------------------------------------------------------------------
| Verify AuthRepository Resolution
|--------------------------------------------------------------------------
*/

$repository = $container->make(

    AuthRepository::class

);


if (!$repository instanceof AuthRepository) {

    throw new RuntimeException(

        'Container failed to resolve AuthRepository.'

    );

}


/*
|--------------------------------------------------------------------------
| Verify AuthValidator Resolution
|--------------------------------------------------------------------------
*/

$validator = $container->make(

    AuthValidator::class

);


if (!$validator instanceof AuthValidator) {

    throw new RuntimeException(

        'Container failed to resolve AuthValidator.'

    );

}


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

echo "AuthService container tests passed successfully."

    . PHP_EOL;
