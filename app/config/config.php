<?php

return [
    // Configuration de la base de données
    'db' => [
        'host' => 'localhost',          // Hôte de la base de données
        'dbname' => 'nom_de_la_base',   // Nom de la base de données
        'user' => 'utilisateur',        // Nom d'utilisateur pour la base de données
        'password' => 'mot_de_passe',   // Mot de passe pour l'utilisateur de la base de données
        'charset' => 'utf8mb4',         // Charset à utiliser pour la connexion
    ],

    // Configuration SMTP pour l'envoi d'emails
    'smtp' => [
        'host' => 'smtp.example.com',   // Hôte SMTP
        'port' => 587,                   // Port SMTP (587 pour TLS, 465 pour SSL)
        'username' => 'votre_email@example.com', // Nom d'utilisateur pour l'authentification SMTP
        'password' => 'votre_mot_de_passe',     // Mot de passe pour l'utilisateur SMTP
        'secure' => 'tls',               // Type de sécurité (tls ou ssl)
    ],

    // Indicateur d'environnement
    'environment' => 'development',    // Peut être 'development' ou 'production'
    'debug' => true,                   // Active le mode debug (affiche les erreurs) en développement
];
