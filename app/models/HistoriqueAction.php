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

    /**
     * Récupère l'historique des actions avec des filtres combinés.
     *
     * @param string $dateDebut Date de début (YYYY-MM-DD).
     * @param string $dateFin Date de fin (YYYY-MM-DD).
     * @param string|null $entiteType Type de l'entité (e.g., 'workorder', 'equipement').
     * @param int|null $entiteId ID de l'entité.
     * @param int|null $userId ID de l'utilisateur.
     *
     * @return array L'historique des actions.
     */
    public function getFilteredHistorique($dateDebut, $dateFin, $entiteType = null, $entiteId = null, $userId = null) {
        $sql = "SELECT h.*, u.nom, u.prenom, u.username 
                FROM historique_actions h 
                JOIN users u ON h.user_id = u.id ";
        
        $conditions = [];
        $params = [];

        // Ajouter la condition de date par défaut
        $conditions[] = "DATE(h.date_action) BETWEEN :date_debut AND :date_fin";
        $params[':date_debut'] = $dateDebut;
        $params[':date_fin'] = $dateFin;

        if ($entiteType !== null && $entiteType !== '') {
            $conditions[] = "h.entite_type = :entite_type";
            $params[':entite_type'] = $entiteType;
        }

        if ($entiteId !== null) {
            $conditions[] = "h.entite_id = :entite_id";
            $params[':entite_id'] = $entiteId;
        }

        if ($userId !== null && $userId !== '') {
            $conditions[] = "h.user_id = :user_id";
            $params[':user_id'] = $userId;
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY h.date_action DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 