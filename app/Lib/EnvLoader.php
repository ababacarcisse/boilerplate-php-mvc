<?php

namespace App\Lib;

class EnvLoader
{
    /**
     * Charge les variables d'environnement depuis un fichier .env
     */
    public static function load($filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("Le fichier .env n'existe pas : $filePath");
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Ignorer les commentaires
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Ignorer les lignes sans signe égal
            if (strpos($line, '=') === false) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Supprimer les guillemets autour de la valeur si présents
            if (preg_match('/^(["\'])(.*)\1$/', $value, $matches)) {
                $value = $matches[2];
            }

            // Définir la variable d'environnement
            putenv("$name=$value");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }

    /**
     * Récupère une variable d'environnement ou une valeur par défaut
     */
    public static function get($key, $default = null)
    {
        return isset($_ENV[$key]) ? $_ENV[$key] : $default;
    }
} 