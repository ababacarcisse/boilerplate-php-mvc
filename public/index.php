<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Router;
use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\EntresController;
use App\Controllers\SortiesController;
use App\Controllers\VentesController;
use App\Controllers\StatistiquesController;
use App\Controllers\UtilisateursController;
use App\Controllers\VenteController;
use App\Controllers\ResetPasswordController;
use App\Controllers\RegisterController;
// Afficher toutes les erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Journaliser les informations de la requête pour le débogage
error_log("REQUEST_URI: " . $_SERVER['REQUEST_URI']);
error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
error_log("SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME']);

$router = new Router();

// Route de test pour vérifier si le routeur principal fonctionne correctement
$router->add('/test', function() {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Test route works in main router!',
        'uri' => $_SERVER['REQUEST_URI']
    ]);
    exit;
});

// Route pour rediriger toutes les requêtes API vers le point d'entrée de l'API
// Cette route doit être définie avant les autres routes
$router->add('/api(/.*)?', function() {
    // Pour le débogage
    error_log("API route matched in main router. URI: " . $_SERVER['REQUEST_URI']);
    
    // Supprimer le préfixe '/api' de l'URI pour qu'il corresponde aux routes définies dans api.php
    $_SERVER['REQUEST_URI_ORIGINAL'] = $_SERVER['REQUEST_URI'];
    
    // Définir un en-tête pour indiquer que la requête vient du routeur principal
    header('X-Routed-From: MainRouter');
    
    try {
        // Inclure le fichier index.php de l'API
        require_once __DIR__ . '/../app/api/index.php';
    } catch (Exception $e) {
        // En cas d'erreur, journaliser et retourner une réponse JSON
        error_log("API Error: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'API Error: ' . $e->getMessage()
        ]);
    }
    exit; // Arrêter l'exécution après le traitement de l'API
});


// Définir une route pour la page d'accueil qui utilise le HomeController
$router->add('/', function() {
    $controller = new HomeController();
    $controller->index();
});

$router->add('/register', function() {
    $controller = new RegisterController();
    $controller->index();
});

$router->add('/reset-password', function() {
    $controller = new ResetPasswordController();
    $controller->index();
});

$router->add('/reset-password/reset/([a-zA-Z0-9.]+)', function() use ($router) {
    $matches = [];
    preg_match('/reset-password\/reset\/([a-zA-Z0-9.]+)/', $_SERVER['REQUEST_URI'], $matches);
    $token = $matches[1] ?? '';
    $controller = new ResetPasswordController();
    $controller->reset([$token]);
});

$router->add('/entres', function() {
    $controller = new EntresController();
    $controller->index();
});
 
$router->add('/sorties', function() {
    $controller = new SortiesController();
    $controller->index();
});
$router->add('/vente', function() {
    $controller = new VenteController();
    $controller->index();
});
 $router->add('/ventes', function() {
    $controller = new VentesController();
    $controller->index();
});
$router->add('/statistiques', function() {
    $controller = new StatistiquesController();
    $controller->index();
});
$router->add('/utilisateurs', function() {
    $controller = new UtilisateursController();
    $controller->index();
});
 
// Routes d'authentification
$router->add('/login', function() {
    $controller = new LoginController();
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $controller->index();
    } else {
        $controller->processLogin();
    }
}, 'GET|POST');

$router->add('/login/logout', function() {
    $controller = new LoginController();
    $controller->logout();
});

// Route API pour l'authentification (déjà gérée par le middleware API)
// Pas besoin de définir /api/auth/login ici car c'est géré par la route API générique

// Dispatcher la requête actuelle
$uri = $_SERVER['REQUEST_URI'];
$router->dispatch($uri);
