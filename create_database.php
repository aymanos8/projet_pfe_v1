<?php
try {
    $cnx = new PDO("mysql:host=localhost", "root", "");
    $cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Création de la base de données
    $sql = "CREATE DATABASE IF NOT EXISTS projet_pfe";
    $cnx->exec($sql);
    echo "Base de données 'projet_pfe' créée avec succès.\n";
    
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
} 