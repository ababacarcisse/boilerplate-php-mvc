<?php

namespace App\Services;

/**
 * Service pour la réinitialisation de mot de passe via l'API
 */
class ResetPasswordService
{
    /**
     * URL de l'API de demande de réinitialisation
     */
    private $requestResetApiUrl;
    
    /**
     * URL de l'API de réinitialisation de mot de passe
     */
    private $resetPasswordApiUrl;
    
    /**
     * Initialise le service de réinitialisation
     */
    public function __construct()
    {
        // Construire les URLs des APIs
        $this->requestResetApiUrl = 'http://localhost/coud_bouletplate/api/auth/request-reset';
        $this->resetPasswordApiUrl = 'http://localhost/coud_bouletplate/api/auth/reset-password';
        
        // Journaliser pour le débogage
        error_log("ResetPasswordService initialisé avec l'URL de demande: " . $this->requestResetApiUrl);
        error_log("ResetPasswordService initialisé avec l'URL de réinitialisation: " . $this->resetPasswordApiUrl);
    }
    
    /**
     * Envoie une demande de réinitialisation de mot de passe
     * 
     * @param array $userData Données de l'utilisateur (email, matricule)
     * @return array Résultat de l'opération
     */
    public function requestReset(array $userData): array
    {
        try {
            // Appeler l'API de demande de réinitialisation
            $result = $this->callRequestResetApi($userData);
            
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
            error_log("Erreur lors de la demande de réinitialisation: " . $e->getMessage());
            
            // Retourner un message d'erreur générique
            return [
                'success' => false,
                'message' => 'Erreur de communication avec le serveur',
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Réinitialise le mot de passe d'un utilisateur
     * 
     * @param array $resetData Données de réinitialisation (token, password)
     * @return array Résultat de l'opération
     */
    public function resetPassword(array $resetData): array
    {
        try {
            // Appeler l'API de réinitialisation
            $result = $this->callResetPasswordApi($resetData);
            
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
            error_log("Erreur lors de la réinitialisation du mot de passe: " . $e->getMessage());
            
            // Retourner un message d'erreur générique
            return [
                'success' => false,
                'message' => 'Erreur de communication avec le serveur',
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Appelle l'API de demande de réinitialisation
     * 
     * @param array $userData Données de l'utilisateur
     * @return array Réponse de l'API
     * @throws \Exception Si l'appel échoue
     */
    private function callRequestResetApi(array $userData): array
    {
        // Initialiser cURL
        $ch = curl_init($this->requestResetApiUrl);
        
        // Journaliser pour le débogage
        error_log("Appel API: POST " . $this->requestResetApiUrl);
        error_log("Données de demande: " . json_encode($userData));
        
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
                'message' => "La demande de réinitialisation a échoué",
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
    
    /**
     * Appelle l'API de réinitialisation de mot de passe
     * 
     * @param array $resetData Données de réinitialisation
     * @return array Réponse de l'API
     * @throws \Exception Si l'appel échoue
     */
    private function callResetPasswordApi(array $resetData): array
    {
        // Initialiser cURL
        $ch = curl_init($this->resetPasswordApiUrl);
        
        // Journaliser pour le débogage
        error_log("Appel API: POST " . $this->resetPasswordApiUrl);
        error_log("Données de réinitialisation: " . json_encode(array_keys($resetData)));
        
        // Configurer cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($resetData));
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
                'message' => "La réinitialisation du mot de passe a échoué",
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