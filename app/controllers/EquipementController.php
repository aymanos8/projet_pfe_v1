<?php
require_once __DIR__ . '/../core/Database.php';

class EquipementController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function index() {
        // Ici, on pourrait charger les équipements depuis le modèle
        // require_once '../app/models/Equipement.php';
        // $equipements = Equipement::getAll();
        // Pour l'instant, on affiche juste la vue
        require_once __DIR__ . '/../views/equipements.php';
    }

    public function getEquipements() {
        try {
            $query = "SELECT * FROM equipements_eseau ORDER BY id DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // En cas d'erreur, retourner un tableau vide
            error_log("Erreur lors de la récupération des équipements: " . $e->getMessage());
            return [];
        }
    }
} 