<?php

// require_once __DIR__ . '/../core/Controller.php'; // Commenté car Controller.php n'est pas accessible
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/WorkOrder.php';
require_once __DIR__ . '/../models/Equipement.php';
require_once __DIR__ . '/../models/Utilisateur.php';
require_once __DIR__ . '/AuthController.php'; // Inclure AuthController pour la vérification de rôle

class StatisticsController // Ne plus étendre Controller pour l'instant
{
    private $db;
    private $workOrderModel;
    private $equipementModel;
    private $utilisateurModel;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->workOrderModel = new WorkOrder($this->db);
        $this->equipementModel = new Equipement($this->db);
        $this->utilisateurModel = new Utilisateur($this->db);
    }

    // Méthode view simple pour inclure les fichiers de vue
    protected function view($view, $data = [])
    {
        $file = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($file)) {
            extract($data);
            require $file;
        } else {
            // Gérer l'erreur si la vue n'existe pas
            die('La vue ' . $view . ' n\'existe pas.');
        }
    }

    public function index()
    {
        // Vérifier si l'utilisateur est connecté et est un admin
        if (!AuthController::isLoggedIn() || AuthController::getUserRole() !== 'admin') {
            // Rediriger vers une page d'erreur ou le tableau de bord non-admin
            header('Location: /projet-pfe-v1/projet-t1/public/dashboard'); // ou une page d'erreur 403
            exit;
        }

        // Statistiques des work orders
        $totalWorkOrders = $this->workOrderModel->countAll();
        $workOrdersByStatus = [
            'en_attente' => $this->workOrderModel->countByStatus('1'),
            'en_cours' => $this->workOrderModel->countByStatus('2'),
            'termine' => $this->workOrderModel->countByStatus('3')
        ];

        // Work orders par période
        $workOrdersToday = $this->getWorkOrdersByPeriod('today');
        $workOrdersThisWeek = $this->getWorkOrdersByPeriod('week');
        $workOrdersThisMonth = $this->getWorkOrdersByPeriod('month');

        // Statistiques des équipements
        $equipements = $this->equipementModel->getAllEquipements();
        $equipementsByStatus = [
            'disponible' => 0,
            'en_service' => 0,
            'maintenance' => 0
        ];
        foreach ($equipements as $equipement) {
            $equipementsByStatus[$equipement['statut']]++;
        }

        // Statistiques des utilisateurs
        $totalUsers = $this->utilisateurModel->countAll();
        $usersByRole = $this->getUsersByRole();

        // Work orders par technologie
        $workOrdersByTechnology = $this->getWorkOrdersByTechnology();

        // Évolution des work orders
        $workOrdersEvolution = $this->getWorkOrdersEvolution();

        // Équipements par marque
        $equipementsByMarque = $this->getEquipementsByMarque();

        // Temps moyen de traitement
        $avgTreatmentTime = $this->getAverageTreatmentTime();

        // Passer les données à la vue
        $data = [
            'title' => 'Statistiques',
            'totalWorkOrders' => $totalWorkOrders,
            'workOrdersByStatus' => $workOrdersByStatus,
            'workOrdersToday' => $workOrdersToday,
            'workOrdersThisWeek' => $workOrdersThisWeek,
            'workOrdersThisMonth' => $workOrdersThisMonth,
            'equipementsByStatus' => $equipementsByStatus,
            'totalUsers' => $totalUsers,
            'usersByRole' => $usersByRole,
            'workOrdersByTechnology' => $workOrdersByTechnology,
            'workOrdersEvolution' => $workOrdersEvolution,
            'equipementsByMarque' => $equipementsByMarque,
            'avgTreatmentTime' => $avgTreatmentTime
        ];

        // Inclure la vue des statistiques
        $this->view('statistics', $data);
    }

    private function getWorkOrdersByPeriod($period) {
        $query = "SELECT COUNT(*) as count FROM `work-orders` WHERE ";
        switch ($period) {
            case 'today':
                $query .= "DATE(date) = CURDATE()";
                break;
            case 'week':
                $query .= "YEARWEEK(date) = YEARWEEK(CURDATE())";
                break;
            case 'month':
                $query .= "YEAR(date) = YEAR(CURDATE()) AND MONTH(date) = MONTH(CURDATE())";
                break;
        }
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    private function getUsersByRole() {
        $query = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getWorkOrdersByTechnology() {
        $query = "SELECT technology, COUNT(*) as count 
                 FROM `work-orders` 
                 GROUP BY technology";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getWorkOrdersEvolution() {
        $query = "SELECT DATE(date) as date, COUNT(*) as count 
                 FROM `work-orders` 
                 GROUP BY DATE(date) 
                 ORDER BY date DESC 
                 LIMIT 30";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getEquipementsByMarque() {
        $query = "SELECT marque, COUNT(*) as count 
                 FROM equipements_reseau 
                 GROUP BY marque";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getAverageTreatmentTime() {
        $query = "SELECT AVG(TIMESTAMPDIFF(HOUR, date, 
                 CASE 
                    WHEN status = '3' THEN date 
                    ELSE NOW() 
                 END)) as avg_time 
                 FROM `work-orders` 
                 WHERE status IN ('2', '3')";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return round($result['avg_time'], 1);
    }
} 