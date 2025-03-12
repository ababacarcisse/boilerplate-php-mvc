<?php
return [
    'app' => [
        'name' => 'PHP MVC Boilerplate',
        'url' => 'http://localhost/coud_bouletplate',
        'environment' => 'development', // 'development', 'testing', 'production'
        'debug' => true,
    ],
    'db' => [
        'host' => 'localhost',
        'dbname' => 'test',
        'user' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ],
    'mail' => [
        'host' => 'smtp.example.com',
        'port' => 587,
        'username' => 'votre_email@example.com',
        'password' => 'votre_mot_de_passe',
        'encryption' => 'tls',
        'from_address' => 'no-reply@example.com',
        'from_name' => 'PHP MVC Boilerplate'
    ],
    'session' => [
        'cookie_lifetime' => 0,
        'cookie_secure' => false, // Mettre à true en production avec HTTPS
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict'
    ]
]; 