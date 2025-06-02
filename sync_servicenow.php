<?php

require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/models/WorkOrder.php';
require_once __DIR__ . '/app/models/Equipement.php';

echo "Début de la synchronisation avec ServiceNow...\n";

try {
    $cnx = Database::getInstance()->getConnection();
    $workOrderModel = new WorkOrder($cnx);
    $equipementModel = new Equipement($cnx);

    $url = "https://dev299646.service-now.com/api/now/table/wm_order";
    $username = "admin";
    $password = "N9@uSi@WW5za"; // Assurez-vous que ces informations sensibles sont gérées de manière sécurisée en production

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retourne la réponse
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password"); // Authentification
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json"]); // Accepte le format JSON

    $response = curl_exec($ch);
    if ($response === false) {
        throw new Exception("Erreur cURL : " . curl_error($ch));
    }
    curl_close($ch);

    $data = json_decode($response, true);
    if (!isset($data['result'])) {
        throw new Exception("Format de réponse invalide de l'API ServiceNow.");
    }

    // 1. Récupérer tous les numéros de work orders ServiceNow
    $serviceNowNumbers = [];
    if (!empty($data['result'])) {
        foreach ($data['result'] as $wo) {
            if (isset($wo['number'])) {
                $serviceNowNumbers[] = $wo['number'];
            }
        }
    }

    // 2. Récupérer tous les numéros de work orders en BDD
    $bddNumbers = $workOrderModel->getAllNumbers();

    // 3. Supprimer ceux qui ne sont plus dans ServiceNow
    $toDelete = array_diff($bddNumbers, $serviceNowNumbers);
    echo "\n" . count($toDelete) . " Work Orders à supprimer de la BDD...\n";
    foreach ($toDelete as $num) {
        if ($workOrderModel->deleteByNumber($num)) {
             echo "- Supprimé : " . $num . "\n";
        } else {
             echo "- Erreur lors de la suppression : " . $num . "\n";
        }
    }

    // 4. Insérer ou mettre à jour les work orders reçus
    $count_saved = 0;
    $count_affected_equipement = 0;
    echo "\nMise à jour/Insertion des Work Orders...\n";

    if (!empty($data['result'])) {
         foreach ($data['result'] as $wo) {
            if (!isset($wo['number']) || !isset($wo['u_client'])) {
                echo "- Saut du WO (données manquantes) : " . ($wo['number'] ?? 'N/A') . "\n";
                continue;
            }
            $numero = $wo['number'];
            $client = $wo['u_client'] ?? '';
            $technology = $wo['u_technologie'] ?? '';
            $offre = $wo['u_offre'] ?? '';
            $status = $wo['state'] ?? '1';
            $date = $wo['opened_at'] ? date('Y-m-d H:i:s', strtotime($wo['opened_at'])) : date('Y-m-d H:i:s');
            $short_description = $wo['short_description'] ?? null;
            
            if ($workOrderModel->save($numero, $client, $technology, $offre, $status, $date, $short_description)) {
                echo "- Sauvegardé : " . $numero . "\n";
                $count_saved++;

                // Tenter d'affecter automatiquement un équipement après sauvegarde/mise à jour
                // Récupérer le work order depuis la base de données pour avoir son ID
                $workOrder = $workOrderModel->getByNumber($numero);

                if ($workOrder) {
                     // Vérifier si le work order a déjà un équipement affecté
                    $equipements = $equipementModel->getEquipementsByWorkOrder($workOrder['id']);
                    if (empty($equipements)) {
                         // Tenter d'affecter un équipement compatible
                        $equipementCompatible = $equipementModel->getEquipementCompatible(
                            $technology,
                            $offre
                        );

                        if ($equipementCompatible) {
                            if ($equipementModel->affecterEquipement($workOrder['id'], $equipementCompatible['id'])) {
                                echo "  - Affectation automatique de l'équipement " . $equipementCompatible['modele'] . " au WO " . $numero . "\n";
                                $count_affected_equipement++;
                            } else {
                                echo "  - Échec de l'affectation automatique de l'équipement au WO " . $numero . "\n";
                            }
                        }
                    }
                }

            } else {
                echo "- Erreur lors de la sauvegarde : " . $numero . "\n";
            }
        }
    }

    echo "\nSynchronisation terminée.\n";
    echo "Work Orders sauvegardés/mis à jour : " . $count_saved . "\n";
    echo "Équipements affectés automatiquement : " . $count_affected_equipement . "\n";

} catch (Exception $e) {
    echo "\nErreur critique lors de la synchronisation : " . $e->getMessage() . "\n";
    exit(1); // Indiquer une erreur
}

exit(0); // Indiquer le succès
?> 