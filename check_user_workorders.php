<?php
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/models/WorkOrder.php';

try {
    $cnx = Database::getInstance()->getConnection();
    $workOrderModel = new WorkOrder($cnx);
    
    // ID de l'utilisateur à vérifier (Exemple : utilisateur avec ID 2)
    $targetUserId = 2; 
    
    echo "Work orders affectés à l'utilisateur ID {$targetUserId} :
";
    $workOrders = $workOrderModel->getByUserId($targetUserId);

    if (count($workOrders) > 0) {
        foreach ($workOrders as $wo) {
            echo "- ID: " . $wo['id'] . ", Numéro: " . $wo['numero'] . ", User ID: " . $wo['user_id'] . "
";
        }
    } else {
        echo "Aucun work order trouvé affecté à cet utilisateur.
";
    }

} catch(PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage() . "
";
} catch(Exception $e) {
    echo "Erreur : " . $e->getMessage() . "
";
} 
?> 