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
            <div class="dashboard-content">
                <?php 
                    if (isset($_SESSION['success'])) {
                        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
                        unset($_SESSION['success']);
                    }
                    if (isset($_SESSION['error'])) {
                        echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
                        unset($_SESSION['error']);
                    }
                ?>
                <div class="workorder-detail-card">
                    <h2>Work Order <?php echo htmlspecialchars($workorder['numero']); ?></h2>
                    <ul class="workorder-detail-list">
                        <li><span class="workorder-detail-label">Client :</span> <?php echo htmlspecialchars($workorder['client']); ?></li>
                        <li><span class="workorder-detail-label">Technologie :</span> <?php echo htmlspecialchars($workorder['technology']); ?></li>
                        <li><span class="workorder-detail-label">Offre :</span> <?php echo htmlspecialchars($workorder['offre']); ?></li>
                        <?php if (!empty($workorder['debit'])): ?>
                            <li><span class="workorder-detail-label">Débit :</span> <?php echo htmlspecialchars($workorder['debit'] ?? '') . ' Mbps'; ?></li>
                        <?php endif; ?>
                        <li><span class="workorder-detail-label">Date :</span> <?php echo htmlspecialchars($workorder['date']); ?></li>
                        <li><span class="workorder-detail-label">Statut :</span> 
                            <?php 
                                function statut_label_detail($status) { // Définir la fonction localement ou l'importer
                                    switch ($status) {
                                        case '1': return '<span style="color: blue;">En Attente</span>'; // Adaptez le style
                                        case '2': return '<span style="color: orange;">En Cours</span>'; // Adaptez le style
                                        case '3': return '<span style="color: green;">Terminé</span>'; // Adaptez le style
                                        default: return htmlspecialchars($status);
                                    }
                                }
                                echo statut_label_detail($workorder['status']); 
                            ?>
                        </li>
                        <?php if (!empty($workorder['user_id'])): // Afficher l'utilisateur affecté si défini ?>
                            <li><span class="workorder-detail-label">Affecté à :</span> 
                                <?php 
                                    // Récupérer le nom d'utilisateur affecté si ce n'est pas déjà fait
                                    // Dans ce cas, comme nous sommes sur la page de détail, il faut peut-être
                                    // une méthode getById avec jointure ou une méthode pour récupérer l'utilisateur par ID
                                    // Pour l'instant, je suppose que $workorder pourrait contenir assigned_username si getById est modifiée,
                                    // sinon il faudrait récupérer l'utilisateur ici.
                                    // En attendant, j'affiche juste l'ID ou un placeholder.
                                    // TODO: Récupérer et afficher le nom d'utilisateur affecté réel ici.
                                    echo htmlspecialchars($workorder['user_id'] ?? 'N/A');
                                ?>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <div class="workorder-description">
                        <strong>Description :</strong><br>
                        <?php echo htmlspecialchars($workorder['short_description'] ?? 'Aucune description.'); ?>
                    </div>
                </div>
                <!-- Section Équipements -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Équipements affectés</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($equipements)): ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Modèle</th>
                                            <th>Numéro de série</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($equipements as $equipement): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($equipement['marque']); ?></td>
                                                <td><?php echo htmlspecialchars($equipement['modele']); ?></td>
                                                <td><?php echo htmlspecialchars($equipement['numero_serie']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $equipement['statut'] === 'disponible' ? 'success' : 'warning'; ?>">
                                                        <?php echo htmlspecialchars($equipement['statut']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="/projet-pfe-v1/projet-t1/public/configuration/<?php echo $equipement['id']; ?>" 
                                                       class="btn btn-sm btn-primary me-2">
                                                        <i class="fas fa-cog"></i> Générer config
                                                    </a>
                                                    <form action="/projet-pfe-v1/projet-t1/public/workorder/desaffecter-equipement" method="POST" style="display:inline;">
                                                        <input type="hidden" name="work_order_id" value="<?php echo $workorder['id']; ?>">
                                                        <input type="hidden" name="equipement_id" value="<?php echo $equipement['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir désaffecter cet équipement ?');">
                                                            <i class="fas fa-unlink"></i> Désaffecter
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">Aucun équipement affecté à ce work order.</p>
                        <?php endif; ?>

                        <!-- Formulaire d'affectation d'équipement -->
                        <?php if (!empty($equipementsDisponiblesCompatibles)): ?>
                            <div class="mt-4">
                                <h6>Affecter un nouvel équipement</h6>
                                <form action="/projet-pfe-v1/projet-t1/public/workorder/affecter-equipement" method="POST" class="row g-3">
                                    <input type="hidden" name="work_order_id" value="<?php echo $workorder['id']; ?>">
                                    <div class="col-md-8">
                                        <select name="equipement_id" class="form-select" required>
                                            <option value="">Sélectionner un équipement</option>
                                            <?php foreach ($equipementsDisponiblesCompatibles as $equipement): ?>
                                                <option value="<?php echo $equipement['id']; ?>">
                                                    <?php echo htmlspecialchars($equipement['marque'] . ' - ' . $equipement['modele'] . ' (' . $equipement['numero_serie'] . ')'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Affecter
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mt-4">Aucun équipement disponible pour l'affectation.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Bouton Terminer le Work Order -->
                <?php 
                    // Afficher le bouton Terminer si l'utilisateur est affecté ou admin, et le WO n'est pas Terminé
                    if (($workorder['user_id'] == $userId || $userRole === 'admin') && $workorder['status'] !== '3'): 
                ?>
                <div class="mt-4 text-center">
                    <form action="/projet-pfe-v1/projet-t1/public/workorder/complete" method="POST">
                        <input type="hidden" name="work_order_id" value="<?php echo htmlspecialchars($workorder['id']); ?>">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-check-circle"></i> Terminer le Work Order
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html> 