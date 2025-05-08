<?php
require_once __DIR__ . '/app/controllers/WorkOrderController.php';

// Créer une instance du contrôleur
$controller = new WorkorderController();

// Exécuter la synchronisation
$result = $controller->syncWorkOrders();

// Afficher le résultat
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT); 