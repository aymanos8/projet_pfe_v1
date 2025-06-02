<?php

require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/models/Equipement.php';

try {
    $cnx = Database::getInstance()->getConnection();
    $equipementModel = new Equipement($cnx);

    // Ajouter les colonnes technology, offre, debit si elles n'existent pas
    try {
        $cnx->exec("ALTER TABLE `equipements_reseau` ADD COLUMN `technology` VARCHAR(100) NULL;");
        echo "Colonne 'technology' ajoutée ou déjà existante.\n";
    } catch(PDOException $e) { /* ignorer si colonne existe déjà */ }

    try {
        $cnx->exec("ALTER TABLE `equipements_reseau` ADD COLUMN `offre` VARCHAR(100) NULL;");
        echo "Colonne 'offre' ajoutée ou déjà existante.\n";
    } catch(PDOException $e) { /* ignorer si colonne existe déjà */ }

    try {
        $cnx->exec("ALTER TABLE `equipements_reseau` ADD COLUMN `debit` VARCHAR(50) NULL;");
        echo "Colonne 'debit' ajoutée ou déjà existante.\n";
    } catch(PDOException $e) { /* ignorer si colonne existe déjà */ }

    // Liste des équipements à insérer avec les nouveaux champs
    $equipements_a_inserer = [
        // ISR 4331/K9
        [
            'modele' => 'ISR 4331/K9',
            'marque' => 'Cisco',
            'type_interfaces' => 'Ethernet, Serial',
            'gamme' => 'ISR 4300',
            'technology' => 'FO,4G',
            'offre' => 'Internet,Voix',
            'debit' => '500',
            'numero_serie' => 'SN_ISR4331_001'
        ],
        [
            'modele' => 'ISR 4331/K9',
            'marque' => 'Cisco',
            'type_interfaces' => 'Ethernet, Serial',
            'gamme' => 'ISR 4300',
            'technology' => 'FO,4G',
            'offre' => 'Internet,Voix',
            'debit' => '500',
            'numero_serie' => 'SN_ISR4331_002'
        ],
        // C9200L-24P-4X
        [
            'modele' => 'C9200L-24P-4X',
            'marque' => 'Cisco',
            'type_interfaces' => 'Ethernet',
            'gamme' => 'Catalyst 9200',
            'technology' => 'FO,FH',
            'offre' => 'Internet,VPN',
            'debit' => '1000',
            'numero_serie' => 'SN_C9200L_001'
        ],
        [
            'modele' => 'C9200L-24P-4X',
            'marque' => 'Cisco',
            'type_interfaces' => 'Ethernet',
            'gamme' => 'Catalyst 9200',
            'technology' => 'FO,FH',
            'offre' => 'Internet,VPN',
            'debit' => '1000',
            'numero_serie' => 'SN_C9200L_002'
        ],
        // C1111-8PWEVD
        [
            'modele' => 'C1111-8PWEVD',
            'marque' => 'Cisco',
            'type_interfaces' => 'Ethernet, VDSL',
            'gamme' => 'C1111',
            'technology' => 'xDSL',
            'offre' => 'Internet',
            'debit' => '100',
            'numero_serie' => 'SN_C1111_001'
        ],
        // C1111-4P
        [
            'modele' => 'C1111-4P',
            'marque' => 'Cisco',
            'type_interfaces' => 'Ethernet',
            'gamme' => 'C1111',
            'technology' => 'FO,FH,4G',
            'offre' => 'Internet,VPN',
            'debit' => '300',
            'numero_serie' => 'SN_C1111_002'
        ],
    ];

    $count_inserted = 0;
    foreach ($equipements_a_inserer as $equipement_data) {
        // Vérifier si l'équipement existe déjà par numero_serie
        $existing_equipement = $equipementModel->getEquipementByNumeroSerie($equipement_data['numero_serie']);

        if (!$existing_equipement) {
            // Insérer l'équipement s'il n'existe pas
            if ($equipementModel->addEquipement(
                $equipement_data['modele'],
                $equipement_data['marque'],
                $equipement_data['type_interfaces'],
                $equipement_data['gamme'],
                $equipement_data['numero_serie'],
                $equipement_data['technology'],
                $equipement_data['offre'],
                $equipement_data['debit']
            )) {
                $count_inserted++;
            }
        } else {
            echo "Équipement avec numéro de série {$equipement_data['numero_serie']} existe déjà.\n";
        }
    }

    echo "\n--\n$count_inserted équipements insérés avec succès (les existants ont été ignorés).\n";

} catch(Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}

?>