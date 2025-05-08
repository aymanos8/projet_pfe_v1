<?php
require_once __DIR__ . '/app/config/database.php';

try {
    $cnx = getConnection();
    
    // Récupérer tous les work orders
    $stmt = $cnx->query("SELECT * FROM `work-orders` ORDER BY date DESC");
    $workOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Afficher les résultats
    echo "<h2>Liste des Work Orders</h2>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background-color: #f2f2f2;'>";
    echo "<th>Numéro</th>";
    echo "<th>Client</th>";
    echo "<th>Technologie</th>";
    echo "<th>Offre</th>";
    echo "<th>Statut</th>";
    echo "<th>Date</th>";
    echo "</tr>";
    
    foreach ($workOrders as $wo) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($wo['numero']) . "</td>";
        echo "<td>" . htmlspecialchars($wo['client']) . "</td>";
        echo "<td>" . htmlspecialchars($wo['technology']) . "</td>";
        echo "<td>" . htmlspecialchars($wo['offre']) . "</td>";
        echo "<td>" . htmlspecialchars($wo['status']) . "</td>";
        echo "<td>" . htmlspecialchars($wo['date']) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Afficher le nombre total de work orders
    echo "<p>Nombre total de work orders : " . count($workOrders) . "</p>";
    
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
} 