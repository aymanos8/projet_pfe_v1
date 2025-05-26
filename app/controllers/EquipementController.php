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
            $query = "SELECT * FROM equipements_reseau ORDER BY id DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // En cas d'erreur, retourner un tableau vide
            error_log("Erreur lors de la récupération des équipements: " . $e->getMessage());
            return [];
        }
    }

    public function ajouterEquipement($modele, $marque, $type_interfaces, $capacite, $numero_serie) {
        try {
            $query = "INSERT INTO equipements_reseau (modele, marque, type_interfaces, capacite, numero_serie) VALUES (:modele, :marque, :type_interfaces, :capacite, :numero_serie)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'modele' => $modele,
                'marque' => $marque,
                'type_interfaces' => $type_interfaces,
                'capacite' => $capacite,
                'numero_serie' => $numero_serie
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Erreur lors de l'ajout d'un équipement: " . $e->getMessage());
            return false;
        }
    }
} 