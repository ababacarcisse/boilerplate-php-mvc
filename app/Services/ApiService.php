<?php

namespace App\Services;

/**
 * Service de base pour les appels API
 */
class ApiService
{
    protected $baseUrl;
    protected $headers;

    public function __construct()
    {
        $this->baseUrl = $_ENV['API_URL'] ?? 'http://localhost/coud_bouletplate/api';
        $this->headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        // Ajouter le token d'authentification s'il existe
        if (isset($_SESSION['auth_token'])) {
            $this->headers[] = 'Authorization: Bearer ' . $_SESSION['auth_token'];
        }
    }

    /**
     * Effectue une requête GET vers l'API
     */
    public function get($endpoint, $params = [])
    {
        $url = $this->baseUrl . $endpoint;
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $this->handleResponse($response, $statusCode);
    }

    /**
     * Effectue une requête POST vers l'API
     */
    public function post($endpoint, $data = [])
    {
        $url = $this->baseUrl . $endpoint;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $this->handleResponse($response, $statusCode);
    }

    /**
     * Gère la réponse de l'API
     */
    protected function handleResponse($response, $statusCode)
    {
        if ($statusCode >= 200 && $statusCode < 300) {
            return json_decode($response, true);
        } else {
            $error = json_decode($response, true);
            throw new \Exception($error['message'] ?? 'Erreur API: ' . $statusCode);
        }
    }
} 