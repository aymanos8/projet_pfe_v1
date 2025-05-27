<?php
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/models/WorkOrder.php';

try {
    $cnx = Database::getInstance()->getConnection();
    $workOrderModel = new WorkOrder($cnx);

    // Données du nouveau work order (modifiez ces valeurs si nécessaire)
    $numero = 'WO-' . date('Ymd') . '-' . uniqid(); // Génère un numéro unique basé sur la date et un identifiant unique
    $client = 'Nouveau Client';
    $technology = 'Fibre';
    $offre = 'Offre Standard';
    $status = '1'; // Statut 'En Attente' initial
    $date = date('Y-m-d H:i:s'); // Date et heure actuelles
    $short_description = 'Installation de service pour nouveau client.';

    echo "Tentative d'ajout d'un nouveau work order...\n";

    // Appeler la méthode save pour insérer le work order
    if ($workOrderModel->save($numero, $client, $technology, $offre, $status, $date, $short_description)) {
        echo "Work order '{$numero}' ajouté avec succès.\n";
    } else {
        echo "Échec de l'ajout du work order.\n";
    }

} catch(PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage() . "\n";
} catch(Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
?> 