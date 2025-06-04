<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un équipement</title>
    <link rel="stylesheet" href="/projet-pfe-v1/projet-t1/public/assets/css/common.css">
    <link rel="stylesheet" href="/projet-pfe-v1/projet-t1/public/assets/css/add_equipement.css">
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
                if (isset($_GET['error'])) {
                    $error_message = '';
                    if ($_GET['error'] === 'equipement_exists') {
                        $error_message = 'Erreur : Un équipement avec ce modèle existe déjà.';
                    } elseif ($_GET['error'] === 'required_fields_missing') {
                        $error_message = 'Erreur : Veuillez remplir tous les champs requis.';
                    } elseif ($_GET['error'] === 'db_error') {
                         $error_message = 'Erreur : Une erreur est survenue lors de l\'ajout de l\'équipement dans la base de données.';
                    }

                    if ($error_message !== '') {
                        echo '<div class="alert alert-danger">' . htmlspecialchars($error_message) . '</div>';
                    }
                }
                ?>
                <h2>Ajouter un nouvel équipement</h2>

                <div id="add-equipement-form-container">
                    <form id="new-equipement-form" action="/projet-pfe-v1/projet-t1/public/equipements/ajouter" method="POST">
                        <div class="form-group">
                            <label for="modele" class="form-label">Modèle:</label>
                            <input type="text" class="form-control" id="modele" name="modele" required>
                        </div>
                        <div class="form-group">
                            <label for="marque" class="form-label">Marque:</label>
                            <input type="text" class="form-control" id="marque" name="marque" required>
                        </div>
                        
                         <div class="form-group">
                            <label for="gamme" class="form-label">Gamme:</label>
                            <input type="text" class="form-control" id="gamme" name="gamme">
                        </div>
                         <div class="form-group">
                            <label for="technology" class="form-label">Technologie:</label>
                            <select class="form-control" id="technology" name="technology[]" multiple>
                                <option value="4G">4G</option>
                                <option value="Ethernet">Ethernet</option>
                                <option value="FO">FO</option>
                                <option value="xDSL">xDSL</option>
                                <!-- Ajoutez d'autres options de technologie si nécessaire -->
                            </select>
                        </div>
                         <div class="form-group">
                            <label for="offre" class="form-label">Offre:</label>
                             <select class="form-control" id="offre" name="offre[]" multiple>
                                <option value="Internet">Internet</option>
                                <option value="VPN">VPN</option>
                                <option value="Voix">Voix</option>
                                <!-- Ajoutez d'autres options d'offre si nécessaire -->
                            </select>
                        </div>
                         <div class="form-group">
                            <label for="debit" class="form-label">Débit:</label>
                            <input type="text" class="form-control" id="debit" name="debit">
                        </div>
                        
                        
                        <div class="form-group">
                            <label for="statut" class="form-label">Statut:</label>
                            <select class="form-control" id="statut" name="statut">
                                <option value="disponible">Disponible</option>
                                <option value="indisponible">Indisponible</option>
                            </select>
                        </div>

                        <!-- Le statut sera par défaut 'disponible' lors de l'ajout via le contrôleur -->
                        <button type="submit" class="btn btn-success">Ajouter l'équipement</button>
                        <a href="/projet-pfe-v1/projet-t1/public/equipements" class="btn btn-secondary">Annuler</a>
                    </form>
                </div>

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