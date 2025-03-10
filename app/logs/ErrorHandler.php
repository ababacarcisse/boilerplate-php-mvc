<?php

namespace Core;

class ErrorHandler
{
    private $logFile;

    public function __construct($logFile)
    {
        $this->logFile = $logFile;
        // Configure le gestionnaire d'erreurs
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'shutdownHandler']);
    }

    // Gère les erreurs PHP
    public function handleError($level, $message, $file, $line)
    {
        $this->log("Erreur [$level]: $message dans $file à la ligne $line");
        if (defined('DEBUG') && DEBUG) {
            echo "Erreur [$level]: $message dans $file à la ligne $line";
        }
    }

    // Gère les exceptions
    public function handleException($exception)
    {
        $this->log("Exception: " . $exception->getMessage() . " dans " . $exception->getFile() . " à la ligne " . $exception->getLine());
        if (defined('DEBUG') && DEBUG) {
            echo "Exception: " . $exception->getMessage() . " dans " . $exception->getFile() . " à la ligne " . $exception->getLine();
        }
    }

    // Gère les erreurs fatales
    public function shutdownHandler()
    {
        $error = error_get_last();
        if ($error) {
            $this->log("Erreur fatale: " . $error['message'] . " dans " . $error['file'] . " à la ligne " . $error['line']);
        }
    }

    // Enregistre les logs dans un fichier
    private function log($message)
    {
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($this->logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
    }
} 