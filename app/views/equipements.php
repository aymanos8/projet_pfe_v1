<?php
require_once __DIR__ . '/../controllers/EquipementController.php';
$controller = new EquipementController();
$equipements = $controller->getEquipements();

function disponibilite_label($status) {
    switch ($status) {
        case '1': return '<span class="status-badge status-1">Disponible</span>';
        case '0': return '<span class="status-badge status-0">Non disponible</span>';
        default: return htmlspecialchars($status);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des équipements</title>
    <link rel="stylesheet" href="/projet-pfe-v1/projet-t1/public/assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2>test</h2>
            </div>
            <ul class="nav-links">
                <li><a href="/projet-pfe-v1/projet-t1/public/dashboard"><i class="fas fa-home"></i> Vue d'ensemble</a></li>
                <li><a href="/projet-pfe-v1/projet-t1/public/workorders"><i class="fas fa-tasks"></i> Work-Orders</a></li>
                <li class="active"><a href="/projet-pfe-v1/projet-t1/public/equipements"><i class="fas fa-server"></i> Équipements</a></li>
                <li><a href="/projet-pfe-v1/projet-t1/public/configuration"><i class="fas fa-cogs"></i> Configurations</a></li>
                <li><a href="#"><i class="fas fa-history"></i> Historiques</a></li>
                <li><a href="#"><i class="fas fa-chart-bar"></i> Statistiques</a></li>
            </ul>
            <div class="sidebar-footer">
                <li><i class="fas fa-cog"></i> Paramètres</li>
                <span class="version">v1.0.0</span>
            </div>
        </nav>
        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Bar -->
            <header class="top-bar">
                <div class="search-container">
                    <input type="text" placeholder="Rechercher...">
                    <i class="fas fa-search"></i>
                </div>
                <div class="profile" id="profile-menu">
                    <div class="profile-info">
                        <span class="name"></span>
                        <span class="role">Administrateur</span>
                    </div>
                </div>
            </header>
            <div class="dashboard-content">
                <h2>Gestion des équipements</h2>
                <table style="width:100%;margin-top:32px;background:#fff;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.07);">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Marque</th>
                            <th>Modèle</th>
                            <th>Capacité</th>
                            <th>Disponibilité</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (
                            isset($equipements) ? $equipements : [] as $equipement): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($equipement['id']); ?></td>
                            <td><?php echo htmlspecialchars($equipement['marque']); ?></td>
                            <td><?php echo htmlspecialchars($equipement['modele']); ?></td>
                            <td><?php echo htmlspecialchars($equipement['capacite']); ?></td>
                            <td>
                                <?php
                                if ($equipement['statut'] === 'disponible') {
                                    echo "<span style='background:#ffd700;color:#000;padding:5px 10px;border-radius:15px;font-size:0.8rem;'>Disponible</span>";
                                } else {
                                    echo "<span style='background:#eee;color:#888;padding:5px 10px;border-radius:15px;font-size:0.8rem;'>" . htmlspecialchars($equipement['statut']) . "</span>";
                                }
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html> 