<?php
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../models/Equipement.php';

class EquipementController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function index() {
        $equipements = $this->getEquipements();
        require_once __DIR__ . '/../views/equipements.php';
    }

    public function getEquipements() {
        try {
            $query = "SELECT * FROM equipements_reseau ORDER BY id DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des équipements: " . $e->getMessage());
            return [];
        }
    }

    public function showAddForm() {
        if (!AuthController::isLoggedIn() || AuthController::getUserRole() !== 'admin') {
            header('Location: /projet-pfe-v1/projet-t1/public/equipements');
            exit;
        }
        require_once __DIR__ . '/../views/add_equipement.php';
    }

    public function ajouterEquipement() {
        if (!AuthController::isLoggedIn() || AuthController::getUserRole() !== 'admin') {
            header('Location: /projet-pfe-v1/projet-t1/public/equipements');
            exit;
        }

        // Récupérer les données du formulaire
        $modele = $_POST['modele'] ?? null;
        $marque = $_POST['marque'] ?? null;
        $gamme = $_POST['gamme'] ?? null;
        $technology_arr = $_POST['technology'] ?? [];
        $offre_arr = $_POST['offre'] ?? [];
        $debit = $_POST['debit'] ?? null;
        $statut = $_POST['statut'] ?? 'disponible'; // Statut par défaut

        // Gérer les champs multi-sélection
        $technology = implode(',', $technology_arr);
        $offre = implode(',', $offre_arr);

        // Valider les champs requis (modele, marque sont requis par le formulaire)
        if (!$modele || !$marque) {
            // Gérer l'erreur, par exemple rediriger avec un message
            header('Location: /projet-pfe-v1/projet-t1/public/equipements/ajouter?error=required_fields_missing');
            exit;
        }

        try {
            // Vérifier si un équipement avec le même modèle existe déjà
            $check_query = "SELECT COUNT(*) FROM equipements_reseau WHERE modele = :modele";
            $check_stmt = $this->db->prepare($check_query);
            $check_stmt->execute(['modele' => $modele]);
            $count = $check_stmt->fetchColumn();

            if ($count > 0) {
                // Si l'équipement existe déjà, rediriger avec un message d'erreur
                header('Location: /projet-pfe-v1/projet-t1/public/equipements/ajouter?error=equipement_exists');
                exit;
            }

            // Si l'équipement n'existe pas, procéder à l'insertion
            $query = "INSERT INTO equipements_reseau (modele, marque, gamme, technology, offre, debit, statut) VALUES (:modele, :marque, :gamme, :technology, :offre, :debit, :statut)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'modele' => $modele,
                'marque' => $marque,
                'gamme' => $gamme,
                'technology' => $technology,
                'offre' => $offre,
                'debit' => $debit,
                'statut' => $statut
            ]);

            // Rediriger après succès
            header('Location: /projet-pfe-v1/projet-t1/public/equipements?success=equipement_added');
            exit;

        } catch (PDOException $e) {
            error_log("Erreur lors de l'ajout d'un équipement: " . $e->getMessage());
            // Gérer l'erreur, par exemple rediriger avec un message
            header('Location: /projet-pfe-v1/projet-t1/public/equipements/ajouter?error=db_error');
            exit;
        }
    }

    public function setIndisponible($id) {
        // Vérifier si l'utilisateur est un admin
        if (!AuthController::isLoggedIn() || AuthController::getUserRole() !== 'admin') {
            header('Location: /projet-pfe-v1/projet-t1/public/equipements');
            exit;
        }

        // Valider l'ID (vous pourriez vouloir ajouter une validation plus robuste)
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            // Gérer l'erreur si l'ID n'est pas valide
            header('Location: /projet-pfe-v1/projet-t1/public/equipements?error=invalid_id');
            exit;
        }

        try {
            // Mettre à jour le statut de l'équipement à 'indisponible'
            $query = "UPDATE equipements_reseau SET statut = 'indisponible' WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['id' => $id]);

            // Rediriger vers la page des équipements avec un message de succès
            header('Location: /projet-pfe-v1/projet-t1/public/equipements?success=equipement_indisponible');
            exit;

        } catch (PDOException $e) {
            error_log("Erreur lors du passage à indisponible de l'équipement " . $id . ": " . $e->getMessage());
            // Gérer l'erreur
            header('Location: /projet-pfe-v1/projet-t1/public/equipements?error=db_error');
            exit;
        }
    }

    public function setDisponible($id) {
        // Vérifier si l'utilisateur est un admin
        if (!AuthController::isLoggedIn() || AuthController::getUserRole() !== 'admin') {
            header('Location: /projet-pfe-v1/projet-t1/public/equipements');
            exit;
        }

        // Valider l'ID
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            header('Location: /projet-pfe-v1/projet-t1/public/equipements?error=invalid_id');
            exit;
        }

        try {
            // Mettre à jour le statut de l'équipement à 'disponible'
            $query = "UPDATE equipements_reseau SET statut = 'disponible' WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['id' => $id]);

            // Rediriger
            header('Location: /projet-pfe-v1/projet-t1/public/equipements?success=equipement_disponible');
            exit;

        } catch (PDOException $e) {
            error_log("Erreur lors du passage à disponible de l'équipement " . $id . ": " . $e->getMessage());
            header('Location: /projet-pfe-v1/projet-t1/public/equipements?error=db_error');
            exit;
        }
    }

    public function supprimerEquipement($id) {
        // Vérifier si l'utilisateur est un admin
        if (!AuthController::isLoggedIn() || AuthController::getUserRole() !== 'admin') {
            header('Location: /projet-pfe-v1/projet-t1/public/equipements');
            exit;
        }

        // Valider l'ID
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            header('Location: /projet-pfe-v1/projet-t1/public/equipements?error=invalid_id');
            exit;
        }

        try {
            // Supprimer l'équipement de la base de données
            $query = "DELETE FROM equipements_reseau WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['id' => $id]);

            // Rediriger vers la page des équipements avec un message de succès
            header('Location: /projet-pfe-v1/projet-t1/public/equipements?success=equipement_supprime');
            exit;

        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression de l'équipement " . $id . ": " . $e->getMessage());
            // Gérer l'erreur
            header('Location: /projet-pfe-v1/projet-t1/public/equipements?error=db_error');
            exit;
        }
    }
} 