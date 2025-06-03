<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../models/Equipement.php';
require_once __DIR__ . '/../core/Database.php';

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