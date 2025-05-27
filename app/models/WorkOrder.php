<?php
// require_once __DIR__ . '/../config/database.php';

class WorkOrder
{
    private $cnx;
    public function __construct($cnx)
    {
        $this->cnx = $cnx;
    }

    public function save($numero, $client, $technology, $offre, $status, $date, $short_description = null)
    {
        try {
            // Vérifier si le work order existe déjà
            $check = $this->cnx->prepare("SELECT * FROM `work-orders` WHERE numero = ?");
            $check->execute([$numero]);
            
            if ($check->rowCount() > 0) {
                // Mise à jour si existe
                $stmt = $this->cnx->prepare("UPDATE `work-orders` SET client = ?, technology = ?, offre = ?, status = ?, date = ?, short_description = ? WHERE numero = ?");
                return $stmt->execute([$client, $technology, $offre, $status, $date, $short_description, $numero]);
            } else {
                // Insertion si n'existe pas
                $stmt = $this->cnx->prepare("INSERT INTO `work-orders` (numero, client, technology, offre, status, date, short_description) VALUES (?, ?, ?, ?, ?, ?, ?)");
                return $stmt->execute([$numero, $client, $technology, $offre, $status, $date, $short_description]);
            }
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la sauvegarde du work order : " . $e->getMessage());
        }
    }
    
    public function getAll()
    {
        try {
            // Jointure avec la table users pour récupérer le nom de l'utilisateur affecté
            $query = "SELECT wo.*, u.username as assigned_username 
                      FROM `work-orders` wo
                      LEFT JOIN users u ON wo.user_id = u.id
                      ORDER BY wo.date DESC";
            $stmt = $this->cnx->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des work orders : " . $e->getMessage());
        }
    }

    public function countByStatus($status)
    {
        try {
            $stmt = $this->cnx->prepare("SELECT COUNT(*) as count FROM `work-orders` WHERE status = ?");
            $stmt->execute([$status]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du comptage des work orders : " . $e->getMessage());
        }
    }

    public function countAll()
    {
        try {
            $stmt = $this->cnx->query("SELECT COUNT(*) as count FROM `work-orders`");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du comptage des work orders : " . $e->getMessage());
        }
    }

    public function getAllNumbers()
    {
        $stmt = $this->cnx->query("SELECT numero FROM `work-orders`");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function deleteByNumber($numero)
    {
        $stmt = $this->cnx->prepare("DELETE FROM `work-orders` WHERE numero = ?");
        return $stmt->execute([$numero]);
    }

    public function getById($id)
    {
        try {
            $stmt = $this->cnx->prepare("SELECT * FROM `work-orders` WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération du work order : " . $e->getMessage());
        }
    }

    /**
     * Met à jour le statut d'un work order.
     *
     * @param int $workOrderId L'ID du work order.
     * @param string $status Le nouveau statut (e.g., '1', '2', '3').
     * @return bool Retourne true en cas de succès, false sinon.
     */
    public function updateStatus($workOrderId, $status) {
        try {
            $query = "UPDATE `work-orders` SET status = :status WHERE id = :work_order_id";
            $stmt = $this->cnx->prepare($query);
            return $stmt->execute([
                'status' => $status,
                'work_order_id' => $workOrderId
            ]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du statut du work order : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Affecte un work order à un utilisateur spécifique.
     *
     * @param int $workOrderId L'ID du work order.
     * @param int $userId L'ID de l'utilisateur.
     * @return bool Retourne true en cas de succès, false sinon.
     */
    public function affectUser($workOrderId, $userId) {
        try {
            // Mettre à jour le user_id et le statut à 'En Cours' (2)
            $query = "UPDATE `work-orders` SET user_id = :user_id, status = '2' WHERE id = :work_order_id";
            $stmt = $this->cnx->prepare($query);
            return $stmt->execute([
                'user_id' => $userId,
                'work_order_id' => $workOrderId
            ]);
        } catch (PDOException $e) {
            // Gérer l'erreur ou la logguer
            error_log("Erreur lors de l'affectation du work order à l'utilisateur : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère tous les work orders affectés à un utilisateur spécifique.
     *
     * @param int $userId L'ID de l'utilisateur.
     * @return array Retourne un tableau des work orders affectés.
     */
    public function getByUserId($userId) {
        $query = "SELECT * FROM `work-orders` WHERE user_id = :user_id ORDER BY date DESC";
        $stmt = $this->cnx->prepare($query);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>