<?php
require_once __DIR__ . '/app/core/Database.php';

try {
    $cnx = Database::getInstance()->getConnection();
    
    // ID de l'utilisateur à qui affecter les work orders (Exemple : utilisateur avec ID 2)
    $targetUserId = 2; 
    
    // IDs des work orders à affecter (Exemple : les work orders 1, 4, 5)
    $workOrderIdsToAssign = [1, 4, 5]; 

    // Assurez-vous que l'utilisateur cible existe (facultatif mais recommandé)
    $checkUserQuery = "SELECT id FROM users WHERE id = :user_id";
    $checkUserStmt = $cnx->prepare($checkUserQuery);
    $checkUserStmt->execute(['user_id' => $targetUserId]);
    $userExists = $checkUserStmt->fetch(PDO::FETCH_ASSOC);

    if (!$userExists) {
        echo "Erreur : L'utilisateur avec l'ID {$targetUserId} n'existe pas dans la table users.\n";
        echo "Veuillez créer un utilisateur avec cet ID ou changer le \$targetUserId dans le script.\n";
        exit;
    }

    // Préparer la requête UPDATE pour affecter les work orders
    // Créer les placeholders pour la clause IN
    $placeholders = rtrim(str_repeat('?, ', count($workOrderIdsToAssign)), ', ');
    
    $updateQuery = "UPDATE `work-orders` SET user_id = ? WHERE id IN ({$placeholders})";
    $updateStmt = $cnx->prepare($updateQuery);
    
    // Exécuter la requête avec l'ID utilisateur et les IDs des work orders
    $params = array_merge([$targetUserId], $workOrderIdsToAssign);
    $success = $updateStmt->execute($params);
    
    if ($success) {
        echo "Work orders avec IDs " . implode(', ', $workOrderIdsToAssign) . " affectés à l'utilisateur ID {$targetUserId} avec succès.\n";
    } else {
        echo "Erreur lors de l'affectation des work orders.\n";
    }
    
    // Vérification (facultatif)
    echo "\nVérification des work orders affectés à l'utilisateur ID {$targetUserId} :\n";
    $verifyQuery = "SELECT id, numero, user_id FROM `work-orders` WHERE user_id = ? ORDER BY id";
    $verifyStmt = $cnx->prepare($verifyQuery);
    $verifyStmt->execute([$targetUserId]);
    $affectedWorkOrders = $verifyStmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($affectedWorkOrders) > 0) {
        foreach ($affectedWorkOrders as $wo) {
            echo "- WO ID: " . $wo['id'] . ", Numero: " . $wo['numero'] . ", User ID: " . $wo['user_id'] . "\n";
        }
    } else {
        echo "Aucun work order trouvé affecté à cet utilisateur (ce qui n'est pas attendu si l'affectation a réussi).\n";
    }

} catch(PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage() . "\n";
} catch(Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
} 