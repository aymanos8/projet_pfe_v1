<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques</title>
    <link rel="stylesheet" href="/projet-pfe-v1/projet-t1/public/assets/css/common.css">
    <link rel="stylesheet" href="/projet-pfe-v1/projet-t1/public/assets/css/statistics.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2>Gestion WO</h2>
            </div>
            <ul class="nav-links">
                <li><a href="/projet-pfe-v1/projet-t1/public/dashboard"><i class="fas fa-home"></i> Tableau de bord</a></li>
                <li><a href="/projet-pfe-v1/projet-t1/public/workorders"><i class="fas fa-tasks"></i> Work Orders</a></li>
                <li><a href="/projet-pfe-v1/projet-t1/public/equipements"><i class="fas fa-server"></i> Équipements</a></li>
                <li><a href="/projet-pfe-v1/projet-t1/public/configuration"><i class="fas fa-cogs"></i> Configurations</a></li>
                <?php if (AuthController::isLoggedIn() && AuthController::getUserRole() === 'admin'): ?>
                <li><a href="/projet-pfe-v1/projet-t1/public/historiques"><i class="fas fa-history"></i> Historiques</a></li>
                <?php endif; ?>
                <li class="active"><a href="/projet-pfe-v1/projet-t1/public/statistics"><i class="fas fa-chart-bar"></i> Statistiques</a></li>
            </ul>
            <div class="sidebar-footer">
                <ul>
                    <li><a href="#"><i class="fas fa-cog"></i> Paramètres</a></li>
                </ul>
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

            <div class="dashboard-content">
                <h2>Statistiques</h2>

                <!-- KPIs -->
                <div class="kpi-container">
                    <div class="kpi-card">
                        <h3>Work Orders</h3>
                        <div class="kpi-value"><?php echo $totalWorkOrders; ?></div>
                        <div class="kpi-details">
                            <div class="kpi-item">
                                <span class="label">En attente</span>
                                <span class="value"><?php echo $workOrdersByStatus['en_attente']; ?></span>
                            </div>
                            <div class="kpi-item">
                                <span class="label">En cours</span>
                                <span class="value"><?php echo $workOrdersByStatus['en_cours']; ?></span>
                            </div>
                            <div class="kpi-item">
                                <span class="label">Terminés</span>
                                <span class="value"><?php echo $workOrdersByStatus['termine']; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="kpi-card">
                        <h3>Work Orders par Période</h3>
                        <div class="kpi-details">
                            <div class="kpi-item">
                                <span class="label">Aujourd'hui</span>
                                <span class="value"><?php echo $workOrdersToday; ?></span>
                            </div>
                            <div class="kpi-item">
                                <span class="label">Cette semaine</span>
                                <span class="value"><?php echo $workOrdersThisWeek; ?></span>
                            </div>
                            <div class="kpi-item">
                                <span class="label">Ce mois</span>
                                <span class="value"><?php echo $workOrdersThisMonth; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="kpi-card">
                        <h3>Équipements</h3>
                        <div class="kpi-value"><?php echo array_sum($equipementsByStatus); ?></div>
                        <div class="kpi-details">
                            <div class="kpi-item">
                                <span class="label">Disponibles</span>
                                <span class="value"><?php echo $equipementsByStatus['disponible']; ?></span>
                            </div>
                             <div class="kpi-item">
                                <span class="label">Indisponible</span>
                                <span class="value"><?php echo $equipementsByStatus['indisponible']; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="kpi-card">
                        <h3>Utilisateurs</h3>
                        <div class="kpi-value"><?php echo $totalUsers; ?></div>
                        <div class="kpi-details">
                            <?php foreach ($usersByRole as $role): ?>
                            <div class="kpi-item">
                                <span class="label"><?php echo ucfirst($role['role']); ?></span>
                                <span class="value"><?php echo $role['count']; ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Graphiques -->
                <div class="charts-container">
                    <div class="chart-card">
                        <h3>Répartition par Technologie</h3>
                        <div id="technologyChart"></div>
                    </div>

                    <div class="chart-card">
                        <h3>Évolution des Work Orders</h3>
                        <div id="evolutionChart"></div>
                    </div>

                    <div class="chart-card">
                        <h3>Répartition des Équipements</h3>
                        <div id="equipementsChart"></div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php
// Rendre les données PHP disponibles pour JavaScript
?>
<script>
    const workOrdersByTechnologyData = <?php echo json_encode(['technologies' => array_column($workOrdersByTechnology, 'technology'), 'counts' => array_column($workOrdersByTechnology, 'count')]); ?>;
    const workOrdersEvolutionData = <?php echo json_encode(['dates' => array_column($workOrdersEvolution, 'date'), 'counts' => array_column($workOrdersEvolution, 'count')]); ?>;
    const equipementsByMarqueData = <?php echo json_encode(['marques' => array_column($equipementsByMarque, 'marque'), 'counts' => array_column($equipementsByMarque, 'count')]); ?>;
</script>
<script src="/projet-pfe-v1/projet-t1/public/assets/js/statistics.js"></script>
<script>
        document.addEventListener('DOMContentLoaded', function() {
             const profileMenu = document.getElementById('profile-menu');
            if (profileMenu) {
                profileMenu.addEventListener('click', function() {
                    this.classList.toggle('active');
                });
            }
        });
    </script>
</body>
</html> 