<?php

namespace App\Services;

use App\Lib\EnvLoader;

/**
 * Service pour gérer l'inscription des utilisateurs
 */
class RegisterService 
{
    /**
     * URL de l'API d'inscription
     */
    private $apiUrl;
    
    /**
     * Initialise le service d'inscription
     */
    public function __construct() 
    {
        // Charger le fichier .env s'il existe
        $envFile = dirname(dirname(__DIR__)) . '/.env';
        if (file_exists($envFile)) {
            EnvLoader::load($envFile);
        }
        
        // URL de l'API d'inscription
        $this->apiUrl = 'http://localhost/coud_bouletplate/api/auth/register';
        
        // Log pour le débogage
        error_log("RegisterService initialisé avec l'URL API: " . $this->apiUrl);
    }
    
    /**
     * Enregistre un nouvel utilisateur via l'API
     * 
     * @param array $userData Données de l'utilisateur
     * @return array Résultat de l'inscription
     */
    public function register(array $userData): array 
    {
        // Appeler l'API d'inscription
        return $this->callRegisterApi($userData);
    }
    
    /**
     * Appelle l'API d'inscription
     * 
     * @param array $data Données d'inscription
     * @return array Réponse de l'API
     */
    private function callRegisterApi(array $data): array 
    {
        try {
            // Log pour le débogage
            error_log("Tentative d'inscription à l'API: " . $this->apiUrl);
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
            error_log("Exception lors de l'inscription via l'API: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Erreur de connexion au serveur: ' . $e->getMessage()
            ];
        }
    }
}
