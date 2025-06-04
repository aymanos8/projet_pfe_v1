<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Equipement.php';
require_once __DIR__ . '/../models/WorkOrder.php'; // Peut être utile pour les liens ou infos WO
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../models/Utilisateur.php'; // Utiliser Utilisateur.php
require_once __DIR__ . '/../models/HistoriqueAction.php';

class HistoriqueController {
    public function index() {
        // Vérifier si l'utilisateur est connecté et a le rôle approprié (admin)
        if (!AuthController::isLoggedIn() || AuthController::getUserRole() !== 'admin') {
            // Rediriger vers une autre page (par exemple, le tableau de bord) ou afficher un message d'erreur
            header('Location: /projet-pfe-v1/projet-t1/public/dashboard'); // Redirection vers le dashboard
            exit; // Assurer que le script s'arrête après la redirection
        }

        try {
            $database = Database::getInstance();
            $equipementModel = new Equipement($database);
            $historiqueModel = new HistoriqueAction($database);
            $userModel = new Utilisateur($database->getConnection()); // Utiliser Utilisateur et passer la connexion

            // Récupérer les filtres depuis la requête GET
            $dateDebut = $_GET['date_debut'] ?? date('Y-m-d', strtotime('-30 days'));
            $dateFin = $_GET['date_fin'] ?? date('Y-m-d');
            $entiteType = $_GET['entite_type'] ?? null;
            $entiteId = $_GET['entite_id'] ?? null;
            $userId = $_GET['user_id'] ?? null;

            // Récupérer l'historique selon les filtres
            // Utiliser la nouvelle méthode getFilteredHistorique qui gère toutes les combinaisons de filtres
            $historique = $historiqueModel->getFilteredHistorique($dateDebut, $dateFin, $entiteType, $entiteId, $userId);

            // Récupérer tous les utilisateurs pour le filtre (utiliser la méthode getAll)
            $users = $userModel->getAll();

            // Inclure la vue pour afficher l'historique
            require __DIR__ . '/../views/historiques.php';

        } catch (Exception $e) {
            // Gérer l'erreur (par exemple, afficher un message d'erreur)
            echo "Erreur lors du chargement de l'historique : " . $e->getMessage();
        }
    }
} 