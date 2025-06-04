<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../models/Equipement.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Configuration.php';
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../models/Utilisateur.php';
require_once __DIR__ . '/../models/HistoriqueAction.php';

class ConfigController {
    private $twig;

    public function __construct() {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../');
        $this->twig = new \Twig\Environment($loader);
    }

    public function index($equipementId = null) {
        $config = '';
        $equipement = null;
        $formData = []; // Initialize formData
        
        if ($equipementId !== null) {
            try {
                $cnx = Database::getInstance()->getConnection();
                $equipementModel = new Equipement($cnx);
                $equipement = $equipementModel->getEquipementById($equipementId);
            } catch (Exception $e) {
                error_log("Erreur lors de la récupération de l'équipement : " . $e->getMessage());
                $_SESSION['error'] = "Erreur lors du chargement de l'équipement.";
            }
        }

        if (isset($_SESSION['generated_config'])) {
            $config = $_SESSION['generated_config'];
            unset($_SESSION['generated_config']);
        }
        
        // Check for and load form data from session
        if (isset($_SESSION['form_data'])) {
            $formData = $_SESSION['form_data'];
            unset($_SESSION['form_data']);
        }
        
        require_once __DIR__ . '/../views/configuration.php';
    }

    public function generer() {
        $config = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formData = $_POST;
            $_SESSION['form_data'] = $formData;

            // Validation des champs IP obligatoires
            $ipFields = [
                'GATEWAY_MGMT' => 'Gateway management',
                'MGMT1' => 'MGMT1',
                'MGMT2' => 'MGMT2',
                'MGMT3' => 'MGMT3'
            ];

            $errors = [];
            foreach ($ipFields as $field => $label) {
                if (!filter_var($formData[$field], FILTER_VALIDATE_IP)) {
                    $errors[] = "Le format de $label n'est pas une adresse IP valide.";
                }
            }

            // Validation des interfaces réseau dynamiques
            if (isset($formData['INTERFACES']) && is_array($formData['INTERFACES'])) {
                foreach ($formData['INTERFACES'] as $index => $interface) {
                    if (!empty($interface['LAN_IP']) && !filter_var($interface['LAN_IP'], FILTER_VALIDATE_IP)) {
                        $errors[] = "L'adresse IP de l'interface " . ($index + 1) . " n'est pas valide.";
                    }
                    if (!empty($interface['LAN_MASK']) && !filter_var($interface['LAN_MASK'], FILTER_VALIDATE_IP)) {
                        $errors[] = "Le masque de l'interface " . ($index + 1) . " n'est pas valide.";
                    }
                }
            }

            // Validation des ACLs dynamiques
            if (isset($formData['ACLS_DYN']) && is_array($formData['ACLS_DYN'])) {
                foreach ($formData['ACLS_DYN'] as $index => $acl) {
                    if (!empty($acl['value']) && !filter_var($acl['value'], FILTER_VALIDATE_IP)) {
                        $errors[] = "La valeur de l'ACL " . ($index + 1) . " n'est pas une adresse IP valide.";
                    }
                }
            }

            if (!empty($errors)) {
                $_SESSION['error'] = implode(' ', $errors);
                header('Location: /projet-pfe-v1/projet-t1/public/configuration');
                exit;
            }

            try {
                $template = $this->twig->load('cisco_config_template.twig');
                $config = $template->render($_POST);
                $_SESSION['generated_config'] = $config;
                
                // Récupérer l'ID de l'équipement et de l'utilisateur connecté
                $equipementId = $_POST['equipement_id'] ?? null;
                $userId = AuthController::getUserId(); // Récupérer l'ID de l'utilisateur connecté

                // Enregistrer la configuration dans la base de données
                if ($equipementId !== null && $userId !== null) {
                    $database = Database::getInstance();
                    $configModel = new Configuration($database->getConnection());
                    
                    $configId = $configModel->saveConfiguration($equipementId, $userId, $config);

                    if ($configId !== false) {
                         // Enregistrer l'action dans l'historique
                        $historiqueModel = new HistoriqueAction($database); // Instancier le modèle HistoriqueAction
                        $historiqueModel->addAction(
                            $userId,
                            'creation', // Revenir à 'creation' pour la compatibilité d'affichage
                            'configuration',
                            $configId, // ID de la configuration créée
                            "Configuration générée pour l'équipement #" . $equipementId
                        );
                        $_SESSION['success'] = "Configuration générée et enregistrée avec succès.";
                    } else {
                        $_SESSION['error'] = "Configuration générée, mais erreur lors de l'enregistrement.";
                    }
                } else if ($equipementId === null) {
                     $_SESSION['warning'] = "Configuration générée, mais non liée à un équipement car l'ID est manquant.";
                } else if ($userId === null) {
                     $_SESSION['warning'] = "Configuration générée, mais non liée à un utilisateur car l'ID est manquant (utilisateur non connecté ?).";
                }

                // Unset form data from session after successful generation
                unset($_SESSION['form_data']);

                header('Location: /projet-pfe-v1/projet-t1/public/configuration');
                exit();
            } catch (\Exception $e) {
                $_SESSION['error'] = "Erreur lors de la génération de la configuration : " . $e->getMessage();
                header('Location: /projet-pfe-v1/projet-t1/public/configuration');
                exit();
            }
        }
        
        if (isset($_SESSION['generated_config'])) {
            $config = $_SESSION['generated_config'];
            unset($_SESSION['generated_config']);
        }
        
        require_once __DIR__ . '/../views/configuration.php';
    }
}