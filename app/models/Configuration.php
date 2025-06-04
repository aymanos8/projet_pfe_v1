<?php

require_once __DIR__ . '/../core/Database.php';

class Configuration
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Enregistre une nouvelle configuration dans la base de données.
     *
     * @param int $equipementId L'ID de l'équipement associé.
     * @param int $generatedByUserId L'ID de l'utilisateur qui a généré la configuration.
     * @param string $contenuConfiguration Le contenu texte de la configuration.
     * @param string $status Le statut de la configuration (par défaut 'draft').
     * @return bool Retourne true en cas de succès, false sinon.
     */
    public function saveConfiguration($equipementId, $generatedByUserId, $contenuConfiguration, $status = 'draft')
    {
        $sql = "INSERT INTO configurations (equipement_id, generated_by_user_id, contenu_configuration, status) 
                VALUES (:equipement_id, :generated_by_user_id, :contenu_configuration, :status)";
        
        $stmt = $this->db->prepare($sql);
        
        try {
            $success = $stmt->execute([
                ':equipement_id' => $equipementId,
                ':generated_by_user_id' => $generatedByUserId,
                ':contenu_configuration' => $contenuConfiguration,
                ':status' => $status
            ]);

            // Retourne l'ID de la dernière insertion si l'exécution a réussi
            if ($success) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
            
        } catch (PDOException $e) {
            error_log("Erreur lors de l'enregistrement de la configuration : " . $e->getMessage());
            return false;
        }
    }

    // Vous pouvez ajouter d'autres méthodes ici (get config by id, get by equipement, etc.)
} 