<?php

namespace App\logs;

/**
 * Gère les erreurs de l'application
 */
class ErrorHandler
{
    /**
     * Répertoire où les fichiers de logs sont stockés
     */
    private $logDirectory;
    
    /**
     * Initialise le gestionnaire d'erreurs
     */
    public function __construct()
    {
        $this->logDirectory = dirname(__DIR__) . '/logs/files/';
        
        // Créer le répertoire de logs s'il n'existe pas
        if (!file_exists($this->logDirectory)) {
            mkdir($this->logDirectory, 0755, true);
        }
    }
    
    /**
     * Enregistre une erreur dans un fichier de log
     * 
     * @param string $message Message d'erreur
     * @param string $context Contexte de l'erreur (ex: login, api, etc.)
     * @param int $level Niveau de l'erreur (1=info, 2=warning, 3=error)
     * @return bool True si l'erreur a été journalisée
     */
    public function logError(string $message, string $context = 'general', int $level = 3): bool
    {
        $logFile = $this->logDirectory . date('Y-m-d') . '_' . $context . '.log';
        
        // Construire le message de log avec timestamp
        $timestamp = date('Y-m-d H:i:s');
        $levelText = $this->getLevelText($level);
        $logMessage = "[$timestamp] [$levelText] $message" . PHP_EOL;
        
        // Écrire dans le fichier de log
        return file_put_contents($logFile, $logMessage, FILE_APPEND) !== false;
    }
    
    /**
     * Convertit le niveau numérique en texte
     * 
     * @param int $level Niveau d'erreur
     * @return string Niveau d'erreur en texte
     */
    private function getLevelText(int $level): string
    {
        switch ($level) {
            case 1:
                return 'INFO';
            case 2:
                return 'WARNING';
            case 3:
                return 'ERROR';
            default:
                return 'UNKNOWN';
        }
    }
    
    /**
     * Gestionnaire global d'erreurs
     * 
     * @param int $errno Numéro de l'erreur
     * @param string $errstr Message d'erreur
     * @param string $errfile Fichier où l'erreur s'est produite
     * @param int $errline Ligne où l'erreur s'est produite
     * @return bool True pour éviter le gestionnaire d'erreurs standard
     */
    public function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        $message = "$errstr in $errfile on line $errline";
        $this->logError($message, 'php');
        
        return true;
    }
    
    /**
     * Enregistre une erreur liée aux emails dans un fichier de log
     * 
     * @param string $message Message d'erreur
     * @param string $details Détails supplémentaires
     * @return bool True si l'erreur a été journalisée
     */
    public function logEmailError(string $message, string $details = ''): bool
    {
        $logFile = $this->logDirectory . date('Y-m-d') . '_email.log';
        
        // Construire le message de log avec timestamp
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message";
        
        if (!empty($details)) {
            $logMessage .= "\nDétails: $details";
        }
        
        $logMessage .= "\n" . str_repeat('-', 80) . "\n";
        
        // Écrire dans le fichier de log
        return file_put_contents($logFile, $logMessage, FILE_APPEND) !== false;
    }
} 