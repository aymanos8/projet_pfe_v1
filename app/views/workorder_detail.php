<?php
if (!$workorder) {
    echo "Aucun work order sélectionné.";
    exit;
}

function statut_label($status) {
    switch ($status) {
        case '1': return '<span class="status-badge status-1">En Attente</span>';
        case '2': return '<span class="status-badge status-2">En Cours</span>';
        case '3': return '<span class="status-badge status-3">Terminé</span>';
        default: return htmlspecialchars($status);
    }
}

// Inclure la fonction statut_label depuis all_workorders.php
// require_once __DIR__ . '/all_workorders.php';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détail Work Order</title>
    <link rel="stylesheet" href="../../public/assets/css/common.css">
    <link rel="stylesheet" href="../../public/assets/css/workorder_detail.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <?php if (AuthController::isLoggedIn() && AuthController::getUserRole() === 'admin'): ?>
                <li><a href="/projet-pfe-v1/projet-t1/public/historiques"><i class="fas fa-history"></i> Historiques</a></li>
                <?php endif; ?>
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
                                echo statut_label($workorder['status']);
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
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($equipements as $equipement): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($equipement['marque']); ?></td>
                                                <td><?php echo htmlspecialchars($equipement['modele']); ?></td>
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
                                                    <?php echo htmlspecialchars($equipement['marque'] . ' - ' . $equipement['modele']); ?>
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