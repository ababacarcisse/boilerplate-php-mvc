<?php

namespace App\Api\Middlewares;

class AuthMiddleware {
    public function handle() {
        // Logique d'authentification
        // Exemple : Vérifier un token d'authentification
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            header('HTTP/1.1 401 Unauthorized');
            exit('Unauthorized');
        }
    }
} 