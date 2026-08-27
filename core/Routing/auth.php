<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

use Modules\Auth\AuthController;

$router->get('/login', [

    AuthController::class,

    'login'

]);

$router->post('/login', [

    AuthController::class,

    'authenticate'

]);

$router->post('/logout', [

    AuthController::class,

    'logout'

]);