<?php
// Activer l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configurer les en-têtes pour une réponse JSON
header('Content-Type: application/json');

// Retourner une réponse JSON simple
echo json_encode([
    'success' => true,
    'message' => 'API Test Direct Access Works!',
    'time' => date('Y-m-d H:i:s'),
    'uri' => $_SERVER['REQUEST_URI'],
    'method' => $_SERVER['REQUEST_METHOD'],
    'server_software' => $_SERVER['SERVER_SOFTWARE'],
    'document_root' => $_SERVER['DOCUMENT_ROOT']
]); 