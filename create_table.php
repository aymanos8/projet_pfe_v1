<?php
require_once __DIR__ . '/app/config/database.php';

try {
    $cnx = getConnection();
    
    // Création de la table work-orders
    $sql = "CREATE TABLE IF NOT EXISTS `work-orders` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `numero` VARCHAR(50) NOT NULL,
        `client` VARCHAR(255) NOT NULL,
        `technology` VARCHAR(100),
        `offre` VARCHAR(100),
        `status` VARCHAR(50),
        `date` DATETIME,
        UNIQUE KEY `numero_unique` (`numero`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $cnx->exec($sql);
    echo "Table 'work-orders' créée ou déjà existante avec succès.\n";
    
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
} 