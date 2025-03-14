<?php

namespace App\Api\Controllers;

use App\Lib\EnvLoader;

class helloController {
    
    public function index() {
        // Configurer les en-têtes pour une réponse JSON
        header('Content-Type: application/json');
        
        // Récupérer quelques variables d'environnement pour tester
        $dbHost = EnvLoader::get('DB_HOST', 'non défini');
        $dbName = EnvLoader::get('DB_NAME', 'non défini');
        
        // Vérifier si le fichier de configuration existe
        $configFile = dirname(dirname(dirname(__FILE__))) . '/config/config.php';
        $configExists = file_exists($configFile);
        
        // Retourner une réponse JSON simple
        echo json_encode([
            'success' => true,
            'message' => 'Hello World from API!',
            'time' => date('Y-m-d H:i:s'),
            'uri' => $_SERVER['REQUEST_URI'],
            'method' => $_SERVER['REQUEST_METHOD'],
            'directory' => __DIR__,
            'file_exists' => file_exists(__FILE__) ? 'Yes' : 'No',
            'namespace' => __NAMESPACE__,
            'env_test' => [
                'DB_HOST' => $dbHost,
                'DB_NAME' => $dbName
            ],
            'config_file' => [
                'path' => $configFile,
                'exists' => $configExists ? 'Yes' : 'No'
            ]
        ]);
    }
} 