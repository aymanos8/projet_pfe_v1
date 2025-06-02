<?php
// Les variables $workOrders et $stats sont passées par le contrôleur
// require_once __DIR__ . '/../controllers/DashboardController.php';
// $controller = new DashboardController();
// $workOrders = array_slice($controller->getWorkOrders(), 0, 3); // Limite à 3 derniers
// $stats = $controller->getStatistics();

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
    <title>test</title>
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
                <li class="active"><a href="/projet-pfe-v1/projet-t1/public/dashboard"><i class="fas fa-home"></i> Vue d'ensemble</a></li>
                <li><a href="/projet-pfe-v1/projet-t1/public/workorders"><i class="fas fa-tasks"></i> Work-Orders</a></li>
                <li><a href="/projet-pfe-v1/projet-t1/public/equipements"><i class="fas fa-server"></i> Équipements</a></li>
                <li><a href="/projet-pfe-v1/projet-t1/public/configuration"><i class="fas fa-cogs"></i> Configurations</a></li>
                <li><a href="#"><i class="fas fa-history"></i> Historiques</a></li>
                <?php if (AuthController::isLoggedIn() && AuthController::getUserRole() === 'admin'): ?>
                    <li><a href="/projet-pfe-v1/projet-t1/public/statistics"><i class="fas fa-chart-bar"></i> Statistiques</a></li>
                <?php endif; ?>
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
                    <!-- <img src="https://via.placeholder.com/40" alt="Profile"> -->
                    <div class="profile-info">
                         <?php if (AuthController::isLoggedIn()): ?>
                            <span class="name"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Utilisateur'); ?></span>
                            <span class="role"><?php echo htmlspecialchars(ucfirst($_SESSION['user_role'] ?? '')); ?></span>
                        <?php else: ?>
                            <span class="name">Invité</span>
                            <span class="role">Non connecté</span>
                        <?php endif; ?>
                    </div>
                    <div class="dropdown-menu">
                        <div class="dropdown-title">Mon compte</div>
                        <div class="dropdown-item">Profil</div>
                        <div class="dropdown-item">Préférences</div>
                         <?php if (AuthController::isLoggedIn()): ?>
                            <a href="/projet-pfe-v1/projet-t1/public/logout" class="dropdown-item logout-item">Se déconnecter</a>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Summary Cards -->
                <div class="summary-cards">
                    <div class="card">
                        <i class="fas fa-server"></i>
                        <h3>Équipements à configurer</h3>
                        <p>12</p>
                    </div>
                    <div class="card">
                        <i class="fas fa-tasks"></i>
                        <h3>Work-Orders en attente</h3>
                        <p><?php echo $stats['pending']; ?></p>
                    </div>
                    <div class="card">
                        <i class="fas fa-ticket-alt"></i>
                        <h3>Work-Orders en cours</h3>
                        <p><?php echo $stats['in_progress']; ?></p>
                    </div>
                    <div class="card">
                        <i class="fas fa-check-circle"></i>
                        <h3>Work-Orders terminés</h3>
                        <p><?php echo $stats['completed']; ?></p>
                    </div>
                    <div class="card">
                        <i class="fas fa-list"></i>
                        <h3>Total Work-Orders</h3>
                        <p><?php echo $stats['total']; ?></p>
                    </div>
                </div>

                <!-- Work Orders Table -->
                <div class="work-orders">
                    <h2>Work Orders Récents</h2>
                    <div class="filters">
                        <select id="technology-filter">
                            <option value="">Toutes les technologies</option>
                            <option value="fo">FO</option>
                            <option value="4g">4G</option>
                            <option value="fh">FH</option>
                            
                        </select>
                        <select id="status-filter">
                            <option value="">Tous les statuts</option>
                            <option value="1">En attente</option>
                            <option value="2">En cours</option>
                            <option value="3">Terminé</option>
                        </select>
                    </div>
                    <table id="work-orders-table">
                        <thead>
                            <tr>
                                <th>N° Work Order</th>
                                <th>Client</th>
                                <th>Technologie</th>
                                <th>Offre</th>
                                <th>État</th>
                                <th>Date de création</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Limiter le tableau $workOrders aux 3 premiers éléments pour l'affichage récent
                            $workOrders = array_slice($workOrders, 0, 3);
                            foreach ($workOrders as $wo):
                            ?>
                            <tr data-status="<?php echo $wo['status']; ?>" data-technology="<?php echo strtolower($wo['technology']); ?>">
                                <td><?php echo htmlspecialchars($wo['numero']); ?></td>
                                <td><?php echo htmlspecialchars($wo['client']); ?></td>
                                <td><?php echo tech_label($wo['technology']); ?></td>
                                <td><?php echo htmlspecialchars($wo['offre']); ?></td>
                                <td><?php echo statut_label($wo['status']); ?></td>
                                <td><?php echo date('j F Y', strtotime($wo['date'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <script src="/projet-pfe-v1/projet-t1/public/assets/js/dashboard.js"></script>
</body>
</html> 
<?php
// Limiter le tableau $workOrders aux 3 premiers éléments pour l'affichage récent
$workOrders = array_slice($workOrders, 0, 3);
?> 