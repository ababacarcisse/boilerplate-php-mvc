<?php

namespace Core;

class Router
{
    protected $routes = [];
    protected $params = [];
    protected $namedRoutes = [];

    // Ajoute une route au tableau
    public function add($route, $params = [], $name = null)
    {
        // Convertir la route en expression régulière
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
        $route = '/^' . $route . '$/i';

        $this->routes[$route] = $params;

        // Si un nom est fourni, enregistrer la route nommée
        if ($name) {
            $this->namedRoutes[$name] = $route;
        }
    }

    public function dispatch($url)
    {
        // Supprimer le sous-répertoire du chemin si présent
        $base_path = '/coud_bouletplate';
        if (strpos($url, $base_path) === 0) {
            $url = substr($url, strlen($base_path));
        }
        
        // S'assurer que l'URL commence par '/'
        if ($url !== '/' && empty($url)) {
            $url = '/';
        }
        
        // Normaliser l'URL
        $url = $this->normalizeUrl($url);
        
        // Chercher une correspondance de route
        if ($this->match($url)) {
            // Si le paramètre est une closure, on l'exécute directement
            if (is_callable($this->params)) {
                call_user_func($this->params);
                return;
            }
            
            // Sinon, on attend un tableau avec 'controller' et 'action'
            $controller = $this->params['controller'];
            $action = $this->params['action'] ?? 'index';
    
            // Instancie le contrôleur
            $controller = "App\\Controllers\\$controller";
            
            if (class_exists($controller)) {
                $controller_object = new $controller();
    
                // Vérifie si l'action est callable
                if (is_callable([$controller_object, $action])) {
                    unset($this->params['controller'], $this->params['action']);
                    call_user_func_array([$controller_object, $action], $this->params);
                } else {
                    echo "La méthode $action n'est pas trouvée dans le contrôleur $controller.";
                }
            } else {
                echo "Le contrôleur $controller n'existe pas.";
            }
        } else {
            // Aucune route trouvée, renvoyer une erreur 404
            header("HTTP/1.0 404 Not Found");
            echo "Page non trouvée";
        }
    }
        // Vérifie si l'URL correspond à une route définie
    protected function match($url)
    {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    // Normalise l'URL pour gérer les slash correctement
    private function normalizeUrl($url)
    {
        // Suppression des slash à la fin si présent
        $url = rtrim($url, '/');
        
        // Si c'est une URL vide, retourne simplement '/'
        if (empty($url)) {
            return '/';
        }
        
        return $url;
    }

    // Génère une URL à partir d'un nom de route
    public function generateUrl($name, $params = [])
    {
        if (isset($this->namedRoutes[$name])) {
            $route = $this->namedRoutes[$name];
            
            // Remplacer les paramètres dans la route
            foreach ($params as $param => $value) {
                $route = str_replace("{{$param}}", $value, $route);
            }
            
            return $route;
        }
        
        return '';
    }

    // Vérifie le rôle de l'utilisateur
    public function requireRole($role)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
            header('HTTP/1.0 403 Forbidden');
            echo "Accès interdit.";
            exit;
        }
    }

    /**
     * Associe un contrôleur à un préfixe d'URL (routage par convention)
     * 
     * @param string $prefix Le préfixe d'URL (ex: /users)
     * @param string $controllerClass Le nom complet de la classe du contrôleur
     * @param string $basePath Le chemin de base de l'application
     * @return void
     */
    public function resource($prefix, $controllerClass, $basePath = '/coud_bouletplate')
    {
        $this->add($prefix . '(/.*)?', function() use ($prefix, $controllerClass, $basePath) {
            // Instancier le contrôleur
            $controller = new $controllerClass();
            
            // Récupérer l'URI actuelle
            $uri = $_SERVER['REQUEST_URI'];
            
            // Si l'URI contient un segment après le préfixe, l'utiliser comme action
            if (strpos($uri, $basePath . $prefix . '/') !== false) {
                $action = substr($uri, strlen($basePath . $prefix) + 1);
                if (method_exists($controller, $action)) {
                    // Extraire les paramètres éventuels (tout ce qui suit après l'action)
                    $params = [];
                    if (($posSlash = strpos($action, '/')) !== false) {
                        $params = explode('/', substr($action, $posSlash + 1));
                        $action = substr($action, 0, $posSlash);
                    }
                    
                    // Appeler la méthode du contrôleur avec les paramètres
                    call_user_func_array([$controller, $action], [$params]);
                } else {
                    // Si l'action n'existe pas, afficher une erreur ou rediriger
                    header("HTTP/1.0 404 Not Found");
                    echo "Action non trouvée : " . htmlspecialchars($action);
                }
            } elseif (strpos($uri, $prefix . '/') !== false) {
                // Gérer le cas où le basePath n'est pas inclus dans l'URI
                $action = substr($uri, strlen($prefix) + 1);
                if (method_exists($controller, $action)) {
                    // Extraire les paramètres éventuels
                    $params = [];
                    if (($posSlash = strpos($action, '/')) !== false) {
                        $params = explode('/', substr($action, $posSlash + 1));
                        $action = substr($action, 0, $posSlash);
                    }
                    
                    call_user_func_array([$controller, $action], [$params]);
                } else {
                    header("HTTP/1.0 404 Not Found");
                    echo "Action non trouvée : " . htmlspecialchars($action);
                }
            } else {
                // Appeler l'action par défaut (index)
                $controller->index();
            }
        });
    }
}
