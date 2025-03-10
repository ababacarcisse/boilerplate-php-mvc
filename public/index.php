<?php
session_start([
    'cookie_lifetime' => 0, // Session dure jusqu'à la fermeture du navigateur
    'cookie_secure' => true, // Le cookie ne sera envoyé que sur des connexions HTTPS
    'cookie_httponly' => true, // Le cookie n'est pas accessible via JavaScript
    'cookie_samesite' => 'Strict', // Empêche l'envoi du cookie sur des requêtes cross-site
]);

// Définir l'environnement
define('DEBUG', true); // Changez à false en production

// Initialiser le gestionnaire d'erreurs
require '../core/ErrorHandler.php';
$errorHandler = new \Core\ErrorHandler('../logs/error.log');

// Autres initialisations... 