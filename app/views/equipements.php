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
    <title>Liste des Équipements Réseau</title>
    <link rel="stylesheet" href="/projet-pfe-v1/projet-t1/public/assets/css/common.css">
    <link rel="stylesheet" href="/projet-pfe-v1/projet-t1/public/assets/css/equipements.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="page-dashboard">
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
                </div>
            </header>
            <div class="dashboard-content">
                <h2>Gestion des équipements</h2>
                <?php if (AuthController::isLoggedIn() && AuthController::getUserRole() === 'admin'): ?>
                <a href="/projet-pfe-v1/projet-t1/public/equipements/ajouter" class="btn btn-primary" style="margin-bottom: 20px;">Ajouter un équipement</a>
                <?php endif; ?>

                <!-- Le formulaire d'ajout d'équipement a été déplacé vers add_equipement.php -->

                <table id="equipements-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Marque</th>
                            <th>Modèle</th>
                            <th>Gamme</th>
                            <th>Disponibilité</th>
                            <?php if (AuthController::isLoggedIn() && AuthController::getUserRole() === 'admin'): ?>
                                <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (
                            isset($equipements) ? $equipements : [] as $equipement): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($equipement['id']); ?></td>
                            <td><?php echo htmlspecialchars($equipement['marque']); ?></td>
                            <td><?php echo htmlspecialchars($equipement['modele']); ?></td>
                            <td><?php echo htmlspecialchars($equipement['gamme']); ?></td>
                            <td>
                                <?php
                                // Utiliser les classes .status-badge
                                switch ($equipement['statut']) {
                                    case 'disponible':
                                        echo '<span class="status-badge disponible">Disponible</span>';
                                        break;
                                    case 'indisponible':
                                        echo '<span class="status-badge indisponible">Indisponible</span>';
                                        break;
                                    default:
                                        // Encapsuler également le statut par défaut dans un span si nécessaire
                                        echo '<span class="status-badge">' . htmlspecialchars($equipement['statut']) . '</span>';
                                }
                                ?>
                            </td>
                            <?php if (AuthController::isLoggedIn() && AuthController::getUserRole() === 'admin'): ?>
                                <td>
                                    <?php if ($equipement['statut'] === 'disponible'): ?>
                                        <a href="/projet-pfe-v1/projet-t1/public/equipements/set-indisponible/<?php echo htmlspecialchars($equipement['id']); ?>" class="btn btn-warning btn-sm">Rendre Indisponible</a>
                                    <?php elseif ($equipement['statut'] === 'indisponible'): ?>
                                         <a href="/projet-pfe-v1/projet-t1/public/equipements/set-disponible/<?php echo htmlspecialchars($equipement['id']); ?>" class="btn btn-success btn-sm">Rendre Disponible</a>
                                    <?php endif; ?>
                                    <a href="/projet-pfe-v1/projet-t1/public/equipements/supprimer/<?php echo htmlspecialchars($equipement['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet équipement ?');">Supprimer</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <!-- Le script JavaScript pour afficher/masquer le formulaire a été supprimé car le formulaire est sur une autre page -->
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