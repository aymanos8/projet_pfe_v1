<?php
require_once __DIR__ . '/app/core/Database.php';

try {
    $cnx = Database::getInstance()->getConnection();

    $username = 'admin'; // Choisissez un nom d'utilisateur
    $email = 'admin@gmail.com'; // Choisissez un email
    $password = 'admin'; // Choisissez un mot de passe FORT

    // Hachage du mot de passe
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'admin')";
    $stmt = $cnx->prepare($query);

    $stmt->execute([
        'username' => $username,
        'email' => $email,
        'password' => $hashedPassword
    ]);

    echo "Utilisateur admin '{$username}' ajouté avec succès.\n";

} catch(PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage() . "\n";
} catch(Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
?> 