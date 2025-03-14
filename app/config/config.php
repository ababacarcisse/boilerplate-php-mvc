<?php

use App\Lib\EnvLoader;

// Charger les variables d'environnement du fichier .env
$envFile = __DIR__ . '/../../.env';
if (file_exists($envFile)) {
    EnvLoader::load($envFile);
}

return [
    // Configuration de la base de données
    'db' => [
        'host' => EnvLoader::get('DB_HOST', 'localhost'),          // Hôte de la base de données
        'dbname' => EnvLoader::get('DB_NAME', 'test'),   // Nom de la base de données
        'user' => EnvLoader::get('DB_USER', 'root'),        // Nom d'utilisateur pour la base de données
        'password' => EnvLoader::get('DB_PASS', ''),   // Mot de passe pour l'utilisateur de la base de données
        'charset' => 'utf8mb4',         // Charset à utiliser pour la connexion
    ],

    // Configuration SMTP pour l'envoi d'emails
    'smtp' => [
        'host' => EnvLoader::get('SMTP_HOST', 'smtp.gmail.com'),   // Hôte SMTP
        'port' => EnvLoader::get('SMTP_PORT', 587),                   // Port SMTP (587 pour TLS, 465 pour SSL)
        'username' => EnvLoader::get('SMTP_USER', 'votre_email@gmail.com'), // Nom d'utilisateur pour l'authentification SMTP
        'password' => EnvLoader::get('SMTP_PASS', 'votre_mot_de_passe'),     // Mot de passe pour l'utilisateur SMTP
        'secure' => 'tls',               // Type de sécurité (tls ou ssl)
    ],

    // Indicateur d'environnement
    'environment' => EnvLoader::get('APP_ENV', 'development'),    // Peut être 'development' ou 'production'
    'debug' => EnvLoader::get('APP_DEBUG', true),                   // Active le mode debug (affiche les erreurs) en développement
];
