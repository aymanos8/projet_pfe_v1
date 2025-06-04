<?php
// app/views/login.php

// Démarrer la session si ce n'est pas déjà fait
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les messages d'erreur ou de succès s'ils existent
$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
unset($_SESSION['error']);
unset($_SESSION['success']);

// Rediriger si déjà connecté
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: /projet-pfe-v1/projet-t1/public/dashboard');
    } else {
        header('Location: /projet-pfe-v1/projet-t1/public/workorders');
    }
    exit;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="/projet-pfe-v1/projet-t1/public/assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Connexion</h2>
        <?php if (isset($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form action="/projet-pfe-v1/projet-t1/public/login" method="POST">
            <div class="form-group">
                <label for="username">Nom d'utilisateur :</label>
                <input type="text" id="username" name="login" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-login">Se connecter</button>
        </form>
         <div class="mt-3">
            <p>Pas encore de compte ? <a href="/projet-pfe-v1/projet-t1/public/register">S'inscrire ici</a></p>
        </div>
    </div>
</body>
</html> 