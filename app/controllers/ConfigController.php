<?php
require_once __DIR__ . '/../../vendor/autoload.php';

class ConfigController {
    private $twig;

    public function __construct() {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../');
        $this->twig = new \Twig\Environment($loader);
    }

    public function index() {
        session_start();
        $config = '';
        
        if (isset($_SESSION['generated_config'])) {
            $config = $_SESSION['generated_config'];
            unset($_SESSION['generated_config']);
        }
        
        // Affiche la page de configuration
        require_once __DIR__ . '/../views/configuration.php';
    }

    public function generer() {
        session_start();
        $config = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $template = $this->twig->load('cisco_config_template.twig');
                $config = $template->render($_POST);
                $_SESSION['generated_config'] = $config;
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