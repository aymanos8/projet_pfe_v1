<?php
class EquipementController {
    public function index() {
        // Ici, on pourrait charger les équipements depuis le modèle
        // require_once '../app/models/Equipement.php';
        // $equipements = Equipement::getAll();
        // Pour l'instant, on affiche juste la vue
        require '../app/views/equipements.php';
    }
} 