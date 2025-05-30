<?php
require_once __DIR__ . '/../models/WorkOrder.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Utilisateur.php';

class DashboardController {
    public function index() {
        session_start();

        $userId = $_SESSION['user_id'] ?? null;
        $is_admin = $_SESSION['is_admin'] ?? false;
        $username = $_SESSION['username'] ?? 'Invité';

        if ($userId === null && !$is_admin) {
            header('Location: /projet-pfe-v1/projet-t1/public/login');
            exit();
        }

        try {
            $cnx = Database::getInstance()->getConnection();
            $workOrderModel = new WorkOrder($cnx);

            if ($is_admin) {
                $workOrders = $workOrderModel->getAll();
                $stats = [
                    'pending' => $workOrderModel->countByStatus('1'),
                    'in_progress' => $workOrderModel->countByStatus('2'),
                    'completed' => $workOrderModel->countByStatus('3'),
                    'total' => $workOrderModel->countAll()
                ];
                $utilisateurModel = new Utilisateur($cnx);
                $users = $utilisateurModel->getAllUsersOnly();
            } else {
                $workOrders = $workOrderModel->getByUserId($userId);
                $stats = [
                    'pending' => $workOrderModel->countByStatusAndUserId('1', $userId),
                    'in_progress' => $workOrderModel->countByStatusAndUserId('2', $userId),
                    'completed' => $workOrderModel->countByStatusAndUserId('3', $userId),
                    'total' => $workOrderModel->countByUserId($userId)
                ];
                $users = [];
            }
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des données du tableau de bord : " . $e->getMessage());
            $workOrders = [];
            $stats = [
                'pending' => 0,
                'in_progress' => 0,
                'completed' => 0,
                'total' => 0
            ];
            $users = [];
            $_SESSION['error'] = "Erreur lors du chargement du tableau de bord.";
        }

        require __DIR__ . '/../views/dashboard.php';
    }
} 