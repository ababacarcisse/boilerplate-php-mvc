<?php

namespace App\Lib;

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;
use Exception;

class JWT
{
    private static $secretKey;
    private static $algorithm = 'HS256';
    private static $issuer = 'coud_api';
    private static $expirationTime = 3600; // 1 heure
    private static $initialized = false;

    public static function initialize()
    {
        if (self::$initialized) {
            return;
        }
        
        // Chargement de la clé secrète depuis un fichier externe sécurisé
        // ou une variable d'environnement
        self::$secretKey = $_ENV['JWT_SECRET'] ?? 'coud_secret_key_'.bin2hex(random_bytes(16));
        
        // En production, utiliser getenv() ou fichier de configuration externe
        // self::$secretKey = getenv('JWT_SECRET');
        
        self::$initialized = true;
    }

    /**
     * Génère un token JWT pour un utilisateur
     */
    public static function generate(array $userData): string
    {
        self::initialize(); // S'assurer que la classe est initialisée
        
        $issuedAt = time();
        $expirationTime = $issuedAt + self::$expirationTime;

        $payload = [
            'iat' => $issuedAt,
            'iss' => self::$issuer,
            'exp' => $expirationTime,
            'data' => [
                'id' => $userData['id'],
                'matricule' => $userData['matricule'],
                'email' => $userData['email'],
                'role' => $userData['role'] ?? 'etudiant'
            ]
        ];

        return FirebaseJWT::encode($payload, self::$secretKey, self::$algorithm);
    }

    /**
     * Vérifie et décode un token JWT
     */
    public static function decode(string $token): ?object
    {
        self::initialize(); // S'assurer que la classe est initialisée
        
        try {
            $decoded = FirebaseJWT::decode($token, new Key(self::$secretKey, self::$algorithm));
            return $decoded;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Extrait le token de l'en-tête Authorization
     */
    public static function extractTokenFromHeader(): ?string
    {
        $headers = apache_request_headers();
        $authHeader = $headers['Authorization'] ?? '';

        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Génère un token de rafraîchissement
     */
    public static function generateRefreshToken(array $userData): string
    {
        self::initialize(); // S'assurer que la classe est initialisée
        
        $issuedAt = time();
        $expirationTime = $issuedAt + (86400 * 30); // 30 jours

        $payload = [
            'iat' => $issuedAt,
            'iss' => self::$issuer,
            'exp' => $expirationTime,
            'data' => [
                'id' => $userData['id'],
                'type' => 'refresh'
            ]
        ];

        return FirebaseJWT::encode($payload, self::$secretKey, self::$algorithm);
    }
}

// L'initialisation sera faite automatiquement lorsqu'une méthode sera appelée 