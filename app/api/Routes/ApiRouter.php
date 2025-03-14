<?php

namespace App\Api\Routes;

class ApiRouter {
    protected $routes = [];
    // Nous conservons cette propriété au cas où nous voudrions réactiver cette fonctionnalité plus tard
    protected $middleware = null;
    
    public function __construct() {
        // Journaliser l'initialisation du routeur API
        error_log("ApiRouter initialized");
    }
    
    public function get($route, $handler, $middleware = null) {
        $this->addRoute('GET', $route, $handler, $middleware);
    }
    
    public function post($route, $handler, $middleware = null) {
        $this->addRoute('POST', $route, $handler, $middleware);
    }
    
    public function put($route, $handler, $middleware = null) {
        $this->addRoute('PUT', $route, $handler, $middleware);
    }
    
    public function delete($route, $handler, $middleware = null) {
        $this->addRoute('DELETE', $route, $handler, $middleware);
    }
    
    // Nous gardons cette méthode au cas où nous voudrions la réutiliser plus tard
    public function setMiddleware($middleware) {
        $this->middleware = $middleware;
    }
    
    protected function addRoute($method, $route, $handler, $middleware) {
        // Journaliser l'ajout d'une route
        error_log("Adding route: $method $route");
        
        $this->routes[] = [
            'method' => $method,
            'route' => $route,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }
    
    public function dispatch($uri) {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($uri, PHP_URL_PATH);
        
        // Journaliser le début du dispatch
        error_log("Starting dispatch of URI: $uri with method: $method");
        
        // Supprimer le sous-répertoire du chemin si présent
        $base_path = '/coud_bouletplate';
        if (strpos($uri, $base_path) === 0) {
            $uri = substr($uri, strlen($base_path));
            error_log("Removed base path, new URI: $uri");
        }
        
        // Supprimer le préfixe '/api' pour faire correspondre aux routes définies
        if (strpos($uri, '/api') === 0) {
            $uri = substr($uri, 4); // Enlever '/api'
            error_log("Removed /api prefix, new URI: $uri");
        }
        
        // Si l'URI est vide après suppression du préfixe, remplacer par '/'
        if (empty($uri)) {
            $uri = '/';
            error_log("Empty URI replaced with /");
        }
        
        // Journaliser les routes disponibles
        error_log("Available routes: " . count($this->routes));
        foreach ($this->routes as $index => $route) {
            error_log("Route $index: {$route['method']} {$route['route']}");
        }
        
        // Pour le débogage
        error_log("URI API traitée: " . $uri);
        
        // Nous ne déclenchons plus le middleware global
        // if ($this->middleware !== null) {
        //     $this->middleware->handle();
        // }
        
        foreach ($this->routes as $route) {
            // Vérifier si la méthode correspond
            if ($route['method'] !== $method) {
                error_log("Method mismatch for route {$route['route']}: Expected {$route['method']}, got $method");
                continue;
            }
            
            // Convertir la route en expression régulière
            $pattern = $this->routeToRegex($route['route']);
            
            error_log("Matching route pattern: " . $pattern . " against URI: " . $uri);
            
            if (preg_match($pattern, $uri, $matches)) {
                error_log("Route match found! " . $route['route']);
                
                // Extraire les paramètres
                $params = array_filter(
                    $matches,
                    function ($key) {
                        return !is_numeric($key);
                    },
                    ARRAY_FILTER_USE_KEY
                );
                
                error_log("Parameters: " . json_encode($params));
                
                // Appliquer le middleware s'il existe
                if ($route['middleware']) {
                    error_log("Applying middleware");
                    $middleware = $route['middleware'];
                    $middleware->handle();
                }
                
                // Appeler le gestionnaire
                $handler = $route['handler'];
                if (is_array($handler) && count($handler) === 2) {
                    $controllerClass = $handler[0];
                    $method = $handler[1];
                    
                    error_log("Calling controller: $controllerClass->$method");
                    
                    // Vérifier si la classe existe
                    if (!class_exists($controllerClass)) {
                        throw new \Exception("Controller class not found: $controllerClass");
                    }
                    
                    $controller = new $controllerClass();
                    
                    // Vérifier si la méthode existe
                    if (!method_exists($controller, $method)) {
                        throw new \Exception("Method not found: $method in controller $controllerClass");
                    }
                    
                    return call_user_func_array([$controller, $method], $params);
                } else if (is_callable($handler)) {
                    error_log("Calling callable handler");
                    return call_user_func_array($handler, $params);
                }
                
                return;
            }
        }
        
        // Aucune route ne correspond
        error_log("No matching route found for URI: $uri");
        header('HTTP/1.1 404 Not Found');
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false, 
            'message' => 'Route non trouvée', 
            'uri' => $uri,
            'method' => $method,
            'routes_count' => count($this->routes)
        ]);
    }
    
    protected function routeToRegex($route) {
        // Remplacer les paramètres {param} par des expressions régulières
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route);
        $pattern = '#^' . $pattern . '$#';
        return $pattern;
    }
} 