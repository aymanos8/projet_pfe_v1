<?php
// public/index.php

use PSpell\Config;

require_once '../app/core/Router.php';
require_once '../app/controllers/ConfigController.php';
require_once '../app/controllers/WorkorderController.php';

$router = new Router();

// Routes GET
$router->get('/', [WorkorderController::class, 'index']);
$router->get('/user/{id}', [ConfigController::class, 'show']);

// Routes POST
$router->post('/user/create', [ConfigController::class, 'create']);

// Exécution du routeur
$router->run();
