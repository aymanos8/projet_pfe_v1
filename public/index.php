<?php
// public/index.php

use PSpell\Config;

require_once '../app/core/Router.php';
require_once '../app/controllers/ConfigController.php';
require_once '../app/controllers/WorkorderController.php';
require_once '../app/controllers/EquipementController.php';
require_once '../app/controllers/DashboardController.php';
require_once '../app/controllers/AuthController.php';
require_once '../app/controllers/StatisticsController.php';
// require_once '../app/config/config.php'; // Inclure le fichier de configuration

$router = new Router();

// Routes GET
// Rediriger la page d'accueil vers le formulaire de connexion si non authentifié
$router->get('/', function() {
    if (!AuthController::isLoggedIn()) {
        header('Location: /projet-pfe-v1/projet-t1/public/login');
        exit;
    } else {
        // Rediriger selon le rôle si déjà connecté
        if (AuthController::getUserRole() === 'admin') {
            header('Location: /projet-pfe-v1/projet-t1/public/dashboard');
        } else {
            header('Location: /projet-pfe-v1/projet-t1/public/workorders');
        }
        exit;
    }
});

$router->get('/login', [AuthController::class, 'showLoginForm']);
$router->get('/register', [AuthController::class, 'showRegisterForm']);
$router->get('/user/{id}', [ConfigController::class, 'show']);
$router->get('/equipements', [EquipementController::class, 'index']);
$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/workorders', [WorkorderController::class, 'index']);
$router->get('/workorder_detail/{id}', [WorkorderController::class, 'detail']);
$router->get('/configuration', [ConfigController::class, 'index']);
$router->get('/configuration/{id}', [ConfigController::class, 'index']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/statistics', [StatisticsController::class, 'index']);

// Routes POST
$router->post('/user/create', [ConfigController::class, 'create']);
$router->post('/configuration/generer', [ConfigController::class, 'generer']);
$router->post('/workorder/affecter-equipement', [WorkorderController::class, 'affecterEquipement']);
$router->post('/workorder/desaffecter-equipement', [WorkorderController::class, 'desaffecterEquipement']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/register', [AuthController::class, 'register']);
$router->post('/workorder/affecter-utilisateur', [WorkorderController::class, 'affecterWorkOrder']);
$router->post('/workorder/complete', [WorkorderController::class, 'completeWorkOrder']);

// Exécution du routeur
$router->run();
