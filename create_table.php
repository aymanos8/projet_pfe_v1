<?php
require_once __DIR__ . '/app/core/Database.php';

try {
    $cnx = Database::getInstance()->getConnection();
    
    // Création de la table work-orders
    $sql = "CREATE TABLE IF NOT EXISTS `work-orders` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `numero` VARCHAR(50) NOT NULL,
        `client` VARCHAR(255) NOT NULL,
        `technology` VARCHAR(100),
        `offre` VARCHAR(100),
        `status` VARCHAR(50),
        `date` DATETIME,
        `short_description` TEXT,
        `user_id` INT NULL,
        UNIQUE KEY `numero_unique` (`numero`),
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $cnx->exec($sql);
    echo "Table 'work-orders' créée ou déjà existante avec succès.\n";

    // Ajouter la colonne user_id si elle n'existe pas (pour les mises à jour)
    $sql_alter = "ALTER TABLE `work-orders` ADD `user_id` INT NULL, ADD CONSTRAINT `fk_work_orders_users` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL;";
    try {
        $cnx->exec($sql_alter);
        echo "Colonne 'user_id' ajoutée à la table 'work-orders' avec succès (ou déjà existante).\n";
    } catch(PDOException $e) {
        // Ignorer l'erreur si la colonne existe déjà
        if (strpos($e->getMessage(), 'Duplicate column name') === false && strpos($e->getMessage(), 'Cannot add foreign key constraint') === false) {
            throw $e; // Relancer l'erreur si ce n'est pas une erreur de colonne existante ou de contrainte existante
        }
         echo "Colonne 'user_id' ou contrainte 'fk_work_orders_users' déjà existante.\n";
    }

    // Création de la table equipements_reseau
    $sql = "CREATE TABLE IF NOT EXISTS `equipements_reseau` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `modele` VARCHAR(100) NOT NULL,
        `marque` VARCHAR(50) NOT NULL,
        `type_interfaces` VARCHAR(255),
        `capacite` VARCHAR(100),
        `statut` ENUM('disponible', 'en_service', 'maintenance') DEFAULT 'disponible',
        `numero_serie` VARCHAR(100),
        `date_ajout` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `date_modification` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $cnx->exec($sql);
    echo "Table 'equipements_reseau' créée ou déjà existante avec succès.\n";

    // Création de la table affectations_workorders
    $sql = "CREATE TABLE IF NOT EXISTS `affectations_workorders` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `work_order_id` INT NOT NULL,
        `equipement_id` INT NOT NULL,
        `date_affectation` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `statut_affectation` ENUM('en_cours', 'terminee', 'annulee') DEFAULT 'en_cours',
        FOREIGN KEY (`work_order_id`) REFERENCES `work-orders`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`equipement_id`) REFERENCES `equipements_reseau`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $cnx->exec($sql);
    echo "Table 'affectations_workorders' créée ou déjà existante avec succès.\n";

    // Insertion des modèles de routeurs Cisco
    $sql = "INSERT INTO `equipements_reseau` (`modele`, `marque`, `type_interfaces`, `capacite`) VALUES
        ('C891F ISR', 'Cisco', 'GigabitEthernet, Serial, Cellular', 'Routeur ISR 4G'),
        ('881-K9', 'Cisco', 'FastEthernet, Serial', 'Routeur SOHO'),
        ('881G-4G-GA-K9', 'Cisco', 'FastEthernet, Serial, Cellular', 'Routeur 4G')
    ON DUPLICATE KEY UPDATE `type_interfaces` = VALUES(`type_interfaces`), `capacite` = VALUES(`capacite`);";
    
    $cnx->exec($sql);
    echo "Modèles de routeurs Cisco ajoutés avec succès.\n";
    
    // Création de la table users
    $sql = "CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `email` VARCHAR(255) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `role` ENUM('admin', 'utilisateur') DEFAULT 'utilisateur',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $cnx->exec($sql);
    echo "Table 'users' créée ou déjà existante avec succès.\n";
    
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
} 

echo password_hash("le_mot_de_passe_que_vous_voulez", PASSWORD_DEFAULT);