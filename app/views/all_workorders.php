<?php
require_once __DIR__ . '/../controllers/DashboardController.php';
$controller = new DashboardController();
$workOrders = $controller->getWorkOrders();

function statut_label($status) {
    switch ($status) {
        case '1': return '<span class="status-badge status-1">En Attente</span>';
        case '2': return '<span class="status-badge status-2">En Cours</span>';
        case '3': return '<span class="status-badge status-3">Terminé</span>';
        default: return htmlspecialchars($status);
    }
}
function tech_label($tech) {
    $map = ['fo' => 'FO', 'fh' => 'FH', 'blr' => 'BLR', 'internet' => 'internet'];
    $t = strtolower($tech);
    return isset($map[$t]) ? $map[$t] : strtoupper($tech);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tous les Work Orders</title>
    <link rel="stylesheet" href="../../public/assets/css/dashboard.css">
    <link rel="stylesheet" href="../../public/assets/css/all_workorders.css">
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
                <li><a href="dashboard.php"><i class="fas fa-home"></i> Vue d'ensemble</a></li>
                <li class="active"><a href="all_workorders.php"><i class="fas fa-tasks"></i> Work-Orders</a></li>
                <li><a href="#"><i class="fas fa-server"></i> Équipements</a></li>
                <li><a href="#"><i class="fas fa-cogs"></i> Configurations</a></li>
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
                <div class="work-orders">
                    <h2>Tous les Work Orders</h2>
                    <table id="all-work-orders-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>N° Work Order</th>
                                <th>Client</th>
                                <th>Technologie</th>
                                <th>Offre</th>
                                <th>État</th>
                                <th>Date de création</th>
                                <th>Détail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($workOrders as $wo): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($wo['id'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($wo['numero']); ?></td>
                                <td><?php echo htmlspecialchars($wo['client']); ?></td>
                                <td><?php echo tech_label($wo['technology']); ?></td>
                                <td><?php echo htmlspecialchars($wo['offre']); ?></td>
                                <td><?php echo statut_label($wo['status']); ?></td>
                                <td><?php echo date('j F Y', strtotime($wo['date'])); ?></td>
                                <td>
                                    <a class="btn-link" href="workorder_detail.php?id=<?php echo urlencode($wo['id']); ?>">Voir détail</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <script src="../../public/assets/js/all_workorders.js"></script>
</body>
</html> 