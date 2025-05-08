<?php
class ConfigController {
    public function show($id) {
        echo "Profil de l'utilisateur avec ID : $id";
    }

    public function create() {
        echo "Création d'un nouvel utilisateur (via POST)";
    }
}
