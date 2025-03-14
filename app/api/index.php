<?php

// Activer l'affichage des erreurs détaillées
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configurer la gestion des erreurs personnalisée
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("API Error [$errno]: $errstr in $errfile on line $errline");
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => $errstr,
        'file' => $errfile,
        'line' => $errline,
        'errno' => $errno
    ]);
    exit;
});

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Api\Routes\ApiRouter;
// Pas besoin d'importer le middleware global
// use App\Api\Middlewares\AuthMiddleware;

// Initialiser le routeur de l'API
$router = new ApiRouter();

// Ne pas ajouter de middleware global
// $router->setMiddleware(new AuthMiddleware());

// Journaliser l'URI pour le débogage
error_log("API index.php traitement de l'URI: " . $_SERVER['REQUEST_URI']);
error_log("API index.php method: " . $_SERVER['REQUEST_METHOD']);
error_log("API index.php script: " . $_SERVER['SCRIPT_NAME']);

// Définir en-têtes pour éviter la mise en cache
header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');

// Charger les routes de l'API
require_once __DIR__ . '/Routes/api.php';

// Démarrer le traitement des requêtes
// Si une URI originale a été sauvegardée, utilisez-la
$uri = isset($_SERVER['REQUEST_URI_ORIGINAL']) ? $_SERVER['REQUEST_URI_ORIGINAL'] : $_SERVER['REQUEST_URI'];

// Journaliser l'URI finale utilisée
error_log("API final URI for dispatch: " . $uri);

try {
    $router->dispatch($uri);
} catch (Exception $e) {
    error_log("API Exception: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'API Exception: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
} 