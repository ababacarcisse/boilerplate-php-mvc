<?php

namespace Core;

class CSRF
{
    // Génère un token CSRF
    public static function generateToken()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // Génère un token aléatoire
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token; // Stocke le token dans la session
        return $token;
    }

    // Vérifie le token CSRF
    public static function verifyToken($token)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
} 