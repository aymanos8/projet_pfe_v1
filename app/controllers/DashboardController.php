<?php
require_once __DIR__ . '/../models/WorkOrder.php';
require_once __DIR__ . '/../config/database.php';

class DashboardController {
    public function getWorkOrders() {
        try {
            $cnx = getConnection();
            $model = new WorkOrder($cnx);
            return $model->getAll();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getStatistics() {
        try {
            $cnx = getConnection();
            $model = new WorkOrder($cnx);
            
            return [
                'pending' => $model->countByStatus('1'),
                'in_progress' => $model->countByStatus('2'),
                'completed' => $model->countByStatus('3'),
                'total' => $model->countAll()
            ];
        } catch (Exception $e) {
            return [
                'pending' => 0,
                'in_progress' => 0,
                'completed' => 0,
                'total' => 0
            ];
        }
    }

    public function index() {
        require __DIR__ . '/../views/dashboard.php';
    }
} 