<?php

namespace Core;

abstract class Controller
{
    /**
     * Constructeur de base pour tous les contrôleurs
     */
    public function __construct()
    {
        // Constructeur vide pour permettre l'appel depuis les classes filles
    }
    
    // Charge une vue et passe des données à celle-ci
    protected function render($view, $data = [])
    {
        // Définir les variables globales nécessaires pour toutes les vues
        require_once __DIR__ . '/../app/config.php';
        require_once __DIR__ . '/../app/helpers/AssetHelper.php';
        
        // Utilisez __DIR__ pour un chemin absolu plutôt qu'un chemin relatif
        $viewPath = dirname(__DIR__) . "/app/Views/$view.php";
        
        // Vérifiez si le fichier existe avant de l'inclure
        if (!file_exists($viewPath)) {
            die("Vue '$view' introuvable: $viewPath");
        }
        
        extract($data); // Extrait les données pour les rendre accessibles dans la vue
        require $viewPath; // Inclut le fichier de vue
    }

    /**
     * Redirige vers une URL
     * 
     * @param string $url URL de destination
     */
    protected function redirectTo(string $url): void
    {
        // S'assurer que config.php est chargé
        require_once __DIR__ . '/../app/config.php';
        
        // Ajouter BASE_URL au début de l'URL
        $url = BASE_URL . $url;
        
        // Effectuer la redirection
        header('Location: ' . $url);
        exit;
    }
}
