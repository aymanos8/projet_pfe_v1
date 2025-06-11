<?php
// require_once __DIR__ . '/../controllers/DashboardController.php';
// $controller = new DashboardController();
// $workOrders = $controller->getWorkOrders();

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
    <title>Work Orders</title>
    <link rel="stylesheet" href="/projet-pfe-v1/projet-t1/public/assets/css/common.css">
    <link rel="stylesheet" href="/projet-pfe-v1/projet-t1/public/assets/css/all_workorders.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2>NetManage</h2>
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

            <!-- Page Content -->
            <div class="page-content">
                <h1>Work Orders</h1>
                <!-- Le contenu spécifique à la page Work Orders ira ici -->
                 <div class="work-orders">
                    <div class="work-orders-header">
                        <h2>Tous les Work Orders</h2>
                        <!-- Bouton de synchronisation -->
                        <?php // if (AuthController::isLoggedIn() && AuthController::getUserRole() === 'admin'): ?>
                            <?php // <a href="/projet-pfe-v1/projet-t1/public/workorders/sync" class="btn btn-primary btn-sync">Synchroniser</a> ?>
                        <?php // endif; ?>
                    </div>
                    <table id="all-work-orders-table">
                        <thead>
                            <tr>
                                <th>N° Work Order</th>
                                <th>Client</th>
                                <th>Technologie</th>
                                <th>Offre</th>
                                <th>Débit</th>
                                <th>État</th>
                                <th>Date de création</th>
                                <th>Détail</th>
                                <?php if (AuthController::isLoggedIn() && AuthController::getUserRole() === 'admin'): ?>
                                <th>Affecter à</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($workOrders)) {
                                foreach ($workOrders as $wo): ?>
                                    <tr data-status="<?php echo $wo['status']; ?>" data-technology="<?php echo strtolower($wo['technology']); ?>">
                                        <td><?php echo htmlspecialchars($wo['numero']); ?></td>
                                        <td><?php echo htmlspecialchars($wo['client']); ?></td>
                                        <td><?php echo htmlspecialchars($wo['technology']); ?></td>
                                        <td><?php echo htmlspecialchars($wo['offre']); ?></td>
                                        <td><?php echo htmlspecialchars($wo['debit']); ?></td>
                                        <td><?php echo statut_label($wo['status']); ?></td>
                                        <td><?php echo date('j F Y', strtotime($wo['date'])); ?></td>
                                        <td><a href="/projet-pfe-v1/projet-t1/public/workorder_detail/<?php echo $wo['id']; ?>" class="btn-link">Voir détail</a></td>
                                        <?php if (AuthController::isLoggedIn() && AuthController::getUserRole() === 'admin'): ?>
                                        <td>
                                            <?php
                                                // Détermine si un utilisateur est affecté pour ce work order en vérifiant assigned_username
                                                $is_assigned = !empty($wo['assigned_username']);
                                                // Détermine les classes CSS initiales pour l'affichage et le formulaire
                                                $displayClass = $is_assigned ? 'show-flex' : 'hide-flex';
                                                $formClass = $is_assigned ? 'hide-flex' : 'show-flex';
                                            ?>
                                            <!-- Div pour afficher le nom de l'utilisateur affecté et le bouton Modifier -->
                                            <div class="assignment-display <?php echo $displayClass; ?>" id="assignment-display-<?php echo htmlspecialchars($wo['id']); ?>">
                                                <span class="assigned-user"><?php echo htmlspecialchars($wo['assigned_username'] ?? 'Non affecté'); ?></span>
                                                <button class="btn-edit-assignment btn-modifier-assignment" data-workorder-id="<?php echo htmlspecialchars($wo['id']); ?>">Modifier</button>
                                            </div>
                                            <!-- Div pour afficher le formulaire d'affectation -->
                                            <div class="assignment-form <?php echo $formClass; ?>" id="assignment-form-<?php echo htmlspecialchars($wo['id']); ?>">
                                                <form action="/projet-pfe-v1/projet-t1/public/workorder/affecter-utilisateur" method="POST">
                                                    <input type="hidden" name="work_order_id" value="<?php echo htmlspecialchars($wo['id']); ?>">
                                                    <select name="user_id" class="form-select">
                                                        <option value="">-- Sélectionner un utilisateur --</option>
                                                        <?php foreach ($users as $user): ?>
                                                            <option value="<?php echo htmlspecialchars($user['id']); ?>"
                                                                    <?php echo (isset($wo['assigned_user_id']) && $wo['assigned_user_id'] == $user['id']) ? 'selected' : ''; ?>>
                                                             <?php echo htmlspecialchars($user['username']); ?>
                                                        </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <button type="submit" class="btn btn-primary btn-sm">Affecter</button>
                                                </form>
                                            </div>
                                        </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach;
                            } else {
                                echo '<tr><td colspan="9">Aucun work order trouvé.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
     <!-- <script src="/projet-pfe-v1/projet-t1/public/assets/js/workorders.js"></script> --> <!-- Assurez-vous que ce fichier existe -->
    <script>
        function statut_label($status) {
            switch ($status) {
                case '1': return '<span class="status-badge status-1">En Attente</span>';
                case '2': return '<span class="status-badge status-2">En Cours</span>';
                case '3': return '<span class="status-badge status-3">Terminé</span>';
                default: return htmlspecialchars($status);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-edit-assignment').forEach(button => {
                button.addEventListener('click', function() {
                    const workOrderId = this.getAttribute('data-workorder-id');
                    const displayDiv = this.closest('.assignment-display');
                    const formDiv = document.getElementById(`assignment-form-${workOrderId}`);

                    if (displayDiv && formDiv) {
                        displayDiv.classList.replace('show-flex', 'hide-flex');
                        formDiv.classList.replace('hide-flex', 'show-flex');
                    }
                });
            });
        });

    </script>
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