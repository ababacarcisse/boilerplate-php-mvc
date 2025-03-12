<?php

return [
    // Configuration de la base de données
    'db' => [
        'host' => $_ENV['DB_HOST'],          // Hôte de la base de données
        'dbname' => $_ENV['DB_NAME'],   // Nom de la base de données
        'user' => $_ENV['DB_USER'],        // Nom d'utilisateur pour la base de données
        'password' => $_ENV['DB_PASS'],   // Mot de passe pour l'utilisateur de la base de données
        'charset' => 'utf8mb4',         // Charset à utiliser pour la connexion
    ],

    // Configuration SMTP pour l'envoi d'emails
    'smtp' => [
        'host' => $_ENV['SMTP_HOST'],   // Hôte SMTP
        'port' => $_ENV['SMTP_PORT'],                   // Port SMTP (587 pour TLS, 465 pour SSL)
        'username' => $_ENV['SMTP_USER'], // Nom d'utilisateur pour l'authentification SMTP
        'password' => $_ENV['SMTP_PASS'],     // Mot de passe pour l'utilisateur SMTP
        'secure' => 'tls',               // Type de sécurité (tls ou ssl)
    ],

    // Indicateur d'environnement
    'environment' => 'development',    // Peut être 'development' ou 'production'
    'debug' => true,                   // Active le mode debug (affiche les erreurs) en développement
];
