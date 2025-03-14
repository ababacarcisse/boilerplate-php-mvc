<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Router;
use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\RegisterController;
use App\Controllers\Reset_PasswordController;

// Activer l'affichage des erreurs pendant le développement
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

$router->add('/about', function() {
    echo 'À propos de nous';
});

$router->add('/contact', function() {
    echo 'Contactez-nous';
});

// Utiliser la nouvelle méthode resource pour ajouter les routes des contrôleurs
$router->resource('/login', \App\Controllers\LoginController::class);
$router->resource('/register', \App\Controllers\RegisterController::class);
$router->resource('/reset-password', \App\Controllers\Reset_PasswordController::class);

// Dispatcher la requête actuelle
$uri = $_SERVER['REQUEST_URI'];
$router->dispatch($uri);
