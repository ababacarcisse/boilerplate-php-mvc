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

    // Gère la correspondance entre l'URL et les contrôleurs/actions
    public function dispatch($url)
    {
        // Normaliser l'URL
        $url = $this->normalizeUrl($url);
        
        // Chercher une correspondance de route
        if ($this->match($url)) {
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
}
