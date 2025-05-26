<?php
require_once __DIR__ . '/app/core/Database.php';

try {
    $cnx = Database::getInstance()->getConnection();
    
    // Mettre à jour le statut de tous les équipements à 'disponible'
    $query = "UPDATE equipements_reseau SET statut = 'disponible'";
    $stmt = $cnx->prepare($query);
    $stmt->execute();
    
    echo "Statut de tous les équipements mis à 'disponible'.\n";
    
    // Vérification des statuts après mise à jour
    $query = "SELECT modele, numero_serie, statut FROM equipements_reseau";
    $stmt = $cnx->query($query);
    $equipements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "\nListe des équipements et leur statut après mise à jour :\n";
    foreach ($equipements as $equipement) {
        echo "- " . $equipement['modele'] . " (" . $equipement['numero_serie'] . ") - Statut : " . $equipement['statut'] . "\n";
    }
    
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
} 