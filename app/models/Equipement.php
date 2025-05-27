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

    public function addEquipement($modele, $marque, $type_interfaces, $gamme, $numero_serie) {
        $query = "INSERT INTO equipements_reseau (modele, marque, type_interfaces, gamme, numero_serie) 
                 VALUES (:modele, :marque, :type_interfaces, :gamme, :numero_serie)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'modele' => $modele,
            'marque' => $marque,
            'type_interfaces' => $type_interfaces,
            'gamme' => $gamme,
            'numero_serie' => $numero_serie
        ]);
    }

    public function updateEquipement($id, $data) {
        $query = "UPDATE equipements_reseau SET 
                 modele = :modele,
                 marque = :marque,
                 type_interfaces = :type_interfaces,
                 gamme = :gamme,
                 statut = :statut,
                 numero_serie = :numero_serie
                 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute(array_merge($data, ['id' => $id]));
    }

    public function affecterEquipement($work_order_id, $equipement_id) {
        // Vérifier si l'équipement est disponible
        $equipement = $this->getEquipementById($equipement_id);
        if ($equipement['statut'] !== 'disponible') {
            return false;
        }

        // Démarrer une transaction
        $this->db->beginTransaction();

        try {
            // Mettre à jour le statut de l'équipement
            $query = "UPDATE equipements_reseau SET statut = 'en_service' WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['id' => $equipement_id]);

            // Créer l'affectation
            $query = "INSERT INTO affectations_workorders (work_order_id, equipement_id) 
                     VALUES (:work_order_id, :equipement_id)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'work_order_id' => $work_order_id,
                'equipement_id' => $equipement_id
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
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

    public function getEquipementCompatible($technology, $offre) {
        // Logique de sélection d'équipement compatible
        $query = "SELECT * FROM equipements_reseau 
                 WHERE statut = 'disponible' 
                 AND (type_interfaces LIKE :technology OR gamme LIKE :offre)
                 ORDER BY date_ajout ASC
                 LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'technology' => "%$technology%",
            'offre' => "%$offre%"
        ]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
