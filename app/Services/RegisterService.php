<?php

namespace App\Services;

/**
 * Service pour l'inscription des utilisateurs via l'API
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
        // Construire l'URL de l'API
        $this->apiUrl = 'http://localhost/coud_bouletplate/api/auth/register';
        
        // Journaliser pour le débogage
        error_log("RegisterService initialisé avec l'URL: " . $this->apiUrl);
    }
    
    /**
     * Inscrit un utilisateur via l'API
     * 
     * @param array $userData Données de l'utilisateur
     * @return array Résultat de l'opération
     */
    public function register(array $userData): array
    {
        try {
            // Appeler l'API d'inscription
            $result = $this->callRegisterApi($userData);
            
            // Si la réponse est valide
            if (isset($result['success'])) {
                return $result;
            }
            
            // Réponse invalide
            return [
                'success' => false,
                'message' => 'Format de réponse invalide de l\'API'
            ];
            
        } catch (\Exception $e) {
            // Journaliser l'erreur
            error_log("Erreur lors de l'inscription: " . $e->getMessage());
            
            // Retourner un message d'erreur générique
            return [
                'success' => false,
                'message' => 'Erreur de communication avec le serveur',
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Appelle l'API d'inscription
     * 
     * @param array $userData Données de l'utilisateur
     * @return array Réponse de l'API
     * @throws \Exception Si l'appel échoue
     */
    private function callRegisterApi(array $userData): array
    {
        // Initialiser cURL
        $ch = curl_init($this->apiUrl);
        
        // Journaliser pour le débogage
        error_log("Appel API: POST " . $this->apiUrl);
        error_log("Données d'inscription: " . json_encode($userData));
        
        // Configurer cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        
        // Exécuter la requête
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errorNumber = curl_errno($ch);
        $errorMessage = curl_error($ch);
        
        // Journaliser la réponse
        error_log("Réponse API (Code $httpCode): " . ($response ?: 'Aucune réponse'));
        
        // Fermer la connexion cURL
        curl_close($ch);
        
        // Si cURL a échoué
        if ($errorNumber) {
            throw new \Exception("Erreur cURL #$errorNumber: $errorMessage");
        }
        
        // Si la requête a échoué
        if ($httpCode >= 400) {
            $result = [
                'success' => false,
                'message' => "L'inscription a échoué",
                'httpCode' => $httpCode,
                'response' => $response
            ];
            
            // Tenter de décoder la réponse JSON pour obtenir le message d'erreur
            $jsonResponse = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($jsonResponse['message'])) {
                $result['message'] = $jsonResponse['message'];
            }
            
            return $result;
        }
        
        // Décoder la réponse JSON
        $jsonResponse = json_decode($response, true);
        
        // Si la réponse n'est pas un JSON valide
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'success' => false,
                'message' => 'Réponse invalide du serveur',
                'httpCode' => $httpCode,
                'response' => $response
            ];
        }
        
        // Ajouter le code HTTP à la réponse
        $jsonResponse['httpCode'] = $httpCode;
        
        return $jsonResponse;
    }
} 