<?php

namespace Core;

class Router
{
    protected $routes = []; // Tableau pour stocker les routes

    // Ajoute une route au tableau
    public function add($route, $params = [])
    {
        $this->routes[$route] = $params;
    }

    // Gère la correspondance entre l'URL et les contrôleurs/actions
    public function dispatch($url)
    {
        // Vérifie si la route existe
        if (array_key_exists($url, $this->routes)) {
            $controller = $this->routes[$url]['controller'];
            $action = $this->routes[$url]['action'];

            // Instancie le contrôleur
            $controller = "App\\Controllers\\$controller";
            $controller_object = new $controller();

            // Vérifie si l'action est callable
            if (is_callable([$controller_object, $action])) {
                $controller_object->$action();
            } else {
                echo "La méthode $action n'est pas trouvée dans le contrôleur $controller.";
            }
        } else {
            echo "Aucune route trouvée pour l'URL $url.";
        }
    }

    // Extrait les paramètres dynamiques de l'URL
    public function getParams($url)
    {
        // Logique pour extraire les paramètres dynamiques
    }

    // Ajoutez cette méthode dans la classe Router
    public function requireRole($role)
    {
        session_start();
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
            header('HTTP/1.0 403 Forbidden');
            echo "Accès interdit.";
            exit;
        }
    }
}
