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
            $stmt = $this->cnx->query("SELECT * FROM `work-orders` ORDER BY date DESC");
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
}
?>