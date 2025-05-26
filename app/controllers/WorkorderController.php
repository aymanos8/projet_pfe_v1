<?php
require_once __DIR__ . '/../models/WorkOrder.php';
require_once __DIR__ . '/../models/Equipement.php';
require_once __DIR__ . '/../core/Database.php';

class WorkorderController {
    public function syncWorkOrders() {
        try {
            $cnx = Database::getInstance()->getConnection();
            $workOrderModel = new WorkOrder($cnx);
            $equipementModel = new Equipement($cnx);

            $url = "https://dev299646.service-now.com/api/now/table/wm_order";
            $username = "admin";
            $password = "N9@uSi@WW5za";

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retourne la réponse
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password"); // Authentification
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json"]); // Ac  cepte le format JSON

            $response = curl_exec($ch);
            if ($response === false) {
                throw new Exception("Erreur cURL : " . curl_error($ch));
            }
            curl_close($ch);

            // Debug : écrire la réponse brute dans un fichier
            file_put_contents(__DIR__ . '/../../debug_api_response.txt', $response);

            $data = json_decode($response, true);
            if (!isset($data['result'])) {
                throw new Exception("Format de réponse invalide");
            }

            // 1. Récupérer tous les numéros de work orders ServiceNow
            $serviceNowNumbers = [];
            foreach ($data['result'] as $wo) {
                if (isset($wo['number'])) {
                    $serviceNowNumbers[] = $wo['number'];
                }
            }

            // 2. Récupérer tous les numéros de work orders en BDD
            $bddNumbers = $workOrderModel->getAllNumbers();

            // 3. Supprimer ceux qui ne sont plus dans ServiceNow
            $toDelete = array_diff($bddNumbers, $serviceNowNumbers);
            foreach ($toDelete as $num) {
                $workOrderModel->deleteByNumber($num);
            }

            // 4. Insérer ou mettre à jour les work orders reçus
            $count = 0;
            foreach ($data['result'] as $wo) {
                if (!isset($wo['number']) || !isset($wo['u_client'])) {
                    continue;
                }
                $numero = $wo['number'];
                $client = $wo['u_client'] ?? '';
                $technology = $wo['u_technologie'] ?? '';
                $offre = $wo['u_offre'] ?? '';
                $status = $wo['state'] ?? '1';
                $date = $wo['opened_at'] ?? date('Y-m-d H:i:s');
                $short_description = $wo['short_description'] ?? null;
                $workOrderModel->save($numero, $client, $technology, $offre, $status, $date, $short_description);
                $count++;
            }

            // Après la synchronisation, tenter d'affecter automatiquement des équipements
            foreach ($data['result'] as $wo) {
                if (!isset($wo['number']) || !isset($wo['u_client'])) {
                    continue;
                }

                // Récupérer le work order depuis la base de données
                $workOrder = $workOrderModel->getByNumber($wo['number']);
                if (!$workOrder) {
                    continue;
                }

                // Vérifier si le work order a déjà un équipement affecté
                $equipements = $equipementModel->getEquipementsByWorkOrder($workOrder['id']);
                if (!empty($equipements)) {
                    continue;
                }

                // Tenter d'affecter un équipement compatible
                $equipementCompatible = $equipementModel->getEquipementCompatible(
                    $wo['u_technologie'] ?? '',
                    $wo['u_offre'] ?? ''
                );

                if ($equipementCompatible) {
                    $equipementModel->affecterEquipement($workOrder['id'], $equipementCompatible['id']);
                }
            }

            return [
                'success' => true,
                'message' => "$count Work Orders synchronisés avec succès."
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "Erreur lors de la synchronisation : " . $e->getMessage()
            ];
        }
    }

    public function index() {
        require __DIR__ . '/../views/all_workorders.php';
    }

    public function detail($id) {
        $cnx = Database::getInstance()->getConnection();
        $workOrderModel = new WorkOrder($cnx);
        $equipementModel = new Equipement($cnx);
        
        $workorder = $workOrderModel->getById($id);
        $equipements = $equipementModel->getEquipementsByWorkOrder($id);
        $equipementsDisponibles = $equipementModel->getEquipementsDisponibles();
        
        require __DIR__ . '/../views/workorder_detail.php';
    }

    public function affecterEquipement() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $work_order_id = $_POST['work_order_id'] ?? null;
            $equipement_id = $_POST['equipement_id'] ?? null;

            if (!$work_order_id || !$equipement_id) {
                $_SESSION['error'] = "Données manquantes pour l'affectation.";
                header('Location: /projet-pfe-v1/projet-t1/public/workorder_detail/' . $work_order_id);
                exit;
            }

            $cnx = Database::getInstance()->getConnection();
            $equipementModel = new Equipement($cnx);

            if ($equipementModel->affecterEquipement($work_order_id, $equipement_id)) {
                $_SESSION['success'] = "Équipement affecté avec succès.";
            } else {
                $_SESSION['error'] = "Erreur lors de l'affectation de l'équipement.";
            }

            header('Location: /projet-pfe-v1/projet-t1/public/workorder_detail/' . $work_order_id);
            exit;
        }
    }

    public function desaffecterEquipement() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $work_order_id = $_POST['work_order_id'] ?? null;
            $equipement_id = $_POST['equipement_id'] ?? null;

            if (!$work_order_id || !$equipement_id) {
                $_SESSION['error'] = "Données manquantes pour la désaffectation.";
                header('Location: /projet-pfe-v1/projet-t1/public/workorder_detail/' . $work_order_id);
                exit;
            }

            $cnx = Database::getInstance()->getConnection();
            $equipementModel = new Equipement($cnx);

            // Démarrer une transaction
            $cnx->beginTransaction();

            try {
                // Supprimer l'affectation
                $queryAffectation = "DELETE FROM affectations_workorders 
                                   WHERE work_order_id = :work_order_id 
                                   AND equipement_id = :equipement_id";
                $stmtAffectation = $cnx->prepare($queryAffectation);
                $stmtAffectation->execute([
                    'work_order_id' => $work_order_id,
                    'equipement_id' => $equipement_id
                ]);

                // Mettre à jour le statut de l'équipement à 'disponible'
                $queryEquipement = "UPDATE equipements_reseau SET statut = 'disponible' WHERE id = :id";
                $stmtEquipement = $cnx->prepare($queryEquipement);
                $stmtEquipement->execute(['id' => $equipement_id]);

                $cnx->commit();
                $_SESSION['success'] = "Équipement désaffecté avec succès.";

            } catch (Exception $e) {
                $cnx->rollBack();
                $_SESSION['error'] = "Erreur lors de la désaffectation de l'équipement : " . $e->getMessage();
            }

            header('Location: /projet-pfe-v1/projet-t1/public/workorder_detail/' . $work_order_id);
            exit;
        }
    }
}
