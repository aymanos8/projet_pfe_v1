<?php
// public/index.php

use PSpell\Config;

require_once '../app/core/Router.php';
require_once '../app/controllers/ConfigController.php';
require_once '../app/controllers/WorkorderController.php';
require_once '../app/controllers/EquipementController.php';
require_once '../app/controllers/DashboardController.php';

$router = new Router();

// Routes GET
$router->get('/', [WorkorderController::class, 'index']);
$router->get('/user/{id}', [ConfigController::class, 'show']);
$router->get('/equipements', [EquipementController::class, 'index']);
$router->get('/dashboard', [DashboardController::class, 'index']);

// Routes POST
$router->post('/user/create', [ConfigController::class, 'create']);

// Exécution du routeur
$router->run();
