<?php

class Utilisateur {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Récupère tous les utilisateurs de la base de données.
     *
     * @return array Retourne un tableau de tous les utilisateurs.
     */
    public function getAll() {
        try {
            $query = "SELECT id, username, email, role FROM users ORDER BY username";
            $stmt = $this->db->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de tous les utilisateurs : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère tous les utilisateurs ayant le rôle 'utilisateur'.
     *
     * @return array Retourne un tableau des utilisateurs non-admin.
     */
    public function getAllUsersOnly() {
        try {
            $query = "SELECT id, username FROM users WHERE role = 'utilisateur' ORDER BY username";
            $stmt = $this->db->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des utilisateurs non-admin : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Trouve un utilisateur par son email ou son nom d'utilisateur.
     *
     * @param string $login L'email ou le nom d'utilisateur.
     * @return array|false Retourne les données de l'utilisateur ou false si non trouvé.
     */
    public function findByEmailOrUsername($login) {
        $query = "SELECT * FROM users WHERE email = :login OR username = :login LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['login' => $login]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Vérifie le mot de passe fourni avec le mot de passe haché de l'utilisateur.
     *
     * @param string $password Le mot de passe en clair.
     * @param string $hashedPassword Le mot de passe haché de la base de données.
     * @return bool Retourne true si le mot de passe correspond, false sinon.
     */
    public function verifyPassword($password, $hashedPassword) {
        return password_verify($password, $hashedPassword);
    }

    /**
     * Trouve un utilisateur par son ID.
     *
     * @param int $id L'ID de l'utilisateur.
     * @return array|false Retourne les données de l'utilisateur ou false si non trouvé.
     */
    public function findById($id) {
        $query = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crée un nouvel utilisateur dans la base de données.
     *
     * @param string $username Le nom d'utilisateur.
     * @param string $email L'email de l'utilisateur.
     * @param string $hashedPassword Le mot de passe haché.
     * @param string $role Le rôle de l'utilisateur ('admin' ou 'utilisateur').
     * @return bool Retourne true en cas de succès, false sinon.
     */
    public function createUser($username, $email, $hashedPassword, $role = 'utilisateur') {
        $query = "INSERT INTO users (username, email, password, role) 
                 VALUES (:username, :email, :password, :role)";
        $stmt = $this->db->prepare($query);
        try {
            return $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => $role
            ]);
        } catch (PDOException $e) {
            // Gérer l'erreur, par exemple, logguer l'erreur
            error_log("Erreur lors de la création de l'utilisateur : " . $e->getMessage());
            return false;
        }
    }

    public function countAll() {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            error_log("Erreur lors du comptage des utilisateurs : " . $e->getMessage());
            return 0;
        }
    }

    // Vous pouvez ajouter ici d'autres méthodes comme createUser, updateUser, deleteUser, etc.
} 