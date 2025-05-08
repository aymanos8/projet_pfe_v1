<?php
// app/core/Router.php

class Router {
    private $routes = [];

    // Définir une route GET
    public function get($route, $action) {
        $this->routes['GET'][$this->normalizeRoute($route)] = $action;
    }

    // Définir une route POST
    public function post($route, $action) {
        $this->routes['POST'][$this->normalizeRoute($route)] = $action;
    }

    // Lancer le routeur
    public function run() {
        $method = $_SERVER['REQUEST_METHOD'];
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = '/' . trim(str_replace($scriptName, '', $uri), '/');
        $uri = $this->normalizeRoute($uri);

        if (!isset($this->routes[$method])) {
            echo "405 Method Not Allowed";
            return;
        }

        foreach ($this->routes[$method] as $route => $action) {
            $pattern = $this->convertRouteToPattern($route);
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Supprimer l'URL complète

                if (is_callable($action)) {
                    call_user_func_array($action, $matches);
                } else {
                    list($controller, $methodName) = $action;
                    if (!class_exists($controller)) {
                        echo "Controller $controller not found";
                        return;
                    }
                    $instance = new $controller();
                    if (!method_exists($instance, $methodName)) {
                        echo "Method $methodName not found in $controller";
                        return;
                    }
                    call_user_func_array([$instance, $methodName], $matches);
                }
                return;
            }
        }

        // Aucune route trouvée
        
        echo "404 Not Found";
    }

    // Convertit une route avec {param} en une expression régulière
    private function convertRouteToPattern($route) {
        $route = preg_replace('/\{(\w+)\}/', '([^\/]+)', $route);
        return '#^' . $route . '$#';
    }

    // Normaliser une route (ex : supprimer les / en trop)
    private function normalizeRoute($route) {
        return '/' . trim($route, '/');
    }

    // Pour afficher les routes (debug)
    public function listRoutes() {
        echo "<pre>";
        print_r($this->routes);
        echo "</pre>";
    }
}
?>
