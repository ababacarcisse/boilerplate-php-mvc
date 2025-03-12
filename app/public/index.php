<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Router;

$router = new Router();

$router->add('/', function() {
    echo 'Bienvenue sur la page d\'accueil';
});

$router->add('/about', function() {
    echo 'À propos de nous';
});

$router->add('/contact', function() {
    echo 'Contactez-nous';
});

$router->dispatch($_SERVER['REQUEST_URI']); 