<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Http\Response;
use Core\Kernel\Application;


/*
|--------------------------------------------------------------------------
| Application
|--------------------------------------------------------------------------
*/

$app = new Application();


/*
|--------------------------------------------------------------------------
| Route
|--------------------------------------------------------------------------
*/

$app->router()->get(

    '/lifecycle',

    function (): string {

        return 'lifecycle-ok';

    }

);


/*
|--------------------------------------------------------------------------
| Dispatch Through Application Router
|--------------------------------------------------------------------------
*/

$result = $app->router()->dispatch(

    'GET',

    '/lifecycle'

);


if ($result !== 'lifecycle-ok') {

    throw new RuntimeException(

        'Application router lifecycle failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Container
|--------------------------------------------------------------------------
*/

if (
    $app->container()->make(
        Application::class
    )
    !==
    $app
) {

    throw new RuntimeException(

        'Application was not registered in the container.'

    );

}


/*
|--------------------------------------------------------------------------
| Router
|--------------------------------------------------------------------------
*/

if (
    $app->container()->make(
        Core\Routing\Router::class
    )
    !==
    $app->router()
) {

    throw new RuntimeException(

        'Router was not registered correctly.'

    );

}


/*
|--------------------------------------------------------------------------
| Test Response
|--------------------------------------------------------------------------
*/

$response = Response::json(

    [
        'success' => true
    ]

);


if ($response->status() !== 200) {

    throw new RuntimeException(

        'Application lifecycle response failed.'

    );

}


/*
|--------------------------------------------------------------------------
| Test Complete
|--------------------------------------------------------------------------
*/

echo 'Application lifecycle tests passed successfully.'
    . PHP_EOL;