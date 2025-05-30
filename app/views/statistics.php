<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques</title>
    <link rel="stylesheet" href="/projet-pfe-v1/projet-t1/public/assets/css/dashboard.css">
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
                <li><a href="#"><i class="fas fa-history"></i> Historiques</a></li>
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
                                <span class="label">En service</span>
                                <span class="value"><?php echo $equipementsByStatus['en_service']; ?></span>
                            </div>
                            <div class="kpi-item">
                                <span class="label">En maintenance</span>
                                <span class="value"><?php echo $equipementsByStatus['maintenance']; ?></span>
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

                    <div class="kpi-card">
                        <h3>Temps Moyen de Traitement</h3>
                        <div class="kpi-value"><?php echo $avgTreatmentTime; ?>h</div>
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

    <script>
        // Données pour les graphiques
        const workOrdersByTechnology = <?php echo json_encode($workOrdersByTechnology); ?>;
        const workOrdersEvolution = <?php echo json_encode($workOrdersEvolution); ?>;
        const equipementsByMarque = <?php echo json_encode($equipementsByMarque); ?>;

        // Graphique de répartition par technologie
        const technologyOptions = {
            series: workOrdersByTechnology.map(item => item.count),
            chart: {
                type: 'donut',
                height: 350
            },
            labels: workOrdersByTechnology.map(item => item.technology),
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };
        new ApexCharts(document.querySelector("#technologyChart"), technologyOptions).render();

        // Graphique d'évolution des work orders
        const evolutionOptions = {
            series: [{
                name: 'Work Orders',
                data: workOrdersEvolution.map(item => item.count)
            }],
            chart: {
                type: 'area',
                height: 350
            },
            xaxis: {
                categories: workOrdersEvolution.map(item => item.date)
            }
        };
        new ApexCharts(document.querySelector("#evolutionChart"), evolutionOptions).render();

        // Graphique de répartition des équipements
        const equipementsOptions = {
            series: equipementsByMarque.map(item => item.count),
            chart: {
                type: 'pie',
                height: 350
            },
            labels: equipementsByMarque.map(item => item.marque),
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };
        new ApexCharts(document.querySelector("#equipementsChart"), equipementsOptions).render();
    </script>
</body>
</html> 