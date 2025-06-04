<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>test</title>
    <link rel="stylesheet" href="/projet-pfe-v1/projet-t1/public/assets/css/common.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                <li><a href="/projet-pfe-v1/projet-t1/public/workorders"><i class="fas fa-tasks"></i> Work-Orders</a></li>
                <li><a href="/projet-pfe-v1/projet-t1/public/equipements"><i class="fas fa-server"></i> Équipements</a></li>
                <li class="active"><a href="/projet-pfe-v1/projet-t1/public/configuration"><i class="fas fa-cogs"></i> Configurations</a></li>
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
                        <div class="dropdown-item">Se déconnecter</div>
                    </div>
                </div>
            </header>
            <section class="content-section" style="max-width:1100px;margin:32px auto 0 auto;">
                <?php
                if (isset($_SESSION['error'])) {
                    $errorMessage = str_replace('<br>', ' ', $_SESSION['error']);
                    echo '<div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 16px; border-radius: 8px; margin: 0 auto 24px auto; border: 1px solid #f5c6cb; max-width: 900px; font-weight: 500; display: flex; align-items: center; gap: 10px;">';
                    echo '<i class="fas fa-exclamation-circle" style="font-size: 1.2em;"></i>';
                    echo htmlspecialchars($errorMessage);
                    echo '</div>';
                    unset($_SESSION['error']);
                }
                ?>
                <div class="form-card" style="background:#fff;box-shadow:0 2px 12px rgba(30,40,90,0.07);border-radius:16px;padding:40px 32px 32px 32px;max-width:900px;margin:auto;">
                    <h1 class="form-title" style="font-size:2rem;font-weight:700;margin-bottom:32px;display:flex;align-items:center;gap:12px;"><i class="fas fa-cogs"></i> Générateur de configuration Cisco</h1>
                    <form action="/projet-pfe-v1/projet-t1/public/configuration/generer" method="post" id="ciscoForm">
                         <?php if (isset($equipement) && $equipement !== null): ?>
                            <input type="hidden" name="equipement_id" value="<?php echo htmlspecialchars($equipement['id']); ?>">
                        <?php endif; ?>
                        <div class="form-row" style="display: flex; gap: 48px; flex-wrap: wrap; align-items: flex-start;">
                            <div class="form-col" style="flex:1; min-width:320px;">
                                <div class="form-group" style="margin-bottom:20px;">
                                    <label style="font-weight:500;">Nom du routeur <span style="color: red;">*</span></label>
                                    <input type="text" name="ROUTER_HOSTNAME" id="ROUTER_HOSTNAME" class="input" style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid #d1d5db;margin-top:6px;"
                                           value="<?php echo htmlspecialchars($equipement['modele'] ?? $formData['ROUTER_HOSTNAME'] ?? ''); ?>"
                                           required
                                    >
                                </div>
                                <div class="form-group" style="margin-bottom:20px;">
                                    <label style="font-weight:500;">Utilisateur admin <span style="color: red;">*</span></label>
                                    <input type="text" name="ADMIN_USER" id="ADMIN_USER" class="input" style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid #d1d5db;margin-top:6px;" required value="<?php echo htmlspecialchars($formData['ADMIN_USER'] ?? ''); ?>">
                                </div>
                                <div class="form-group" style="margin-bottom:20px;">
                                    <label style="font-weight:500;">Mot de passe admin <span style="color: red;">*</span></label>
                                    <input type="text" name="ADMIN_PASS" id="ADMIN_PASS" class="input" style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid #d1d5db;margin-top:6px;" required value="<?php echo htmlspecialchars($formData['ADMIN_PASS'] ?? ''); ?>">
                                </div>
                                <div class="form-group" style="margin-bottom:20px;">
                                    <label style="font-weight:500;">Interface management <span style="color: red;">*</span></label>
                                    <input type="text" name="INTERFACE_MGMT" id="INTERFACE_MGMT" class="input" style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid #d1d5db;margin-top:6px;" required value="<?php echo htmlspecialchars($formData['INTERFACE_MGMT'] ?? ''); ?>">
                                </div>
                                <div class="form-group" style="margin-bottom:20px;">
                                    <label style="font-weight:500;">Gateway management <span style="color: red;">*</span></label>
                                    <input type="text" name="GATEWAY_MGMT" id="GATEWAY_MGMT" class="input" style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid #d1d5db;margin-top:6px;" required value="<?php echo htmlspecialchars($formData['GATEWAY_MGMT'] ?? ''); ?>">
                                </div>
                                <div class="form-group" style="margin-bottom:20px;">
                                    <label style="font-weight:500;">Masque WAN <span style="color: red;">*</span></label>
                                    <input type="text" name="WAN_MASK" id="WAN_MASK" class="input" style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid #d1d5db;margin-top:6px;" required oninput="calculerWildcard()" value="<?php echo htmlspecialchars($formData['WAN_MASK'] ?? ''); ?>">
                                </div>
                                <div class="form-group" style="margin-bottom:20px;">
                                    <label style="font-weight:500;">MGMT1 <span style="color: red;">*</span></label>
                                    <input type="text" name="MGMT1" id="MGMT1" class="input" style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid #d1d5db;margin-top:6px;" required value="<?php echo htmlspecialchars($formData['MGMT1'] ?? ''); ?>">
                                </div>
                                <div class="form-group" style="margin-bottom:20px;">
                                    <label style="font-weight:500;">MGMT2 <span style="color: red;">*</span></label>
                                    <input type="text" name="MGMT2" id="MGMT2" class="input" style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid #d1d5db;margin-top:6px;" required value="<?php echo htmlspecialchars($formData['MGMT2'] ?? ''); ?>">
                                </div>
                                <div class="form-group" style="margin-bottom:20px;">
                                    <label style="font-weight:500;">MGMT3 <span style="color: red;">*</span></label>
                                    <input type="text" name="MGMT3" id="MGMT3" class="input" style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid #d1d5db;margin-top:6px;" required value="<?php echo htmlspecialchars($formData['MGMT3'] ?? ''); ?>">
                                </div>
                                <div class="form-group" style="margin-bottom:20px;">
                                    <label style="font-weight:500;">WILDWARD <span style="color: red;">*</span></label>
                                    <input type="text" name="WILDWARD" id="WILDWARD" class="input" style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid #d1d5db;margin-top:6px; background-color: #f3f4f6;" required readonly value="<?php echo htmlspecialchars($formData['WILDWARD'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-col" style="flex:1; min-width:320px; max-width:480px;">
                                <div class="form-group" style="margin-bottom:32px;">
                                    <label style="font-weight:600;font-size:1.1rem;display:flex;align-items:center;gap:8px;"><i class="fas fa-network-wired"></i> Interfaces réseau dynamiques</label>
                                    <div id="interfaces" style="display:flex;flex-direction:column;gap:10px;margin-top:10px;overflow-x:auto;"></div>
                                    <button type="button" class="btn btn-success" onclick="addInterface()" style="margin-top:10px;padding:8px 18px;border-radius:8px;font-weight:600;font-size:1rem;display:inline-flex;align-items:center;gap:6px;"><i class="fas fa-plus"></i> Ajouter une interface</button>
                                </div>
                                <div class="form-group" style="margin-bottom:32px;">
                                    <label style="font-weight:600;font-size:1.1rem;display:flex;align-items:center;gap:8px;"><i class="fas fa-shield-alt"></i> ACLs supplémentaires</label>
                                    <div id="acls" style="display:flex;flex-direction:column;gap:10px;margin-top:10px;overflow-x:auto;"></div>
                                    <button type="button" class="btn btn-success" onclick="addAcl()" style="margin-top:10px;padding:8px 18px;border-radius:8px;font-weight:600;font-size:1rem;display:inline-flex;align-items:center;gap:6px;"><i class="fas fa-plus"></i> Ajouter une ACL</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions" style="margin-top:40px; display:flex; gap:20px; justify-content:center;">
                            <button type="button" class="btn btn-warning" onclick="remplirExemple()" style="padding:10px 28px;border-radius:8px;font-weight:600;font-size:1.1rem;display:flex;align-items:center;gap:8px;"><i class="fas fa-lightbulb"></i> Exemple</button>
                            <button type="submit" class="btn btn-primary" style="padding:10px 28px;border-radius:8px;font-weight:600;font-size:1.1rem;display:flex;align-items:center;gap:8px;"><i class="fas fa-cog"></i> Générer la configuration</button>
                        </div>
                    </form>
                </div>
                <?php if ($config): ?>
                    <div class="config-card" style="margin-top:40px; max-width:900px; margin-left:auto; margin-right:auto; background:#fff; border-radius:12px; box-shadow:0 2px 12px rgba(30,40,90,0.07);">
                        <div class="config-header" style="padding:18px 24px; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:1.1rem; background:#f1f5f9; border-radius:12px 12px 0 0;">
                            <i class="fas fa-file-code"></i> Configuration générée
                        </div>
                        <div class="config-body" style="padding:24px;">
                            <button class="btn btn-outline-dark btn-sm" onclick="copyConfig(event)" style="float:right;margin-bottom:8px;"><i class="fas fa-copy"></i> Copier</button>
                            <pre id="configOutput" style="background:#1e293b;color:#fff;padding:18px;border-radius:10px;overflow-x:auto; margin-top:8px;"><?php echo htmlspecialchars($config); ?></pre>
                        </div>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
    <script>
    let interfaceIndex = 0;
    function addInterface() {
        const div = document.createElement('div');
        div.className = 'dyn-block';
        div.style.display = 'grid';
        div.style.gridTemplateColumns = '1fr 1fr 1fr 1fr auto';
        div.style.gap = '8px';
        div.style.alignItems = 'center';
        div.innerHTML = `
            <input type="text" name="INTERFACES[${interfaceIndex}][NOM_INTERFACE]" placeholder="Nom (ex: GigabitEthernet0/1)" style="padding:8px;border-radius:6px;border:1px solid #d1d5db;width:100%;">
            <input type="text" name="INTERFACES[${interfaceIndex}][DESCRIPTION]" placeholder="Description" style="padding:8px;border-radius:6px;border:1px solid #d1d5db;width:100%;">
            <input type="text" name="INTERFACES[${interfaceIndex}][LAN_IP]" placeholder="IP" style="padding:8px;border-radius:6px;border:1px solid #d1d5db;width:100%;">
            <input type="text" name="INTERFACES[${interfaceIndex}][LAN_MASK]" placeholder="Masque" style="padding:8px;border-radius:6px;border:1px solid #d1d5db;width:100%;">
            <button type="button" class="btn btn-outline-danger" onclick="this.parentNode.remove();" style="padding:8px 12px;border-radius:6px;"><i class="fas fa-trash"></i></button>
        `;
        document.getElementById('interfaces').appendChild(div);
        interfaceIndex++;
    }

    let aclIndex = 0;
    function addAcl() {
        const div = document.createElement('div');
        div.className = 'dyn-block';
        div.style.display = 'grid';
        div.style.gridTemplateColumns = '1fr 3fr auto';
        div.style.gap = '8px';
        div.style.alignItems = 'center';
        div.innerHTML = `
            <input type="number" name="ACLS_DYN[${aclIndex}][num]" placeholder="Numéro (ex: 90)" style="padding:8px;border-radius:6px;border:1px solid #d1d5db;width:100%;">
            <input type="text" name="ACLS_DYN[${aclIndex}][value]" placeholder="IP ou valeur" style="padding:8px;border-radius:6px;border:1px solid #d1d5db;width:100%;">
            <button type="button" class="btn btn-outline-danger" onclick="this.parentNode.remove();" style="padding:8px 12px;border-radius:6px;"><i class="fas fa-trash"></i></button>
        `;
        document.getElementById('acls').appendChild(div);
        aclIndex++;
    }

    function remplirExemple() {
        document.getElementById('ROUTER_HOSTNAME').value = "ROUTER-PFE-01";
        document.getElementById('ADMIN_USER').value = "admin";
        document.getElementById('ADMIN_PASS').value = "Admin@123";
        document.getElementById('INTERFACE_MGMT').value = "Vlan37";
        document.getElementById('GATEWAY_MGMT').value = "172.23.64.1";
        document.getElementById('WAN_MASK').value = "255.255.255.0";
        document.getElementById('MGMT1').value = "172.23.34.1";
        document.getElementById('MGMT2').value = "172.23.34.2";
        document.getElementById('MGMT3').value = "172.23.34.3";
        document.getElementById('WILDWARD').value = "0.0.0.255";
        
        calculerWildcard();
        
        document.getElementById('interfaces').innerHTML = '';
        interfaceIndex = 0;
        const interfaces = [
            { nom: "GigabitEthernet0/2", desc: "WAN", ip: "172.23.34.0", mask: "255.255.255.0" },
            { nom: "GigabitEthernet0/3", desc: "LAN", ip: "192.168.10.1", mask: "255.255.255.0" }
        ];
        interfaces.forEach((iface, idx) => {
            addInterface();
            document.querySelector(`input[name='INTERFACES[${idx}][NOM_INTERFACE]']`).value = iface.nom;
            document.querySelector(`input[name='INTERFACES[${idx}][DESCRIPTION]']`).value = iface.desc;
            document.querySelector(`input[name='INTERFACES[${idx}][LAN_IP]']`).value = iface.ip;
            document.querySelector(`input[name='INTERFACES[${idx}][LAN_MASK]']`).value = iface.mask;
        });

        document.getElementById('acls').innerHTML = '';
        aclIndex = 0;
        const acls = [
            { num: 90, value: "192.168.100.1" }
        ];
        acls.forEach((acl, idx) => {
            addAcl();
            document.querySelector(`input[name='ACLS_DYN[${idx}][num]']`).value = acl.num;
            document.querySelector(`input[name='ACLS_DYN[${idx}][value]']`).value = acl.value;
        });
    }

    function copyConfig(e) {
        e.preventDefault();
        const text = document.getElementById('configOutput').innerText;
        navigator.clipboard.writeText(text);
        const btn = e.currentTarget;
        btn.innerHTML = '<i class="fas fa-check"></i> Copié!';
        setTimeout(() => { btn.innerHTML = '<i class="fas fa-copy"></i> Copier'; }, 1500);
    }

    const wildcardTable = {
        "255.255.255.255": "0.0.0.0",
        "255.255.255.254": "0.0.0.1",
        "255.255.255.252": "0.0.0.3",
        "255.255.255.248": "0.0.0.7",
        "255.255.255.240": "0.0.0.15",
        "255.255.255.224": "0.0.0.31",
        "255.255.255.192": "0.0.0.63",
        "255.255.255.128": "0.0.0.127",
        "255.255.255.0": "0.0.0.255",
        "255.255.254.0": "0.0.1.255",
        "255.255.252.0": "0.0.3.255",
        "255.255.248.0": "0.0.7.255",
        "255.255.240.0": "0.0.15.255",
        "255.255.224.0": "0.0.31.255",
        "255.255.192.0": "0.0.63.255",
        "255.255.128.0": "0.0.127.255",
        "255.255.0.0": "0.0.255.255",
        "255.254.0.0": "0.1.255.255",
        "255.252.0.0": "0.3.255.255",
        "255.248.0.0": "0.7.255.255",
        "255.240.0.0": "0.15.255.255",
        "255.224.0.0": "0.31.255.255",
        "255.192.0.0": "0.63.255.255",
        "255.128.0.0": "0.127.255.255",
        "255.0.0.0": "0.255.255.255",
        "254.0.0.0": "1.255.255.255",
        "252.0.0.0": "3.255.255.255",
        "248.0.0.0": "7.255.255.255",
        "240.0.0.0": "15.255.255.255",
        "224.0.0.0": "31.255.255.255",
        "192.0.0.0": "63.255.255.255",
        "128.0.0.0": "127.255.255.255",
        "0.0.0.0": "255.255.255.255"
    };

    function calculerWildcard() {
        const wanMaskInput = document.getElementById('WAN_MASK');
        const wildcardInput = document.getElementById('WILDWARD');
        const wanMaskValue = wanMaskInput.value.trim();

        if (wildcardTable[wanMaskValue]) {
            wildcardInput.value = wildcardTable[wanMaskValue];
        } else {
            wildcardInput.value = '';
        }
    }

    calculerWildcard();
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