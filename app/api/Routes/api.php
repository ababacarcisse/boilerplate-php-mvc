<?php

use App\Api\Controllers\UserController;
use App\Api\Controllers\AuthController;
use App\Api\Middlewares\JWTAuthMiddleware;
use App\Api\Controllers\helloController;
// Routes d'authentification (pas besoin de middleware d'authentification)
$router->post('/auth/login', [AuthController::class, 'login']);
$router->post('/auth/register', [AuthController::class, 'register']);
$router->post('/auth/refresh-token', [AuthController::class, 'refreshToken']);
// Logout devrait nécessiter une authentification
$router->post('/auth/logout', [AuthController::class, 'logout'], new JWTAuthMiddleware());

// Routes pour la réinitialisation de mot de passe
$router->post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
$router->post('/auth/verify-reset-token', [AuthController::class, 'verifyResetToken']);
$router->post('/auth/reset-password', [AuthController::class, 'resetPassword']);

// Routes protégées pour les utilisateurs
$router->get('/users', [UserController::class, 'index'], new JWTAuthMiddleware(['admin']));
$router->get('/users/{id}', [UserController::class, 'show'], new JWTAuthMiddleware(['admin']));
$router->post('/users', [UserController::class, 'store'], new JWTAuthMiddleware(['admin']));
$router->put('/users/{id}', [UserController::class, 'update'], new JWTAuthMiddleware(['admin']));
$router->delete('/users/{id}', [UserController::class, 'destroy'], new JWTAuthMiddleware(['admin']));

// Route protégée pour le profil utilisateur (accessible par tous les utilisateurs authentifiés)
$router->get('/profile', [UserController::class, 'profile'], new JWTAuthMiddleware()); 
//un route public
$router->get('/hello', [helloController::class, 'index']); 