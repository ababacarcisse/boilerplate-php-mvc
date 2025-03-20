<?php

namespace App\Services;

require_once dirname(__DIR__) . '/config.php';

use App\Lib\EnvLoader;
use App\Models\User;

/**
 * Service pour gérer l'authentification des utilisateurs
 */
class LoginService 
{
    /**
     * URL de l'API d'authentification
     */
    private $apiUrl;
    
    /**
     * Initialise le service de connexion
     */
    public function __construct() 
    {
        // Charger le fichier .env s'il existe
        $envFile = dirname(dirname(__DIR__)) . '/.env';
        if (file_exists($envFile)) {
            EnvLoader::load($envFile);
        }
        
        // Utiliser la constante BASE_URL
        $this->apiUrl = 'http://localhost' . BASE_URL . '/api/auth/login';
        
        // Log pour le débogage
        error_log("LoginService initialisé avec l'URL API: " . $this->apiUrl);
    }
    
    /**
     * Tente de connecter un utilisateur via l'API
     * 
     * @param string $matricule Matricule de l'utilisateur
     * @param string $password Mot de passe de l'utilisateur
     * @return array Résultat de la tentative de connexion
     */
    public function login(string $matricule, string $password): array 
    {
        // Préparer les données pour l'API
        $data = [
            'matricule' => $matricule,
            'password' => $password
        ];
        
        // Appeler l'API de connexion
        $response = $this->callLoginApi($data);
        
        if ($response['success']) {
            // Stocke les informations de session si la connexion est réussie
            $this->setUserSession($response);
        }
        
        return $response;
    }
    
    /**
     * Appelle l'API d'authentification
     * 
     * @param array $data Données de connexion
     * @return array Réponse de l'API
     */
    private function callLoginApi(array $data): array 
    {
        try {
            // Log pour le débogage
            error_log("Tentative de connexion à l'API: " . $this->apiUrl);
            error_log("Données envoyées: " . json_encode($data));
            
            // Configurer la requête cURL
            $ch = curl_init($this->apiUrl);
            
            // Définir les options de la requête
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json'
            ]);
            
            // Activer le suivi des informations d'erreur
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            
            // Exécuter la requête
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            // Vérifier si la requête a échoué
            if ($response === false) {
                $error = curl_error($ch);
                error_log("Erreur cURL: " . $error);
                curl_close($ch);
                return [
                    'success' => false,
                    'message' => 'Erreur de connexion: ' . $error,
                    'httpCode' => $httpCode
                ];
            }
            
            // Log de la réponse pour le débogage
            error_log("Code HTTP: " . $httpCode);
            error_log("Réponse brute: " . $response);
            
            // Fermer la session cURL
            curl_close($ch);
            
            // Décoder la réponse JSON
            $result = json_decode($response, true);
            
            // Si la réponse est null ou n'est pas un tableau
            if ($result === null || !is_array($result)) {
                error_log("Échec du décodage JSON ou format de réponse invalide");
                return [
                    'success' => false,
                    'message' => 'Erreur de communication avec le serveur',
                    'response' => $response,
                    'httpCode' => $httpCode
                ];
            }
            
            return $result;
            
        } catch (\Exception $e) {
            // Journaliser l'erreur
            error_log("Exception lors de la connexion à l'API: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Erreur de connexion au serveur: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Stocke les informations de l'utilisateur en session
     * 
     * @param array $response Réponse de l'API
     */
    private function setUserSession(array $response): void 
    {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Stocker les tokens
        $_SESSION['access_token'] = $response['accessToken'];
        $_SESSION['refresh_token'] = $response['refreshToken'];
        
        // Stocker les informations de l'utilisateur
        $_SESSION['user'] = $response['user'];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
    }
    
    /**
     * Vérifie si l'utilisateur est connecté
     * 
     * @return bool True si l'utilisateur est connecté
     */
    public function isLoggedIn(): bool 
    {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Déconnecte l'utilisateur
     */
    public function logout(): void 
    {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Supprimer toutes les variables de session
        $_SESSION = [];
        
        // Détruire le cookie de session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        // Détruire la session
        session_destroy();
    }
} 