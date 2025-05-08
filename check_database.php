<?php
require_once __DIR__ . '/app/config/database.php';

try {
    $cnx = getConnection();
    
    // Vérifier la structure de la table
    echo "<h3>Structure de la table work-orders :</h3>";
    $stmt = $cnx->query("DESCRIBE `work-orders`");
    echo "<pre>";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    echo "</pre>";
    
    // Vérifier le contenu de la table
    echo "<h3>Contenu de la table work-orders :</h3>";
    $stmt = $cnx->query("SELECT * FROM `work-orders`");
    echo "<pre>";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    echo "</pre>";
    
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
} 