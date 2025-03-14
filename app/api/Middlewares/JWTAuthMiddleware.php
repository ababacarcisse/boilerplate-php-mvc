<?php

namespace App\Api\Middlewares;

use App\Lib\JWT;

class JWTAuthMiddleware
{
    private $allowedRoles;

    public function __construct($allowedRoles = null)
    {
        $this->allowedRoles = $allowedRoles;
    }

    public function handle()
    {
        // Vérifier si le token est présent
        $token = JWT::extractTokenFromHeader();
        
        if (!$token) {
            $this->sendUnauthorizedResponse('Token d\'authentification requis');
            return false;
        }
        
        // Décoder le token
        $decoded = JWT::decode($token);
        
        if (!$decoded) {
            $this->sendUnauthorizedResponse('Token d\'authentification invalide');
            return false;
        }
        
        // Vérifier les rôles si nécessaire
        if ($this->allowedRoles !== null) {
            $userRole = $decoded->data->role;
            
            if (!in_array($userRole, $this->allowedRoles)) {
                $this->sendUnauthorizedResponse('Accès non autorisé pour ce rôle');
                return false;
            }
        }
        
        // Ajouter les données de l'utilisateur à la requête
        $_REQUEST['user'] = $decoded->data;
        
        return true;
    }

    private function sendUnauthorizedResponse($message)
    {
        header('HTTP/1.1 401 Unauthorized');
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $message]);
        exit;
    }
} 