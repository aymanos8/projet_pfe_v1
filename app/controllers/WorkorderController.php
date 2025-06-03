<?php
require_once __DIR__ . '/../models/WorkOrder.php';
require_once __DIR__ . '/../models/Equipement.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/AuthController.php'; // Assurez-vous que AuthController est chargé
require_once __DIR__ . '/../models/Utilisateur.php'; // Inclure le modèle Utilisateur
require_once __DIR__ . '/../models/HistoriqueAction.php'; // Inclure le modèle HistoriqueAction

class WorkorderController {
    public function syncWorkOrders() {
        try {
            $cnx = Database::getInstance()->getConnection();
            $workOrderModel = new WorkOrder($cnx);

            $url = "https://dev299646.service-now.com/api/now/table/wm_order";
            $username = "admin";
            $password = "N9@uSi@WW5za";

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json"]);

            $response = curl_exec($ch);
            if ($response === false) {
                throw new Exception("Erreur cURL : " . curl_error($ch));
            }
            curl_close($ch);

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
                $date = $wo['opened_at'] ? date('Y-m-d H:i:s', strtotime($wo['opened_at'])) : date('Y-m-d H:i:s');
                $short_description = $wo['short_description'] ?? null;
                $debit = $wo['u_debit'] ?? null;

                $workOrderModel->save($numero, $client, $technology, $offre, null, $date, $short_description, $debit);
                $count++;
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
        // Déclencher la synchronisation avec ServiceNow
        $this->syncWorkOrders();

        require_once __DIR__ . '/../core/Database.php';
        require_once __DIR__ . '/../models/WorkOrder.php';
        require_once __DIR__ . '/AuthController.php';
        require_once __DIR__ . '/../models/Utilisateur.php'; // Inclure le modèle Utilisateur

        $cnx = Database::getInstance()->getConnection();
        $workOrderModel = new WorkOrder($cnx);
        $userModel = new Utilisateur($cnx); // Instancier le modèle Utilisateur

        $users = []; // Initialiser le tableau des utilisateurs

        if (AuthController::isLoggedIn()) {
            $userRole = AuthController::getUserRole();
            $userId = AuthController::getUserId();

            if ($userRole === 'admin') {
                // L'admin voit tous les work orders
                $workOrders = $workOrderModel->getAll();
                // Récupérer tous les utilisateurs pour l'admin
                $users = $userModel->getAllUsersOnly();
            } else {
                // Un utilisateur classique ne voit que ses work orders affectés
                $workOrders = $workOrderModel->getByUserId($userId);
            }
        } else {
            // Si non connecté, afficher une page vide ou rediriger
            $workOrders = [];
        }

        require __DIR__ . '/../views/all_workorders.php';
    }

    public function detail($id) {
        $cnx = Database::getInstance()->getConnection();
        $workOrderModel = new WorkOrder($cnx);
        $equipementModel = new Equipement($cnx);
        
        $workorder = $workOrderModel->getById($id);
        $equipements = $equipementModel->getEquipementsByWorkOrder($id);

        // Récupérer les équipements disponibles compatibles, en incluant le débit requis
        $requiredDebit = $workorder['debit'] ?? ''; // Récupérer le débit du work order

        echo "<!-- Debug: Critères de compatibilité pour WO #" . htmlspecialchars($workorder['numero']) . " -->\n";
        echo "<!-- Debug: Tech requise: " . htmlspecialchars($workorder['technology'] ?? '') . " -->\n";
        echo "<!-- Debug: Offre requise: " . htmlspecialchars($workorder['offre'] ?? '') . " -->\n";
        echo "<!-- Debug: Débit requis: " . htmlspecialchars($requiredDebit) . " -->\n";

        $equipementsDisponiblesCompatibles = $equipementModel->getAvailableCompatibleEquipements(
            $workorder['technology'] ?? '',
            $workorder['offre'] ?? '',
            $requiredDebit // Passer le débit requis
        );

        // Passer l'ID et le rôle de l'utilisateur connecté à la vue
        $userId = AuthController::getUserId();
        $userRole = AuthController::getUserRole();

        require __DIR__ . '/../views/workorder_detail.php';
    }

    public function affecterEquipement() {
        error_log("DEBUG: affecterEquipement method called.");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $work_order_id = $_POST['work_order_id'] ?? null;
            $equipement_id = $_POST['equipement_id'] ?? null;

            error_log("DEBUG: POST request received.");
            error_log("DEBUG: work_order_id = " . $work_order_id);
            error_log("DEBUG: equipement_id = " . $equipement_id);

            if (!$work_order_id || !$equipement_id) {
                error_log("DEBUG: Missing work_order_id or equipement_id.");
                $_SESSION['error'] = "Données manquantes pour l'affectation.";
                header('Location: /projet-pfe-v1/projet-t1/public/workorder_detail/' . $work_order_id);
                exit;
            }

            $database = Database::getInstance();
            $equipementModel = new Equipement($database->getConnection());
            $historiqueModel = new HistoriqueAction($database); // Instancier le modèle HistoriqueAction
            $userId = AuthController::getUserId(); // Récupérer l'ID de l'utilisateur connecté

            // Démarrer une transaction pour affectation et historique
            $database->getConnection()->beginTransaction();

            try {
                if ($equipementModel->affecterEquipement($work_order_id, $equipement_id)) {
                    // Enregistrer l'action dans l'historique
                    $historiqueModel->addAction(
                        $userId,
                        'affectation',
                        'equipement',
                        $equipement_id,
                        "Affectation au Work Order #" . $work_order_id
                    );
                    $_SESSION['success'] = "Équipement affecté avec succès.";
                     $database->getConnection()->commit(); // Committer la transaction
                } else {
                    $_SESSION['error'] = "Erreur lors de l'affectation de l'équipement.";
                     $database->getConnection()->rollBack(); // Annuler la transaction
                }
            } catch (Exception $e) {
                 $database->getConnection()->rollBack(); // Annuler la transaction en cas d'exception
                 $_SESSION['error'] = "Erreur lors de l'affectation de l'équipement : " . $e->getMessage();
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

            $database = Database::getInstance();
            $equipementModel = new Equipement($database->getConnection());
            $historiqueModel = new HistoriqueAction($database); // Instancier le modèle HistoriqueAction
            $userId = AuthController::getUserId(); // Récupérer l'ID de l'utilisateur connecté

            // Démarrer une transaction
            $database->getConnection()->beginTransaction();

            try {
                // Supprimer l'affectation (Utiliser le modèle Equipement ou faire la requête ici)
                 // Assuming EquipementModel::desaffecterEquipement exists and handles DB operations
                 if ($equipementModel->desaffecterEquipement($work_order_id, $equipement_id)) {
                    // Enregistrer l'action dans l'historique
                    $historiqueModel->addAction(
                        $userId,
                        'desaffectation',
                        'equipement',
                        $equipement_id,
                        "Désaffectation du Work Order #" . $work_order_id
                    );
                     $database->getConnection()->commit(); // Committer la transaction
                     $_SESSION['success'] = "Équipement désaffecté avec succès.";
                 } else {
                     $database->getConnection()->rollBack(); // Annuler la transaction
                     $_SESSION['error'] = "Erreur lors de la désaffectation de l'équipement.";
                 }

            } catch (Exception $e) {
                $database->getConnection()->rollBack();
                $_SESSION['error'] = "Erreur lors de la désaffectation de l'équipement : " . $e->getMessage();
            }

            header('Location: /projet-pfe-v1/projet-t1/public/workorder_detail/' . $work_order_id);
            exit;
            

        }
    }

    // Nouvelle méthode pour affecter un work order à un utilisateur
    public function affecterWorkOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $work_order_id = $_POST['work_order_id'] ?? null;
            $user_id = $_POST['user_id'] ?? null;

            // Vérifier que l'utilisateur connecté est un admin
            if (!AuthController::isLoggedIn() || AuthController::getUserRole() !== 'admin') {
                $_SESSION['error'] = "Accès non autorisé.";
                header('Location: /projet-pfe-v1/projet-t1/public/workorders');
                exit;
            }

            if (!$work_order_id || !$user_id) {
                $_SESSION['error'] = "Données manquantes pour l'affectation du work order.";
                header('Location: /projet-pfe-v1/projet-t1/public/workorders');
                exit;
            }

            $database = Database::getInstance();
            $workOrderModel = new WorkOrder($database->getConnection()); // Assurez-vous que WorkOrder prend la connexion
            $historiqueModel = new HistoriqueAction($database); // Instancier le modèle HistoriqueAction
            $currentUserId = AuthController::getUserId(); // Récupérer l'ID de l'utilisateur connecté qui effectue l'action

            // Démarrer une transaction
            $database->getConnection()->beginTransaction();

            try {
                // Appeler une méthode dans le modèle WorkOrder pour affecter l'utilisateur
                // Supposons que la méthode affectUser existe dans WorkOrder et gère les opérations DB
                if ($workOrderModel->affectUser($work_order_id, $user_id)) {
                     // Enregistrer l'action dans l'historique
                    $historiqueModel->addAction(
                        $currentUserId,
                        'affectation',
                        'workorder',
                        $work_order_id,
                        "Affectation à l'utilisateur #" . $user_id
                    );

                    $database->getConnection()->commit();
                    $_SESSION['success'] = "Work order affecté à l'utilisateur avec succès.";
                } else {
                    $database->getConnection()->rollBack();
                    $_SESSION['error'] = "Erreur lors de l'affectation du work order à l'utilisateur.";
                }

            } catch (Exception $e) {
                 $database->getConnection()->rollBack();
                 $_SESSION['error'] = "Erreur lors de l'affectation du work order à l'utilisateur : " . $e->getMessage();
            }

            // Rediriger vers la page des work orders après l'affectation
            header('Location: /projet-pfe-v1/projet-t1/public/workorders');
            exit;
        }
    }

    // Nouvelle méthode pour marquer un work order comme terminé
    public function completeWorkOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $work_order_id = $_POST['work_order_id'] ?? null;

            if (!$work_order_id) {
                $_SESSION['error'] = "ID du work order manquant pour terminer.";
                header('Location: /projet-pfe-v1/projet-t1/public/workorders'); // Rediriger vers la liste ou la page de détail
                exit;
            }

            $cnx = Database::getInstance()->getConnection();
            $workOrderModel = new WorkOrder($cnx);
            
            // Vérifier si l'utilisateur connecté est l'affecté ou un admin
            $workorder = $workOrderModel->getById($work_order_id);
            $userId = AuthController::getUserId();
            $userRole = AuthController::getUserRole();

            if ($workorder && ($workorder['user_id'] == $userId || $userRole === 'admin')) {
                // Mettre à jour le statut à 'Terminé' (3)
                if ($workOrderModel->updateStatus($work_order_id, '3')) {
                    // Mettre également à jour le statut des équipements associés à 'en_stock' si nécessaire (selon la logique métier)
                    // ... (logique pour les équipements si applicable)

                    $_SESSION['success'] = "Work order marqué comme terminé.";
                } else {
                    $_SESSION['error'] = "Erreur lors de la mise à jour du statut du work order.";
                }
            } else {
                $_SESSION['error'] = "Vous n'êtes pas autorisé à terminer ce work order.";
            }
            
            // Rediriger vers la page de détail du work order ou la liste
            if ($work_order_id) {
                 header('Location: /projet-pfe-v1/projet-t1/public/workorder_detail/' . $work_order_id);
            } else {
                 header('Location: /projet-pfe-v1/projet-t1/public/workorders');
            }
            exit;
        }
    }
}
