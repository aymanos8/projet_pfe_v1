<?php

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Utilisateur.php';

class AuthController {

    /**
     * Affiche la page de connexion.
     */
    public function showLoginForm() {
        $error = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);
        require __DIR__ . '/../views/login.php';
    }

    /**
     * Affiche la page d'inscription.
     */
    public function showRegisterForm() {
        $error = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);
        require __DIR__ . '/../views/register.php';
    }

    /**
     * Gère la soumission du formulaire de connexion.
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = $_POST['login'] ?? ''; // Peut être email ou username
            $password = $_POST['password'] ?? '';

            if (empty($login) || empty($password)) {
                $_SESSION['error'] = "Veuillez saisir votre identifiant et votre mot de passe.";
                header('Location: /projet-pfe-v1/projet-t1/public/login');
                exit;
            }

            $cnx = Database::getInstance()->getConnection();
            $utilisateurModel = new Utilisateur($cnx);

            // Chercher l'utilisateur par email ou username
            $user = $utilisateurModel->findByEmailOrUsername($login);

            // Vérifier l'utilisateur et le mot de passe
            if ($user && $utilisateurModel->verifyPassword($password, $user['password'])) {
                // Authentification réussie
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['username'] = $user['username']; // Stocker aussi le username/email pour l'affichage

                // Définir la variable is_admin dans la session
                $_SESSION['is_admin'] = ($user['role'] === 'admin');

                // Redirection selon le rôle
                if ($user['role'] === 'admin') {
                    header('Location: /projet-pfe-v1/projet-t1/public/dashboard'); // Page admin
                } else {
                    header('Location: /projet-pfe-v1/projet-t1/public/workorders'); // Page utilisateur classique
                }
                exit;

            } else {
                // Authentification échouée
                $_SESSION['error'] = "Identifiant ou mot de passe incorrect.";
                header('Location: /projet-pfe-v1/projet-t1/public/login');
                exit;
            }
        }
         // Si la requête n'est pas POST, rediriger vers le formulaire de connexion
        header('Location: /projet-pfe-v1/projet-t1/public/login');
        exit;
    }

    /**
     * Gère la soumission du formulaire d'inscription.
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';

            if (empty($username) || empty($email) || empty($password) || empty($passwordConfirm)) {
                $_SESSION['error'] = "Veuillez remplir tous les champs.";
                 header('Location: /projet-pfe-v1/projet-t1/public/register');
                exit;
            }

            if ($password !== $passwordConfirm) {
                $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
                 header('Location: /projet-pfe-v1/projet-t1/public/register');
                exit;
            }

            $cnx = Database::getInstance()->getConnection();
            $utilisateurModel = new Utilisateur($cnx);

            // Vérifier si l'email ou le nom d'utilisateur existe déjà
            if ($utilisateurModel->findByEmailOrUsername($email) || $utilisateurModel->findByEmailOrUsername($username)) {
                 $_SESSION['error'] = "Cet email ou nom d'utilisateur est déjà utilisé.";
                 header('Location: /projet-pfe-v1/projet-t1/public/register');
                exit;
            }

            // Hacher le mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Créer l'utilisateur (par défaut rôle 'utilisateur')
            if ($utilisateurModel->createUser($username, $email, $hashedPassword, 'utilisateur')) {
                $_SESSION['success'] = "Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.";
                 header('Location: /projet-pfe-v1/projet-t1/public/login');
                exit;
            } else {
                 $_SESSION['error'] = "Une erreur est survenue lors de la création du compte.";
                 header('Location: /projet-pfe-v1/projet-t1/public/register');
                exit;
            }
        }
         // Si la requête n'est pas POST, rediriger vers le formulaire d'inscription
        header('Location: /projet-pfe-v1/projet-t1/public/register');
        exit;
    }

    /**
     * Gère la déconnexion de l'utilisateur.
     */
    public function logout() {
        // Détruire toutes les variables de session
        $_SESSION = array();

        // Si vous voulez détruire complètement la session, supprimez également
        // le cookie de session.
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finalement, détruire la session.
        session_destroy();

        // Rediriger vers la page de connexion
        header('Location: /projet-pfe-v1/projet-t1/public/login');
        exit;
    }

     /**
     * Vérifie si l'utilisateur est connecté.
     *
     * @return bool True si connecté, false sinon.
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

     /**
     * Obtient le rôle de l'utilisateur connecté.
     *
     * @return string|null Le rôle de l'utilisateur ou null si non connecté.
     */
    public static function getUserRole() {
        return $_SESSION['user_role'] ?? null;
    }

    /**
     * Obtient l'ID de l'utilisateur connecté.
     *
     * @return int|null L'ID de l'utilisateur ou null si non connecté.
     */
   public static function getUserId() {
       return $_SESSION['user_id'] ?? null;
   }
} 