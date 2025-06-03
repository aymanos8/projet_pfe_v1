<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Actions</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/projet-pfe-v1/projet-t1/public/assets/css/dashboard.css" rel="stylesheet"> <!-- Chemin corrigé -->
    <style>
        /* Styles pour surcharger les styles specifiques de page-historiques dans dashboard.css */
        .page-historiques .main-content {
            flex-grow: 1 !important;
            flex-shrink: 1 !important;
            flex-basis: auto !important; /* Ou 0, en fonction du comportement souhaité */
            width: auto !important;
            min-width: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            box-sizing: border-box !important;
        }

        .page-historiques .dashboard-content {
             padding: 30px !important; /* Rétablir le padding souhaité */
             flex-grow: 1 !important;
             width: 100% !important;
             box-sizing: border-box !important;
        }

        .page-historiques .dashboard-content > h2,
        .page-historiques .dashboard-content > .card,
        .page-historiques .dashboard-content > .row {
             margin-left: 0 !important;
             margin-right: 0 !important;
             padding-left: 0 !important; /* S'assurer que le padding est réinitialisé */
             padding-right: 0 !important; /* S'assurer que le padding est réinitialisé */
        }

         /* Styles pour le tableau et les badges si non inclus ou surchargés par dashboard.css */
        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .history-table th,
        .history-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .history-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .badge-success { background-color: #28a745; color: white; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-primary { background-color: #007bff; color: white; }
        .badge-secondary { background-color: #6c757d; color: white; }

    </style>
    <!-- Styles spécifiques pour la page d'historique -->
</head>
<body class="page-historiques">
    <div class="container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2>test</h2>
            </div>
            <ul class="nav-links">
                <li><a href="/projet-pfe-v1/projet-t1/public/dashboard"><i class="fas fa-home"></i> Vue d'ensemble</a></li>
                <li><a href="/projet-pfe-v1/projet-t1/public/workorders"><i class="fas fa-tasks"></i> Work-Orders</a></li>
                <li><a href="/projet-pfe-v1/projet-t1/public/equipements"><i class="fas fa-server"></i> Équipements</a></li>
                <li><a href="/projet-pfe-v1/projet-t1/public/configuration"><i class="fas fa-cogs"></i> Configurations</a></li>
                <li class="active"><a href="/projet-pfe-v1/projet-t1/public/historiques"><i class="fas fa-history"></i> Historiques</a></li>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
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
                         <?php if (isset($_SESSION['username'])): ?>
                            <span class="name"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            <span class="role"><?php echo htmlspecialchars(ucfirst($_SESSION['user_role'])); ?></span>
                        <?php else: ?>
                            <span class="name">Invité</span>
                            <span class="role">Non connecté</span>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <div class="dashboard-content">
                <h2 class="mb-4">Historique des Actions</h2>

                <!-- Filtres -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="/projet-pfe-v1/projet-t1/public/historiques" class="row g-3">
                            
                            <div class="col-md-3">
                                <label for="date_debut" class="form-label">Date début</label>
                                <input type="date" class="form-control" id="date_debut" name="date_debut" 
                                       value="<?php echo $_GET['date_debut'] ?? date('Y-m-d', strtotime('-30 days')); ?>">
                            </div>
                            
                            <div class="col-md-3">
                                <label for="date_fin" class="form-label">Date fin</label>
                                <input type="date" class="form-control" id="date_fin" name="date_fin" 
                                       value="<?php echo $_GET['date_fin'] ?? date('Y-m-d'); ?>">
                            </div>
                            
                            <div class="col-md-3">
                                <label for="entite_type" class="form-label">Type d'entité</label>
                                <select class="form-select" id="entite_type" name="entite_type">
                                    <option value="">Tous</option>
                                    <option value="workorder" <?php echo ($_GET['entite_type'] ?? '') === 'workorder' ? 'selected' : ''; ?>>Work Order</option>
                                    <option value="equipement" <?php echo ($_GET['entite_type'] ?? '') === 'equipement' ? 'selected' : ''; ?>>Équipement</option>
                                    <option value="user" <?php echo ($_GET['entite_type'] ?? '') === 'user' ? 'selected' : ''; ?>>Utilisateur</option>
                                    <option value="configuration" <?php echo ($_GET['entite_type'] ?? '') === 'configuration' ? 'selected' : ''; ?>>Configuration</option>
                                </select>
                            </div>

                             <!-- Champ pour l'ID de l'entité si un type est sélectionné -->
                             <!-- Peut être ajouté via JS pour un meilleur UX -->
                            
                            <div class="col-md-3">
                                <label for="user_id" class="form-label">Utilisateur</label>
                                <select class="form-select" id="user_id" name="user_id">
                                    <option value="">Tous</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?php echo $user['id']; ?>" <?php echo (isset($_GET['user_id']) && (int)$_GET['user_id'] === (int)$user['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($user['username']); ?> <!-- Ou $user['prenom'] . ' ' . $user['nom'] si ces colonnes existent -->
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Filtrer</button>
                                <a href="/projet-pfe-v1/projet-t1/public/historiques" class="btn btn-secondary">Réinitialiser</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tableau des actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="history-table table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Utilisateur</th>
                                        <th>Action</th>
                                        <th>Entité</th>
                                        <th>Détails</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($historique)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Aucune action trouvée</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($historique as $action): ?>
                                            <tr>
                                                <td><?php echo date('d/m/Y H:i', strtotime($action['date_action'])); ?></td>
                                                <td><?php echo htmlspecialchars($action['prenom'] . ' ' . $action['nom']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo match($action['action_type']) {
                                                            'affectation' => 'success',
                                                            'desaffectation' => 'warning',
                                                            'configuration' => 'info',
                                                            'creation' => 'primary',
                                                            'modification' => 'secondary',
                                                            'suppression' => 'danger',
                                                            default => 'secondary'
                                                        };
                                                    ?>">
                                                        <?php echo ucfirst($action['action_type']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php echo ucfirst($action['entite_type']); ?> #<?php echo $action['entite_id']; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($action['details']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <!-- Si vous avez un fichier script.js spécifique à votre projet, incluez-le ici -->
    <!-- <script src="/projet-pfe-v1/projet-t1/public/assets/js/script.js"></script> -->

    <script>
        // Script pour gérer l'affichage conditionnel du champ Entite ID si nécessaire
        // Ou pour toute autre interactivité sur la page
    </script>

</body>
</html> 