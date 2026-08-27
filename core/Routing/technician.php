<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Technician Routes
|--------------------------------------------------------------------------
*/

use Modules\Technicians\TechnicianController;

$router->get('/technicians', [

    TechnicianController::class,

    'index'

]);

$router->get('/technicians/online', [

    TechnicianController::class,

    'online'

]);

$router->get('/technicians/profile', [

    TechnicianController::class,

    'profile'

]);