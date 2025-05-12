<?php
if (!$workorder) {
    echo "Aucun work order sélectionné.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détail Work Order</title>
    <link rel="stylesheet" href="../../public/assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .workorder-detail-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            padding: 40px 32px 32px 32px;
            max-width: 700px;
            margin: 50px auto 0 auto;
        }
        .workorder-detail-card h2 {
            font-size: 2rem;
            margin-bottom: 18px;
            color: #2d3a4b;
        }
        .workorder-detail-list {
            list-style: none;
            padding: 0;
            margin: 0 0 30px 0;
        }
        .workorder-detail-list li {
            margin-bottom: 14px;
            font-size: 1.08rem;
        }
        .workorder-detail-label {
            font-weight: 600;
            color: #3b4a5a;
            min-width: 120px;
            display: inline-block;
        }
        .workorder-description {
            background: #f5f6fa;
            border-radius: 8px;
            padding: 18px 20px;
            color: #222;
            font-size: 1.08rem;
            margin-bottom: 0;
        }
    </style>
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
                <li class="active"><a href="/projet-pfe-v1/projet-t1/public/workorders"><i class="fas fa-tasks"></i> Work-Orders</a></li>
                <li><a href="/projet-pfe-v1/projet-t1/public/equipements"><i class="fas fa-server"></i> Équipements</a></li>
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
            <div class="dashboard-content">
                <div class="workorder-detail-card">
                    <h2>Work Order <?php echo htmlspecialchars($workorder['numero']); ?></h2>
                    <ul class="workorder-detail-list">
                        <li><span class="workorder-detail-label">Client :</span> <?php echo htmlspecialchars($workorder['client']); ?></li>
                        <li><span class="workorder-detail-label">Technologie :</span> <?php echo htmlspecialchars($workorder['technology']); ?></li>
                        <li><span class="workorder-detail-label">Offre :</span> <?php echo htmlspecialchars($workorder['offre']); ?></li>
                        <li><span class="workorder-detail-label">Date :</span> <?php echo htmlspecialchars($workorder['date']); ?></li>
                    </ul>
                    <div class="workorder-description">
                        <strong>Description :</strong><br>
                        <?php echo htmlspecialchars($workorder['short_description'] ?? 'Aucune description.'); ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html> 