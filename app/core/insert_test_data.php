<?php
require_once __DIR__ . '/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Suppression des données existantes (optionnel)
    $db->exec("TRUNCATE TABLE equipements");
    
    // Préparation de la requête d'insertion
    $query = "INSERT INTO equipements (type, modele, capacite, disponibilite) VALUES 
        ('Router', 'Cisco ASR 1001-X', '2.5 Gbps', 1),
        ('Router', 'Juniper MX104', '80 Gbps', 0),
        ('Switch', 'Cisco Catalyst 9300', '1 Gbps (48 ports)', 1),
        ('Router', 'Cisco ISR 4431', '1 Gbps', 1),
        ('Switch', 'Arista 7050X3', '10 Gbps (48 ports)', 0),
        ('Router', 'Huawei NE40E-X8', '40 Gbps', 1),
        ('Switch', 'Juniper EX4300', '1 Gbps (48 ports)', 1),
        ('Router', 'Nokia 7750 SR-12', '100 Gbps', 0),
        ('Switch', 'Cisco Nexus 9000', '100 Gbps (32 ports)', 1),
        ('Router', 'Cisco CRS-1', '1.2 Tbps', 1)";
    
    // Exécution de la requête
    $db->exec($query);
    
    echo "Données de test insérées avec succès !";
    
} catch(PDOException $e) {
    echo "Erreur lors de l'insertion des données : " . $e->getMessage();
} 