<?php

namespace App\Services;

/**
 * Service pour gérer l'authentification côté client
 */
class AuthService extends ApiService
{
    /**
     * Tente de connecter un utilisateur
     */
    public function login($matricule, $password)
    {
        $data = [
            'matricule' => $matricule,
            'password' => $password
        ];

        $response = $this->post('/auth/login', $data);
        
        if (isset($response['token'])) {
            $_SESSION['auth_token'] = $response['token'];
            $_SESSION['user'] = $response['user'];
            return true;
        }
        
        return false;
    }

    /**
     * Déconnecte l'utilisateur
     */
    public function logout()
    {
        // Appel API pour invalider le token côté serveur
        $this->post('/auth/logout');
        
        // Suppression des données de session
        unset($_SESSION['auth_token']);
        unset($_SESSION['user']);
        
        return true;
    }

    /**
     * Inscrit un nouvel utilisateur
     */
    public function register($userData)
    {
        return $this->post('/auth/register', $userData);
    }

    /**
     * Vérifie si l'utilisateur est connecté
     */
    public function isLoggedIn()
    {
        return isset($_SESSION['auth_token']) && !empty($_SESSION['auth_token']);
    }

    /**
     * Récupère l'utilisateur connecté
     */
    public function getCurrentUser()
    {
        return $_SESSION['user'] ?? null;
    }
} 