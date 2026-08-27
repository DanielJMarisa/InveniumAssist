<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Public application routes.
|
*/

use Modules\Dashboard\DashboardController;

$router->get('/', [

    DashboardController::class,

    'index'

]);