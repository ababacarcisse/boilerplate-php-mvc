<?php

use App\Api\Controllers\UserController;

// Exemple de route pour obtenir tous les utilisateurs
$router->get('/api/users', [UserController::class, 'index']);

// Exemple de route pour obtenir un utilisateur par ID
$router->get('/api/users/{id}', [UserController::class, 'show']);

// Exemple de route pour créer un nouvel utilisateur
$router->post('/api/users', [UserController::class, 'store']);

// Exemple de route pour mettre à jour un utilisateur
$router->put('/api/users/{id}', [UserController::class, 'update']);

// Exemple de route pour supprimer un utilisateur
$router->delete('/api/users/{id}', [UserController::class, 'destroy']); 