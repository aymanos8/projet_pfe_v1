<?php

class HistoriqueAction {
    private $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    public function addAction($userId, $actionType, $entiteType, $entiteId, $details = null) {
        $sql = "INSERT INTO historique_actions (user_id, action_type, entite_type, entite_id, details) 
                VALUES (:user_id, :action_type, :entite_type, :entite_id, :details)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':action_type' => $actionType,
            ':entite_type' => $entiteType,
            ':entite_id' => $entiteId,
            ':details' => $details
        ]);
    }

    public function getHistoriqueByEntite($entiteType, $entiteId) {
        $sql = "SELECT h.*, u.nom, u.prenom 
                FROM historique_actions h 
                JOIN users u ON h.user_id = u.id 
                WHERE h.entite_type = :entite_type 
                AND h.entite_id = :entite_id 
                ORDER BY h.date_action DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':entite_type' => $entiteType,
            ':entite_id' => $entiteId
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getHistoriqueByUser($userId) {
        $sql = "SELECT h.*, u.nom, u.prenom 
                FROM historique_actions h 
                JOIN users u ON h.user_id = u.id 
                WHERE h.user_id = :user_id 
                ORDER BY h.date_action DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getHistoriqueByDateRange($dateDebut, $dateFin) {
        $sql = "SELECT h.*, u.nom, u.prenom 
                FROM historique_actions h 
                JOIN users u ON h.user_id = u.id 
                WHERE h.date_action BETWEEN :date_debut AND :date_fin 
                ORDER BY h.date_action DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':date_debut' => $dateDebut,
            ':date_fin' => $dateFin
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 