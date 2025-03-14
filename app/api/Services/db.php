<?php

namespace App\Api\Services;

use PDO;
use PDOException;

/**
 * Service de base pour la connexion à la base de données
 */
class db
{
    protected $pdo;
    protected static $instance = null;

    /**
     * Constructeur privé (pattern Singleton)
     */
    private function __construct()
    {
        try {
            // Charger la configuration
            $config = require dirname(dirname(dirname(__DIR__))) . '/app/config/config.php';
            $dbConfig = $config['db'];
            
            // Extraire les paramètres de connexion
            $host = $dbConfig['host'];
            $dbname = $dbConfig['dbname'];
            $username = $dbConfig['user'];
            $password = $dbConfig['password'];
            $charset = $dbConfig['charset'];

            $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            throw new \Exception('Erreur de connexion à la base de données: ' . $e->getMessage());
        }
    }

    /**
     * Récupère l'instance unique de db
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Exécute une requête SQL
     */
    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Récupère une seule ligne
     */
    public function fetchOne($sql, $params = [])
    {
        return $this->query($sql, $params)->fetch();
    }

    /**
     * Récupère toutes les lignes
     */
    public function fetchAll($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Insère des données et retourne l'ID généré
     */
    public function insert($table, $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        
        $this->query($sql, $data);
        return $this->pdo->lastInsertId();
    }

    /**
     * Met à jour des données
     */
    public function update($table, $data, $conditions)
    {
        $setParts = [];
        foreach (array_keys($data) as $column) {
            $setParts[] = "$column = :$column";
        }
        $setClause = implode(', ', $setParts);
        
        $whereParts = [];
        foreach (array_keys($conditions) as $column) {
            $whereParts[] = "$column = :where_$column";
        }
        $whereClause = implode(' AND ', $whereParts);
        
        $sql = "UPDATE $table SET $setClause WHERE $whereClause";
        
        $params = $data;
        foreach ($conditions as $key => $value) {
            $params["where_$key"] = $value;
        }
        
        $this->query($sql, $params);
    }
} 