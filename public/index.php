<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Router;
use App\Controllers\HomeController;

// Activer l'affichage des erreurs pendant le développement
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$router = new Router();

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

// Dispatcher la requête actuelle
$uri = $_SERVER['REQUEST_URI'];
$router->dispatch($uri);
