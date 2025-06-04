<?php
class Equipement {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllEquipements() {
        $query = "SELECT * FROM equipements_reseau ORDER BY date_ajout DESC";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEquipementById($id) {
        $query = "SELECT * FROM equipements_reseau WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getEquipementsDisponibles() {
        $query = "SELECT * FROM equipements_reseau WHERE statut = 'disponible' ORDER BY modele";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addEquipement($modele, $marque, $gamme, $technology, $offre, $debit) {
        $query = "INSERT INTO equipements_reseau (modele, marque, gamme, technology, offre, debit) 
                 VALUES (:modele, :marque, :gamme, :technology, :offre, :debit)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'modele' => $modele,
            'marque' => $marque,
            'gamme' => $gamme,
            'technology' => $technology,
            'offre' => $offre,
            'debit' => $debit
        ]);
    }

    public function updateEquipement($id, $data) {
        $query = "UPDATE equipements_reseau SET 
                 modele = :modele,
                 marque = :marque,
                 gamme = :gamme,
                 statut = :statut,
                 technology = :technology,
                 offre = :offre,
                 debit = :debit
                 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        // Filtrer les clés non existantes dans $data avant de fusionner
        $allowed_keys = ['modele', 'marque', 'gamme', 'statut', 'technology', 'offre', 'debit'];
        $filtered_data = array_intersect_key($data, array_flip($allowed_keys));
        return $stmt->execute(array_merge($filtered_data, ['id' => $id]));
    }

    public function affecterEquipement($work_order_id, $equipement_id) {
        // Vérifier si l'équipement est disponible
        $equipement = $this->getEquipementById($equipement_id);
        if ($equipement['statut'] !== 'disponible') {
            return false; // L'équipement n'est pas disponible
        }

        // Vérifier si l'affectation existe déjà
        $checkQuery = "SELECT COUNT(*) FROM affectations_workorders WHERE work_order_id = :work_order_id AND equipement_id = :equipement_id";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([
            'work_order_id' => $work_order_id,
            'equipement_id' => $equipement_id
        ]);
        if ($checkStmt->fetchColumn() > 0) {
            // L'affectation existe déjà
            // error_log("DEBUG: Affectation Work Order #" . $work_order_id . " Equipement #" . $equipement_id . " already exists.");
            return false; // Indique que l'affectation n'a pas été effectuée (car déjà existante)
        }

        try {
            // Créer l'affectation
            $query = "INSERT INTO affectations_workorders (work_order_id, equipement_id) 
                     VALUES (:work_order_id, :equipement_id)";
            $stmt = $this->db->prepare($query);
            $success = $stmt->execute([
                'work_order_id' => $work_order_id,
                'equipement_id' => $equipement_id
            ]);

            // Retourner true si l'insertion a réussi, false sinon
            return $success;

        } catch (Exception $e) {
            error_log("Erreur DB dans Equipement::affecterEquipement lors de l'insertion : " . $e->getMessage());
            return false;
        }
    }

    public function getEquipementsByWorkOrder($work_order_id) {
        $query = "SELECT e.* FROM equipements_reseau e
                 INNER JOIN affectations_workorders a ON e.id = a.equipement_id
                 WHERE a.work_order_id = :work_order_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['work_order_id' => $work_order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un équipement compatible disponible pour l'affectation automatique (limite 1).
     *
     * @param string $technology La ou les technologies requises (séparées par des virgules si plusieurs).
     * @param string $offre La ou les offres requises (séparées par des virgules si plusieurs).
     * @param float $requiredDebit Le débit requis pour l'équipement.
     * @return array|false Retourne un équipement compatible disponible sous forme de tableau associatif ou false si non trouvé.
     */
    public function getEquipementCompatible($technology, $offre, $requiredDebit)
    {
        try {
            // Séparer les technologies et offres requises du Work Order en tableaux
            $requiredTechs = array_map('trim', explode(',', $technology));
            $requiredOffres = array_map('trim', explode(',', $offre));

            // Construction dynamique de la clause WHERE pour vérifier l'intersection des listes et le débit
            $whereClauses = ["statut = 'disponible'"];
            $params = [];

            // Clause pour les technologies (l'équipement doit supporter au moins une technologie requise)
            if (!empty($requiredTechs)) {
                $techConditions = [];
                foreach ($requiredTechs as $tech) {
                    $techConditions[] = "FIND_IN_SET(?, technology)";
                    $params[] = $tech;
                }
                $whereClauses[] = "(" . implode(" OR ", $techConditions) . ")";
            }

            // Clause pour les offres (l'équipement doit supporter au moins une offre requise)
            if (!empty($requiredOffres)) {
                $offreConditions = [];
                foreach ($requiredOffres as $offreItem) {
                    $offreConditions[] = "FIND_IN_SET(?, offre)";
                    $params[] = $offreItem;
                }
                $whereClauses[] = "(" . implode(" OR ", $offreConditions) . ")";
            }

            // Clause pour le débit (l'équipement doit supporter un débit >= au débit requis)
            $requiredDebitNum = is_numeric($requiredDebit) ? (int) $requiredDebit : 0;

            if ($requiredDebitNum > 0) {
                 $whereClauses[] = "CAST(debit AS UNSIGNED) >= ?";
                 $params[] = $requiredDebitNum;
            }

            // Combiner toutes les clauses WHERE avec AND et ajouter la limite 1
            $query = "SELECT * FROM equipements_reseau WHERE " . implode(" AND ", $whereClauses) . " ORDER BY date_ajout ASC LIMIT 1";

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Erreur lors de la recherche d'équipement compatible : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère les équipements disponibles qui sont compatibles avec une technologie et une offre spécifiques.
     *
     * @param string $technology La ou les technologies requises (séparées par des virgules si plusieurs).
     * @param string $offre La ou les offres requises (séparées par des virgules si plusieurs).
     * @param float $requiredDebit Le débit requis pour l'équipement.
     * @return array Retourne un tableau d'équipements compatibles et disponibles.
     */
    public function getAvailableCompatibleEquipements($technology, $offre, $requiredDebit)
    {
        try {
            // Temporairement, ignorer les filtres et retourner tous les équipements disponibles
            $query = "SELECT * FROM equipements_reseau WHERE statut = 'disponible' ORDER BY modele";

            $stmt = $this->db->prepare($query);
            $stmt->execute([]); // Aucun paramètre nécessaire dans cette version temporaire

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération temporaire des équipements disponibles : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère un équipement par son numéro de série.
     *
     * @param string $numero_serie Le numéro de série de l'équipement.
     * @return array|false Retourne l'équipement sous forme de tableau associatif ou false si non trouvé.
     */
    public function getEquipementByNumeroSerie($numero_serie)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM equipements_reseau WHERE numero_serie = ?");
            $stmt->execute([$numero_serie]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de l'équipement par numéro de série : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifie si un work order a déjà un équipement affecté.
     *
     * @param int $work_order_id L'ID du work order.
     * @return bool Retourne true si un équipement est déjà affecté, false sinon.
     */
    public function hasEquipementAffected($work_order_id) {
        $query = "SELECT COUNT(*) FROM affectations_workorders WHERE work_order_id = :work_order_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['work_order_id' => $work_order_id]);
        return $stmt->fetchColumn() > 0;
    }

    // Nouvelle méthode pour désaffecter un équipement d'un work order
    public function desaffecterEquipement($work_order_id, $equipement_id) {
        try {
            // Supprimer l'affectation de la table affectations_workorders
            $queryAffectation = "DELETE FROM affectations_workorders 
                               WHERE work_order_id = :work_order_id 
                               AND equipement_id = :equipement_id";
            $stmtAffectation = $this->db->prepare($queryAffectation);
            $stmtAffectation->execute([
                'work_order_id' => $work_order_id,
                'equipement_id' => $equipement_id
            ]);

            // Mettre à jour le statut de l'équipement à 'disponible'
            $queryEquipement = "UPDATE equipements_reseau SET statut = 'disponible' WHERE id = :id";
            $stmtEquipement = $this->db->prepare($queryEquipement);
            $stmtEquipement->execute(['id' => $equipement_id]);

            return true;
        } catch (Exception $e) {
            error_log("Erreur DB dans Equipement::desaffecterEquipement : " . $e->getMessage());
            return false;
        }
    }
}
