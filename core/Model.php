<?php

namespace Core;

use PDO;

abstract class Model
{
    protected static $db = null; // Instance de la connexion PDO

    // Fournit une connexion PDO sécurisée à la base de données
    protected static function getDB()
    {
        if (self::$db === null) {
            $config = require '../config/config.php'; // Charge la configuration
            $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['dbname'] . ';charset=' . $config['db']['charset'];
            self::$db = new PDO($dsn, $config['db']['user'], $config['db']['password']);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Définit le mode d'erreur
        }

        return self::$db; // Retourne l'instance de la connexion
    }

    // Méthode pour exécuter une requête préparée
    protected function query($sql, $params = [])
    {
        $stmt = self::getDB()->prepare($sql); // Prépare la requête
        $stmt->execute($params); // Exécute la requête avec les paramètres
        return $stmt; // Retourne l'objet statement
    }

    // Méthodes CRUD peuvent être ajoutées ici
}
