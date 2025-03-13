<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\api\Routes\ApiRouter;
use App\api\Middlewares\AuthMiddleware;

// Initialiser le routeur de l'API
$router = new ApiRouter();

// Ajouter le middleware d'authentification
$router->addMiddleware(new AuthMiddleware());

// Charger les routes de l'API
require_once __DIR__ . '/Routes/api.php';

// Démarrer le traitement des requêtes
$router->dispatch($_SERVER['REQUEST_URI']); 