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
        session_start();
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
        session_start();
        $config = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Store form data in session before validation
            $_SESSION['form_data'] = $_POST;

            // Vérification des champs requis
            $champs_requis = [
                'ROUTER_HOSTNAME',
                'ADMIN_USER',
                'ADMIN_PASS',
                'INTERFACE_MGMT',
                'GATEWAY_MGMT',
                'WAN_MASK', // Added WAN_MASK to required fields
                'MGMT1',
                'MGMT2',
                'MGMT3',
                'WILDWARD'
            ];

            $champs_manquants = [];
            foreach ($champs_requis as $champ) {
                if (empty($_POST[$champ])) {
                    $champs_manquants[] = $champ;
                }
            }

            if (!empty($champs_manquants)) {
                $_SESSION['error'] = "Veuillez remplir tous les champs obligatoires : " . implode(', ', $champs_manquants);
                header('Location: /projet-pfe-v1/projet-t1/public/configuration');
                exit();
            }

            // Vérification du format des adresses IP (Simplifiée)
            $champs_ip = [
                'GATEWAY_MGMT',
                'WAN_MASK',
                'MGMT1',
                'MGMT2',
                'MGMT3',
                'WILDWARD' // Inclure WILDWARD ici pour la validation simple
            ];

            $erreurs_format = [];
            foreach ($champs_ip as $champ) {
                 // Valider le format comme une adresse IP standard
                 if (isset($_POST[$champ]) && !empty($_POST[$champ]) && !filter_var($_POST[$champ], FILTER_VALIDATE_IP)) {
                      $erreurs_format[] = "Le format de " . $champ . " n'est pas une adresse IP valide.";
                 }
            }

            if (!empty($erreurs_format)) {
                $_SESSION['error'] = ($_SESSION['error'] ?? '') . "<br>" . implode('<br>', $erreurs_format);
                header('Location: /projet-pfe-v1/projet-t1/public/configuration');
                exit();
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