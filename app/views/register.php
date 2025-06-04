<?php
// app/views/register.php

// Démarrer la session si ce n'est pas déjà fait
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les messages d'erreur ou de succès s'ils existent
$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
unset($_SESSION['error']);
unset($_SESSION['success']);

// Rediriger si déjà connecté (facultatif, selon si on veut qu'un user connecté puisse s'inscrire)
// if (isset($_SESSION['user_id'])) {
//     header('Location: /projet-pfe-v1/projet-t1/public/dashboard'); // ou une autre page
//     exit;
// }

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="/projet-pfe-v1/projet-t1/public/assets/css/register.css">
</head>
<body>
    <div class="register-container">
        <h2>Inscription</h2>
        <?php if (isset($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form action="/projet-pfe-v1/projet-t1/public/register" method="POST">
            <div class="form-group">
                <label for="username">Nom d'utilisateur :</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="password_confirm">Confirmer le mot de passe</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>
            <button type="submit" class="btn-register">S'inscrire</button>
        </form>
        <div class="mt-3">
             <p>Déjà un compte ? <a href="/projet-pfe-v1/projet-t1/public/login">Se connecter ici</a></p>
        </div>
    </div>
</body>
</html> 