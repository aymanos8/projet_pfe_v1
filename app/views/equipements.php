<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des équipements</title>
    <link rel="stylesheet" href="/projet-pfe-v1/projet-t1/public/assets/css/equipements.css">
</head>
<body>
    <div class="wrapper">
        <nav class="sidebar">
            <h2>Network Order</h2>
            <ul>
                <li class="active"><a href="/equipements">Équipements</a></li>
                <li><a href="/workorders">Workorders</a></li>
                <li><a href="/dashboard">Dashboard</a></li>
                <!-- Ajoutez d'autres liens ici -->
            </ul>
        </nav>
        <main class="main-content">
            <div class="container">
                <h1>Gestion des équipements</h1>
                <input class="search-bar" type="text" placeholder="Rechercher par type ou modèle...">
                <button class="btn-add">+ Ajouter un équipement</button>
                <h2 style="margin-top: 48px;">Équipements disponibles</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Modèle</th>
                            <th>Capacité</th>
                            <th>Disponibilité</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Router</td>
                            <td>Cisco ASR 1001-X</td>
                            <td>2.5 Gbps</td>
                            <td><span class="badge-dispo">Disponible</span></td>
                            <td>
                                <button class="action-btn edit">✏️</button>
                                <button class="action-btn delete">🗑️</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Router</td>
                            <td>Juniper MX104</td>
                            <td>80 Gbps</td>
                            <td><span class="badge-indispo">Non disponible</span></td>
                            <td>
                                <button class="action-btn edit">✏️</button>
                                <button class="action-btn delete">🗑️</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Switch</td>
                            <td>Cisco Catalyst 9300</td>
                            <td>1 Gbps (48 ports)</td>
                            <td><span class="badge-dispo">Disponible</span></td>
                            <td>
                                <button class="action-btn edit">✏️</button>
                                <button class="action-btn delete">🗑️</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Router</td>
                            <td>Cisco ISR 4431</td>
                            <td>1 Gbps</td>
                            <td><span class="badge-dispo">Disponible</span></td>
                            <td>
                                <button class="action-btn edit">✏️</button>
                                <button class="action-btn delete">🗑️</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Switch</td>
                            <td>Arista 7050X3</td>
                            <td>10 Gbps (48 ports)</td>
                            <td><span class="badge-indispo">Non disponible</span></td>
                            <td>
                                <button class="action-btn edit">✏️</button>
                                <button class="action-btn delete">🗑️</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script src="/projet-pfe-v1/projet-t1/public/assets/js/equipements.js"></script>
</body>
</html> 