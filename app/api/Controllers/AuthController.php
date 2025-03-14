<?php

namespace App\Api\Controllers;

use App\Api\Services\AuthService;
use App\Lib\JWT;

class AuthController
{
    private $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Authentifie un utilisateur
     */
    public function login()
    {
        // Vérifier la méthode
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            return;
        }
        
        // Récupérer les données
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['matricule']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Matricule et mot de passe requis']);
            return;
        }
        
        // Vérifier s'il y a eu trop de tentatives de connexion échouées
        if ($this->authService->tooManyLoginAttempts($data['matricule'])) {
            http_response_code(429); // Too Many Requests
            echo json_encode([
                'success' => false, 
                'message' => 'Trop de tentatives de connexion échouées. Veuillez réessayer plus tard.'
            ]);
            return;
        }
        
        // Authentifier l'utilisateur
        $result = $this->authService->login($data['matricule'], $data['password']);
        
        // Définir le code de réponse HTTP en fonction du résultat
        if (!$result['success']) {
            http_response_code(401);
        } else {
            http_response_code(200);
        }
        
        // Retourner le résultat au format JSON
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    /**
     * Inscrit un nouvel utilisateur
     */
    public function register()
    {
        // Vérifier la méthode
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            return;
        }
        
        // Récupérer les données
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Valider les données
        $requiredFields = ['matricule', 'nom', 'prenom', 'date_naissance', 'email', 'password'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => "Le champ $field est requis"]);
                return;
            }
        }
        
        // Valider le format de l'email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "L'adresse email n'est pas valide"]);
            return;
        }
        
        // Valider le format de la date de naissance
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['date_naissance'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Le format de la date de naissance doit être YYYY-MM-DD"]);
            return;
        }
        
        // Valider la complexité du mot de passe
        if (strlen($data['password']) < 8) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Le mot de passe doit contenir au moins 8 caractères"]);
            return;
        }
        
        // Inscrire l'utilisateur
        $result = $this->authService->register($data);
        
        // Définir le code de réponse HTTP en fonction du résultat
        if (!$result['success']) {
            http_response_code(400);
        } else {
            http_response_code(201); // Created
        }
        
        // Retourner le résultat au format JSON
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    /**
     * Rafraîchit les tokens
     */
    public function refreshToken()
    {
        // Vérifier la méthode
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            return;
        }
        
        // Récupérer les données
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['refreshToken'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Token de rafraîchissement requis']);
            return;
        }
        
        // Rafraîchir les tokens
        $result = $this->authService->refreshToken($data['refreshToken']);
        
        // Définir le code de réponse HTTP en fonction du résultat
        if (!$result['success']) {
            http_response_code(401);
        } else {
            http_response_code(200);
        }
        
        // Retourner le résultat au format JSON
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    /**
     * Déconnecte un utilisateur
     */
    public function logout()
    {
        // Vérifier la méthode
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            return;
        }
        
        // Vérifier l'authentification
        $token = JWT::extractTokenFromHeader();
        $decoded = JWT::decode($token);
        
        if (!$decoded || !isset($decoded->data->id)) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            return;
        }
        
        // Déconnecter l'utilisateur
        $result = $this->authService->logout($decoded->data->id);
        
        // Retourner le résultat au format JSON
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    
    /**
     * Initie le processus de réinitialisation de mot de passe
     */
    public function forgotPassword()
    {
        // Vérifier la méthode
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            return;
        }
        
        // Récupérer les données
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Adresse email valide requise']);
            return;
        }
        
        // Initier la réinitialisation du mot de passe
        $result = $this->authService->initiatePasswordReset($data['email']);
        
        // Toujours retourner succès pour ne pas révéler si l'email existe
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    
    /**
     * Vérifie si un token de réinitialisation est valide
     */
    public function verifyResetToken()
    {
        // Vérifier la méthode
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            return;
        }
        
        // Récupérer les données
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['token'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Token requis']);
            return;
        }
        
        // Vérifier le token
        $isValid = $this->authService->verifyPasswordResetToken($data['token']);
        
        // Retourner le résultat
        header('Content-Type: application/json');
        if ($isValid) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Token invalide ou expiré']);
        }
    }
    
    /**
     * Réinitialise le mot de passe d'un utilisateur
     */
    public function resetPassword()
    {
        // Vérifier la méthode
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            return;
        }
        
        // Récupérer les données
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['token']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Token et mot de passe requis']);
            return;
        }
        
        // Valider la complexité du mot de passe
        if (strlen($data['password']) < 8) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Le mot de passe doit contenir au moins 8 caractères']);
            return;
        }
        
        // Réinitialiser le mot de passe
        $result = $this->authService->resetPassword($data['token'], $data['password']);
        
        // Définir le code de réponse HTTP en fonction du résultat
        if (!$result['success']) {
            http_response_code(400);
        } else {
            http_response_code(200);
        }
        
        // Retourner le résultat au format JSON
        header('Content-Type: application/json');
        echo json_encode($result);
    }
} 