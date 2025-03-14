<?php

namespace App\Api\Middlewares;

use App\Lib\JWT;

class AuthMiddleware {
    private $roles;
    
    public function __construct($roles = null) {
        $this->roles = $roles;
    }
    
    public function handle() {
        // Vérifier le token d'authentification
        $token = JWT::extractTokenFromHeader();
        
        if (!$token) {
            header('HTTP/1.1 401 Unauthorized');
            exit(json_encode(['success' => false, 'message' => 'Authentification requise']));
        }
        
        // Valider le token
        $payload = JWT::decode($token);
        
        if (!$payload) {
            header('HTTP/1.1 401 Unauthorized');
            exit(json_encode(['success' => false, 'message' => 'Token invalide ou expiré']));
        }
        
        // Vérifier le rôle si spécifié
        if ($this->roles !== null) {
            $userRole = $payload->data->role;
            
            if (!in_array($userRole, $this->roles)) {
                header('HTTP/1.1 403 Forbidden');
                exit(json_encode(['success' => false, 'message' => 'Accès refusé pour ce rôle']));
            }
        }
        
        // Stocker les informations de l'utilisateur pour les contrôleurs
        $_REQUEST['user'] = $payload->data;
    }
} 