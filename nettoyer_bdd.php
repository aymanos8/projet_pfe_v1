<?php
require_once __DIR__ . '/app/core/Database.php';

try {
    $cnx = Database::getInstance()->getConnection();

    echo "Recherche et suppression des doublons dans equipements_reseau...\n";

    // Identifier les doublons (basé sur marque, modele, capacite)
    $query = "SELECT marque, modele, capacite, COUNT(*) as count, GROUP_CONCAT(id ORDER BY id) as ids
              FROM equipements_reseau
              GROUP BY marque, modele, capacite
              HAVING count > 1";
    
    $stmt = $cnx->query($query);
    $duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($duplicates)) {
        echo "Aucun doublon trouvé.\n";
    } else {
        echo "Doublons trouvés et en cours de suppression :\n";
        foreach ($duplicates as $duplicate) {
            $ids = explode(',', $duplicate['ids']);
            $idsToKeep = [$ids[0]]; // Garder le premier ID
            $idsToDelete = array_slice($ids, 1); // Supprimer les IDs suivants

            if (!empty($idsToDelete)) {
                $placeholders = rtrim(str_repeat('?, ', count($idsToDelete)), ', ');
                $deleteQuery = "DELETE FROM equipements_reseau WHERE id IN ({$placeholders})";
                $deleteStmt = $cnx->prepare($deleteQuery);
                $deleteStmt->execute($idsToDelete);

                echo "- Marque: " . $duplicate['marque'] . ", Modèle: " . $duplicate['modele'] . ", Capacité: " . $duplicate['capacite'] . ", Supprimé IDs: " . implode(', ', $idsToDelete) . "\n";
            }
        }
        echo "Suppression des doublons terminée.\n";
    }

    // Remettre le statut de tous les équipements à 'disponible'
    echo "Remise à zéro des statuts des équipements à 'disponible'...\n";
    $updateStatusQuery = "UPDATE equipements_reseau SET statut = 'disponible'";
    $updateStatusStmt = $cnx->prepare($updateStatusQuery);
    $updateStatusStmt->execute();
    echo "Statuts mis à jour.\n";


} catch(PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage() . "\n";
} catch(Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
?> 